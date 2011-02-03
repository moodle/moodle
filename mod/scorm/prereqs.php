<?php

    // this page is called via AJAX to repopulte the TOC when LMSFinish() is called

    require_once('../../config.php');
    require_once($CFG->dirroot.'/mod/scorm/locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);  // sco ID
    $attempt = required_param('attempt', PARAM_INT);  // attempt number
    $mode = optional_param('mode', 'normal', PARAM_ALPHA); // navigation mode
    $currentorg = optional_param('currentorg', '', PARAM_RAW); // selected organization


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

    $PAGE->set_url('/mod/scorm/prereqs.php', array('scoid'=>$scoid,'attempt'=>$attempt, 'id'=>$cm->id));

    require_login($course->id, false, $cm);

    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (!file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php')) {
        $scorm->version = 'scorm_12';
    }
    require_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'lib.php');


    if (confirm_sesskey() && (!empty($scoid))) {
        $result = true;
        $request = null;
        if (has_capability('mod/scorm:savetrack', get_context_instance(CONTEXT_MODULE,$cm->id))) {
            $result = scorm_get_toc($USER,$scorm,$cm->id,TOCJSLINK,$currentorg,$scoid,$mode,$attempt,true, false);
            echo $result->toc;
        }
    }

