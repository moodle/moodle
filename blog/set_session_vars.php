<?php
/// inspired/taken from moodle calendar module's set.php file

    require_once('../config.php');

    if (empty($CFG->bloglevel)) {
        error('Blogging is disabled!');
    }

    $referrer = required_param('referrer', PARAM_URL);

    if (isset($SESSION->blog_editing_enabled)) {
         $SESSION->blog_editing_enabled = !$SESSION->blog_editing_enabled;
    } else {
         $SESSION->blog_editing_enabled = true;
    }

    redirect($referrer);
?>
