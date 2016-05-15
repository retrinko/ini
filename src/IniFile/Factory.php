<?php

namespace Retrinko\Ini\IniFile;

use Retrinko\Ini\Exceptions\InvalidDataException;
use Retrinko\Ini\IniFile;
use Retrinko\Ini\IniParser;
use Retrinko\Ini\IniSection;

class Factory
{
    /**
     * @param array $data
     *
     * @return IniFile
     * @throws \Retrinko\Ini\Exceptions\InvalidDataException
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
            if (false == $iniSection instanceof IniSection)
            {
                throw new InvalidDataException('Invalid data! ');
            }
            $iniFile->addSection($iniSection);
        }

        return $iniFile;
    }
    
}