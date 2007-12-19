<?php  //$Id$

$settings->add(new admin_setting_configtextarea('filter_censor_badwords', get_string('badwordslist','admin'),
               get_string('badwordsconfig', 'admin').'<br />'.get_string('badwordsdefault', 'admin'), ''));

?>
