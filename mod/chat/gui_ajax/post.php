<?php  // $Id$
include('../../../config.php');
include('../lib.php');

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');
header('X-Powered-By: MOODLE-Chat-V2');

$chat_sid     = required_param('chat_sid', PARAM_ALPHANUM);
$chat_message = optional_param('chat_message', '', PARAM_RAW);
$beep_id      = optional_param('beep', '', PARAM_RAW);

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    chat_print_error('ERROR', get_string('notlogged','chat'));
}
if (!$chat = $DB->get_record('chat', array('id'=>$chatuser->chatid))) {
    chat_print_error('ERROR', get_string('invalidcoursemodule', 'error'));
}
if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    chat_print_error('ERROR', get_string('invaliduserid', 'error'));
}
if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    chat_print_error('ERROR', get_string('invalidcoursemodule', 'error'));
}
if (isguest()) {
    chat_print_error('ERROR', get_string('notlogged','chat'));
}
session_write_close();
chat_delete_old_users();
$chat_message = clean_text($chat_message, FORMAT_MOODLE);

if (!empty($beep_id)) {
    $chat_message = 'beep '.$beep_id;
}

if (!empty($chat_message)) {
    $message = new object();
    $message->chatid    = $chatuser->chatid;
    $message->userid    = $chatuser->userid;
    $message->groupid   = $chatuser->groupid;
    $message->message   = $chat_message;
    $message->timestamp = time();

    $chatuser->lastmessageping = time() - 2;
    $DB->update_record('chat_users', $chatuser);

    if (!($DB->insert_record('chat_messages', $message) && $DB->insert_record('chat_messages_current', $message))) {
        chat_print_error('ERROR', get_string('cantlogin','chat'));
    } else {
        echo 200;
    }
    add_to_log($course->id, 'chat', 'talk', "view.php?id=$cm->id", $chat->id, $cm->id);

    ob_end_flush();
}
