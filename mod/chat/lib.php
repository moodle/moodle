<?php  // $Id$

/// Library of functions and constants for module chat
require_once($CFG->libdir.'/pagelib.php');

// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output
$CHAT_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body>\n\n".padding(200);

// The HTML head for the message window to start with (with js scrolling)
$CHAT_HTMLHEAD_JS = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head><script type="text/javascript">
//<![CDATA[
function move(){
    if (scroll_active)
        window.scroll(1,400000);
    window.setTimeout("move()",100);
}
var scroll_active = true;
move();
//]]>
</script>
</head>
<body onBlur="scroll_active = true" onFocus="scroll_active = false">
EOD;
$CHAT_HTMLHEAD_JS .= padding(200);

// The HTML code for standard empty pages (e.g. if a user was kicked out)
$CHAT_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body></body></html>";

// The HTML head for the message input page
$CHAT_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body>";

// The HTML code for the message input page, with JavaScript
$CHAT_HTMLHEAD_MSGINPUT_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title>\n<script type=\"text/javascript\">\n//<![CDATA[\nscroll_active = true;\nfunction empty_field_and_submit()\n{\ndocument.fdummy.arsc_message.value=document.f.arsc_message.value;\ndocument.fdummy.submit();\ndocument.f.arsc_message.focus();\ndocument.f.arsc_message.select();\nreturn false;\n}\n//]]>\n</script>\n</head><body bgcolor=\"#FFFFFF\" OnLoad=\"document.f.arsc_message.focus();document.f.arsc_message.select();\">";

// Dummy data that gets output to the browser as needed, in order to make it show output
$CHAT_DUMMY_DATA = padding(200);

function padding($n){
    $str = '';
    for($i=0; $i<$n; $i++){
        $str.="<!-- nix -->\n";
    }
    return $str;
}

function chat_add_instance($chat) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.

    $chat->timemodified = time();

    if ($returnid = insert_record("chat", $chat)) {

        $event = NULL;
        $event->name        = $chat->name;
        $event->description = $chat->intro;
        $event->courseid    = $chat->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'chat';
        $event->instance    = $returnid;
        $event->eventtype   = $chat->schedule;
        $event->timestart   = $chat->chattime;
        $event->timeduration = 0;

        add_event($event);
    }

    return $returnid;
}


function chat_update_instance($chat) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will update an existing instance with new data.

    $chat->timemodified = time();
    $chat->id = $chat->instance;


    if ($returnid = update_record("chat", $chat)) {

        $event = new object();

        if ($event->id = get_field('event', 'id', 'modulename', 'chat', 'instance', $chat->id)) {

            $event->name        = $chat->name;
            $event->description = $chat->intro;
            $event->timestart   = $chat->chattime;

            update_event($event);
        }
    }

    return $returnid;
}


