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
 * setup.php - Sets up sessions, connects to databases and so on
 *
 * Normally this is only called by the main config.php file
 * Normally this file does not need to be edited.
 *
 * @package    core
 * @subpackage lib
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Holds the core settings that affect how Moodle works. Some of its fields
 * are set in config.php, and the rest are loaded from the config table.
 *
 * Some typical settings in the $CFG global:
 *  - $CFG->wwwroot  - Path to moodle index directory in url format.
 *  - $CFG->dataroot - Path to moodle data files directory on server's filesystem.
 *  - $CFG->dirroot  - Path to moodle's library folder on server's filesystem.
 *  - $CFG->libdir   - Path to moodle's library folder on server's filesystem.
 *  - $CFG->tempdir  - Path to moodle's temp file directory on server's filesystem.
 *  - $CFG->cachedir - Path to moodle's cache directory on server's filesystem.
 *
 * @global object $CFG
 * @name $CFG
 */
global $CFG; // this should be done much earlier in config.php before creating new $CFG instance

if (!isset($CFG)) {
    if (defined('PHPUNIT_TEST') and PHPUNIT_TEST) {
        echo('There is a missing "global $CFG;" at the beginning of the config.php file.'."\n");
        exit(1);
    } else {
        // this should never happen, maybe somebody is accessing this file directly...
        exit(1);
    }
}

// We can detect real dirroot path reliably since PHP 4.0.2,
// it can not be anything else, there is no point in having this in config.php
$CFG->dirroot = dirname(dirname(__FILE__));

// Normalise dataroot - we do not want any symbolic links, trailing / or any other weirdness there
if (!isset($CFG->dataroot)) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    }
    echo('Fatal error: $CFG->dataroot is not specified in config.php! Exiting.'."\n");
    exit(1);
}
$CFG->dataroot = realpath($CFG->dataroot);
if ($CFG->dataroot === false) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    }
    echo('Fatal error: $CFG->dataroot is not configured properly, directory does not exist or is not accessible! Exiting.'."\n");
    exit(1);
} else if (!is_writable($CFG->dataroot)) {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    }
    echo('Fatal error: $CFG->dataroot is not writable, admin has to fix directory permissions! Exiting.'."\n");
    exit(1);
}

// wwwroot is mandatory
if (!isset($CFG->wwwroot) or $CFG->wwwroot === 'http://example.com/moodle') {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    }
    echo('Fatal error: $CFG->wwwroot is not configured! Exiting.'."\n");
    exit(1);
}

// Test environment is requested if:
// * If $CFG->behat_switchcompletely has been set (maintains CLI scripts behaviour, which ATM is only preventive).
// * If we are accessing though the built-in web server (cli-server).
// * Behat is running (constant set hooking the behat init process before requiring config.php).
// * If $CFG->behat_wwwroot has been set and the hostname/port match what the page was requested with.
// Test environment is enabled if:
// * User has previously enabled through admin/tool/behat/cli/util.php --enable or admin/tool/behat/cli/init.php
// Both are required to switch to test mode
if (!defined('BEHAT_SITE_RUNNING') && !empty($CFG->behat_dataroot) &&
        !empty($CFG->behat_prefix) && file_exists($CFG->behat_dataroot)) {

    // Only included if behat_* are set, it is not likely to be a production site.
    require_once(__DIR__ . '/../lib/behat/lib.php');

    $defaultbehatwwwroot = behat_get_wwwroot();

    if (!empty($CFG->behat_switchcompletely) && php_sapi_name() !== 'cli') {
        // Switch completely uses the production wwwroot as the test site URL.
        $behatwwwroot = $defaultbehatwwwroot;

    } elseif (php_sapi_name() === 'cli-server') {
        // If we are using the built-in server we use the provided $CFG->behat_wwwroot
        // value or the default one if $CFG->behat_wwwroot is not set, only if it matches
        // the requested URL.
        if (behat_is_requested_url($defaultbehatwwwroot)) {
            $behatwwwroot = $defaultbehatwwwroot;
        }

    } elseif (defined('BEHAT_TEST')) {
        // This is when moodle codebase runs through vendor/bin/behat, we "are not supposed"
        // to need a wwwroot, but before using the production one we should set something else
        // as an alternative.
        $behatwwwroot = $defaultbehatwwwroot;

    } elseif (!empty($CFG->behat_wwwroot) && !empty($_SERVER['HTTP_HOST'])) {
        // If $CFG->behat_wwwroot was set and matches the requested URL we
        // use $CFG->behat_wwwroot as our wwwroot.
        if (behat_is_requested_url($CFG->behat_wwwroot)) {
            $behatwwwroot = $CFG->behat_wwwroot;
        }
    }

    // If we found a proper behatwwwroot then we consider the behat test as requested.
    $testenvironmentrequested = !empty($behatwwwroot);

    // Only switch to test environment if it has been enabled.
    $CFG->behat_dataroot = realpath($CFG->behat_dataroot);
    $testenvironmentenabled = file_exists($CFG->behat_dataroot . '/behat/test_environment_enabled.txt');

    if ($testenvironmentenabled && $testenvironmentrequested) {

        // Now we know which one will be our behat wwwroot.
        $CFG->behat_wwwroot = $behatwwwroot;

        // Checking the integrity of the provided $CFG->behat_* vars and the
        // selected wwwroot to prevent conflicts with production and phpunit environments.
        behat_check_config_vars();

        // Constant used to inform that the behat test site is being used,
        // this includes all the processes executed by the behat CLI command like
        // the site reset, the steps executed by the browser drivers when simulating
        // a user session and a real session when browsing manually to $CFG->behat_wwwroot
        // like the browser driver does automatically.
        // Different from BEHAT_TEST as only this last one can perform CLI
        // actions like reset the site or use data generators.
        define('BEHAT_SITE_RUNNING', true);

        // Clean extra config.php settings.
        behat_clean_init_config();

        // Now we can begin switching $CFG->X for $CFG->behat_X.
        $CFG->wwwroot = $CFG->behat_wwwroot;
        $CFG->passwordsaltmain = 'moodle';
        $CFG->prefix = $CFG->behat_prefix;
        $CFG->dataroot = $CFG->behat_dataroot;
    }
}

