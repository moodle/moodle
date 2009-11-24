<?php
/* 
 * This script can set environment variables in the current session
 */

require('../config.php');

if (!empty($SESSION)) {
    if ($flashversion = optional_param('flashversion', false, PARAM_TEXT)) {   // eg 10.0.32
        $SESSION->flashversion = $flashversion;
    }
}

?>
