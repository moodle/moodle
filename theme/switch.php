<?php
require('../config.php');

$url = required_param('url', PARAM_TEXT);

if(!empty($USER->switchtheme)){
	$pref = 0;
}
else {
	$pref = 1;
}
	
set_user_preference('switchtheme',$pref,$USER->id);

redirect($url);