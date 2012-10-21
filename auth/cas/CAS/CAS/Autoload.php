<?php

/**
 * Autoloader Class
 *
 *  PHP Version 5
 *
 * @file      CAS/Autoload.php
 * @category  Authentication
 * @package   SimpleCAS
 * @author    Brett Bieber <brett.bieber@gmail.com>
 * @copyright 2008 Regents of the University of Nebraska
 * @license   http://www1.unl.edu/wdn/wiki/Software_License BSD License
 * @link      http://code.google.com/p/simplecas/
 **/

/**
 * Autoload a class
 *
 * @param string $class Classname to load
 *
 * @return bool
 */
function CAS_autoload($class)
{
    // Static to hold the Include Path to CAS
    static $include_path;
    // Setup the include path if it's not already set from a previous call
    if (!$include_path) {
        $include_path = dirname(dirname(__FILE__));
    }
    if (substr($class, 0, 4) !== 'CAS_') {
        return false;
    }
    // Declare local variable to store the expected full path to the file
    $file_path = $include_path . '/' . str_replace('_', '/', $class) . '.php';

    $fp = @fopen($file_path, 'r', true);
    if ($fp) {
        fclose($fp);
        include $file_path;
        if (!class_exists($class, false) && !interface_exists($class, false)) {
            die(
                new Exception(
                    'Class ' . $class . ' was not present in ' .
                    $file_path .
                    ' [CAS_autoload]'
                )
            );
        }
        return true;
    }
    $e = new Exception(
        'Class ' . $class . ' could not be loaded from ' .
        $file_path . ', file does not exist (Path="'
        . $include_path .'") [CAS_autoload]'
    );
    $trace = $e->getTrace();
    if (isset($trace[2]) && isset($trace[2]['function'])
        && in_array($trace[2]['function'], array('class_exists', 'interface_exists'))
    ) {
        return false;
    }
    if (isset($trace[1]) && isset($trace[1]['function'])
        && in_array($trace[1]['function'], array('class_exists', 'interface_exists'))
    ) {
        return false;
    }
    die ((string) $e);
}

// set up __autoload
if (function_exists('spl_autoload_register')) {
    if (!(spl_autoload_functions()) || !in_array('CAS_autoload', spl_autoload_functions())) {
        spl_autoload_register('CAS_autoload');
        if (function_exists('__autoload') && !in_array('__autoload', spl_autoload_functions())) {
            // __autoload() was being used, but now would be ignored, add
            // it to the autoload stack
            spl_autoload_register('__autoload');
        }
    }
} elseif (!function_exists('__autoload')) {

    /**
     * Autoload a class
     *
     * @param string $class Class name
     *
     * @return bool
     */
    function __autoload($class)
    {
        return CAS_autoload($class);
    }
}

?>