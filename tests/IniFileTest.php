<?php /** @noinspection PhpUnhandledExceptionInspection */


use PHPUnit\Framework\TestCase;
use Retrinko\Ini\Exceptions\FileException;
use Retrinko\Ini\Exceptions\InvalidDataException;
use Retrinko\Ini\IniFile;

class IniFileTest extends TestCase
{
    public function test_construct_withNotExistingFile_throwsException()
    {
        $this->expectException(FileException::class);
        $file = 'no-file.ini';
        new IniFile($file);
    }

    public function test_construct_withValidFile_returnsIniFileInstance()
    {
        $file = __DIR__.'/data/simple.ini';
        $ini = new IniFile($file);
        $this->assertInstanceOf(IniFile::class, $ini);
    }

    public function test_construct_withNoParams_returnsIniFileInstance()
    {
        $ini = new IniFile();
        $this->assertInstanceOf(IniFile::class, $ini);
    }

    public function test_construct_withEmptyFile_returnsIniFileInstance()
    {
        $file = __DIR__.'/data/empty.ini';
        $ini = new IniFile($file);
        $this->assertInstanceOf(IniFile::class, $ini);
    }

    public function test_toArray_withValidFile_returnsProperArray()
    {
        $file = __DIR__ . '/data/simple.ini';
        $ini = new IniFile($file);
        $this->assertEquals([
                                'A' => [
                                    'key' => 'value (Section A)',
                                    'intVal' => '11',
                                    'floatVal' => '1.5',
                                    'boolValTrue' => 'false',
                                    'boolValFalse' => 'true',
                                    'arrayData' => ['red', 'green']
                                ],
                                'B' => ['key' => 'value (Section B - LOCAL)',
                                        'intVal' => '11',
                                        'floatVal' => '1.5',
                                        'boolValTrue' => 'false',
                                        'boolValFalse' => 'true',
                                        'arrayData' => ['red', 'green']
                                ],
                                'C' => ['foo' => 'bar (Section C) LOCAL'],
                            ],
                            $ini->toArray());
    }

    public function test_get_withValidFileAndNotPresetField_returnsDefaultValue()
    {
        $file = __DIR__.'/data/simple.ini';
        $ini = new IniFile($file);
        $this->assertEquals('default', $ini->get('B', 'key2', 'default'));
    }

    public function test_get_withValidFileAndNotPresetSection_throwsException()
    {
        $this->expectException(InvalidDataException::class);
        $file = __DIR__.'/data/simple.ini';
        $ini = new IniFile($file);
        $ini->get('NO-EXISTING-SECTION', 'key2', 'default');
    }

    public function test_get_withValidFileAndSimpleArray_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new IniFile($file);
        $this->assertEquals(['a', 'b', true, true, true, false, false, false, 1, 0.5], $ini->get('B', 'simpleArray'));
    }

    public function test_get_withValidFileAndAssociativeArray_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new IniFile($file);
        $this->assertEquals([
            'one'=>1,
            'two'=>2,
            'true'=>true,
            'false'=>false,
            'on'=>true,
            'off'=>false,
            'yes'=>true,
            'no'=>false,
        ],
            $ini->get('B', 'associativeArray'));
    }

    public function test_get_withValidFileAndBoolValue_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new IniFile($file);
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
        $ini = new IniFile($file);
        $this->assertIsInt($ini->get('B', 'intVal'));
    }

    public function test_get_withValidFileAndFloatValue_returnsProperValue()
    {
        $file = __DIR__.'/data/complex.ini';
        $ini = new IniFile($file);
        $this->assertIsFloat($ini->get('B', 'floatVal'));
    }
}