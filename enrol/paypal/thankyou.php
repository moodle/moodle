<?php
require ("../../config.php");


if (isset($_GET['courseid'])) {
    $course = get_record("course", "id", $_GET['courseid']);
}

if ($SESSION->wantsurl) {
    $destination = $SESSION->wantsurl;
    unset($SESSION->wantsurl);
} else {
    if ($course) {
        $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
    } else {
        $destination = "$CFG->wwwroot/course/";
    }
}


$str = "Thank you for your payment.";
if ($course) {
    $str .= "You should now be able to access $course->fullname";
}



print_header();

notice($str, $destination);


?>