function chat_delete_instance($id) {
/// Given an ID of an instance of this module,
/// this function will permanently delete the instance
/// and any data that depends on it.

    if (! $chat = get_record('chat', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('chat', 'id', $chat->id)) {
        $result = false;
    }
    if (! delete_records('chat_messages', 'chatid', $chat->id)) {
        $result = false;
    }
    if (! delete_records('chat_users', 'chatid', $chat->id)) {
        $result = false;
    }

    $pagetypes = page_import_types('mod/chat/');
    foreach($pagetypes as $pagetype) {
        if(!blocks_delete_all_on_page($pagetype, $chat->id)) {
            $result = false;
        }
    }

    if (! delete_records('event', 'modulename', 'chat', 'instance', $chat->id)) {
        $result = false;
    }

    return $result;
}

function chat_user_outline($course, $user, $mod, $chat) {
/// Return a small object with summary information about what a
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    $return = NULL;
    return $return;
}

function chat_user_complete($course, $user, $mod, $chat) {
/// Print a detailed representation of what a  user has done with
/// a given particular instance of this module, for user activity reports.

    return true;
}

function chat_print_recent_activity($course, $viewfullnames, $timestart) {
/// Given a course and a date, prints a summary of all chat rooms past and present
/// This function is called from course/lib.php: print_recent_activity()

    global $CFG, $USER;

    // this is approximate only, but it is really fast ;-)
    $timeout = $CFG->chat_old_ping * 10;

    if (!$mcms = get_records_sql("SELECT cm.id, MAX(chm.timestamp) AS lasttime
                                    FROM {$CFG->prefix}course_modules cm
                                         JOIN {$CFG->prefix}modules md        ON md.id = cm.module
                                         JOIN {$CFG->prefix}chat ch           ON ch.id = cm.instance
                                         JOIN {$CFG->prefix}chat_messages chm ON chm.chatid = ch.id
                                   WHERE chm.timestamp > $timestart AND ch.course = {$course->id} AND md.name = 'chat'
                                GROUP BY cm.id
                                ORDER BY lasttime ASC")) {
         return false;
    }

    $past     = array();
    $current  = array();
    $modinfo =& get_fast_modinfo($course); // reference needed because we might load the groups

    foreach ($mcms as $cmid=>$mcm) {
        if (!array_key_exists($cmid, $modinfo->cms)) {
            continue;
        }
        $cm = $modinfo->cms[$cmid];
        $cm->lasttime = $mcm->lasttime;
        if (!$modinfo->cms[$cm->id]->uservisible) {
            continue;
        }

        if (groups_get_activity_groupmode($cm) != SEPARATEGROUPS
         or has_capability('moodle/site:accessallgroups', get_context_instance(CONTEXT_MODULE, $cm->id))) {
            if ($timeout > time() - $cm->lasttime) {
                $current[] = $cm;
            } else {
                $past[] = $cm;
            }

            continue;
        }

        if (is_null($modinfo->groups)) {
            $modinfo->groups = groups_get_user_groups($course->id); // load all my groups and cache it in modinfo
        }

        // verify groups in separate mode
        if (!$mygroupids = $modinfo->groups[$cm->groupingid]) {
            continue;
        }

        // ok, last post was not for my group - we have to query db to get last message from one of my groups
        // only minor problem is that the order will not be correct
        $mygroupids = implode(',', $mygroupids);
        $cm->mygroupids = $mygroupids;

        if (!$mcm = get_record_sql("SELECT cm.id, MAX(chm.timestamp) AS lasttime
                                      FROM {$CFG->prefix}course_modules cm
                                           JOIN {$CFG->prefix}chat ch           ON ch.id = cm.instance
                                           JOIN {$CFG->prefix}chat_messages chm ON chm.chatid = ch.id
                                     WHERE chm.timestamp > $timestart AND cm.id = {$cm->id} AND
                                           (chm.groupid IN ($mygroupids) OR chm.groupid = 0)
                                  GROUP BY cm.id")) {
             continue;
        }

        $cm->lasttime = $mcm->lasttime;
        if ($timeout > time() - $cm->lasttime) {
            $current[] = $cm;
        } else {
            $past[] = $cm;
        }
    }

    if (!$past and !$current) {
        return false;
    }

    $strftimerecent = get_string('strftimerecent');

    if ($past) {
        print_headline(get_string('pastchats', 'chat').':');

        foreach ($past as $cm) {
            $link = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
            $date = userdate($cm->lasttime, $strftimerecent);
            echo '<div class="head"><div class="date">'.$date.'</div></div>';
            echo '<div class="info"><a href="'.$link.'">'.format_string($cm->name,true).'</a></div>';
        }
    }

    if ($current) {
        print_headline(get_string('currentchats', 'chat').':');

        $oldest = floor((time()-$CFG->chat_old_ping)/10)*10;  // better db caching

        $timeold    = time() - $CFG->chat_old_ping;
        $timeold    = floor($timeold/10)*10;  // better db caching
        $timeoldext = time() - ($CFG->chat_old_ping*10); // JSless gui_basic needs much longer timeouts
        $timeoldext = floor($timeoldext/10)*10;  // better db caching

        $timeout = "AND (chu.version<>'basic' AND chu.lastping>$timeold) OR (chu.version='basic' AND chu.lastping>$timeoldext)";

        foreach ($current as $cm) {
            //count users first
            if (isset($cm->mygroupids)) {
                $groupselect = "AND (chu.groupid IN ({$cm->mygroupids}) OR chu.groupid = 0)";
            } else {
                $groupselect = "";
            }

            if (!$users = get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email, u.picture
                                             FROM {$CFG->prefix}course_modules cm
                                             JOIN {$CFG->prefix}chat ch        ON ch.id = cm.instance
                                             JOIN {$CFG->prefix}chat_users chu ON chu.chatid = ch.id
                                             JOIN {$CFG->prefix}user u         ON u.id = chu.userid
                                            WHERE cm.id = {$cm->id} $timeout $groupselect
                                         GROUP BY u.id, u.firstname, u.lastname, u.email, u.picture")) {
            }

            $link = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
            $date = userdate($cm->lasttime, $strftimerecent);

            echo '<div class="head"><div class="date">'.$date.'</div></div>';
            echo '<div class="info"><a href="'.$link.'">'.format_string($cm->name,true).'</a></div>';
            echo '<div class="userlist">';
            if ($users) {
                echo '<ul>';
                    foreach ($users as $user) {
                        echo '<li>'.fullname($user, $viewfullnames).'</li>';
                    }
                echo '</ul>';
            }
            echo '</div>';
        }
    }

    return true;
}


function chat_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such
/// as sending out mail, toggling flags etc ...

    global $CFG;

    chat_update_chat_times();

    chat_delete_old_users();

    /// Delete old messages with a
    /// single SQL query.
    $subselect = "SELECT c.keepdays
                    FROM {$CFG->prefix}chat c
                   WHERE c.id = {$CFG->prefix}chat_messages.chatid";

    $sql = "DELETE
              FROM {$CFG->prefix}chat_messages
             WHERE ($subselect) > 0 AND timestamp < ( ".time()." -($subselect) * 24 * 3600)"; 

    execute_sql($sql, false);

    return true;
}

function chat_get_participants($chatid, $groupid=0) {
//Returns the users with data in one chat
//(users with records in chat_messages, students)

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}chat_messages c
                                 WHERE c.chatid = '$chatid' $groupselect
                                   AND u.id = c.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

function chat_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every chat event in the site is checked, else
// only chat events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid) {
        if (! $chats = get_records("chat", "course", $courseid)) {
            return true;
        }
    } else {
        if (! $chats = get_records("chat")) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'chat');

    foreach ($chats as $chat) {
        $event = NULL;
        $event->name        = addslashes($chat->name);
        $event->description = addslashes($chat->intro);
        $event->timestart   = $chat->chattime;

        if ($event->id = get_field('event', 'id', 'modulename', 'chat', 'instance', $chat->id)) {
            update_event($event);

        } else {
            $event->courseid    = $chat->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'chat';
            $event->instance    = $chat->id;
            $event->eventtype   = $chat->schedule;
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $chat->id);

            add_event($event);
        }
    }
    return true;
}


