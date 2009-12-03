<?php
/* 
 * This file displays some variable for the flashupgrade.swf
 * the variable contain the translated message to displayed into the flashupgrade.swf ("Please update you flash ...")
 * NOTE: flash can load variable from html (variable1=value&variable2=value2)
 */
require('../../config.php');
require_login();
echo 'alertmessage='.get_string('flashupgrademessage').'&linkmessage='.get_string('flashlinkmessage');
?>
