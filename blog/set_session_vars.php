<?php
/// inspired/taken from moodle calendar module's set.php file

    require_once('../config.php');

    $referrer = required_param('referrer', PARAM_URL);

    if (isset($SESSION->blog_editing_enabled)) {
         $SESSION->blog_editing_enabled = !$SESSION->blog_editing_enabled;
    } else {
         $SESSION->blog_editing_enabled = true;
    }

    redirect($referrer);
?>
