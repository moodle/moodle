<?php

    require_once("../../config.php");
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);     // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode
    $attempt = required_param('attempt', PARAM_INT); // new attempt

    //IE 6 Bug workaround
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false) {
        @ini_set('zlib.output_compression', 'Off');
        @apache_setenv('no-gzip', 1);
        header( 'Content-Type: application/javascript' );
    }

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scorm', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
            print_error('coursemisconf');
        }
        if (! $scorm = $DB->get_record('scorm', array('id'=>$cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else if (!empty($a)) {
        if (! $scorm = $DB->get_record('scorm', array('id'=>$a))) {
            print_error('coursemisconf');
        }
        if (! $course = $DB->get_record('course', array('id'=>$scorm->course))) {
            print_error('coursemisconf');
        }
        if (! $cm = get_coursemodule_from_instance('scorm', $scorm->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    } else {
        print_error('missingparameter');
    }

    $PAGE->set_url('/mod/scorm/api.php', array('scoid'=>$scoid, 'id'=>$cm->id));

    require_login($course->id, false, $cm);

    if ($usertrack = scorm_get_tracks($scoid,$USER->id,$attempt)) {
        //according to SCORM 2004 spec(RTE V1, 4.2.8), only cmi.exit==suspend should allow previous datamodel elements on re-launch
        if ($scorm->version != "SCORM_1.3" || (isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} == 'suspend'))) {
            foreach ($usertrack as $key => $value) {
                $userdata->$key = addslashes_js($value);
            }
        } else {
            $userdata->status = '';
            $userdata->score_raw = '';
        }
    } else {
        $userdata->status = '';
        $userdata->score_raw = '';
    }
    $userdata->student_id = addslashes_js($USER->username);
    $userdata->student_name = addslashes_js($USER->lastname .', '. $USER->firstname);
    $userdata->mode = 'normal';
    if (isset($mode)) {
        $userdata->mode = $mode;
    }
    if ($userdata->mode == 'normal') {
        $userdata->credit = 'credit';
    } else {
        $userdata->credit = 'no-credit';
    }
    if ($scodatas = scorm_get_sco($scoid, SCO_DATA)) {
        foreach ($scodatas as $key => $value) {
            $userdata->$key = addslashes_js($value);
        }
    } else {
        print_error('cannotfindsco', 'scorm');
    }
    if (!$sco = scorm_get_sco($scoid)) {
        print_error('cannotfindsco', 'scorm');
    }
    $scorm->version = strtolower(clean_param($scorm->version, PARAM_SAFEDIR));   // Just to be safe
    if (file_exists($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php')) {
        include_once($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php');
    } else {
        include_once($CFG->dirroot.'/mod/scorm/datamodels/scorm_12.js.php');
    }
    // set the start time of this SCO
    scorm_insert_track($USER->id,$scorm->id,$scoid,$attempt,'x.start.time',time());
?>


var errorCode = "0";
function underscore(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}
