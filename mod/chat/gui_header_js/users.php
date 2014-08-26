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

define('NO_MOODLE_COOKIES', true); // Session not used here.

require_once('../../../config.php');
require_once($CFG->dirroot.'/mod/chat/lib.php');

$chatsid   = required_param('chat_sid', PARAM_ALPHANUM);
$beep       = optional_param('beep', 0, PARAM_INT);  // Beep target.

$PAGE->set_url('/mod/chat/gui_header_js/users.php', array('chat_sid' => $chatsid));
$PAGE->set_popup_notification_allowed(false);

if (!$chatuser = $DB->get_record('chat_users', array('sid' => $chatsid))) {
    print_error('notlogged', 'chat');
}

// Get the minimal course.
if (!$course = $DB->get_record('course', array('id' => $chatuser->course))) {
    print_error('invalidcourseid');
}

// Get the user theme and enough info to be used in chat_format_message() which passes it along to.
// No optimisation here, it would break again in future!
if (!$user = $DB->get_record('user', array('id' => $chatuser->userid, 'deleted' => 0, 'suspended' => 0))) {
    print_error('invaliduser');
}
\core\session\manager::set_user($user);

$PAGE->set_pagelayout('embedded');

// Setup course, lang and theme.
$PAGE->set_course($course);

$courseid = $chatuser->course;

if (!$cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $courseid)) {
    print_error('invalidcoursemodule');
}

if ($beep) {
    chat_send_chatmessage($chatuser, "beep $beep", 0, $cm);
    $chatuser->lastmessageping = time(); // A beep is a ping.
}

$chatuser->lastping = time();
$DB->set_field('chat_users', 'lastping', $chatuser->lastping, array('id' => $chatuser->id));

$refreshurl = "users.php?chat_sid=$chatsid";

// Get list of users.

if (!$chatusers = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid)) {
    print_error('errornousers', 'chat');
}

$uidles = Array();
foreach ($chatusers as $chatuser) {
    $uidles[] = $chatuser->id;
}

$module = array(
    'name'      => 'mod_chat_header',
    'fullpath'  => '/mod/chat/gui_header_js/module.js',
    'requires'  => array('node')
);
$PAGE->requires->js_init_call('M.mod_chat_header.init_users', array($uidles), false, $module);

// Print user panel body.
$timenow    = time();
$stridle    = get_string('idle', 'chat');
$strbeep    = get_string('beep', 'chat');

$table = new html_table();
$table->width = '100%';
$table->data = array();
foreach ($chatusers as $chatuser) {
    $lastping = $timenow - $chatuser->lastmessageping;
    $min = (int) ($lastping / 60);
    $sec = $lastping - ($min * 60);
    $min = $min < 10 ? '0'.$min : $min;
    $sec = $sec < 10 ? '0'.$sec : $sec;
    $idle = $min.':'.$sec;

    $row = array();
    $row[0] = $OUTPUT->user_picture($chatuser, array('courseid' => $courseid, 'popup' => true));
    $row[1]  = html_writer::start_tag('p');
    $row[1] .= html_writer::start_tag('font', array('size' => '1'));
    $row[1] .= fullname($chatuser).'<br />';
    $row[1] .= html_writer::tag('span', $stridle . html_writer::tag('span',
                                                                    $idle,
                                                                    array('name' => 'uidles', 'id' => 'uidle'.$chatuser->id)),
                                array('class' => 'dimmed_text')) . ' ';
    $row[1] .= html_writer::tag('a', $strbeep, array('href' => new moodle_url('/mod/chat/gui_header_js/users.php',
                                                                              array('chat_sid' => $chatsid,
                                                                              'beep' => $chatuser->id))));
    $row[1] .= html_writer::end_tag('font');
    $row[1] .= html_writer::end_tag('p');
    $table->data[] = $row;
}

ob_start();
echo $OUTPUT->header();
echo html_writer::tag('div', html_writer::tag('a', get_string('refresh'),
        array('href' => $refreshurl, 'id' => 'refreshLink')), array('style' => 'display:none'));
echo html_writer::table($table);
echo $OUTPUT->footer();

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

exit; // No further output.

