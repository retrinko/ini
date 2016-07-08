<?php


class IniFileLocatorTest extends PHPUnit_Framework_TestCase
{

    public function test_i_returnsIniFileLocatorInstance()
    {
        $instance = \Retrinko\Ini\IniFileLocator::i();
        $this->assertTrue($instance instanceof \Retrinko\Ini\IniFileLocator);
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws \Retrinko\Ini\Exceptions\FileException
     */
    public function test_locate_returnsSameFileIfNoExistLocalFile()
    {
        $file = __DIR__.'/data/simple.ini';
        $path = \Retrinko\Ini\IniFileLocator::i()->i()->locate($file);
        $this->assertEquals(realpath($file), realpath($path));
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws \Retrinko\Ini\Exceptions\FileException
     */
    public function test_locate_returnsLocalFileIfExists()
    {
        $file = __DIR__.'/data/test.ini';
        $expected = __DIR__.'/data/test.local.ini';
        $path = \Retrinko\Ini\IniFileLocator::i()->i()->locate($file);
        $this->assertEquals(realpath($expected), realpath($path));
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @expectedException \Retrinko\Ini\Exceptions\FileException
     * @throws \Retrinko\Ini\Exceptions\FileException
     */
    public function test_locate_unexistingFile_throwsException()
    {
        $file = __DIR__.'/data/nofile.ini';
        \Retrinko\Ini\IniFileLocator::i()->i()->locate($file);
    }

}