// Define admin directory
if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
    $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
}

// Set up some paths.
$CFG->libdir = $CFG->dirroot .'/lib';

// Allow overriding of tempdir but be backwards compatible
if (!isset($CFG->tempdir)) {
    $CFG->tempdir = "$CFG->dataroot/temp";
}

// Allow overriding of cachedir but be backwards compatible
if (!isset($CFG->cachedir)) {
    $CFG->cachedir = "$CFG->dataroot/cache";
}

// The current directory in PHP version 4.3.0 and above isn't necessarily the
// directory of the script when run from the command line. The require_once()
// would fail, so we'll have to chdir()
if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
    // do it only once - skip the second time when continuing after prevous abort
    if (!defined('ABORT_AFTER_CONFIG') and !defined('ABORT_AFTER_CONFIG_CANCEL')) {
        chdir(dirname($_SERVER['argv'][0]));
    }
}

// sometimes default PHP settings are borked on shared hosting servers, I wonder why they have to do that??
ini_set('precision', 14); // needed for upgrades and gradebook

// Scripts may request no debug and error messages in output
// please note it must be defined before including the config.php script
// and in some cases you also need to set custom default exception handler
if (!defined('NO_DEBUG_DISPLAY')) {
    define('NO_DEBUG_DISPLAY', false);
}

// Some scripts such as upgrade may want to prevent output buffering
if (!defined('NO_OUTPUT_BUFFERING')) {
    define('NO_OUTPUT_BUFFERING', false);
}

// PHPUnit tests need custom init
if (!defined('PHPUNIT_TEST')) {
    define('PHPUNIT_TEST', false);
}

// Performance tests needs to always display performance info, even in redirections.
if (!defined('MDL_PERF_TEST')) {
    define('MDL_PERF_TEST', false);
} else {
    // We force the ones we need.
    if (!defined('MDL_PERF')) {
        define('MDL_PERF', true);
    }
    if (!defined('MDL_PERFDB')) {
        define('MDL_PERFDB', true);
    }
    if (!defined('MDL_PERFTOFOOT')) {
        define('MDL_PERFTOFOOT', true);
    }
}

// When set to true MUC (Moodle caching) will be disabled as much as possible.
// A special cache factory will be used to handle this situation and will use special "disabled" equivalents objects.
// This ensure we don't attempt to read or create the config file, don't use stores, don't provide persistence or
// storage of any kind.
if (!defined('CACHE_DISABLE_ALL')) {
    define('CACHE_DISABLE_ALL', false);
}

// When set to true MUC (Moodle caching) will not use any of the defined or default stores.
// The Cache API will continue to function however this will force the use of the cachestore_dummy so all requests
// will be interacting with a static property and will never go to the proper cache stores.
// Useful if you need to avoid the stores for one reason or another.
if (!defined('CACHE_DISABLE_STORES')) {
    define('CACHE_DISABLE_STORES', false);
}

