<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Chat daemon
 *
 * @package    mod_chat
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->dirroot . '/mod/chat/lib.php');

// Browser quirks.
define('QUIRK_CHUNK_UPDATE', 0x0001);

// Connection telltale.
define('CHAT_CONNECTION',           0x10);
// Connections: Incrementing sequence, 0x10 to 0x1f.
define('CHAT_CONNECTION_CHANNEL',   0x11);

// Sidekick telltale.
define('CHAT_SIDEKICK',             0x20);
// Sidekicks: Incrementing sequence, 0x21 to 0x2f.
define('CHAT_SIDEKICK_USERS',       0x21);
define('CHAT_SIDEKICK_MESSAGE',     0x22);
define('CHAT_SIDEKICK_BEEP',        0x23);

$phpversion = phpversion();
echo 'Moodle chat daemon v1.0 on PHP '.$phpversion."\n\n";

// Set up all the variables we need.

// The $CFG variables are now defined in database by chat/lib.php.

$_SERVER['PHP_SELF']        = 'dummy';
$_SERVER['SERVER_NAME']     = 'dummy';
$_SERVER['HTTP_USER_AGENT'] = 'dummy';

$_SERVER['SERVER_NAME'] = $CFG->chat_serverhost;
$_SERVER['PHP_SELF']    = "http://$CFG->chat_serverhost:$CFG->chat_serverport/mod/chat/chatd.php";

core_php_time_limit::raise(0);
error_reporting(E_ALL);

function chat_empty_connection() {
    return array('sid' => null, 'handle' => null, 'ip' => null, 'port' => null, 'groupid' => null);
}

class ChatConnection {
    // Chat-related info.
    public $sid  = null;
    public $type = null;

    // PHP-level info.
    public $handle = null;

    // TCP/IP.
    public $ip     = null;
    public $port   = null;

    public function __construct($resource) {
        $this->handle = $resource;
        @socket_getpeername($this->handle, $this->ip, $this->port);
    }
}

class ChatDaemon {
    public $_resetsocket       = false;
    public $_readytogo         = false;
    public $_logfile           = false;
    public $_trace_to_console  = true;
    public $_trace_to_stdout   = true;
    public $_logfile_name      = 'chatd.log';
    public $_last_idle_poll    = 0;

    public $connectionsunidentified  = array(); // Connections not identified yet.
    public $connectionsside = array(); // Sessions with sidekicks waiting for the main connection to be processed.
    public $connectionshalf = array(); // Sessions that have valid connections but not all of them.
    public $connectionssets = array(); // Sessions with complete connection sets.
    public $setsinfo = array(); // Keyed by sessionid exactly like conn_sets, one of these for each of those.
    public $chatrooms = array(); // Keyed by chatid, holding arrays of data.

    // IMPORTANT: $connectionssets, $setsinfo and $chatrooms must remain synchronized!
    //            Pay extra attention when you write code that affects any of them!

    public function __construct() {
        $this->_trace_level         = E_ALL ^ E_USER_NOTICE;
        $this->_pcntl_exists        = function_exists('pcntl_fork');
        $this->_time_rest_socket    = 20;
        $this->_beepsoundsrc        = $GLOBALS['CFG']->wwwroot.'/mod/chat/beep.mp3';
        $this->_freq_update_records = 20;
        $this->_freq_poll_idle_chat = $GLOBALS['CFG']->chat_old_ping;
        $this->_stdout = fopen('php://stdout', 'w');
        if ($this->_stdout) {
            // Avoid double traces for everything.
            $this->_trace_to_console = false;
        }
    }

    public function error_handler ($errno, $errmsg, $filename, $linenum, $vars) {
        // Checks if an error needs to be suppressed due to @.
        if (error_reporting() != 0) {
            $this->trace($errmsg.' on line '.$linenum, $errno);
        }
        return true;
    }

