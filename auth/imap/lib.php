<?PHP  // $Id$
       // Authentication by looking up an IMAP server

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    $hosts = split(';', $CFG->auth_imaphost);   // Could be multiple hosts

    foreach ($hosts as $host) {                 // Try each host in turn

        $host = trim($host);

        switch ($CFG->auth_imaptype) {
            case "imapssl":
                $host = '{'.$host.":$CFG->auth_imapport/imap/ssl}";
            break;
    
            case "imapcert":
                $host = '{'.$host.":$CFG->auth_imapport/imap/ssl/novalidate-cert}";
            break;
    
            case "imaptls":
                $host = '{'.$host.":$CFG->auth_imapport/imap/notls}";
            break;
    
            default:
                $host = '{'.$host.":$CFG->auth_imapport}";
        }

        error_reporting(0);
        $connection = imap_open($host, $username, $password, OP_HALFOPEN);
        error_reporting($CFG->debug);   

        if ($connection) {
            imap_close($connection);
            return true;
        }
    }

    return false;  // No match
}


?>
