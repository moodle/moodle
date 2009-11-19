#!/usr/bin/php -q
<?php

// Browser quirks
define('QUIRK_CHUNK_UPDATE', 0x0001);

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

$phpversion = phpversion();
echo 'Moodle chat daemon v1.0 on PHP '.$phpversion." (\$Id$)\n\n";

/// Set up all the variables we need   /////////////////////////////////////

/// $CFG variables are now defined in database by chat/lib.php

$_SERVER['PHP_SELF']        = 'dummy';
$_SERVER['SERVER_NAME']     = 'dummy';
$_SERVER['HTTP_USER_AGENT'] = 'dummy';

$nomoodlecookie = true;

include('../../config.php');
include('lib.php');

$_SERVER['SERVER_NAME'] = $CFG->chat_serverhost;
$_SERVER['PHP_SELF']    = "http://$CFG->chat_serverhost:$CFG->chat_serverport/mod/chat/chatd.php";

$safemode = ini_get('safe_mode');

if($phpversion < '4.3') {
    die("Error: The Moodle chat daemon requires at least PHP version 4.3 to run.\n       Since your version is $phpversion, you have to upgrade.\n\n");
}
if(!empty($safemode)) {
    die("Error: Cannot run with PHP safe_mode = On. Turn off safe_mode in php.ini.\n");
}

$passref = ini_get('allow_call_time_pass_reference');
if(empty($passref)) {
    die("Error: Cannot run with PHP allow_call_time_pass_reference = Off. Turn on allow_call_time_pass_reference in php.ini.\n");
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
        @socket_getpeername($this->handle, $this->ip, $this->port);
    }
}

class ChatDaemon {
    var $_resetsocket       = false;
    var $_readytogo         = false;
    var $_logfile           = false;
    var $_trace_to_console  = true;
    var $_trace_to_stdout   = true;
    var $_logfile_name      = 'chatd.log';
    var $_last_idle_poll    = 0;

    var $conn_ufo  = array();    // Connections not identified yet
    var $conn_side = array();    // Sessions with sidekicks waiting for the main connection to be processed
    var $conn_half = array();    // Sessions that have valid connections but not all of them
    var $conn_sets = array();    // Sessions with complete connection sets sets
    var $sets_info = array();    // Keyed by sessionid exactly like conn_sets, one of these for each of those
    var $chatrooms = array();    // Keyed by chatid, holding arrays of data

    // IMPORTANT: $conn_sets, $sets_info and $chatrooms must remain synchronized!
    //            Pay extra attention when you write code that affects any of them!

    function ChatDaemon() {
        $this->_trace_level         = E_ALL ^ E_USER_NOTICE;
        $this->_pcntl_exists        = function_exists('pcntl_fork');
        $this->_time_rest_socket    = 20;
        $this->_beepsoundsrc        = $GLOBALS['CFG']->wwwroot.'/mod/chat/beep.wav';
        $this->_freq_update_records = 20;
        $this->_freq_poll_idle_chat = $GLOBALS['CFG']->chat_old_ping;
        $this->_stdout = fopen('php://stdout', 'w');
        if($this->_stdout) {
            // Avoid double traces for everything
            $this->_trace_to_console = false;
        }
    }

    function error_handler ($errno, $errmsg, $filename, $linenum, $vars) {
        // Checks if an error needs to be suppressed due to @
        if(error_reporting() != 0) {
            $this->trace($errmsg.' on line '.$linenum, $errno);
        }
        return true;
    }

