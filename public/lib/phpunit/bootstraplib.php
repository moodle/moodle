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
 * Note: these functions must be self contained and must not rely on any other library or include
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../testing/lib.php');

define('PHPUNIT_EXITCODE_PHPUNITMISSING', 129);
define('PHPUNIT_EXITCODE_PHPUNITWRONG', 130);
define('PHPUNIT_EXITCODE_PHPUNITEXTMISSING', 131);
define('PHPUNIT_EXITCODE_CONFIGERROR', 135);
define('PHPUNIT_EXITCODE_CONFIGWARNING', 136);
define('PHPUNIT_EXITCODE_INSTALL', 140);
define('PHPUNIT_EXITCODE_REINSTALL', 141);

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
        case PHPUNIT_EXITCODE_PHPUNITMISSING:
            $text = "Can not find PHPUnit library, to install use: php composer.phar install";
            break;
        case PHPUNIT_EXITCODE_PHPUNITWRONG:
            $text = 'Moodle requires PHPUnit 3.6.x, '.$text.' is not compatible';
            break;
        case PHPUNIT_EXITCODE_PHPUNITEXTMISSING:
            $text = 'Moodle can not find required PHPUnit extension '.$text;
            break;
        case PHPUNIT_EXITCODE_CONFIGERROR:
            $text = "Moodle PHPUnit environment configuration error:\n".$text;
            break;
        case PHPUNIT_EXITCODE_CONFIGWARNING:
            $text = "Moodle PHPUnit environment configuration warning:\n".$text;
            break;
        case PHPUNIT_EXITCODE_INSTALL:
            $path = testing_cli_argument_path('/public/admin/tool/phpunit/cli/init.php');
            $text = "Moodle PHPUnit environment is not initialised, please use:\n php $path";
            break;
        case PHPUNIT_EXITCODE_REINSTALL:
            $path = testing_cli_argument_path('/public/admin/tool/phpunit/cli/init.php');
            $text = "Moodle PHPUnit environment was initialised for different version, please use:\n php $path";
            break;
        default:
            $text = empty($text) ? '' : ': '.$text;
            $text = 'Unknown error '.$errorcode.$text;
            break;
    }

    testing_error($errorcode, $text);
}
