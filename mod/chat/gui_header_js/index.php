<?php

require_once('../../../config.php');
require_once('../lib.php');

require_variable($id);

if (!$chat = get_record("chat", "id", $id)) {
    error("Could not find that chat room!");
}

if (!$course = get_record("course", "id", $chat->course)) {
    error("Could not find the course this belongs to!");
}

require_login($course->id);

if (!$chat_sid = chat_login_user($chat->id, "header_js")) {
    error("Could not log in to chat room!!");
}

$strchat = get_string("modulename", "chat");


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <title>
   <?php echo "$strchat: $course->shortname: $chat->name" ?>
  </title>
 </head>
 <frameset cols="*,200" border="4" framespacing="no" frameborder="yes" marginwidth="2" marginheight="1">
  <frameset rows="1,1,*,40" border="0" framespacing="no" frameborder="no" marginwidth="2" marginheight="1">
   <frame src="../empty.php" NAME="empty" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="jsupdate.php?chat_sid=<?php echo $chat_sid; ?>&chat_enter=true" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="chatmsg.php" NAME="msg" scrolling="auto" marginwidth="2" marginheight="1">
   <frame src="chatinput.php?chat_sid=<?php echo $chat_sid; ?>" name="input" scrolling="no" marginwidth="2" marginheight="1">
  </frameset>
  <frame src="../users.php?chat_sid=<?php echo $chat_sid; ?>&chat_enter=true" name="users" scrolling="auto" marginwidth="5" marginheight="5">
 </frameset>
 <noframes>
  Sorry, this version of ARSC needs a browser that understands framesets. We have a Lynx friendly version too.
 </noframes>
</html>
