<?php  //$Id$

$nomoodlecookie = true; // session not used here
require '../../../config.php';

$id = required_param('id', PARAM_INT); // course id

require_user_key_login('grade/import', $id); // we want different keys for each course

if (empty($CFG->gradepublishing)) {
    error('Grade publishing disabled');
}

// use the same page parameters as import.php and append &key=sdhakjsahdksahdkjsahksadjksahdkjsadhksa
require 'import.php';

?>