//////////////////////////////////////////////////////////////////////
/// Functions that require some SQL

function chat_get_users($chatid, $groupid=0, $groupingid=0) {

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }
    
    if (!empty($CFG->enablegroupings) && !(empty($groupingid))) {
        $groupingjoin = "INNER JOIN {$CFG->prefix}groups_members gm ON u.id = gm.userid
                         INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = gg.groupid AND gg.groupingid = $groupingid ";
        
    } else {
        $groupingjoin = '';
    }

    return get_records_sql("SELECT DISTINCT u.id, u.firstname, u.lastname, u.picture, c.lastmessageping, c.firstping, u.imagealt
                              FROM {$CFG->prefix}chat_users c
                                INNER JOIN {$CFG->prefix}user u ON u.id = c.userid
                                $groupingjoin
                             WHERE c.chatid = '$chatid'
                                $groupselect
                             ORDER BY c.firstping ASC");
}

function chat_get_latest_message($chatid, $groupid=0) {
/// Efficient way to extract just the latest message
/// Uses ADOdb directly instead of get_record_sql()
/// because the LIMIT command causes problems with
/// the developer debugging in there.

    global $db, $CFG;

    if ($groupid) {
        $groupselect = " AND (groupid='$groupid' OR groupid='0')";
    } else {
        $groupselect = "";
    }

    if (!$rs = $db->SelectLimit("SELECT *
                                 FROM {$CFG->prefix}chat_messages
                                 WHERE chatid = '$chatid' $groupselect
                                 ORDER BY timestamp DESC", 1)) {
        return false;
    }

    $result = rs_fetch_record($rs);
    
    rs_close($rs);

    return $result;
}


//////////////////////////////////////////////////////////////////////
// login if not already logged in

function chat_login_user($chatid, $version, $groupid, $course) {
    global $USER;
    if (($version != 'sockets') and $chatuser = get_record_select('chat_users', "chatid='$chatid' AND userid='$USER->id' AND groupid='$groupid'")) {
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = time();
        $chatuser->lang     = current_language();

        // Sometimes $USER->lastip is not setup properly
        // during login. Update with current value if possible
        // or provide a dummy value for the db
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
            if (empty($chatuser->ip)) {
                $chatuser->ip = '';
            }
        }

        if (($chatuser->course != $course->id)
         or ($chatuser->userid != $USER->id)) {
            return false;
        }
        if (!update_record('chat_users', $chatuser)) {
            return false;
        }
    } else {
        $chatuser = new object();
        $chatuser->chatid   = $chatid;
        $chatuser->userid   = $USER->id;
        $chatuser->groupid  = $groupid;
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = $chatuser->firstping = $chatuser->lastmessageping = time();
        $chatuser->sid      = random_string(32);
        $chatuser->course   = $course->id; //caching - needed for current_language too
        $chatuser->lang     = current_language(); //caching - to resource intensive to find out later

        // Sometimes $USER->lastip is not setup properly
        // during login. Update with current value if possible
        // or provide a dummy value for the db
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
            if (empty($chatuser->ip)) {
                $chatuser->ip = '';
            }
        }


        if (!insert_record('chat_users', $chatuser)) {
            return false;
        }

        if ($version == 'sockets') {
            // do not send 'enter' message, chatd will do it
        } else {
            $message = new object();
            $message->chatid    = $chatuser->chatid;
            $message->userid    = $chatuser->userid;
            $message->groupid   = $groupid;
            $message->message   = 'enter';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('chat_messages', $message)) {
                error('Could not insert a chat message!');
            }
        }
    }

    return $chatuser->sid;
}

