<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Testing general functions
 *
 * Note: these functions must be self contained and must not rely on any library or include
 *
 * @package    core
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Composer error exit status.
 *
 * @var int
 */
define('TESTING_EXITCODE_COMPOSER', 255);

/**
 * Returns relative path against current working directory,
 * to be used for shell execution hints.
 * @param string $moodlepath starting with "/", ex: "/admin/tool/cli/init.php"
 * @return string path relative to current directory or absolute path
 */
function testing_cli_argument_path($moodlepath) {
    global $CFG;

    if (isset($CFG->admin) and $CFG->admin !== 'admin') {
        $moodlepath = preg_replace('|^/admin/|', "/$CFG->admin/", $moodlepath);
    }

    if (isset($_SERVER['REMOTE_ADDR'])) {
        // Web access, this should not happen often.
        $cwd = dirname(dirname(__DIR__));
    } else {
        // This is the real CLI script, work with relative paths.
        $cwd = getcwd();
    }

    // Remove last directory separator as $path will not contain one.
    if ((substr($cwd, -1) === '/') || (substr($cwd, -1) === '\\')) {
        $cwd = substr($cwd, -1);
    }

    $path = realpath($CFG->dirroot.$moodlepath);

    // We need standrad directory seperator for path and cwd, so it can be compared.
    $cwd = testing_cli_fix_directory_separator($cwd);
    $path = testing_cli_fix_directory_separator($path);

    if (strpos($path, $cwd) === 0) {
        // Remove current working directory and directory separator.
        $path = substr($path, strlen($cwd) + 1);
    }

    return $path;
}

/**
 * Try to change permissions to $CFG->dirroot or $CFG->dataroot if possible
 * @param string $file
 * @return bool success
 */
function testing_fix_file_permissions($file) {
    global $CFG;

    $permissions = fileperms($file);
    if ($permissions & $CFG->filepermissions != $CFG->filepermissions) {
        $permissions = $permissions | $CFG->filepermissions;
        return chmod($file, $permissions);
    }

    return true;
}

/**
 * Find out if running under Cygwin on Windows.
 * @return bool
 */
function testing_is_cygwin() {
    if (empty($_SERVER['OS']) or $_SERVER['OS'] !== 'Windows_NT') {
        return false;

    } else if (!empty($_SERVER['SHELL']) and $_SERVER['SHELL'] === '/bin/bash') {
        return true;

    } else if (!empty($_SERVER['TERM']) and $_SERVER['TERM'] === 'cygwin') {
        return true;

    } else {
        return false;
    }
}

/**
 * Returns whether a mingw CLI is running.
 *
 * MinGW sets $_SERVER['TERM'] to cygwin, but it
 * can not run .bat files; this function may be useful
 * when we need to output proposed commands to users
 * using Windows CLI interfaces.
 *
 * @link http://sourceforge.net/p/mingw/bugs/1902
 * @return bool
 */
function testing_is_mingw() {

    if (!testing_is_cygwin()) {
        return false;
    }

    if (!empty($_SERVER['MSYSTEM'])) {
        return true;
    }

    return false;
}

/**
 * Mark empty dataroot to be used for testing.
 * @param string $dataroot  The dataroot directory
 * @param string $framework The test framework
 * @return void
 */
function testing_initdataroot($dataroot, $framework) {
    global $CFG;

    $filename = $dataroot . '/' . $framework . 'testdir.txt';

    umask(0);
    if (!file_exists($filename)) {
        file_put_contents($filename, 'Contents of this directory are used during tests only, do not delete this file!');
    }
    testing_fix_file_permissions($filename);

    $varname = $framework . '_dataroot';
    $datarootdir = $CFG->{$varname} . '/' . $framework;
    if (!file_exists($datarootdir)) {
        mkdir($datarootdir, $CFG->directorypermissions);
    }
}

/**
 * Prints an error and stops execution
 *
 * @param integer $errorcode
 * @param string $text
 * @return void exits
 */
