<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require('../../config.php');
require_login();
echo 'alertmessage='.get_string('flashupgrademessage').'&linkmessage='.get_string('flashlinkmessage');
?>
