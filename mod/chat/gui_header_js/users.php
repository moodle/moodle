<?php  // $Id$

    $nomoodlecookie = true;     // Session not needed!

    include('../../../config.php');
    include('../lib.php');

    $chat_sid   = required_param('chat_sid', PARAM_ALPHANUM);
    $beep       = optional_param('beep', 0, PARAM_INT);  // beep target

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

    $courseid = $chatuser->course;

    if ($beep) {
        $message->chatid    = $chatuser->chatid;
        $message->userid    = $chatuser->userid;
        $message->groupid   = $chatuser->groupid;
        $message->message   = "beep $beep";
        $message->system    = 0;
        $message->timestamp = time();

        if (!insert_record('chat_messages', $message)) {
            error('Could not insert a chat message!');
        }

        $chatuser->lastmessageping = time();          // A beep is a ping  ;-)
    }

    $chatuser->lastping = time();
    update_record('chat_users', $chatuser);

    $refreshurl = "users.php?chat_sid=$chat_sid";

    /// Get list of users

    if (!$chatusers = chat_get_users($chatuser->chatid, $chatuser->groupid)) {
        error(get_string('errornousers', 'chat'));
    }

    ob_start();
    ?>
    <script type="text/javascript">
    <!--
    var timer = null
    var f = 1; //seconds
    var uidles = new Array(<?php echo count($chatusers) ?>);
    <?php
        $i = 0;
        foreach ($chatusers as $chatuser) {
            echo "uidles[$i] = 'uidle{$chatuser->id}';\n";
            $i++;
        }
    ?>

    function stop() {
        clearTimeout(timer)
    }

    function start() {
        timer = setTimeout("update()", f*1000);
    }

    function update() {
        for(i=0; i<uidles.length; i++) {
            el = document.getElementById(uidles[i]);
            if (el != null) {
                parts = el.innerHTML.split(":");
                time = f + (parseInt(parts[0], 10)*60) + parseInt(parts[1], 10);
                min = Math.floor(time/60);
                sec = time % 60;
                el.innerHTML = ((min < 10) ? "0" : "") + min + ":" + ((sec < 10) ? "0" : "") + sec;
            }
        }
        timer = setTimeout("update()", f*1000);
    }
    // -->
    </script>
    <?php


    /// Print headers
    $meta = ob_get_clean();
    print_header('', '', '', '', $meta, false, '', '', false, 'onload="start()" onunload="stop()"');


    /// Print user panel body
    $timenow    = time();
    $stridle    = get_string('idle', 'chat');
    $strbeep    = get_string('beep', 'chat');


    echo '<div style="display: none"><a href="'.$refreshurl.'" name="refreshLink">Refresh link</a></div>';
    echo '<table width="100%">';
    foreach ($chatusers as $chatuser) {
        $lastping = $timenow - $chatuser->lastmessageping;
        $min = (int) ($lastping/60);
        $sec = $lastping - ($min*60);
        $min = $min < 10 ? '0'.$min : $min;
        $sec = $sec < 10 ? '0'.$sec : $sec;
        $idle = $min.':'.$sec;
        echo '<tr><td width="35">';
        echo "<a target=\"_blank\" onClick=\"return openpopup('/user/view.php?id=$chatuser->id&amp;course=$courseid','user$chatuser->id','');\" href=\"$CFG->wwwroot/user/view.php?id=$chatuser->id&amp;course=$courseid\">";
        print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
        echo '</a></td><td valign="center">';
        echo '<p><font size="1">';
        echo fullname($chatuser).'<br />';
        echo "<span class=\"dimmed_text\">$stridle <span name=\"uidles\" id=\"uidle{$chatuser->id}\">$idle</span></span>";
        echo " <a href=\"users.php?chat_sid=$chat_sid&amp;beep=$chatuser->id\">$strbeep</a>";
        echo '</font></p>';
        echo '<td></tr>';
    }
    echo '</table></body></html>';
?>
