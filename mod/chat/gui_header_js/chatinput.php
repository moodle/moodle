<?php

require("../../../config.php");
require("../lib.php");

if (!$chatuser = get_record("chat_users", "sid", $chat_sid)) {
    echo "Not logged in!";
}

?>
 
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\">
<html>
<head>
<title>Message Input</title>
<script language="Javascript">
<!--
scroll_active = true;
function empty_field_and_submit() {
    document.fdummy.chat_message.value=document.f.chat_message.value;
    document.fdummy.submit();
    document.f.chat_message.value='';
    document.f.chat_message.focus();
    return false;
}
// -->
</script>
</head>

<body bgcolor="<?php echo $THEME->body ?>" 
      OnLoad="document.f.chat_message.focus();document.f.chat_message.select();">


<form action="../insert.php" method="GET" target="empty" name="f" 
      OnSubmit="return empty_field_and_submit()">
&gt;&gt;<input type="text" name="chat_message" size="60" value="<?php echo $chat_pretext; ?>">
</form>

<form action="../insert.php" method="GET" target="empty" name="fdummy" 
      OnSubmit="return empty_field_and_submit()">
    <input type="hidden" name="chat_sid" value="<?php echo $chat_sid; ?>">
    <input type="hidden" name="chat_version" value="header_js">
    <input type="hidden" name="chat_message">
</form>

</body>

</html>
