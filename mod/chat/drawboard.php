<?php

include("../config.inc.php");
include("../functions.inc.php");

$arsc_result = mysql_query("SELECT COUNT(*) AS anzahl FROM arsc_users WHERE sid = '$arsc_sid'");
$arsc_a = mysql_fetch_array($arsc_result);
if ($arsc_a["anzahl"] == 1)
{
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
 <html>
  <head>
   <title>
    Abacho - Drawboard
   </title>
  </head>
  <body bgcolor="#000000" topmargin="0" leftmargin="0" marginleft="0" margintop="0">
   <applet code="drawboard/Main.class" width="<?php echo $arsc_parameters["drawboard_width"]; ?>" height="<?php echo $arsc_parameters["drawboard_height"]; ?>">
    <param name="port" value="<?php echo $arsc_parameters["drawboard_port"]; ?>">
    <param name="bgcolor" value="FFFFFF">
    <param name="menubgcolor" value="FAE6A6">
    <param name="emptythumbnailcolor" value="FDF0C6">
    <param name="countercolor" value="000000">
    <param name="pencolor" value="0000FF">
    <param name="skindef" value="abacho/abacho.def">
    Unfortunatelly, your browser doesn't support Java applets. You have to use another one.
   </applet>
  </body>
 </html>
 <?php
}
else
{
 header("Location: ../shared/empty.php");
 die();
}
?>