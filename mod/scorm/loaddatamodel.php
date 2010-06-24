<?php

    require_once("../../config.php");
    require_once('locallib.php');

    $id = optional_param('id', '', PARAM_INT);       // Course Module ID, or
    $a = optional_param('a', '', PARAM_INT);         // scorm ID
    $scoid = required_param('scoid', PARAM_INT);     // sco ID
    $mode = optional_param('mode', '', PARAM_ALPHA); // navigation mode
    $attempt = required_param('attempt', PARAM_INT); // new attempt

    //IE 6 Bug workaround
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false && ini_get('zlib.output_compression') == 'On') {
        ini_set('zlib.output_compression', 'Off');
    }
    
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

    $url = new moodle_url('/mod/scorm/loaddatamodel.php', array('scoid'=>$scoid, 'id'=>$cm->id,'attempt'=>$attempt));
    if ($mode !== '') {
        $url->param('mode', $mode);
    }
    $PAGE->set_url($url);

    require_login($course, false, $cm);

    if ($usertrack = scorm_get_tracks($scoid,$USER->id,$attempt)) {
        if ((isset($usertrack->{'cmi.exit'}) && ($usertrack->{'cmi.exit'} != 'time-out')) || ($scorm->version != "SCORM_1.3")) {
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
        include($CFG->dirroot.'/mod/scorm/datamodels/'.$scorm->version.'.js.php');
    } else {
        include($CFG->dirroot.'/mod/scorm/datamodels/scorm_12.js.php');
    }
    // set the start time of this SCO
    scorm_insert_track($USER->id,$scorm->id,$scoid,$attempt,'x.start.time',time());
?>

var errorCode = "0";
function underscore(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}
