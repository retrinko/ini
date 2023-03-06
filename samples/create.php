<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Retrinko\Ini\IniFile;
use Retrinko\Ini\IniSection;

try
{
    // Create new IniFile instance
    $iniFile = new IniFile();

    // Create section "base"
    $section = new IniSection('base');
    // Add items to section "base"
    $section->set('hello', 'world');
    $section->set('colors', ['red', 'green']);
    $section->set('rgb', ['red'=>'AA0000', 'green'=>'00AA00']);
    $section->set('width', 25);
    $section->set('height', 50.33);
    $section->set('bool', true);
    $section->set('nullValue', null);
    // Add section "base" to ini file
    $iniFile->addSection($section);

    // Add child section "child"
    $childSection = new IniSection('child', $section);
    // Add items to section "child"
    $childSection->set('height', 20);
    $childSection->set('width', 20);
    // Add section "child" to ini file
    $iniFile->addSection($childSection);

    // Add item values to sections
    $iniFile->set('base', 'new-item', 'value');
    $iniFile->set('child', 'last-item', 'last');

    // Save to file
    $iniFile->save(__DIR__.'/test.ini');

}
catch (Exception $e)
{
    /** @noinspection ForgottenDebugOutputInspection */
    var_dump($e->getMessage());
}

