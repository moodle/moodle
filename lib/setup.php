<?php
/**
 * setup.php - Sets up sessions, connects to databases and so on
 *
 * Normally this is only called by the main config.php file
 * Normally this file does not need to be edited.
 * @author Martin Dougiamas
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

////// DOCUMENTATION IN PHPDOC FORMAT FOR MOODLE GLOBALS AND COMMON OBJECT TYPES /////////////
/**
 * $USER is a global instance of a typical $user record.
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
 * @global object(user) $USER
 */
global $USER;
/**
 * This global variable is read in from the 'config' table.
 *
 * Some typical settings in the $CFG global:
 *  - $CFG->wwwroot - Path to moodle index directory in url format.
 *  - $CFG->dataroot - Path to moodle index directory on server's filesystem.
 *  - $CFG->libdir  - Path to moodle's library folder on server's filesystem.
 *
 * @global object(cfg) $CFG
 */
global $CFG;
/**
 * Definition of session type
 * @global object(session) $SESSION
 */
global $SESSION;
/**
 * Definition of shared memory cache
 */
global $MCACHE;
/**
 * Definition of course type
 * @global object(course) $COURSE
 */
global $COURSE;
/**
 * Database instances
 * @global object(mdb) $DB
 */
global $DB;
/**
 * $THEME is a global that defines the site theme.
 *
 * Items found in the theme record:
 *  - $THEME->cellheading - Cell colors.
 *  - $THEME->cellheading2 - Alternate cell colors.
 *
 * @global object(theme) $THEME
 */
global $THEME;

/**
 * HTTPSPAGEREQUIRED is a global to define if the page being displayed must run under HTTPS.
 *
 * It's primary goal is to allow 100% HTTPS pages when $CFG->loginhttps is enabled. Default to false.
 * It's enabled only by the httpsrequired() function and used in some pages to update some URLs
*/
global $HTTPSPAGEREQUIRED;


/// First try to detect some attacks on older buggy PHP versions
    if (isset($_REQUEST['GLOBALS']) || isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
        die('Fatal: Illegal GLOBALS overwrite attempt detected!');
    }


    if (!isset($CFG->wwwroot)) {
        trigger_error('Fatal: $CFG->wwwroot is not configured! Exiting.');
        die;
    }

/// store settings from config.php in array in $CFG - we can use it later to detect problems and overrides
    $CFG->config_php_settings = (array)$CFG;

/// Set httpswwwroot default value (this variable will replace $CFG->wwwroot
/// inside some URLs used in HTTPSPAGEREQUIRED pages.
    $CFG->httpswwwroot = $CFG->wwwroot;

    $CFG->libdir   = $CFG->dirroot .'/lib';

    require_once($CFG->libdir .'/setuplib.php');        // Functions that MUST be loaded first

/// Time to start counting
    init_performance_info();


/// If there are any errors in the standard libraries we want to know!
    error_reporting(E_ALL);

/// Just say no to link prefetching (Moz prefetching, Google Web Accelerator, others)
/// http://www.google.com/webmasters/faq.html#prefetchblock
    if (!empty($_SERVER['HTTP_X_moz']) && $_SERVER['HTTP_X_moz'] === 'prefetch'){
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Prefetch Forbidden');
        trigger_error('Prefetch request forbidden.');
        exit;
    }

/// Define admin directory
    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
    }

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = '';
    }

/// Load up standard libraries
    require_once($CFG->libdir .'/textlib.class.php');   // Functions to handle multibyte strings
    require_once($CFG->libdir .'/weblib.php');          // Functions for producing HTML
    require_once($CFG->libdir .'/datalib.php');         // Legacy lib with a big-mix of functions.
    require_once($CFG->libdir .'/accesslib.php');       // Access control functions
    require_once($CFG->libdir .'/deprecatedlib.php');   // Deprecated functions included for backward compatibility
    require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions
    require_once($CFG->libdir .'/eventslib.php');       // Events functions
    require_once($CFG->libdir .'/grouplib.php');        // Groups functions
    require_once($CFG->libdir .'/sessionlib.php');      // All session and cookie related stuff

    //point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
    //the problem is that we need specific version of quickforms and hacked excel files :-(
    ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));

/// set handler for uncought exceptions - equivalent to print_error() call
    set_exception_handler('default_exception_handler');

/// Connect to the database
    setup_DB();

/// Increase memory limits if possible
    raise_memory_limit('96M');    // We should never NEED this much but just in case...

/// Disable errors for now - needed for installation when debug enabled in config.php
    if (isset($CFG->debug)) {
        $originalconfigdebug = $CFG->debug;
        unset($CFG->debug);
    } else {
        $originalconfigdebug = -1;
    }

/// Load up any configuration from the config table
    $CFG = get_config();

/// Verify upgrade is not running
    if (isset($CFG->upgraderunning)) {
        if ($CFG->upgraderunning < time()) {
            unset_config('upgraderunning');
        } else {
            print_error('upgraderunning');
        }
    }

