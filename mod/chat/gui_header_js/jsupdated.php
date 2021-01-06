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

/** jsupdated.php - notes by Martin Langhoff <martin@catalyst.net.nz>
 **
 ** This is an alternative version of jsupdate.php that acts
 ** as a long-running daemon. It will feed/stall/feed JS updates
 ** to the client. From the module configuration select "Stream"
 ** updates.
 **
 ** The client connection is not forever though. Once we reach
 ** CHAT_MAX_CLIENT_UPDATES, it will force the client to re-fetch it.
 **
 ** This buys us all the benefits that chatd has, minus the setup,
 ** as we are using apache to do the daemon handling.
 **
 **/

define('CHAT_MAX_CLIENT_UPDATES', 1000);
define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');
require_once('../lib.php');

// We are going to run for a long time.
// Avoid being terminated by php.
core_php_time_limit::raise();

$chatsid      = required_param('chat_sid',          PARAM_ALPHANUM);
$chatlasttime = optional_param('chat_lasttime',  0, PARAM_INT);
$chatlastrow  = optional_param('chat_lastrow',   1, PARAM_INT);
$chatlastid   = optional_param('chat_lastid',    0, PARAM_INT);

$url = new moodle_url('/mod/chat/gui_header_js/jsupdated.php', array('chat_sid' => $chatsid));
if ($chatlasttime !== 0) {
    $url->param('chat_lasttime', $chatlasttime);
}
if ($chatlastrow !== 1) {
    $url->param('chat_lastrow', $chatlastrow);
}
if ($chatlastid !== 1) {
    $url->param('chat_lastid', $chatlastid);
}
$PAGE->set_url($url);

if (!$chatuser = $DB->get_record('chat_users', array('sid' => $chatsid))) {
    print_error('notlogged', 'chat');
}

// Get the minimal course.
if (!$course = $DB->get_record('course', array('id' => $chatuser->course))) {
    print_error('invalidcourseid');
}

// Get the user theme and enough info to be used in chat_format_message() which passes it along to
// chat_format_message_manually() -- and only id and timezone are used.
// No optimisation here, it would break again in future!
if (!$user = $DB->get_record('user', array('id' => $chatuser->userid, 'deleted' => 0, 'suspended' => 0))) {
    print_error('invaliduser');
}
\core\session\manager::set_user($user);

// Setup course, lang and theme.
$PAGE->set_course($course);

// Force deleting of timed out users if there is a silence in room or just entering.
if ((time() - $chatlasttime) > $CFG->chat_old_ping) {
    // Must be done before chat_get_latest_message!
    chat_delete_old_users();
}

// Time to send headers, and lay out the basic JS updater page.
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

$refreshurl = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdated.php?".
              "chat_sid=$chatsid&chat_lasttime=$chatlasttime&chat_lastrow=$chatnewrow&chat_lastid=$chatlastid";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript">
        //<![CDATA[
        if (parent.msg.document.getElementById("msgStarted") == null) {
            parent.msg.document.close();
            parent.msg.document.open("text/html","replace");
            parent.msg.document.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
            parent.msg.document.write("<html><head>");
            parent.msg.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />");
            parent.msg.document.write("<base target=\"_blank\" />");
            parent.msg.document.write("</head><body class=\"mod-chat-gui_header_js course-<?php echo $chatuser->course ?>\" id=\"mod-chat-gui_header_js-jsupdate\"><div style=\"display: none\" id=\"msgStarted\">&nbsp;</div>");
        }
        //]]>
        </script>
    </head>
    <body>

<?php

// Ensure the HTML head makes it out there.
echo $CHAT_DUMMY_DATA;