    public function poll_idle_chats($now) {
        $this->trace('Polling chats to detect disconnected users');
        if (!empty($this->chatrooms)) {
            foreach ($this->chatrooms as $chatid => $chatroom) {
                if (!empty($chatroom['users'])) {
                    foreach ($chatroom['users'] as $sessionid => $userid) {
                        // We will be polling each user as required.
                        $this->trace('...shall we poll '.$sessionid.'?');
                        if (!empty($this->sets_info[$sessionid]) && isset($this->sets_info[$sessionid]['chatuser']) &&
                                // Having tried to exclude race conditions as already done in user_lazy_update()
                                // please do the real job by checking the last poll.
                                ($this->sets_info[$sessionid]['chatuser']->lastmessageping < $this->_last_idle_poll)) {
                            $this->trace('YES!');
                            // This user hasn't been polled since his last message.
                            $result = $this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], '<!-- poll -->');
                            if ($result === false) {
                                // User appears to have disconnected.
                                $this->disconnect_session($sessionid);
                            }
                        }
                    }
                }
            }
        }
        $this->_last_idle_poll = $now;
    }

    public function query_start() {
        return $this->_readytogo;
    }

    public function trace($message, $level = E_USER_NOTICE) {
        $severity = '';

        switch($level) {
            case E_USER_WARNING:
                $severity = '*IMPORTANT* ';
                break;
            case E_USER_ERROR:
                $severity = ' *CRITICAL* ';
                break;
            case E_NOTICE:
            case E_WARNING:
                $severity = ' *CRITICAL* [php] ';
                break;
        }

        $date = date('[Y-m-d H:i:s] ');
        $message = $date.$severity.$message."\n";

        if ($this->_trace_level & $level) {
            // It is accepted for output.

            // Error-class traces go to STDERR too.
            if ($level & E_USER_ERROR) {
                fwrite(STDERR, $message);
            }

            // Emit the message to wherever we should.
            if ($this->_trace_to_stdout) {
                fwrite($this->_stdout, $message);
                fflush($this->_stdout);
            }
            if ($this->_trace_to_console) {
                echo $message;
                flush();
            }
            if ($this->_logfile) {
                fwrite($this->_logfile, $message);
                fflush($this->_logfile);
            }
        }
    }

    public function write_data($connection, $text) {
        $written = @socket_write($connection, $text, strlen($text));
        if ($written === false) {
            return false;
        }
        return true;
    }

    public function user_lazy_update($sessionid) {
        global $DB;

        if (empty($this->sets_info[$sessionid])) {
            $this->trace('user_lazy_update() called for an invalid SID: '.$sessionid, E_USER_WARNING);
            return false;
        }

        // Does promote_final() already finish its job?
        if (!isset($this->sets_info[$sessionid]['lastinfocommit'])) {
            return false;
        }

        $now = time();

        // We 'll be cheating a little, and NOT updating the record data as
        // often as we can, so that we save on DB queries (imagine MANY users).
        if ($now - $this->sets_info[$sessionid]['lastinfocommit'] > $this->_freq_update_records) {
            // Commit to permanent storage.
            $this->sets_info[$sessionid]['lastinfocommit'] = $now;
            $DB->update_record('chat_users', $this->sets_info[$sessionid]['chatuser']);
        }
        return true;
    }

    public function get_user_window($sessionid) {
        global $CFG, $OUTPUT;

        static $str;

        $info = &$this->sets_info[$sessionid];

        $timenow = time();

        if (empty($str)) {
            $str = new stdClass();
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
        $refreshinval = $CFG->chat_refresh_userlist * 1000;
        echo <<<EOD
        <html><head>
        <meta http-equiv="refresh" content="$refreshinval">
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

        // Get the users from that chatroom.
        $users = $this->chatrooms[$info['chatid']]['users'];

        foreach ($users as $usersessionid => $userid) {
            // Fetch each user's sessionid and then the rest of his data from $this->sets_info.
            $userinfo = $this->sets_info[$usersessionid];

            $lastping = $timenow - $userinfo['chatuser']->lastmessageping;

            echo '<tr><td width="35">';

            $link = '/user/view.php?id='.$userinfo['user']->id.'&course='.$userinfo['courseid'];
            $anchortagcontents = $OUTPUT->user_picture($userinfo['user'], array('courseid' => $userinfo['courseid']));

            $action = new popup_action('click', $link, 'user'.$userinfo['chatuser']->id);
            $anchortag = $OUTPUT->action_link($link, $anchortagcontents, $action);

            echo $anchortag;
            echo "</td><td valign=\"center\">";
            echo "<p><font size=\"1\">";
            echo fullname($userinfo['user'])."<br />";
            echo "<font color=\"#888888\">$str->idle: ".format_time($lastping, $str)."</font> ";
            echo '<a target="empty" href="http://'.$CFG->chat_serverhost.':'.$CFG->chat_serverport.
                 '/?win=beep&amp;beep='.$userinfo['user']->id.
                 '&chat_sid='.$sessionid.'">'.$str->beep."</a>\n";
            echo "</font></p>";
            echo "<td></tr>";
        }

        echo '</tbody></table>';
        echo "</body>\n</html>\n";

        return ob_get_clean();

    }

    public function new_ufo_id() {
        static $id = 0;
        if ($id++ === 0x1000000) { // Cycling very very slowly to prevent overflow.
            $id = 0;
        }
        return $id;
    }

    public function process_sidekicks($sessionid) {
        if (empty($this->conn_side[$sessionid])) {
            return true;
        }
        foreach ($this->conn_side[$sessionid] as $sideid => $sidekick) {
            // TODO: is this late-dispatch working correctly?
            $this->dispatch_sidekick($sidekick['handle'], $sidekick['type'], $sessionid, $sidekick['customdata']);
            unset($this->conn_side[$sessionid][$sideid]);
        }
        return true;
    }

    public function dispatch_sidekick($handle, $type, $sessionid, $customdata) {
        global $CFG, $DB;

        switch($type) {
            case CHAT_SIDEKICK_BEEP:

                // Incoming beep.
                $msg = new stdClass;
                $msg->chatid    = $this->sets_info[$sessionid]['chatid'];
                $msg->userid    = $this->sets_info[$sessionid]['userid'];
                $msg->groupid   = $this->sets_info[$sessionid]['groupid'];
                $msg->issystem  = 0;
                $msg->message   = 'beep '.$customdata['beep'];
                $msg->timestamp = time();

                // Commit to DB.
                chat_send_chatmessage($this->sets_info[$sessionid]['chatuser'], $msg->message, false,
                    $this->sets_info[$sessionid]['cm']);

                // OK, now push it out to all users.
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping.
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
                $header .= "Content-Type: text/html; charset=utf-8\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response.
                $this->write_data($handle, $header);
                // All done.
            break;

            case CHAT_SIDEKICK_USERS:
                // A request to paint a user window.

                $content = $this->get_user_window($sessionid);

                $header  = "HTTP/1.1 200 OK\n";
                $header .= "Connection: close\n";
                $header .= "Date: ".date('r')."\n";
                $header .= "Server: Moodle\n";
                $header .= "Content-Type: text/html; charset=utf-8\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "Content-Length: ".strlen($content)."\n";

                // The refresh value is 2 seconds higher than the configuration variable.
                // This is because we are doing JS refreshes all the time.
                // However, if the JS doesn't work for some reason, we still want to refresh once in a while.
                $header .= "Refresh: ".(intval($CFG->chat_refresh_userlist) + 2).
                           "; url=http://$CFG->chat_serverhost:$CFG->chat_serverport/?win=users&".
                           "chat_sid=".$sessionid."\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response.
                $this->trace('writing users http response to handle '.$handle);
                $this->write_data($handle, $header . $content);

                // Update that user's lastping.
                $this->sets_info[$sessionid]['chatuser']->lastping = time();
                $this->user_lazy_update($sessionid);

            break;

            case CHAT_SIDEKICK_MESSAGE:
                // Incoming message.

                // Browser stupidity protection from duplicate messages.
                $messageindex = intval($customdata['index']);

                if ($this->sets_info[$sessionid]['lastmessageindex'] >= $messageindex) {
                    // We have already broadcasted that!
                    break;
                } else {
                    // Update our info.
                    $this->sets_info[$sessionid]['lastmessageindex'] = $messageindex;
                }

                $msg = new stdClass;
                $msg->chatid    = $this->sets_info[$sessionid]['chatid'];
                $msg->userid    = $this->sets_info[$sessionid]['userid'];
                $msg->groupid   = $this->sets_info[$sessionid]['groupid'];
                $msg->issystem  = 0;
                $msg->message   = urldecode($customdata['message']); // Have to undo the browser's encoding.
                $msg->timestamp = time();

                if (empty($msg->message)) {
                    // Someone just hit ENTER, send them on their way.
                    break;
                }

                // A slight hack to prevent malformed SQL inserts.
                $origmsg = $msg->message;
                $msg->message = $msg->message;

                // Commit to DB.
                chat_send_chatmessage($this->sets_info[$sessionid]['chatuser'], $msg->message, false,
                    $this->sets_info[$sessionid]['cm']);

                // Undo the hack.
                $msg->message = $origmsg;

                // OK, now push it out to all users.
                $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

                // Update that user's lastmessageping.
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
                $header .= "Content-Type: text/html; charset=utf-8\n";
                $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
                $header .= "Cache-Control: no-cache, must-revalidate\n";
                $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
                $header .= "\n";

                // That's enough headers for one lousy dummy response.
                $this->write_data($handle, $header);

                // All done.
            break;
        }

        socket_shutdown($handle);
        socket_close($handle);
    }

    public function promote_final($sessionid, $customdata) {
        global $DB;

        if (isset($this->conn_sets[$sessionid])) {
            $this->trace('Set cannot be finalized: Session '.$sessionid.' is already active');
            return false;
        }

        $chatuser = $DB->get_record('chat_users', array('sid' => $sessionid));
        if ($chatuser === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        $chat = $DB->get_record('chat', array('id' => $chatuser->chatid));
        if ($chat === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        $user = $DB->get_record('user', array('id' => $chatuser->userid));
        if ($user === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        $course = $DB->get_record('course', array('id' => $chat->course));
        if ($course === false) {
            $this->dismiss_half($sessionid);
            return false;
        }
        if (!($cm = get_coursemodule_from_instance('chat', $chat->id, $course->id))) {
            $this->dismiss_half($sessionid);
            return false;
        }

        global $CHAT_HTMLHEAD_JS;

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
            'cm'        => $cm,
            'user'      => $user,
            'userid'    => $user->id,
            'groupid'   => $chatuser->groupid,
            'lang'      => $chatuser->lang,
            'quirks'    => $customdata['quirks']
        );

        // If we know nothing about this chatroom, initialize it and add the user.
        if (!isset($this->chatrooms[$chat->id]['users'])) {
            $this->chatrooms[$chat->id]['users'] = array($sessionid => $user->id);
        } else {
            // Otherwise just add the user.
            $this->chatrooms[$chat->id]['users'][$sessionid] = $user->id;
        }

        $header  = "HTTP/1.1 200 OK\n";
        $header .= "Connection: close\n";
        $header .= "Date: ".date('r')."\n";
        $header .= "Server: Moodle\n";
        $header .= "Content-Type: text/html; charset=utf-8\n";
        $header .= "Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT\n";
        $header .= "Cache-Control: no-cache, must-revalidate\n";
        $header .= "Expires: Wed, 4 Oct 1978 09:32:45 GMT\n";
        $header .= "\n";

        $this->dismiss_half($sessionid, false);
        $this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $header . $CHAT_HTMLHEAD_JS);
        $this->trace('Connection accepted: '
                     .$this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL]
                     .', SID: '
                     .$sessionid
                     .' UID: '
                     .$chatuser->userid
                     .' GID: '
                     .$chatuser->groupid, E_USER_WARNING);

        // Finally, broadcast the "entered the chat" message.

        $msg = new stdClass;
        $msg->chatid = $chatuser->chatid;
        $msg->userid = $chatuser->userid;
        $msg->groupid = $chatuser->groupid;
        $msg->issystem = 1;
        $msg->message = 'enter';
        $msg->timestamp = time();

        chat_send_chatmessage($chatuser, $msg->message, true);
        $this->message_broadcast($msg, $this->sets_info[$sessionid]['user']);

        return true;
    }

    public function promote_ufo($handle, $type, $sessionid, $customdata) {
        if (empty($this->conn_ufo)) {
            return false;
        }
        foreach ($this->conn_ufo as $id => $ufo) {
            if ($ufo->handle == $handle) {
                // OK, got the id of the UFO, but what is it?

                if ($type & CHAT_SIDEKICK) {
                    // Is the main connection ready?
                    if (isset($this->conn_sets[$sessionid])) {
                        // Yes, so dispatch this sidekick now and be done with it.
                        $this->dispatch_sidekick($handle, $type, $sessionid, $customdata);
                        $this->dismiss_ufo($handle, false);
                    } else {
                        // No, so put it in the waiting list.
                        $this->trace('sidekick waiting');
                        $this->conn_side[$sessionid][] = array('type' => $type, 'handle' => $handle, 'customdata' => $customdata);
                    }
                    return true;
                }

                // If it's not a sidekick, at this point it can only be da man.

                if ($type & CHAT_CONNECTION) {
                    // This forces a new connection right now.
                    $this->trace('Incoming connection from '.$ufo->ip.':'.$ufo->port);

                    // Do we have such a connection active?
                    if (isset($this->conn_sets[$sessionid])) {
                        // Yes, so regrettably we cannot promote you.
                        $this->trace('Connection rejected: session '.$sessionid.' is already final');
                        $this->dismiss_ufo($handle, true, 'Your SID was rejected.');
                        return false;
                    }

                    // Join this with what we may have already.
                    $this->conn_half[$sessionid][$type] = $handle;

                    // Do the bookkeeping.
                    $this->promote_final($sessionid, $customdata);

                    // It's not a UFO anymore.
                    $this->dismiss_ufo($handle, false);

                    // Dispatch waiting sidekicks.
                    $this->process_sidekicks($sessionid);

                    return true;
                }
            }
        }
        return false;
    }

    public function dismiss_half($sessionid, $disconnect = true) {
        if (!isset($this->conn_half[$sessionid])) {
            return false;
        }
        if ($disconnect) {
            foreach ($this->conn_half[$sessionid] as $handle) {
                @socket_shutdown($handle);
                @socket_close($handle);
            }
        }
        unset($this->conn_half[$sessionid]);
        return true;
    }

    public function dismiss_set($sessionid) {
        if (!empty($this->conn_sets[$sessionid])) {
            foreach ($this->conn_sets[$sessionid] as $handle) {
                // Since we want to dismiss this, don't generate any errors if it's dead already.
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

    public function dismiss_ufo($handle, $disconnect = true, $message = null) {
        if (empty($this->conn_ufo)) {
            return false;
        }
        foreach ($this->conn_ufo as $id => $ufo) {
            if ($ufo->handle == $handle) {
                unset($this->conn_ufo[$id]);
                if ($disconnect) {
                    if (!empty($message)) {
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

    public function conn_accept() {
        $readsocket = array($this->listen_socket);
        $write = null;
        $except = null;
        $changed = socket_select($readsocket, $write, $except, 0, 0);

        if (!$changed) {
            return false;
        }
        $handle = socket_accept($this->listen_socket);
        if (!$handle) {
            return false;
        }

        $newconn = new ChatConnection($handle);
        $id = $this->new_ufo_id();
        $this->conn_ufo[$id] = $newconn;
    }

    public function conn_activity_ufo(&$handles) {
        $monitor = array();
        if (!empty($this->conn_ufo)) {
            foreach ($this->conn_ufo as $ufoid => $ufo) {
                // Avoid socket_select() warnings by preventing the check over invalid resources.
                if (is_resource($ufo->handle)) {
                    $monitor[$ufoid] = $ufo->handle;
                } else {
                    $this->dismiss_ufo($ufo->handle, false);
                }
            }
        }

        if (empty($monitor)) {
            $handles = array();
            return 0;
        }

        $a = null;
        $b = null;
        $retval = socket_select($monitor, $a, $b, null);
        $handles = $monitor;

        return $retval;
    }

    public function message_broadcast($message, $sender) {

        if (empty($this->conn_sets)) {
            return true;
        }

        $now = time();

        // First of all, mark this chatroom as having had activity now.
        $this->chatrooms[$message->chatid]['lastactivity'] = $now;

        foreach ($this->sets_info as $sessionid => $info) {
            // We need to get handles from users that are in the same chatroom, same group.
            if ($info['chatid'] == $message->chatid &&
              ($info['groupid'] == $message->groupid || $message->groupid == 0)) {

                // Simply give them the message.
                $output = chat_format_message_manually($message, $info['courseid'], $sender, $info['user']);
                if ($output !== false) {
                    $this->trace('Delivering message "'.$output->text.'" to ' .
                        $this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL]);

                    if ($output->beep) {
                        $playscript = '(function() { var audioElement = document.createElement("audio");' . "\n";
                        $playscript .= 'audioElement.setAttribute("src", "'.$this->_beepsoundsrc.'");' . "\n";
                        $playscript .= 'audioElement.play(); })();' . "\n";
                        $this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL],
                                          '<script>' . $playscript . '</script>');
                    }

                    if ($info['quirks'] & QUIRK_CHUNK_UPDATE) {
                        $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                        $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                        $output->html .= $GLOBALS['CHAT_DUMMY_DATA'];
                    }

                    if (!$this->write_data($this->conn_sets[$sessionid][CHAT_CONNECTION_CHANNEL], $output->html)) {
                        $this->disconnect_session($sessionid);
                    }
                }
            }
        }
    }

    public function disconnect_session($sessionid) {
        global $DB;

        $info = $this->sets_info[$sessionid];

        $DB->delete_records('chat_users', array('sid' => $sessionid));
        $msg = new stdClass;
        $msg->chatid = $info['chatid'];
        $msg->userid = $info['userid'];
        $msg->groupid = $info['groupid'];
        $msg->issystem = 1;
        $msg->message = 'exit';
        $msg->timestamp = time();

        $this->trace('User has disconnected, destroying uid '.$info['userid'].' with SID '.$sessionid, E_USER_WARNING);
        chat_send_chatmessage($info['chatuser'], $msg->message, true);

        // IMPORTANT, kill him BEFORE broadcasting, otherwise we 'll get infinite recursion!
        $latesender = $info['user'];
        $this->dismiss_set($sessionid);
        $this->message_broadcast($msg, $latesender);
    }

    public function fatal($message) {
        $message .= "\n";
        if ($this->_logfile) {
            $this->trace($message, E_USER_ERROR);
        }
        echo "FATAL ERROR:: $message\n";
        die();
    }

    public function init_sockets() {
        global $CFG;

        $this->trace('Setting up sockets');

        if (false === ($this->listen_socket = socket_create(AF_INET, SOCK_STREAM, 0))) {
            // Failed to create socket.
            $lasterr = socket_last_error();
            $this->fatal('socket_create() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        if (!socket_bind($this->listen_socket, $CFG->chat_serverip, $CFG->chat_serverport)) {
            // Failed to bind socket.
            $lasterr = socket_last_error();
            $this->fatal('socket_bind() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        if (!socket_listen($this->listen_socket, $CFG->chat_servermax)) {
            // Failed to get socket to listen.
            $lasterr = socket_last_error();
            $this->fatal('socket_listen() failed: '. socket_strerror($lasterr).' ['.$lasterr.']');
        }

        // Socket has been initialized and is ready.
        $this->trace('Socket opened on port '.$CFG->chat_serverport);

        // What exactly does this do? http://www.unixguide.net/network/socketfaq/4.5.shtml is still not enlightening enough for me.
        socket_set_option($this->listen_socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->listen_socket);
    }

    public function cli_switch($switch, $param = null) {
        switch($switch) { // LOL!
            case 'reset':
                // Reset sockets.
                $this->_resetsocket = true;
                return false;
            case 'start':
                // Start the daemon.
                $this->_readytogo = true;
                return false;
            break;
            case 'v':
                // Verbose mode.
                $this->_trace_level = E_ALL;
                return false;
            break;
            case 'l':
                // Use logfile.
                if (!empty($param)) {
                    $this->_logfile_name = $param;
                }
                $this->_logfile = @fopen($this->_logfile_name, 'a+');
                if ($this->_logfile == false) {
                    $this->fatal('Failed to open '.$this->_logfile_name.' for writing');
                }
                return false;
            default:
                // Unrecognized.
                $this->fatal('Unrecognized command line switch: '.$switch);
            break;
        }
        return false;
    }

}

$daemon = new ChatDaemon;
set_error_handler(array($daemon, 'error_handler'));

// Check the parameters.

unset($argv[0]);
$commandline = implode(' ', $argv);
if (strpos($commandline, '-') === false) {
    if (!empty($commandline)) {
        // We cannot have received any meaningful parameters.
        $daemon->fatal('Garbage in command line');
    }
} else {
    // Parse command line.
    $switches = preg_split('/(-{1,2}[a-zA-Z]+) */', $commandline, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

    // Taking advantage of the fact that $switches is indexed with incrementing numeric keys.
    // We will be using that to pass additional information to those switches who need it.
    $numswitches = count($switches);

    // Fancy way to give a "hyphen" boolean flag to each "switch".
    $switches = array_map(function($x) {
        return array("str" => $x, "hyphen" => (substr($x, 0, 1) == "-"));
    }, $switches);

    for ($i = 0; $i < $numswitches; ++$i) {

        $switch = $switches[$i]['str'];
        $params = ($i == $numswitches - 1 ? null :
                                            ($switches[$i + 1]['hyphen'] ? null : trim($switches[$i + 1]['str']))
                  );

        if (substr($switch, 0, 2) == '--') {
            // Double-hyphen switch.
            $daemon->cli_switch(strtolower(substr($switch, 2)), $params);
        } else if (substr($switch, 0, 1) == '-') {
            // Single-hyphen switch(es), may be more than one run together.
            $switch = substr($switch, 1); // Get rid of the "-".
            $len = strlen($switch);
            for ($j = 0; $j < $len; ++$j) {
                $daemon->cli_switch(strtolower(substr($switch, $j, 1)), $params);
            }
        }
    }
}

if (!$daemon->query_start()) {
    // For some reason we didn't start, so print out some info.
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

if (!function_exists('socket_set_option')) {
    echo "Error: Function socket_set_option() does not exist.\n";
    echo "Possibly PHP has not been compiled with --enable-sockets.\n\n";
    die();
}

$daemon->init_sockets();

$daemon->trace('Started Moodle chatd on port '.$CFG->chat_serverport.', listening socket '.$daemon->listen_socket, E_USER_WARNING);

// Clear the decks of old stuff.
$DB->delete_records('chat_users', array('version' => 'sockets'));

while (true) {
    $active = array();

    // First of all, let's see if any of our UFOs have identified itself.
    if ($daemon->conn_activity_ufo($active)) {
        foreach ($active as $handle) {
            $readsocket = array($handle);
            $write = null;
            $except = null;
            $changed = socket_select($readsocket, $write, $except, 0, 0);

            if ($changed > 0) {
                // Let's see what it has to say.

                $data = socket_read($handle, 2048); // Should be more than 512 to prevent empty pages and repeated messages!
                if (empty($data)) {
                    continue;
                }

                if (strlen($data) == 2048) { // If socket_read has more data, ignore all data.
                    $daemon->trace('UFO with '.$handle.': Data too long; connection closed', E_USER_WARNING);
                    $daemon->dismiss_ufo($handle, true, 'Data too long; connection closed');
                    continue;
                }

                // Ignore desktop browser fake "favorite icon" requests.
                if (strpos($data, 'GET /favicon.ico HTTP') === 0) {
                    // Known malformed data, drop it without any further notice.
                    continue;
                }

                if (!preg_match('/win=(chat|users|message|beep).*&chat_sid=([a-zA-Z0-9]*) HTTP/', $data, $info)) {
                    // Malformed data.
                    $daemon->trace('UFO with '.$handle.': Request with malformed data; connection closed', E_USER_WARNING);
                    $daemon->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                    continue;
                }

                $type      = $info[1];
                $sessionid = $info[2];

                $customdata = array();

                switch($type) {
                    case 'chat':
                        $type = CHAT_CONNECTION_CHANNEL;
                        $customdata['quirks'] = 0;
                        if (strpos($data, 'Safari')) {
                            $daemon->trace('Safari identified...', E_USER_WARNING);
                            $customdata['quirks'] += QUIRK_CHUNK_UPDATE;
                        }
                    break;
                    case 'users':
                        $type = CHAT_SIDEKICK_USERS;
                    break;
                    case 'beep':
                        $type = CHAT_SIDEKICK_BEEP;
                        if (!preg_match('/beep=([^&]*)[& ]/', $data, $info)) {
                            $daemon->trace('Beep sidekick did not contain a valid userid', E_USER_WARNING);
                            $daemon->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                            continue;
                        } else {
                            $customdata = array('beep' => intval($info[1]));
                        }
                    break;
                    case 'message':
                        $type = CHAT_SIDEKICK_MESSAGE;
                        if (!preg_match('/chat_message=([^&]*)[& ]chat_msgidnr=([^&]*)[& ]/', $data, $info)) {
                            $daemon->trace('Message sidekick did not contain a valid message', E_USER_WARNING);
                            $daemon->dismiss_ufo($handle, true, 'Request with malformed data; connection closed');
                            continue;
                        } else {
                            $customdata = array('message' => $info[1], 'index' => $info[2]);
                        }
                    break;
                    default:
                        $daemon->trace('UFO with '.$handle.': Request with unknown type; connection closed', E_USER_WARNING);
                        $daemon->dismiss_ufo($handle, true, 'Request with unknown type; connection closed');
                        continue;
                    break;
                }

                // OK, now we know it's something good. Promote it and pass it all the data it needs.
                $daemon->promote_ufo($handle, $type, $sessionid, $customdata);
                continue;
            }
        }
    }

    $now = time();

    // Clean up chatrooms with no activity as required.
    if ($now - $daemon->_last_idle_poll >= $daemon->_freq_poll_idle_chat) {
        $daemon->poll_idle_chats($now);
    }

    // Finally, accept new connections.
    $daemon->conn_accept();

    usleep($daemon->_time_rest_socket);
}

@socket_shutdown($daemon->listen_socket, 0);
die("\n\n-- terminated --\n");

