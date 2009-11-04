<?php
      // format.php - course format featuring single activity
      //              included from view.php

    $module = $course->format;
    require_once($CFG->dirroot.'/mod/'.$module.'/locallib.php');

    $strgroups  = get_string('groups');
    $strgroupmy = get_string('groupmy');
    $editing    = $PAGE->user_is_editing();

    $moduleformat = $module.'_course_format_display';
    if (function_exists($moduleformat)) {
        $moduleformat($USER,$course);
    } else {
        echo $OUTPUT->notification('The module '. $module. ' does not support single activity course format');
    }
