<?php

    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $organization = optional_param('organization', '', PARAM_INT); // organization ID

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record("scorm", array("id"=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if (!empty($a)) {
        if (! $scorm = $DB->get_record("scorm", array("id"=>$a))) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record("course", array("id"=>$scorm->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('missingparameter');
    }

    $url = new moodle_url('/mod/scorm/view.php', array('id'=>$cm->id));
    if ($organization !== '') {
        $url->param('organization', $organization);
    }
    $PAGE->set_url($url);

    require_login($course->id, false, $cm);

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    if (isset($SESSION->scorm_scoid)) {
        unset($SESSION->scorm_scoid);
    }

    $strscorms = get_string("modulenameplural", "scorm");
    $strscorm  = get_string("modulename", "scorm");

    $pagetitle = strip_tags($course->shortname.': '.format_string($scorm->name));

    add_to_log($course->id, 'scorm', 'pre-view', 'view.php?id='.$cm->id, "$scorm->id", $cm->id);

    if ((has_capability('mod/scorm:skipview', get_context_instance(CONTEXT_MODULE,$cm->id))) && scorm_simple_play($scorm,$USER)) {
        exit;
    }

    //
    // Print the page header
    //
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();

    $currenttab = 'info';
    require($CFG->dirroot . '/mod/scorm/tabs.php');

    // Print the main part of the page
    echo $OUTPUT->heading(format_string($scorm->name));
    $attemptstatus = '';
    if ($scorm->displayattemptstatus == 1) {
        $attemptstatus = scorm_get_attempt_status($USER,$scorm);
    }
    echo $OUTPUT->box(format_module_intro('scorm', $scorm, $cm->id).$attemptstatus, 'generalbox boxaligncenter boxwidthwide', 'intro');

    $scormopen = true;
    $timenow = time();
    if ($scorm->timeclose !=0) {
        if ($scorm->timeopen > $timenow) {
            echo $OUTPUT->box(get_string("notopenyet", "scorm", userdate($scorm->timeopen)), "generalbox boxaligncenter");
            $scormopen = false;
        } else if ($timenow > $scorm->timeclose) {
            echo $OUTPUT->box(get_string("expired", "scorm", userdate($scorm->timeclose)), "generalbox boxaligncenter");
            $scormopen = false;
        }
    }
    if ($scormopen) {
        scorm_view_display($USER, $scorm, 'view.php?id='.$cm->id, $cm);
    }
    echo $OUTPUT->footer();

