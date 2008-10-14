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

    //Get the minimal course
    if (!$course = get_record('course','id',$chatuser->course,'','','','','id,theme,lang')) {
        error('incorrect course id');
    }

    //Get the user theme and enough info to be used in chat_format_message() which passes it along to
    if (!$USER = get_record('user','id',$chatuser->userid)) { // no optimisation here, it would break again in future!
        error('User does not exist!');
    }
    $USER->description = '';

    //Setup course, lang and theme
    course_setup($course);

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

    // no &amp; in url, does not work in header!
    $refreshurl = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdate.php?chat_sid=$chat_sid&chat_lasttime=$chat_newlasttime&chat_lastrow=$chat_newrow";
    $refreshurlamp = "{$CFG->wwwroot}/mod/chat/gui_header_js/jsupdate.php?chat_sid=$chat_sid&amp;chat_lasttime=$chat_newlasttime&amp;chat_lastrow=$chat_newrow";

    header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Content-Type: text/html; charset=utf-8');
    header("Refresh: $CFG->chat_refresh_room; url=$refreshurl");

    /// required stylesheets
    $stylesheetshtml = '';
    foreach ($CFG->stylesheets as $stylesheet) {
        $stylesheetshtml .= '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
    }

    // use ob to be able to send Content-Length headers
    // needed for Keep-Alive to work
    ob_start();

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
        if (parent.msg && parent.msg.document.getElementById("msgStarted") == null) {
            parent.msg.document.close();
            parent.msg.document.open("text/html","replace");
            parent.msg.document.write("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">");
            parent.msg.document.write("<html><head>");
            parent.msg.document.write("<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />");
            parent.msg.document.write("<base target=\"_blank\" />");
            parent.msg.document.write("<?php echo addslashes_js($stylesheetshtml) ?>");
            parent.msg.document.write("<\/head><body class=\"mod-chat-gui_header_js course-<?php echo $chatuser->course ?>\" id=\"mod-chat-gui_header_js-jsupdate\"><div style=\"display: none\" id=\"msgStarted\">&nbsp;<\/div>");
        }
        <?php
        $beep = false;
        $refreshusers = false;
        $us = array ();
        if (($chat_lasttime != $chat_newlasttime) and $messages) {

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
                echo "if(parent.msg)";
                echo "parent.msg.document.write('".addslashes_js($formatmessage->html)."\\n');\n";
             }
        }

        $chatuser->lastping = time();
        set_field('chat_users', 'lastping', $chatuser->lastping, 'id', $chatuser->id  );

        if ($refreshusers) {
        ?>
        var link = parent.users.document.getElementById('refreshLink');
        if (link != null) {
            parent.users.location.href = link.href;
        }
        <?php
        } else {
            foreach($us as $uid=>$lastping) {
                $min = (int) ($lastping/60);
                $sec = $lastping - ($min*60);
                $min = $min < 10 ? '0'.$min : $min;
                $sec = $sec < 10 ? '0'.$sec : $sec;
                $idle = $min.':'.$sec;
                echo "if (parent.users && parent.users.document.getElementById('uidle{$uid}') != null) {".
                        "parent.users.document.getElementById('uidle{$uid}').innerHTML = '$idle';}\n";
            }
        }
        ?>
        if(parent.input){
            var autoscroll = parent.input.document.getElementById('auto');
            if(parent.msg && autoscroll && autoscroll.checked){
                parent.msg.scroll(1,5000000);
            }
        }
        //]]>
        </script>
    </head>
    <body>
       <?php
            if ($beep) {
                echo '<embed src="../beep.wav" autostart="true" hidden="true" name="beep" />';
            }
        ?>
       <a href="<?php echo $refreshurlamp ?>" name="refreshLink">Refresh link</a>
    </body>
</html>
<?php

// support HTTP Keep-Alive
header("Content-Length: " . ob_get_length() );
ob_end_flush();
exit;


?>
