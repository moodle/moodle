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

/// If there are any errors in the standard libraries we want to know!
    error_reporting(E_ALL);

/// Connect to the database using adodb

    $CFG->libdir   = "$CFG->dirroot/lib";

    require_once("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions

    $db = &ADONewConnection($CFG->dbtype);         

    if (! $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname)) {
        echo "<P><FONT COLOR=RED>The database details specified in config.php are not correct, or the database is down.</P>";
        die;
    }

    if (!isset($CFG->prefix)) {   // Just in case it isn't defined in config.php
        $CFG->prefix = "";
    }
    //$CFG->prefix = "$CFG->dbname.$CFG->prefix";


/// Define admin directory

    if (!isset($CFG->admin)) {   // Just in case it isn't defined in config.php
        $CFG->admin = "admin";   // This is relative to the wwwroot and dirroot
    }


/// Load up standard libraries 

    require_once("$CFG->libdir/weblib.php");          // Functions for producing HTML
    require_once("$CFG->libdir/datalib.php");         // Functions for accessing databases
    require_once("$CFG->libdir/moodlelib.php");       // Other general-purpose functions


/// Load up any configuration from the config table
    
    if ($configs = get_records("config")) {
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
        ini_set("session.gc_maxlifetime", $CFG->sessiontimeout);
    }


/// Location of standard files

    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->moddata     = "moddata";


/// Load up theme variables (colours etc)

    if (!isset($CFG->theme)) {
        $CFG->theme = "standard";
    }
    include("$CFG->dirroot/theme/$CFG->theme/config.php");

    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.php";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";



/// Reference code to remove magic quotes from everything ... just in case.
/// If you have problems with slashes everywhere then you might want to 
/// uncomment this code.  It will not be necessary on 99.9% of PHP servers.
/// Got this from http://www.php.net/manual/en/configuration.php
//    if (ini_get("magic_quotes_gpc") ) {
//        foreach ($GLOBALS["HTTP_".$GLOBALS["REQUEST_METHOD"]."_VARS"] as $key => $value) {
//            if (!is_array($value)) { // Simple value
//                $newval = stripslashes($value);
//                $GLOBALS["HTTP_".$GLOBALS["REQUEST_METHOD"]."_VARS"][$key] = $newval;
//                if (ini_get("register_globals")) {
//                    $GLOBALS[$key] = $newval;
//                }
//            } else {  // Array
//                foreach ($value as $k => $v) {
//                    $newval = stripslashes($v);
//                    $GLOBALS["HTTP_".$GLOBALS["REQUEST_METHOD"]."_VARS"][$key][$k] = $newval;
//                    if (ini_get("register_globals")) {
//                        $GLOBALS[$key][$k] = $newval;
//                    }
//                }
//            }
//        }
//    }

/// The following is a hack to get around the problem of PHP installations
/// that have "register_globals" turned off (default since PHP 4.1.0).
/// Eventually I'll go through and upgrade all the code to make this unnecessary

    if (isset($_REQUEST)) {
        extract($_REQUEST);
    }
    if (isset($_SERVER)) { 
        extract($_SERVER);
    }

    
/// Load up global environment variables

    class object {};
    
    @session_start();
    if (! isset($_SESSION['SESSION'])) { 
        $_SESSION['SESSION'] = new object; 
    }
    if (! isset($_SESSION['USER']))    { 
        $_SESSION['USER']    = new object; 
    }

    $SESSION = &$_SESSION['SESSION'];   // Makes them easier to reference
    $USER    = &$_SESSION['USER'];

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

    if (isset($lang)) {
        $SESSION->lang = $lang;
    }

    if (!empty($SESSION->lang) and ($SESSION->lang != $CFG->lang) ) {
        $CFG->locale = get_string("locale");
    } else if (!empty($USER->lang) and ($USER->lang != $CFG->lang) ) {
        $CFG->locale = get_string("locale");
    } else if (empty($CFG->locale)) {
        $CFG->locale = get_string("locale");
        set_config("locale", $CFG->locale);   // cache it to save lookups in future
    }
    setlocale (LC_TIME, $CFG->locale);
    setlocale (LC_CTYPE, $CFG->locale);
    setlocale (LC_COLLATE, $CFG->locale);

?>
