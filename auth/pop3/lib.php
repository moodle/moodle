<?PHP  // $Id$
       // Authentication by looking up a POP3 server

function auth_user_login ($username, $password) {
// Returns true if the username and password work
// and false if they are wrong or don't exist.

    global $CFG;

    $hosts = split(';', $CFG->auth_pop3host);   // Could be multiple hosts

    foreach ($hosts as $host) {                 // Try each host in turn

        $host = trim($host);

        switch ($CFG->auth_pop3type) {
            case "pop3":
                $host = '{'.$host.":$CFG->auth_pop3port/pop3}INBOX";
            break;
            case "pop3notls":
                $host = '{'.$host.":$CFG->auth_pop3port/pop3/notls}INBOX";
            break;
            case "pop3cert":
                $host = '{'.$host.":$CFG->auth_pop3port/pop3/ssl/novalidate-cert}INBOX";
            break;
        }

        error_reporting(0);
        $connection = imap_open($host, $username, $password, OP_HALFOPEN);
        error_reporting($CFG->debug);   
 
        if ($connection) {
            imap_close($connection);
            return true;
        }
    }

    return false;  // No matches found
}


?>
