#!./php -q
<?php

echo "Moodle chat daemon v1.0\n\n";



/// Set up all the variables we need   /////////////////////////////////////

/// $CFG variables are now defined in database by chat/lib.php

$_SERVER['PHP_SELF'] = "dummy";
$_SERVER['SERVER_NAME'] = "dummy";

include('../../config.php');
include('lib.php');

$_SERVER['SERVER_NAME'] = $CFG->chat_serverhost;
$_SERVER['PHP_SELF'] = "http://$CFG->chat_serverhost:$CFG->chat_serverport/mod/chat/chatd.php";

$safemode = ini_get('safe_mode');

if(!empty($safemode)) {
    die("Error: Cannot run with PHP safe_mode = On. Turn off safe_mode.\n");
}

@set_time_limit (0);
set_magic_quotes_runtime(0);

error_reporting(E_ALL);

function chat_empty_connection() {
    return array('sid' => NULL, 'handle' => NULL, 'ip' => NULL, 'port' => NULL, 'groupid' => NULL);
}

class ChatConnection {
    var $sid = NULL;
    var $handle = NULL;
    var $ip = NULL;
    var $port = NULL;
    var $groupid = NULL;
    var $lastmessages = array();
    var $lastmsgindex = 0;
    var $type = NULL;
}

class ChatMessage {
    var $chatid     = NULL;
    var $userid     = NULL;
    var $groupid    = NULL;
    var $system     = NULL;
    var $message    = NULL;
    var $timestamp  = NULL;

    var $text_ = '';
    var $html_ = '';
    var $beep_ = false;

}

class ChatDaemon {
    var $conn_ufo = array();     // Connections not identified yet
    var $conn_side = array();    // Sessions with sidekicks waiting for the main connection to be processed
    var $conn_half = array();    // Sessions that have valid connections but not all of them
    var $conn_sets = array();    // Sessions with complete connection sets sets
    var $sets_info = array();    // Keyed by sessionid exactly like conn_sets, one of these for each of those

    var $message_queue = array(); // Holds messages that we haven't committed to the DB yet

    function new_ufo_id() {
        static $id = 0;
        if($id++ === 0x1000000) { // Cycling very very slowly to prevent overflow
            $id = 0;
        }
        return $id;
    }

    function process_sidekicks($sessionid) {
        if(empty($this->conn_side[$sessionid])) {
            return true;
        }
        foreach($this->conn_side[$sessionid] as $sideid => $sidekick) {
            $this->dispatch_sidekick($sidekick['handle'], $sidekick['type'], $sessionid, $sidekick['customdata']);
            unset($this->conn_side[$sessionid][$sideid]);
        }
        return true;
    }

    function dispatch_sidekick($handle, $type, $sessionid, $customdata) {
        global $CFG;

        switch($type) {
            case CHAT_SIDEKICK_USERS:
                $x = pusers($this->sets_info[$sessionid]['chat'], $this->sets_info[$sessionid]['groupid']);
                //$x = "<html>Lalalala! ".time()."</html>";

                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "Refresh: 3; url=http://$CFG->chat_serverhost:$CFG->chat_serverport?win=users&".
                           "chat_sid=".$sessionid."&groupid=".$this->sets_info[$sessionid]['groupid']."\n";
                $header .= "\n";

                $x = $header.$x;

                trace('Outputting user list('.strlen($x).' chars)');
                //trace($x);

                chat_socket_write($handle, $x);
            break;
            case CHAT_SIDEKICK_MESSAGE:
                // Incoming message
                $msg = &New ChatMessage;
                $msg->chatid    = $this->sets_info[$sessionid]['chatid'];
                $msg->userid    = $this->sets_info[$sessionid]['userid'];
                $msg->groupid   = $this->sets_info[$sessionid]['groupid'];
                $msg->system    = 0;
                $msg->message   = urldecode($customdata['message']); // have to undo the browser's encoding
                $msg->timestamp = time();

                if(empty($msg->message)) {
                    // Someone just hit ENTER, send them on their way
                    break;
                }

                // Format the message object here
                $output = chat_format_message($msg);

                $msg->message = $output->text; // This is for writing into the DB
                $msg->text_ = $output->text;
                $msg->html_ = $output->html;
                $msg->beep_ = $output->beep;

                // OK, now push it out to all users
                $this->message_broadcast($msg);

                // Put it on the uncommitted queue
                // TODO: error checking!!!
                //$message_queue[] = $msg;
                insert_record('chat_messages', $msg);

                // Update that user's lastmessageping
                // TODO: this can and should be written as a single UPDATE query
                $user = get_record('chat_users', 'sid', $sessionid);
                if($user !== false) {
                    $user->lastmessageping = $msg->timestamp;
                    update_record('chat_users', $user);
                }

                // All done
            break;
        }

        socket_shutdown($handle);
        socket_close($handle);
    }

