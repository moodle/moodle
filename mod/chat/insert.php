<?php

include("../../config.php");
include("lib.php");

require_variable($chat_sid);
require_variable($chat_version);
require_variable($chat_message);

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
    die;
}

/// Delete old messages here

/// Clean up the message

$chat_message = clean_text($chat_message, FORMAT_MOODLE);  // Strip bad tags

if (!empty($chat_message)) {

    $message->chatid = $chatuser->chatid;
    $message->userid = $chatuser->userid;
    $message->message = $chat_message;
    $message->timestamp = time();
 
    if (!insert_record("chat_messages", $message)) {
        error("Could not insert a chat message!");
    }
}

if ($chat_version == "header" OR $chat_version == "box") {
    redirect("../gui_$chat_version/chatinput.php?chat_sid=$chat_sid");

} else if ($chat_version == "text") {
    redirect("../gui_$chat_version/index.php?chat_sid=$chat_sid&chat_lastid=$chat_lastid");

} else {
    redirect("empty.php");
}

?>
