<?PHP  // $Id$
       // Authentication by looking up an NNTP server


function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    $host = "{".$CFG->auth_nntphost.":$CFG->auth_nntpport/nntp}";

    error_reporting(0);
    $connection = imap_open($host, $username, $password, OP_HALFOPEN);
    error_reporting($CFG->debug);   

    if ($connection) {
        imap_close($connection);
        return true;

    } else {
        return false;
    }
}


?>
