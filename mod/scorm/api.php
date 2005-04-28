<?php

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID
    //require_variable($scoid);  // sco ID
    optional_variable($mode);  // navigation mode

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
    $scoid = $SESSION->scorm_scoid;
    
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
    if ($sco = get_record('scorm_scoes','id',$scoid)) {
	$userdata->datafromlms = $sco->datafromlms;
	$userdata->masteryscore = $sco->masteryscore;
	$userdata->maxtimeallowed = $sco->maxtimeallowed;
	$userdata->timelimitaction = $sco->timelimitaction;
        if (!empty($sco->masteryscore)) {
	    $userdata->credit = 'credit';
	} else {
	    $userdata->credit = 'no-credit';
	}    
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

function CollectData(apiname,data,parent) {
    var datastring = '';
    for (property in data) {
	if (typeof data[property] == 'object') {
	    datastring += CollectData(apiname,data[property],parent+'.'+property);
	} else {
		element = parent+'.'+property;
		expression = new RegExp(CMIIndex,'g');
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval(apiname+'.datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval(apiname+'.datamodel["'+elementmodel+'"].mod') != 'r') {
			if (eval(apiname+'.datamodel["'+elementmodel+'"].defaultvalue') != data[property]) {
			    datastring += '&'+underscore(element)+'='+escape(data[property]);
			    //alert(element+'='+data[property]);
			}
		    }
		}
	}
    }
    return datastring;
}

function AddTime (first, second) {
    var sFirst = first.split(":");
    var sSecond = second.split(":");
    var change = 0;

    var secs = (Math.round((parseFloat(sFirst[2],10)+parseFloat(sSecond[2],10))*100))/100;  //Seconds
    change = Math.floor(secs / 60);
    secs = secs - (change * 60);
    if (Math.floor(secs) < 10) secs = "0" + secs.toString();

    mins = parseInt(sFirst[1],10)+parseInt(sSecond[1],10)+change;   //Minutes
    change = Math.floor(mins / 60);
    mins = mins - (change * 60);
    if (mins < 10) mins = "0" + mins.toString();

    hours = parseInt(sFirst[0],10)+parseInt(sSecond[0],10)+change;  //Hours
    if (hours < 10) hours = "0" + hours.toString();

    return hours + ":" + mins + ":" + secs;
}

function StoreData(apiname,data,storetotaltime) {
    datastring = CollectData(apiname,data,'cmi');
    if (storetotaltime) {
	datastring += eval(apiname+'.TotalTime();');
    }
    //popupwin(datastring);
    var myRequest = NewHttpReq();
    result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>"+datastring);
    results = result.split('\n');
    //alert(results);
    errorCode = results[1];
    return results[0]; 
}
