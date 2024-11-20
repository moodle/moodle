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
 * @package   mod_chat
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/calendar/lib.php');

// Event types.
define('CHAT_EVENT_TYPE_CHATTIME', 'chattime');

// Gap between sessions. 5 minutes or more of idleness between messages in a chat means the messages belong in different sessions.
define('CHAT_SESSION_GAP', 300);
// Don't publish next chat time
define('CHAT_SCHEDULE_NONE', 0);
// Publish the specified time only.
define('CHAT_SCHEDULE_SINGLE', 1);
// Repeat chat session at the same time daily.
define('CHAT_SCHEDULE_DAILY', 2);
// Repeat chat session at the same time weekly.
define('CHAT_SCHEDULE_WEEKLY', 3);

// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output.
global $CHAT_HTMLHEAD;
$CHAT_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body>\n\n".padding(200);

// The HTML head for the message window to start with (with js scrolling).
global $CHAT_HTMLHEAD_JS;
$CHAT_HTMLHEAD_JS = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html><head><script type="text/javascript">
//<![CDATA[
function move() {
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

// The HTML code for standard empty pages (e.g. if a user was kicked out).
global $CHAT_HTMLHEAD_OUT;
$CHAT_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body></body></html>";

// The HTML head for the message input page.
global $CHAT_HTMLHEAD_MSGINPUT;
$CHAT_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body>";

// The HTML code for the message input page, with JavaScript.
global $CHAT_HTMLHEAD_MSGINPUT_JS;
$CHAT_HTMLHEAD_MSGINPUT_JS = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
    <head><title>Message Input</title>
    <script type="text/javascript">
    //<![CDATA[
    scroll_active = true;
    function empty_field_and_submit() {
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

// Dummy data that gets output to the browser as needed, in order to make it show output.
global $CHAT_DUMMY_DATA;
$CHAT_DUMMY_DATA = padding(200);

/**
 * @param int $n
 * @return string
 */
function padding($n) {
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $str .= "<!-- nix -->\n";
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
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');

    $chat->timemodified = time();
    $chat->chattime = chat_calculate_next_chat_time($chat->schedule, $chat->chattime);

    $returnid = $DB->insert_record("chat", $chat);

    if ($chat->schedule > 0) {
        $event = new stdClass();
        $event->type        = CALENDAR_EVENT_TYPE_ACTION;
        $event->name        = $chat->name;
        $event->description = format_module_intro('chat', $chat, $chat->coursemodule, false);
        $event->format      = FORMAT_HTML;
        $event->courseid    = $chat->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'chat';
        $event->instance    = $returnid;
        $event->eventtype   = CHAT_EVENT_TYPE_CHATTIME;
        $event->timestart   = $chat->chattime;
        $event->timesort    = $chat->chattime;
        $event->timeduration = 0;

        calendar_event::create($event, false);
    }

    if (!empty($chat->completionexpected)) {
        \core_completion\api::update_completion_date_event($chat->coursemodule, 'chat', $returnid, $chat->completionexpected);
    }

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
    $chat->chattime = chat_calculate_next_chat_time($chat->schedule, $chat->chattime);

    $DB->update_record("chat", $chat);

    $event = new stdClass();

    if ($event->id = $DB->get_field('event', 'id', array('modulename' => 'chat',
        'instance' => $chat->id, 'eventtype' => CHAT_EVENT_TYPE_CHATTIME))) {

        if ($chat->schedule > 0) {
            $event->type        = CALENDAR_EVENT_TYPE_ACTION;
            $event->name        = $chat->name;
            $event->description = format_module_intro('chat', $chat, $chat->coursemodule, false);
            $event->format      = FORMAT_HTML;
            $event->timestart   = $chat->chattime;
            $event->timesort    = $chat->chattime;

            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        } else {
            // Do not publish this event, so delete it.
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->delete();
        }
    } else {
        // No event, do we need to create one?
        if ($chat->schedule > 0) {
            $event = new stdClass();
            $event->type        = CALENDAR_EVENT_TYPE_ACTION;
            $event->name        = $chat->name;
            $event->description = format_module_intro('chat', $chat, $chat->coursemodule, false);
            $event->format      = FORMAT_HTML;
            $event->courseid    = $chat->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'chat';
            $event->instance    = $chat->id;
            $event->eventtype   = CHAT_EVENT_TYPE_CHATTIME;
            $event->timestart   = $chat->chattime;
            $event->timesort    = $chat->chattime;
            $event->timeduration = 0;

            calendar_event::create($event, false);
        }
    }

    $completionexpected = (!empty($chat->completionexpected)) ? $chat->completionexpected : null;
    \core_completion\api::update_completion_date_event($chat->coursemodule, 'chat', $chat->id, $completionexpected);

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

    if (! $chat = $DB->get_record('chat', array('id' => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here.

    if (! $DB->delete_records('chat', array('id' => $chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_messages', array('chatid' => $chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_messages_current', array('chatid' => $chat->id))) {
        $result = false;
    }
    if (! $DB->delete_records('chat_users', array('chatid' => $chat->id))) {
        $result = false;
    }

    if (! $DB->delete_records('event', array('modulename' => 'chat', 'instance' => $chat->id))) {
        $result = false;
    }

    return $result;
}

/**
 * Given a course and a date, prints a summary of all chat rooms past and present
 * This function is called from block_recent_activity
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

    // This is approximate only, but it is really fast.
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
    $modinfo = get_fast_modinfo($course); // Reference needed because we might load the groups.

    foreach ($mcms as $cmid => $mcm) {
        if (!array_key_exists($cmid, $modinfo->cms)) {
            continue;
        }
        $cm = $modinfo->cms[$cmid];
        if (!$modinfo->cms[$cm->id]->uservisible) {
            continue;
        }

        if (groups_get_activity_groupmode($cm) != SEPARATEGROUPS
         or has_capability('moodle/site:accessallgroups', context_module::instance($cm->id))) {
            if ($timeout > time() - $mcm->lasttime) {
                $current[] = $cm;
            } else {
                $past[] = $cm;
            }

            continue;
        }

        // Verify groups in separate mode.
        if (!$mygroupids = $modinfo->get_groups($cm->groupingid)) {
            continue;
        }

        // Ok, last post was not for my group - we have to query db to get last message from one of my groups.
        // The only minor problem is that the order will not be correct.
        $mygroupids = implode(',', $mygroupids);

        if (!$mcm = $DB->get_record_sql("SELECT cm.id, MAX(chm.timestamp) AS lasttime
                                           FROM {course_modules} cm
                                           JOIN {chat} ch           ON ch.id = cm.instance
                                           JOIN {chat_messages_current} chm ON chm.chatid = ch.id
                                          WHERE chm.timestamp > ? AND cm.id = ? AND
                                                (chm.groupid IN ($mygroupids) OR chm.groupid = 0)
                                       GROUP BY cm.id", array($timestart, $cm->id))) {
             continue;
        }

        $mcms[$cmid]->lasttime = $mcm->lasttime;
        if ($timeout > time() - $mcm->lasttime) {
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
        echo $OUTPUT->heading(get_string("pastchats", 'chat') . ':', 6);

        foreach ($past as $cm) {
            $link = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
            $date = userdate($mcms[$cm->id]->lasttime, $strftimerecent);
            echo '<div class="head"><div class="date">'.$date.'</div></div>';
            echo '<div class="info"><a href="'.$link.'">'.format_string($cm->name, true).'</a></div>';
        }
    }

    if ($current) {
        echo $OUTPUT->heading(get_string("currentchats", 'chat') . ':', 6);

        $oldest = floor((time() - $CFG->chat_old_ping) / 10) * 10;  // Better db caching.

        $timeold    = time() - $CFG->chat_old_ping;
        $timeold    = floor($timeold / 10) * 10;  // Better db caching.
        $timeoldext = time() - ($CFG->chat_old_ping * 10); // JSless gui_basic needs much longer timeouts.
        $timeoldext = floor($timeoldext / 10) * 10;  // Better db caching.

        $params = array('timeold' => $timeold, 'timeoldext' => $timeoldext, 'cmid' => $cm->id);

        $timeout = "AND ((chu.version<>'basic' AND chu.lastping>:timeold) OR (chu.version='basic' AND chu.lastping>:timeoldext))";

        foreach ($current as $cm) {
            // Count users first.
            $mygroupids = $modinfo->groups[$cm->groupingid];
            if (!empty($mygroupids)) {
                list($subquery, $subparams) = $DB->get_in_or_equal($mygroupids, SQL_PARAMS_NAMED, 'gid');
                $params += $subparams;
                $groupselect = "AND (chu.groupid $subquery OR chu.groupid = 0)";
            } else {
                $groupselect = "";
            }

            $userfieldsapi = \core_user\fields::for_userpic();
            $userfields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
            if (!$users = $DB->get_records_sql("SELECT $userfields
                                                  FROM {course_modules} cm
                                                  JOIN {chat} ch        ON ch.id = cm.instance
                                                  JOIN {chat_users} chu ON chu.chatid = ch.id
                                                  JOIN {user} u         ON u.id = chu.userid
                                                 WHERE cm.id = :cmid $timeout $groupselect
                                              GROUP BY $userfields", $params)) {
            }

            $link = $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id;
            $date = userdate($mcms[$cm->id]->lasttime, $strftimerecent);

            echo '<div class="head"><div class="date">'.$date.'</div></div>';
            echo '<div class="info"><a href="'.$link.'">'.format_string($cm->name, true).'</a></div>';
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
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every chat event in the site is checked, else
 * only chat events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @global object
 * @param int $courseid
 * @param int|stdClass $instance Chat module instance or ID.
 * @param int|stdClass $cm Course module object or ID.
 * @return bool
 */
function chat_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $DB;

    // If we have instance information then we can just update the one event instead of updating all events.
    if (isset($instance)) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('chat', array('id' => $instance), '*', MUST_EXIST);
        }
        if (isset($cm)) {
            if (!is_object($cm)) {
                chat_prepare_update_events($instance);
                return true;
            } else {
                chat_prepare_update_events($instance, $cm);
                return true;
            }
        }
    }

    if ($courseid) {
        if (! $chats = $DB->get_records("chat", array("course" => $courseid))) {
            return true;
        }
    } else {
        if (! $chats = $DB->get_records("chat")) {
            return true;
        }
    }
    foreach ($chats as $chat) {
        chat_prepare_update_events($chat);
    }
    return true;
}

/**
 * Updates both the normal and completion calendar events for chat.
 *
 * @param  stdClass $chat The chat object (from the DB)
 * @param  stdClass $cm The course module object.
 */
function chat_prepare_update_events($chat, $cm = null) {
    global $DB;
    if (!isset($cm)) {
        $cm = get_coursemodule_from_instance('chat', $chat->id, $chat->course);
    }
    $event = new stdClass();
    $event->name        = $chat->name;
    $event->type        = CALENDAR_EVENT_TYPE_ACTION;
    $event->description = format_module_intro('chat', $chat, $cm->id, false);
    $event->format      = FORMAT_HTML;
    $event->timestart   = $chat->chattime;
    $event->timesort    = $chat->chattime;
    if ($event->id = $DB->get_field('event', 'id', array('modulename' => 'chat', 'instance' => $chat->id,
            'eventtype' => CHAT_EVENT_TYPE_CHATTIME))) {
        $calendarevent = calendar_event::load($event->id);
        $calendarevent->update($event, false);
    } else if ($chat->schedule > 0) {
        // The chat is scheduled and the event should be published.
        $event->courseid    = $chat->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'chat';
        $event->instance    = $chat->id;
        $event->eventtype   = CHAT_EVENT_TYPE_CHATTIME;
        $event->timeduration = 0;
        $event->visible = $cm->visible;
        calendar_event::create($event, false);
    }
}

// Functions that require some SQL.

/**
 * @global object
 * @param int $chatid
 * @param int $groupid
 * @param int $groupingid
 * @return array
 */
function chat_get_users($chatid, $groupid=0, $groupingid=0) {
    global $DB;

    $params = array('chatid' => $chatid, 'groupid' => $groupid, 'groupingid' => $groupingid);

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

    $userfieldsapi = \core_user\fields::for_userpic();
    $ufields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;
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

    $params = array('chatid' => $chatid, 'groupid' => $groupid);

    if ($groupid) {
        $groupselect = "AND (groupid=:groupid OR groupid=0)";
    } else {
        $groupselect = "";
    }

    $sql = "SELECT *
        FROM {chat_messages_current} WHERE chatid = :chatid $groupselect
        ORDER BY timestamp DESC, id DESC";

    // Return the lastest one message.
    return $DB->get_record_sql($sql, $params, true);
}

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

    if (($version != 'sockets') and $chatuser = $DB->get_record('chat_users', array('chatid' => $chatid,
                                                                                    'userid' => $USER->id,
                                                                                    'groupid' => $groupid))) {
        // This will update logged user information.
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = time();
        $chatuser->lang     = current_language();

        // Sometimes $USER->lastip is not setup properly during login.
        // Update with current value if possible or provide a dummy value for the db.
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
        $chatuser->course   = $course->id; // Caching - needed for current_language too.
        $chatuser->lang     = current_language(); // Caching - to resource intensive to find out later.

        // Sometimes $USER->lastip is not setup properly during login.
        // Update with current value if possible or provide a dummy value for the db.
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
        }

        $DB->insert_record('chat_users', $chatuser);

        if ($version == 'sockets') {
            // Do not send 'enter' message, chatd will do it.
        } else {
            chat_send_chatmessage($chatuser, 'enter', true);
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
    // Delete the old and in the way.
    global $CFG, $DB;

    $timeold = time() - $CFG->chat_old_ping;
    $timeoldext = time() - ($CFG->chat_old_ping * 10); // JSless gui_basic needs much longer timeouts.

    $query = "(version<>'basic' AND lastping<?) OR (version='basic' AND lastping<?)";
    $params = array($timeold, $timeoldext);

    if ($oldusers = $DB->get_records_select('chat_users', $query, $params) ) {
        $DB->delete_records_select('chat_users', $query, $params);
        foreach ($oldusers as $olduser) {
            chat_send_chatmessage($olduser, 'exit', true);
        }
    }
}

/**
 * Calculate next chat session time based on schedule.
 *
 * @param int $schedule
 * @param int $chattime
 *
 * @return int timestamp
 */
function chat_calculate_next_chat_time(int $schedule, int $chattime): int {
    $timenow = time();

    switch ($schedule) {
        case CHAT_SCHEDULE_DAILY: { // Repeat daily.
            while ($chattime <= $timenow) {
                $chattime += DAYSECS;
            }
            break;
        }
        case CHAT_SCHEDULE_WEEKLY: { // Repeat weekly.
            while ($chattime <= $timenow) {
                $chattime += WEEKSECS;
            }
            break;
        }
    }

    return $chattime;
}

/**
 * Updates chat records so that the next chat time is correct
 *
 * @global object
 * @param int $chatid
 * @return void
 */
function chat_update_chat_times($chatid=0) {
    // Updates chat records so that the next chat time is correct.
    global $DB;

    $timenow = time();

    $params = array('timenow' => $timenow, 'chatid' => $chatid);

    if ($chatid) {
        if (!$chats[] = $DB->get_record_select("chat", "id = :chatid AND chattime <= :timenow AND schedule > 0", $params)) {
            return;
        }
    } else {
        if (!$chats = $DB->get_records_select("chat", "chattime <= :timenow AND schedule > 0", $params)) {
            return;
        }
    }

    $courseids = [];
    foreach ($chats as $chat) {
        $originalchattime = $chat->chattime;
        $chat->chattime = chat_calculate_next_chat_time($chat->schedule, $chat->chattime);
        if ($originalchattime != $chat->chattime) {
            $courseids[] = $chat->course;
            $DB->update_record("chat", $chat);

            $cm = get_coursemodule_from_instance('chat', $chat->id, $chat->course);
            \course_modinfo::purge_course_module_cache($cm->course, $cm->id);
        }

        $event = new stdClass(); // Update calendar too.
        $cond = "modulename='chat' AND eventtype = :eventtype AND instance = :chatid AND timestart <> :chattime";
        $params = ['chattime' => $chat->chattime, 'eventtype' => CHAT_EVENT_TYPE_CHATTIME, 'chatid' => $chat->id];

        if ($event->id = $DB->get_field_select('event', 'id', $cond, $params)) {
            $event->timestart = $chat->chattime;
            $event->timesort = $chat->chattime;
            $calendarevent = calendar_event::load($event->id);
            $calendarevent->update($event, false);
        }
    }

    $courseids = array_unique($courseids);
    foreach ($courseids as $courseid) {
        rebuild_course_cache($courseid, true, true);
    }
}

/**
 * Send a message on the chat.
 *
 * @param object $chatuser The chat user record.
 * @param string $messagetext The message to be sent.
 * @param bool $issystem False for non-system messages, true for system messages.
 * @param object $cm The course module object, pass it to save a database query when we trigger the event.
 * @return int The message ID.
 * @since Moodle 2.6
 */
function chat_send_chatmessage($chatuser, $messagetext, $issystem = false, $cm = null) {
    global $DB;

    $message = new stdClass();
    $message->chatid    = $chatuser->chatid;
    $message->userid    = $chatuser->userid;
    $message->groupid   = $chatuser->groupid;
    $message->message   = $messagetext;
    $message->issystem  = $issystem ? 1 : 0;
    $message->timestamp = time();

    $messageid = $DB->insert_record('chat_messages', $message);
    $DB->insert_record('chat_messages_current', $message);
    $message->id = $messageid;

    if (!$issystem) {

        if (empty($cm)) {
            $cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $chatuser->course);
        }

        $params = array(
            'context' => context_module::instance($cm->id),
            'objectid' => $message->id,
            // We set relateduserid, because when triggered from the chat daemon, the event userid is null.
            'relateduserid' => $chatuser->userid
        );
        $event = \mod_chat\event\message_sent::create($params);
        $event->add_record_snapshot('chat_messages', $message);
        $event->trigger();
    }

    return $message->id;
}

/**
 * @global object
 * @global object
 * @param object $message
 * @param int $courseid
 * @param object $sender
 * @param object $currentuser
 * @param string $chatlastrow
 * @return bool|string Returns HTML or false
 */
function chat_format_message_manually($message, $courseid, $sender, $currentuser, $chatlastrow = null) {
    global $CFG, $USER, $OUTPUT;

    $output = new stdClass();
    $output->beep = false;       // By default.
    $output->refreshusers = false; // By default.

    // Find the correct timezone for displaying this message.
    $tz = core_date::get_user_timezone($currentuser);

    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'chat'), $tz);

    $message->picture = $OUTPUT->user_picture($sender, array('size' => false, 'courseid' => $courseid, 'link' => false));

    if ($courseid) {
        $message->picture = "<a onclick=\"window.open('$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid')\"".
                            " href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";
    }

    // Calculate the row class.
    if ($chatlastrow !== null) {
        $rowclass = ' class="r'.$chatlastrow.'" ';
    } else {
        $rowclass = '';
    }

    // Start processing the message.

    if (!empty($message->issystem)) {
        // System event.
        $output->text = $message->strtime.': '.get_string('message'.$message->message, 'chat', fullname($sender));
        $output->html  = '<table class="chat-event"><tr'.$rowclass.'><td class="picture">'.$message->picture.'</td>';
        $output->html .= '<td class="text"><span class="event">'.$output->text.'</span></td></tr></table>';
        $output->basic = '<tr class="r1">
                            <th scope="row" class="cell c1 title"></th>
                            <td class="cell c2 text">' . get_string('message'.$message->message, 'chat', fullname($sender)) . '</td>
                            <td class="cell c3">' . $message->strtime . '</td>
                          </tr>';
        if ($message->message == 'exit' or $message->message == 'enter') {
            $output->refreshusers = true; // Force user panel refresh ASAP.
        }
        return $output;
    }

    // It's not a system event.
    $rawtext = trim($message->message);

    // Options for format_text, when we get to it...
    // format_text call will parse the text to clean and filter it.
    // It cannot be called here as HTML-isation interferes with special case
    // recognition, but *must* be called on any user-sourced text to be inserted
    // into $outmain.
    $options = new stdClass();
    $options->para = false;
    $options->blanktarget = true;

    // And now check for special cases.
    $patternto = '#^\s*To\s([^:]+):(.*)#';
    $special = false;

    if (substr($rawtext, 0, 5) == 'beep ') {
        // It's a beep!
        $special = true;
        $beepwho = trim(substr($rawtext, 5));

        if ($beepwho == 'all') {   // Everyone.
            $outinfobasic = get_string('messagebeepseveryone', 'chat', fullname($sender));
            $outinfo = $message->strtime . ': ' . $outinfobasic;
            $outmain = '';

            $output->beep = true;  // Eventually this should be set to a filename uploaded by the user.

        } else if ($beepwho == $currentuser->id) {  // Current user.
            $outinfobasic = get_string('messagebeepsyou', 'chat', fullname($sender));
            $outinfo = $message->strtime . ': ' . $outinfobasic;
            $outmain = '';
            $output->beep = true;

        } else {  // Something is not caught?
            return false;
        }
    } else if (substr($rawtext, 0, 1) == '/') {     // It's a user command.
        $special = true;
        $pattern = '#(^\/)(\w+).*#';
        preg_match($pattern, $rawtext, $matches);
        $command = isset($matches[2]) ? $matches[2] : false;
        // Support some IRC commands.
        switch ($command) {
            case 'me':
                $outinfo = $message->strtime;
                $text = '*** <b>'.$sender->firstname.' '.substr($rawtext, 4).'</b>';
                $outmain = format_text($text, FORMAT_MOODLE, array_merge((array) $options, [
                    'context' => \core\context\course::instance($courseid),
                ]));
                break;
            default:
                // Error, we set special back to false to use the classic message output.
                $special = false;
                break;
        }
    } else if (preg_match($patternto, $rawtext)) {
        $special = true;
        $matches = array();
        preg_match($patternto, $rawtext, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $text = format_text($matches[2], FORMAT_MOODLE, array_merge((array) $options, [
                'context' => \core\context\course::instance($courseid),
            ]));
            $outinfo = $message->strtime;
            $outmain = $sender->firstname.' '.get_string('saidto', 'chat').' <i>'.$matches[1].'</i>: '.$text;
        } else {
            // Error, we set special back to false to use the classic message output.
            $special = false;
        }
    }

    if (!$special) {
        $text = format_text($rawtext, FORMAT_MOODLE, array_merge((array) $options, [
            'context' => \core\context\course::instance($courseid),
        ]));
        $outinfo = $message->strtime.' '.$sender->firstname;
        $outmain = $text;
    }

    // Format the message as a small table.

    $output->text  = strip_tags($outinfo.': '.$outmain);

    $output->html  = "<table class=\"chat-message\"><tr$rowclass><td class=\"picture\" valign=\"top\">$message->picture</td>";
    $output->html .= "<td class=\"text\"><span class=\"title\">$outinfo</span>";
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
 * Given a message object this function formats it appropriately into text and html then returns the formatted data
 * @global object
 * @param object $message
 * @param int $courseid
 * @param object $currentuser
 * @param string $chatlastrow
 * @return bool|string Returns HTML or false
 */
function chat_format_message($message, $courseid, $currentuser, $chatlastrow=null) {
    global $DB;

    static $users;     // Cache user lookups.

    if (isset($users[$message->userid])) {
        $user = $users[$message->userid];
    } else if ($user = $DB->get_record('user', ['id' => $message->userid], implode(',', \core_user\fields::get_picture_fields()))) {
        $users[$message->userid] = $user;
    } else {
        return null;
    }
    return chat_format_message_manually($message, $courseid, $user, $currentuser, $chatlastrow);
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

    static $users;     // Cache user lookups.

    $result = new stdClass();

    if (file_exists($CFG->dirroot . '/mod/chat/gui_ajax/theme/'.$theme.'/config.php')) {
        include($CFG->dirroot . '/mod/chat/gui_ajax/theme/'.$theme.'/config.php');
    }

    if (isset($users[$message->userid])) {
        $sender = $users[$message->userid];
    } else if ($sender = $DB->get_record('user', array('id' => $message->userid),
            implode(',', \core_user\fields::get_picture_fields()))) {
        $users[$message->userid] = $sender;
    } else {
        return null;
    }

    // Find the correct timezone for displaying this message.
    $tz = core_date::get_user_timezone($currentuser);

    if (empty($chatuser->course)) {
        $courseid = $COURSE->id;
    } else {
        $courseid = $chatuser->course;
    }

    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'chat'), $tz);
    $message->picture = $OUTPUT->user_picture($sender, array('courseid' => $courseid));

    $message->picture = "<a target='_blank'".
                        " href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";

    // Start processing the message.
    if (!empty($message->issystem)) {
        $result->type = 'system';

        $senderprofile = $CFG->wwwroot.'/user/view.php?id='.$sender->id.'&amp;course='.$courseid;
        $event = get_string('message'.$message->message, 'chat', fullname($sender));
        $eventmessage = new event_message($senderprofile, fullname($sender), $message->strtime, $event, $theme);

        $output = $PAGE->get_renderer('mod_chat');
        $result->html = $output->render($eventmessage);

        return $result;
    }

    // It's not a system event.
    $rawtext = trim($message->message);

    // Options for format_text, when we get to it...
    // format_text call will parse the text to clean and filter it.
    // It cannot be called here as HTML-isation interferes with special case
    // recognition, but *must* be called on any user-sourced text to be inserted
    // into $outmain.
    $options = [
        'para' => false,
        'blanktarget' => true,
    ];

    // And now check for special cases.
    $special = false;
    $outtime = $message->strtime;

    // Initialise variables.
    $outmain = '';
    $patternto = '#^\s*To\s([^:]+):(.*)#';

    if (substr($rawtext, 0, 5) == 'beep ') {
        $special = true;
        // It's a beep!
        $result->type = 'beep';
        $beepwho = trim(substr($rawtext, 5));

        if ($beepwho == 'all') {   // Everyone.
            $outmain = get_string('messagebeepseveryone', 'chat', fullname($sender));
        } else if ($beepwho == $currentuser->id) {  // Current user.
            $outmain = get_string('messagebeepsyou', 'chat', fullname($sender));
        } else if ($sender->id == $currentuser->id) {  // Something is not caught?
            // Allow beep for a active chat user only, else user can beep anyone and get fullname.
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
    } else if (substr($rawtext, 0, 1) == '/') {     // It's a user command.
        $special = true;
        $result->type = 'command';
        $pattern = '#(^\/)(\w+).*#';
        preg_match($pattern, $rawtext, $matches);
        $command = isset($matches[2]) ? $matches[2] : false;
        // Support some IRC commands.
        switch ($command) {
            case 'me':
                $text = '*** <b>'.$sender->firstname.' '.substr($rawtext, 4).'</b>';
                $outmain = format_text($text, FORMAT_MOODLE, array_merge($options, [
                    'context' => \core\context\course::instance($courseid),
                ]));
                break;
            default:
                // Error, we set special back to false to use the classic message output.
                $special = false;
                break;
        }
    } else if (preg_match($patternto, $rawtext)) {
        $special = true;
        $result->type = 'dialogue';
        $matches = array();
        preg_match($patternto, $rawtext, $matches);
        if (isset($matches[1]) && isset($matches[2])) {
            $text = format_text($matches[2], FORMAT_MOODLE, array_merge($options, [
                'context' => \core\context\course::instance($courseid),
            ]));
            $outmain = $sender->firstname.' <b>'.get_string('saidto', 'chat').'</b> <i>'.$matches[1].'</i>: '.$text;
        } else {
            // Error, we set special back to false to use the classic message output.
            $special = false;
        }
    }

    if (!$special) {
        $text = format_text($rawtext, FORMAT_MOODLE, array_merge($options, [
            'context' => \core\context\course::instance($courseid),
        ]));
        $outmain = $text;
    }

    $result->text = strip_tags($outtime.': '.$outmain);

    $mymessageclass = '';
    if ($sender->id == $USER->id) {
        $mymessageclass = 'chat-message-mymessage';
    }

    $senderprofile = $CFG->wwwroot.'/user/view.php?id='.$sender->id.'&amp;course='.$courseid;
    $usermessage = new user_message($senderprofile, fullname($sender), $message->picture,
                                    $mymessageclass, $outtime, $outmain, $theme);

    $output = $PAGE->get_renderer('mod_chat');
    $result->html = $output->render($usermessage);

    // When user beeps other user, then don't show any timestamp to other users in chat.
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
    foreach ($users as $user) {
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
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function chat_get_view_actions() {
    return array('view', 'view all', 'report');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function chat_get_post_actions() {
    return array('talk');
}

/**
 * @deprecated since Moodle 3.3, when the block_course_overview block was removed.
 */
function chat_print_overview() {
    throw new coding_exception('chat_print_overview() can not be used any more and is obsolete.');
}


/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the chat.
 *
 * @param MoodleQuickForm $mform form passed by reference
 */
function chat_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'chatheader', get_string('modulenameplural', 'chat'));
    $mform->addElement('static', 'chatdelete', get_string('delete'));
    $mform->addElement('advcheckbox', 'reset_chat', get_string('removemessages', 'chat'));
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function chat_reset_course_form_defaults($course) {
    return array('reset_chat' => 1);
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
    $status = [];

    if (!empty($data->reset_chat)) {
        $chatessql = "SELECT ch.id
                        FROM {chat} ch
                       WHERE ch.course=?";
        $params = [$data->courseid];

        $DB->delete_records_select('chat_messages', "chatid IN ($chatessql)", $params);
        $DB->delete_records_select('chat_messages_current', "chatid IN ($chatessql)", $params);
        $DB->delete_records_select('chat_users', "chatid IN ($chatessql)", $params);
        $status[] = [
            'component' => $componentstr,
            'item' => get_string('removemessages', 'chat'),
            'error' => false,
        ];
    }

    // Updating dates - shift may be negative too.
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('chat', ['chattime'], $data->timeshift, $data->courseid);
        $status[] = [
            'component' => $componentstr,
            'item' => get_string('date'),
            'error' => false,
        ];
    }

    return $status;
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function chat_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COMMUNICATION;
        default:
            return null;
    }
}

