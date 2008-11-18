<?php  // $Id$

// Produce update information (json)
require('../../../config.php');
require('../lib.php');
require_once('common.php');


function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec+(float)$sec);
}
function format_user_list(&$data, $course) {
    global $CFG, $DB;
    $users = array();
    foreach($data as $v){
        $user['name'] = fullname($v);
        $user['url'] = $CFG->wwwroot.'/user/view.php?id='.$v->id.'&amp;course='.$course->id;
        $user['picture'] = print_user_picture($v->id, 0, $v->picture, false, true, false);
        $users[] = $user;
    }
    //return json_encode($users);
}

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
if (!$USER = $DB->get_record('user', array('id'=>$chatuser->userid))) { // no optimisation here, it would break again in future!
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
    if($CFG->chat_ajax_debug)
        $response['cache'] = 'yes';
} else {
    $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
    if($CFG->chat_ajax_debug)
        $response['cache'] = 'no';
}

if (!$users) {
    $response['error'] = get_string('nousers', 'error');
}

format_user_list($users, $course);

if(!empty($chat_init)) {
    $response['users'] = $users;
    echo json_encode($response);
    die;
}

//Setup course, lang and theme
course_setup($course);

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

if ($chat_lasttime == 0) { //display some previous messages
    $chat_lasttime = time() - $CFG->chat_old_ping; //TODO - any better value??
}

$params = array('groupid'=>$chatuser->groupid, 'chatid'=>$chatuser->chatid, 'lasttime'=>$chat_lasttime);

$groupselect = $chatuser->groupid ? " AND (groupid=".$chatuser->groupid." OR groupid=0) " : "";

$messages = $DB->get_records_select("chat_messages_current",
                    "chatid = :chatid AND timestamp > :lasttime $groupselect", $params,
                    "timestamp ASC");
if ($messages) {
    $num = count($messages);
    if($CFG->chat_ajax_debug)
        $response['count'] = $num;
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

$beep = false;
$us = array ();
$sendlist = false;
if ($messages && ($chat_lasttime != $chat_newlasttime)) {
    foreach ($messages as $n => &$message) {
        if($message->system == 1){
            $sendlist = true;
            $users = chat_get_users($chatuser->chatid, $chatuser->groupid, $cm->groupingid);
            if($CFG->chat_use_cache){
                $cache = new file_cache();
                $cache->set('user', $users);
            }
            format_user_list($users, $course);
        }
        $html = chat_format_message($message, $chatuser->course, $USER, $chat_lastrow);
        if ($html->beep) {
             $beep = true;
        }
        $message = $html->html;
    }
}

if($users && $sendlist){
    $response['users'] = $users;
}
if ($beep) {
    $response['beep'] = true;
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
header("X-Powered-By: MOODLE-Chat-MOD");
ob_end_flush();

$time_end = microtime_float();
$time = $time_end-$time_start;
if($CFG->chat_ajax_debug)
    $response['time']=$time;

echo json_encode($response);
?>
