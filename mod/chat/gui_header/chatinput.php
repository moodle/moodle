<?php

include("../config.inc.php");
include("../functions.inc.php");

if ($arsc_my = arsc_getdatafromsid($arsc_sid))
{
 include("../shared/language/".$arsc_my["language"].".inc.php");
 
 if ($arsc_my["level"] >= 0)
 {
  echo $arsc_parameters["htmlhead_msginput"];
  ?>
    <form action="../shared/chatins.php" METHOD="POST" name="f">
     <input type="hidden" name="arsc_sid" value="<?php echo $arsc_sid; ?>">
     <input type="hidden" name="arsc_chatversion" value="header">
     <input type="text" name="arsc_message" size="50" maxlength="<?php echo $arsc_parameters["input_maxsize"]; ?>" value="<?php echo $arsc_pretext; ?>">
     <input type="submit" value="<?php echo $arsc_lang["sendmessage"]; ?>">
    </form>
   </body>
  </html>
  <?php
 }
 else
 {
  echo $arsc_parameters["htmlhead_out"];
 }
}
else
{
 echo $arsc_parameters["htmlhead_out"];
}
?>
