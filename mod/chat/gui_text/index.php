<?php

include("../config.inc.php");
include("../functions.inc.php");
include("../filter.inc.php");

if ($arsc_my = arsc_getdatafromsid($arsc_sid))
{
 include("../shared/language/".$arsc_my["language"].".inc.php");
 
 $arsc_user = $arsc_my["user"];
 $arsc_room = $arsc_my["room"];
 if ($arsc_lastid == "")
 {
  $arsc_result = mysql_query("SELECT * from arsc_room_$arsc_room ORDER BY timeid DESC");
  $arsc_b = mysql_fetch_array($arsc_result);
  $arsc_lastid = $arsc_b["timeid"];
 }
 
 if ($arsc_my["level"] < 0)
 {
  switch($arsc_my["level"])
  {
   case "-1": $arsc_message = $arsc_lang["youwerekicked"];
              mysql_query("DELETE from arsc_users WHERE sid = '$arsc_sid'");
              break;
  }
  header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: text/html");
  ?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
  <html>
   <body>
    <?php echo arsc_filter_posting("System", date("H:i:s"), "<font size=4><b>".$arsc_message."</b></font>", $arsc_room, 0); ?>
   </body>
  </html>
  <?php
 }
 else
 {
  if ($arsc_enter == "true")
  {
   $arsc_sendtime = date("H:i:s");
   $arsc_timeid = arsc_microtime();
   $arsc_message = "arsc_user_enter~~".$arsc_my["user"]."~~".arsc_nice_room($arsc_room);
   mysql_query("INSERT into arsc_room_$arsc_room (message, user, sendtime, timeid) VALUES ('$arsc_message', 'System', '$arsc_sendtime', '$arsc_timeid')");
  }
  header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: text/html");
 
  ?>
  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
  <html>
   <head>
    <title>
     <?php echo $arsc_parameters["title"]; ?>
    </title>
   </head>
   <body>
    <form action="../shared/chatins.php" METHOD="POST">
     <a href="index.php?arsc_sid=<?php echo $arsc_sid; ?>&arsc_lastid=<?php echo $arsc_lastid; ?>"><?php echo $arsc_lang["refreshmessages"]; ?></a>
     <input type="hidden" name="arsc_sid" value="<?php echo $arsc_sid; ?>">
     <input type="hidden" name="arsc_lastid" value="<?php echo $arsc_lastid; ?>">
     <input type="hidden" name="arsc_chatversion" value="text">
     <input type="text" name="arsc_message" size="30" maxlength="<?php echo $arsc_parameters["input_maxsize"]; ?>" value="<?php echo $arsc_pretext; ?>">
     <input type="submit" value="<?php echo $arsc_lang["sendmessage"]; ?>">
     &nbsp;&nbsp;&nbsp;<a href="../logout.php?arsc_sid=<?php echo $arsc_sid; ?>"><?php echo $arsc_lang["leave"]; ?></a>
    </form>
    <?php
     set_magic_quotes_runtime(0);
     echo $arsc_lang["usersinroom"]." ".arsc_nice_room($arsc_room).": ";
     $arsc_result = mysql_query("SELECT user, level from arsc_users WHERE room = '$arsc_room' ORDER BY level DESC, user ASC");
     while ($arsc_a = mysql_fetch_array($arsc_result))
     {
      $arsc_opstring = "";
      if ($arsc_a["level"] == 1)
      {
       $arsc_opstring = "@";
      }
      if ($arsc_a["level"] == 2)
      {
       $arsc_opstring = "<b>@</b>";
      }
      if ($arsc_a["user"] == $arsc_my["user"])
      {
       echo "<i>".$arsc_opstring.$arsc_a["user"]."</i>\n";
      }
      else
      {
       echo "<a href=\"../version_".$arsc_my["version"]."/index.php?arsc_sid=".$arsc_my["sid"]."&arsc_lastid=".$arsc_lastid."&arsc_pretext=".urlencode("/msg ".$arsc_a["user"]." ")."\">".$arsc_opstring.$arsc_a["user"]."</a>\n";
      }
     }
     echo "<br>";
     $arsc_result = mysql_query("SELECT * from arsc_room_$arsc_room WHERE timeid > '$arsc_lastid' ORDER BY timeid DESC");
     while ($arsc_a = mysql_fetch_array($arsc_result))
     {
      echo arsc_filter_posting($arsc_a["user"], $arsc_a["sendtime"], $arsc_a["message"], $arsc_room, $arsc_a["flag_ripped"])."\n";
     }
     $arsc_sendtime = date("H:i:s");
     $arsc_timeid = arsc_microtime();
     $arsc_message = "/msg ".$arsc_my["user"]." ".$arsc_lang["welcome"];
     echo arsc_filter_posting("System", $arsc_sendtime, $arsc_message, $arsc_room, 0);
     $arsc_ping = time();
     $arsc_ip = getenv("REMOTE_ADDR");
     mysql_query("UPDATE arsc_users SET lastping = '$arsc_ping', ip = '$arsc_ip' WHERE user = '$arsc_user'");
    ?>
   </body>
  </html>
  <?php
 }
}
else
{
 echo $arsc_htmlhead_out;
}
?>
