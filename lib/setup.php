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
 * @package   moodlecore
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 *
 * @global object $CFG
 * @name $CFG
 */
global $CFG;

// We can detect real dirroot path reliably since PHP 4.0.2,
// it can not be anything else, there is no point in having this in config.php
$CFG->dirroot = dirname(dirname(__FILE__));

// Normalise dataroot - we do not want any symbolic links, trailing / or any other weirdness there
if (!isset($CFG->dataroot)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    echo('Fatal error: $CFG->dataroot is not specified in config.php! Exiting.');
    exit(1);
}
$CFG->dataroot = realpath($CFG->dataroot);
if ($CFG->dataroot === false) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    echo('Fatal error: $CFG->dataroot is not configured properly, directory does not exist or is not accessible! Exiting.');
    exit(1);
}

// wwwroot is mandatory
if (!isset($CFG->wwwroot) or $CFG->wwwroot === 'http://example.com/moodle') {
    // trigger_error() is not correct here, no need to log this
    header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
    echo('Fatal error: $CFG->wwwroot is not configured! Exiting.');
    exit(1);
}

// Define admin directory
if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
    $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
}

// Set up some paths.
$CFG->libdir = $CFG->dirroot .'/lib';

// The current directory in PHP version 4.3.0 and above isn't necessarily the
// directory of the script when run from the command line. The require_once()
// would fail, so we'll have to chdir()
if (!isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['argv'][0])) {
    chdir(dirname($_SERVER['argv'][0]));
}

// sometimes default PHP settings are borked on shared hosting servers, I wonder why they have to do that??
@ini_set('precision', 14); // needed for upgrades and gradebook

// Scripts may request no debug and error messages in output
// please note it must be defined before including the config.php script
// and in some cases you also need to set custom default exception handler
if (!defined('NO_DEBUG_DISPLAY')) {
    define('NO_DEBUG_DISPLAY', false);
}

// Servers should define a default timezone in php.ini, but if they don't then make sure something is defined.
// This is a quick hack.  Ideally we should ask the admin for a value.  See MDL-22625 for more on this.
if (function_exists('date_default_timezone_set') and function_exists('date_default_timezone_get')) {
    @date_default_timezone_set(@date_default_timezone_get());
}

