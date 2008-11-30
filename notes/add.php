<?php // $Id$

    require_once('../config.php');

    $courseid      = required_param('course', PARAM_INT);
    $userid        = required_param('user', PARAM_INT);

    redirect("edit.php?courseid=$courseid&amp;userid=$userid");

    //note: this script is not used anymore - removed from HEAD
?>
