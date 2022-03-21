<?php /** @noinspection PhpUnhandledExceptionInspection */

use PHPUnit\Framework\TestCase;
use Retrinko\Ini\Exceptions\InvalidDataException;
use Retrinko\Ini\IniSection;

class IniSectionTest extends TestCase
{

    public function test_setContents_withProperParameters_returnsSection()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new IniSection('section');
        $section = $section->setContents($contents);
        $this->assertInstanceOf(IniSection::class, $section);
    }

    public function test_setContents_withBadParameters_thrownsInvalidDataException()
    {
        $this->expectException(InvalidDataException::class);
        $contents = 'bad contents';
        $section = new IniSection('section');
        /** @noinspection PhpParamsInspection */
        $section->setContents($contents);
    }

    public function test_set_withProperParamaters_retrunsIniSection()
    {
        $section = new IniSection('test');
        $section = $section->set('hello', 'world');
        $this->assertInstanceOf(IniSection::class, $section);
    }

    public function test_set_withInvalidItemName_thrownsInvalidDataException()
    {
        $this->expectException(InvalidDataException::class);
        $invalidItemName = 'hello!';
        $section = new IniSection('test');
        $section->set($invalidItemName, 'world');
    }

    public function test_get_withProperParameters_returnsProperValue()
    {
        $parentContents = ['k0'=>'v0', 'k1'=>'v1', 'k2'=>'v2'];
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $parent = new IniSection('parentSection');
        $parent->setContents($parentContents);
        $section = new IniSection('test', $parent);
        $section->setContents($contents);
        $this->assertEquals('v0', $section->get('k0'));
        $this->assertEquals('V1', $section->get('k1'));
        $this->assertEquals('V2', $section->get('k2'));
        $this->assertEquals('V3', $section->get('k3'));
    }

    public function test_get_withNotPresentFieldAndDefaultValue_returnsDefaultValue()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new IniSection('test');
        $section->setContents($contents);
        $this->assertEquals('default', $section->get('noPresentField', 'default'));
    }

    public function test_hasField_returnsAlwaysBool()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new IniSection('test');
        $section->setContents($contents);
        $this->assertIsBool($section->hasItem('k2'));
        $this->assertIsBool($section->hasItem('k0'));
    }

    public function test_hasField_withPresentField_returnsTrue()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new IniSection('test');
        $section->setContents($contents);
        $this->assertTrue($section->hasItem('k1'));

    }

    public function test_hasField_withNotPresentField_returnsFalse()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new IniSection('test');
        $section->setContents($contents);
        $this->assertFalse($section->hasItem('k0'));
    }

    public function test_toArray_returnsProperArray()
    {
        $parentContents = ['k0'=>'v0', 'k1'=>'v1'];
        $contents = ['k1'=>'V1', 'k2'=>'v2'];
        $parent = new IniSection('parentSection');
        $parent->setContents($parentContents);
        $section = new IniSection('test', $parent);
        $section->setContents($contents);

        $this->assertEquals(['k0'=>'v0', 'k1'=>'V1', 'k2'=>'v2'], $section->toArray());
    }

    public function test_toString_returnsProperString()
    {
        $section = new IniSection('test');
        $section->set('hello', 'world');
        $section->set('bool', true);
        $section->set('int', 3);
        $section->set('nullVal', null);

        $expected = <<<EOF
[test]
hello = "world"
bool = "true"
int = "3"
nullVal = "null"

EOF;

        $string = $section->toString();
        $this->assertEquals($expected, $string);
    }


}