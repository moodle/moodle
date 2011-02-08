<?php
require('../config.php');

$url = required_param('url', PARAM_TEXT);

$current_prefs = get_user_preferences('switchthemes');

$current_prefs = json_decode($current_prefs, true);
$device_type = get_device_type();

if(!empty($current_prefs)){
    $i = 0;
	
	foreach($current_prefs as $current){
		if($current['device'] == $device_type){
		   $switched = $current['switched']; 
		   array_splice($current_prefs,$i,1);
		   break;
		}
		
		$i++;
	}
}
else {
	$current_prefs = array();
}

if(!empty($switched)){
	$switched = 0;
}
else {
	$switched = 1;
}

$device_pref = array();
$device_pref['device'] = $device_type;
$device_pref['switched'] = $switched;

$current_prefs[] = $device_pref;

set_user_preference('switchthemes',json_encode($current_prefs),$USER->id);

redirect($url);