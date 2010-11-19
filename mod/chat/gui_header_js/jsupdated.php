<?php

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
define('NO_MOODLE_COOKIES', true); // session not used here
define('NO_OUTPUT_BUFFERING', true);

require('../../../config.php');
require('../lib.php');

// we are going to run for a long time
// avoid being terminated by php
@set_time_limit(0);

$chat_sid      = required_param('chat_sid',          PARAM_ALPHANUM);
$chat_lasttime = optional_param('chat_lasttime',  0, PARAM_INT);
$chat_lastrow  = optional_param('chat_lastrow',   1, PARAM_INT);
$chat_lastid   = optional_param('chat_lastid',    0, PARAM_INT);

$url = new moodle_url('/mod/chat/gui_header_js/jsupdated.php', array('chat_sid'=>$chat_sid));
if ($chat_lasttime !== 0) {
    $url->param('chat_lasttime', $chat_lasttime);
}
if ($chat_lastrow !== 1) {
    $url->param('chat_lastrow', $chat_lastrow);
}
if ($chat_lastid !== 1) {
    $url->param('chat_lastid', $chat_lastid);
}
$PAGE->set_url($url);

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    print_error('notlogged', 'chat');
}

//Get the minimal course
if (!$course = $DB->get_record('course', array('id'=>$chatuser->course))) {
    print_error('invalidcourseid');
}

//Get the user theme and enough info to be used in chat_format_message() which passes it along to
// chat_format_message_manually() -- and only id and timezone are used.
if (!$USER = $DB->get_record('user', array('id'=>$chatuser->userid))) { // no optimisation here, it would break again in future!
    print_error('invaliduser');
}
$USER->description = '';

//Setup course, lang and theme
$PAGE->set_course($course);

// force deleting of timed out users if there is a silence in room or just entering
if ((time() - $chat_lasttime) > $CFG->chat_old_ping) {
    // must be done before chat_get_latest_message!!!
    chat_delete_old_users();
}

//
// Time to send headers, and lay out the basic JS updater page
//
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

/// required stylesheets
$stylesheetshtml = '';
/*foreach ($CFG->stylesheets as $stylesheet) {
    //TODO: MDL-21120
    $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
}*/

$refreshurl = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdated.php?chat_sid=$chat_sid&chat_lasttime=$chat_lasttime&chat_lastrow=$chat_newrow&chat_lastid=$chat_lastid";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <script type="text/javascript">
        //<![CDATA[
        function safari_refresh() {
            self.location.href= '<?php echo $refreshurl;?>';
        }
        var issafari = false;
        if(window.devicePixelRatio){
            issafari = true;
            setTimeout('safari_refresh()', <?php echo $CFG->chat_refresh_room*1000;?>);
        }
        if (parent.msg.document.getElementById("msgStarted") == null) {
            parent.msg.document.close();
            parent.msg.document.open("text/html","replace");
            parent.msg.document.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
            parent.msg.document.write("<html><head>");
            parent.msg.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />");
            parent.msg.document.write("<base target=\"_blank\" />");
            parent.msg.document.write("<?php echo addslashes_js($stylesheetshtml) ?>");
            parent.msg.document.write("</head><body class=\"mod-chat-gui_header_js course-<?php echo $chatuser->course ?>\" id=\"mod-chat-gui_header_js-jsupdate\"><div style=\"display: none\" id=\"msgStarted\">&nbsp;</div>");
        }
        //]]>
        </script>
    </head>
    <body>

<?php

    // Ensure the HTML head makes it out there
    echo $CHAT_DUMMY_DATA;

    for ($n=0; $n <= CHAT_MAX_CLIENT_UPDATES; $n++) {

        // ping first so we can later shortcut as needed.
        $chatuser->lastping = time();
        $DB->set_field('chat_users', 'lastping', $chatuser->lastping, array('id'=>$chatuser->id));

        if ($message = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
            $chat_newlasttime = $message->timestamp;
            $chat_newlastid   = $message->id;
        } else {
            $chat_newlasttime = 0;
            $chat_newlastid   = 0;
            print " \n";
            print $CHAT_DUMMY_DATA;
            sleep($CFG->chat_refresh_room);
            continue;
        }

        $timenow    = time();

        $params = array('groupid'=>$chatuser->groupid, 'lastid'=>$chat_lastid, 'lasttime'=>$chat_lasttime, 'chatid'=>$chatuser->chatid);
        $groupselect = $chatuser->groupid ? " AND (groupid=:groupid OR groupid=0) " : "";

        $newcriteria = '';
        if ($chat_lastid > 0) {
            $newcriteria = "id > :lastid";
        } else {
            if ($chat_lasttime == 0) { //display some previous messages
                $chat_lasttime = $timenow - $CFG->chat_old_ping; //TO DO - any better value??
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
            $num = 0;
        }

        print '<script type="text/javascript">' . "\n";
        print "//<![CDATA[\n\n";

        $chat_newrow = ($chat_lastrow + $num) % 2;

        $refreshusers = false;
        $us = array ();
        if (($chat_lasttime != $chat_newlasttime) and $messages) {

            $beep         = false;
            $refreshusers = false;
            foreach ($messages as $message) {
                $chat_lastrow = ($chat_lastrow + 1) % 2;
                $formatmessage = chat_format_message($message, $chatuser->course, $USER, $chat_lastrow);
                if ($formatmessage->beep) {
                    $beep = true;
                }
                if ($formatmessage->refreshusers) {
                    $refreshusers = true;
                }
                $us[$message->userid] = $timenow - $message->timestamp;
                echo "parent.msg.document.write('".addslashes_js($formatmessage->html )."\\n');\n";

            }
            // from the last message printed...
            // a strange case where lack of closures is useful!
            $chat_lasttime = $message->timestamp;
            $chat_lastid   = $message->id;
        }

        if ($refreshusers) {
            echo "if (parent.users.document.anchors[0] != null) {" .
                "parent.users.location.href = parent.users.document.anchors[0].href;}\n";
        } else {
            foreach($us as $uid=>$lastping) {
                $min = (int) ($lastping/60);
                $sec = $lastping - ($min*60);
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
            print '<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />';
        }
        print $CHAT_DUMMY_DATA;
        sleep($CFG->chat_refresh_room);
    } // here ends the for() loop

    // here & should be written & :-D
    $refreshurl = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdated.php?chat_sid=$chat_sid&chat_lasttime=$chat_lasttime&chat_lastrow=$chat_newrow&chat_lastid=$chat_lastid";
    print '<script type="text/javascript">' . "\n";
    print "//<![CDATA[ \n\n";
    print "location.href = '$refreshurl';\n";
    print "//]]>\n";
    print '</script>' . "\n\n";

?>

    </body>
</html>
