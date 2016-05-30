<?php

/// Displays external information about a course

    require_once("../config.php");
    require_once("lib.php");

    $id   = optional_param('id', false, PARAM_INT); // Course id
    $name = optional_param('name', false, PARAM_RAW); // Course short name

    if (!$id and !$name) {
        print_error("unspecifycourseid");
    }

    if ($name) {
        if (!$course = $DB->get_record("course", array("shortname"=>$name))) {
            print_error("invalidshortname");
        }
    } else {
        if (!$course = $DB->get_record("course", array("id"=>$id))) {
            print_error("invalidcourseid");
        }
    }

    $site = get_site();

    if ($CFG->forcelogin) {
        require_login();
    }

    $context = context_course::instance($course->id);
    if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
        print_error('coursehidden', '', $CFG->wwwroot .'/');
    }

    $PAGE->set_course($course);
    $PAGE->set_pagelayout('incourse');
    $PAGE->set_url('/course/info.php', array('id' => $course->id));
    $PAGE->set_title(get_string("summaryof", "", $course->fullname));
    $PAGE->set_heading(get_string('courseinfo'));
    $PAGE->navbar->add(get_string('summary'));

    echo $OUTPUT->header();

    // print enrol info
    if ($texts = enrol_get_course_description_texts($course)) {
        echo $OUTPUT->box_start('generalbox icons');
        echo implode($texts);
        echo $OUTPUT->box_end();
    }

    $courserenderer = $PAGE->get_renderer('core', 'course');
    echo $courserenderer->course_info_box($course);

    echo "<br />";

    echo $OUTPUT->footer();