    function poll_idle_chats($now) {
        $this->trace('Polling chats to detect disconnected users');
        if(!empty($this->chatrooms)) {
            foreach($this->chatrooms as $chatid => $chatroom) {
                if(!empty($chatroom['users'])) {
                    foreach($chatroom['users'] as $sessionid => $userid) {
                        // We will be polling each user as required
                        $this->trace('...shall we poll '.$sessionid.'?');
                        if($this->sets_info[$sessionid]['chatuser']->lastmessageping < $this->_last_idle_poll) {
                            $this->trace('YES!');
                            // This user hasn't been polled since his last message
                            if($this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], '<!-- poll -->') === false) {
                                // User appears to have disconnected
                                $this->disconnect_session($sessionid);
                            }
                        }
                    }
                }
            }
        }
        $this->_last_idle_poll = $now;
    }

    function query_start() {
        return $this->_readytogo;
    }

    function trace($message, $level = E_USER_NOTICE) {
        $severity = '';

        switch($level) {
            case E_USER_WARNING: $severity = '*IMPORTANT* '; break;
            case E_USER_ERROR:   $severity = ' *CRITICAL* '; break;
            case E_NOTICE:
            case E_WARNING:      $severity = ' *CRITICAL* [php] '; break;
        }

        $date = date('[Y-m-d H:i:s] ');
        $message = $date.$severity.$message."\n";

        if ($this->_trace_level & $level) {
            // It is accepted for output

            // Error-class traces go to STDERR too
            if($level & E_USER_ERROR) {
                fwrite(STDERR, $message);
            }

            // Emit the message to wherever we should
            if($this->_trace_to_stdout) {
                fwrite($this->_stdout, $message);
                fflush($this->_stdout);
            }
            if($this->_trace_to_console) {
                echo $message;
                flush();
            }
            if($this->_logfile) {
                fwrite($this->_logfile, $message);
                fflush($this->_logfile);
            }
        }
    }

    function write_data($connection, $text) {
        $written = @socket_write($connection, $text, strlen($text));
        if($written === false) {
            // $this->trace("socket_write() failed: reason: " . socket_strerror(socket_last_error($connection)));
            return false;
        }
        return true;

        // Enclosing the above code inside this blocks makes sure that
        // "a socket write operation will not block". I 'm not so sure
        // if this is needed, as we have a nonblocking socket anyway.
        // If trouble starts to creep up, we 'll restore this.
//        $check_socket = array($connection);
//        $socket_changed = socket_select($read = NULL, $check_socket, $except = NULL, 0, 0);
//        if($socket_changed > 0) {
//
//            // ABOVE CODE GOES HERE
//
//        }
//        return false;
    }

    function user_lazy_update($sessionid) {
        // TODO: this can and should be written as a single UPDATE query
        if(empty($this->sets_info[$sessionid])) {
            $this->trace('user_lazy_update() called for an invalid SID: '.$sessionid, E_USER_WARNING);
            return false;
        }

        $now = time();

        // We 'll be cheating a little, and NOT updating the record data as
        // often as we can, so that we save on DB queries (imagine MANY users)
        if($now - $this->sets_info[$sessionid]['lastinfocommit'] > $this->_freq_update_records) {
            // commit to permanent storage
            $this->sets_info[$sessionid]['lastinfocommit'] = $now;
            update_record('chat_users', $this->sets_info[$sessionid]['chatuser']);
        }
        return true;
    }

    function get_user_window($sessionid) {

        global $CFG;

        static $str;

        $info = &$this->sets_info[$sessionid];
        course_setup($info['course'], $info['user']);

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
            $str->years = get_string('years');
        }

        ob_start();
        $refresh_inval = $CFG->chat_refresh_userlist * 1000;
        echo <<<EOD
        <html><head>
        <meta http-equiv="refresh" content="$refresh_inval">
        <style type="text/css"> img{border:0} </style>
        <script type="text/javascript">
        //<![CDATA[
        function openpopup(url,name,options,fullscreen) {
            fullurl = "$CFG->wwwroot" + url;
            windowobj = window.open(fullurl,name,options);
            if (fullscreen) {
                windowobj.moveTo(0,0);
                windowobj.resizeTo(screen.availWidth,screen.availHeight);
            }
            windowobj.focus();
            return false;
        }
        //]]>
        </script></head><body><table><tbody>
