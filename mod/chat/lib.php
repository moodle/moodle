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
 * Library of functions and constants for module chat
 *
 * @package   mod-chat
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/calendar/lib.php');

// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output
global $CHAT_HTMLHEAD;
$CHAT_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body>\n\n".padding(200);

// The HTML head for the message window to start with (with js scrolling)
global $CHAT_HTMLHEAD_JS;
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
global $CHAT_HTMLHEAD_JS;
$CHAT_HTMLHEAD_JS .= padding(200);

// The HTML code for standard empty pages (e.g. if a user was kicked out)
global $CHAT_HTMLHEAD_OUT;
$CHAT_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body></body></html>";

// The HTML head for the message input page
global $CHAT_HTMLHEAD_MSGINPUT;
$CHAT_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body>";

// The HTML code for the message input page, with JavaScript
global $CHAT_HTMLHEAD_MSGINPUT_JS;
$CHAT_HTMLHEAD_MSGINPUT_JS = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
    <head><title>Message Input</title>
    <script type="text/javascript">
    //<![CDATA[
    scroll_active = true;
    function empty_field_and_submit(){
        document.fdummy.arsc_message.value=document.f.arsc_message.value;
        document.fdummy.submit();
        document.f.arsc_message.focus();
        document.f.arsc_message.select();
        return false;
    }
    //]]>
    </script>
    </head><body OnLoad="document.f.arsc_message.focus();document.f.arsc_message.select();">;
EOD;

// Dummy data that gets output to the browser as needed, in order to make it show output
global $CHAT_DUMMY_DATA;
$CHAT_DUMMY_DATA = padding(200);

/**
 * @param int $n
 * @return string
 */
