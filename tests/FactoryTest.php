<?php /** @noinspection PhpUnhandledExceptionInspection */

use PHPUnit\Framework\TestCase;
use Retrinko\Ini\Exceptions\InvalidDataException;
use Retrinko\Ini\IniFile;
use Retrinko\Ini\IniFile\Factory;
use Retrinko\Ini\IniSection;

class FactoryTest extends TestCase
{

    public function test_fromArray_withEmptyArray_returnsEmptyIniFile()
    {
        $array = [];
        $iniFile = Factory::fromArray($array);
        $this->assertInstanceOf(IniFile::class, $iniFile);
        $this->assertEquals('', $iniFile->toString());
        $this->assertEquals([], $iniFile->toArray());
    }

    public function test_fromArray_withInvalidArray_thrownsException()
    {
        $this->expectException(InvalidDataException::class);
        $array = ['a', 'b', 'c'];
        Factory::fromArray($array);
    }

    public function test_fromIniSections_withEmptyArray_returnsEmptyIniFile()
    {
        $array = [];
        $iniFile = Factory::fromIniSections($array);
        $this->assertInstanceOf(IniFile::class, $iniFile);
        $this->assertEquals('', $iniFile->toString());
        $this->assertEquals([], $iniFile->toArray());
    }

    public function test_fromIniSections_withInvalidArray_thrownsException()
    {
        $this->expectException(InvalidDataException::class);
        $array = ['a', 'b', 'c'];
        /** @noinspection PhpParamsInspection */
        Factory::fromIniSections($array);
    }

    public function test_fromIniSections_withProperSectionsArray_returnsIniFile()
    {
        $sectionA = new IniSection('section A');
        $sectionA->set('key1', 'val 1');
        $sectionA->set('key2', 'val 2');

        $sectionB = new IniSection('section B', $sectionA);
        $sectionB->set('key3', 'val 3');

        $sections = [$sectionA, $sectionB];

        $iniFile = Factory::fromIniSections($sections);
        $this->assertInstanceOf(IniFile::class, $iniFile);
        $this->assertEquals('val 1', $iniFile->get('section A', 'key1'));
        $this->assertEquals('val 2', $iniFile->get('section A', 'key2'));
        $this->assertEquals('default', $iniFile->get('section A', 'key3', 'default'));
        $this->assertEquals('val 1', $iniFile->get('section B', 'key1'));
        $this->assertEquals('val 2', $iniFile->get('section B', 'key2'));
        $this->assertEquals('val 3', $iniFile->get('section B', 'key3'));
    }

}