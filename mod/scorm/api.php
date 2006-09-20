<?php

    require_once("../../config.php");
    require_once('locallib.php');
    
    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);     // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode
    $attempt = required_param('attempt', PARAM_INT); // new attempt

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
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
    
    if ($usertrack=scorm_get_tracks($scoid,$USER->id,$attempt)) {
        $userdata = $usertrack;
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }
    $userdata->student_id = addslashes($USER->username);
    $userdata->student_name = addslashes($USER->lastname .', '. $USER->firstname);
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
    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php')) {
        include_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php');
    } else {
        include_once($CFG->dirroot.'/mod/scorm/datamodels/scorm_12.js.php');
    }
?>

var errorCode = "0";
function underscore(str) {
    return str.replace(/\./g,"__");
}
