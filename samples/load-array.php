<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Retrinko\Ini\IniFile;

try
{
    // Array with ini data
    $data = ['section 1' => ['key1' => 'value 1.1',
                             'key2' => 'value 1.2'],
             'section 2' => ['key3' => 'value 2.3'],
             'section 3 : section 1' => ['key1' => 'value 3.1'],
             'section 4' => ['intVal' => 1,
                             'floatVal' => 1.2,
                             'boolVal' => false,
                             'nullVal' => null,
                             'arrayVal' => ['a'=>'A', 'b'=>'B', 'c'=>'C']]
    ];

    // Get an IniFile instance from the previous array
    $iniFile = IniFile\Factory::fromArray($data);

    // Obtain some data
    $val = $iniFile->get('section 1', 'key1');
    printLn('section 1, key1: %s', $val);

    $val = $iniFile->get('section 2', 'key1', 'no value!');
    printLn('section 2, key1: %s', $val);

    $val = $iniFile->get('section 3', 'key1');
    printLn('section 3, key1: %s', $val);

    $val = $iniFile->get('section 3', 'key2');
    printLn('section 3, key2: %s', $val);

    $val = $iniFile->get('section 4', 'intVal');
    printLn('section 4, intVal: %s', $val);

    $val = $iniFile->get('section 4', 'floatVal');
    printLn('section 4, floatVal: %s', $val);

    $val = $iniFile->get('section 4', 'boolVal');
    printLn('section 4, boolVal: %s', is_bool($val) ? true == $val ? 'true' : 'false' : $val);

    $val = $iniFile->get('section 4', 'nullVal');
    printLn('section 4, nullVal: %s', is_null($val) ? 'null' : $val);

    $val = $iniFile->get('section 4', 'arrayVal');
    printLn('section 4, arrayVal: %s', var_export($val, true));

    // Save to ini file
    $outputFile = __DIR__ . '/array-to-inifile.ini';
    $iniFile->save($outputFile);
    printLn('Ini file saved to: %s', $outputFile);

}
catch (Exception $e)
{
    printLn('Exception! %s', $e->getMessage());
}


function printLn($string)
{
    $vars = func_get_args();
    array_shift($vars);
    vprintf('>>> ' . $string . PHP_EOL, $vars);
}