    function promote_final($sessionid, $groupid) {
        if(isset($this->conn_sets[$sessionid])) {
            trace('Set cannot be finalized: Session '.$sessionid.' is already active');
            return false;
        }

        $chatuser = get_record('chat_users', 'sid', $sessionid);
        if($chatuser === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        $chat = get_record('chat', 'id', $chatuser->chatid);
        if($chat === false) {
            $this->dismiss_half($sessionid);
            return false;
        }

        global $CHAT_HTMLHEAD_JS;

        $this->conn_sets[$sessionid] = $this->conn_half[$sessionid];
        $this->sets_info[$sessionid] = array('chatid' => $chatuser->chatid, 'userid' => $chatuser->userid, 'groupid' => $groupid, 'lastmessages' => array(), 'lastmsgindex' => 0);
        $this->dismiss_half($sessionid, false);
        chat_socket_write($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $CHAT_HTMLHEAD_JS);
        trace('Finalized client: sid: '.$sessionid.' uid: '.$chatuser->userid.' gid: '.intval($groupid));

        // Finally, broadcast the "entered the chat" message

        $msg = &New ChatMessage;
        $msg->chatid = $chatuser->chatid;
        $msg->userid = $chatuser->userid;
        $msg->groupid = 0;
        $msg->system = 1;
        $msg->message = 'enter';
        $msg->timestamp = time();

        insert_record('chat_messages', $msg);

        // Format the message object here
        $output = chat_format_message($msg);

        $msg->message = $output->text; // This is for writing into the DB
        $msg->text_ = $output->text;
        $msg->html_ = $output->html;
        $msg->beep_ = $output->beep;

        $this->message_broadcast($msg);

        return true;
    }

    function promote_ufo($handle, $type, $sessionid, $groupid, $customdata) {
        if(empty($this->conn_ufo)) {
            return false;
        }
        foreach($this->conn_ufo as $id => $ufo) {
            if($ufo == $handle) {
                // OK, got the id of the UFO, but what is it?

                if($type & CHAT_SIDEKICK) {
                    // Is the main connection ready?
                    if(isset($this->conn_sets[$sessionid])) {
                        // Yes, so dispatch this sidekick now and be done with it
                        trace('Dispatching sidekick immediately');
                        $this->dispatch_sidekick($handle, $type, $sessionid, $customdata);
                        $this->dismiss_ufo($handle, false);
                    }
                    else {
                        // No, so put it in the waiting list
                        trace('sidekick waiting');
                        $this->conn_side[$sessionid][] = array('type' => $type, 'handle' => $handle, 'customdata' => $customdata);
                    }
                    return true;
                }

                // If it's not a sidekick, at this point it can only be da man

                if($type & CHAT_CONNECTION) {
                    // This forces a new connection right now...

                    // Do we have such a connection active?
                    if(isset($this->conn_sets[$sessionid])) {
                        // Yes, so regrettably we cannot promote you
                        trace('UFO #'.$id.' with '.$ufo.' cannot be promoted: session '.$sessionid.' is already final');
                        $this->dismiss_ufo($handle);
                        return false;
                    }

                    // Join this with what we may have already
                    $this->conn_half[$sessionid][$type] = $handle;

                    // Do the bookkeeping
                    $this->promote_final($sessionid, $groupid);

                    // It's not an UFO anymore
                    $this->dismiss_ufo($handle, false);

                    // Dispatch waiting sidekicks
                    $this->process_sidekicks($sessionid);

                    return true;
                }

                /*
                // It's the first one for that sessionid, so it will start an incomplete connection
                $this->conn_half[$sessionid] = array($type => $handle);
                unset($this->conn_ufo[$id]);
                trace('UFO #'.$id.': identified session '.$sessionid.' wintype '.$type);
                return true;
                */
            }
        }
        return false;
    }

    function dismiss_half($sessionid, $disconnect = true) {
        if(!isset($this->conn_half[$sessionid])) {
            return false;
        }
        if($disconnect) {
            foreach($this->conn_half[$sessionid] as $handle) {
                socket_shutdown($handle);
                socket_close($handle);
            }
        }
        unset($this->conn_half[$sessionid]);
        return true;
    }

    function dismiss_set($sessionid) {
        if(!isset($this->conn_sets[$sessionid])) {
            return false;
        }
        foreach($this->conn_sets[$sessionid] as $handle) {
            socket_shutdown($handle);
            socket_close($handle);
        }
        unset($this->conn_sets[$sessionid]);
        unset($this->sets_info[$sessionid]);
        return true;
    }


    function dismiss_ufo($handle, $disconnect = true) {
        if(empty($this->conn_ufo)) {
            return false;
        }
        foreach($this->conn_ufo as $id => $ufo) {
            if($ufo == $handle) {
                unset($this->conn_ufo[$id]);
                if($disconnect) {
                    chat_socket_write($handle, "You don't seem to be a valid client.\n");
                    socket_shutdown($handle);
                    socket_close($handle);
                }
                return true;
            }
        }
        return false;
    }

    function conn_accept() {
        $handle = @socket_accept($this->listen_socket);
        if(!$handle) {
            return false;
        }

        $newconn = &New ChatConnection;
        $newconn->handle = $handle;
        socket_getpeername($newconn->handle, &$newconn->ip, &$newconn->port);

        $id = $this->new_ufo_id();
        trace('UFO #'.$id.': connection from '.$newconn->ip.' on port '.$newconn->port.', '.$newconn->handle);

        $this->conn_ufo[$id] = $newconn->handle;
    }

    function conn_activity_ufo (&$handles) {
        $monitor = array();
        if(!empty($this->conn_ufo)) {
            foreach($this->conn_ufo as $ufoid => $ufo) {
                $monitor[$ufoid] = $ufo;
            }
        }

        if(empty($monitor)) {
            $handles = array();
            return 0;
        }

        $retval = socket_select($monitor, $a = NULL, $b = NULL, NULL);
        $handles = $monitor;

        return $retval;
    }

    function message_broadcast($message) {
        if(empty($this->conn_sets)) {
            return true;
        }

        //trace('Broadcasting message');
        foreach($this->sets_info as $sessionid => $info) {
            // We need to get handles from users that are in the same chatroom, same group
            if($info['chatid'] == $message->chatid &&
              ($info['groupid'] == $message->groupid || $message->groupid == 0))
            {

                // Simply give them the message

                if(!chat_socket_write($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $message->html_)) {

                    // Send failed! We must now disconnect/forget about the user FIRST
                    // and THEN broadcast a message to all others... otherwise, infinite recursion.

                    delete_records('chat_users', 'sid', $sessionid);
                    $msg = &New ChatMessage;
                    $msg->chatid = $info['chatid'];
                    $msg->userid = $info['userid'];
                    $msg->groupid = 0;
                    $msg->system = 1;
                    $msg->message = 'exit';
                    $msg->timestamp = time();

                    trace('Client socket write failed, destroying uid '.$info['userid'].' with SID '.$sessionid);
                    insert_record('chat_messages', $msg);

                    // Format the message object here
                    $output = chat_format_message($msg);

                    $msg->message = $output->text; // This is for writing into the DB
                    $msg->text_ = $output->text;
                    $msg->html_ = $output->html;
                    $msg->beep_ = $output->beep;

                    // Kill him before broadcasting!!
                    $this->dismiss_set($sessionid);
                    $this->message_broadcast($msg);
                }
                //trace('Sent to UID '.$this->sets_info[$sessionid]['userid'].': '.$message->text_);
            }
        }
    }

    function message_commit() {
    }

}

// Connection telltale
define('CHAT_CONNECTION',           0x10);
// Connections: Incrementing sequence, 0x10 to 0x1f
define('CHAT_CONNECTION_CHANNEL',   0x11);

// Sidekick telltale
define('CHAT_SIDEKICK',             0x20);
// Sidekicks: Incrementing sequence, 0x21 to 0x2f
define('CHAT_SIDEKICK_USERS',       0x21);
define('CHAT_SIDEKICK_MESSAGE',     0x22);


$DAEMON = New ChatDaemon;
$DAEMON->socket_active = false;
$DAEMON->trace_level = E_ALL;
$DAEMON->socketserver_refresh = 10;
$DAEMON->rememberlast = 10;
$DAEMON->can_daemonize = function_exists('pcntl_fork');

$logfile = fopen('chatd.log', 'a+');


/// Check the parameters //////////////////////////////////////////////////////

