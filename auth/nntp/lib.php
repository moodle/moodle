<?PHP  // $Id$
       // Authentication by looking up an NNTP server

// This code is completely untested so far  -  IT NEEDS TESTERS!
// Looks like it should work though ...

$CFG->auth_nntphost   = "127.0.0.1";  // Should be IP number
$CFG->auth_nntpport   = "119";        // 119 is most common
$CFG->auth_instructions = "Use the same username and password as your school news account";   // Instructions


function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    $host = "{".$CFG->auth_nntphost.":$CFG->auth_nntpport/nntp}";

    if ($connection = imap_open($host, $username, $password, OP_HALFOPEN)) {
        imap_close($connection);
        return true;

    } else {
        return false;
    }
}


?>
