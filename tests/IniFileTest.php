<?php



class IniFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \FlmBus\Ini\Exceptions\FileException
     */
    public function test_construct_withNotExistingFile_throwsException()
    {
        $file = 'no-file.ini';
        new \FlmBus\Ini\IniFile($file);
    }

    public function test_construct_withValidFile_returnsIniFileInstance()
    {
        $file = __DIR__.'/data/simple.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertTrue($ini instanceof \FlmBus\Ini\IniFile);
    }

    public function test_construct_withNoParams_returnsIniFileInstance()
    {
        $ini = new \FlmBus\Ini\IniFile();
        $this->assertTrue($ini instanceof \FlmBus\Ini\IniFile);
    }

    public function test_construct_withEmptyFile_returnsIniFileInstance()
    {
        $file = __DIR__.'/data/empty.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertTrue($ini instanceof \FlmBus\Ini\IniFile);
    }

    public function test_toArray_withValidFile_returnsProperArray()
    {
        $file = __DIR__ . '/data/simple.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertEquals([
                                'A' => [
                                    'key' => 'value (Section A)',
                                    'intVal' => '1',
                                    'floatVal' => '1.5',
                                    'boolValTrue' => 'true',
                                    'boolValFalse' => 'false',
                                    'arrayData' => ['red', 'green']
                                ],
                                'B' => ['key' => 'value (Section B)',
                                        'intVal' => '1',
                                        'floatVal' => '1.5',
                                        'boolValTrue' => 'true',
                                        'boolValFalse' => 'false',
                                        'arrayData' => ['red', 'green']
                                ],
                                'C' => ['foo' => 'bar (Section C)'],
                            ],
                            $ini->toArray());
    }

    public function test_get_withValidFileAndNotPresetField_returnsDefaultValue()
    {
        $file = __DIR__.'/data/simple.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertEquals('default', $ini->get('B', 'key2', 'default'));
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_get_withValidFileAndNotPresetSection_throwsException()
    {
        $file = __DIR__.'/data/simple.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $ini->get('NO-EXISTING-SECTION', 'key2', 'default');
    }

    public function test_get_withValidFileAndSimpleArray_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertEquals(['a', 'b'], $ini->get('B', 'simpleArray'));
    }

    public function test_get_withValidFileAndAssociativeArray_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertEquals(['one'=>1, 'two'=>2], $ini->get('B', 'associativeArray'));
    }

    public function test_get_withValidFileAndBoolValue_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertEquals(true, $ini->get('B', 'boolTrue'));
        $this->assertEquals(false, $ini->get('B', 'boolFalse'));
        $this->assertEquals(true, $ini->get('B', 'boolYes'));
        $this->assertEquals(false, $ini->get('B', 'boolNo'));
        $this->assertEquals(true, $ini->get('B', 'boolOn'));
        $this->assertEquals(false, $ini->get('B', 'boolOff'));
        $this->assertEquals(false, $ini->get('B', 'boolNone'));
    }

    public function test_get_withValidFileAndIntValue_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertTrue(is_int($ini->get('B', 'intVal')));
    }

    public function test_get_withValidFileAndFloatValue_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $this->assertTrue(is_float($ini->get('B', 'floatVal')));
    }

    public function test_delete_withValidSectionNameAndItemName_returnsProperValue()
    {
        $file = __DIR__.'/data/test.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $return = $ini->delete('test', 'A');
        $this->assertEquals($ini, $return);
    }

    public function test_delete_withValidSectionNameAndItemName_deletesItem()
    {
        $file = __DIR__.'/data/test.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $ini->delete('test', 'A');
        $this->assertFalse( $ini->getSection('test')->hasItem('A') );
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_delete_withValidSectionNameAndInvalidItemName_throwsException()
    {
        $file = __DIR__.'/data/test.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $ini->delete('test', 'NON-EXISTENT-ITEM');
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_delete_withInvalidSectionNameAndValidItemName_throwsException()
    {
        $file = __DIR__.'/data/test.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $ini->delete('NON-EXISTENT-SECTION', 'A');
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_delete_withInvalidSectionNameAndItemName_throwsException()
    {
        $file = __DIR__.'/data/test.ini';
        $ini = new \FlmBus\Ini\IniFile($file);
        $ini->delete('NON-EXISTENT-SECTION', 'NON-EXISTENT-ITEM');
    }
}