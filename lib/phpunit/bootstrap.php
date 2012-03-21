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
 * Prepares PHPUnit environment, it is called automatically.
 *
 * Exit codes:
 *  0   - success
 *  1   - general error
 *  130 - coding error
 *  131 - configuration problem
 *  132 - drop data, then install new test database
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// we want to know about all problems
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

if (isset($_SERVER['REMOTE_ADDR'])) {
    phpunit_bootstrap_error('Unit tests can be executed only from commandline!', 1);
}

if (defined('PHPUNITTEST')) {
    phpunit_bootstrap_error("PHPUNITTEST constant must not be manually defined anywhere!", 130);
}
define('PHPUNITTEST', true);

if (defined('CLI_SCRIPT')) {
    phpunit_bootstrap_error('CLI_SCRIPT must not be manually defined in any PHPUnit test scripts', 130);
}
define('CLI_SCRIPT', true);

define('NO_OUTPUT_BUFFERING', true);

// only load CFG from config.php
define('ABORT_AFTER_CONFIG', true);
require(__DIR__ . '/../../config.php');

// remove error handling overrides done in config.php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// prepare dataroot
umask(0);
if (isset($CFG->phpunit_directorypermissions)) {
    $CFG->directorypermissions = $CFG->phpunit_directorypermissions;
} else {
    $CFG->directorypermissions = 02777;
}
$CFG->filepermissions = ($CFG->directorypermissions & 0666);
if (!isset($CFG->phpunit_dataroot)) {
    phpunit_bootstrap_error('Missing $CFG->phpunit_dataroot in config.php, can not run tests!', 131);
}
if (isset($CFG->dataroot) and $CFG->phpunit_dataroot === $CFG->dataroot) {
    phpunit_bootstrap_error('$CFG->dataroot and $CFG->phpunit_dataroot must not be identical, can not run tests!', 131);
}
if (!file_exists($CFG->phpunit_dataroot)) {
    mkdir($CFG->phpunit_dataroot, $CFG->directorypermissions);
}
if (!is_dir($CFG->phpunit_dataroot)) {
    phpunit_bootstrap_error('$CFG->phpunit_dataroot directory can not be created, can not run tests!', 131);
}
if (!is_writable($CFG->phpunit_dataroot)) {
    // try to fix premissions if possible
    if (function_exists('posix_getuid')) {
        $chmod = fileperms($CFG->phpunit_dataroot);
        if (fileowner($dir) == posix_getuid()) {
            $chmod = $chmod | 0700;
            chmod($CFG->phpunit_dataroot, $chmod);
        }
    }
    if (!is_writable($CFG->phpunit_dataroot)) {
        phpunit_bootstrap_error('$CFG->phpunit_dataroot directory is not writable, can not run tests!', 131);
    }
}
if (!file_exists("$CFG->phpunit_dataroot/phpunittestdir.txt")) {
    if ($dh = opendir($CFG->phpunit_dataroot)) {
        while (($file = readdir($dh)) !== false) {
            if ($file === 'phpunit' or $file === '.' or $file === '..' or $file === '.DS_store') {
                continue;
            }
            phpunit_bootstrap_error('$CFG->phpunit_dataroot directory is not empty, can not run tests! Is it used for anything else?', 131);
        }
        closedir($dh);
        unset($dh);
        unset($file);
    }

    // now we are 100% sure this dir is used only for phpunit tests
    phpunit_bootstrap_initdataroot($CFG->phpunit_dataroot);
}


// verify db prefix
if (!isset($CFG->phpunit_prefix)) {
    phpunit_bootstrap_error('Missing $CFG->phpunit_dataroot in config.php, can not run tests!', 131);
}
if (isset($CFG->prefix) and $CFG->prefix === $CFG->phpunit_prefix) {
    phpunit_bootstrap_error('$CFG->prefix and $CFG->phpunit_prefix must not be identical, can not run tests!', 131);
}

// throw away standard CFG settings

$CFG->dataroot = $CFG->phpunit_dataroot;
$CFG->prefix = $CFG->phpunit_prefix;

$allowed = array('wwwroot', 'dataroot', 'dirroot', 'admin', 'directorypermissions', 'filepermissions',
                 'dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'prefix', 'dboptions');
$productioncfg = (array)$CFG;
$CFG = new stdClass();
foreach ($productioncfg as $key=>$value) {
    if (!in_array($key, $allowed) and strpos($key, 'phpunit_') !== 0) {
        // ignore
        continue;
    }
    $CFG->{$key} = $value;
}
unset($key);
unset($value);
unset($allowed);
unset($productioncfg);

// force the same CFG settings in all sites
$CFG->debug = (E_ALL | E_STRICT | 38911); // can not use DEBUG_DEVELOPER here
$CFG->debugdisplay = 1;
error_reporting($CFG->debug);
ini_set('display_errors', '1');
ini_set('log_errors', '0');

$CFG->noemailever = true; // better not mail anybody from tests, override temporarily if necessary
$CFG->cachetext = 0; // disable this very nasty setting

// some ugly hacks
$CFG->themerev = 1;
$CFG->jsrev = 1;

// load test case stub classes and other stuff
require_once("$CFG->dirroot/lib/phpunit/lib.php");

// finish moodle init
define('ABORT_AFTER_CONFIG_CANCEL', true);
require("$CFG->dirroot/lib/setup.php");

raise_memory_limit(MEMORY_EXTRA);

if (defined('PHPUNIT_CLI_UTIL')) {
    // all other tests are done in the CLI scripts...
    return;
}

if (!phpunit_util::is_testing_ready()) {
    phpunit_bootstrap_error('Database is not initialised to run unit tests, please use "php admin/tool/phpunit/cli/util.php --install"', 132);
}

// refresh data in all tables, clear caches, etc.
phpunit_util::reset_all_data();

// store fresh globals
phpunit_util::init_globals();


//=========================================================

/**
 * Print error and stop execution
 * @param string $text An error message to display
 * @param int $errorcode The error code (see docblock for detailed list)
 * @return void stops code execution with error code
 */
function phpunit_bootstrap_error($text, $errorcode = 1) {
    fwrite(STDERR, $text."\n");
    exit($errorcode);
}

/**
 * Mark empty dataroot to be used for testing.
 * @param string $dataroot The dataroot directory
 * @return void
 */
function phpunit_bootstrap_initdataroot($dataroot) {
    global $CFG;

    file_put_contents("$dataroot/phpunittestdir.txt", 'Contents of this directory are used during tests only, do not delete this file!');
    chmod("$dataroot/phpunittestdir.txt", $CFG->filepermissions);
    if (!file_exists("$CFG->phpunit_dataroot/phpunit")) {
        mkdir("$CFG->phpunit_dataroot/phpunit", $CFG->directorypermissions);
    }
}