function padding($n){
    $str = '';
    for($i=0; $i<$n; $i++){
        $str.="<!-- nix -->\n";
    }
    return $str;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $chat
 * @return int
 */
function chat_add_instance($chat) {
    global $DB;

    $chat->timemodified = time();

    $returnid = $DB->insert_record("chat", $chat);

    $event = new stdClass();
    $event->name        = $chat->name;
    $event->description = format_module_intro('chat', $chat, $chat->coursemodule);
    $event->courseid    = $chat->course;
    $event->groupid     = 0;
    $event->userid      = 0;
    $event->modulename  = 'chat';
    $event->instance    = $returnid;
    $event->eventtype   = 'chattime';
    $event->timestart   = $chat->chattime;
    $event->timeduration = 0;

    calendar_event::create($event);

    return $returnid;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @global object
 * @param object $chat
 * @return bool
 */
function chat_update_instance($chat) {
    global $DB;

    $chat->timemodified = time();
    $chat->id = $chat->instance;


    $DB->update_record("chat", $chat);

    $event = new stdClass();

    if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'chat', 'instance'=>$chat->id))) {

        $event->name        = $chat->name;
        $event->description = format_module_intro('chat', $chat, $chat->coursemodule);
        $event->timestart   = $chat->chattime;

        $calendarevent = calendar_event::load($event->id);
        $calendarevent->update($event);
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @global object
 * @param int $id
 * @return bool
 */
function chat_delete_instance($id) {
    global $DB;


    if (! $chat = $DB->get_record('chat', array('id'=>$id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here

    if (! $DB->delete_records('chat', array('id'=>$chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_messages', array('chatid'=>$chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_messages_current', array('chatid'=>$chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_users', array('chatid'=>$chat->id))) {
        $result = false;
    }

    if (! $DB->delete_records('event', array('modulename'=>'chat', 'instance'=>$chat->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * <code>
 * $return->time = the time they did it
 * $return->info = a short text description
 * </code>
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $chat
 * @return void
 */
function chat_user_outline($course, $user, $mod, $chat) {
    return NULL;
}

/**
 * Print a detailed representation of what a  user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $chat
 * @return bool
 */
function chat_user_complete($course, $user, $mod, $chat) {
    return true;
}

/**
 * Given a course and a date, prints a summary of all chat rooms past and present
 * This function is called from course/lib.php: print_recent_activity()
 *
 * @global object
 * @global object
 * @global object
 * @param object $course
 * @param bool $viewfullnames
 * @param int|string $timestart Timestamp
 * @return bool
 */
function chat_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;

    // this is approximate only, but it is really fast ;-)
    $timeout = $CFG->chat_old_ping * 10;

    if (!$mcms = $DB->get_records_sql("SELECT cm.id, MAX(chm.timestamp) AS lasttime
                                         FROM {course_modules} cm
                                         JOIN {modules} md        ON md.id = cm.module
                                         JOIN {chat} ch           ON ch.id = cm.instance
                                         JOIN {chat_messages} chm ON chm.chatid = ch.id
                                        WHERE chm.timestamp > ? AND ch.course = ? AND md.name = 'chat'
                                     GROUP BY cm.id
                                     ORDER BY lasttime ASC", array($timestart, $course->id))) {
         return false;
    }

    $past     = array();
    $current  = array();
    $modinfo = get_fast_modinfo($course); // reference needed because we might load the groups

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
         or has_capability('moodle/site:accessallgroups', context_module::instance($cm->id))) {
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

        if (!$mcm = $DB->get_record_sql("SELECT cm.id, MAX(chm.timestamp) AS lasttime
                                           FROM {course_modules} cm
                                           JOIN {chat} ch           ON ch.id = cm.instance
                                           JOIN {chat_messages_current} chm ON chm.chatid = ch.id
                                          WHERE chm.timestamp > ? AND cm.id = ? AND
                                                (chm.groupid IN ($mygroupids) OR chm.groupid = 0)
                                       GROUP BY cm.id", array($timestart, $cm->id))) {
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
        echo $OUTPUT->heading(get_string("pastchats", 'chat').':');

        foreach ($past as $cm) {
            $link = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
            $date = userdate($cm->lasttime, $strftimerecent);
            echo '<div class="head"><div class="date">'.$date.'</div></div>';
            echo '<div class="info"><a href="'.$link.'">'.format_string($cm->name,true).'</a></div>';
        }
    }

    if ($current) {
        echo $OUTPUT->heading(get_string("currentchats", 'chat').':');

        $oldest = floor((time()-$CFG->chat_old_ping)/10)*10;  // better db caching

        $timeold    = time() - $CFG->chat_old_ping;
        $timeold    = floor($timeold/10)*10;  // better db caching
        $timeoldext = time() - ($CFG->chat_old_ping*10); // JSless gui_basic needs much longer timeouts
        $timeoldext = floor($timeoldext/10)*10;  // better db caching

        $params = array('timeold'=>$timeold, 'timeoldext'=>$timeoldext, 'cmid'=>$cm->id);

        $timeout = "AND (chu.version<>'basic' AND chu.lastping>:timeold) OR (chu.version='basic' AND chu.lastping>:timeoldext)";

        foreach ($current as $cm) {
            //count users first
            if (isset($cm->mygroupids)) {
                $groupselect = "AND (chu.groupid IN ({$cm->mygroupids}) OR chu.groupid = 0)";
            } else {
                $groupselect = "";
            }

            if (!$users = $DB->get_records_sql("SELECT u.id, u.firstname, u.lastname, u.email, u.picture
                                                  FROM {course_modules} cm
                                                  JOIN {chat} ch        ON ch.id = cm.instance
                                                  JOIN {chat_users} chu ON chu.chatid = ch.id
                                                  JOIN {user} u         ON u.id = chu.userid
                                                 WHERE cm.id = :cmid $timeout $groupselect
                                              GROUP BY u.id, u.firstname, u.lastname, u.email, u.picture", $params)) {
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

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @global object
 * @return bool
 */
function chat_cron () {
    global $DB;

    chat_update_chat_times();

    chat_delete_old_users();

    /// Delete old messages with a
    /// single SQL query.
    $subselect = "SELECT c.keepdays
                    FROM {chat} c
                   WHERE c.id = {chat_messages}.chatid";

    $sql = "DELETE
              FROM {chat_messages}
             WHERE ($subselect) > 0 AND timestamp < ( ".time()." -($subselect) * 24 * 3600)";

    $DB->execute($sql);

    $sql = "DELETE
              FROM {chat_messages_current}
             WHERE timestamp < ( ".time()." - 8 * 3600)";

    $DB->execute($sql);

    return true;
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every chat event in the site is checked, else
 * only chat events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @global object
 * @param int $courseid
 * @return bool
 */
function chat_refresh_events($courseid = 0) {
    global $DB;

    if ($courseid) {
        if (! $chats = $DB->get_records("chat", array("course"=>$courseid))) {
            return true;
        }
    } else {
        if (! $chats = $DB->get_records("chat")) {
            return true;
        }
    }
    $moduleid = $DB->get_field('modules', 'id', array('name'=>'chat'));

    foreach ($chats as $chat) {
        $cm = get_coursemodule_from_id('chat', $chat->id);
        $event = new stdClass();
        $event->name        = $chat->name;
        $event->description = format_module_intro('chat', $chat, $cm->id);
        $event->timestart   = $chat->chattime;

        if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'chat', 'instance'=>$chat->id))) {
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event);
        } else {
            $event->courseid    = $chat->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'chat';
            $event->instance    = $chat->id;
            $event->eventtype   = 'chattime';
            $event->timeduration = 0;
            $event->visible     = $DB->get_field('course_modules', 'visible', array('module'=>$moduleid, 'instance'=>$chat->id));

            calendar_event::create($event);
        }
    }
    return true;
}


//////////////////////////////////////////////////////////////////////
/// Functions that require some SQL

/**
 * @global object
 * @param int $chatid
 * @param int $groupid
 * @param int $groupingid
 * @return array
 */
function chat_get_users($chatid, $groupid=0, $groupingid=0) {
    global $DB;

    $params = array('chatid'=>$chatid, 'groupid'=>$groupid, 'groupingid'=>$groupingid);

    if ($groupid) {
        $groupselect = " AND (c.groupid=:groupid OR c.groupid='0')";
    } else {
        $groupselect = "";
    }

    if (!empty($groupingid)) {
        $groupingjoin = "JOIN {groups_members} gm ON u.id = gm.userid
                         JOIN {groupings_groups} gg ON gm.groupid = gg.groupid AND gg.groupingid = :groupingid ";

    } else {
        $groupingjoin = '';
    }

    $ufields = user_picture::fields('u');
    return $DB->get_records_sql("SELECT DISTINCT $ufields, c.lastmessageping, c.firstping
                                   FROM {chat_users} c
                                   JOIN {user} u ON u.id = c.userid $groupingjoin
                                  WHERE c.chatid = :chatid $groupselect
                               ORDER BY c.firstping ASC", $params);
}

/**
 * @global object
 * @param int $chatid
 * @param int $groupid
 * @return array
 */
function chat_get_latest_message($chatid, $groupid=0) {
    global $DB;

    $params = array('chatid'=>$chatid, 'groupid'=>$groupid);

    if ($groupid) {
        $groupselect = "AND (groupid=:groupid OR groupid=0)";
    } else {
        $groupselect = "";
    }

    $sql = "SELECT *
        FROM {chat_messages_current} WHERE chatid = :chatid $groupselect
        ORDER BY timestamp DESC";

    // return the lastest one message
    return $DB->get_record_sql($sql, $params, true);
}


//////////////////////////////////////////////////////////////////////
// login if not already logged in

/**
 * login if not already logged in
 *
 * @global object
 * @global object
 * @param int $chatid
 * @param string $version
 * @param int $groupid
 * @param object $course
 * @return bool|int Returns the chat users sid or false
 */
function chat_login_user($chatid, $version, $groupid, $course) {
    global $USER, $DB;

    if (($version != 'sockets') and $chatuser = $DB->get_record('chat_users', array('chatid'=>$chatid, 'userid'=>$USER->id, 'groupid'=>$groupid))) {
        // this will update logged user information
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = time();
        $chatuser->lang     = current_language();

        // Sometimes $USER->lastip is not setup properly
        // during login. Update with current value if possible
        // or provide a dummy value for the db
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
        }

        if (($chatuser->course != $course->id) or ($chatuser->userid != $USER->id)) {
            return false;
        }
        $DB->update_record('chat_users', $chatuser);

    } else {
        $chatuser = new stdClass();
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
        }


        $DB->insert_record('chat_users', $chatuser);

        if ($version == 'sockets') {
            // do not send 'enter' message, chatd will do it
        } else {
            $message = new stdClass();
            $message->chatid    = $chatuser->chatid;
            $message->userid    = $chatuser->userid;
            $message->groupid   = $groupid;
            $message->message   = 'enter';
            $message->system    = 1;
            $message->timestamp = time();

            $DB->insert_record('chat_messages', $message);
            $DB->insert_record('chat_messages_current', $message);
        }
    }

    return $chatuser->sid;
}

/**
 * Delete the old and in the way
 *
 * @global object
 * @global object
 */
function chat_delete_old_users() {
// Delete the old and in the way
    global $CFG, $DB;

    $timeold = time() - $CFG->chat_old_ping;
    $timeoldext = time() - ($CFG->chat_old_ping*10); // JSless gui_basic needs much longer timeouts

    $query = "(version<>'basic' AND lastping<?) OR (version='basic' AND lastping<?)";
    $params = array($timeold, $timeoldext);

    if ($oldusers = $DB->get_records_select('chat_users', $query, $params) ) {
        $DB->delete_records_select('chat_users', $query, $params);
        foreach ($oldusers as $olduser) {
            $message = new stdClass();
            $message->chatid    = $olduser->chatid;
            $message->userid    = $olduser->userid;
            $message->groupid   = $olduser->groupid;
            $message->message   = 'exit';
            $message->system    = 1;
            $message->timestamp = time();

            $DB->insert_record('chat_messages', $message);
            $DB->insert_record('chat_messages_current', $message);
        }
    }
}

/**
 * Updates chat records so that the next chat time is correct
 *
 * @global object
 * @param int $chatid
 * @return void
 */
function chat_update_chat_times($chatid=0) {
/// Updates chat records so that the next chat time is correct
    global $DB;

    $timenow = time();

    $params = array('timenow'=>$timenow, 'chatid'=>$chatid);

    if ($chatid) {
        if (!$chats[] = $DB->get_record_select("chat", "id = :chatid AND chattime <= :timenow AND schedule > 0", $params)) {
            return;
        }
    } else {
        if (!$chats = $DB->get_records_select("chat", "chattime <= :timenow AND schedule > 0", $params)) {
            return;
        }
    }

    foreach ($chats as $chat) {
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
        $DB->update_record("chat", $chat);

        $event = new stdClass();           // Update calendar too

        $cond = "modulename='chat' AND instance = :chatid AND timestart <> :chattime";
        $params = array('chattime'=>$chat->chattime, 'chatid'=>$chatid);

        if ($event->id = $DB->get_field_select('event', 'id', $cond, $params)) {
            $event->timestart   = $chat->chattime;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        }
    }
}

/**
 * @global object
 * @global object
 * @param object $message
 * @param int $courseid
 * @param object $sender
 * @param object $currentuser
 * @param string $chat_lastrow
 * @return bool|string Returns HTML or false
 */
function chat_format_message_manually($message, $courseid, $sender, $currentuser, $chat_lastrow=NULL) {
    global $CFG, $USER, $OUTPUT;

    $output = new stdClass();
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

    $message->picture = $OUTPUT->user_picture($sender, array('size'=>false, 'courseid'=>$courseid, 'link'=>false));

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
        $output->basic = '<tr class="r1">
                            <th scope="row" class="cell c1 title"></th>
                            <td class="cell c2 text">' . get_string('message'.$message->message, 'chat', fullname($sender)) . '</td>
                            <td class="cell c3">' . $message->strtime . '</td>
                          </tr>';
        if($message->message == 'exit' or $message->message == 'enter') {
            $output->refreshusers = true; //force user panel refresh ASAP
        }
        return $output;
    }

    // It's not a system event
    $text = trim($message->message);

    /// Parse the text to clean and filter it
    $options = new stdClass();
    $options->para = false;
    $text = format_text($text, FORMAT_MOODLE, $options, $courseid);

    // And now check for special cases
    $patternTo = '#^\s*To\s([^:]+):(.*)#';
    $special = false;

    if (substr($text, 0, 5) == 'beep ') {
        /// It's a beep!
        $special = true;
        $beepwho = trim(substr($text, 5));

        if ($beepwho == 'all') {   // everyone
            $outinfobasic = get_string('messagebeepseveryone', 'chat', fullname($sender));
            $outinfo = $message->strtime . ': ' . $outinfobasic;
            $outmain = '';

            $output->beep = true;  // (eventually this should be set to
                                   //  to a filename uploaded by the user)

        } else if ($beepwho == $currentuser->id) {  // current user
            $outinfobasic = get_string('messagebeepsyou', 'chat', fullname($sender));
            $outinfo = $message->strtime . ': ' . $outinfobasic;
            $outmain = '';
            $output->beep = true;

        } else {  //something is not caught?
            return false;
        }
    } else if (substr($text, 0, 1) == '/') {     /// It's a user command
        $special = true;
        $pattern = '#(^\/)(\w+).*#';
        preg_match($pattern, $text, $matches);
        $command = isset($matches[2]) ? $matches[2] : false;
        // Support some IRC commands.
        switch ($command){
            case 'me':
                $outinfo = $message->strtime;
                $outmain = '*** <b>'.$sender->firstname.' '.substr($text, 4).'</b>';
                break;
            default:
                // Error, we set special back to false to use the classic message output.
                $special = false;
                break;
        }
    } else if (preg_match($patternTo, $text)) {
        $special = true;
        $matches = array();
        preg_match($patternTo, $text, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $outinfo = $message->strtime;
            $outmain = $sender->firstname.' '.get_string('saidto', 'chat').' <i>'.$matches[1].'</i>: '.$matches[2];
        } else {
            // Error, we set special back to false to use the classic message output.
            $special = false;
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
        $output->basic = '<tr class="r0">
                            <th scope="row" class="cell c1 title">' . $sender->firstname . '</th>
                            <td class="cell c2 text">' . $outmain . '</td>
                            <td class="cell c3">' . $message->strtime . '</td>
                          </tr>';
    } else {
        $output->basic = '<tr class="r1">
                            <th scope="row" class="cell c1 title"></th>
                            <td class="cell c2 text">' . $outinfobasic . '</td>
                            <td class="cell c3">' . $message->strtime . '</td>
                          </tr>';
    }
    $output->html .= "</td></tr></table>";
    return $output;
}

/**
 * @global object
 * @param object $message
 * @param int $courseid
 * @param object $currentuser
 * @param string $chat_lastrow
 * @return bool|string Returns HTML or false
 */
function chat_format_message($message, $courseid, $currentuser, $chat_lastrow=NULL) {
/// Given a message object full of information, this function
/// formats it appropriately into text and html, then
/// returns the formatted data.
    global $DB;

    static $users;     // Cache user lookups

    if (isset($users[$message->userid])) {
        $user = $users[$message->userid];
    } else if ($user = $DB->get_record('user', array('id'=>$message->userid), user_picture::fields())) {
        $users[$message->userid] = $user;
    } else {
        return NULL;
    }
    return chat_format_message_manually($message, $courseid, $user, $currentuser, $chat_lastrow);
}

/**
 * @global object
 * @param object $message message to be displayed.
 * @param mixed $chatuser user chat data
 * @param object $currentuser current user for whom the message should be displayed.
 * @param int $groupingid course module grouping id
 * @param string $theme name of the chat theme.
 * @return bool|string Returns HTML or false
 */
function chat_format_message_theme ($message, $chatuser, $currentuser, $groupingid, $theme = 'bubble') {
    global $CFG, $USER, $OUTPUT, $COURSE, $DB, $PAGE;
    require_once($CFG->dirroot.'/mod/chat/locallib.php');

    static $users;     // Cache user lookups

    $result = new stdClass();

    if (file_exists($CFG->dirroot . '/mod/chat/gui_ajax/theme/'.$theme.'/config.php')) {
        include($CFG->dirroot . '/mod/chat/gui_ajax/theme/'.$theme.'/config.php');
    }

    if (isset($users[$message->userid])) {
        $sender = $users[$message->userid];
    } else if ($sender = $DB->get_record('user', array('id'=>$message->userid), user_picture::fields())) {
        $users[$message->userid] = $sender;
    } else {
        return NULL;
    }

    $USER->timezone = 99;
    $tz = get_user_timezone($currentuser->timezone);
    $USER->timezone = $tz;

    if (empty($chatuser->course)) {
        $courseid = $COURSE->id;
    } else {
        $courseid = $chatuser->course;
    }

    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'chat'), $tz);
    $message->picture = $OUTPUT->user_picture($sender, array('courseid'=>$courseid));

    $message->picture = "<a target='_blank' href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";

    // Start processing the message
    if(!empty($message->system)) {
        $result->type = 'system';

        $senderprofile = $CFG->wwwroot.'/user/view.php?id='.$sender->id.'&amp;course='.$courseid;
        $event = get_string('message'.$message->message, 'chat', fullname($sender));
        $eventmessage = new event_message($senderprofile, fullname($sender), $message->strtime, $event, $theme);

        $output = $PAGE->get_renderer('mod_chat');
        $result->html = $output->render($eventmessage);

        return $result;
    }

    // It's not a system event
    $text = trim($message->message);

    /// Parse the text to clean and filter it
    $options = new stdClass();
    $options->para = false;
    $text = format_text($text, FORMAT_MOODLE, $options, $courseid);

    // And now check for special cases
    $special = false;
    $outtime = $message->strtime;

    // Initialise variables.
    $outmain = '';
    $patternTo = '#^\s*To\s([^:]+):(.*)#';

    if (substr($text, 0, 5) == 'beep ') {
        $special = true;
        /// It's a beep!
        $result->type = 'beep';
        $beepwho = trim(substr($text, 5));

        if ($beepwho == 'all') {   // everyone
            $outmain =  get_string('messagebeepseveryone', 'chat', fullname($sender));
        } else if ($beepwho == $currentuser->id) {  // current user
            $outmain = get_string('messagebeepsyou', 'chat', fullname($sender));
        } else if ($sender->id == $currentuser->id) {  //something is not caught?
            //allow beep for a active chat user only, else user can beep anyone and get fullname
            if (!empty($chatuser) && is_numeric($beepwho)) {
               $chatusers = chat_get_users($chatuser->chatid, $chatuser->groupid, $groupingid);
               if (array_key_exists($beepwho, $chatusers)) {
                   $outmain = get_string('messageyoubeep', 'chat', fullname($chatusers[$beepwho]));
               } else {
                   $outmain = get_string('messageyoubeep', 'chat', $beepwho);
               }
            } else {
                $outmain = get_string('messageyoubeep', 'chat', $beepwho);
            }
        }
    } else if (substr($text, 0, 1) == '/') {     /// It's a user command
        $special = true;
        $result->type = 'command';
        $pattern = '#(^\/)(\w+).*#';
        preg_match($pattern, $text, $matches);
        $command = isset($matches[2]) ? $matches[2] : false;
        // Support some IRC commands.
        switch ($command){
            case 'me':
                $outmain = '*** <b>'.$sender->firstname.' '.substr($text, 4).'</b>';
                break;
            default:
                // Error, we set special back to false to use the classic message output.
                $special = false;
                break;
        }
    } else if (preg_match($patternTo, $text)) {
        $special = true;
        $result->type = 'dialogue';
        $matches = array();
        preg_match($patternTo, $text, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $outmain = $sender->firstname.' <b>'.get_string('saidto', 'chat').'</b> <i>'.$matches[1].'</i>: '.$matches[2];
        } else {
            // Error, we set special back to false to use the classic message output.
            $special = false;
        }
    }

    if (!$special) {
        $outmain = $text;
    }

    $result->text = strip_tags($outtime.': '.$outmain);

    $mymessageclass = '';
    if ($sender->id == $USER->id) {
        $mymessageclass = 'chat-message-mymessage';
    }

    $senderprofile = $CFG->wwwroot.'/user/view.php?id='.$sender->id.'&amp;course='.$courseid;
    $usermessage = new user_message($senderprofile, fullname($sender), $message->picture, $mymessageclass, $outtime, $outmain, $theme);

    $output = $PAGE->get_renderer('mod_chat');
    $result->html = $output->render($usermessage);

    //When user beeps other user, then don't show any timestamp to other users in chat.
    if (('' === $outmain) && $special) {
        return false;
    } else {
        return $result;
    }
}

/**
 * @global object $DB
 * @global object $CFG
 * @global object $COURSE
 * @global object $OUTPUT
 * @param object $users
 * @param object $course
 * @return array return formatted user list
 */
function chat_format_userlist($users, $course) {
    global $CFG, $DB, $COURSE, $OUTPUT;
    $result = array();
    foreach($users as $user){
        $item = array();
        $item['name'] = fullname($user);
        $item['url'] = $CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$course->id;
        $item['picture'] = $OUTPUT->user_picture($user);
        $item['id'] = $user->id;
        $result[] = $item;
    }
    return $result;
}

/**
 * Print json format error
 * @param string $level
 * @param string $msg
 */
function chat_print_error($level, $msg) {
    header('Content-Length: ' . ob_get_length() );
    $error = new stdClass();
    $error->level = $level;
    $error->msg   = $msg;
    $response['error'] = $error;
    echo json_encode($response);
    ob_end_flush();
    exit;
}

/**
 * @return array
 */
function chat_get_view_actions() {
    return array('view','view all','report');
}

/**
 * @return array
 */
function chat_get_post_actions() {
    return array('talk');
}

/**
 * @global object
 * @global object
 * @param array $courses
 * @param array $htmlarray Passed by reference
 */
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
 *
 * @param object $mform form passed by reference
 */
function chat_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'chatheader', get_string('modulenameplural', 'chat'));
    $mform->addElement('advcheckbox', 'reset_chat', get_string('removemessages','chat'));
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function chat_reset_course_form_defaults($course) {
    return array('reset_chat'=>1);
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * chat messages for course $data->courseid.
 *
 * @global object
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function chat_reset_userdata($data) {
    global $CFG, $DB;

    $componentstr = get_string('modulenameplural', 'chat');
    $status = array();

    if (!empty($data->reset_chat)) {
        $chatessql = "SELECT ch.id
                        FROM {chat} ch
                       WHERE ch.course=?";
        $params = array($data->courseid);

        $DB->delete_records_select('chat_messages', "chatid IN ($chatessql)", $params);
        $DB->delete_records_select('chat_messages_current', "chatid IN ($chatessql)", $params);
        $DB->delete_records_select('chat_users', "chatid IN ($chatessql)", $params);
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
 *
 * @return array
 */
function chat_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames');
}


/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function chat_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

function chat_extend_navigation($navigation, $course, $module, $cm) {
    global $CFG;

    $currentgroup = groups_get_activity_group($cm, true);

    if (has_capability('mod/chat:chat', context_module::instance($cm->id))) {
        $strenterchat    = get_string('enterchat', 'chat');

        $target = $CFG->wwwroot.'/mod/chat/';
        $params = array('id'=>$cm->instance);

        if ($currentgroup) {
            $params['groupid'] = $currentgroup;
        }

        $links = array();

        $url = new moodle_url($target.'gui_'.$CFG->chat_method.'/index.php', $params);
        $action = new popup_action('click', $url, 'chat'.$course->id.$cm->instance.$currentgroup, array('height' => 500, 'width' => 700));
        $links[] = new action_link($url, $strenterchat, $action);

        $url = new moodle_url($target.'gui_basic/index.php', $params);
        $action = new popup_action('click', $url, 'chat'.$course->id.$cm->instance.$currentgroup, array('height' => 500, 'width' => 700));
        $links[] = new action_link($url, get_string('noframesjs', 'message'), $action);

        foreach ($links as $link) {
            $navigation->add($link->text, $link, navigation_node::TYPE_SETTING, null ,null, new pix_icon('i/group' , ''));
        }
    }

    $chatusers = chat_get_users($cm->instance, $currentgroup, $cm->groupingid);
    if (is_array($chatusers) && count($chatusers)>0) {
        $users = $navigation->add(get_string('currentusers', 'chat'));
        foreach ($chatusers as $chatuser) {
            $userlink = new moodle_url('/user/view.php', array('id'=>$chatuser->id,'course'=>$course->id));
            $users->add(fullname($chatuser).' '.format_time(time() - $chatuser->lastmessageping), $userlink, navigation_node::TYPE_USER, null, null, new pix_icon('i/user', ''));
        }
    }
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $chatnode The node to add module settings to
 */
function chat_extend_settings_navigation(settings_navigation $settings, navigation_node $chatnode) {
    global $DB, $PAGE, $USER;
    $chat = $DB->get_record("chat", array("id" => $PAGE->cm->instance));

    if ($chat->chattime && $chat->schedule) {
        $nextsessionnode = $chatnode->add(get_string('nextsession', 'chat').': '.userdate($chat->chattime).' ('.usertimezone($USER->timezone));
        $nextsessionnode->add_class('note');
    }

    $currentgroup = groups_get_activity_group($PAGE->cm, true);
    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
    } else {
        $groupselect = '';
    }

    if ($chat->studentlogs || has_capability('mod/chat:readlog',$PAGE->cm->context)) {
        if ($DB->get_records_select('chat_messages', "chatid = ? $groupselect", array($chat->id))) {
            $chatnode->add(get_string('viewreport', 'chat'), new moodle_url('/mod/chat/report.php', array('id'=>$PAGE->cm->id)));
        }
    }
}

/**
 * user logout event handler
 *
 * @param object $user full $USER object
 */
function chat_user_logout($user) {
    global $DB;
    $DB->delete_records('chat_users', array('userid'=>$user->id));
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function chat_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-chat-*'=>get_string('page-mod-chat-x', 'chat'));
    return $module_pagetype;
}
