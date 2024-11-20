<?php

declare(strict_types=1);

namespace SimpleSAML\Utils;

use SimpleSAML\Configuration;
use SimpleSAML\Error;

/**
 * System-related utility methods.
 *
 * @package SimpleSAMLphp
 */

class System
{
    public const WINDOWS = 1;
    public const LINUX = 2;
    public const OSX = 3;
    public const HPUX = 4;
    public const UNIX = 5;
    public const BSD = 6;
    public const IRIX = 7;
    public const SUNOS = 8;


    /**
     * This function returns the Operating System we are running on.
     *
     * @return mixed A predefined constant identifying the OS we are running on. False if we are unable to determine it.
     *
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     */
    public static function getOS()
    {
        if (stristr(PHP_OS, 'LINUX')) {
            return self::LINUX;
        }
        if (stristr(PHP_OS, 'DARWIN')) {
            return self::OSX;
        }
        if (stristr(PHP_OS, 'WIN')) {
            return self::WINDOWS;
        }
        if (stristr(PHP_OS, 'BSD')) {
            return self::BSD;
        }
        if (stristr(PHP_OS, 'UNIX')) {
            return self::UNIX;
        }
        if (stristr(PHP_OS, 'HP-UX')) {
            return self::HPUX;
        }
        if (stristr(PHP_OS, 'IRIX')) {
            return self::IRIX;
        }
        if (stristr(PHP_OS, 'SUNOS')) {
            return self::SUNOS;
        }
        return false;
    }


    /**
     * This function retrieves the path to a directory where temporary files can be saved.
     *
     * @return string Path to a temporary directory, without a trailing directory separator.
     * @throws Error\Exception If the temporary directory cannot be created or it exists and cannot be written
     * to by the current user.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     * @author Aaron St. Clair, ECRS AS <astclair@ecrs.com>
     */
    public static function getTempDir()
    {
        $globalConfig = Configuration::getInstance();

        $tempDir = rtrim(
            $globalConfig->getString(
                'tempdir',
                sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'simplesaml'
            ),
            DIRECTORY_SEPARATOR
        );

        /**
         * If the temporary directory does not exist then attempt to create it. If the temporary directory
         * already exists then verify the current user can write to it. Otherwise, throw an error.
         */
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0700, true)) {
                $error = error_get_last();
                throw new Error\Exception(
                    'Error creating temporary directory "' . $tempDir . '": ' .
                    (is_array($error) ? $error['message'] : 'no error available')
                );
            }
        } elseif (!is_writable($tempDir)) {
            throw new Error\Exception(
                'Temporary directory "' . $tempDir .
                '" cannot be written to by the current user' .
                (function_exists('posix_getuid') ? ' "' .  posix_getuid() . '"' : '')
            );
        }

        return $tempDir;
    }


    /**
     * Resolve a (possibly) relative path from the given base path.
     *
     * A path which starts with a stream wrapper pattern (e.g. s3://) will not be touched
     * and returned as is - regardles of the value given as base path.
     * If it starts with a '/' it is assumed to be absolute, all others are assumed to be
     * relative. The default base path is the root of the SimpleSAMLphp installation.
     *
     * @param string      $path The path we should resolve.
     * @param string|null $base The base path, where we should search for $path from. Default value is the root of the
     *     SimpleSAMLphp installation.
     *
     * @return string An absolute path referring to $path.
     *
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     */
    public static function resolvePath($path, $base = null)
    {
        if ($base === null) {
            $config = Configuration::getInstance();
            $base = $config->getBaseDir();
        }

        // normalise directory separator
        $base = str_replace('\\', '/', $base);
        $path = str_replace('\\', '/', $path);

        // remove trailing slashes
        $base = rtrim($base, '/');
        $path = rtrim($path, '/');

        // check for absolute path
        if (substr($path, 0, 1) === '/') {
            // absolute path. */
            $ret = '/';
        } elseif (static::pathContainsDriveLetter($path)) {
            $ret = '';
        } else {
            // path relative to base
            $ret = $base;
        }

        if (static::pathContainsStreamWrapper($path)) {
            $ret = $path;
        } else {
            $path = explode('/', $path);
            foreach ($path as $d) {
                if ($d === '.') {
                    continue;
                } elseif ($d === '..') {
                    $ret = dirname($ret);
                } else {
                    if ($ret && substr($ret, -1) !== '/') {
                        $ret .= '/';
                    }
                    $ret .= $d;
                }
            }
        }

        return $ret;
    }


    /**
     * Atomically write a file.
     *
     * This is a helper function for writing data atomically to a file. It does this by writing the file data to a
     * temporary file, then renaming it to the required file name.
     *
     * @param string $filename The path to the file we want to write to.
     * @param string $data The data we should write to the file.
     * @param int    $mode The permissions to apply to the file. Defaults to 0600.
     *
     * @throws \InvalidArgumentException If any of the input parameters doesn't have the proper types.
     * @throws Error\Exception If the file cannot be saved, permissions cannot be changed or it is not
     *     possible to write to the target file.
     *
     * @author Andreas Solberg, UNINETT AS <andreas.solberg@uninett.no>
     * @author Olav Morken, UNINETT AS <olav.morken@uninett.no>
     * @author Andjelko Horvat
     * @author Jaime Perez, UNINETT AS <jaime.perez@uninett.no>
     *
     * @return void
     */
    public static function writeFile($filename, $data, $mode = 0600)
    {
        if (!is_string($filename) || !is_string($data) || !is_numeric($mode)) {
            throw new \InvalidArgumentException('Invalid input parameters');
        }

        $tmpFile = self::getTempDir() . DIRECTORY_SEPARATOR . rand();

        $res = @file_put_contents($tmpFile, $data);
        if ($res === false) {
            $error = error_get_last();
            throw new Error\Exception(
                'Error saving file "' . $tmpFile . '": ' .
                (is_array($error) ? $error['message'] : 'no error available')
            );
        }

        if (self::getOS() !== self::WINDOWS) {
            if (!chmod($tmpFile, $mode)) {
                unlink($tmpFile);
                $error = error_get_last();
                //$error = (is_array($error) ? $error['message'] : 'no error available');
                throw new Error\Exception(
                    'Error changing file mode of "' . $tmpFile . '": ' .
                    (is_array($error) ? $error['message'] : 'no error available')
                );
            }
        }

        if (!rename($tmpFile, $filename)) {
            unlink($tmpFile);
            $error = error_get_last();
            throw new Error\Exception(
                'Error moving "' . $tmpFile . '" to "' . $filename . '": ' .
                (is_array($error) ? $error['message'] : 'no error available')
            );
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($filename);
        }
    }


    /**
     * Check if the supplied path is an absolute path.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolutePath(string $path): bool
    {
        return (0 === strpos($path, '/') || self::pathContainsDriveLetter($path));
    }


    /**
     * Check if the supplied path contains a Windows-style drive letter.
     *
     * @param string $path
     *
     * @return bool
     */
    private static function pathContainsDriveLetter(string $path): bool
    {
        $letterAsciiValue = ord(strtoupper(substr($path, 0, 1)));
        return substr($path, 1, 1) === ':'
                && $letterAsciiValue >= 65 && $letterAsciiValue <= 90;
    }

    /**
     * Check if the supplied path contains a stream wrapper
     * @param string $path
     * @return bool
     */
    private static function pathContainsStreamWrapper(string $path): bool
    {
        return preg_match('/^[\w\d]*:\/{2}/', $path) === 1;
    }
}
