<?php


namespace Retrinko\Ini;


use Retrinko\Ini\Exceptions\FileException;

class IniFileLocator
{
    const LOCAL_FILE_CHUNK = 'local';
    /**
     * @var array
     */
    protected $supportedExtensions = ['ini'];


    /**
     * @var IniFileLocator
     */
    protected static $instance;

    /**
     * IniFileLocator constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @return IniFileLocator
     */
    public static function i()
    {
        return self::getInstance();
    }

    /**
     * @return IniFileLocator
     */
    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __clone()
    {
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function __wakeup()
    {
    }

    /**
     * @param string $filePath
     *
     * @return string
     * @throws FileException
     */
    public function locate($filePath)
    {
        $file = $filePath;
        if (!is_file($filePath))
        {
            throw new FileException(sprintf('Invalid file! File "%s" does not exist.',
                                            $filePath));
        }

        if (!is_readable($filePath))
        {
            throw new FileException(sprintf('Invalid file! File "%s" is not readable.',
                                            $filePath));
        }

        $pathInfo = pathinfo($filePath);
        if (!isset($pathInfo['extension'])
            || !in_array($pathInfo['extension'], $this->supportedExtensions)
        )
        {
            throw new FileException(sprintf('Invalid file extension! Supported file extensions: %s',
                implode(', ', $this->supportedExtensions)));
        }

        return realpath($file);
    }

    /**
     * @param $filePath
     * @return null|string
     * @throws FileException
     */
    public function locateLocalFile($filePath)
    {
        $file = null;
        $pathInfo = pathinfo($filePath);
        $localFileName = $pathInfo['dirname'] . DIRECTORY_SEPARATOR
            . $this->composeLocalFileName($pathInfo['filename'],
                $pathInfo['extension']);
        if (is_file($localFileName) && is_readable($localFileName))
        {
            $file = realpath($localFileName);
        }

        return $file;
    }

    /**
     * @param string $baseName
     * @param string $extension
     *
     * @return string
     */
    protected function composeLocalFileName($baseName, $extension)
    {
        return $baseName . '.' . self::LOCAL_FILE_CHUNK . '.' . $extension;
    }
}