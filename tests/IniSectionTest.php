<?php

class IniSectionTest extends PHPUnit_Framework_TestCase
{

    public function test_setContents_withProperParameters_returnsSection()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new \Retrinko\Ini\IniSection('section');
        $section = $section->setContents($contents);
        $this->assertTrue($section instanceof \Retrinko\Ini\IniSection);
    }

    /**
     * @expectedException \Retrinko\Ini\Exceptions\InvalidDataException
     */
    public function test_setContents_withBadParameters_thrownsInvalidDataException()
    {
        $contents = 'bad contents';
        $section = new \Retrinko\Ini\IniSection('section');
        $section->setContents($contents);
    }

    public function test_set_withProperParamaters_retrunsIniSection()
    {
        $section = new \Retrinko\Ini\IniSection('test');
        $section = $section->set('hello', 'world');
        $this->assertTrue($section instanceof \Retrinko\Ini\IniSection);
    }

    /**
     * @expectedException \Retrinko\Ini\Exceptions\InvalidDataException
     */
    public function test_set_withInvalidItemName_thrownsInvalidDataException()
    {
        $invalidItemName = 'hello!';
        $section = new \Retrinko\Ini\IniSection('test');
        $section->set($invalidItemName, 'world');
    }

    public function test_get_withProperParameters_returnsProperValue()
    {
        $parentContents = ['k0'=>'v0', 'k1'=>'v1', 'k2'=>'v2'];
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $parent = new \Retrinko\Ini\IniSection('parentSection');
        $parent->setContents($parentContents);
        $section = new \Retrinko\Ini\IniSection('test', $parent);
        $section->setContents($contents);
        $this->assertEquals('v0', $section->get('k0'));
        $this->assertEquals('V1', $section->get('k1'));
        $this->assertEquals('V2', $section->get('k2'));
        $this->assertEquals('V3', $section->get('k3'));
    }

    public function test_get_withNotPresentFieldAndDefaultValue_returnsDefaultValue()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new \Retrinko\Ini\IniSection('test');
        $section->setContents($contents);
        $this->assertEquals('default', $section->get('noPresentField', 'default'));
    }

    public function test_hasField_returnsAlwaysBool()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new \Retrinko\Ini\IniSection('test');
        $section->setContents($contents);
        $this->assertTrue(is_bool($section->hasItem('k2')));
        $this->assertTrue(is_bool($section->hasItem('k0')));
    }

    public function test_hasField_withPresentField_returnsTrue()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new \Retrinko\Ini\IniSection('test');
        $section->setContents($contents);
        $this->assertTrue($section->hasItem('k1'));

    }

    public function test_hasField_withNotPresentField_returnsFalse()
    {
        $contents = ['k1'=>'V1', 'k2'=>'V2', 'k3'=>'V3'];
        $section = new \Retrinko\Ini\IniSection('test');
        $section->setContents($contents);
        $this->assertFalse($section->hasItem('k0'));
    }

    public function test_toArray_returnsProperArray()
    {
        $parentContents = ['k0'=>'v0', 'k1'=>'v1'];
        $contents = ['k1'=>'V1', 'k2'=>'v2'];
        $parent = new \Retrinko\Ini\IniSection('parentSection');
        $parent->setContents($parentContents);
        $section = new \Retrinko\Ini\IniSection('test', $parent);
        $section->setContents($contents);

        $this->assertEquals(['k0'=>'v0', 'k1'=>'V1', 'k2'=>'v2'], $section->toArray());
    }

    public function test_toString_returnsProperString()
    {
        $section = new \Retrinko\Ini\IniSection('test');
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

    public function test_delete_withValidSection_deletesItem()
    {
        $section = new \Retrinko\Ini\IniSection('SECTION');
        $section->set('a', 1);
        $section->set('DELETEME', 2);
        $section->set('c', 3);
        $section->delete('DELETEME');
        $this->assertFalse($section->hasItem('DELETEME'));
    }

    /**
     * @expectedException \Retrinko\Ini\Exceptions\InvalidDataException
     */
    public function test_delete_withInvalidItem_throwsException()
    {
        $section = new \Retrinko\Ini\IniSection('SECTION');
        $section->set('a', 1);
        $section->set('c', 3);
        $section->delete('DELETEME');
    }


}