EOD;

        // Get the users from that chatroom
        $users = $this->chatrooms[$info['chatid']]['users'];

        foreach ($users as $usersessionid => $userid) {
            // Fetch each user's sessionid and then the rest of his data from $this->sets_info
            $userinfo = $this->sets_info[$usersessionid];

            $lastping = $timenow - $userinfo['chatuser']->lastmessageping;
            $popuppar = '\'/user/view.php?id='.$userinfo['user']->id.'&amp;course='.$userinfo['courseid'].'\',\'user'.$userinfo['chatuser']->id.'\',\'\'';
            echo '<tr><td width="35">';
            echo '<a target="_new" onclick="return openpopup('.$popuppar.');" href="'.$CFG->wwwroot.'/user/view.php?id='.$userinfo['chatuser']->id.'&amp;course='.$userinfo['courseid'].'">';
            print_user_picture($userinfo['user']->id, 0, $userinfo['user']->picture, false, false, false);
            echo "</a></td><td valign=\"center\">";
            echo "<p><font size=\"1\">";
            echo fullname($userinfo['user'])."<br />";
            echo "<font color=\"#888888\">$str->idle: ".format_time($lastping, $str)."</font> ";
            echo '<a target="empty" href="http://'.$CFG->chat_serverhost.':'.$CFG->chat_serverport.'/?win=beep&amp;beep='.$userinfo['user']->id.
                 '&chat_sid='.$sessionid.'">'.$str->beep."</a>\n";
            echo "</font></p>";
            echo "<td></tr>";
        }

        echo '</tbody></table>';

        // About 2K of HTML comments to force browsers to render the HTML
        // echo $GLOBALS['CHAT_DUMMY_DATA'];

        echo "</body>\n</html>\n";

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
                insert_record('chat_messages', $msg, false);

                // OK, now push it out to all users
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping
                $this->sets_info[$sessionid]['chatuser']->lastping        = $msg->timestamp;
                $this->sets_info[$sessionid]['chatuser']->lastmessageping = $msg->timestamp;
                $this->user_lazy_update($sessionid);

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
                $this->write_data($handle, $header);
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

                // The refresh value is 2 seconds higher than the configuration variable because we are doing JS refreshes all the time.
                // However, if the JS doesn't work for some reason, we still want to refresh once in a while.
                $header .= "Refresh: ".(intval($CFG->chat_refresh_userlist) + 2)."; url=http://$CFG->chat_serverhost:$CFG->chat_serverport/?win=users&".
                           "chat_sid=".$sessionid."\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response
                $this->trace('writing users http response to handle '.$handle);
                $this->write_data($handle, $header . $content);

                // Update that user's lastping
                $this->sets_info[$sessionid]['chatuser']->lastping = time();
                $this->user_lazy_update($sessionid);

            break;

            case CHAT_SIDEKICK_MESSAGE:
                // Incoming message

                // Browser stupidity protection from duplicate messages:
                $messageindex = intval($customdata['index']);

                if($this->sets_info[$sessionid]['lastmessageindex'] >= $messageindex) {
                    // We have already broadcasted that!
                    // $this->trace('discarding message with stale index');
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

                // A slight hack to prevent malformed SQL inserts
                $origmsg = $msg->message;
                $msg->message = addslashes($msg->message);

                // Commit to DB
                insert_record('chat_messages', $msg, false);

                // Undo the hack
                $msg->message = $origmsg;

                // OK, now push it out to all users
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping
                $this->sets_info[$sessionid]['chatuser']->lastping        = $msg->timestamp;
                $this->sets_info[$sessionid]['chatuser']->lastmessageping = $msg->timestamp;
                $this->user_lazy_update($sessionid);

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
                $this->write_data($handle, $header);

                // All done
            break;
        }

        socket_shutdown($handle);
        socket_close($handle);
    }

    function promote_final($sessionid, $customdata) {
        if(isset($this->conn_sets[$sessionid])) {
            $this->trace('Set cannot be finalized: Session '.$sessionid.' is already active');
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

        $this->conn_sets[$sessionid] = $this->conn_half[$sessionid];

        // This whole thing needs to be purged of redundant info, and the
        // code base to follow suit. But AFTER development is done.
        $this->sets_info[$sessionid] = array(
            'lastinfocommit' => 0,
            'lastmessageindex' => 0,
            'course'    => $course,
            'courseid'  => $course->id,
            'chatuser'  => $chatuser,
            'chatid'    => $chat->id,
            'user'      => $user,
            'userid'    => $user->id,
            'groupid'   => $chatuser->groupid,
            'lang'      => $chatuser->lang,
            'quirks'    => $customdata['quirks']
        );

        // If we know nothing about this chatroom, initialize it and add the user
        if(!isset($this->chatrooms[$chat->id]['users'])) {
            $this->chatrooms[$chat->id]['users'] = array($sessionid => $user->id);
        }
        else {
            // Otherwise just add the user
            $this->chatrooms[$chat->id]['users'][$sessionid] = $user->id;
        }

        // $this->trace('QUIRKS value for this connection is '.$customdata['quirks']);

        $this->dismiss_half($sessionid, false);
        $this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $CHAT_HTMLHEAD_JS);
        $this->trace('Connection accepted: '.$this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL].', SID: '.$sessionid.' UID: '.$chatuser->userid.' GID: '.$chatuser->groupid, E_USER_WARNING);

        // Finally, broadcast the "entered the chat" message

        $msg = &New stdClass;
        $msg->chatid = $chatuser->chatid;
        $msg->userid = $chatuser->userid;
        $msg->groupid = $chatuser->groupid;
        $msg->system = 1;
        $msg->message = 'enter';
        $msg->timestamp = time();

        insert_record('chat_messages', $msg, false);
        $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

        return true;
    }

    function promote_ufo($handle, $type, $sessionid, $customdata) {
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
                        //$this->trace('Dispatching sidekick immediately');
                        $this->dispatch_sidekick($handle, $type, $sessionid, $customdata);
                        $this->dismiss_ufo($handle, false);
                    }
                    else {
                        // No, so put it in the waiting list
                        $this->trace('sidekick waiting');
                        $this->conn_side[$sessionid][] = array('type' => $type, 'handle' => $handle, 'customdata' => $customdata);
                    }
                    return true;
                }

                // If it's not a sidekick, at this point it can only be da man

                if($type & CHAT_CONNECTION) {
                    // This forces a new connection right now...
                    $this->trace('Incoming connection from '.$ufo->ip.':'.$ufo->port);

                    // Do we have such a connection active?
                    if(isset($this->conn_sets[$sessionid])) {
                        // Yes, so regrettably we cannot promote you
                        $this->trace('Connection rejected: session '.$sessionid.' is already final');
                        $this->dismiss_ufo($handle, true, 'Your SID was rejected.');
                        return false;
                    }

                    // Join this with what we may have already
                    $this->conn_half[$sessionid][$type] = $handle;

                    // Do the bookkeeping
                    $this->promote_final($sessionid, $customdata);

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
        $chatroom = $this->sets_info[$sessionid]['chatid'];
        $userid   = $this->sets_info[$sessionid]['userid'];
        unset($this->conn_sets[$sessionid]);
        unset($this->sets_info[$sessionid]);
        unset($this->chatrooms[$chatroom]['users'][$sessionid]);
        $this->trace('Removed all traces of user with session '.$sessionid, E_USER_NOTICE);
        return true;
    }


    function dismiss_ufo($handle, $disconnect = true, $message = NULL) {
        if(empty($this->conn_ufo)) {
            return false;
        }
        foreach($this->conn_ufo as $id => $ufo) {
            if($ufo->handle == $handle) {
                unset($this->conn_ufo[$id]);
                if($disconnect) {
                    if(!empty($message)) {
                        $this->write_data($handle, $message."\n\n");
                    }
                    socket_shutdown($handle);
                    socket_close($handle);
                }
                return true;
            }
        }
        return false;
    }

    function conn_accept() {
        $read_socket = array($this->listen_socket);
        $changed = socket_select($read_socket, $write = NULL, $except = NULL, 0, 0);

        if(!$changed) {
            return false;
        }
        $handle = socket_accept($this->listen_socket);
        if(!$handle) {
            return false;
        }

        $newconn = &New ChatConnection($handle);
        $id = $this->new_ufo_id();
        $this->conn_ufo[$id] = $newconn;

        //$this->trace('UFO #'.$id.': connection from '.$newconn->ip.' on port '.$newconn->port.', '.$newconn->handle);
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

        $now = time();

        // First of all, mark this chatroom as having had activity now
        $this->chatrooms[$message->chatid]['lastactivity'] = $now;

        foreach($this->sets_info as $sessionid => $info) {
            // We need to get handles from users that are in the same chatroom, same group
            if($info['chatid'] == $message->chatid &&
              ($info['groupid'] == $message->groupid || $message->groupid == 0))
            {

                // Simply give them the message
                course_setup($info['course'], $info['user']);
                $output = chat_format_message_manually($message, $info['courseid'], $sender, $info['user']);
                $this->trace('Delivering message "'.$output->text.'" to '.$this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL]);

                if($output->beep) {
                    $this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], '<embed src="'.$this->_beepsoundsrc.'" autostart="true" hidden="true" />');
                }

                if($info['quirks'] & QUIRK_CHUNK_UPDATE) {
                    $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                    $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                    $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                }

                if(!$this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $output->html)) {
                    $this->disconnect_session($sessionid);
                }
                //$this->trace('Sent to UID '.$this->sets_info[$sessionid]['userid'].': '.$message->text_);
            }
        }
    }

    function disconnect_session($sessionid) {
        $info = $this->sets_info[$sessionid];

        delete_records('chat_users', 'sid', $sessionid);
        $msg = &New stdClass;
        $msg->chatid = $info['chatid'];
        $msg->userid = $info['userid'];
        $msg->groupid = $info['groupid'];
        $msg->system = 1;
        $msg->message = 'exit';
        $msg->timestamp = time();

        $this->trace('User has disconnected, destroying uid '.$info['userid'].' with SID '.$sessionid, E_USER_WARNING);
        insert_record('chat_messages', $msg, false);

        // *************************** IMPORTANT
        //
        // Kill him BEFORE broadcasting, otherwise we 'll get infinite recursion!
        //
        // **********************************************************************
        $latesender = $info['user'];
        $this->dismiss_set($sessionid);
        $this->message_broadcast($msg, $latesender);
    }

    function fatal($message) {
        $message .= "\n";
        if($this->_logfile) {
            $this->trace($message, E_USER_ERROR);
        }
        echo "FATAL ERROR:: $message\n";
        die();
    }

    function init_sockets() {
        global $CFG;

        $this->trace('Setting up sockets');

        if(false === ($this->listen_socket = socket_create(AF_INET, SOCK_STREAM, 0))) {
            // Failed to create socket
            $lasterr = socket_last_error();
            $this->fatal('socket_create() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        //socket_close($DAEMON->listen_socket);
        //die();

        if(!socket_bind($this->listen_socket, $CFG->chat_serverip, $CFG->chat_serverport)) {
            // Failed to bind socket
            $lasterr = socket_last_error();
            $this->fatal('socket_bind() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        if(!socket_listen($this->listen_socket, $CFG->chat_servermax)) {
            // Failed to get socket to listen
            $lasterr = socket_last_error();
            $this->fatal('socket_listen() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        // Socket has been initialized and is ready
        $this->trace('Socket opened on port '.$CFG->chat_serverport);

        // [pj]: I really must have a good read on sockets. What exactly does this do?
        // http://www.unixguide.net/network/socketfaq/4.5.shtml is still not enlightening enough for me.
        socket_setopt($this->listen_socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->listen_socket);
    }

    function cli_switch($switch, $param = NULL) {
        switch($switch) { //LOL
            case 'reset':
                // Reset sockets
                $this->_resetsocket = true;
                return false;
            case 'start':
                // Start the daemon
                $this->_readytogo = true;
                return false;
            break;
            case 'v':
                // Verbose mode
                $this->_trace_level = E_ALL;
                return false;
            break;
            case 'l':
                // Use logfile
                if(!empty($param)) {
                    $this->_logfile_name = $param;
                }
                $this->_logfile = @fopen($this->_logfile_name, 'a+');
                if($this->_logfile == false) {
                    $this->fatal('Failed to open '.$this->_logfile_name.' for writing');
                }
                return false;
            default:
                // Unrecognized
                $this->fatal('Unrecognized command line switch: '.$switch);
            break;
        }
        return false;
    }

}

$DAEMON = New ChatDaemon;
set_error_handler(array($DAEMON, 'error_handler'));

/// Check the parameters //////////////////////////////////////////////////////

unset($argv[0]);
$commandline = implode(' ', $argv);
if(strpos($commandline, '-') === false) {
    if(!empty($commandline)) {
        // We cannot have received any meaningful parameters
        $DAEMON->fatal('Garbage in command line');
    }
}
else {
    // Parse command line
    $switches = preg_split('/(-{1,2}[a-zA-Z]+) */', $commandline, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    // Taking advantage of the fact that $switches is indexed with incrementing numeric keys
    // We will be using that to pass additional information to those switches who need it
    $numswitches = count($switches);

    // Fancy way to give a "hyphen" boolean flag to each "switch"
    $switches = array_map(create_function('$x', 'return array("str" => $x, "hyphen" => (substr($x, 0, 1) == "-"));'), $switches);

    for($i = 0; $i < $numswitches; ++$i) {

        $switch = $switches[$i]['str'];
        $params = ($i == $numswitches - 1 ? NULL :
                                            ($switches[$i + 1]['hyphen'] ? NULL : trim($switches[$i + 1]['str']))
                  );

        if(substr($switch, 0, 2) == '--') {
            // Double-hyphen switch
            $DAEMON->cli_switch(strtolower(substr($switch, 2)), $params);
        }
        else if(substr($switch, 0, 1) == '-') {
            // Single-hyphen switch(es), may be more than one run together
            $switch = substr($switch, 1); // Get rid of the -
            $len = strlen($switch);
            for($j = 0; $j < $len; ++$j) {
                $DAEMON->cli_switch(strtolower(substr($switch, $j, 1)), $params);
            }
        }
    }
}

if(!$DAEMON->query_start()) {
    // For some reason we didn't start, so print out some info
    echo 'Starts the Moodle chat socket server on port '.$CFG->chat_serverport;
    echo "\n\n";
    echo "Usage: chatd.php [parameters]\n\n";
    echo "Parameters:\n";
    echo "  --start         Starts the daemon\n";
    echo "  -v              Verbose mode (prints trivial information messages)\n";
    echo "  -l [logfile]    Log all messages to logfile (if not specified, chatd.log)\n";
    echo "Example:\n";
    echo "  chatd.php --start -l\n\n";
    die();
}

if (!function_exists('socket_setopt')) {
    echo "Error: Function socket_setopt() does not exist.\n";
    echo "Possibly PHP has not been compiled with --enable-sockets.\n\n";
    die();
}

$DAEMON->init_sockets();

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

if($DAEMON->_pcntl_exists && false) {
    $DAEMON->trace('Unholy spirit possession: daemonizing');
    $DAEMON->pid = pcntl_fork();
    if($pid == -1) {
        $DAEMON->trace('Process fork failed, terminating');
        die();
    }
    else if($pid) {
        // We are the parent
        $DAEMON->trace('Successfully forked the daemon with PID '.$pid);
        die();
    }
    else {
        // We are the daemon! :P
    }

    // FROM NOW ON, IT'S THE DAEMON THAT'S RUNNING!

    // Detach from controlling terminal
    if(!posix_setsid()) {
        $DAEMON->trace('Could not detach daemon process from terminal!');
    }
}
else {
    // Cannot go demonic
    $DAEMON->trace('Unholy spirit possession failed: PHP is not compiled with --enable-pcntl');
}
*/

$DAEMON->trace('Started Moodle chatd on port '.$CFG->chat_serverport.', listening socket '.$DAEMON->listen_socket, E_USER_WARNING);

/// Clear the decks of old stuff
delete_records('chat_users', 'version', 'sockets');

while(true) {
    $active = array();

    // First of all, let's see if any of our UFOs has identified itself
    if($DAEMON->conn_activity_ufo($active)) {
        foreach($active as $handle) {
            $read_socket = array($handle);
            $changed = socket_select($read_socket, $write = NULL, $except = NULL, 0, 0);

            if($changed > 0) {
                // Let's see what it has to say

                $data = socket_read($handle, 2048); // should be more than 512 to prevent empty pages and repeated messages!!
                if(empty($data)) {
                    continue;
                }

                if (strlen($data) == 2048) { // socket_read has more data, ignore all data
                    $DAEMON->trace('UFO with '.$handle.': Data too long; connection closed', E_USER_WARNING);
                    $DAEMON->dismiss_ufo($handle, true, 'Data too long; connection closed');
                    continue;
                }

                if(!ereg('win=(chat|users|message|beep).*&chat_sid=([a-zA-Z0-9]*) HTTP', $data, $info)) {
                    // Malformed data
                    $DAEMON->trace('UFO with '.$handle.': Request with malformed data; connection closed', E_USER_WARNING);
                    $DAEMON->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                    continue;
                }

                $type      = $info[1];
                $sessionid = $info[2];

                $customdata = array();

                switch($type) {
                    case 'chat':
                       $type = CHAT_CONNECTION_CHANNEL;
                        $customdata['quirks'] = 0;
                        if(strpos($data, 'Safari')) {
                            $DAEMON->trace('Safari identified...', E_USER_WARNING);
                            $customdata['quirks'] += QUIRK_CHUNK_UPDATE;
                        }
                    break;
                    case 'users':
                        $type = CHAT_SIDEKICK_USERS;
                    break;
                    case 'beep':
                        $type = CHAT_SIDEKICK_BEEP;
                        if(!ereg('beep=([^&]*)[& ]', $data, $info)) {
                            $DAEMON->trace('Beep sidekick did not contain a valid userid', E_USER_WARNING);
                            $DAEMON->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                            continue;
                        }
                        else {
                            $customdata = array('beep' => intval($info[1]));
                        }
                    break;
                    case 'message':
                        $type = CHAT_SIDEKICK_MESSAGE;
                        if(!ereg('chat_message=([^&]*)[& ]chat_msgidnr=([^&]*)[& ]', $data, $info)) {
                            $DAEMON->trace('Message sidekick did not contain a valid message', E_USER_WARNING);
                            $DAEMON->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                            continue;
                        }
                        else {
                            $customdata = array('message' => $info[1], 'index' => $info[2]);
                        }
                    break;
                    default:
                        $DAEMON->trace('UFO with '.$handle.': Request with unknown type; connection closed', E_USER_WARNING);
                        $DAEMON->dismiss_ufo($handle, true, 'Request with unknown type; connection closed');
                        continue;
                    break;
                }

                // OK, now we know it's something good... promote it and pass it all the data it needs
                $DAEMON->promote_ufo($handle, $type, $sessionid, $customdata);
                continue;
            }
        }
    }

    $now = time();

    // Clean up chatrooms with no activity as required
    if($now - $DAEMON->_last_idle_poll >= $DAEMON->_freq_poll_idle_chat) {
        $DAEMON->poll_idle_chats($now);
    }

    // Finally, accept new connections
    $DAEMON->conn_accept();

    usleep($DAEMON->_time_rest_socket);
}

@socket_shutdown($DAEMON->listen_socket, 0);
die("\n\n-- terminated --\n");

?>
