<?php
include("../config.inc.php");
include("../functions.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <title>
   <?php echo $arsc_parameters["title"]; ?>
  </title>
 </head>
 <frameset cols="193,*" border="0" framespacing="no" frameborder="0" marginwidth="2" marginheight="1">
  <frame src="../shared/roomlist.php?arsc_sid=<?php echo $arsc_sid; ?>" name="roomlist" scrolling="auto" noresize marginwidth="0" marginheight="0">
  <frameset cols="*,120" border="0" framespacing="no" frameborder="0" marginwidth="2" marginheight="1">
   <frameset rows="1,*,40" border="1" framespacing="no" frameborder="0" marginwidth="2" marginheight="1">
    <frame src="../shared/empty.php" NAME="empty" scrolling="no" noresize marginwidth="0" marginheight="0">
    <frame src="http://<?php echo $arsc_parameters["socketserver_adress"].":".$arsc_parameters["socketserver_port"]; ?>/?arsc_sid=<?php echo $arsc_sid; ?>" NAME="msg" scrolling="auto" noresize marginwidth="2" marginheight="0">
    <frame src="chatinput.php?arsc_sid=<?php echo $arsc_sid; ?>" name="input" scrolling="no" noresize marginwidth="2" marginheight="1">
   </frameset>
   <frame src="../shared/userlist.php?arsc_sid=<?php echo $arsc_sid; ?>&arsc_enter=true" name="users" scrolling="auto" noresize marginwidth="2" marginheight="2">
  </frameset>
 </frameset>
 <noframes>
  Sorry, this version of ARSC needs a browser that understands framesets. But we have a lynx-friendly version too.
 </noframes>
</html>
