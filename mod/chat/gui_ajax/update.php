<?php
/**
 * Produce update data (json format)
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

define('NO_MOODLE_COOKIES', true); // session not used here

require_once('../../../config.php');
require_once('../lib.php');
require_once('common.php');

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: MOODLE-Chat-V2');


$time_start = microtime_float();

$chat_sid      = required_param('chat_sid', PARAM_ALPHANUM);
$chat_init     = optional_param('chat_init', 0, PARAM_INT);
$chat_lasttime = optional_param('chat_lasttime', 0, PARAM_INT);
$chat_lastrow  = optional_param('chat_lastrow', 1, PARAM_INT);

$response = array();

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    chat_print_error('ERROR', get_string('notlogged','chat'));
}

//Get the minimal course
if (!$course = $DB->get_record('course', array('id'=>$chatuser->course), 'id,theme,lang')) {
    chat_print_error('ERROR', get_string('invalidcourseid', 'error'));
}

//Get the user theme and enough info to be used in chat_format_message() which passes it along to
if (!$USER = $DB->get_record('user', array('id'=>$chatuser->userid))) {
    // no optimisation here, it would break again in future!
    chat_print_error('ERROR', get_string('invaliduserid', 'error'));
}

if (!$chat = $DB->get_record('chat', array('id'=>$chatuser->chatid))) {
    chat_print_error('ERROR', get_string('invalidcoursemodule', 'error'));
}

if (!$cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $course->id)) {
    chat_print_error('ERROR', get_string('invalidcoursemodule', 'error'));
}
// setup $PAGE so that format_text will work properly
$PAGE->set_cm($cm, $course, $chat);

$OUTPUT->initialise_deprecated_cfg_pixpath();

if($CFG->chat_use_cache){
    $cache = new file_cache();
    $users = $cache->get('user');
    if(empty($users)) {
        $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
        $cache->set('user', $users);
    }
} else {
    $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
}

$users = format_user_list($users, $course);

if(!empty($chat_init)) {
    $response['users'] = $users;
    echo json_encode($response);
    exit;
}

// force deleting of timed out users if there is a silence in room or just entering
if ((time() - $chat_lasttime) > $CFG->chat_old_ping) {
    // must be done before chat_get_latest_message!!!
    chat_delete_old_users();
}

if ($latest_message = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
    $chat_newlasttime = $latest_message->timestamp;
} else {
    $chat_newlasttime = 0;
}

if ($chat_lasttime == 0) {
    $chat_lasttime = time() - $CFG->chat_old_ping;
}

$params = array('groupid'=>$chatuser->groupid, 'chatid'=>$chatuser->chatid, 'lasttime'=>$chat_lasttime);

$groupselect = $chatuser->groupid ? " AND (groupid=".$chatuser->groupid." OR groupid=0) " : "";

$messages = $DB->get_records_select('chat_messages_current',
    'chatid = :chatid AND timestamp > :lasttime '.$groupselect, $params,
    'timestamp ASC');

if (!empty($messages)) {
    $num = count($messages);
} else {
    $num = 0;
}

$chat_newrow = ($chat_lastrow + $num) % 2;

$send_user_list = false;
if ($messages && ($chat_lasttime != $chat_newlasttime)) {
    foreach ($messages as $n => &$message) {
        $tmp = new stdclass;
        // when somebody enter room, user list will be updated
        if($message->system == 1){
            $send_user_list = true;
            $tmp->type = 'system';
            $users = format_user_list(
                chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid), $course);
        }
        if ($html = chat_format_message($message, $chatuser->course, $USER, $chat_lastrow)) {
            if ($html->beep) {
                $tmp->type = 'beep';
            } elseif (empty($tmp->type)) {
                $tmp->type = 'user';
            }
            $tmp->msg  = $html->html;
            $message = $tmp;
        } else {
            unset($message);
        }
    }
}

if(!empty($users) && $send_user_list){
    // return users when system message coming
    $response['users'] = $users;
}

$DB->set_field('chat_users', 'lastping', time(), array('id'=>$chatuser->id));

$response['lasttime'] = $chat_newlasttime;
$response['lastrow']  = $chat_newrow;
if($messages){
    $response['msgs'] = $messages;
}

$time_end = microtime_float();
$time = $time_end - $time_start;
if(!empty($CFG->chat_ajax_debug)) {
    $response['time'] = $time;
}

echo json_encode($response);

header('Content-Length: ' . ob_get_length() );

ob_end_flush();
exit;
