<?php

include("../../config.php");
include("lib.php");

require_variable($chat_sid);
optional_variable($groupid, 0);

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
    die;
}

if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
    error("No chat found");
}

require_login($chat->course);


if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
    error("Could not find chat! id = $chatuser->chatid");
}

if (isset($_GET['chat_enter'])) {
    $message->chatid = $chatuser->chatid;
    $message->userid = $chatuser->userid;
    $message->groupid = $groupid;
    $message->message = "enter";
    $message->system = 1;
    $message->timestamp = time();
 
    if (!insert_record("chat_messages", $message)) {
        error("Could not insert a chat message!");
    }
}

if (isset($_GET['beep'])) {
    $message->chatid = $chatuser->chatid;
    $message->userid = $chatuser->userid;
    $message->groupid = $groupid;
    $message->message = "beep $beep";
    $message->system = 0;
    $message->timestamp = time();
 
    if (!insert_record("chat_messages", $message)) {
        error("Could not insert a chat message!");
    }

    $chatuser->lastmessageping = time();          // A beep is a ping  ;-)
    update_record("chat_users", $chatuser);
}


/// Delete users who are using text version and are old

chat_delete_old_users();


/// Print headers

header("Expires: Wed, 4 Oct 1978 09:32:45 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html");
header("Refresh: $CFG->chat_refresh_userlist; URL=users.php?chat_sid=$chat_sid&groupid=$groupid");

print_header();

$timenow = time();

$stridle   = get_string("idle", "chat");
$strbeep   = get_string("beep", "chat");
$str->day   = get_string("day");
$str->days  = get_string("days");
$str->hour  = get_string("hour");
$str->hours = get_string("hours");
$str->min   = get_string("min");
$str->mins  = get_string("mins");
$str->sec   = get_string("sec");
$str->secs  = get_string("secs");

/// Get list of users

if (!$chatusers = chat_get_users($chatuser->chatid, $groupid)) {
    print_string("errornousers", "chat");
    exit;
}


echo "<table width=\"100%\">";
foreach ($chatusers as $chatuser) {
    $lastping = $timenow - $chatuser->lastmessageping;
    echo "<tr><td width=35>";
    echo "<a target=\"_new\" onClick=\"return openpopup('/user/view.php?id=$chatuser->id&course=$chat->course','user$chatuser->id','');\" href=\"$CFG->wwwroot/user/view.php?id=$chatuser->id&course=$chat->course\">";
    print_user_picture($chatuser->id, 0, $chatuser->picture, false, false, false);
    echo "</a></td><td valign=center>";
    echo "<p><font size=1>";
    echo fullname($chatuser)."<br />";
    echo "<font color=\"#888888\">$stridle: ".format_time($lastping, $str)."</font>";
    echo " <a href=\"users.php?chat_sid=$chat_sid&beep=$chatuser->id&groupid=$groupid\">$strbeep</a>";
    echo "</font></p>";
    echo "<td></tr>";
}
echo "</table>";

?>
