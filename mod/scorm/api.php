<?php

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', '', PARAM_INT);  // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode

    if (!empty($id)) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_login($course->id, false, $cm);
    
    if ($usertrack=scorm_get_tracks($scoid,$USER->id)) {
        $userdata = $usertrack;
    } else {
        $userdata->status = '';
        $userdata->scorre_raw = '';
    }
    $userdata->student_id = $USER->username;
    $userdata->student_name = $USER->lastname .', '. $USER->firstname;
    $userdata->mode = 'normal';
    if (isset($mode)) {
        $userdata->mode = $mode;
    }
    if ($userdata->mode == 'normal') {
        $userdata->credit = 'credit';
    } else {
        $userdata->credit = 'no-credit';
    }    
    if ($sco = get_record('scorm_scoes','id',$scoid)) {
        $userdata->datafromlms = $sco->datafromlms;
        $userdata->masteryscore = $sco->masteryscore;
        $userdata->maxtimeallowed = $sco->maxtimeallowed;
        $userdata->timelimitaction = $sco->timelimitaction;
    } else {
        error('Sco not found');
    }

    switch ($scorm->version) {
        case 'SCORM_1.2':
            include_once ('datamodels/scorm1_2.js.php');
        break;
        case 'SCORM_1.3':
            include_once ('datamodels/scorm1_3.js.php');
        break;
        case 'AICC':
            include_once ('datamodels/aicc.js.php');
        break;
        default:
            include_once ('datamodels/scorm1_2.js.php');
        break;
    }
?>

var errorCode = "0";

function underscore(str) {
    return str.replace(/\./g,"__");
}
