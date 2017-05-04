<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FlmBus\Ini\IniFile;

try
{
    // Load contents from ini file
    $iniFile = IniFile::load((__DIR__ . '/sample.ini'));

    // Read key1 value from default section
    $key1 = $iniFile->get('default', 'key1');
    printLn('default section, key1 value: %s', $key1);

    // Read key1 value from A section
    $key1 = $iniFile->get('A', 'key1');
    printLn('A section, key1 value: %s', $key1);

    // Read key1 value from B section
    $key1 = $iniFile->get('B', 'key1');
    printLn('B section, key1 value: %s', $key1);

    // Read boolYes value from B section
    $boolYes = $iniFile->get('B', 'boolYes');
    printLn('B section, boolYes value: %s', $boolYes);

    // Get ini file contents as array
    $array = $iniFile->toArray();
    printLn('Contents as array: %s', PHP_EOL . var_export($array, true));
}
catch (\Exception $e)
{
    printLn('Exception! %s', $e->getMessage());
}


function printLn($string)
{
    $vars = func_get_args();
    array_shift($vars);
    vprintf('>>> ' . $string . PHP_EOL, $vars);
}
