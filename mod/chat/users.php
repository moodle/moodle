<?php

include("../../config.php");
include("lib.php");

require_variable($chat_sid);

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
    die;
}

if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
    error("Could not find chat! id = $chatuser->chatid");
}

if (isset($chat_enter)) {
    $message->chatid = $chatuser->chatid;
    $message->userid = $chatuser->userid;
    $message->message = "enter";
    $message->system = 1;
    $message->timestamp = time();
 
    if (!insert_record("chat_messages", $message)) {
        error("Could not insert a chat message!");
    }
}

/// Delete users who are using text version and are old

$timeold = time() - CHAT_OLD_PING;

delete_records_select("chat_users", "lastping < '$timeold'");

 
/// Get list of users

if (!$chatusers = chat_get_users($chatuser->chatid)) {
    error("Could not find any users!");
}


/// Print headers

header("Expires: Wed, 4 Oct 1978 09:32:45 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html");
header("Refresh: ".CHAT_REFRESH_USERLIST."; URL=users.php?chat_sid=".$chat_sid);

print_header();

echo "<table width=\"100%\">";
foreach ($chatusers as $chatuser) {
    echo "<tr><td width=35>";
    print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
    echo "</td><td valign=center>";
    echo "<p><font size=1>$chatuser->firstname $chatuser->lastname</font></p>";
    echo "<td></tr>";
}
echo "</table>";

?>