/// Turn on SQL logging if required
    if (!empty($CFG->logsql)) {
        $DB->set_logging(true);
    }

/// Prevent warnings from roles when upgrading with debug on
    if (isset($CFG->debug)) {
        $originaldatabasedebug = $CFG->debug;
        unset($CFG->debug);
    } else {
        $originaldatabasedebug = -1;
    }


/// For now, only needed under apache (and probably unstable in other contexts)
    if (function_exists('register_shutdown_function')) {
        register_shutdown_function('moodle_request_shutdown');
    }

/// Defining the site
    if ($SITE = get_site()) {
        /**
         * If $SITE global from {@link get_site()} is set then SITEID to $SITE->id, otherwise set to 1.
         */
        define('SITEID', $SITE->id);
        /// And the 'default' course
        $COURSE = clone($SITE);   // For now.  This will usually get reset later in require_login() etc.
    } else {
        /**
         * @ignore
         */
        define('SITEID', 1);
        /// And the 'default' course
        $COURSE = new object;  // no site created yet
        $COURSE->id = 1;
    }

    // define SYSCONTEXTID in config.php if you want to save some queries (after install or upgrade!)
    if (!defined('SYSCONTEXTID')) {
        get_system_context();
    }

/// Set error reporting back to normal
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


/// find out if PHP cofigured to display warnings
    if (ini_get_bool('display_errors')) {
        define('WARN_DISPLAY_ERRORS_ENABLED', true);
    }
/// If we want to display Moodle errors, then try and set PHP errors to match
    if (!isset($CFG->debugdisplay)) {
        //keep it as is during installation
    } else if (empty($CFG->debugdisplay)) {
        @ini_set('display_errors', '0');
        @ini_set('log_errors', '1');
    } else {
        @ini_set('display_errors', '1');
    }
// Even when users want to see errors in the output,
// some parts of Moodle cannot display them at all.
// (Once we are XHTML strict compliant, debugdisplay
//  _must_ go away).
    if (defined('MOODLE_SANE_OUTPUT')) {
        @ini_set('display_errors', '0');
        @ini_set('log_errors', '1');
    }

/// detect unsupported upgrade jump as soon as possible - do not change anything, do not use system functions
    if (!empty($CFG->version) and $CFG->version < 2007101509) {
        print_error('upgraderequires19', 'error');
        die;
    }

/// Shared-Memory cache init -- will set $MCACHE
/// $MCACHE is a global object that offers at least add(), set() and delete()
/// with similar semantics to the memcached PHP API http://php.net/memcache
/// Ensure we define rcache - so we can later check for it
/// with a really fast and unambiguous $CFG->rcache === false
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

/// Set a default enrolment configuration (see bug 1598)
    if (!isset($CFG->enrol)) {
        $CFG->enrol = 'manual';
    }

/// Set default enabled enrolment plugins
    if (!isset($CFG->enrol_plugins_enabled)) {
        $CFG->enrol_plugins_enabled = 'manual';
    }

/// File permissions on created directories in the $CFG->dataroot

    if (empty($CFG->directorypermissions)) {
        $CFG->directorypermissions = 0777;      // Must be octal (that's why it's here)
    }

/// Calculate and set $CFG->ostype to be used everywhere. Possible values are:
/// - WINDOWS: for any Windows flavour.
/// - UNIX: for the rest
/// Also, $CFG->os can continue being used if more specialization is required
    if (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) {
        $CFG->ostype = 'WINDOWS';
    } else {
        $CFG->ostype = 'UNIX';
    }
    $CFG->os = PHP_OS;

/// Set up default frame target string, based on $CFG->framename
    $CFG->frametarget = frametarget();

/// Setup cache dir for Smarty and others
    if (!file_exists($CFG->dataroot .'/cache')) {
        make_upload_directory('cache');
    }

/// Configure ampersands in URLs
    @ini_set('arg_separator.output', '&amp;');

/// Work around for a PHP bug   see MDL-11237
    @ini_set('pcre.backtrack_limit', 20971520);  // 20 MB

/// Location of standard files
    $CFG->wordlist    = $CFG->libdir .'/wordlist.txt';
    $CFG->javascript  = $CFG->libdir .'/javascript.php';
    $CFG->moddata     = 'moddata';

// Alas, in some cases we cannot deal with magic_quotes.
    if (defined('MOODLE_SANE_INPUT') && ini_get_bool('magic_quotes_gpc')) {
        mdie("Facilities that require MOODLE_SANE_INPUT "
             . "cannot work with magic_quotes_gpc. Please disable "
             . "magic_quotes_gpc.");
    }
/// A hack to get around magic_quotes_gpc being turned on
/// It is strongly recommended to disable "magic_quotes_gpc"!
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

/// start session and prepare global $SESSION
    $SESSION = new moodle_session();

