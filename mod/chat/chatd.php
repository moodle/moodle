#!/usr/bin/php -q
<?php

echo "Moodle chat daemon v1.0  (\$Id$)\n\n";

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
    // Chat-related info
    var $sid    = NULL;
    var $type   = NULL;
    //var $groupid        = NULL;

    // PHP-level info
    var $handle = NULL;

    // TCP/IP
    var $ip     = NULL;
    var $port   = NULL;

    function ChatConnection($resource) {
        $this->handle = $resource;
        socket_getpeername($this->handle, &$this->ip, &$this->port);
    }
}

class ChatDaemon {
    var $conn_ufo = array();     // Connections not identified yet
    var $conn_side = array();    // Sessions with sidekicks waiting for the main connection to be processed
    var $conn_half = array();    // Sessions that have valid connections but not all of them
    var $conn_sets = array();    // Sessions with complete connection sets sets
    var $sets_info = array();    // Keyed by sessionid exactly like conn_sets, one of these for each of those

    var $message_queue = array(); // Holds messages that we haven't committed to the DB yet

    function update_lastmessageping($sessionid, $time = NULL) {
        // TODO: this can and should be written as a single UPDATE query
        if(empty($this->sets_info[$sessionid])) {
            trace('update_lastmessageping() called for an invalid SID: '.$sessionid, E_USER_WARNING);
            return false;
        }

        $now = time();
        if(empty($time)) {
            $time = $now;
        }

        // We 'll be cheating a little, and NOT updating lastmessageping
        // as often as we have to, so we can save on DB queries (imagine MANY users)
        $this->sets_info[$sessionid]['chatuser']->lastmessageping = $time;

        // This will set it just fine for bookkeeping purposes.
        if($now - $this->sets_info[$sessionid]['lastinfocommit'] > $this->live_data_update_threshold) {
            // commit to permanent storage
            // trace('Committing volatile lastmessageping for session '.$sessionid);
            $this->sets_info[$sessionid]['lastinfocommit'] = $now;
            update_record('chat_users', $this->sets_info[$sessionid]['chatuser']);
        }
        return true;
    }

