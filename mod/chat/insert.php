<?php

    include("../../config.php");
    include("lib.php");

    require_variable($chat_sid);
    require_variable($chat_version);
    require_variable($chat_message);
    optional_variable($groupid);

    if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
        echo "Not logged in!";
        die;
    }
    
    if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
        error("No chat found");
    }
    
    require_login($chat->course);

    if ($groupid) {
        if (!isteacheredit($chat->course) and !ismember($groupid)) {
            error("You can't chat here!");
        }
    }
    
/// Clean up the message

    $chat_message = clean_text($chat_message, FORMAT_MOODLE);  // Strip bad tags

/// Add the message to the database

    if (!empty($chat_message)) {
    
        $message->chatid = $chatuser->chatid;
        $message->userid = $chatuser->userid;
        $message->groupid = $groupid;
        $message->message = $chat_message;
        $message->timestamp = time();
     
        if (!insert_record("chat_messages", $message)) {
            error("Could not insert a chat message!");
        }

        $chatuser->lastmessageping = time();
        update_record("chat_users", $chatuser);

        if ($cm = get_coursemodule_from_instance("chat", $chat->id, $chat->course)) {
            add_to_log($chat->course, "chat", "talk", "view.php?id=$cm->id", $chat->id, $cm->id);
        }
    }
    
/// Go back to the other page

    if ($chat_version == "header" OR $chat_version == "box") {
        redirect("../gui_$chat_version/chatinput.php?chat_sid=$chat_sid&groupid=$groupid");
    
    } else if ($chat_version == "text") {
        redirect("../gui_$chat_version/index.php?chat_sid=$chat_sid&chat_lastid=$chat_lastid&groupid=$groupid");
    
    } else {
        redirect("empty.php");
    }

?>
