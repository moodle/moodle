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
  echo $arsc_parameters["htmlhead"];
  echo arsc_filter_posting("System", date("H:i:s"), "<font size=\"4\"><b>".$arsc_message."</b></font>", $arsc_room, 0);
  ?>
   </body>
  </html>
  <?php
 }
 else
 {
  header("Expires: Sun, 28 Dec 1997 09:32:45 GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
  header("Content-Type: text/html");
  header("Refresh: 4; URL=chatmsg.php?arsc_sid=".$arsc_sid."&arsc_lastid=".$arsc_lastid."&dummy=".time()."#end");
  echo $arsc_parameters["htmlhead"];
  
  set_magic_quotes_runtime(0);
  $arsc_sendtime = date("H:i:s");
  $arsc_timeid = arsc_microtime();
  $arsc_message = "/msg ".$arsc_my["user"]." ".$arsc_lang["welcome"];
  echo arsc_filter_posting("System", $arsc_sendtime, $arsc_message, $arsc_room, 0);
  $arsc_result = mysql_query("SELECT * FROM arsc_room_$arsc_room WHERE timeid > '$arsc_lastid' ORDER BY timeid ASC");
  while ($arsc_a = mysql_fetch_array($arsc_result))
  {
   echo arsc_filter_posting($arsc_a["user"], $arsc_a["sendtime"], $arsc_a["message"], $arsc_room, $arsc_a["flag_ripped"])."\n";
  }
  $arsc_ping = time();
  $arsc_ip = getenv("REMOTE_ADDR");
  mysql_query("UPDATE arsc_users SET lastping = '$arsc_ping', ip = '$arsc_ip' WHERE user = '$arsc_user'");
  ?>
    <a name="end"></a>
   </body>
  </html>
  <?php
 }
}
else
{
 echo $arsc_parameters["htmlhead_out"];
}
?>
