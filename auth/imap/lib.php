<?PHP  // $Id$
       // Authentication by looking up an IMAP server

// This code is completely untested so far - I'm just jotting down ideas ...
// Looks like it should work though ...

$CFG->auth_imaphost   = "127.0.0.1";  // Should be IP number
$CFG->auth_imaptype   = "imap";       // imap, imapssl, imapcert, pop3, pop3cert, nntp
$CFG->auth_imapport   = "143";        // 143, 993, 100, 119


function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    switch ($CFG->auth_imaptype) {
        case "imap":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport}INBOX";
        break;
        case "imapssl":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport/imap/ssl}INBOX";
        break;
        case "imapcert":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport/imap/ssl/novalidate-cert}INBOX";
        break;
        case "pop3":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport/pop3}INBOX";
        break;
        case "pop3cert":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport/pop3/ssl/novalidate-cert}INBOX";
        break;
        case "nntp":
            $host = "{$CFG->auth_imaphost:$CFG->auth_imapport/nntp}";
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
