<?PHP // $Id$
///////////////////////////////////////////////////////////////////////////
//
// Moodle configuration file
//
///////////////////////////////////////////////////////////////////////////

// Site configuration variables are all stored in the CFG object.

// First, we need to configure the database where all Moodle data 
// will be stored.  This database must already have been created
// and a username/password created to access it.   See INSTALL doc.

$CFG->dbtype    = "mysql";       // eg mysql, postgres, oracle, access etc 
$CFG->dbhost    = "localhost";   // eg localhost 
$CFG->dbname    = "moodle";      // eg moodle
$CFG->dbuser    = "username";
$CFG->dbpass    = "password";


// Next you need to tell Moodle where it is, and where it can save files.

$CFG->wwwroot   = "http://example.com/moodle";
$CFG->dirroot   = "/web/moodle";
$CFG->dataroot  = "/home/moodledata";    // Web-server writeable


// Choose a theme from the "themes" folder.  Default theme is "standard".

$CFG->theme     = "standard";


// Give the full name (eg mail.example.com) of an SMTP server that the 
// web server machine has access to (to send mail).  Default: "localhost".

$CFG->smtphost  = "mail.example.com";


// You should not need to change anything below this line
///////////////////////////////////////////////////////////////////////////

$CFG->libdir    = "$CFG->dirroot/lib";

require("$CFG->libdir/setup.php");  // Sets up all libraries, sessions etc

?>
