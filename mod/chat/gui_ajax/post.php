<?php  // $Id$
include('../../../config.php');
include('../lib.php');

$chat_sid     = required_param('chat_sid', PARAM_ALPHANUM);
$chat_message = required_param('chat_message', PARAM_RAW);

if (!$chatuser = $DB->get_record('chat_users', array('sid'=>$chat_sid))) {
    echo 'invalid sid';
}
if (!$chat = $DB->get_record('chat', array('id'=>$chatuser->chatid))) {
    echo 'invalid chat id';
}
if (!$course = $DB->get_record('course', array('id'=>$chat->course))) {
    echo 'invalid course id';
}
if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    echo 'invalid course module';
}
if (isguest()) {
    echo 'Guest does not have access to chat rooms';
}
session_write_close();
chat_delete_old_users();
$chat_message = clean_text($chat_message, FORMAT_MOODLE);

//TODO: Before insert the chat message into database, we should push the
//message into a global object (which can hold 100 messages), when user request
//the lastest messages, we compare the oldest messsage's timestamp $a to user's
//timestamp $b, if $a<$b, directly return messages in global object, otherwise,
//fetch the message from database.

if (!empty($chat_message)) {
    $message = new object();
    $message->chatid    = $chatuser->chatid;
    $message->userid    = $chatuser->userid;
    $message->groupid   = $chatuser->groupid;
    $message->message   = $chat_message;
    $message->timestamp = time();

    if (!($DB->insert_record('chat_messages', $message) && $DB->insert_record('chat_messages_current', $message))) {
        echo get_string('cantlogin', 'chat');
    } else {
        echo 200;
    }

    $chatuser->lastmessageping = time() - 2;
    $DB->update_record('chat_users', $chatuser);

    add_to_log($course->id, 'chat', 'talk', "view.php?id=$cm->id", $chat->id, $cm->id);
}
?>
