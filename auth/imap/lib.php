<?PHP  // $Id$
       // Authentication by looking up an IMAP server

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    switch ($CFG->auth_imaptype) {
        case "imapssl":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/ssl}";
        break;

        case "imapcert":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/ssl/novalidate-cert}";
        break;

        case "imaptls":
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport/imap/notls}";
        break;

        default:
            $host = "{".$CFG->auth_imaphost.":$CFG->auth_imapport}";
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
