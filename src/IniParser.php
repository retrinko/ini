<?php


namespace FlmBus\Ini;

use FlmBus\Ini\Exceptions\InvalidDataException;

class IniParser
{

    const SECTION_INHERITANCE_OPERATOR = ':';

    /**
     * @var IniParser
     */
    protected static $instance;
    /**
     * @var string
     */
    protected $invalidCharsForItemNames = '?{}|&~!()^"';
    /**
     * @var array
     */
    protected $invalidItemNames = ['null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none'];

    /**
     * IniParser constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return IniParser
     */
    public static function i()
    {
        return static::getInstance();
    }

    /**
     * @return IniParser
     */
    public static function getInstance()
    {
        if (is_null(static::$instance))
        {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __clone()
    {
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function __wakeup()
    {
    }

    /**
     * @param $string
     *
     * @return IniSection[]
     * @throws InvalidDataException
     */
    public function parseIniString($string)
    {
        $parsedContents = [];

        if (strlen($string) > 0)
        {
            $rawContents = @parse_ini_string($string, true, INI_SCANNER_RAW);
            if (false === $rawContents)
            {
                throw new InvalidDataException('Error parsing ini string!');
            }

            $parsedContents = $this->parseArray($rawContents);
        }

        return $parsedContents;
    }

    /**
     * Cast item string value to proper type
     *
     * @param string $value
     *
     * @return array|string|bool|int|float|null
     */
    public function castItemValueToProperType($value)
    {
        $normalized = $value;

        if (in_array($value, ['true', 'on', 'yes']))
        {
            $normalized = true;
        }
        elseif (in_array($value, ['false', 'off', 'no', 'none']))
        {
            $normalized = false;
        }
        elseif ('null' == $value)
        {
            $normalized = null;
        }
        elseif (is_numeric($value))
        {
            $number = $value + 0;
            if (intval($number) == $number)
            {
                $normalized = (int)$number;
            }
            elseif (floatval($number) == $number)
            {
                $normalized = (float)$number;
            }
        }

        return $normalized;
    }

    /**
     * Get an string (or an array of strings) representation of $value
     *
     * @param bool|int|float|null|array $value
     *
     * @return array|string
     * @throws InvalidDataException
     */
    public function itemValuetoStringRepresentation($value)
    {
        if (is_bool($value))
        {
            $castedValue = (true === $value) ? 'true' : 'false';
        }
        elseif (is_null($value))
        {
            $castedValue = 'null';
        }
        elseif (is_array($value))
        {
            $castedValue = [];
            foreach ($value as $k => $v)
            {
                $castedValue[$k] = $this->itemValuetoStringRepresentation($v);
            }
        }
        elseif (is_numeric($value))
        {
            $castedValue = (string)$value;
        }
        elseif (is_string($value))
        {
            $castedValue = $value;
        }
        else
        {
            throw new InvalidDataException('Invalid item value type!');
        }

        return $castedValue;
    }

    /**
     * @param string $name
     *
     * @return array [validationResult, message]
     */
    public function validateItemName($name)
    {
        $valid = true;
        $message = 'Valid item name.';
        if (!is_string($name))
        {
            $valid = false;
            $message = 'Only string values are allowed for item names!';
        }

        if (in_array($name, $this->invalidItemNames))
        {
            $valid = false;
            $message = sprintf('Item name is not allowed! Not allowed item names: %s',
                               implode(', ', $this->invalidItemNames));
        }

        if (preg_match(sprintf('/[%s]/', $this->invalidCharsForItemNames), $name) > 0)
        {
            $valid = false;
            $message = sprintf('Invalid name for ini item! Provided item name contains not ' .
                               'allowed chars (%s).', $this->invalidCharsForItemNames);
        }

        return [$valid, $message];
    }


    /**
     * @param array $rawContents
     *
     * @return IniSection[]
     * @throws InvalidDataException
     */
    public function parseArray(array $rawContents)
    {
        $parsedContents = [];
        foreach ($rawContents as $sectionFullName => $sectionContents)
        {
            $pieces = explode(self::SECTION_INHERITANCE_OPERATOR, $sectionFullName, 2);
            $sectionName = trim($pieces[0]);
            if (!is_array($sectionContents))
            {
                throw new InvalidDataException(sprintf('Orphan fields are not allowed! ' .
                                                       'Please define a section for field "%s".',
                                                       $sectionName));
            }
            $parsedContents[$sectionName] = new IniSection($sectionName);
            $parsedContents[$sectionName]->setContents($sectionContents);
            $parentName = isset($pieces[1]) ? trim($pieces[1]) : null;
            if (!is_null($parentName))
            {
                if (!isset($parsedContents[$parentName]))
                {
                    throw new InvalidDataException(sprintf('Parent section not found! ' .
                                                           'Define "%s" section before "%s" section.',
                                                           $parentName, $sectionName));
                }
                $parsedContents[$sectionName]->setParent($parsedContents[$parentName]);
            }
        }

        return $parsedContents;
    }

}