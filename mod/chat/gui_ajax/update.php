<?php
/**
 * Produce update data (json format)
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once('../../../config.php');
require_once('../lib.php');
require_once('common.php');

$time_start = microtime_float();

$chat_sid      = required_param('chat_sid', PARAM_ALPHANUM);
$chat_lasttime = optional_param('chat_lasttime', 0, PARAM_INT);
$chat_init     = optional_param('chat_init', 0, PARAM_INT);
$chat_lastrow  = optional_param('chat_lastrow', 1, PARAM_INT);

$response = array();

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    $response['error'] = get_string('notlogged', 'chat');
}

//Get the minimal course
if (!$course = $DB->get_record('course', array('id'=>$chatuser->course), 'id,theme,lang')) {
    $response['error'] = get_string('invalidcourseid', 'error');
}

//Get the user theme and enough info to be used in chat_format_message() which passes it along to
if (!$USER = $DB->get_record('user', array('id'=>$chatuser->userid))) {
    // no optimisation here, it would break again in future!
    $response['error'] = get_string('invaliduserid', 'error');
}

if (!$cm = get_coursemodule_from_instance('chat', $chatuser->chatid, $course->id)) {
    $response['error'] = get_string('invalidcoursemodule', 'error');
}

$users = new stdclass;

if($CFG->chat_use_cache){
    $cache = new file_cache();
    $users = $cache->get('user');
    if(empty($users)) {
        $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
        $cache->set('user', $users);
    }
    if($CFG->chat_ajax_debug) {
        $response['cache'] = true;
    }
} else {
    $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
    if($CFG->chat_ajax_debug) {
        $response['cache'] = false;
    }
}

if (!$users) {
    $response['error'] = get_string('nousers', 'error');
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
if ($message = chat_get_latest_message($chatuser->chatid, $chatuser->groupid)) {
    $chat_newlasttime = $message->timestamp;
} else {
    $chat_newlasttime = 0;
}

if ($chat_lasttime == 0) {
    $chat_lasttime = time() - $CFG->chat_old_ping;
}

$params = array('groupid'=>$chatuser->groupid, 'chatid'=>$chatuser->chatid, 'lasttime'=>$chat_lasttime);

$groupselect = $chatuser->groupid ? " AND (groupid=".$chatuser->groupid." OR groupid=0) " : "";

$messages = $DB->get_records_select("chat_messages_current",
                    "chatid = :chatid AND timestamp > :lasttime $groupselect", $params,
                    "timestamp ASC");
if ($messages) {
    $num = count($messages);
    if($CFG->chat_ajax_debug) {
        $response['count'] = $num;
    }
} else {
    $num = 0;
}

$chat_newrow = ($chat_lastrow + $num) % 2;

header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

ob_start();

$sendlist = false;
if ($messages && ($chat_lasttime != $chat_newlasttime)) {
    foreach ($messages as $n => &$message) {
        $tmp = new stdclass;
        // when somebody enter room, user list will be updated
        if($message->system == 1){
            $sendlist = true;
            $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
            if($CFG->chat_use_cache){
                $cache = new file_cache();
                $cache->set('user', $users);
            }
            $users = format_user_list($users, $course);
        }
        if ($html = chat_format_message($message, $chatuser->course, $USER, $chat_lastrow)) {
            if ($html->beep) {
                $tmp->type = 'beep';
            }
            $tmp->msg  = $html->html;
            $message = $tmp;
        } else {
            unset($message);
        }
    }
}

if($users && $sendlist){
    // return users when system message coming
    $response['users'] = $users;
}

$response['lasttime'] = $chat_newlasttime;
$response['lastrow']  = $chat_newrow;
if($messages){
    $response['msgs'] = $messages;
}

// set user's last active time
$chatuser->lastping = time();
$DB->set_field('chat_users', 'lastping', $chatuser->lastping, array('id'=>$chatuser->id));
header("Content-Length: " . ob_get_length() );
header("X-Powered-By: MOODLE-Chat-V2");
ob_end_flush();

$time_end = microtime_float();
$time = $time_end - $time_start;
if($CFG->chat_ajax_debug) {
    $response['time']=$time;
}

echo json_encode($response);
