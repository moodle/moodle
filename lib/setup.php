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
// XXX this might need some rationalisation
//
//////////////////////////////////////////////////////////////

// Error reporting and bug hunting

    error_reporting(7);   // use 0=none 7=normal 15=all 

// Load up standard libraries 

    require("$CFG->libdir/weblib.php");          // Standard web page functions
    require("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions
    require("$CFG->libdir/adodb/tohtml.inc.php");// Database display functions
    require("$CFG->libdir/moodlelib.php");       // Various Moodle functions

// Connect to the database using adodb

    ADOLoadCode($CFG->dbtype);          
    $db = &ADONewConnection();         
    $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname); 

// Default editing time for posts and the like (in seconds)

    $CFG->maxeditingtime = 1800;

// Load up any configuration from the config table
    
    if (!$CFG->theme = get_field("config", "value", "name", "theme")) {
        $theme->name  = "theme";
        $theme->value = $CFG->theme = "standard";
        insert_record("config", $theme);
    }

// Location of standard files

    $CFG->templatedir = "$CFG->dirroot/templates";
    $CFG->imagedir    = "$CFG->wwwroot/images";
    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.css";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";

// Load up theme variables (colours etc)

    require("$CFG->dirroot/theme/$CFG->theme/config.php");

// Set language/locale of printed times (must be supported by OS)

    if ($CFG->locale) {
        setlocale ("LC_TIME", $CFG->locale);
    } else {
        setlocale ("LC_TIME", $CFG->lang);
    }

// Check that PHP is of a sufficient version

    if ( ! check_php_version("4.1.0") ) {
        $version = phpversion();
        print_heading("Sorry, Moodle requires PHP 4.1.0 or later (currently using version $version)");
        die;
    }

// The following is a big hack to get around the problem of PHP installations
// that have "register_globals" turned off (default since PHP 4.1.0).
// Eventually I'll go through and upgrade all the code to make this unnecessary

   if (isset($_REQUEST)) {
       extract($_REQUEST);
   }
   if (isset($_SERVER)) { 
       extract($_SERVER);
   }
    
// Load up global environment variables

    class object {};
    
    session_start();
    if (! isset($_SESSION["SESSION"])) { $_SESSION["SESSION"] = new object; }
    if (! isset($_SESSION["USER"]))    { $_SESSION["USER"]    = new object; }
    extract($_SESSION);  // Makes $SESSION and $USER available for read-only access

    $FULLME = qualified_me();
    $ME     = strip_querystring($FULLME);


?>
