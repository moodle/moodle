<?PHP  // $Id$
       // Authentication by looking up a POP3 server

// This code is completely untested so far  -  IT NEEDS TESTERS!
// Looks like it should work though ...

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    switch ($CFG->auth_pop3type) {
        case "pop3":
            $host = "{".$CFG->auth_pop3host.":$CFG->auth_pop3port/pop3}INBOX";
        break;
        case "pop3cert":
            $host = "{".$CFG->auth_pop3host.":$CFG->auth_pop3port/pop3/ssl/novalidate-cert}INBOX";
        break;
    }

    error_reporting(0);
    $connection = imap_open($host, $username, $password, OP_HALFOPEN);
    error_reporting(7);   

    if ($connection) {
        imap_close($connection);
        return true;

    } else {
        return false;
    }
}


?>
