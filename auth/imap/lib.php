<?PHP  // $Id$
       // Authentication by looking up an IMAP server

// This code is completely untested so far  -  IT NEEDS TESTERS!
// Looks like it should work though ...

$CFG->auth_imaphost   = "127.0.0.1";  // Should be IP number
$CFG->auth_imaptype   = "imap";       // imap, imaptls, imapssl, imapcert
$CFG->auth_imapport   = "143";        // 143, 993
$CFG->auth_instructions = "Use the same username and password as your school email account";   // Instructions


function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    switch ($CFG->auth_imaptype) {
        case "imapssl":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/ssl}INBOX";
        break;

        case "imapcert":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/ssl/novalidate-cert}INBOX";
        break;

        case "imaptls":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/notls}INBOX";
        break;

        default:
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport}";
    }

    if ($connection = imap_open($host, $username, $password, OP_HALFOPEN)) {
        imap_close($connection);
        return true;

    } else {
        return false;
    }
}


?>
