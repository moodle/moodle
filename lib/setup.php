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

/// Set error reporting to whatever the admin has said they want

    if (isset($CFG->errorlevel)) {
        error_reporting((int)$CFG->errorlevel);   
    } else {
        error_reporting(7);   
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

/// Set language/locale of printed times (must be supported by OS)

    if (! setlocale ("LC_TIME", $CFG->locale)) {
        setlocale ("LC_TIME", $CFG->lang);        // Might work
    }

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
