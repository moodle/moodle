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
 * Definition of db type
 * @global object(db) $db
 */
global $db;
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

/// sometimes default PHP settings are borked on shared hosting servers, I wonder why they have to do that??
    @ini_set('precision', 14); // needed for upgrades and gradebook

/// New versions of HTML Purifier are not compatible with PHP 4
    if (version_compare(phpversion(), "5.0.5") < 0) {
        $CFG->enablehtmlpurifier = 0;
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

/// Connect to the database using adodb

/// Set $CFG->dbfamily global
/// and configure some other specific variables for each db BEFORE attempting the connection
    preconfigure_dbconnection();

    require_once($CFG->libdir .'/adodb/adodb.inc.php'); // Database access functions

    $db = &ADONewConnection($CFG->dbtype);

    // See MDL-6760 for why this is necessary. In Moodle 1.8, once we start using NULLs properly,
    // we probably want to change this value to ''.
    $db->null2null = 'A long random string that will never, ever match something we want to insert into the database, I hope. \'';

    error_reporting(0);  // Hide errors

    if (!isset($CFG->dbpersist) or !empty($CFG->dbpersist)) {    // Use persistent connection (default)
        $dbconnected = $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    } else {                                                     // Use single connection
        $dbconnected = $db->Connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    }
    if (! $dbconnected) {
        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine comsumption
        if (isset($_SERVER['SERVER_PROTOCOL'])) { 
            // Avoid it with cron.php. Note that we assume it's HTTP/1.x
            header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');        
        }
        // and then for human consumption...
        echo '<html><body>';
        echo '<table align="center"><tr>';
        echo '<td style="color:#990000; text-align:center; font-size:large; border-width:1px; '.
             '    border-color:#000000; border-style:solid; border-radius: 20px; border-collapse: collapse; '.
             '    -moz-border-radius: 20px; padding: 15px">';
        echo '<p>Error: Database connection failed.</p>';
        echo '<p>It is possible that the database is overloaded or otherwise not running properly.</p>';
        echo '<p>The site administrator should also check that the database details have been correctly specified in config.php</p>';
        echo '</td></tr></table>';
        echo '</body></html>';

        error_log('ADODB Error: '.$db->ErrorMsg()); // see MDL-14628

        if (empty($CFG->noemailever) and !empty($CFG->emailconnectionerrorsto)) {
            if (file_exists($CFG->dataroot.'/emailcount')){
                $fp = fopen($CFG->dataroot.'/emailcount', 'r');
                $content = fread($fp, 24);
                fclose($fp);
                if((time() - (int)$content) > 600){
                    mail($CFG->emailconnectionerrorsto, 
                        'WARNING: Database connection error: '.$CFG->wwwroot, 
                        'Connection error: '.$CFG->wwwroot);
                    $fp = fopen($CFG->dataroot.'/emailcount', 'w');
                    fwrite($fp, time());
                }
            } else {
               mail($CFG->emailconnectionerrorsto, 
                    'WARNING: Database connection error: '.$CFG->wwwroot, 
                    'Connection error: '.$CFG->wwwroot);
               $fp = fopen($CFG->dataroot.'/emailcount', 'w');
               fwrite($fp, time());
            }
        }
        die;
    }

/// Forcing ASSOC mode for ADOdb (some DBs default to FETCH_BOTH)
    $db->SetFetchMode(ADODB_FETCH_ASSOC);

/// Starting here we have a correct DB conection but me must avoid
/// to execute any DB transaction until "set names" has been executed
/// some lines below!

    error_reporting(E_ALL);       // Show errors from now on.

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = '';
    }


/// Define admin directory

    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
    }

/// Increase memory limits if possible
    raise_memory_limit('96M');    // We should never NEED this much but just in case...

