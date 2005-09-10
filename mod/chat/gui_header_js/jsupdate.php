<?php  // $Id$

    $nomoodlecookie = true;     // Session not needed!

    require('../../../config.php');
    require('../lib.php');

    $chat_sid      = required_param('chat_sid', PARAM_ALPHANUM);
    $chat_lasttime = optional_param('chat_lasttime', 0, PARAM_INT);
    $chat_lastrow  = optional_param('chat_lastrow', 1, PARAM_INT);

    if (!$chatuser = get_record('chat_users', 'sid', $chat_sid)) {
        error('Not logged in!');
    }

    //Get the course theme
    $course = get_record('course','id',$chatuser->course,'','','','','id,theme');
    //Set the course theme if necessary
    if (!empty($course->theme)) {
        if (!empty($CFG->allowcoursethemes)) {
            $CFG->coursetheme = $course->theme;
        }
    }
    //Get the user theme
    $USER = get_record('user','id',$chatuser->userid,'','','','','id, theme');

    //Adjust the prefered theme (main, course, user)
    theme_setup();

    chat_force_language($chatuser->lang);

    // force deleting of timed out users if there is a silence in room or just entering
    if ((time() - $chat_lasttime) > $CFG->chat_old_ping) {
        // must be done before chat_get_latest_message!!!
        chat_delete_old_users();
    }

    if ($message = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
        $chat_newlasttime = $message->timestamp;
    } else {
        $chat_newlasttime = 0;
    }

    if ($chat_lasttime == 0) { //display some previous messages
        $chat_lasttime = time() - $CFG->chat_old_ping; //TO DO - any better value??
    }

    $timenow    = time();

    $groupselect = $chatuser->groupid ? " AND (groupid='".$chatuser->groupid."' OR groupid='0') " : "";

    $messages = get_records_select("chat_messages",
                        "chatid = '$chatuser->chatid' AND timestamp > '$chat_lasttime' $groupselect",
                        "timestamp ASC");

    if ($messages) {
        $num = count($messages);
    } else {
        $num = 0;
    }

    $chat_newrow = ($chat_lastrow + $num) % 2;

    $refreshurl = "jsupdate.php?chat_sid=$chat_sid&chat_lasttime=$chat_newlasttime&chat_lastrow=$chat_newrow"; // no &amp; in url, does not work in header!

    header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: text/html');
    header("Refresh: $CFG->chat_refresh_room; url=$refreshurl");

    /// required stylesheets
    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel=\\"stylesheet\\" type=\\"text/css\\" href=\\"'.$stylesheet.'\\" />';
    }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=<?php echo get_string('thischarset'); ?>" />
        <script type="text/javascript">
        <!--
        if (parent.msg.document.getElementById("msgStarted") == null) {
            parent.msg.document.close();
            parent.msg.document.open("text/html","replace");
            parent.msg.document.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
            parent.msg.document.write("<html><head>");
            parent.msg.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=<?php echo get_string('thischarset'); ?>\" />");
            parent.msg.document.write("<base target=\"_blank\" />");
            parent.msg.document.write("<?php echo $stylesheetshtml ?>");
            parent.msg.document.write("</head><body class=\"mod-chat-gui_header_js course-<?php echo $chatuser->course ?>\" id=\"mod-chat-gui_header_js-jsupdate\"><div style=\"display: none\" id=\"msgStarted\">&nbsp;</div>");
        }
        <?php
        $beep = false;
        $refreshusers = false;
        $us = array ();
        if (($chat_lasttime != $chat_newlasttime) and $messages) {

            if (!$currentuser = get_record('user', 'id', $chatuser->userid)) {
                error('User does not exist!');
            }
            $currentuser->description = '';

            foreach ($messages as $message) {
                $chat_lastrow = ($chat_lastrow + 1) % 2;
                $formatmessage = chat_format_message($message, $chatuser->course, $currentuser, $chat_lastrow);
                if ($formatmessage->beep) {
                     $beep = true;
                }
                if ($formatmessage->refreshusers) {
                     $refreshusers = true;
                }
                $us[$message->userid] = $timenow - $message->timestamp;
                echo "parent.msg.document.write('".addslashes($formatmessage->html)."\\n');\n";
             }
        }

        $chatuser->lastping = time();
        update_record('chat_users', $chatuser);

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
        ?>
        parent.msg.scroll(1,5000000);
        // -->
        </script>
    </head>
    <body>
       <?php
            if ($beep) {
                echo '<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />';
            }
        ?>
       <a href="<? echo $refreshurl ?>" name="refreshLink">Refresh link</a>
    </body>
</html>
