<?php

declare(strict_types=1);

/**
 * This file registers an autoloader for test classes used by SimpleSAMLphp modules unit tests.
 */

/**
 * Autoload function for SimpleSAMLphp modules test classes following PSR-4.
 * Module test classes have namespaces like SimpleSAML\Test\Module\<moduleName>\Auth\Process
 *
 * @param string $className Name of the class.
 * @return void
 */
function sspmodTestClassAutoloadPSR4(string $className): void
{
    $elements = explode('\\', $className);
    if ($elements[0] === '') {
        // class name starting with /, ignore
        array_shift($elements);
    }
    if (count($elements) < 5) {
        return; // it can't be a module test class
    }
    if (array_shift($elements) !== 'SimpleSAML') {
        return; // the first element is not "SimpleSAML"
    }
    if (array_shift($elements) !== 'Test') {
        return; // the second element is not "test"
    }
    if (array_shift($elements) !== 'Module') {
        return; // the third element is not "module"
    }

    // this is a SimpleSAMLphp module test class following PSR-4
    $module = array_shift($elements);
    $moduleTestDir = __DIR__  . '/modules/' . $module;
    $file = $moduleTestDir . '/lib/' . implode('/', $elements) . '.php';

    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register('sspmodTestClassAutoloadPSR4');