// Servers should define a default timezone in php.ini, but if they don't then make sure something is defined.
// This is a quick hack.  Ideally we should ask the admin for a value.  See MDL-22625 for more on this.
if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
    $olddebug = error_reporting(0);
    date_default_timezone_set(date_default_timezone_get());
    error_reporting($olddebug);
    unset($olddebug);
}

// Detect CLI scripts - CLI scripts are executed from command line, do not have session and we do not want HTML in output
// In your new CLI scripts just add "define('CLI_SCRIPT', true);" before requiring config.php.
// Please note that one script can not be accessed from both CLI and web interface.
if (!defined('CLI_SCRIPT')) {
    define('CLI_SCRIPT', false);
}
if (defined('WEB_CRON_EMULATED_CLI')) {
    if (!isset($_SERVER['REMOTE_ADDR'])) {
        echo('Web cron can not be executed as CLI script any more, please use admin/cli/cron.php instead'."\n");
        exit(1);
    }
} else if (isset($_SERVER['REMOTE_ADDR'])) {
    if (CLI_SCRIPT) {
        echo('Command line scripts can not be executed from the web interface');
        exit(1);
    }
} else {
    if (!CLI_SCRIPT) {
        echo('Command line scripts must define CLI_SCRIPT before requiring config.php'."\n");
        exit(1);
    }
}

// Detect CLI maintenance mode - this is useful when you need to mess with database, such as during upgrades
if (file_exists("$CFG->dataroot/climaintenance.html")) {
    if (!CLI_SCRIPT) {
        header('Content-type: text/html; charset=utf-8');
        header('X-UA-Compatible: IE=edge');
        /// Headers to make it not cacheable and json
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Accept-Ranges: none');
        readfile("$CFG->dataroot/climaintenance.html");
        die;
    } else {
        if (!defined('CLI_MAINTENANCE')) {
            define('CLI_MAINTENANCE', true);
        }
    }
} else {
    if (!defined('CLI_MAINTENANCE')) {
        define('CLI_MAINTENANCE', false);
    }
}

if (CLI_SCRIPT) {
    // sometimes people use different PHP binary for web and CLI, make 100% sure they have the supported PHP version
    if (version_compare(phpversion(), '5.3.3') < 0) {
        $phpversion = phpversion();
        // do NOT localise - lang strings would not work here and we CAN NOT move it to later place
        echo "Moodle 2.5 or later requires at least PHP 5.3.3 (currently using version $phpversion).\n";
        echo "Some servers may have multiple PHP versions installed, are you using the correct executable?\n";
        exit(1);
    }
}

// Detect ajax scripts - they are similar to CLI because we can not redirect, output html, etc.
if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', false);
}

// File permissions on created directories in the $CFG->dataroot
if (empty($CFG->directorypermissions)) {
    $CFG->directorypermissions = 02777;      // Must be octal (that's why it's here)
}
if (empty($CFG->filepermissions)) {
    $CFG->filepermissions = ($CFG->directorypermissions & 0666); // strip execute flags
}
// better also set default umask because recursive mkdir() does not apply permissions recursively otherwise
umask(0000);

// exact version of currently used yui2 and 3 library
$CFG->yui2version = '2.9.0';
$CFG->yui3version = '3.9.1';


