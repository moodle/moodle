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

    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    if (!$course->visible and !has_capability('moodle/course:viewhiddencourses', $context)) {
        print_error('coursehidden', '', $CFG->wwwroot .'/');
    }

    $PAGE->set_context($context);
    $PAGE->set_pagelayout('popup');
    $PAGE->set_url('/course/info.php', array('id' => $course->id));
    $PAGE->set_title(get_string("summaryof", "", $course->fullname));
    $PAGE->set_heading(get_string('courseinfo'));
    $PAGE->set_course($course);
    $PAGE->navbar->add(get_string('summary'));

    echo $OUTPUT->header();
    echo $OUTPUT->heading('<a href="view.php?id='.$course->id.'">'.format_string($course->fullname) . '</a><br />(' . format_string($course->shortname, true, array('context' => $context)) . ')');

    // print enrol info
    if ($texts = enrol_get_course_description_texts($course)) {
        echo $OUTPUT->box_start('generalbox icons');
        echo implode($texts);
        echo $OUTPUT->box_end();
    }

    echo $OUTPUT->box_start('generalbox info');

    $course->summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', NULL);
    echo format_text($course->summary, $course->summaryformat, array('overflowdiv'=>true), $course->id);

    if (!empty($CFG->coursecontact)) {
        $coursecontactroles = explode(',', $CFG->coursecontact);
        foreach ($coursecontactroles as $roleid) {
            $role = $DB->get_record('role', array('id'=>$roleid));
            $roleid = (int) $roleid;
            if ($users = get_role_users($roleid, $context, true)) {
                foreach ($users as $teacher) {
                    $fullname = fullname($teacher, has_capability('moodle/site:viewfullnames', $context));
                    $namesarray[] = format_string(role_get_name($role, $context)).': <a href="'.$CFG->wwwroot.'/user/view.php?id='.
                                    $teacher->id.'&amp;course='.SITEID.'">'.$fullname.'</a>';
                }
            }
        }

        if (!empty($namesarray)) {
            echo "<ul class=\"teachers\">\n<li>";
            echo implode('</li><li>', $namesarray);
            echo "</li></ul>";
        }
    }

// TODO: print some enrol icons

    echo $OUTPUT->box_end();

    echo "<br />";

    echo $OUTPUT->footer();


