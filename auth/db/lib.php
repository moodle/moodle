<?PHP  // $Id$
       // Authentication by looking up an external database table

// This code is completely untested so far - I'm just jotting down ideas ...
// Looks like it should work though ...

$CFG->authdbhost   = "localhost";
$CFG->authdbtype   = "mysql";     // (postgresql, etc)
$CFG->authdbname   = "authtest";
$CFG->authdbtable  = "users";
$CFG->authdbuser   = "user";
$CFG->authdbpass   = "pass";
$CFG->authdbfielduser   = "user";
$CFG->authdbfieldpass   = "pass";

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    ADOLoadCode($CFG->authdbtype);          
    $authdb = &ADONewConnection();         
    $authdb->PConnect($CFG->authdbhost,$CFG->authdbuser,$CFG->authdbpass,$CFG->authdbname); 


    $rs = $authdb->Execute("SELECT * FROM $CFG->authdbtable 
                            WHERE $CFG->authdbfielduser = '$username' 
                              AND $CFG->authdbfieldpass = '$password' ");
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
