<?php  //$Id$

$nomoodlecookie = true; // session not used here
require '../../../config.php';

$id = required_param('id', PARAM_INT); // course id

require_user_key_login('grade/export', $id); // we want different keys for each course

// use the same page parameters as export.php and append &key=sdhakjsahdksahdkjsahksadjksahdkjsadhksa
require 'export.php';

?>