// special support for highly optimised scripts that do not need libraries and DB connection
if (defined('ABORT_AFTER_CONFIG')) {
    if (!defined('ABORT_AFTER_CONFIG_CANCEL')) {
        // hide debugging if not enabled in config.php - we do not want to disclose sensitive info
        if (isset($CFG->debug)) {
            error_reporting($CFG->debug);
        } else {
            error_reporting(0);
        }
        if (NO_DEBUG_DISPLAY) {
            // Some parts of Moodle cannot display errors and debug at all.
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else if (empty($CFG->debugdisplay)) {
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        } else {
            ini_set('display_errors', '1');
        }
        require_once("$CFG->dirroot/lib/configonlylib.php");
        return;
    }
}

/** Used by library scripts to check they are being called by Moodle */
if (!defined('MOODLE_INTERNAL')) { // necessary because cli installer has to define it earlier
    define('MOODLE_INTERNAL', true);
}

// Early profiling start, based exclusively on config.php $CFG settings
if (!empty($CFG->earlyprofilingenabled)) {
    require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');
    if (profiling_start()) {
        register_shutdown_function('profiling_stop');
    }
}

/**
 * Database connection. Used for all access to the database.
 * @global moodle_database $DB
 * @name $DB
 */
global $DB;

/**
 * Moodle's wrapper round PHP's $_SESSION.
 *
 * @global object $SESSION
 * @name $SESSION
 */
global $SESSION;

/**
 * Holds the user table record for the current user. Will be the 'guest'
 * user record for people who are not logged in.
 *
 * $USER is stored in the session.
 *
 * Items found in the user record:
 *  - $USER->email - The user's email address.
 *  - $USER->id - The unique integer identified of this user in the 'user' table.
 *  - $USER->email - The user's email address.
 *  - $USER->firstname - The user's first name.
 *  - $USER->lastname - The user's last name.
 *  - $USER->username - The user's login username.
 *  - $USER->secret - The user's ?.
 *  - $USER->lang - The user's language choice.
 *
 * @global object $USER
 * @name $USER
 */
global $USER;

/**
 * Frontpage course record
 */
global $SITE;

/**
 * A central store of information about the current page we are
 * generating in response to the user's request.
 *
 * @global moodle_page $PAGE
 * @name $PAGE
 */
global $PAGE;

/**
 * The current course. An alias for $PAGE->course.
 * @global object $COURSE
 * @name $COURSE
 */
global $COURSE;

/**
 * $OUTPUT is an instance of core_renderer or one of its subclasses. Use
 * it to generate HTML for output.
 *
 * $OUTPUT is initialised the first time it is used. See {@link bootstrap_renderer}
 * for the magic that does that. After $OUTPUT has been initialised, any attempt
 * to change something that affects the current theme ($PAGE->course, logged in use,
 * httpsrequried ... will result in an exception.)
 *
 * Please note the $OUTPUT is replacing the old global $THEME object.
 *
 * @global object $OUTPUT
 * @name $OUTPUT
 */
global $OUTPUT;

/**
 * Full script path including all params, slash arguments, scheme and host.
 *
 * Note: Do NOT use for getting of current page URL or detection of https,
 * instead use $PAGE->url or strpos($CFG->httpswwwroot, 'https:') === 0
 *
 * @global string $FULLME
 * @name $FULLME
 */
global $FULLME;

/**
 * Script path including query string and slash arguments without host.
 * @global string $ME
 * @name $ME
 */
global $ME;

/**
 * $FULLME without slasharguments and query string.
 * @global string $FULLSCRIPT
 * @name $FULLSCRIPT
 */
global $FULLSCRIPT;

/**
 * Relative moodle script path '/course/view.php'
 * @global string $SCRIPT
 * @name $SCRIPT
 */
global $SCRIPT;

// Store settings from config.php in array in $CFG - we can use it later to detect problems and overrides
$CFG->config_php_settings = (array)$CFG;
// Forced plugin settings override values from config_plugins table
unset($CFG->config_php_settings['forced_plugin_settings']);
if (!isset($CFG->forced_plugin_settings)) {
    $CFG->forced_plugin_settings = array();
}
// Set httpswwwroot default value (this variable will replace $CFG->wwwroot
// inside some URLs used in HTTPSPAGEREQUIRED pages.
$CFG->httpswwwroot = $CFG->wwwroot;

require_once($CFG->libdir .'/setuplib.php');        // Functions that MUST be loaded first

if (NO_OUTPUT_BUFFERING) {
    // we have to call this always before starting session because it discards headers!
    disable_output_buffering();
}

// Increase memory limits if possible
raise_memory_limit(MEMORY_STANDARD);

// Time to start counting
init_performance_info();

// Put $OUTPUT in place, so errors can be displayed.
$OUTPUT = new bootstrap_renderer();

// set handler for uncaught exceptions - equivalent to print_error() call
if (!PHPUNIT_TEST or PHPUNIT_UTIL) {
    set_exception_handler('default_exception_handler');
    set_error_handler('default_error_handler', E_ALL | E_STRICT);
}

// Acceptance tests needs special output to capture the errors,
// but not necessary for behat CLI command.
if (defined('BEHAT_SITE_RUNNING') && !defined('BEHAT_TEST')) {
    require_once(__DIR__ . '/behat/lib.php');
    set_error_handler('behat_error_handler', E_ALL | E_STRICT);
}

// If there are any errors in the standard libraries we want to know!
error_reporting(E_ALL | E_STRICT);

// Just say no to link prefetching (Moz prefetching, Google Web Accelerator, others)
// http://www.google.com/webmasters/faq.html#prefetchblock
if (!empty($_SERVER['HTTP_X_moz']) && $_SERVER['HTTP_X_moz'] === 'prefetch'){
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Prefetch Forbidden');
    echo('Prefetch request forbidden.');
    exit(1);
}

if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
    $CFG->prefix = '';
}