/// Load up standard libraries

    require_once($CFG->libdir .'/textlib.class.php');   // Functions to handle multibyte strings
    require_once($CFG->libdir .'/weblib.php');          // Functions for producing HTML
    require_once($CFG->libdir .'/dmllib.php');          // Functions to handle DB data (DML)
    require_once($CFG->libdir .'/datalib.php');         // Legacy lib with a big-mix of functions.
    require_once($CFG->libdir .'/accesslib.php');       // Access control functions
    require_once($CFG->libdir .'/deprecatedlib.php');   // Deprecated functions included for backward compatibility
    require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions
    require_once($CFG->libdir .'/eventslib.php');       // Events functions
    require_once($CFG->libdir .'/grouplib.php');        // Groups functions

    //point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
    //the problem is that we need specific version of quickforms and hacked excel files :-(
    ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));

/// Disable errors for now - needed for installation when debug enabled in config.php
    if (isset($CFG->debug)) {
        $originalconfigdebug = $CFG->debug;
        unset($CFG->debug);
    } else {
        $originalconfigdebug = -1;
    }

/// Set the client/server and connection to utf8
/// and configure some other specific variables for each db
    configure_dbconnection();

/// Load up any configuration from the config table
    $CFG = get_config();

/// Turn on SQL logging if required
    if (!empty($CFG->logsql)) {
        $db->LogSQL();
        // And override ADODB's default logging time
        if (isset($CFG->logsqlmintime)) {
            global $ADODB_PERF_MIN;
            $ADODB_PERF_MIN = $CFG->logsqlmintime;
        }
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

/// Set up smarty template system
    //require_once($CFG->libdir .'/smarty/Smarty.class.php');
    //$smarty = new Smarty;
    //$smarty->template_dir = $CFG->dirroot .'/templates/'. $CFG->template;
    //if (!file_exists($CFG->dataroot .'/cache/smarty')) {
    //    make_upload_directory('cache/smarty');
    //}
    //$smarty->compile_dir = $CFG->dataroot .'/cache/smarty';

/// Set up session handling
    if(empty($CFG->respectsessionsettings)) {
        if (empty($CFG->dbsessions)) {   /// File-based sessions

            // Some distros disable GC by setting probability to 0
            // overriding the PHP default of 1
            // (gc_probability is divided by gc_divisor, which defaults to 1000)
            if (ini_get('session.gc_probability') == 0) {
                ini_set('session.gc_probability', 1);
            }

            if (!empty($CFG->sessiontimeout)) {
                ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);
            }

            if (!file_exists($CFG->dataroot .'/sessions')) {
                make_upload_directory('sessions');
            }
            ini_set('session.save_path', $CFG->dataroot .'/sessions');

        } else {                         /// Database sessions
            ini_set('session.save_handler', 'user');

            $ADODB_SESSION_DRIVER  = $CFG->dbtype;
            $ADODB_SESSION_CONNECT = $CFG->dbhost;
            $ADODB_SESSION_USER    = $CFG->dbuser;
            $ADODB_SESSION_PWD     = $CFG->dbpass;
            $ADODB_SESSION_DB      = $CFG->dbname;
            $ADODB_SESSION_TBL     = $CFG->prefix.'sessions2';
            if (!empty($CFG->sessiontimeout)) {
                $ADODB_SESS_LIFE   = $CFG->sessiontimeout;
            }

            require_once($CFG->libdir. '/adodb/session/adodb-session2.php');
        }
    }
