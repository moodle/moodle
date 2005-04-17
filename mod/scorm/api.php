<?php

    require_once("../../config.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID
    optional_variable($userid);  // user ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $scorm = get_record("scorm", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $scorm = get_record("scorm", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scorm->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scorm", $scorm->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id, false, $cm);
    
    if (empty($userid) || !isteacher($course->id)) {
    	$user = $USER;
    } else {
    	$user = get_complete_user_data('id', $userid);
    }
?>

function SCOFinish(){
    /*if (typeof API != "undefined") {
	API.SaveTotalTime();
    } */
}

// 
// SCORM Call Implementation
//
var errorCode = "0";

function SCORM_Call (call,param) {
    if (arguments.length < 2) {
    	alert ("Invalid SCORM_Call function call: too few arguments.\nYou need pass at least 2 parameters");
    }
    var myRequest = NewHttpReq();
    result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>&call="+call+param);
    //alert('Call: '+call+'\nParam: '+param+'\nResult: '+result);
    results = result.split('\n');
    
    errorCode = results[1];
    return results[0];
}

//
// SCORM 1.2 API Implementation
//
function SCORMapi1_2() {
    
    <?php include_once ('datamodels/scorm1_2.js.php'); ?>
    
}

var API = new SCORMapi1_2();

//
// SCORM 2004 API Implementation
//
function SCORMapi2004() {

    <?php include_once ('datamodels/scorm1_3.js.php'); ?>
    
}

var API_1484_11 = new SCORMapi2004();
