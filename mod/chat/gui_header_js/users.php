<?php  // $Id$

    $nomoodlecookie = true;     // Session not needed!

    include('../../../config.php');
    include('../lib.php');

    $chat_sid   = required_param('chat_sid', PARAM_ALPHANUM);
    $beep       = optional_param('beep', 0, PARAM_INT);  // beep target

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

    $courseid = $chatuser->course;

    if (!$cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $courseid)) {
        error('Course Module ID was incorrect');
    }

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
    set_field('chat_users', 'lastping', $chatuser->lastping, 'id', $chatuser->id  );

    $refreshurl = "users.php?chat_sid=$chat_sid";

    /// Get list of users

    if (!$chatusers = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid)) {
        print_error('errornousers', 'chat');
    }

    ob_start();
    ?>
    <script type="text/javascript">
    //<![CDATA[
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
    //]]>
    </script>
    <?php


    /// Print headers
    $meta = ob_get_clean();


    // Use ob to support Keep-Alive
    ob_start();
    print_header('', '', '', '', $meta, false, '', '', false, 'onload="start()" onunload="stop()"');


    /// Print user panel body
    $timenow    = time();
    $stridle    = get_string('idle', 'chat');
    $strbeep    = get_string('beep', 'chat');


    echo '<div style="display: none"><a href="'.$refreshurl.'" id="refreshLink">Refresh link</a></div>';
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
        echo '</td></tr>';
    }
    // added 2 </div>s, xhtml strict complaints
    echo '</table>';
    print_footer('empty');

    //
    // Support HTTP Keep-Alive by printing Content-Length
    //
    // If the user pane is refreshing often, using keepalives 
    // is lighter on the server and faster for most clients. 
    //
    // Apache is normally configured to have a 15s timeout on 
    // keepalives, so let's observe that. Unfortunately, we cannot
    // autodetect the keepalive timeout. 
    //
    // Using keepalives when the refresh is longer than the timeout
    // wastes server resources keeping an apache child around on a  
    // connection that will timeout. So we don't. 
    if ($CFG->chat_refresh_userlist < 15) {    
        header("Content-Length: " . ob_get_length() );
        ob_end_flush(); 
    }

    exit; // no further output


?>
