<?php /** @noinspection PhpUnhandledExceptionInspection */


use PHPUnit\Framework\TestCase;
use Retrinko\Ini\Exceptions\InvalidDataException;
use Retrinko\Ini\IniParser;
use Retrinko\Ini\IniSection;

class IniParserTest extends TestCase
{

    public function test_parseIniString_withValidString_returnsNotEmptyArray()
    {
        $string = file_get_contents(__DIR__.'/data/simple.ini');
        $parser = IniParser::i();
        $parsedContents = $parser->parseIniString($string);
        $this->assertTrue(is_array($parsedContents) && !empty($parsedContents));
    }


    public function test_parseIniString_withEmptyString_returnsEmptyArray()
    {
        $string = '';
        $parser = IniParser::i();
        $parsedContents = $parser->parseIniString($string);
        $this->assertTrue(is_array($parsedContents) && empty($parsedContents));
    }

    public function test_parseIniString_withInvalidIniString_throwsException()
    {
        $this->expectException(InvalidDataException::class);
        $string = 'No ini string!!';
        $parser = IniParser::i();
        $parser->parseIniString($string);
    }

    public function test_parseArray_withInvalidArrayFormat_throwsException()
    {
        $this->expectException(InvalidDataException::class);
        $array = ['a', 'b', 'c'];
        $parser = IniParser::i();
        $parser->parseArray($array);
    }

    public function test_parseArray_withEmptyArray_returnsEmptyArray()
    {
        $array = [];
        $parser = IniParser::i();
        $result = $parser->parseArray($array);
        $this->assertTrue(is_array($result) && empty($result));
    }

    public function test_parseArray_withProperArrayFormat_returnsIniSectionsArray()
    {
        $array = ['a'=>['key'=>'val-a'],
                  'b'=>['key'=>'val-b'],
                  'c'=>['key'=>'val-c']];
        $parser = IniParser::i();
        $sections = $parser->parseArray($array);
        $this->assertIsArray($sections);
        foreach ($sections as $section)
        {
            $this->assertInstanceOf(IniSection::class, $section);
        }
        $this->assertArrayHasKey('a', $sections);
        $this->assertArrayHasKey('b', $sections);
        $this->assertArrayHasKey('c', $sections);
    }


    public function test_castItemValueToProperType_withValidParam_returnsProperData()
    {
        $string = 'hello world';
        $casted = IniParser::i()->castItemValueToProperType($string);
        $this->assertIsString($casted);
        $this->assertEquals('hello world', $casted);

        $int = '1';
        $casted = IniParser::i()->castItemValueToProperType($int);
        $this->assertIsInt($casted);
        $this->assertEquals(1, $casted);

        $float = '1.5';
        $casted = IniParser::i()->castItemValueToProperType($float);
        $this->assertIsFloat($casted);
        $this->assertEquals(floatval(1.5), $casted);

        $bool = 'true';
        $casted = IniParser::i()->castItemValueToProperType($bool);
        $this->assertIsBool($casted);
        $this->assertEquals(true, $casted);

        $null = 'null';
        $casted = IniParser::i()->castItemValueToProperType($null);
        $this->assertNull($casted);

    }

    public function test_itemValuetoStringRepresentation_withValidParam_returnStringRepresentation()
    {
        $string = 'hello world';
        $casted = IniParser::i()->itemValuetoStringRepresentation($string);
        $this->assertEquals('hello world', $casted);

        $int = 1;
        $casted = IniParser::i()->itemValuetoStringRepresentation($int);
        $this->assertEquals('1', $casted);

        $float = 1.5;
        $casted = IniParser::i()->itemValuetoStringRepresentation($float);
        $this->assertEquals('1.5', $casted);

        $bool = true;
        $casted = IniParser::i()->itemValuetoStringRepresentation($bool);
        $this->assertEquals('true', $casted);

        $null = null;
        $casted = IniParser::i()->itemValuetoStringRepresentation($null);
        $this->assertEquals('null', $casted);

        $array = ['string'=>'hello world', 'int'=>1, 'float'=>1.5, 'bool'=>true, 'null'=>null];
        $expected = ['string'=>'hello world', 'int'=>'1', 'float'=>'1.5', 'bool'=>'true', 'null'=>'null'];
        $casted = IniParser::i()->itemValuetoStringRepresentation($array);
        $this->assertEquals($expected, $casted);

    }

    public function test_validateItemName_returnProperValues()
    {
        $invalidNames = ['hello!', 'hello?', 'he(ll)o', 'he^llo', 'hell{o}', 'hell&o', 'he"llo', 'he||o',
                         '?{}|&~!()^"',
                         'null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none'];

        foreach ($invalidNames  as $invalidName)
        {
            $result = IniParser::i()->validateItemName($invalidName);
            $this->assertIsArray($result);
            $this->assertTrue(isset($result[0]));
            $this->assertIsBool($result[0]);
            $this->assertTrue(isset($result[1]));
            $this->assertIsString($result[1]);
            $this->assertFalse($result[0]);
            $this->assertTrue(strlen($result[1])>0);
        }

        $validName = 'hello';
        $result = IniParser::i()->validateItemName($validName);
        $this->assertIsArray($result);
        $this->assertTrue(isset($result[0]));
        $this->assertIsBool($result[0]);
        $this->assertTrue(isset($result[1]));
        $this->assertIsString($result[1]);
        $this->assertTrue($result[0]);
        $this->assertTrue(strlen($result[1])>0);


    }

}