function chat_extend_navigation($navigation, $course, $module, $cm) {
    global $CFG;

    $currentgroup = groups_get_activity_group($cm, true);

    if (has_capability('mod/chat:chat', context_module::instance($cm->id))) {
        $strenterchat    = get_string('enterchat', 'chat');

        $target = $CFG->wwwroot.'/mod/chat/';
        $params = array('id' => $cm->instance);

        if ($currentgroup) {
            $params['groupid'] = $currentgroup;
        }

        $links = array();

        $url = new moodle_url($target.'gui_'.$CFG->chat_method.'/index.php', $params);
        $action = new popup_action('click', $url, 'chat'.$course->id.$cm->instance.$currentgroup,
                                   array('height' => 500, 'width' => 700));
        $links[] = new action_link($url, $strenterchat, $action);

        $url = new moodle_url($target.'gui_basic/index.php', $params);
        $action = new popup_action('click', $url, 'chat'.$course->id.$cm->instance.$currentgroup,
                                   array('height' => 500, 'width' => 700));
        $links[] = new action_link($url, get_string('noframesjs', 'message'), $action);

        foreach ($links as $link) {
            $navigation->add($link->text, $link, navigation_node::TYPE_SETTING, null , null, new pix_icon('i/group' , ''));
        }
    }

    $chatusers = chat_get_users($cm->instance, $currentgroup, $cm->groupingid);
    if (is_array($chatusers) && count($chatusers) > 0) {
        $users = $navigation->add(get_string('currentusers', 'chat'));
        foreach ($chatusers as $chatuser) {
            $userlink = new moodle_url('/user/view.php', array('id' => $chatuser->id, 'course' => $course->id));
            $users->add(fullname($chatuser).' '.format_time(time() - $chatuser->lastmessageping),
                        $userlink, navigation_node::TYPE_USER, null, null, new pix_icon('i/user', ''));
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
    global $DB;
    $chat = $DB->get_record("chat", array("id" => $settings->get_page()->cm->instance));

    $currentgroup = groups_get_activity_group($settings->get_page()->cm, true);
    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
    } else {
        $groupselect = '';
    }

    if ($chat->studentlogs || has_capability('mod/chat:readlog', $settings->get_page()->cm->context)) {
        if ($DB->get_records_select('chat_messages', "chatid = ? $groupselect", array($chat->id))) {
            $chatnode->add(get_string('pastsessions', 'chat'),
                new moodle_url('/mod/chat/report.php', array('id' => $settings->get_page()->cm->id)),
                navigation_node::TYPE_SETTING, null, 'pastsessions');
        }
    }
}

/**
 * user logout event handler
 *
 * @param \core\event\user_loggedout $event The event.
 * @return void
 */
function chat_user_logout(\core\event\user_loggedout $event) {
    global $DB;
    $DB->delete_records('chat_users', array('userid' => $event->objectid));
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function chat_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array('mod-chat-*' => get_string('page-mod-chat-x', 'chat'));
    return $modulepagetype;
}

/**
 * Return a list of the latest messages in the given chat session.
 *
 * @param  stdClass $chatuser     chat user session data
 * @param  int      $chatlasttime last time messages were retrieved
 * @return array    list of messages
 * @since  Moodle 3.0
 */
function chat_get_latest_messages($chatuser, $chatlasttime) {
    global $DB;

    $params = array('groupid' => $chatuser->groupid, 'chatid' => $chatuser->chatid, 'lasttime' => $chatlasttime);

    $groupselect = $chatuser->groupid ? " AND (groupid=" . $chatuser->groupid . " OR groupid=0) " : "";

    return $DB->get_records_select('chat_messages_current', 'chatid = :chatid AND timestamp > :lasttime ' . $groupselect,
                                    $params, 'timestamp ASC');
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $chat       chat object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function chat_view($chat, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $chat->id
    );

    $event = \mod_chat\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('chat', $chat);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_chat_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory,
                                                     int $userid = 0) {
    global $USER, $DB;

    if ($userid) {
        $user = core_user::get_user($userid, 'id, timezone');
    } else {
        $user = $USER;
    }

    $cm = get_fast_modinfo($event->courseid, $user->id)->instances['chat'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    $chattime = $DB->get_field('chat', 'chattime', array('id' => $event->instance));
    $usertimezone = core_date::get_user_timezone($user);
    $chattimemidnight = usergetmidnight($chattime, $usertimezone);
    $todaymidnight = usergetmidnight(time(), $usertimezone);

    if ($chattime < $todaymidnight) {
        // The chat is before today. Do not show at all.
        return null;
    } else {
        // The chat is actionable if it is at some point today.
        $actionable = $chattimemidnight == $todaymidnight;

        return $factory->create_instance(
            get_string('enterchat', 'chat'),
            new \moodle_url('/mod/chat/view.php', array('id' => $cm->id)),
            1,
            $actionable
        );
    }
}

/**
 * Given a set of messages for a chat, return the completed chat sessions (including optionally not completed ones).
 *
 * @param  array $messages list of messages from a chat. It is assumed that these are sorted by timestamp in DESCENDING order.
 * @param  bool $showall   whether to include incomplete sessions or not
 * @return array           the list of sessions
 * @since  Moodle 3.5
 */
function chat_get_sessions($messages, $showall = false) {
    $sessions     = [];
    $start        = 0;
    $end          = 0;
    $sessiontimes = [];

    // Group messages by session times.
    foreach ($messages as $message) {
        // Initialise values start-end times if necessary.
        if (empty($start)) {
            $start = $message->timestamp;
        }
        if (empty($end)) {
            $end = $message->timestamp;
        }

        // If this message's timestamp has been more than the gap, it means it's been idle.
        if ($start - $message->timestamp > CHAT_SESSION_GAP) {
            // Mark this as the session end of the next session.
            $end = $message->timestamp;
        }
        // Use this time as the session's start (until it gets overwritten on the next iteration, if needed).
        $start = $message->timestamp;

        // Set this start-end pair in our list of session times.
        $sessiontimes[$end]['sessionstart'] = $start;
        if (!isset($sessiontimes[$end]['sessionend'])) {
            $sessiontimes[$end]['sessionend'] = $end;
        }
        if ($message->userid && !$message->issystem) {
            if (!isset($sessiontimes[$end]['sessionusers'][$message->userid])) {
                $sessiontimes[$end]['sessionusers'][$message->userid] = 1;
            } else {
                $sessiontimes[$end]['sessionusers'][$message->userid]++;
            }
        }
    }

    // Go through each session time and prepare the session data to be returned.
    foreach ($sessiontimes as $sessionend => $sessiondata) {
        if (!isset($sessiondata['sessionusers'])) {
            $sessiondata['sessionusers'] = [];
        }
        $sessionusers = $sessiondata['sessionusers'];
        $sessionstart = $sessiondata['sessionstart'];

        $iscomplete = $sessionend - $sessionstart > 60 && count($sessionusers) > 1;
        if ($showall || $iscomplete) {
            $sessions[] = (object) ($sessiondata + ['iscomplete' => $iscomplete]);
        }
    }

    return $sessions;
}

/**
 * Return the messages of the given chat session.
 *
 * @param  int $chatid      the chat id
 * @param  mixed $group     false if groups not used, int if groups used, 0 means all groups
 * @param  int $start       the session start timestamp (0 to not filter by time)
 * @param  int $end         the session end timestamp (0 to not filter by time)
 * @param  string $sort     an order to sort the results in (optional, a valid SQL ORDER BY parameter)
 * @return array session messages
 * @since  Moodle 3.5
 */
function chat_get_session_messages($chatid, $group = false, $start = 0, $end = 0, $sort = '') {
    global $DB;

    $params = array('chatid' => $chatid);

    // If the user is allocated to a group, only show messages from people in the same group, or no group.
    if ($group) {
        $groupselect = " AND (groupid = :currentgroup OR groupid = 0)";
        $params['currentgroup'] = $group;
    } else {
        $groupselect = "";
    }

    $select = "chatid = :chatid $groupselect";
    if (!empty($start)) {
        $select .= ' AND timestamp >= :start';
        $params['start'] = $start;
    }
    if (!empty($end)) {
        $select .= ' AND timestamp <= :end';
        $params['end'] = $end;
    }

    return $DB->get_records_select('chat_messages', $select, $params, $sort);
}

/**
 * Add a get_coursemodule_info function in case chat instance wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function chat_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, chattime, schedule';
    if (!$chat = $DB->get_record('chat', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $chat->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('chat', $chat, $coursemodule->id, false);
    }

    // Populate some other values that can be used in calendar or on dashboard.
    if ($chat->chattime) {
        $result->customdata['chattime'] = $chat->chattime;
        $result->customdata['schedule'] = $chat->schedule;
    }

    return $result;
}