    function get_user_window($sessionid) {

        global $CFG, $THEME;

        static $str;

        $info = &$this->sets_info[$sessionid];
        $oldlang = chat_language_override($info['lang']);

        $timenow = time();

        if (empty($str)) {
            $str->idle  = get_string("idle", "chat");
            $str->beep  = get_string("beep", "chat");
            $str->day   = get_string("day");
            $str->days  = get_string("days");
            $str->hour  = get_string("hour");
            $str->hours = get_string("hours");
            $str->min   = get_string("min");
            $str->mins  = get_string("mins");
            $str->sec   = get_string("sec");
            $str->secs  = get_string("secs");
        }

        ob_start();
        echo '<html><head>';
        echo '<script language="JavaScript">';
        echo "<!-- //hide\n";

        echo 'function openpopup(url,name,options,fullscreen) {';
        echo 'fullurl = "'.$CFG->wwwroot.'" + url;';
        echo 'windowobj = window.open(fullurl,name,options);';
        echo 'if (fullscreen) {';
        echo '  windowobj.moveTo(0,0);';
        echo '  windowobj.resizeTo(screen.availWidth,screen.availHeight); ';
        echo '}';
        echo 'windowobj.focus();';
        echo 'return false;';
        echo "}\n-->\n";
        echo '</script></head><body style="font-face: serif;" bgcolor="'.$THEME->body.'">';

        echo '<table style="width: 100%;"><tbody>';
        if(empty($this->sets_info)) {
            // No users
            echo '<tr><td>&nbsp;</td></tr>';
        }
        else {
            foreach ($this->sets_info as $usersid => $userinfo) {
                $lastping = $timenow - $userinfo['chatuser']->lastmessageping;
                $popuppar = '\'/user/view.php?id='.$userinfo['user']->id.'&amp;course='.$userinfo['courseid'].'\',\'user'.$userinfo['chatuser']->id.'\',\'\'';
                echo '<tr><td width="35">';
                echo '<a target="_new" onclick="return openpopup('.$popuppar.');" href="'.$CFG->wwwroot.'/user/view.php?id='.$userinfo['chatuser']->id.'&amp;course='.$userinfo['courseid'].'">';
                print_user_picture($userinfo['user']->id, 0, $userinfo['user']->picture, false, false, false);
                echo "</a></td><td valign=center>";
                echo "<p><font size=1>";
                echo fullname($userinfo['user'])."<br />";
                echo "<font color=\"#888888\">$str->idle: ".format_time($lastping, $str)."</font> ";
                echo '<a target="empty" href="http://'.$CFG->chat_serverhost.':'.$CFG->chat_serverport.'/?win=beep&beep='.$userinfo['user']->id.
                     '&chat_sid='.$sessionid.'&groupid='.$this->sets_info[$sessionid]['groupid'].'">'.$str->beep."</a>\n";
                echo "</font></p>";
                echo "<td></tr>";
            }
        }
        echo '</tbody></table>';

        // About 2K of HTML comments to force browsers to render the HTML
        // echo $GLOBALS['CHAT_DUMMY_DATA'];

        echo "</body>\n</html>\n";

        chat_language_restore($oldlang);
        return ob_get_clean();

    }

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
            // TODO: is this late-dispatch working correctly?
            $this->dispatch_sidekick($sidekick['handle'], $sidekick['type'], $sessionid, $sidekick['customdata']);
            unset($this->conn_side[$sessionid][$sideid]);
        }
        return true;
    }

    function dispatch_sidekick($handle, $type, $sessionid, $customdata) {
        global $CFG;

        switch($type) {
            case CHAT_SIDEKICK_BEEP:
                // Incoming beep
                $msg = &New stdClass;
                $msg->chatid    = $this->sets_info[$sessionid]['chatid'];
                $msg->userid    = $this->sets_info[$sessionid]['userid'];
                $msg->groupid   = $this->sets_info[$sessionid]['groupid'];
                $msg->system    = 0;
                $msg->message   = 'beep '.$customdata['beep'];
                $msg->timestamp = time();

                // Commit to DB
                insert_record('chat_messages', $msg);

                // OK, now push it out to all users
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping
                $this->update_lastmessageping($sessionid, $msg->timestamp);

                // We did our work, but before slamming the door on the poor browser
                // show the courtesy of responding to the HTTP request. Otherwise, some
                // browsers decide to get vengeance by flooding us with repeat requests.

                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response
                chat_socket_write($handle, $header);
                // All done
            break;

            case CHAT_SIDEKICK_USERS:
                // A request to paint a user window

                $content = $this->get_user_window($sessionid);

                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "Content-Length: ".strlen($content)."\n";
                $header .= "Refresh: $CFG->chat_refresh_userlist; URL=http://$CFG->chat_serverhost:$CFG->chat_serverport/?win=users&".
                           "chat_sid=".$sessionid."&groupid=".$this->sets_info[$sessionid]['groupid']."\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response
                trace('writing users http response to handle '.$handle);
                chat_socket_write($handle, $header . $content);

/*
                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "\n";
                trace('writing users http response to handle '.$handle);
                chat_socket_write($handle, $header);
*/
            break;
            case CHAT_SIDEKICK_MESSAGE:
                // Incoming message

                // Browser stupidity protection from duplicate messages:
                $messageindex = intval($customdata['index']);
                
                if($this->sets_info[$sessionid]['lastmessageindex'] >= $messageindex) {
                    // We have already broadcasted that!
                    trace('discarding message with stale index');
                    break;
                }
                else {
                    // Update our info
                    $this->sets_info[$sessionid]['lastmessageindex'] = $messageindex;
                }

                $msg = &New stdClass;
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

                // Commit to DB
                insert_record('chat_messages', $msg);

                // OK, now push it out to all users
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping
                $this->update_lastmessageping($sessionid, $msg->timestamp);

                // We did our work, but before slamming the door on the poor browser
                // show the courtesy of responding to the HTTP request. Otherwise, some
                // browsers decide to get vengeance by flooding us with repeat requests.

                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response
                chat_socket_write($handle, $header);

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
        $user = get_record('user', 'id', $chatuser->userid);
        if($user === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        $course = get_record('course', 'id', $chat->course); {
        if($course === false) {
            $this->dismiss_half($sessionid);
            return false;
            }
        }

        global $CHAT_HTMLHEAD_JS, $CFG;

        // A really sad thing, to have to do this by hand.... :-(
        $lang = NULL;
        if(empty($lang) && !empty($course->lang)) {
            $lang = $course->lang;
        }
        if(empty($lang) && !empty($user->lang)) {
            $lang = $user->lang;
        }
        if(empty($lang)) {
            $lang = $CFG->lang;
        }

        $this->conn_sets[$sessionid] = $this->conn_half[$sessionid];

        // This whole thing needs to be purged of redundant info, and the
        // code base to follow suit. But AFTER development is done.
        $this->sets_info[$sessionid] = array(
            'lastinfocommit' => 0,
            'lastmessageindex' => 0,
            'courseid'  => $course->id,
            'chatuser'  => $chatuser,
            'chatid'    => $chatuser->chatid,
            'user'      => $user,
            'userid'    => $chatuser->userid,
            'groupid'   => $groupid,
            'lang'      => $lang
        );

        $this->dismiss_half($sessionid, false);
        chat_socket_write($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $CHAT_HTMLHEAD_JS);
        trace('Connection accepted: '.$this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL].', SID: '.$sessionid.' UID: '.$chatuser->userid.' GID: '.intval($groupid));

        // Finally, broadcast the "entered the chat" message

        $msg = &New stdClass;
        $msg->chatid = $chatuser->chatid;
        $msg->userid = $chatuser->userid;
        $msg->groupid = 0;
        $msg->system = 1;
        $msg->message = 'enter';
        $msg->timestamp = time();

        insert_record('chat_messages', $msg);
        $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

        return true;
    }

    function promote_ufo($handle, $type, $sessionid, $groupid, $customdata) {
        if(empty($this->conn_ufo)) {
            return false;
        }
        foreach($this->conn_ufo as $id => $ufo) {
            if($ufo->handle == $handle) {
                // OK, got the id of the UFO, but what is it?

                if($type & CHAT_SIDEKICK) {
                    // Is the main connection ready?
                    if(isset($this->conn_sets[$sessionid])) {
                        // Yes, so dispatch this sidekick now and be done with it
                        //trace('Dispatching sidekick immediately');
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
                    trace('Incoming connection from '.$ufo->ip.':'.$ufo->port);

                    // Do we have such a connection active?
                    if(isset($this->conn_sets[$sessionid])) {
                        // Yes, so regrettably we cannot promote you
                        trace('Connection rejected: session '.$sessionid.' is already final');
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
                @socket_shutdown($handle);
                @socket_close($handle);
            }
        }
        unset($this->conn_half[$sessionid]);
        return true;
    }

    function dismiss_set($sessionid) {
        if(!empty($this->conn_sets[$sessionid])) {
            foreach($this->conn_sets[$sessionid] as $handle) {
                // Since we want to dismiss this, don't generate any errors if it's dead already
                @socket_shutdown($handle);
                @socket_close($handle);
            }
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
            if($ufo->handle == $handle) {
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

        $newconn = &New ChatConnection($handle);
        $id = $this->new_ufo_id();
        $this->conn_ufo[$id] = $newconn;

        //trace('UFO #'.$id.': connection from '.$newconn->ip.' on port '.$newconn->port.', '.$newconn->handle);
    }

    function conn_activity_ufo (&$handles) {
        $monitor = array();
        if(!empty($this->conn_ufo)) {
            foreach($this->conn_ufo as $ufoid => $ufo) {
                $monitor[$ufoid] = $ufo->handle;
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

    function message_broadcast($message, $sender) {
        if(empty($this->conn_sets)) {
            return true;
        }

        foreach($this->sets_info as $sessionid => $info) {
            // We need to get handles from users that are in the same chatroom, same group
            if($info['chatid'] == $message->chatid &&
              ($info['groupid'] == $message->groupid || $message->groupid == 0))
            {

                // Simply give them the message
                $output = chat_format_message_manually($message, 0, $sender, $info['user'], $info['lang']);
                trace('Delivering message "'.$output->text.'" to '.$this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL]);

                if($output->beep) {
                    chat_socket_write($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], '<embed src="'.$this->beepsoundsrc.'" autostart="true" hidden="true" />');
                }

                // Testing for Safari
                $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];

                if(!chat_socket_write($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $output->html)) {

                    // Send failed! We must now disconnect/forget about the user FIRST
                    // and THEN broadcast a message to all others... otherwise, infinite recursion.

                    delete_records('chat_users', 'sid', $sessionid);
                    $msg = &New stdClass;
                    $msg->chatid = $info['chatid'];
                    $msg->userid = $info['userid'];
                    $msg->groupid = 0;
                    $msg->system = 1;
                    $msg->message = 'exit';
                    $msg->timestamp = time();

                    trace('Client socket write failed, destroying uid '.$info['userid'].' with SID '.$sessionid);
                    insert_record('chat_messages', $msg);

                    // *************************** IMPORTANT
                    //
                    // Kill him BEFORE broadcasting, otherwise we 'll get infinite recursion!
                    //
                    // **********************************************************************
                    $latesender = $this->sets_info[$sessionid]['user'];
                    $this->dismiss_set($sessionid);
                    $this->message_broadcast($msg, $latesender);
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
define('CHAT_SIDEKICK_BEEP',        0x23);


$DAEMON = New ChatDaemon;
$DAEMON->socket_active = false;
$DAEMON->trace_level = E_ALL;
$DAEMON->socketserver_refresh = 20;
$DAEMON->can_daemonize = function_exists('pcntl_fork');
$DAEMON->beepsoundsrc = $CFG->wwwroot.'/mod/chat/beep.wav';
$DAEMON->live_data_update_threshold = 15;

/// Check the parameters //////////////////////////////////////////////////////

    $param = empty($argv[1]) ? NULL : trim(strtolower($argv[1]));

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


$logfile = fopen('chatd.log', 'a+');

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

    if($DAEMON->last_error != 98) {
        die();
    }

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

                if(!ereg('win=(chat|users|message|beep).*&chat_sid=([a-zA-Z0-9]*)&groupid=([0-9]*) HTTP', $data, $info)) {
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
                    case 'beep':
                        $type = CHAT_SIDEKICK_BEEP;
                        if(!ereg('beep=([^&]*)[& ]', $data, $info)) {
                            trace('Beep sidekick did not contain a valid userid', E_USER_WARNING);
                            $DAEMON->dismiss_ufo($handle);
                            continue;
                        }
                        else {
                            $customdata = array('beep' => intval($info[1]));
                        }
                    break;
                    case 'message':
                        $type = CHAT_SIDEKICK_MESSAGE;
                        if(!ereg('chat_message=([^&]*)[& ]chat_msgidnr=([^&]*)[& ]', $data, $info)) {
                            trace('Message sidekick did not contain a valid message', E_USER_WARNING);
                            $DAEMON->dismiss_ufo($handle);
                            continue;
                        }
                        else {
                            $customdata = array('message' => $info[1], 'index' => $info[2]);
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
        $written = socket_write($connection, $text, strlen($text));
        //trace('socket_write wrote '.$written.' of '.strlen($text).' bytes');
        return true;
    }
    return false;
}


?>
