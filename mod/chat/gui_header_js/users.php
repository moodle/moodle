<?php

define('NO_MOODLE_COOKIES', true); // session not used here

require('../../../config.php');
require('../lib.php');

$chat_sid   = required_param('chat_sid', PARAM_ALPHANUM);
$beep       = optional_param('beep', 0, PARAM_INT);  // beep target

$PAGE->set_url('/mod/chat/gui_header_js/users.php', array('chat_sid'=>$chat_sid));

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    print_error('notlogged', 'chat');
}

//Get the minimal course
if (!$course = $DB->get_record('course', array('id'=>$chatuser->course))) {
    print_error('invalidcourseid');
}

//Get the user theme and enough info to be used in chat_format_message() which passes it along to
if (!$USER = $DB->get_record('user', array('id'=>$chatuser->userid))) { // no optimisation here, it would break again in future!
    print_error('invaliduser');
}

$PAGE->set_pagelayout('embedded');

$USER->description = '';

//Setup course, lang and theme
$PAGE->set_course($course);

$courseid = $chatuser->course;

if (!$cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $courseid)) {
    print_error('invalidcoursemodule');
}

if ($beep) {
    $message->chatid    = $chatuser->chatid;
    $message->userid    = $chatuser->userid;
    $message->groupid   = $chatuser->groupid;
    $message->message   = "beep $beep";
    $message->system    = 0;
    $message->timestamp = time();

    $DB->insert_record('chat_messages', $message);
    $DB->insert_record('chat_messages_current', $message);

    $chatuser->lastmessageping = time();          // A beep is a ping  ;-)
}

$chatuser->lastping = time();
$DB->set_field('chat_users', 'lastping', $chatuser->lastping, array('id'=>$chatuser->id));

$refreshurl = "users.php?chat_sid=$chat_sid";

/// Get list of users

if (!$chatusers = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid)) {
    print_error('errornousers', 'chat');
}

$uidles = Array();
$i = 0;
foreach ($chatusers as $chatuser) {
    $uidles[$i] = 'uidle{$chatuser->id}';
    $i++;
}
$PAGE->requires->data_for_js('uidles', $uidles);
$PAGE->requires->js('/mod/chat/gui_header_js/chat_gui_header.js');
$PAGE->requires->js_function_call('start', null, true);
ob_start();
echo $OUTPUT->header();

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
    echo $OUTPUT->user_picture($chatuser, array('courseid'=>$courseid));
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
echo $OUTPUT->footer();

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

