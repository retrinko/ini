<?php

class FactoryTest extends PHPUnit_Framework_TestCase
{

    public function test_fromArray_withEmptyArray_returnsEmptyIniFile()
    {
        $array = [];
        $iniFile = \FlmBus\Ini\IniFile\Factory::fromArray($array);
        $this->assertTrue($iniFile instanceof \FlmBus\Ini\IniFile);
        $this->assertEquals('', $iniFile->toString());
        $this->assertEquals([], $iniFile->toArray());
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_fromArray_withInvalidArray_thrownsException()
    {
        $array = ['a', 'b', 'c'];
        \FlmBus\Ini\IniFile\Factory::fromArray($array);
    }

    public function test_fromIniSections_withEmptyArray_returnsEmptyIniFile()
    {
        $array = [];
        $iniFile = \FlmBus\Ini\IniFile\Factory::fromIniSections($array);
        $this->assertTrue($iniFile instanceof \FlmBus\Ini\IniFile);
        $this->assertEquals('', $iniFile->toString());
        $this->assertEquals([], $iniFile->toArray());
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_fromIniSections_withInvalidArray_thrownsException()
    {
        $array = ['a', 'b', 'c'];
        \FlmBus\Ini\IniFile\Factory::fromIniSections($array);
    }

    public function test_fromIniSections_withProperSectionsArray_returnsIniFile()
    {
        $sectionA = new \FlmBus\Ini\IniSection('section A');
        $sectionA->set('key1', 'val 1');
        $sectionA->set('key2', 'val 2');

        $sectionB = new \FlmBus\Ini\IniSection('section B', $sectionA);
        $sectionB->set('key3', 'val 3');

        $sections = [$sectionA, $sectionB];

        $iniFile = \FlmBus\Ini\IniFile\Factory::fromIniSections($sections);
        $this->assertTrue($iniFile instanceof \FlmBus\Ini\IniFile);
        $this->assertEquals('val 1', $iniFile->get('section A', 'key1'));
        $this->assertEquals('val 2', $iniFile->get('section A', 'key2'));
        $this->assertEquals('default', $iniFile->get('section A', 'key3', 'default'));
        $this->assertEquals('val 1', $iniFile->get('section B', 'key1'));
        $this->assertEquals('val 2', $iniFile->get('section B', 'key2'));
        $this->assertEquals('val 3', $iniFile->get('section B', 'key3'));
    }

}