/// Set sessioncookie and sessioncookiepath variable if it isn't already
    if (!isset($CFG->sessioncookie)) {
        $CFG->sessioncookie = '';
    }
    if (!isset($CFG->sessioncookiedomain)) {
        $CFG->sessioncookiedomain = '';
    }
    if (!isset($CFG->sessioncookiepath)) {
        $CFG->sessioncookiepath = '/';
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
/// A hack to get around magic_quotes_gpc being turned off
/// It is strongly recommended to enable "magic_quotes_gpc"!
    if (!ini_get_bool('magic_quotes_gpc') && !defined('MOODLE_SANE_INPUT') ) {
        function addslashes_deep($value) {
            $value = is_array($value) ?
                    array_map('addslashes_deep', $value) :
                    addslashes($value);
            return $value;
        }
        $_POST = array_map('addslashes_deep', $_POST);
        $_GET = array_map('addslashes_deep', $_GET);
        $_COOKIE = array_map('addslashes_deep', $_COOKIE);
        $_REQUEST = array_map('addslashes_deep', $_REQUEST);
        if (!empty($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = addslashes($_SERVER['REQUEST_URI']);
        }
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SERVER['QUERY_STRING'] = addslashes($_SERVER['QUERY_STRING']);
        }
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $_SERVER['HTTP_REFERER'] = addslashes($_SERVER['HTTP_REFERER']);
        }
       if (!empty($_SERVER['PATH_INFO'])) {
            $_SERVER['PATH_INFO'] = addslashes($_SERVER['PATH_INFO']);
        }
        if (!empty($_SERVER['PHP_SELF'])) {
            $_SERVER['PHP_SELF'] = addslashes($_SERVER['PHP_SELF']);
        }
        if (!empty($_SERVER['PATH_TRANSLATED'])) {
            $_SERVER['PATH_TRANSLATED'] = addslashes($_SERVER['PATH_TRANSLATED']);
        }
    }

/// neutralise nasty chars in PHP_SELF
    if (isset($_SERVER['PHP_SELF'])) {
        $phppos = strpos($_SERVER['PHP_SELF'], '.php');
        if ($phppos !== false) {
            $_SERVER['PHP_SELF'] = substr($_SERVER['PHP_SELF'], 0, $phppos+4);
        }
        unset($phppos);
    }

/// The following code can emulate "register globals" if required.
/// This hack is no longer being applied as of Moodle 1.6 unless you really 
/// really want to use it (by defining  $CFG->enableglobalshack = true)

    if (!empty($CFG->enableglobalshack) && !defined('MOODLE_SANE_INPUT')) {
        if (!empty($CFG->detect_unchecked_vars)) {
            global $UNCHECKED_VARS;
            $UNCHECKED_VARS->url = $_SERVER['PHP_SELF'];
            $UNCHECKED_VARS->vars = array();
        }
        if (isset($_GET)) {
            extract($_GET, EXTR_SKIP);    // Skip existing variables, ie CFG
            if (!empty($CFG->detect_unchecked_vars)) {
                foreach ($_GET as $key => $val) {
                    $UNCHECKED_VARS->vars[$key]=$val;
                }
            }
        }
        if (isset($_POST)) {
            extract($_POST, EXTR_SKIP);   // Skip existing variables, ie CFG
            if (!empty($CFG->detect_unchecked_vars)) {
                foreach ($_POST as $key => $val) {
                    $UNCHECKED_VARS->vars[$key]=$val;
                }
            }
        }
        if (isset($_SERVER)) {
            extract($_SERVER);
        }
    }


