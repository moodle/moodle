<?php

require("../../../config.php");
require("../lib.php");

require_variable($chat_sid);
optional_variable($groupid);

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
    die;
}

if (!$chat = get_record("chat", "id", $chatuser->chatid)) {
    error("No chat found");
}

require_login($chat->course);
optional_variable($chat_pretext, '');

?>

<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\">
<html>
<head>
<title>Message Input</title>

<?php include("$CFG->javascript"); ?>

<script language="Javascript">
<!--
scroll_active = true;
function empty_field_and_submit() {
    cf = document.getElementById('chatform');
    cf.chat_msgidnr.value = parseInt(cf.chat_msgidnr.value) + 1;
    cf.chat_message.value = document.f.chat_message.value;
    cf.submit();
    document.f.chat_message.value='';
    document.f.chat_message.focus();
    return false;
}
// -->
</script>
</head>

<body bgcolor="<?php echo $THEME->body ?>" onload="document.f.chat_message.focus();document.f.chat_message.select();">

<!--
<form action="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport"; ?>" method="GET" target="empty" name="f" onsubmit="return empty_field_and_submit()">
-->
<form action="../insert.php" method="GET" target="empty" name="f" onsubmit="return empty_field_and_submit()">

&gt;&gt;<input type="text" name="chat_message" size="60" value="<?php echo $chat_pretext; ?>">
<?php helpbutton("chatting", get_string("helpchatting", "chat"), "chat", true, false); ?>
</form>



<form action="<?php echo "http://$CFG->chat_serverhost:$CFG->chat_serverport/"; ?>" method="GET" target="empty" id="chatform">
<!--
<form action="../insert.php" method="GET" target="empty" id="chatform" onsubmit="return empty_field_and_submit()">
-->
    <input type="hidden" name="win" value="message" />
    <input type="hidden" name="chat_version" value="sockets" />
    <input type="hidden" name="chat_message" value="" />
    <input type="hidden" name="chat_msgidnr" value="0" />
    <input type="hidden" name="chat_sid" value="<?php echo $chat_sid ?>" />
    <input type="hidden" name="groupid" value="<?php echo $groupid ?>" />
</form>

</body>

</html>

