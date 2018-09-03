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
 * Prepares PHPUnit environment, the phpunit.xml configuration
 * must specify this file as bootstrap.
 *
 * Exit codes: {@see phpunit_bootstrap_error()}
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die; // No access from web!
}

// we want to know about all problems
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// Make sure OPcache does not strip comments, we need them in phpunit!
if (ini_get('opcache.enable') and strtolower(ini_get('opcache.enable')) !== 'off') {
    if (!ini_get('opcache.save_comments') or strtolower(ini_get('opcache.save_comments')) === 'off') {
        ini_set('opcache.enable', 0);
    } else {
        ini_set('opcache.load_comments', 1);
    }
}

if (!defined('IGNORE_COMPONENT_CACHE')) {
    define('IGNORE_COMPONENT_CACHE', true);
}

require_once(__DIR__.'/bootstraplib.php');
require_once(__DIR__.'/../testing/lib.php');
require_once(__DIR__.'/classes/autoloader.php');

if (isset($_SERVER['REMOTE_ADDR'])) {
    phpunit_bootstrap_error(1, 'Unit tests can be executed only from command line!');
}

if (defined('PHPUNIT_TEST')) {
    phpunit_bootstrap_error(1, "PHPUNIT_TEST constant must not be manually defined anywhere!");
}
/** PHPUnit testing framework active */
define('PHPUNIT_TEST', true);

if (!defined('PHPUNIT_UTIL')) {
    /** Identifies utility scripts - the database does not need to be initialised */
    define('PHPUNIT_UTIL', false);
}

if (defined('CLI_SCRIPT')) {
    phpunit_bootstrap_error(1, 'CLI_SCRIPT must not be manually defined in any PHPUnit test scripts');
}
define('CLI_SCRIPT', true);

$phpunitversion = PHPUnit\Runner\Version::id();
if ($phpunitversion === '@package_version@') {
    // library checked out from git, let's hope dev knows that 3.6.0 is required
} else if (version_compare($phpunitversion, '3.6.0', 'lt')) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_PHPUNITWRONG, $phpunitversion);
}
unset($phpunitversion);

define('NO_OUTPUT_BUFFERING', true);

// only load CFG from config.php, stop ASAP in lib/setup.php
define('ABORT_AFTER_CONFIG', true);
require(__DIR__ . '/../../config.php');

if (!defined('PHPUNIT_LONGTEST')) {
    /** Execute longer version of tests */
    define('PHPUNIT_LONGTEST', false);
}

// remove error handling overrides done in config.php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
set_time_limit(0); // no time limit in CLI scripts, user may cancel execution

// prepare dataroot
umask(0);
if (isset($CFG->phpunit_directorypermissions)) {
    $CFG->directorypermissions = $CFG->phpunit_directorypermissions;
} else {
    $CFG->directorypermissions = 02777;
}
$CFG->filepermissions = ($CFG->directorypermissions & 0666);
if (!isset($CFG->phpunit_dataroot)) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Missing $CFG->phpunit_dataroot in config.php, can not run tests!');
}

// Create test dir if does not exists yet.
if (!file_exists($CFG->phpunit_dataroot)) {
    mkdir($CFG->phpunit_dataroot, $CFG->directorypermissions);
}
if (!is_dir($CFG->phpunit_dataroot)) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->phpunit_dataroot directory can not be created, can not run tests!');
}

// Ensure we access to phpunit_dataroot realpath always.
$CFG->phpunit_dataroot = realpath($CFG->phpunit_dataroot);

if (isset($CFG->dataroot) and $CFG->phpunit_dataroot === $CFG->dataroot) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->dataroot and $CFG->phpunit_dataroot must not be identical, can not run tests!');
}

if (!is_writable($CFG->phpunit_dataroot)) {
    // try to fix permissions if possible
    if (function_exists('posix_getuid')) {
        $chmod = fileperms($CFG->phpunit_dataroot);
        if (fileowner($CFG->phpunit_dataroot) == posix_getuid()) {
            $chmod = $chmod | 0700;
            chmod($CFG->phpunit_dataroot, $chmod);
        }
    }
    if (!is_writable($CFG->phpunit_dataroot)) {
        phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->phpunit_dataroot directory is not writable, can not run tests!');
    }
}
if (!file_exists("$CFG->phpunit_dataroot/phpunittestdir.txt")) {
    if ($dh = opendir($CFG->phpunit_dataroot)) {
        while (($file = readdir($dh)) !== false) {
            if ($file === 'phpunit' or $file === '.' or $file === '..' or $file === '.DS_Store') {
                continue;
            }
            phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->phpunit_dataroot directory is not empty, can not run tests! Is it used for anything else?');
        }
        closedir($dh);
        unset($dh);
        unset($file);
    }

    // now we are 100% sure this dir is used only for phpunit tests
    testing_initdataroot($CFG->phpunit_dataroot, 'phpunit');
}

