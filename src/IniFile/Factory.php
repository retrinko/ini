<?php

namespace FlmBus\Ini\IniFile;

use FlmBus\Ini\Exceptions\InvalidDataException;
use FlmBus\Ini\IniFile;
use FlmBus\Ini\IniParser;
use FlmBus\Ini\IniSection;

class Factory
{
    /**
     * @param array $data
     *
     * @return IniFile
     * @throws \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public static function fromArray(array $data)
    {
        $iniSections = IniParser::i()->parseArray($data);

        return self::fromIniSections($iniSections);
    }

    /**
     * @param IniSection[] $iniSections
     *
     * @return IniFile
     * @throws InvalidDataException
     */
    public static function fromIniSections(array $iniSections)
    {
        $iniFile = new IniFile();
        foreach ($iniSections as $iniSection)
        {
            if (false === $iniSection instanceof IniSection)
            {
                throw new InvalidDataException('Invalid data! ');
            }
            $iniFile->addSection($iniSection);
        }

        return $iniFile;
    }

    /**
     * @param string $file File path
     *
     * @return IniFile
     */
    public static function fromFile($file)
    {
        return new IniFile($file);
    }

}