function chat_delete_old_users() {
// Delete the old and in the way

    global $CFG;

    $timeold = time() - $CFG->chat_old_ping;
    $timeoldext = time() - ($CFG->chat_old_ping*10); // JSless gui_basic needs much longer timeouts

    $query = "(version<>'basic' AND lastping<'$timeold') OR (version='basic' AND lastping<'$timeoldext')";

    if ($oldusers = get_records_select('chat_users', $query) ) {
        delete_records_select('chat_users', $query);
        foreach ($oldusers as $olduser) {
            $message = new object();
            $message->chatid    = $olduser->chatid;
            $message->userid    = $olduser->userid;
            $message->groupid   = $olduser->groupid;
            $message->message   = 'exit';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('chat_messages', $message)) {
                error('Could not insert a chat message!');
            }
        }
    }
}


function chat_update_chat_times($chatid=0) {
/// Updates chat records so that the next chat time is correct

    $timenow = time();
    if ($chatid) {
        if (!$chats[] = get_record_select("chat", "id = '$chatid' AND chattime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    } else {
        if (!$chats = get_records_select("chat", "chattime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    }

    foreach ($chats as $chat) {
        unset($chat->name);
        unset($chat->intro);
        switch ($chat->schedule) {
            case 1: // Single event - turn off schedule and disable
                    $chat->chattime = 0;
                    $chat->schedule = 0;
                    break;
            case 2: // Repeat daily
                    while ($chat->chattime <= $timenow) {
                        $chat->chattime += 24 * 3600;
                    }
                    break;
            case 3: // Repeat weekly
                    while ($chat->chattime <= $timenow) {
                        $chat->chattime += 7 * 24 * 3600;
                    }
                    break;
        }
        update_record("chat", $chat);

        $event = NULL;           // Update calendar too
        $cond = "modulename='chat' AND instance = {$chat->id} 
                 AND timestart != {$chat->chattime}";
        if ($event->id = get_field_select('event', 'id', $cond)) {
            $event->timestart   = $chat->chattime;
            update_event($event);
        }
    }
}


function chat_format_message_manually($message, $courseid, $sender, $currentuser, $chat_lastrow=NULL) {
    global $CFG, $USER;

    $output = new object();
    $output->beep = false;       // by default
    $output->refreshusers = false; // by default

    // Use get_user_timezone() to find the correct timezone for displaying this message:
    // It's either the current user's timezone or else decided by some Moodle config setting
    // First, "reset" $USER->timezone (which could have been set by a previous call to here)
    // because otherwise the value for the previous $currentuser will take precedence over $CFG->timezone
    $USER->timezone = 99;
    $tz = get_user_timezone($currentuser->timezone);

    // Before formatting the message time string, set $USER->timezone to the above.
    // This will allow dst_offset_on (called by userdate) to work correctly, otherwise the
    // message times appear off because DST is not taken into account when it should be.
    $USER->timezone = $tz;
    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'chat'), $tz);

    $message->picture = print_user_picture($sender->id, 0, $sender->picture, false, true, false);
    if ($courseid) {
        $message->picture = "<a onclick=\"window.open('$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid')\" href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";
    }

    //Calculate the row class
    if ($chat_lastrow !== NULL) {
        $rowclass = ' class="r'.$chat_lastrow.'" ';
    } else {
        $rowclass = '';
    }

    // Start processing the message

    if(!empty($message->system)) {
        // System event
        $output->text = $message->strtime.': '.get_string('message'.$message->message, 'chat', fullname($sender));
        $output->html  = '<table class="chat-event"><tr'.$rowclass.'><td class="picture">'.$message->picture.'</td><td class="text">';
        $output->html .= '<span class="event">'.$output->text.'</span></td></tr></table>';
        $output->basic = '<dl><dt class="event">'.$message->strtime.': '.get_string('message'.$message->message, 'chat', fullname($sender)).'</dt></dl>'; 

        if($message->message == 'exit' or $message->message == 'enter') {
            $output->refreshusers = true; //force user panel refresh ASAP
        }
        return $output;
    }

    // It's not a system event

    $text = $message->message;

    /// Parse the text to clean and filter it

    $options = new object();
    $options->para = false;
    $text = format_text($text, FORMAT_MOODLE, $options, $courseid);

    // And now check for special cases
    $special = false;

    if (substr($text, 0, 5) == 'beep ') {
        /// It's a beep! 
        $special = true;
        $beepwho = trim(substr($text, 5));

        if ($beepwho == 'all') {   // everyone
            $outinfo = $message->strtime.': '.get_string('messagebeepseveryone', 'chat', fullname($sender));
            $outmain = '';
            $output->beep = true;  // (eventually this should be set to
                                   //  to a filename uploaded by the user)

        } else if ($beepwho == $currentuser->id) {  // current user
            $outinfo = $message->strtime.': '.get_string('messagebeepsyou', 'chat', fullname($sender));
            $outmain = '';
            $output->beep = true;

        } else {  //something is not caught?
            return false;
        }
    } else if (substr($text, 0, 1) == '/') {     /// It's a user command
        if (trim(substr($text, 0, 4)) == '/me') {
            $special = true;
            $outinfo = $message->strtime;
            $outmain = $sender->firstname.' '.substr($text, 4);
        }
    }

    if(!$special) {
        $outinfo = $message->strtime.' '.$sender->firstname;
        $outmain = $text;
    }

    /// Format the message as a small table

    $output->text  = strip_tags($outinfo.': '.$outmain);

    $output->html  = "<table class=\"chat-message\"><tr$rowclass><td class=\"picture\" valign=\"top\">$message->picture</td><td class=\"text\">";
    $output->html .= "<span class=\"title\">$outinfo</span>";
    if ($outmain) {
        $output->html .= ": $outmain";
        $output->basic = '<dl><dt class="title">'.$outinfo.':</dt><dd class="text">'.$outmain.'</dd></dl>'; 
    } else {
        $output->basic = '<dl><dt class="title">'.$outinfo.'</dt></dl>'; 
    }
    $output->html .= "</td></tr></table>";
    return $output;
}

function chat_format_message($message, $courseid, $currentuser, $chat_lastrow=NULL) {
/// Given a message object full of information, this function
/// formats it appropriately into text and html, then
/// returns the formatted data.

    static $users;     // Cache user lookups

    if (isset($users[$message->userid])) {
        $user = $users[$message->userid];
    } else if ($user = get_record('user', 'id', $message->userid, '','','','','id,picture,firstname,lastname')) {
        $users[$message->userid] = $user;
    } else {
        return NULL;
    }
    return chat_format_message_manually($message, $courseid, $user, $currentuser, $chat_lastrow);
}

function chat_get_view_actions() {
    return array('view','view all','report');
}

function chat_get_post_actions() {
    return array('talk');
}

function chat_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$chats = get_all_instances_in_courses('chat',$courses)) {
        return;
    }

    $strchat = get_string('modulename', 'chat');
    $strnextsession  = get_string('nextsession', 'chat');

    foreach ($chats as $chat) {
        if ($chat->chattime and $chat->schedule) {  // A chat is scheduled
            $str = '<div class="chat overview"><div class="name">'.
                   $strchat.': <a '.($chat->visible?'':' class="dimmed"').
                   ' href="'.$CFG->wwwroot.'/mod/chat/view.php?id='.$chat->coursemodule.'">'.
                   $chat->name.'</a></div>';
            $str .= '<div class="info">'.$strnextsession.': '.userdate($chat->chattime).'</div></div>';

            if (empty($htmlarray[$chat->course]['chat'])) {
                $htmlarray[$chat->course]['chat'] = $str;
            } else {
                $htmlarray[$chat->course]['chat'] .= $str;
            }
        }
    }
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the chat.
 * @param $mform form passed by reference
 */
function chat_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'chatheader', get_string('modulenameplural', 'chat'));
    $mform->addElement('advcheckbox', 'reset_chat', get_string('removemessages','chat'));
}

/**
 * Course reset form defaults.
 */
function chat_reset_course_form_defaults($course) {
    return array('reset_chat'=>1);
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * chat messages for course $data->courseid.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function chat_reset_userdata($data) {
    global $CFG;

    $componentstr = get_string('modulenameplural', 'chat');
    $status = array();

    if (!empty($data->reset_chat)) {
        $chatessql = "SELECT ch.id
                        FROM {$CFG->prefix}chat ch
                       WHERE ch.course={$data->courseid}";

        delete_records_select('chat_messages', "chatid IN ($chatessql)");
        delete_records_select('chat_users', "chatid IN ($chatessql)");
        $status[] = array('component'=>$componentstr, 'item'=>get_string('removemessages', 'chat'), 'error'=>false);
    }

    /// updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('chat', array('chattime'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 */
function chat_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}

?>