// location of all languages except core English pack
if (!isset($CFG->langotherroot)) {
    $CFG->langotherroot = $CFG->dataroot.'/lang';
}

// location of local lang pack customisations (dirs with _local suffix)
if (!isset($CFG->langlocalroot)) {
    $CFG->langlocalroot = $CFG->dataroot.'/lang';
}

//point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
//the problem is that we need specific version of quickforms and hacked excel files :-(
ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));
//point zend include path to moodles lib/zend so that includes and requires will search there for files before anywhere else
//please note zend library is supposed to be used only from web service protocol classes, it may be removed in future
ini_set('include_path', $CFG->libdir.'/zend' . PATH_SEPARATOR . ini_get('include_path'));

// Load up standard libraries
require_once($CFG->libdir .'/textlib.class.php');   // Functions to handle multibyte strings
require_once($CFG->libdir .'/filterlib.php');       // Functions for filtering test as it is output
require_once($CFG->libdir .'/ajax/ajaxlib.php');    // Functions for managing our use of JavaScript and YUI
require_once($CFG->libdir .'/weblib.php');          // Functions relating to HTTP and content
require_once($CFG->libdir .'/outputlib.php');       // Functions for generating output
require_once($CFG->libdir .'/navigationlib.php');   // Class for generating Navigation structure
require_once($CFG->libdir .'/dmllib.php');          // Database access
require_once($CFG->libdir .'/datalib.php');         // Legacy lib with a big-mix of functions.
require_once($CFG->libdir .'/accesslib.php');       // Access control functions
require_once($CFG->libdir .'/deprecatedlib.php');   // Deprecated functions included for backward compatibility
require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions
require_once($CFG->libdir .'/enrollib.php');        // Enrolment related functions
require_once($CFG->libdir .'/pagelib.php');         // Library that defines the moodle_page class, used for $PAGE
require_once($CFG->libdir .'/blocklib.php');        // Library for controlling blocks
require_once($CFG->libdir .'/eventslib.php');       // Events functions
require_once($CFG->libdir .'/grouplib.php');        // Groups functions
require_once($CFG->libdir .'/sessionlib.php');      // All session and cookie related stuff
require_once($CFG->libdir .'/editorlib.php');       // All text editor related functions and classes
require_once($CFG->libdir .'/messagelib.php');      // Messagelib functions
require_once($CFG->libdir .'/modinfolib.php');      // Cached information on course-module instances
require_once($CFG->dirroot.'/cache/lib.php');       // Cache API

// make sure PHP is not severly misconfigured
setup_validate_php_configuration();

// Connect to the database
setup_DB();

if (PHPUNIT_TEST and !PHPUNIT_UTIL) {
    // make sure tests do not run in parallel
    test_lock::acquire('phpunit');
    $dbhash = null;
    try {
        if ($dbhash = $DB->get_field('config', 'value', array('name'=>'phpunittest'))) {
            // reset DB tables
            phpunit_util::reset_database();
        }
    } catch (Exception $e) {
        if ($dbhash) {
            // we ned to reinit if reset fails
            $DB->set_field('config', 'value', 'na', array('name'=>'phpunittest'));
        }
    }
    unset($dbhash);
}

// Disable errors for now - needed for installation when debug enabled in config.php
if (isset($CFG->debug)) {
    $originalconfigdebug = $CFG->debug;
    unset($CFG->debug);
} else {
    $originalconfigdebug = null;
}

// Load up any configuration from the config table

if (PHPUNIT_TEST) {
    phpunit_util::initialise_cfg();
} else {
    initialise_cfg();
}

// Verify upgrade is not running unless we are in a script that needs to execute in any case
if (!defined('NO_UPGRADE_CHECK') and isset($CFG->upgraderunning)) {
    if ($CFG->upgraderunning < time()) {
        unset_config('upgraderunning');
    } else {
        print_error('upgraderunning');
    }
}

// Turn on SQL logging if required
if (!empty($CFG->logsql)) {
    $DB->set_logging(true);
}