for ($n = 0; $n <= CHAT_MAX_CLIENT_UPDATES; $n++) {

    // Ping first so we can later shortcut as needed.
    $chatuser->lastping = time();
    $DB->set_field('chat_users', 'lastping', $chatuser->lastping, array('id' => $chatuser->id));

    if ($message = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
        $chatnewlasttime = $message->timestamp;
        $chatnewlastid   = $message->id;
    } else {
        $chatnewlasttime = 0;
        $chatnewlastid   = 0;
        print " \n";
        print $CHAT_DUMMY_DATA;
        sleep($CFG->chat_refresh_room);
        continue;
    }

    $timenow    = time();

    $params = array('groupid' => $chatuser->groupid,
                    'lastid' => $chatlastid,
                    'lasttime' => $chatlasttime,
                    'chatid' => $chatuser->chatid);
    $groupselect = $chatuser->groupid ? " AND (groupid=:groupid OR groupid=0) " : "";

    $newcriteria = '';
    if ($chatlastid > 0) {
        $newcriteria = "id > :lastid";
    } else {
        if ($chatlasttime == 0) { // Display some previous messages.
            $chatlasttime = $timenow - $CFG->chat_old_ping; // TO DO - any better value?
        }
        $newcriteria = "timestamp > :lasttime";
    }

    $messages = $DB->get_records_select("chat_messages_current",
                                   "chatid = :chatid AND $newcriteria $groupselect", $params,
                                   "timestamp ASC");

    if ($messages) {
        $num = count($messages);
    } else {
        print " \n";
        print $CHAT_DUMMY_DATA;
        sleep($CFG->chat_refresh_room);
        continue;
    }

    print '<script type="text/javascript">' . "\n";
    print "//<![CDATA[\n\n";

    $chatnewrow = ($chatlastrow + $num) % 2;

    $refreshusers = false;
    $us = array ();
    if (($chatlasttime != $chatnewlasttime) and $messages) {

        $beep         = false;
        $refreshusers = false;
        foreach ($messages as $message) {
            $chatlastrow = ($chatlastrow + 1) % 2;
            $formatmessage = chat_format_message($message, $chatuser->course, $USER, $chatlastrow);
            if ($formatmessage->beep) {
                $beep = true;
            }
            if ($formatmessage->refreshusers) {
                $refreshusers = true;
            }
            $us[$message->userid] = $timenow - $message->timestamp;
            echo "parent.msg.document.write('".addslashes_js($formatmessage->html )."\\n');\n";

        }
        // From the last message printed.
        // A strange case where lack of closures is useful!
        $chatlasttime = $message->timestamp;
        $chatlastid   = $message->id;
    }

    if ($refreshusers) {
        echo "if (parent.users.document.anchors[0] != null) {" .
            "parent.users.location.href = parent.users.document.anchors[0].href;}\n";
    } else {
        foreach ($us as $uid => $lastping) {
            $min = (int) ($lastping / 60);
            $sec = $lastping - ($min * 60);
            $min = $min < 10 ? '0'.$min : $min;
            $sec = $sec < 10 ? '0'.$sec : $sec;
            $idle = $min.':'.$sec;
            echo "if (parent.users.document.getElementById('uidle{$uid}') != null) {".
                    "parent.users.document.getElementById('uidle{$uid}').innerHTML = '$idle';}\n";
        }
    }

    print <<<EOD
    if(parent.input){
        var autoscroll = parent.input.document.getElementById('auto');
        if(parent.msg && autoscroll && autoscroll.checked){
            parent.msg.scroll(1,5000000);
        }
    }
EOD;
    print "//]]>\n";
    print '</script>' . "\n\n";
    if ($beep) {
        print '<script> (function() {';
        print 'var audioElement = document.createElement("audio");';
        print 'audioElement.setAttribute("src", "../beep.mp3");';
        print 'audioElement.play(); })();';
        print '</script>';
    }
    print $CHAT_DUMMY_DATA;
    sleep($CFG->chat_refresh_room);
} // Here ends the for() loop.

// Here & should be written & :-D.
$refreshurl = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdated.php?";
$refreshurl .= "chat_sid=$chatsid&chat_lasttime=$chatlasttime&chat_lastrow=$chatnewrow&chat_lastid=$chatlastid";

print '<script type="text/javascript">' . "\n";
print "//<![CDATA[ \n\n";
print "location.href = '$refreshurl';\n";
print "//]]>\n";
print '</script>' . "\n\n";
?>

    </body>
</html>
