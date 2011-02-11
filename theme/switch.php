<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This code processes switch requests-> ... -> Theme selector UI.
 */

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
} else {
    $current_prefs = array();
}

if(!empty($switched)){
    $switched = 0;
} else {
    $switched = 1;
}

$device_pref = array();
$device_pref['device'] = $device_type;
$device_pref['switched'] = $switched;

$current_prefs[] = $device_pref;

set_user_preference('switchthemes',json_encode($current_prefs),$USER->id);

redirect($url);