function testing_error($errorcode, $text = '') {

    // do not write to error stream because we need the error message in PHP exec result from web ui
    echo($text."\n");
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header('HTTP/1.1 500 Internal Server Error');
    }
    exit($errorcode);
}

/**
 * Updates the composer installer and the dependencies.
 *
 * @return void exit() if something goes wrong
 */
function testing_update_composer_dependencies() {
    // To restore the value after finishing.
    $cwd = getcwd();

    // Set some paths.
    $dirroot = dirname(dirname(__DIR__));
    $composerpath = $dirroot . DIRECTORY_SEPARATOR . 'composer.phar';
    $composerurl = 'https://getcomposer.org/composer.phar';

    // Switch to Moodle's dirroot for easier path handling.
    chdir($dirroot);

    // Download or update composer.phar. Unfortunately we can't use the curl
    // class in filelib.php as we're running within one of the test platforms.
    if (!file_exists($composerpath)) {
        $file = @fopen($composerpath, 'w');
        if ($file === false) {
            $errordetails = error_get_last();
            $error = sprintf("Unable to create composer.phar\nPHP error: %s",
                             $errordetails['message']);
            testing_error(TESTING_EXITCODE_COMPOSER, $error);
        }
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL,  $composerurl);
        curl_setopt($curl, CURLOPT_FILE, $file);
        $result = curl_exec($curl);

        $curlerrno = curl_errno($curl);
        $curlerror = curl_error($curl);
        $curlinfo = curl_getinfo($curl);

        curl_close($curl);
        fclose($file);

        if (!$result) {
            $error = sprintf("Unable to download composer.phar\ncURL error (%d): %s",
                             $curlerrno, $curlerror);
            testing_error(TESTING_EXITCODE_COMPOSER, $error);
        } else if ($curlinfo['http_code'] === 404) {
            if (file_exists($composerpath)) {
                // Deleting the resource as it would contain HTML.
                unlink($composerpath);
            }
            $error = sprintf("Unable to download composer.phar\n" .
                                "404 http status code fetching $composerurl");
            testing_error(TESTING_EXITCODE_COMPOSER, $error);
        }
    } else {
        passthru("php composer.phar self-update", $code);
        if ($code != 0) {
            exit($code);
        }
    }

    // Update composer dependencies.
    passthru("php composer.phar install", $code);
    if ($code != 0) {
        exit($code);
    }

    // Return to our original location.
    chdir($cwd);
}

/**
 * Fix DIRECTORY_SEPARATOR for windows.
 *
 * In PHP on Windows, DIRECTORY_SEPARATOR is set to the backslash (\)
 * character. However, if you're running a Cygwin/Msys/Git shell
 * exec() calls will return paths using the forward slash (/) character.
 *
 * NOTE: Because PHP on Windows will accept either forward or backslashes,
 * paths should be built using ONLY forward slashes, regardless of
 * OS. MOODLE_DIRECTORY_SEPARATOR should only be used when parsing
 * paths returned by the shell.
 *
 * @param string $path
 * @return string.
 */
function testing_cli_fix_directory_separator($path) {
    global $CFG;

    static $dirseparator = null;

    if (!$dirseparator) {
        // Default directory separator.
        $dirseparator = DIRECTORY_SEPARATOR;

        // On windows we need to find what directory separator is used.
        if ($CFG->ostype = 'WINDOWS') {
            if (!empty($_SERVER['argv'][0])) {
                if (false === strstr($_SERVER['argv'][0], '\\')) {
                    $dirseparator = '/';
                } else {
                    $dirseparator = '\\';
                }
            } else if (testing_is_cygwin()) {
                $dirseparator = '/';
            }
        }
    }

    // Normalize \ and / to directory separator.
    $path = str_replace('\\', $dirseparator, $path);
    $path = str_replace('/', $dirseparator, $path);

    return $path;
}