    $param = trim(strtolower($argv[1]));

    if (empty($param) || eregi('^(\-\-help|\-h)$', $param)) {
        echo 'Starts the Moodle chat socket server on port '.$CFG->chat_serverport;
        echo "\n\n";
        echo "Usage: chatd.php [-h|--start]\n\n";
        echo "Example:\n";
        echo "  chatd.php --start\n\n";
        echo "Options:\n";
        echo "  --start      Starts the daemon\n";
        echo "  -h, --help   Show this help\n";
        echo "\n";
        die();
    }



/// Try to set up all the sockets ////////////////////////////////////////////////

trace('Setting up sockets');

if (!function_exists('socket_set_option')) {
    // PHP < 4.3
    if (!function_exists('socket_setopt')) {
        // No socket_setopt!
        echo "Error: Neither socket_setopt() nor socket_set_option() exists.\n";
        echo "Possibly PHP has not been compiled with --enable-sockets.\n\n";
        die();
    }
    function socket_set_option($socket, $level, $name, $val) {
        return socket_setopt($socket, $level, $name, $val);
    }
}

// Creating socket

if(false === ($DAEMON->listen_socket = socket_create(AF_INET, SOCK_STREAM, 0))) {
    // Failed to create socket
    $DAEMON->last_error = socket_last_error();
    echo "Error: socket_create() failed: ". socket_strerror(socket_last_error($DAEMON->last_error)).' ['.$DAEMON->last_error."]\n";
    die();
}

//socket_close($DAEMON->listen_socket);
//die();

if(!socket_bind($DAEMON->listen_socket, $CFG->chat_serverip, $CFG->chat_serverport)) {
    // Failed to bind socket
    $DAEMON->last_error = socket_last_error();
    echo "Error: socket_bind() failed: ". socket_strerror(socket_last_error($DAEMON->last_error)).' ['.$DAEMON->last_error."]\n";
    die();
}
if(!socket_listen($DAEMON->listen_socket, $CFG->chat_servermax)) {
    // Failed to get socket to listen
    $DAEMON->last_error = socket_last_error();
    echo "Error: socket_listen() failed: ". socket_strerror(socket_last_error($DAEMON->last_error)).' ['.$DAEMON->last_error."]\n";
    die();
}

// Socket has been initialized and is ready
trace('Socket opened on port '.$CFG->chat_serverport);
$DAEMON->socket_active = true;

// [pj]: I really must have a good read on sockets. What exactly does this do?
// http://www.unixguide.net/network/socketfaq/4.5.shtml is still not enlightening enough for me.
socket_set_option($DAEMON->listen_socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_set_nonblock($DAEMON->listen_socket);

/// Sockets all set up!   Now we loop and process incoming data.
/*
declare(ticks=1);

$pid = pcntl_fork();
if ($pid == -1) {
     die("could not fork");
} else if ($pid) {
     exit(); // we are the parent
} else {
     // we are the child
}

// detatch from the controlling terminal
if (!posix_setsid()) {
   die("could not detach from terminal");
}

// setup signal handlers
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP, "sig_handler");
*/

if($DAEMON->can_daemonize) {
    trace('Unholy spirit possession: daemonizing');
    $DAEMON->pid = pcntl_fork();
    if($pid == -1) {
        trace('Process fork failed, terminating');
        die();
    }
    else if($pid) {
        // We are the parent
        trace('Successfully forked the daemon with PID '.$pid);
        die();
    }
    else {
        // We are the daemon! :P
    }

    // FROM NOW ON, IT'S THE DAEMON THAT'S RUNNING!

    // Detach from controlling terminal
    if(!posix_setsid()) {
        trace('Could not detach daemon process from terminal!');
    }
}
else {
    // Cannot go demonic
    trace('Unholy spirit possession failed: PHP is not compiled with --enable-pcntl');
}

trace('Started Moodle chatd on port '.$CFG->chat_serverport.', listening socket '.$DAEMON->listen_socket, E_USER_WARNING);

while(true) {
    $active = array();

    // First of all, let's see if any of our UFOs has identified itself
    if($DAEMON->conn_activity_ufo($active)) {
        foreach($active as $handle) {
            $read_socket = array($handle);
            $changed = socket_select($read_socket, $write = NULL, $except = NULL, 0, 0);

            if($changed > 0) {
                // Let's see what it has to say

                $data = socket_read($handle, 512);
                if(empty($data)) {
                    continue;
                }

                if(!ereg('win=(chat|users|message).*&chat_sid=([a-zA-Z0-9]*)&groupid=([0-9]*) HTTP', $data, $info)) {
                    // Malformed data
                    trace('UFO with '.$handle.': Request with malformed data; connection closed', E_USER_WARNING);
                    $DAEMON->dismiss_ufo($handle);
                    continue;
                }

                $type      = $info[1];
                $sessionid = $info[2];
                $groupid   = $info[3];

                $customdata = array();

                switch($type) {
                    case 'chat':
                       $type = CHAT_CONNECTION_CHANNEL;
                    break;
                    case 'users':
                        $type = CHAT_SIDEKICK_USERS;
                    break;
                    case 'message':
                        $type = CHAT_SIDEKICK_MESSAGE;
                        if(!ereg('chat_message=([^&]*)[& ]', $data, $info)) {
                            trace('Message sidekick did not contain a valid message', E_USER_WARNING);
                            $DAEMON->dismiss_ufo($handle);
                            continue;
                        }
                        else {
                            $customdata = array('message' => $info[1]);
                        }
                    break;
                    default:
                        trace('UFO with '.$handle.': Request with unknown type; connection closed', E_USER_WARNING);
                        $DAEMON->dismiss_ufo($handle);
                        continue;
                    break;
                }

                // OK, now we know it's something good... promote it and pass it all the data it needs
                $DAEMON->promote_ufo($handle, $type, $sessionid, $groupid, $customdata);
                continue;
            }
        }
    }

    // Finally, accept new connections
    $DAEMON->conn_accept();

    usleep($DAEMON->socketserver_refresh);
}

@socket_shutdown($DAEMON->listen_socket, 0);
die("\n\n-- terminated --\n");


function trace($message, $level = E_USER_NOTICE) {
    global $DAEMON, $logfile;

    $date = date('[Y-m-d H:i:s] ');
    $severity = '';

    switch($level) {
        case E_USER_WARNING: $severity = '*IMPORTANT* '; break;
        case E_USER_ERROR:   $severity = ' *CRITICAL* '; break;
    }

    $message = $date.$severity.$message."\n";

    if ($DAEMON->trace_level & $level) {
        if($level & E_USER_ERROR) {
            fwrite(STDERR, $message);
        }
        fwrite(STDOUT, $message);
        fwrite($logfile, $message);
        fflush($logfile);
    }
    flush();
}

function chat_socket_write($connection, $text) {
    $check_socket = array($connection);
    $socket_changed = socket_select($read = NULL, $check_socket, $except = NULL, 0, 0);
    if($socket_changed > 0) {
        socket_write($connection, $text, strlen($text));
        return true;
    }
    return false;
}

function pusers($chat, $groupid) {
/// Delete users who are using text version and are old

global $CFG, $str;

//chat_delete_old_users();


/// Print headers
ob_start();

//print_header();

$timenow = time();

if (empty($str)) {
    $str->idle   = get_string("idle", "chat");
    $str->beep   = get_string("beep", "chat");
    $str->day   = get_string("day");
    $str->days  = get_string("days");
    $str->hour  = get_string("hour");
    $str->hours = get_string("hours");
    $str->min   = get_string("min");
    $str->mins  = get_string("mins");
    $str->sec   = get_string("sec");
    $str->secs  = get_string("secs");
}

/// Get list of users

if (!$chatusers = chat_get_users($chat->id, $groupid)) {
    print_string("errornousers", "chat");
    die('no users');
    //return ob_get_clean();
    //exit;
}

echo "<html>\n";
echo "<head>\n";
echo "<script lang=\"Javascript\">\n";
echo "   function reloadme() {\n";
echo "       return false;\n";
echo "       window.location.reload();\n";
echo "   }\n";
echo "</script></head>\n";
echo '<body onload="setTimeout(\'reloadme()\', 5000);">'."\n\n";
for ($i = 0; $i < 100; ++$i) {
    echo "<!-- nix -->\n";
}

echo "<table width=\"100%\">";
foreach ($chatusers as $chatuser) {
    $lastping = $timenow - $chatuser->lastmessageping;
    echo "<tr><td width=35>";
    echo "<a target=\"_new\" onClick=\"return openpopup('/user/view.php?id=$chatuser->id&course=$chat->course','user$chatuser->id','');\" href=\"$CFG->wwwroot/user/view.php?id=$chatuser->id&course=$chat->course\">";
    print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
    echo "</a></td><td valign=center>";
    echo "<p><font size=1>";
    echo fullname($chatuser)."<br />";
    echo "<font color=\"#888888\">$str->idle: ".format_time($lastping, $str)."</font>";
    echo " <a href=\"users.php?chat_sid=$chat_sid&beep=$chatuser->id&groupid=$groupid\">$str->beep</a>";
    echo "</font></p>";
    echo "<td></tr>";
}
echo "</table>";

//window.setTimeout('reloadme', 1000)
echo '</body></html>';
return ob_get_clean();

}

?>