// Prevent warnings from roles when upgrading with debug on
if (isset($CFG->debug)) {
    $originaldatabasedebug = $CFG->debug;
    unset($CFG->debug);
} else {
    $originaldatabasedebug = null;
}

// enable circular reference collector in PHP 5.3,
// it helps a lot when using large complex OOP structures such as in amos or gradebook
if (function_exists('gc_enable')) {
    gc_enable();
}

// Register default shutdown tasks - such as Apache memory release helper, perf logging, etc.
if (function_exists('register_shutdown_function')) {
    register_shutdown_function('moodle_request_shutdown');
}

// Set error reporting back to normal
if ($originaldatabasedebug === null) {
    $CFG->debug = DEBUG_MINIMAL;
} else {
    $CFG->debug = $originaldatabasedebug;
}
if ($originalconfigdebug !== null) {
    $CFG->debug = $originalconfigdebug;
}
unset($originalconfigdebug);
unset($originaldatabasedebug);
error_reporting($CFG->debug);

// find out if PHP configured to display warnings,
// this is a security problem because some moodle scripts may
// disclose sensitive information
if (ini_get_bool('display_errors')) {
    define('WARN_DISPLAY_ERRORS_ENABLED', true);
}
// If we want to display Moodle errors, then try and set PHP errors to match
if (!isset($CFG->debugdisplay)) {
    // keep it "as is" during installation
} else if (NO_DEBUG_DISPLAY) {
    // some parts of Moodle cannot display errors and debug at all.
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else if (empty($CFG->debugdisplay)) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    // This is very problematic in XHTML strict mode!
    ini_set('display_errors', '1');
}

// detect unsupported upgrade jump as soon as possible - do not change anything, do not use system functions
if (!empty($CFG->version) and $CFG->version < 2007101509) {
    print_error('upgraderequires19', 'error');
    die;
}

// Calculate and set $CFG->ostype to be used everywhere. Possible values are:
// - WINDOWS: for any Windows flavour.
// - UNIX: for the rest
// Also, $CFG->os can continue being used if more specialization is required
if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
    $CFG->ostype = 'WINDOWS';
} else {
    $CFG->ostype = 'UNIX';
}
$CFG->os = PHP_OS;

// Configure ampersands in URLs
ini_set('arg_separator.output', '&amp;');

// Work around for a PHP bug   see MDL-11237
ini_set('pcre.backtrack_limit', 20971520);  // 20 MB

// Location of standard files
$CFG->wordlist = $CFG->libdir .'/wordlist.txt';
$CFG->moddata  = 'moddata';

// A hack to get around magic_quotes_gpc being turned on
// It is strongly recommended to disable "magic_quotes_gpc"!
if (ini_get_bool('magic_quotes_gpc')) {
    function stripslashes_deep($value) {
        $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);
        return $value;
    }
    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
    if (!empty($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = stripslashes($_SERVER['REQUEST_URI']);
    }
    if (!empty($_SERVER['QUERY_STRING'])) {
        $_SERVER['QUERY_STRING'] = stripslashes($_SERVER['QUERY_STRING']);
    }
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $_SERVER['HTTP_REFERER'] = stripslashes($_SERVER['HTTP_REFERER']);
    }
   if (!empty($_SERVER['PATH_INFO'])) {
        $_SERVER['PATH_INFO'] = stripslashes($_SERVER['PATH_INFO']);
    }
    if (!empty($_SERVER['PHP_SELF'])) {
        $_SERVER['PHP_SELF'] = stripslashes($_SERVER['PHP_SELF']);
    }
    if (!empty($_SERVER['PATH_TRANSLATED'])) {
        $_SERVER['PATH_TRANSLATED'] = stripslashes($_SERVER['PATH_TRANSLATED']);
    }
}

// neutralise nasty chars in PHP_SELF
if (isset($_SERVER['PHP_SELF'])) {
    $phppos = strpos($_SERVER['PHP_SELF'], '.php');
    if ($phppos !== false) {
        $_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, $phppos+4);
    }
    unset($phppos);
}

// initialise ME's - this must be done BEFORE starting of session!
initialise_fullme();

// define SYSCONTEXTID in config.php if you want to save some queries,
// after install it must match the system context record id.
if (!defined('SYSCONTEXTID')) {
    get_system_context();
}

// Defining the site - aka frontpage course
try {
    $SITE = get_site();
} catch (dml_exception $e) {
    $SITE = null;
    if (empty($CFG->version)) {
        $SITE = new stdClass();
        $SITE->id = 1;
        $SITE->shortname = null;
    } else {
        throw $e;
    }
}
// And the 'default' course - this will usually get reset later in require_login() etc.
$COURSE = clone($SITE);
/** @deprecated Id of the frontpage course, use $SITE->id instead */
define('SITEID', $SITE->id);

