<?php

    require_once("../../config.php");

    $id   = required_param('id', PARAM_INT);          // Course module ID

    $PAGE->set_url(new moodle_url($CFG->wwwroot.'/mod/lesson/grade.php', array('id'=>$id)));

    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $lesson = $DB->get_record("lesson", array("id" => $cm->instance))) {
        print_error('invalidlessonid', 'lesson');
    }

    if (! $course = $DB->get_record("course", array("id" => $lesson->course))) {
        print_error('coursemisconf');
    }

    require_login($course->id, false, $cm);

    if (has_capability('mod/lesson:edit', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        redirect('report.php?id='.$cm->id);
    } else {
        redirect('view.php?id='.$cm->id);
    }


