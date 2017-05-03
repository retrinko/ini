<?php



class IniParserTest extends PHPUnit_Framework_TestCase
{

    public function test_parseIniString_withValidString_returnsNotEmptyArray()
    {
        $string = file_get_contents(__DIR__.'/data/simple.ini');
        $parser = \FlmBus\Ini\IniParser::i();
        $parsedContents = $parser->parseIniString($string);
        $this->assertTrue(is_array($parsedContents) && !empty($parsedContents));
    }


    public function test_parseIniString_withEmptyString_returnsEmptyArray()
    {
        $string = '';
        $parser = \FlmBus\Ini\IniParser::i();
        $parsedContents = $parser->parseIniString($string);
        $this->assertTrue(is_array($parsedContents) && empty($parsedContents));
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_parseIniString_withInvalidIniString_throwsException()
    {
        $string = 'No ini string!!';
        $parser = \FlmBus\Ini\IniParser::i();
        $parser->parseIniString($string);
    }

    /**
     * @expectedException \FlmBus\Ini\Exceptions\InvalidDataException
     */
    public function test_parseArray_withInvalidArrayFormat_throwsException()
    {
        $array = ['a', 'b', 'c'];
        $parser = \FlmBus\Ini\IniParser::i();
        $parser->parseArray($array);
    }

    public function test_parseArray_withEmptyArray_returnsEmptyArray()
    {
        $array = [];
        $parser = \FlmBus\Ini\IniParser::i();
        $result = $parser->parseArray($array);
        $this->assertTrue(is_array($result) && empty($result));
    }

    public function test_parseArray_withProperArrayFormat_returnsIniSectionsArray()
    {
        $array = ['a'=>['key'=>'val-a'], 
                  'b'=>['key'=>'val-b'], 
                  'c'=>['key'=>'val-c']];
        $parser = \FlmBus\Ini\IniParser::i();
        $sections = $parser->parseArray($array);
        $this->assertTrue(is_array($sections));
        foreach ($sections as $section)
        {
            $this->assertTrue($section instanceof \FlmBus\Ini\IniSection);
        }
        $this->assertArrayHasKey('a', $sections);
        $this->assertArrayHasKey('b', $sections);
        $this->assertArrayHasKey('c', $sections);
    }


    public function test_castItemValueToProperType_withValidParam_returnsProperData()
    {
        $string = 'hello world';
        $casted = \FlmBus\Ini\IniParser::i()->castItemValueToProperType($string);
        $this->assertTrue(is_string($casted));
        $this->assertEquals('hello world', $casted);

        $int = '1';
        $casted = \FlmBus\Ini\IniParser::i()->castItemValueToProperType($int);
        $this->assertTrue(is_int($casted));
        $this->assertEquals(1, $casted);

        $float = '1.5';
        $casted = \FlmBus\Ini\IniParser::i()->castItemValueToProperType($float);
        $this->assertTrue(is_float($casted));
        $this->assertEquals(floatval(1.5), $casted);

        $bool = 'true';
        $casted = \FlmBus\Ini\IniParser::i()->castItemValueToProperType($bool);
        $this->assertTrue(is_bool($casted));
        $this->assertEquals(true, $casted);

        $null = 'null';
        $casted = \FlmBus\Ini\IniParser::i()->castItemValueToProperType($null);
        $this->assertTrue(is_null($casted));

    }
    
    public function test_itemValuetoStringRepresentation_withValidParam_returnStringRepresentation()
    {
        $string = 'hello world';
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($string);
        $this->assertEquals('hello world', $casted);

        $int = 1;
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($int);
        $this->assertEquals('1', $casted);

        $float = 1.5;
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($float);
        $this->assertEquals('1.5', $casted);

        $bool = true;
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($bool);
        $this->assertEquals('true', $casted);

        $null = null;
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($null);
        $this->assertEquals('null', $casted);

        $array = ['string'=>'hello world', 'int'=>1, 'float'=>1.5, 'bool'=>true, 'null'=>null];
        $expected = ['string'=>'hello world', 'int'=>'1', 'float'=>'1.5', 'bool'=>'true', 'null'=>'null'];
        $casted = \FlmBus\Ini\IniParser::i()->itemValuetoStringRepresentation($array);
        $this->assertEquals($expected, $casted);

    }

    public function test_validateItemName_returnProperValues()
    {
        $invalidNames = ['hello!', 'hello?', 'he(ll)o', 'he^llo', 'hell{o}', 'hell&o', 'he"llo', 'he||o',
                         '?{}|&~!()^"',
                         'null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none'];

        foreach ($invalidNames  as $invalidName)
        {
            $result = \FlmBus\Ini\IniParser::i()->validateItemName($invalidName);
            $this->assertTrue(is_array($result));
            $this->assertTrue(isset($result[0]));
            $this->assertTrue(is_bool($result[0]));
            $this->assertTrue(isset($result[1]));
            $this->assertTrue(is_string($result[1]));
            $this->assertFalse($result[0]);
            $this->assertTrue(strlen($result[1])>0);
        }

        $validName = 'hello';
        $result = \FlmBus\Ini\IniParser::i()->validateItemName($validName);
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result[0]));
        $this->assertTrue(is_bool($result[0]));
        $this->assertTrue(isset($result[1]));
        $this->assertTrue(is_string($result[1]));
        $this->assertTrue($result[0]);
        $this->assertTrue(strlen($result[1])>0);


    }

}