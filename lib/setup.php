<?PHP // $Id$
//
// setup.php
// 
// Sets up sessions, connects to databases and so on
//
// Normally this is only called by the main config.php file 
// 
// Normally this file does not need to be edited.
//
//////////////////////////////////////////////////////////////

    if (!isset($CFG->wwwroot)) {
        die;
    }

/// If there are any errors in the standard libraries we want to know!
    error_reporting(E_ALL);

/// Connect to the database using adodb

    $CFG->libdir   = "$CFG->dirroot/lib";

    require_once("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions

    $db = &ADONewConnection($CFG->dbtype);         

    error_reporting(0);  // Hide errors 

    if (!isset($CFG->dbpersist) or !empty($CFG->dbpersist)) {    // Use persistent connection (default)
        $dbconnected = $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    } else {                                                     // Use single connection
        $dbconnected = $db->Connect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname);
    }
    if (! $dbconnected) {
        echo "<font color=\"#990000\">";
        echo "<p>Error: Moodle could not connect to the database.</p>";
        echo "<p>It's possible the database itself is just not working at the moment.</p>";
        echo "<p>The admin should 
                 also check that the database details have been correctly specified in config.php</p>";
        echo "<p>Database host: $CFG->dbhost<br />";
        echo "Database name: $CFG->dbname<br />";
        echo "Database user: $CFG->dbuser<br />";
        if (!isset($CFG->dbpersist)) {
            echo "<p>The admin should also try setting this in config.php:  $"."CFG->dbpersist = false; </p>";
        }
        echo "</font>";
        die;
    }

    error_reporting(E_ALL);       // Show errors from now on.

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = "";
    }


/// Define admin directory

    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = 'admin';   // This is relative to the wwwroot and dirroot
    }


/// Load up standard libraries 

    require_once("$CFG->libdir/weblib.php");          // Functions for producing HTML
    require_once("$CFG->libdir/datalib.php");         // Functions for accessing databases
    require_once("$CFG->libdir/moodlelib.php");       // Other general-purpose functions


/// Load up any configuration from the config table
    
    if ($configs = get_records('config')) {
        $CFG = (array)$CFG;
        foreach ($configs as $config) {
            $CFG[$config->name] = $config->value;
        }
        $CFG = (object)$CFG;
        unset($configs);
        unset($config);
    }


/// Set error reporting back to normal
    if (empty($CFG->debug)) {
        $CFG->debug = 7;
    }
    error_reporting($CFG->debug);


/// File permissions on created directories in the $CFG->dataroot

    if (empty($CFG->directorypermissions)) {
        $CFG->directorypermissions = 0777;      // Must be octal (that's why it's here)
    }


/// Set session timeouts
    if (!empty($CFG->sessiontimeout)) {
        ini_set('session.gc_maxlifetime', $CFG->sessiontimeout);
    }

/// Set custom session path
    if (!file_exists("$CFG->dataroot/sessions")) {
        make_upload_directory('sessions');
    }
    ini_set('session.save_path', "$CFG->dataroot/sessions");

/// Set sessioncookie variable if it isn't already
    if (!isset($CFG->sessioncookie)) {
        $CFG->sessioncookie = '';
    }

/// Location of standard files

    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->moddata     = 'moddata';


/// Load up theme variables (colours etc)

    if (!isset($CFG->theme)) {
        $CFG->theme = 'standard';
    }
    include("$CFG->dirroot/theme/$CFG->theme/config.php");

    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.php";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";

    if (empty($THEME->custompix)) {
        $CFG->pixpath = "$CFG->wwwroot/pix";
        $CFG->modpixpath = "$CFG->wwwroot/mod";
    } else {
        $CFG->pixpath = "$CFG->wwwroot/theme/$CFG->theme/pix";
        $CFG->modpixpath = "$CFG->wwwroot/theme/$CFG->theme/pix/mod";
    }


/// A hack to get around magic_quotes_gpc being turned off

    if (!ini_get_bool('magic_quotes_gpc') ) {
        foreach ($_GET as $key => $var) {
            if (!is_array($var)) {
                $_GET[$key] = addslashes($var);
            } else {
                foreach ($var as $arrkey => $arrvar) {
                    $var[$arrkey] = addslashes($arrvar);
                }
                $_GET[$key] = $var;
            }
        }
        foreach ($_POST as $key => $var) {
            if (!is_array($var)) {
                $_POST[$key] = addslashes($var);
            } else {
                foreach ($var as $arrkey => $arrvar) {
                    $var[$arrkey] = addslashes($arrvar);
                }
                $_POST[$key] = $var;
            }
        }
    }


/// The following is a hack to get around the problem of PHP installations
/// that have "register_globals" turned off (default since PHP 4.1.0).
/// Eventually I'll go through and upgrade all the code to make this unnecessary

    if (isset($_GET)) {
        extract($_GET, EXTR_SKIP);    // Skip existing variables, ie CFG
    }
    if (isset($_POST)) {
        extract($_POST, EXTR_SKIP);   // Skip existing variables, ie CFG
    }
    if (isset($_SERVER)) { 
        extract($_SERVER);
    }

    
/// Load up global environment variables

    class object {};
    
    if (!isset($nomoodlecookie)) {
        session_name('MoodleSession'.$CFG->sessioncookie);
        @session_start();
        if (! isset($_SESSION['SESSION'])) { 
            $_SESSION['SESSION'] = new object; 
        }
        if (! isset($_SESSION['USER']))    { 
            $_SESSION['USER']    = new object; 
        }
    
        $SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
        $USER    = &$_SESSION['USER'];
    }

    if (isset($FULLME)) {
        $ME = $FULLME;
    } else {
        $FULLME = qualified_me();
        $ME = strip_querystring($FULLME);
    }

/// In VERY rare cases old PHP server bugs (it has been found on PHP 4.1.2 running 
/// as a CGI under IIS on Windows) may require that you uncomment the following:
//  session_register("USER");
//  session_register("SESSION");


/// Set language/locale of printed times.  If user has chosen a language that 
/// that is different from the site language, then use the locale specified 
/// in the language file.  Otherwise, if the admin hasn't specified a locale
/// then use the one from the default language.  Otherwise (and this is the 
/// majority of cases), use the stored locale specified by admin.

    if (isset($_GET['lang'])) {
        if (!detect_munged_arguments($lang) and file_exists("$CFG->dirroot/lang/$lang")) {
            $SESSION->lang = $lang;
            $SESSION->encoding = get_string('thischarset');
        }
    }
    if (empty($CFG->lang)) {
        $CFG->lang = "en";
    }

    moodle_setlocale();

    if (!empty($CFG->opentogoogle)) {
        if (empty($_SESSION['USER'])) {
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot') !== false ) {
                    $USER = guest_user();
                }
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'google.com') !== false ) {
                    $USER = guest_user();
                }
            }
            if (empty($_SESSION['USER']) and !empty($_SERVER['HTTP_REFERER'])) {
                if (strpos($_SERVER['HTTP_REFERER'], 'google') !== false ) {
                    $USER = guest_user();
                }
            }
        }
    }

?>
