<?php

namespace Retrinko\Ini;

use Retrinko\Ini\Exceptions\FileException;
use Retrinko\Ini\Exceptions\InvalidDataException;

class IniFile
{
    /**
     * @var string
     */
    protected $file;
    /**
     * @var string
     */
    protected $localFile;
    /**
     * @var IniParser;
     */
    protected $parser;
    /**
     * @var IniSection[]
     */
    protected $sections = [];

    /**
     * IniFile constructor.
     *
     * @param string|null $file
     *
     * @throws FileException
     * @throws InvalidDataException
     */
    public function __construct($file = null)
    {
        $rawContents = '';
        $localRawContents = '';
        $this->parser = IniParser::i();
        if (!is_null($file))
        {
            $this->file = IniFileLocator::i()->locate($file);
            $rawContents = file_get_contents($this->file);
            $this->localFile = IniFileLocator::i()->locateLocalFile($file);
            if (!is_null($this->localFile)) {
                $localRawContents = file_get_contents($this->localFile);
            }
        }
        $this->sections = $this->parser->parseIniString($rawContents, $localRawContents);
    }

    /**
     * @param string $file
     *
     * @return IniFile
     * @throws FileException
     * @throws InvalidDataException
     */
    public static function load($file)
    {
        return new self($file);
    }

    /**
     * @param null|string $outputFile
     *
     * @throws FileException
     */
    public function save($outputFile = null)
    {
        if (is_null($outputFile))
        {
            if (is_null($this->file))
            {
                throw new FileException('No output file set! Please, set an output file.');
            }
            $outputFile = $this->file;
        }

        if (is_file($outputFile) && !is_writable($outputFile))
        {
            throw new FileException(sprintf('Output file "%s" is not writable!', $outputFile));
        }

        $result = file_put_contents($outputFile, $this->toString());
        if (false === $result)
        {
            throw new FileException(sprintf('Error writing file "%s"!', $outputFile));
        }
    }

    /**
     * @param IniSection $section
     *
     * @return $this
     * @throws InvalidDataException
     */
    public function addSection(IniSection $section)
    {
        if ($this->hasSection($section->getName()))
        {
            throw new InvalidDataException(sprintf('Section "%s" already exists!',
                                                   $section->getName()));
        }

        if ($section->hasParent())
        {
            if (!isset($this->sections[$section->getParent()->getName()]))
            {
                throw new InvalidDataException(sprintf('Parent section "%s" does not exists!',
                                                       $section->getParent()->getName()));
            }
        }

        $this->sections[$section->getName()] = $section;

        return $this;
    }

    /**
     * @param string $sectionName
     *
     * @return bool
     */
    public function hasSection($sectionName)
    {
        return isset($this->sections[$sectionName]);
    }

    /**
     * @param string $sectionName
     *
     * @return IniSection
     * @throws InvalidDataException
     */
    public function getSection($sectionName)
    {
        if (!$this->hasSection($sectionName))
        {
            throw new InvalidDataException(sprintf('Section "%s" does not exists!', $sectionName));
        }

        return $this->sections[$sectionName];
    }

    /**
     * Get normalized item value
     *
     * @param string $sectionName
     * @param string $itemName
     * @param mixed $defaultValue
     *
     * @return mixed
     * @throws InvalidDataException
     */
    public function get($sectionName, $itemName, $defaultValue = null)
    {
        $section = $this->getSection($sectionName);

        return $section->get($itemName, $defaultValue);
    }

    /**
     * @param string $sectionName
     * @param string $itemName
     * @param string $itemValue
     *
     * @return $this
     * @throws InvalidDataException
     */
    public function set($sectionName, $itemName, $itemValue)
    {
        $section = $this->getSection($sectionName);
        $section->set($itemName, $itemValue);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->sections as $sectionName => $section)
        {
            $data[$sectionName] = $section->toArray();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $contents = [];
        foreach ($this->sections as $section)
        {
            $contents[] = $section->toString();
        }

        return implode(PHP_EOL, $contents);
    }
}