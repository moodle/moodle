<?php

require_once('../../../config.php');
require_once('../lib.php');

$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT);  // only for teachers
$message = optional_param('message', '', PARAM_CLEANHTML);
$refresh = optional_param('refresh', '', PARAM_RAW); // force refresh
$last    = optional_param('last', 0, PARAM_INT);     // last time refresh or sending
$newonly = optional_param('newonly', 0, PARAM_BOOL); // show only new messages

$url = new moodle_url('/mod/chat/gui_basic/index.php', array('id'=>$id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
if ($message !== 0) {
    $url->param('message', $message);
}
if ($refresh !== 0) {
    $url->param('refresh', $refresh);
}
if ($last !== 0) {
    $url->param('last', $last);
}
if ($newonly !== 0) {
    $url->param('newonly', $newonly);
}
$PAGE->set_url($url);

if (!$chat = $DB->get_record('chat', array('id'=>$id))) {
    print_error('invalidid', 'chat');
}

if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    print_error('invalidcoursemodule');
}

$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/chat:chat', $context);
$PAGE->set_pagelayout('base');
$PAGE->set_popup_notification_allowed(false);

/// Check to see if groups are being used here
 if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
    if ($groupid = groups_get_activity_group($cm)) {
        if (!$group = groups_get_group($groupid)) {
            print_error('invalidgroupid');
        }
        $groupname = ': '.$group->name;
    } else {
        $groupname = ': '.get_string('allparticipants');
    }
} else {
    $groupid = 0;
    $groupname = '';
}

$strchat  = get_string('modulename', 'chat'); // must be before current_language() in chat_login_user() to force course language!!!
$strchats = get_string('modulenameplural', 'chat');
$stridle  = get_String('idle', 'chat');
if (!$chat_sid = chat_login_user($chat->id, 'basic', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}

if (!$chatusers = chat_get_users($chat->id, $groupid, $cm->groupingid)) {
    print_error('errornousers', 'chat');
}

$DB->set_field('chat_users', 'lastping', time(), array('sid'=>$chat_sid));

if (!isset($SESSION->chatprefs)) {
    $SESSION->chatprefs = array();
}
if (!isset($SESSION->chatprefs[$chat->id])) {
    $SESSION->chatprefs[$chat->id] = array();
    $SESSION->chatprefs[$chat->id]['chatentered'] = time();
}
$chatentered = $SESSION->chatprefs[$chat->id]['chatentered'];

$refreshedmessage = '';

if (!empty($refresh) and data_submitted()) {
    $refreshedmessage = $message;

    chat_delete_old_users();

} else if (empty($refresh) and data_submitted() and confirm_sesskey()) {

    if ($message!='') {
        $newmessage = new stdClass();
        $newmessage->chatid = $chat->id;
        $newmessage->userid = $USER->id;
        $newmessage->groupid = $groupid;
        $newmessage->systrem = 0;
        $newmessage->message = $message;
        $newmessage->timestamp = time();
        $DB->insert_record('chat_messages', $newmessage);
        $DB->insert_record('chat_messages_current', $newmessage);

        $DB->set_field('chat_users', 'lastmessageping', time(), array('sid'=>$chat_sid));

        add_to_log($course->id, 'chat', 'talk', "view.php?id=$cm->id", $chat->id, $cm->id);
    }

    chat_delete_old_users();

    $url = new moodle_url('/mod/chat/gui_basic/index.php', array('id'=>$id, 'newonly'=>$newonly, 'last'=>$last));
    redirect($url);
}

$PAGE->set_title("$strchat: $course->shortname: ".format_string($chat->name,true)."$groupname");
echo $OUTPUT->header();
echo $OUTPUT->container_start(null, 'page-mod-chat-gui_basic');
echo $OUTPUT->heading(get_string('participants'), 2, 'mdl-left');

echo $OUTPUT->box_start('generalbox', 'participants');
echo '<ul>';
foreach($chatusers as $chu) {
    echo '<li class="clearfix">';
    echo $OUTPUT->user_picture($chu, array('size'=>24, 'courseid'=>$course->id));
    echo '<div class="userinfo">';
    echo fullname($chu).' ';
    if ($idle = time() - $chu->lastmessageping) {
        echo '<span class="idle">'.$stridle.' '.format_time($idle).'</span>';
    } else {
        echo '<span class="idle" />';
    }
    echo '</div>';
    echo '</li>';
}
echo '</ul>';
echo $OUTPUT->box_end();
echo '<div id="send">';
echo '<form id="editing" method="post" action="index.php">';

echo '<h2><label for="message">'.get_string('sendmessage', 'message').'</label></h2>';
echo '<div>';
echo '<input type="text" id="message" name="message" value="'.s($refreshedmessage, true).'" size="60" />';
echo '</div><div>';
echo '<input type="hidden" name="id" value="'.$id.'" />';
echo '<input type="hidden" name="groupid" value="'.$groupid.'" />';
echo '<input type="hidden" name="last" value="'.time().'" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input type="submit" value="'.get_string('submit').'" />&nbsp;';
echo '<input type="submit" name="refresh" value="'.get_string('refresh').'" />';
echo '<input type="checkbox" name="newonly" id="newonly" '.($newonly?'checked="checked" ':'').'/><label for="newonly">'.get_string('newonlymsg', 'message').'</label>';
echo '</div>';
echo '</form>';
echo '</div>';

echo '<div id="messages">';
echo $OUTPUT->heading(get_string('messages', 'chat'), 2, 'mdl-left');

$allmessages = array();
$options = new stdClass();
$options->para = false;
$options->newlines = true;

$params = array('last'=>$last, 'groupid'=>$groupid, 'chatid'=>$chat->id, 'chatentered'=>$chatentered);

if ($newonly) {
    $lastsql = "AND timestamp > :last";
} else {
    $lastsql = "";
}

$groupselect = $groupid ? "AND (groupid=:groupid OR groupid=0)" : "";

$messages = $DB->get_records_select("chat_messages_current",
                    "chatid = :chatid AND timestamp > :chatentered $lastsql $groupselect", $params,
                    "timestamp DESC");

if ($messages) {
    foreach ($messages as $message) {
        $allmessages[] = chat_format_message($message, $course->id, $USER);
    }
}
echo '<table class="generaltable"><tbody>';
echo '<tr>
        <th scope="col" class="cell">' . get_string('from') . '</th>
        <th scope="col" class="cell">' . get_string('message', 'message') . '</th>
        <th scope="col" class="cell">' . get_string('time') . '</th>
      </tr>';
if (empty($allmessages)) {
    echo get_string('nomessagesfound', 'message');
} else {
    foreach ($allmessages as $message) {
        echo $message->basic;
    }
}
echo '</tbody></table>';
echo '</div>';
echo $OUTPUT->container_end();
echo $OUTPUT->footer();
