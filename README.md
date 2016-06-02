[![Build Status](https://travis-ci.org/retrinko/ini.svg?branch=master)](https://travis-ci.org/retrinko/ini)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/retrinko/ini/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/retrinko/ini/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/retrinko/ini/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/retrinko/ini/?branch=master)

# retrinko/ini

Library for parsing and writing ini files.

Features provided over native PHP ini parser:

- Throws exceptions instead of PHP errors.
- Better type support.
- Section inheritance.

_NOTE_: This parser does not allow orphan items (items defined outside a section). 

##  Installation

Install the latest version with

    $ composer require retrinko/ini
    
##  Basic usage



###  Reading ini contents:

Ini file contents (sample.ini):

    [default]
    key1 = default value1
    key2 = default value2
    
    ; A section inherits default section
    [A : default]
    key1 = A value1 ; overrides default section key1 item
    key3 = A value3 ; add new item
    
    ; B section inherits A section
    [B : A]
    key1 = B value1 ; overrides A section key1 item
    key3 = B value3 ; overrides A section key3 item
    ; Simple array
    A[] = a
    A[] = b
    ; Assoc. array
    B[one] = 1
    B[two] = 2
    ; Bool values
    boolTrue = true
    boolFalse = false
    boolYes = yes
    boolNo = no
    boolOn = on
    boolOff = off
    boolNone = none
    intVal = 3
    floatVal = 5.7
    
PHP sample code for reading ini file (sample.ini):

    use Retrinko\Ini\IniFile;
    
    try
    {
        // Load ini file
        $iniFile = IniFile::load((__DIR__ . '/sample.ini'));
    
        // Read "key1" value from "default" section
        $key1 = $iniFile->get('default', 'key1');
        
        // Read "key1" value from "A" section
        $key1 = $iniFile->get('A', 'key1');
        
        // Read "key1" value from "B" section
        $key1 = $iniFile->get('B', 'key1');
        
        // Read "boolYes" value from "B" section
        $boolYes = $iniFile->get('B', 'boolYes');
        
        // Get ini file contents as array
        $array = $iniFile->toArray();
    }
    catch (\Exception $e)
    {
        printf('Exception! %s'.PHP_EOL, $e->getMessage());
    }

### Writing ini contents:

PHP sample code for writing ini file:

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
    catch (\Exception $e)
    {
        printf('Exception! %s'.PHP_EOL, $e->getMessage());
    }