// Detect CLI scripts - CLI scripts are executed from command line, do not have session and we do not want HTML in output
// In your new CLI scripts just add: if (isset($_SERVER['REMOTE_ADDR'])) {die;} before requiring config.php.
if (!defined('CLI_SCRIPT')) {
    // CLI_SCRIPT is defined in 'fake' CLI script /admin/cron.php, do not abuse this elsewhere!
    if (isset($_SERVER['REMOTE_ADDR'])) {
        define('CLI_SCRIPT', false);
    } else {
        /** @ignore */
        define('CLI_SCRIPT', true);
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
$CFG->yui2version = '2.8.1';
$CFG->yui3version = '3.1.1';


// special support for highly optimised scripts that do not need libraries and DB connection
if (defined('ABORT_AFTER_CONFIG')) {
    if (!defined('ABORT_AFTER_CONFIG_CANCEL')) {
        // hide debugging if not enabled in config.php - we do not want to disclose sensitive info
        if (isset($CFG->debug)) {
            error_reporting($CFG->debug);
        } else {
            error_reporting(0);
        }
        if (empty($CFG->debugdisplay)) {
            @ini_set('display_errors', '0');
            @ini_set('log_errors', '1');
        } else {
            @ini_set('display_errors', '1');
        }
        require_once("$CFG->dirroot/lib/configonlylib.php");
        return;
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
 *  - $USER->emailstop - Does the user want email sent to them?
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
 * Shared memory cache.
 * @global object $MCACHE
 * @name $MCACHE
 */
global $MCACHE;

/**
 * A global to define if the page being displayed must run under HTTPS.
 *
 * Its primary goal is to allow 100% HTTPS pages when $CFG->loginhttps is enabled. Default to false.
 * Its enabled only by the $PAGE->https_required() function and used in some pages to update some URLs
 *
 * @global bool $HTTPSPAGEREQUIRED
 * @name $HTTPSPAGEREQUIRED
 */
global $HTTPSPAGEREQUIRED;

/**
 * Full script path including all params, slash arguments, scheme and host.
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

// Time to start counting
init_performance_info();

// Put $OUTPUT in place, so errors can be displayed.
$OUTPUT = new bootstrap_renderer();

// set handler for uncaught exceptions - equivalent to print_error() call
set_exception_handler('default_exception_handler');

// If there are any errors in the standard libraries we want to know!
error_reporting(E_ALL);

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
require_once($CFG->libdir .'/pagelib.php');         // Library that defines the moodle_page class, used for $PAGE
require_once($CFG->libdir .'/blocklib.php');        // Library for controlling blocks
require_once($CFG->libdir .'/eventslib.php');       // Events functions
require_once($CFG->libdir .'/grouplib.php');        // Groups functions
require_once($CFG->libdir .'/sessionlib.php');      // All session and cookie related stuff
require_once($CFG->libdir .'/editorlib.php');       // All text editor related functions and classes
require_once($CFG->libdir .'/messagelib.php');      // Messagelib functions

// make sure PHP is not severly misconfigured
setup_validate_php_configuration();

// Increase memory limits if possible
raise_memory_limit('96M');    // We should never NEED this much but just in case...

// Connect to the database
setup_DB();

// Disable errors for now - needed for installation when debug enabled in config.php
if (isset($CFG->debug)) {
    $originalconfigdebug = $CFG->debug;
    unset($CFG->debug);
} else {
    $originalconfigdebug = -1;
}

// Load up any configuration from the config table
initialise_cfg();

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
    $originaldatabasedebug = -1;
}


// For now, only needed under apache (and probably unstable in other contexts)
if (function_exists('register_shutdown_function')) {
    register_shutdown_function('moodle_request_shutdown');
}

// Defining the site
try {
    $SITE = get_site();
    /**
     * If $SITE global from {@link get_site()} is set then SITEID to $SITE->id, otherwise set to 1.
     */
    define('SITEID', $SITE->id);
    // And the 'default' course - this will usually get reset later in require_login() etc.
    $COURSE = clone($SITE);
} catch (dml_read_exception $e) {
    $SITE = null;
    if (empty($CFG->version)) {
        // we are just installing
        /**
         * @ignore
         */
        define('SITEID', 1);
        // And the 'default' course
        $COURSE = new object();  // no site created yet
        $COURSE->id = 1;
    } else {
        throw $e;
    }
}

// define SYSCONTEXTID in config.php if you want to save some queries (after install or upgrade!)
if (!defined('SYSCONTEXTID')) {
    get_system_context();
}

// Set error reporting back to normal
if ($originaldatabasedebug == -1) {
    $CFG->debug = DEBUG_MINIMAL;
} else {
    $CFG->debug = $originaldatabasedebug;
}
if ($originalconfigdebug !== -1) {
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
    @ini_set('display_errors', '0');
    @ini_set('log_errors', '1');
} else if (empty($CFG->debugdisplay)) {
    @ini_set('display_errors', '0');
    @ini_set('log_errors', '1');
} else {
    // This is very problematic in XHTML strict mode!
    @ini_set('display_errors', '1');
}

// detect unsupported upgrade jump as soon as possible - do not change anything, do not use system functions
if (!empty($CFG->version) and $CFG->version < 2007101509) {
    print_error('upgraderequires19', 'error');
    die;
}

// Shared-Memory cache init -- will set $MCACHE
// $MCACHE is a global object that offers at least add(), set() and delete()
// with similar semantics to the memcached PHP API http://php.net/memcache
// Ensure we define rcache - so we can later check for it
// with a really fast and unambiguous $CFG->rcache === false
if (!empty($CFG->cachetype)) {
    if (empty($CFG->rcache)) {
        $CFG->rcache = false;
    } else {
        $CFG->rcache = true;
    }

    // do not try to initialize if cache disabled
    if (!$CFG->rcache) {
        $CFG->cachetype = '';
    }

    if ($CFG->cachetype === 'memcached' && !empty($CFG->memcachedhosts)) {
        if (!init_memcached()) {
            debugging("Error initialising memcached");
            $CFG->cachetype = '';
            $CFG->rcache = false;
        }
    } else if ($CFG->cachetype === 'eaccelerator') {
        if (!init_eaccelerator()) {
            debugging("Error initialising eaccelerator cache");
            $CFG->cachetype = '';
            $CFG->rcache = false;
        }
    }

} else { // just make sure it is defined
    $CFG->cachetype = '';
    $CFG->rcache    = false;
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

// Setup cache dir for Smarty and others
if (!file_exists($CFG->dataroot .'/cache')) {
    make_upload_directory('cache');
}

// Configure ampersands in URLs
@ini_set('arg_separator.output', '&amp;');

// Work around for a PHP bug   see MDL-11237
@ini_set('pcre.backtrack_limit', 20971520);  // 20 MB

// Location of standard files
$CFG->wordlist = $CFG->libdir .'/wordlist.txt';
$CFG->moddata  = 'moddata';

// Create the $PAGE global.
if (!empty($CFG->moodlepageclass)) {
    $classname = $CFG->moodlepageclass;
} else {
    $classname = 'moodle_page';
}
$PAGE = new $classname();
unset($classname);

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

// initialise ME's
initialise_fullme();

// start session and prepare global $SESSION, $USER
session_get_instance();
$SESSION = &$_SESSION['SESSION'];
$USER    = &$_SESSION['USER'];

// Process theme change in the URL.
if (!empty($CFG->allowthemechangeonurl) and !empty($_GET['theme'])) {
    // we have to use _GET directly because we do not want this to interfere with _POST
    $urlthemename = optional_param('theme', '', PARAM_SAFEDIR);
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

// Adjust ALLOWED_TAGS
adjust_allowed_tags();

// Use a custom script replacement if one exists
if (!empty($CFG->customscripts)) {
    if (($customscript = custom_script_path()) !== false) {
        require ($customscript);
    }
}

// in the first case, ip in allowed list will be performed first
// for example, client IP is 192.168.1.1
// 192.168 subnet is an entry in allowed list
// 192.168.1.1 is banned in blocked list
// This ip will be banned finally
if (!empty($CFG->allowbeforeblock)) { // allowed list processed before blocked list?
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

// note: we can not block non utf-8 installations here, because empty mysql database
// might be converted to utf-8 in admin/index.php during installation



// this is a funny trick to make Eclipse believe that $OUTPUT and other globals
// contains an instance of core_renderer, etc. which in turn fixes autocompletion ;-)
if (false) {
    $DB = new moodle_database();
    $OUTPUT = new core_renderer(null, null);
    $PAGE = new moodle_page();
}
