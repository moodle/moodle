<?PHP  // $Id$
       // Authentication by looking up an external database table

// This code is completely untested so far - IT NEEDS TESTERS!
// Looks like it should work though ...

$CFG->auth_dbhost   = "localhost";
$CFG->auth_dbtype   = "mysql";     // (postgresql, etc)
$CFG->auth_dbname   = "authtest";
$CFG->auth_dbtable  = "users";
$CFG->auth_dbuser   = "user";
$CFG->auth_dbpass   = "pass";
$CFG->auth_dbfielduser   = "user";
$CFG->auth_dbfieldpass   = "pass";

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    ADOLoadCode($CFG->auth_dbtype);          
    $authdb = &ADONewConnection();         
    $authdb->PConnect($CFG->auth_dbhost,$CFG->auth_dbuser,$CFG->auth_dbpass,$CFG->auth_dbname); 


    $rs = $authdb->Execute("SELECT * FROM $CFG->auth_dbtable 
                            WHERE $CFG->auth_dbfielduser = '$username' 
                              AND $CFG->auth_dbfieldpass = '$password' ");
    if (!$rs) {
        notify("Could not connect to the specified authentication database...");
        return false;
    }

    if ( $rs->RecordCount() ) {
        return true;
    } else {
        return false;
    }
}


?>
