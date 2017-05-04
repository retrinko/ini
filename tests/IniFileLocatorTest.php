<?php


class IniFileLocatorTest extends PHPUnit_Framework_TestCase
{

    public function test_i_returnsIniFileLocatorInstance()
    {
        $instance = \FlmBus\Ini\IniFileLocator::i();
        $this->assertTrue($instance instanceof \FlmBus\Ini\IniFileLocator);
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws \FlmBus\Ini\Exceptions\FileException
     */
    public function test_locate_returnsSameFileIfNoExistLocalFile()
    {
        $file = __DIR__.'/data/simple.ini';
        $path = \FlmBus\Ini\IniFileLocator::i()->i()->locate($file);
        $this->assertEquals(realpath($file), realpath($path));
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws \FlmBus\Ini\Exceptions\FileException
     */
    public function test_locate_returnsLocalFileIfExists()
    {
        $file = __DIR__.'/data/test.ini';
        $expected = __DIR__.'/data/test.local.ini';
        $path = \FlmBus\Ini\IniFileLocator::i()->i()->locate($file);
        $this->assertEquals(realpath($expected), realpath($path));
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @expectedException \FlmBus\Ini\Exceptions\FileException
     * @throws \FlmBus\Ini\Exceptions\FileException
     */
    public function test_locate_unexistingFile_throwsException()
    {
        $file = __DIR__.'/data/nofile.ini';
        \FlmBus\Ini\IniFileLocator::i()->i()->locate($file);
    }

}