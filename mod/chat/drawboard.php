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
   <APPLET CODE="drawboard/Main.class" WIDTH="<?php echo $arsc_parameters["drawboard_width"]; ?>" HEIGHT="<?php echo $arsc_parameters["drawboard_height"]; ?>">
    <param name="port" value="<?php echo $arsc_parameters["drawboard_port"]; ?>">
    <param name="bgcolor" value="FFFFFF">
    <PARAM NAME="menubgcolor" VALUE="FAE6A6">
    <PARAM NAME="emptythumbnailcolor" VALUE="FDF0C6">
    <PARAM NAME="countercolor" VALUE="000000">
    <param name="pencolor" value="0000FF">
    <PARAM NAME="skindef" VALUE="abacho/abacho.def">
    Unfortunatelly, your browser doesn't support Java applets. You have to use another one.
   </APPLET>
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