// init session prevention flag - this is defined on pages that do not want session
if (CLI_SCRIPT) {
    // no sessions in CLI scripts possible
    define('NO_MOODLE_COOKIES', true);

} else if (!defined('NO_MOODLE_COOKIES')) {
    if (empty($CFG->version) or $CFG->version < 2009011900) {
        // no session before sessions table gets created
        define('NO_MOODLE_COOKIES', true);
    } else if (CLI_SCRIPT) {
        // CLI scripts can not have session
        define('NO_MOODLE_COOKIES', true);
    } else {
        define('NO_MOODLE_COOKIES', false);
    }
}

// start session and prepare global $SESSION, $USER
session_get_instance();
$SESSION = &$_SESSION['SESSION'];
$USER    = &$_SESSION['USER'];

// Late profiling, only happening if early one wasn't started
if (!empty($CFG->profilingenabled)) {
    require_once($CFG->libdir . '/xhprof/xhprof_moodle.php');
    if (profiling_start()) {
        register_shutdown_function('profiling_stop');
    }
}

// Hack to get around max_input_vars restrictions,
// we need to do this after session init to have some basic DDoS protection.
workaround_max_input_vars();

// Process theme change in the URL.
if (!empty($CFG->allowthemechangeonurl) and !empty($_GET['theme'])) {
    // we have to use _GET directly because we do not want this to interfere with _POST
    $urlthemename = optional_param('theme', '', PARAM_PLUGIN);
    try {
        $themeconfig = theme_config::load($urlthemename);
        // Makes sure the theme can be loaded without errors.
        if ($themeconfig->name === $urlthemename) {
            $SESSION->theme = $urlthemename;
        } else {
            unset($SESSION->theme);
        }
        unset($themeconfig);
        unset($urlthemename);
    } catch (Exception $e) {
        debugging('Failed to set the theme from the URL.', DEBUG_DEVELOPER, $e->getTrace());
    }
}
unset($urlthemename);

// Ensure a valid theme is set.
if (!isset($CFG->theme)) {
    $CFG->theme = 'standardwhite';
}

// Set language/locale of printed times.  If user has chosen a language that
// that is different from the site language, then use the locale specified
// in the language file.  Otherwise, if the admin hasn't specified a locale
// then use the one from the default language.  Otherwise (and this is the
// majority of cases), use the stored locale specified by admin.
// note: do not accept lang parameter from POST
if (isset($_GET['lang']) and ($lang = optional_param('lang', '', PARAM_SAFEDIR))) {
    if (get_string_manager()->translation_exists($lang, false)) {
        $SESSION->lang = $lang;
    }
}
unset($lang);

setup_lang_from_browser();

if (empty($CFG->lang)) {
    if (empty($SESSION->lang)) {
        $CFG->lang = 'en';
    } else {
        $CFG->lang = $SESSION->lang;
    }
}

// Set the default site locale, a lot of the stuff may depend on this
// it is definitely too late to call this first in require_login()!
moodle_setlocale();

// Create the $PAGE global - this marks the PAGE and OUTPUT fully initialised, this MUST be done at the end of setup!
if (!empty($CFG->moodlepageclass)) {
    if (!empty($CFG->moodlepageclassfile)) {
        require_once($CFG->moodlepageclassfile);
    }
    $classname = $CFG->moodlepageclass;
} else {
    $classname = 'moodle_page';
}
$PAGE = new $classname();
unset($classname);


if (!empty($CFG->debugvalidators) and !empty($CFG->guestloginbutton)) {
    if ($CFG->theme == 'standard' or $CFG->theme == 'standardwhite') {    // Temporary measure to help with XHTML validation
        if (isset($_SERVER['HTTP_USER_AGENT']) and empty($USER->id)) {      // Allow W3CValidator in as user called w3cvalidator (or guest)
            if ((strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false) or
                (strpos($_SERVER['HTTP_USER_AGENT'], 'Cynthia') !== false )) {
                if ($user = get_complete_user_data("username", "w3cvalidator")) {
                    $user->ignoresesskey = true;
                } else {
                    $user = guest_user();
                }
                session_set_user($user);
            }
        }
    }
}

