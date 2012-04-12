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
 * PHPUnit bootstrap function
 *
 * Note: these functions must be self contained and must not rely on any library or include
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Print error and stop execution
 * @param int $errorcode The exit error code
 * @param string $text An error message to display
 * @return void stops code execution with error code
 */
function phpunit_bootstrap_error($errorcode, $text = '') {
    switch ($errorcode) {
        case 0:
            // this is not an error, just print information and exit
            break;
        case 1:
            $text = 'Error: '.$text;
            break;
        case 129:
            $text = 'Moodle requires PHPUnit 3.6.x, '.$text.' is not compatible';
            break;
        case 130:
            $text = 'Moodle can not find PHPUnit PEAR library or necessary PHPUnit extension';
            break;
        case 131:
            $text = 'Moodle configuration problem: '.$text;
            break;
        case 132:
            $text = "Moodle PHPUnit environment is not initialised, please use:\n php admin/tool/phpunit/cli/util.php --install";
            break;
        case 133:
            $text = "Moodle PHPUnit environment was initialised for different version, please use:\n php admin/tool/phpunit/cli/util.php --drop\n php admin/tool/phpunit/cli/util.php --install";
            break;
        case 134:
            $text = 'Moodle can not create PHPUnit configuration file, please verify dirroot permissions';
            break;
        default:
            $text = empty($text) ? '' : ': '.$text;
            $text = 'Unknown error '.$errorcode.$text;
            break;
    }
    if (defined('PHPUNIT_UTIL') and PHPUNIT_UTIL) {
        // do not write to error stream because we need the error message in PHP exec result from web ui
        echo($text."\n");
    } else {
        fwrite(STDERR, $text."\n");
    }
    exit($errorcode);
}

/**
 * Mark empty dataroot to be used for testing.
 * @param string $dataroot The dataroot directory
 * @return void
 */
function phpunit_bootstrap_initdataroot($dataroot) {
    global $CFG;
    umask(0);
    if (!file_exists("$dataroot/phpunittestdir.txt")) {
        file_put_contents("$dataroot/phpunittestdir.txt", 'Contents of this directory are used during tests only, do not delete this file!');
    }
    phpunit_boostrap_fix_file_permissions("$dataroot/phpunittestdir.txt");
    if (!file_exists("$CFG->phpunit_dataroot/phpunit")) {
        mkdir("$CFG->phpunit_dataroot/phpunit", $CFG->directorypermissions);
    }
}

/**
 * Try to change permissions to $CFG->dirroot or $CFG->dataroot if possible
 * @param string $file
 * @return bool success
 */
function phpunit_boostrap_fix_file_permissions($file) {
    global $CFG;

    $permissions = fileperms($file);
    if ($permissions & $CFG->filepermissions != $CFG->filepermissions) {
        $permissions = $permissions | $CFG->filepermissions;
        return chmod($file, $permissions);
    }

    return true;
}
