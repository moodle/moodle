<?php

include("../config.inc.php");
include("../functions.inc.php");
include("../filter.inc.php");

if ($arsc_my = arsc_getdatafromsid($arsc_sid))
{
 include("../shared/language/".$arsc_my["language"].".inc.php");
 
 if ($arsc_my["level"] >= 0)
 {
  echo $arsc_parameters["htmlhead_msginput_js"];
  ?>
    <form action="../shared/chatins.php" method="GET" target="empty" name="f" OnSubmit="return empty_field_and_submit()">
     <input type="text" name="arsc_message" size="50" maxlength="<?php echo $arsc_parameters["input_maxsize"]; ?>" value="<?php echo $arsc_pretext; ?>">
    </form>
    <form action="../shared/chatins.php" method="GET" target="empty" name="fdummy" OnSubmit="return empty_field_and_submit()">
     <input type="hidden" name="arsc_sid" value="<?php echo $arsc_sid; ?>">
     <input type="hidden" name="arsc_chatversion" value="sockets">
     <input type="hidden" name="arsc_message">
    </form>
   </body>
  </html>
  <?php
 }
 else
 {
  echo $arsc_htmlhead_out;
 }
}
else
{
 echo $arsc_htmlhead_out;
}
?>