/// Load up global environment variables

    if (!isset($CFG->cookiesecure) or strpos($CFG->wwwroot, 'https://') !== 0) {
        $CFG->cookiesecure = false;
    }

    if (!isset($CFG->cookiehttponly)) {
        $CFG->cookiehttponly = false;
    }

    //discard session ID from POST, GET and globals to tighten security,
    //this session fixation prevention can not be used in cookieless mode
    if (empty($CFG->usesid) && !defined('MOODLE_SANE_INPUT')) {
        unset(${'MoodleSession'.$CFG->sessioncookie});
        unset($_GET['MoodleSession'.$CFG->sessioncookie]);
        unset($_POST['MoodleSession'.$CFG->sessioncookie]);
    }
    //compatibility hack for Moodle Cron, cookies not deleted, but set to "deleted" - should not be needed with $nomoodlecookie in cron.php now 
    if (!empty($_COOKIE['MoodleSession'.$CFG->sessioncookie]) && $_COOKIE['MoodleSession'.$CFG->sessioncookie] == "deleted") {
        unset($_COOKIE['MoodleSession'.$CFG->sessioncookie]);
    }
    if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] == "deleted") {
        unset($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]);
    }


    if (empty($nomoodlecookie)) {
        session_name('MoodleSession'.$CFG->sessioncookie);
        if (check_php_version('5.2.0')) {
            session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
        } else {
            session_set_cookie_params(0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
        }
        @session_start();
        if (! isset($_SESSION['SESSION'])) {
            $_SESSION['SESSION'] = new object;
            $_SESSION['SESSION']->session_test = random_string(10);
            if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
                $_SESSION['SESSION']->has_timed_out = true;
            }
            if (check_php_version('5.2.0')) {
                setcookie('MoodleSessionTest'.$CFG->sessioncookie, $_SESSION['SESSION']->session_test, 0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure, $CFG->cookiehttponly);
            } else {
                setcookie('MoodleSessionTest'.$CFG->sessioncookie, $_SESSION['SESSION']->session_test, 0, $CFG->sessioncookiepath, $CFG->sessioncookiedomain, $CFG->cookiesecure);
            }
            $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] = $_SESSION['SESSION']->session_test;
        }
        if (! isset($_SESSION['USER']))    {
            $_SESSION['USER']    = new object;
        }

        $SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
        $USER    = &$_SESSION['USER'];
        if (!isset($USER->id)) {
            $USER->id = 0; // to enable proper function of $CFG->notloggedinroleid hack
        }
    }
    else {
        $SESSION  = NULL;
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
    if (!empty($CFG->usesid) && empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
        require_once("$CFG->dirroot/lib/cookieless.php");
    }
/// In VERY rare cases old PHP server bugs (it has been found on PHP 4.1.2 running
/// as a CGI under IIS on Windows) may require that you uncomment the following:
//  session_register("USER");
//  session_register("SESSION");



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
/// disable checks when working in cookieless mode
    if (empty($CFG->usesid) || !empty($_COOKIE['MoodleSession'.$CFG->sessioncookie])) {
        if ($SESSION != NULL) {
            if (empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
                report_session_error();
            } else if (isset($SESSION->session_test) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] != $SESSION->session_test) {
                report_session_error();
            }
        }
    }


/// Set language/locale of printed times.  If user has chosen a language that
/// that is different from the site language, then use the locale specified
/// in the language file.  Otherwise, if the admin hasn't specified a locale
/// then use the one from the default language.  Otherwise (and this is the
/// majority of cases), use the stored locale specified by admin.
    if ($SESSION !== NULL && isset($_GET['lang']) && ($lang = clean_param($_GET['lang'], PARAM_SAFEDIR))) {
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
        if (empty($USER->id)) {  // Ignore anyone logged in
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
            if (!empty($CFG->guestloginbutton) && empty($USER) && !empty($_SERVER['HTTP_REFERER'])) {
                if (strpos($_SERVER['HTTP_REFERER'], 'google') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_REFERER'], 'altavista') !== false ) {
                    $USER = guest_user();
                }
            }
            if (!empty($USER)) {
                load_all_capabilities();
            }
        }
    }

    if (!empty($CFG->guestloginbutton)) {
        if ($CFG->theme == 'standard' or $CFG->theme == 'standardwhite') {    // Temporary measure to help with XHTML validation
            if (isset($_SERVER['HTTP_USER_AGENT']) and empty($_SESSION['USER']->id)) {      // Allow W3CValidator in as user called w3cvalidator (or guest)
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
            if ($realuser = get_record('user', 'id', $USER->realuser)) {
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

/// note: we can not block non utf-8 installatrions here, because empty mysql database
/// might be converted to utf-8 in admin/index.php during installation
?>
