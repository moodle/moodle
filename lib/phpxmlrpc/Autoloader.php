<?php

namespace PhpXmlRpc;

/**
 * In the unlikely event that you are not using Composer to manage class autoloading, here's an autoloader for this lib.
 * For usage, see any file in the demo/client directory
 */
class Autoloader
{
    /**
     * Registers PhpXmlRpc\Autoloader as an SPL autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not.
     */
    public static function register($prepend = false)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'), true, $prepend);
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, 'PhpXmlRpc\\')) {
            return;
        }

        if (is_file($file = __DIR__ . str_replace(array('PhpXmlRpc\\', '\\'), '/', $class).'.php')) {
            require $file;
        }
    }
}
