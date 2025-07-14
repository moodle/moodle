<?php

/*
 * This file is part of Mustache.php.
 *
 * (c) 2010-2017 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Mustache class autoloader.
 */
class Mustache_Autoloader
{
    private $baseDir;

    /**
     * An array where the key is the baseDir and the key is an instance of this
     * class.
     *
     * @var array
     */
    private static $instances;

    /**
     * Autoloader constructor.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     */
    public function __construct($baseDir = null)
    {
        if ($baseDir === null) {
            $baseDir = dirname(__FILE__) . '/..';
        }

        // realpath doesn't always work, for example, with stream URIs
        $realDir = realpath($baseDir);
        if (is_dir($realDir)) {
            $this->baseDir = $realDir;
        } else {
            $this->baseDir = $baseDir;
        }
    }

    /**
     * Register a new instance as an SPL autoloader.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     *
     * @return Mustache_Autoloader Registered Autoloader instance
     */
    public static function register($baseDir = null)
    {
        $key = $baseDir ? $baseDir : 0;

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($baseDir);
        }

        $loader = self::$instances[$key];
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Autoload Mustache classes.
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        if (strpos($class, 'Mustache') !== 0) {
            return;
        }

        $file = sprintf('%s/%s.php', $this->baseDir, str_replace('_', '/', $class));
        if (is_file($file)) {
            require $file;
        }
    }
}