// verify db prefix
if (!isset($CFG->phpunit_prefix)) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, 'Missing $CFG->phpunit_prefix in config.php, can not run tests!');
}
if ($CFG->phpunit_prefix === '') {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->phpunit_prefix can not be empty, can not run tests!');
}
if (isset($CFG->prefix) and $CFG->prefix === $CFG->phpunit_prefix) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGERROR, '$CFG->prefix and $CFG->phpunit_prefix must not be identical, can not run tests!');
}

// override CFG settings if necessary and throw away extra CFG settings
$CFG->wwwroot   = 'https://www.example.com/moodle';
$CFG->dataroot  = $CFG->phpunit_dataroot;
$CFG->prefix    = $CFG->phpunit_prefix;
$CFG->dbtype    = isset($CFG->phpunit_dbtype) ? $CFG->phpunit_dbtype : $CFG->dbtype;
$CFG->dblibrary = isset($CFG->phpunit_dblibrary) ? $CFG->phpunit_dblibrary : $CFG->dblibrary;
$CFG->dbhost    = isset($CFG->phpunit_dbhost) ? $CFG->phpunit_dbhost : $CFG->dbhost;
$CFG->dbname    = isset($CFG->phpunit_dbname) ? $CFG->phpunit_dbname : $CFG->dbname;
$CFG->dbuser    = isset($CFG->phpunit_dbuser) ? $CFG->phpunit_dbuser : $CFG->dbuser;
$CFG->dbpass    = isset($CFG->phpunit_dbpass) ? $CFG->phpunit_dbpass : $CFG->dbpass;
$CFG->prefix    = isset($CFG->phpunit_prefix) ? $CFG->phpunit_prefix : $CFG->prefix;
$CFG->dboptions = isset($CFG->phpunit_dboptions) ? $CFG->phpunit_dboptions : $CFG->dboptions;

$allowed = array('wwwroot', 'dataroot', 'dirroot', 'admin', 'directorypermissions', 'filepermissions',
                 'dbtype', 'dblibrary', 'dbhost', 'dbname', 'dbuser', 'dbpass', 'prefix', 'dboptions',
                 'proxyhost', 'proxyport', 'proxytype', 'proxyuser', 'proxypassword', 'proxybypass', // keep proxy settings from config.php
                 'altcacheconfigpath', 'pathtogs', 'pathtodu', 'aspellpath', 'pathtodot',
                 'pathtounoconv', 'alternative_file_system_class', 'pathtopython'
                );
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
$CFG->debug = (E_ALL | E_STRICT); // can not use DEBUG_DEVELOPER yet
$CFG->debugdeveloper = true;
$CFG->debugdisplay = 1;
error_reporting($CFG->debug);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

// some ugly hacks
$CFG->themerev = 1;
$CFG->jsrev = 1;

// load test case stub classes and other stuff
require_once("$CFG->dirroot/lib/phpunit/lib.php");

// finish moodle init
define('ABORT_AFTER_CONFIG_CANCEL', true);
if (isset($CFG->phpunit_profilingenabled) && $CFG->phpunit_profilingenabled) {
    $CFG->profilingenabled = true;
    $CFG->profilingincluded = '*';
}
require("$CFG->dirroot/lib/setup.php");

raise_memory_limit(MEMORY_HUGE);

if (PHPUNIT_UTIL) {
    // we are not going to do testing, this is 'true' in utility scripts that only init database
    return;
}

// is database and dataroot ready for testing?
list($errorcode, $message) = phpunit_util::testing_ready_problem();
// print some version info
phpunit_util::bootstrap_moodle_info();
if ($errorcode) {
    phpunit_bootstrap_error($errorcode, $message);
}

// prepare for the first test run - store fresh globals, reset database and dataroot, etc.
phpunit_util::bootstrap_init();
