<?php


use PHPUnit\Framework\TestCase;
use Retrinko\Ini\Exceptions\FileException;
use Retrinko\Ini\IniFileLocator;

class IniFileLocatorTest extends TestCase
{

    public function test_i_returnsIniFileLocatorInstance()
    {
        $instance = IniFileLocator::i();
        $this->assertInstanceOf(IniFileLocator::class, $instance);
    }

    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws FileException
     */
    public function test_locate_returnsSameFileIfNoExistLocalFile()
    {
        $file = __DIR__.'/data/simple.ini';
        $path = IniFileLocator::i()->i()->locate($file);
        $this->assertEquals(realpath($file), realpath($path));
    }


    /**
     * @depends test_i_returnsIniFileLocatorInstance
     * @throws FileException
     */
    public function test_locate_unexistingFile_throwsException()
    {
        $file = __DIR__.'/data/nofile.ini';
        $this->expectException(FileException::class);
        IniFileLocator::i()->i()->locate($file);
    }

}