/// set up global $USER
    if (!NO_MOODLE_COOKIES) {
        $USER = &$_SESSION['USER'];
    } else {
        $USER     = new object();
        $USER->id = 0; // user not logged in when session disabled
        if (isset($CFG->mnet_localhost_id)) {
            $USER->mnethostid = $CFG->mnet_localhost_id;
        }
    }

    if (defined('FULLME')) {     // Usually in command-line scripts like admin/cron.php
        $FULLME = FULLME;
        $ME = FULLME;
    } else {
        $FULLME = qualified_me();
        $ME = strip_querystring($FULLME);
    }

/// Load up theme variables (colours etc)

    if (!isset($CFG->themedir)) {
        $CFG->themedir = $CFG->dirroot.'/theme';
        $CFG->themewww = $CFG->wwwroot.'/theme';
    }
    $CFG->httpsthemewww = $CFG->themewww;

    if (isset($_GET['theme'])) {
        if ($CFG->allowthemechangeonurl || confirm_sesskey()) {
            $themename = clean_param($_GET['theme'], PARAM_SAFEDIR);
            if (($themename != '') and file_exists($CFG->themedir.'/'.$themename)) {
                $SESSION->theme = $themename;
            }
            unset($themename);
        }
    }

    if (!isset($CFG->theme)) {
        $CFG->theme = 'standardwhite';
    }

/// now do a session test to prevent random user switching - observed on some PHP/Apache combinations,
    $SESSION->session_verify();

/// Set language/locale of printed times.  If user has chosen a language that
/// that is different from the site language, then use the locale specified
/// in the language file.  Otherwise, if the admin hasn't specified a locale
/// then use the one from the default language.  Otherwise (and this is the
/// majority of cases), use the stored locale specified by admin.
    if (isset($_GET['lang']) && ($lang = clean_param($_GET['lang'], PARAM_SAFEDIR))) {
        if (file_exists($CFG->dataroot .'/lang/'. $lang) or file_exists($CFG->dirroot .'/lang/'. $lang)) {
            $SESSION->lang = $lang;
        } else if (file_exists($CFG->dataroot.'/lang/'.$lang.'_utf8') or
                   file_exists($CFG->dirroot .'/lang/'.$lang.'_utf8')) {
            $SESSION->lang = $lang.'_utf8';
        }
    }

    setup_lang_from_browser();

    unset($lang);

    if (empty($CFG->lang)) {
        if (empty($SESSION->lang)) {
            $CFG->lang = 'en_utf8';
        } else {
            $CFG->lang = $SESSION->lang;
        }
    }

    // set default locale and themes - might be changed again later from require_login()
    course_setup();

    if (!empty($CFG->opentogoogle)) {
        if (!NO_MOODLE_COOKIES and empty($USER->id)) {  // Ignore anyone logged in, or scripts without cookies
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) { // Google
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo! Slurp') !== false ) {  // Yahoo
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], '[ZSEBOT]') !== false ) {  // Zoomspider
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSNBOT') !== false ) {  // MSN Search
                    $USER = guest_user();
                }
            }
            if (empty($USER) && !empty($_SERVER['HTTP_REFERER'])) {
                if (strpos($_SERVER['HTTP_REFERER'], 'google') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_REFERER'], 'altavista') !== false ) {
                    $USER = guest_user();
                }
            }
            if (!empty($USER->id)) {
                load_all_capabilities();
            }
        }
    }

    if ($CFG->theme == 'standard' or $CFG->theme == 'standardwhite') {    // Temporary measure to help with XHTML validation
        if (isset($_SERVER['HTTP_USER_AGENT']) and empty($USER->id)) {      // Allow W3CValidator in as user called w3cvalidator (or guest)
            if ((strpos($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false) or
                (strpos($_SERVER['HTTP_USER_AGENT'], 'Cynthia') !== false )) {
                if ($USER = get_complete_user_data("username", "w3cvalidator")) {
                    $USER->ignoresesskey = true;
                } else {
                    $USER = guest_user();
                }
            }
        }
    }

/// Apache log intergration. In apache conf file one can use ${MOODULEUSER}n in
/// LogFormat to get the current logged in username in moodle.
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
        if (isset($USER->realuser)) {
            if ($realuser = $DB->get_record('user', array('id'=>$USER->realuser))) {
                $apachelog_username = clean_filename($realuser->username." as ".$apachelog_username);
                $apachelog_name = clean_filename($realuser->firstname." ".$realuser->lastname ." as ".$apachelog_name);
                $apachelog_userid = clean_filename($realuser->id." as ".$apachelog_userid);
            }
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

/// Adjust ALLOWED_TAGS
    adjust_allowed_tags();

/// Use a custom script replacement if one exists
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

/// note: we can not block non utf-8 installatrions here, because empty mysql database
/// might be converted to utf-8 in admin/index.php during installation
?>