// Apache log integration. In apache conf file one can use ${MOODULEUSER}n in
// LogFormat to get the current logged in username in moodle.
if ($USER && function_exists('apache_note')
    && !empty($CFG->apacheloguser) && isset($USER->username)) {
    $apachelog_userid = $USER->id;
    $apachelog_username = clean_filename($USER->username);
    $apachelog_name = '';
    if (isset($USER->firstname)) {
        // We can assume both will be set
        // - even if to empty.
        $apachelog_name = clean_filename($USER->firstname . " " .
                                         $USER->lastname);
    }
    if (session_is_loggedinas()) {
        $realuser = session_get_realuser();
        $apachelog_username = clean_filename($realuser->username." as ".$apachelog_username);
        $apachelog_name = clean_filename($realuser->firstname." ".$realuser->lastname ." as ".$apachelog_name);
        $apachelog_userid = clean_filename($realuser->id." as ".$apachelog_userid);
    }
    switch ($CFG->apacheloguser) {
        case 3:
            $logname = $apachelog_username;
            break;
        case 2:
            $logname = $apachelog_name;
            break;
        case 1:
        default:
            $logname = $apachelog_userid;
            break;
    }
    apache_note('MOODLEUSER', $logname);
}

// Use a custom script replacement if one exists
if (!empty($CFG->customscripts)) {
    if (($customscript = custom_script_path()) !== false) {
        require ($customscript);
    }
}

if (PHPUNIT_TEST) {
    // no ip blocking, these are CLI only
} else if (CLI_SCRIPT and !defined('WEB_CRON_EMULATED_CLI')) {
    // no ip blocking
} else if (!empty($CFG->allowbeforeblock)) { // allowed list processed before blocked list?
    // in this case, ip in allowed list will be performed first
    // for example, client IP is 192.168.1.1
    // 192.168 subnet is an entry in allowed list
    // 192.168.1.1 is banned in blocked list
    // This ip will be banned finally
    if (!empty($CFG->allowedip)) {
        if (!remoteip_in_list($CFG->allowedip)) {
            die(get_string('ipblocked', 'admin'));
        }
    }
    // need further check, client ip may a part of
    // allowed subnet, but a IP address are listed
    // in blocked list.
    if (!empty($CFG->blockedip)) {
        if (remoteip_in_list($CFG->blockedip)) {
            die(get_string('ipblocked', 'admin'));
        }
    }

} else {
    // in this case, IPs in blocked list will be performed first
    // for example, client IP is 192.168.1.1
    // 192.168 subnet is an entry in blocked list
    // 192.168.1.1 is allowed in allowed list
    // This ip will be allowed finally
    if (!empty($CFG->blockedip)) {
        if (remoteip_in_list($CFG->blockedip)) {
            // if the allowed ip list is not empty
            // IPs are not included in the allowed list will be
            // blocked too
            if (!empty($CFG->allowedip)) {
                if (!remoteip_in_list($CFG->allowedip)) {
                    die(get_string('ipblocked', 'admin'));
                }
            } else {
                die(get_string('ipblocked', 'admin'));
            }
        }
    }
    // if blocked list is null
    // allowed list should be tested
    if(!empty($CFG->allowedip)) {
        if (!remoteip_in_list($CFG->allowedip)) {
            die(get_string('ipblocked', 'admin'));
        }
    }

}

// // try to detect IE6 and prevent gzip because it is extremely buggy browser
if (!empty($_SERVER['HTTP_USER_AGENT']) and strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {
    @ini_set('zlib.output_compression', 'Off');
    if (function_exists('apache_setenv')) {
        @apache_setenv('no-gzip', 1);
    }
}

// Switch to CLI maintenance mode if required, we need to do it here after all the settings are initialised.
if (isset($CFG->maintenance_later) and $CFG->maintenance_later <= time()) {
    if (!file_exists("$CFG->dataroot/climaintenance.html")) {
        require_once("$CFG->libdir/adminlib.php");
        enable_cli_maintenance_mode();
    }
    unset_config('maintenance_later');
    if (AJAX_SCRIPT) {
        die;
    } else if (!CLI_SCRIPT) {
        redirect(new moodle_url('/'));
    }
}

// note: we can not block non utf-8 installations here, because empty mysql database
// might be converted to utf-8 in admin/index.php during installation



// this is a funny trick to make Eclipse believe that $OUTPUT and other globals
// contains an instance of core_renderer, etc. which in turn fixes autocompletion ;-)
if (false) {
    $DB = new moodle_database();
    $OUTPUT = new core_renderer(null, null);
    $PAGE = new moodle_page();
}
