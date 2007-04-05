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
 * This global variable is read in from the 'config' table.
 *
 * Some typical settings in the $CFG global:
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
 * $USER is a global instance of a typical $user record.
 *
 * Items found in the user record:
 *  - $CFG->wwwroot - Path to moodle index directory in url format.
 *  - $CFG->dataroot - Path to moodle index directory on server's filesystem.
 *  - $CFG->libroot  - Path to moodle's library folder on server's filesystem.
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

/// Set httpswwwroot default value (this variable will replace $CFG->wwwroot
/// inside some URLs used in HTTPSPAGEREQUIRED pages.
$CFG->httpswwwroot = $CFG->wwwroot;

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

    $CFG->libdir   = $CFG->dirroot .'/lib';

    require_once($CFG->libdir .'/adodb/adodb.inc.php'); // Database access functions

    $db = &ADONewConnection($CFG->dbtype);

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

        if (!empty($CFG->emailconnectionerrorsto)) {
            mail($CFG->emailconnectionerrorsto, 
                 'WARNING: Database connection error: '.$CFG->wwwroot, 
                 'Connection error: '.$CFG->wwwroot);
        }
        die;
    }

    error_reporting(E_ALL);       // Show errors from now on.

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = '';
    }


/// Define admin directory

    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
    }


/// Load up standard libraries

    require_once($CFG->libdir .'/weblib.php');          // Functions for producing HTML
    require_once($CFG->libdir .'/datalib.php');         // Functions for accessing databases
    require_once($CFG->libdir .'/moodlelib.php');       // Other general-purpose functions


/// Increase memory limits if possible

    raise_memory_limit('64M');    // We should never NEED this much but just in case...


/// Load up any configuration from the config table
    $CFG = get_config();

/// Turn on SQL logging if required
    if (!empty($CFG->logsql)) {
        $db->LogSQL();
    }


/// Set error reporting back to normal
    if (empty($CFG->debug)) {
        $CFG->debug = 7;
    }
    error_reporting($CFG->debug);

/// For now, only needed under apache (and probably unstable in other contexts)
    if (function_exists('apache_child_terminate')) {
        register_shutdown_function('moodle_request_shutdown');
    }

/// Set a default enrolment configuration (see bug 1598)
    if (!isset($CFG->enrol)) {
        $CFG->enrol = 'internal';
    }

/// File permissions on created directories in the $CFG->dataroot

    if (empty($CFG->directorypermissions)) {
        $CFG->directorypermissions = 0777;      // Must be octal (that's why it's here)
    }

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
            $ADODB_SESSION_TBL     = $CFG->prefix.'sessions';
            // Respect sessiontimeout setting for DB sessions.
            if (!empty($CFG->sessiontimeout)) {
             	$ADODB_SESS_LIFE   = $CFG->sessiontimeout;
            } 

            require_once($CFG->libdir. '/adodb/session/adodb-session.php');
        }
    }
/// Set sessioncookie variable if it isn't already
    if (!isset($CFG->sessioncookie)) {
        $CFG->sessioncookie = '';
    }

/// Configure ampersands in URLs

    @ini_set('arg_separator.output', '&amp;');

/// Location of standard files

    $CFG->wordlist    = $CFG->libdir .'/wordlist.txt';
    $CFG->javascript  = $CFG->libdir .'/javascript.php';
    $CFG->moddata     = 'moddata';


/// A hack to get around magic_quotes_gpc being turned off
/// It is strongly recommended to enable "magic_quotes_gpc"!

    if (!ini_get_bool('magic_quotes_gpc') ) {
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


/// The following is a hack to get around the problem of PHP installations
/// that have "register_globals" turned off (default since PHP 4.1.0).
/// This hack will be removed in 1.6.
/// It is strongly recommended to disable "register_globals"!

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

/// Load up global environment variables

    class object {};

    unset(${'MoodleSession'.$CFG->sessioncookie});
    unset($_GET['MoodleSession'.$CFG->sessioncookie]);
    unset($_POST['MoodleSession'.$CFG->sessioncookie]);

    //compatibility hack for Moodle Cron, cookies not deleted, but set to "deleted"
    if (!empty($_COOKIE['MoodleSession'.$CFG->sessioncookie]) && $_COOKIE['MoodleSession'.$CFG->sessioncookie] == "deleted") {
        unset($_COOKIE['MoodleSession'.$CFG->sessioncookie]);
    }
    if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] == "deleted") {
        unset($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie]);
    }

    if (!isset($nomoodlecookie)) {
        session_name('MoodleSession'.$CFG->sessioncookie);
        @session_start();
        if (! isset($_SESSION['SESSION'])) {
            $_SESSION['SESSION'] = new object;
            $_SESSION['SESSION']->session_test = random_string(10);
            if (!empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
                $_SESSION['SESSION']->has_timed_out = true;
            }
            setcookie('MoodleSessionTest'.$CFG->sessioncookie, $_SESSION['SESSION']->session_test, 0, '/');
            $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] = $_SESSION['SESSION']->session_test;
        }
        if (! isset($_SESSION['USER']))    {
            $_SESSION['USER']    = new object;
        }

        $SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
        $USER    = &$_SESSION['USER'];
    }
    else {
        $SESSION = NULL;
        $USER    = NULL;
    }

    if (defined('FULLME')) {     // Usually in command-line scripts like admin/cron.php
        $FULLME = FULLME;
        $ME = FULLME;
    } else {
        $FULLME = qualified_me();
        $ME = strip_querystring($FULLME);
    }

