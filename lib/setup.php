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
    error_reporting(15);   // use 0=none 7=normal 15=all 

/// Load up standard libraries 

    require("$CFG->libdir/weblib.php");          // Standard web page functions
    require("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions
    require("$CFG->libdir/adodb/tohtml.inc.php");// Database display functions
    require("$CFG->libdir/moodlelib.php");       // Various Moodle functions

/// Connect to the database using adodb

    ADOLoadCode($CFG->dbtype);          
    $db = &ADONewConnection();         
    $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname); 

/// Set error reporting back to normal
    error_reporting(7);   


/// Load up any configuration from the config table
    
    if ($configs = get_records_sql("SELECT * FROM config")) {
        $CFG = (array)$CFG;
        foreach ($configs as $config) {
            $CFG[$config->name] = $config->value;
        }
        $CFG = (object)$CFG;
        unset($configs);
        unset($config);
    }


/// Location of standard files

    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.css";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";
    $CFG->moddata     = "moddata";


/// Load up theme variables (colours etc)

    if (!isset($CFG->theme)) {
        $CFG->theme = "standard";
    }
    require("$CFG->dirroot/theme/$CFG->theme/config.php");


/// Set language/locale of printed times etc (must be supported by OS)

    if (! setlocale ("LC_ALL", $CFG->locale)) {
        setlocale ("LC_ALL", $CFG->lang);        // Might work
    }

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
    
    session_start();
    if (! isset($_SESSION["SESSION"])) { $_SESSION["SESSION"] = new object; }
    if (! isset($_SESSION["USER"]))    { $_SESSION["USER"]    = new object; }
    extract($_SESSION);  // Makes $SESSION and $USER available for read-only access

    $FULLME = qualified_me();
    $ME     = strip_querystring($FULLME);


?>
