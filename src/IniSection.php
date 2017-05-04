<?php


namespace FlmBus\Ini;


use FlmBus\Ini\Exceptions\InvalidDataException;

class IniSection
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var IniSection|null
     */
    protected $parent;
    /**
     * @var array
     */
    protected $contents = [];

    /**
     * Section constructor.
     *
     * @param string $name
     * @param IniSection $parent
     */
    public function __construct($name, IniSection $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @param IniSection $parent
     *
     * @return $this
     */
    public function setParent(IniSection $parent)
    {
        $this->parent = $parent;

        return $this;
    }


    /**
     * @param array $data
     *
     * @return $this
     * @throws InvalidDataException
     */
    public function setContents($data)
    {
        if (!is_array($data))
        {
            throw new InvalidDataException('Invalid section contents! ' .
                                           'Section contents must be an array.');
        }
        $this->contents = IniParser::i()->itemValuetoStringRepresentation($data);

        return $this;
    }

    /**
     * @return array
     */
    protected function getContents()
    {
        return $this->contents;
    }

    /**
     * @return bool
     */
    public function hasParent()
    {
        return ($this->parent instanceof IniSection);
    }

    /**
     * @return IniSection|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return IniSection[]
     */
    protected function getParents()
    {
        $parents = [];
        $currentSection = $this;
        while ($currentSection->hasParent())
        {
            $parent = $currentSection->getParent();
            $parents[] = $parent;
            $currentSection = $parent;
        }
        rsort($parents);

        return $parents;
    }

    /**
     * @return array
     */
    protected function composeContents()
    {
        $contents = [];
        $parents = $this->getParents();
        foreach ($parents as $section)
        {
            $contents = array_merge($contents, $section->getContents());
        }
        $contents = array_merge($contents, $this->getContents());

        return $contents;
    }

    /**
     * Get normalized item value
     *
     * @param string $itemName
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function get($itemName, $defaultValue = null)
    {
        $contents = $this->composeContents();
        $value = isset($contents[$itemName]) ? $contents[$itemName] : $defaultValue;

        return IniParser::i()->castItemValueToProperType($value);
    }

    /**
     * @param string $itemName
     * @param string|array|bool|null $itemValue
     *
     * @return $this
     * @throws InvalidDataException
     */
    public function set($itemName, $itemValue)
    {
        list($validationResult, $message) = IniParser::i()->validateItemName($itemName);
        if (false === $validationResult)
        {
            throw new InvalidDataException($message);
        }
        $this->contents[$itemName] = IniParser::i()->itemValuetoStringRepresentation($itemValue);

        return $this;
    }

    /**
     * @param string $itemName
     *
     * @return $this
     * @throws InvalidDataException
     */
    public function delete($itemName)
    {
        list($validationResult, $message) = IniParser::i()->validateItemName($itemName);
        if (false === $this->hasItem($itemName) || false === $validationResult)
        {
            throw new InvalidDataException($message);
        }
        unset($this->contents[$itemName]);

        return $this;
    }

    /**
     * @param string $itemName
     *
     * @return bool
     */
    public function hasItem($itemName)
    {
        $value = $this->get($itemName, null);

        return !is_null($value);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->contents);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->composeContents();
    }

    /**
     * @return string
     */
    public function toString()
    {
        $lines = $this->renderName();
        foreach ($this->contents as $itemName => $itemValue)
        {
            $lines = array_merge($lines, $this->renderItem($itemName, $itemValue));
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    /**
     * @return string[]
     */
    protected function renderName()
    {

        if ($this->hasParent())
        {
            $line = [sprintf('[%s : %s]', $this->getName(), $this->getParent()->getName())];
        }
        else
        {
            $line = [sprintf('[%s]', $this->getName())];
        }

        return $line;
    }

    /**
     * @param string $name
     * @param string|array $value
     *
     * @return array
     */
    protected function renderItem($name, $value)
    {
        if (is_array($value))
        {
            $lines = $this->renderArrayItem($name, $value);
        }
        else
        {
            $lines = $this->renderStringItem($name, $value);
        }

        return $lines;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return string[]
     */
    protected function renderStringItem($name, $value)
    {
        return [sprintf('%s = "%s"', $name, $value)];
    }

    /**
     * @param string $name
     * @param array $values
     *
     * @return array
     */
    protected function renderArrayItem($name, array $values)
    {
        $lines = [];
        $isAssocArray = (array_values($values) !== $values);
        foreach ($values as $key => $value)
        {
            $stringKey = $isAssocArray ? $key : '';
            $lines[] = sprintf('%s[%s] = "%s"', $name, $stringKey, $value);
        }

        return $lines;
    }

}