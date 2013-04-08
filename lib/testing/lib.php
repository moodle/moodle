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

    $cwd = getcwd();
    if (substr($cwd, -1) !== DIRECTORY_SEPARATOR) {
        $cwd .= DIRECTORY_SEPARATOR;
    }
    $path = realpath($CFG->dirroot.$moodlepath);

    if (strpos($path, $cwd) === 0) {
        $path = substr($path, strlen($cwd));
    }

    if (testing_is_cygwin()) {
        $path = str_replace('\\', '/', $path);
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
    exit($errorcode);
}

/**
 * Updates the composer installer and the dependencies.
 *
 * Includes --dev dependencies.
 *
 * @return void exit() if something goes wrong
 */
function testing_update_composer_dependencies() {

    // To restore the value after finishing.
    $cwd = getcwd();

    // Dirroot.
    chdir(__DIR__ . '/../..');

    // Download composer.phar if we can.
    if (!file_exists(__DIR__ . '/../../composer.phar')) {
        passthru("curl http://getcomposer.org/installer | php", $code);
        if ($code != 0) {
            exit($code);
        }
    } else {

        // If it is already there update the installer.
        passthru("php composer.phar self-update", $code);
        if ($code != 0) {
            exit($code);
        }
    }

    // Update composer dependencies.
    passthru("php composer.phar update --dev", $code);
    if ($code != 0) {
        exit($code);
    }

    chdir($cwd);
}
