<?PHP  // $Id$
       // Authentication by looking up a POP3 server

// This code is completely untested so far  -  IT NEEDS TESTERS!
// Looks like it should work though ...

$CFG->auth_pop3host   = "127.0.0.1";  // Should be IP number
$CFG->auth_pop3type   = "pop3";       // pop3, pop3cert
$CFG->auth_pop3port   = "110";        // 110 is most common
$CFG->auth_instructions = "Use the same username and password as your school email account";   // Instructions


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

    if ($connection = imap_open($host, $username, $password, OP_HALFOPEN)) {
        imap_close($connection);
        return true;

    } else {
        return false;
    }
}


?>