/// In VERY rare cases old PHP server bugs (it has been found on PHP 4.1.2 running
/// as a CGI under IIS on Windows) may require that you uncomment the following:
//  session_register("USER");
//  session_register("SESSION");



/// Load up theme variables (colours etc)

    if (!isset($CFG->themedir)) {
        $CFG->themedir = $CFG->dirroot.'/theme/';
        $CFG->themewww = $CFG->wwwroot.'/theme/';
    }

    if (isset($_GET['theme'])) {
        if ($CFG->allowthemechangeonurl || confirm_sesskey()) {
            if (!detect_munged_arguments($_GET['theme'], 0) and file_exists($CFG->themedir. $_GET['theme'])) {
                $SESSION->theme = $_GET['theme'];
            }
        }
    }

    if (!isset($CFG->theme)) {
        $CFG->theme = 'standardwhite';
    }

    theme_setup();  // Sets up theme global variables

/// now do a session test to prevent random user switching
    if ($SESSION != NULL) {
        if (empty($_COOKIE['MoodleSessionTest'.$CFG->sessioncookie])) {
            report_session_error();
        } else if (isset($SESSION->session_test) && $_COOKIE['MoodleSessionTest'.$CFG->sessioncookie] != $SESSION->session_test) {
            report_session_error();
        }
    }



/// Set language/locale of printed times.  If user has chosen a language that
/// that is different from the site language, then use the locale specified
/// in the language file.  Otherwise, if the admin hasn't specified a locale
/// then use the one from the default language.  Otherwise (and this is the
/// majority of cases), use the stored locale specified by admin.

    if (isset($_GET['lang'])) {
        if (!detect_munged_arguments($lang, 0) and file_exists($CFG->dirroot .'/lang/'. $lang)) {
            $SESSION->lang = $lang;
            $SESSION->encoding = get_string('thischarset');
        }
    }
    if (empty($CFG->lang)) {
        $CFG->lang = "en";
    }

    moodle_setlocale();

    if (!empty($CFG->opentogoogle)) {
        if (empty($_SESSION['USER'])) {  // Ignore anyone logged in
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Yahoo! Slurp') !== false ) {
                    $USER = guest_user();
                } else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSNBOT') !== false ) {
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
        }
    }

    if ($CFG->theme == 'standard' or $CFG->theme == 'standardwhite') {    // Temporary measure to help with XHTML validation
        if (empty($_SESSION['USER']->id)) {      // Allow W3CValidator in as user called w3cvalidator (or guest)
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
    if ($USER && function_exists('apache_note') && !empty($CFG->apacheloguser)) {
        $apachelog_username = clean_filename($USER->username);
        $apachelog_name = clean_filename($USER->firstname. " ".$USER->lastname);
        $apachelog_userid = $USER->id;
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

/***
 *** init_performance_info() {
 ***
 *** Initializes our performance info early.
 *** 
 *** Pairs up with get_performance_info() which is actually
 *** in moodlelib.php. This function is here so that we can 
 *** call it before all the libs are pulled in. 
 ***
 **/
function init_performance_info() {

    global $PERF;

    $PERF = new Object;
    $PERF->dbqueries = 0;   
    $PERF->logwrites = 0;
    if (function_exists('microtime')) {
        $PERF->starttime = microtime();
        }
    if (function_exists('memory_get_usage')) {
        $PERF->startmemory = memory_get_usage();
    }
    if (function_exists('posix_times')) {
        $PERF->startposixtimes = posix_times();  
    }
}

?>
