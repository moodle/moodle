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

// Moodle overall version

    $CFG->moodleversion = "1.0";

// Default editing time for discussions and the like (in seconds)

    $CFG->maxeditingtime = 1800;

// Location of standard files

    $CFG->templatedir = "$CFG->dirroot/templates";
    $CFG->imagedir    = "$CFG->wwwroot/images";
    $CFG->wordlist    = "$CFG->libdir/wordlist.txt";
    $CFG->javascript  = "$CFG->libdir/javascript.php";
    $CFG->stylesheet  = "$CFG->wwwroot/theme/$CFG->theme/styles.css";
    $CFG->header      = "$CFG->dirroot/theme/$CFG->theme/header.html";
    $CFG->footer      = "$CFG->dirroot/theme/$CFG->theme/footer.html";

// Set language/locale of printed times (must be supported by OS)

    if ($CFG->locale) {
        setlocale ("LC_TIME", $CFG->locale);
    } else {
        setlocale ("LC_TIME", $CFG->lang);
    }

// Load up theme variables (colours etc)

    require("$CFG->dirroot/theme/$CFG->theme/config.php");


// Load up standard libraries 

    require("$CFG->libdir/weblib.php");          // Standard web page functions
    require("$CFG->libdir/adodb/adodb.inc.php"); // Database access functions
    require("$CFG->libdir/adodb/tohtml.inc.php");// Database display functions
    require("$CFG->libdir/moodlelib.php");       // Various Moodle functions
    
// Load up global environment variables

    class object {};
    
    session_start();
    session_register("SESSION");    // Current session info 
    session_register("USER");       // Current user info
    if (! isset($SESSION)) $SESSION = new object;
    if (! isset($USER))    $USER = new object;

    $FULLME = qualified_me();
    $ME     = strip_querystring($FULLME);

// Connect to the database using adodb

    ADOLoadCode($CFG->dbtype);          
    $db = &ADONewConnection();         
    $db->PConnect($CFG->dbhost,$CFG->dbuser,$CFG->dbpass,$CFG->dbname); 



?>
