<?php

function arsc_shutdown()
{
 GLOBAL $arsc_sid,
        $arsc_my;
 if ($arsc_my["user"] <> "")
 {
  $arsc_user = $arsc_my["user"];
  $arsc_room = $arsc_my["room"];
  $arsc_nice_room = arsc_nice_room($arsc_room);
  $arsc_timeid = arsc_microtime();
  $arsc_sendtime = date("H:i:s");
  mysql_query("DELETE from arsc_users WHERE sid = '$arsc_sid'");
  mysql_query("INSERT into arsc_room_$arsc_room (message, user, sendtime, timeid) VALUES ('arsc_user_quit~~$arsc_user~~$arsc_nice_room', 'System', '$arsc_sendtime', '$arsc_timeid')");
 }
}

register_shutdown_function("arsc_shutdown");

include("../config.inc.php");
include("../functions.inc.php");
include("../filter.inc.php");

if ($arsc_my = arsc_getdatafromsid($arsc_sid))
{
 include("../shared/language/".$arsc_my["language"].".inc.php");

 $arsc_room = $arsc_my["room"];
 if ($arsc_lastid == "")
 {
  $arsc_result = mysql_query("SELECT * from arsc_room_$arsc_room ORDER BY timeid DESC");
  $arsc_b = mysql_fetch_array($arsc_result);
  $arsc_lastid = $arsc_b["timeid"];
 }
 
 echo $arsc_parameters["htmlhead_js"];
 
 set_magic_quotes_runtime(0);
 @set_time_limit(0);
 $arsc_sendtime = date("H:i:s");
 $arsc_timeid = arsc_microtime();
 $arsc_message = "/msg ".$arsc_my["user"]." ".$arsc_lang["welcome"];
 echo arsc_filter_posting("System", $arsc_sendtime, $arsc_message, $arsc_room, 0);
 $i = 0;
 while(!connection_aborted())
 {
  $arsc_my = arsc_getdatafromsid($arsc_sid);
  $arsc_room = $arsc_my["room"];
  if(!$arsc_result = mysql_query("SELECT * from arsc_room_$arsc_room ORDER BY timeid DESC"))
  {
   arsc_shutdown();
   die();
  }
  $arsc_b = mysql_fetch_array($arsc_result);
  if ($arsc_lastid == "")
  {
   $arsc_lastid = $arsc_b["timeid"];
  }
  $arsc_lastid_save = $arsc_b["timeid"];
  if ($arsc_my["level"] < 0)
  {
   switch($arsc_my["level"])
   {
    case "-1": echo arsc_filter_posting("System", date("H:i:s"), "<font size=\"4\"><b>".$arsc_lang["youwerekicked"]."</b></font>", $arsc_room, 0);
               mysql_query("DELETE from arsc_users WHERE sid = '$arsc_sid'");
               break;
   }
   break;
  }
  
  $i++;
  $arsc_result = mysql_query("SELECT * from arsc_room_$arsc_room WHERE timeid > '$arsc_lastid' ORDER BY timeid ASC, id ASC");
  while ($arsc_a = mysql_fetch_array($arsc_result))
  {
   $arsc_posting = arsc_filter_posting($arsc_a["user"], $arsc_a["sendtime"], $arsc_a["message"], $arsc_room, $arsc_a["flag_ripped"]);
   echo "$arsc_posting";
  }
  $arsc_lastid = $arsc_lastid_save;
  $arsc_ping = time();
  $arsc_ip = getenv("REMOTE_ADDR");
  mysql_query("UPDATE arsc_users SET lastping = '$arsc_ping', ip = '$arsc_ip' WHERE sid = '$arsc_sid'");
  echo " ";
  flush();
  usleep($arsc_parameters["socketserver_refresh"]);
  flush();
  flush();
  flush();
  flush();
  flush();
  // just to be sure :)
 }
}
else
{
 echo $arsc_parameters["htmlhead_out"];
}
?>
