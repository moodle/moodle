<?php

    require_once("../../config.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($a);     // scorm ID

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
?>

function SCOFinish(){
    /*if (typeof API != "undefined") {
	API.SaveTotalTime();
    } */
}

function closeMain() {
    if (document.all) {
    	document.cookie = "SCORMpopup=" + escape(",top="+top.main.screenTop+",left="+top.main.screenLeft);
    } else {
    	document.cookie = "SCORMpopup=" + escape(",top="+top.main.screenY+",left="+top.main.screenX);
    }
    top.main.close();
}

// 
// SCORM API Implementation Call
//
var errorCode = "0";

function SCORM_Call (call,param,value) {
    if (arguments.length < 2) {
    	alert ("Invalid SCORM_Call function call: too few arguments.\nYou need pass at least 2 parameters");
    } else if (arguments.length == 3) {
    	param = param.concat("&value=",value);
    }
    var myRequest = NewHttpReq();
    result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php?id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>&call="+call+"&param="+param);
    results = result.split('\n');
    
    errorCode = results[1];
    return results[0];
}

//
// SCORM 1.2 API Implementation
//
function SCORMapi1_2() {

    function LMSInitialize (param) {
	return SCORM_Call('LMSInitialize',param);
    }
    
    function LMSFinish (param) {
	return SCORM_Call('LMSGetValue',param);
    }
    
    function LMSGetValue (element) {
	return SCORM_Call('LMSGetValue',element);
    }
    
    function LMSSetValue (element,value) {
	return SCORM_Call('LMSGetValue',element,value);
    }
    
    function LMSCommit (param) {
	return SCORM_Call('LMSGetValue',param);
    }
    
    function LMSGetLastError () {
	return errorCode;
    }
    
    function LMSGetErrorString (param) {
	var errorString = new Array();
	errorString["0"] = "No error";
	errorString["101"] = "General exception";
	errorString["201"] = "Invalid argument error";
	errorString["202"] = "Element cannot have children";
	errorString["203"] = "Element not an array - cannot have count";
	errorString["301"] = "Not initialized";
	errorString["401"] = "Not implemented error";
	errorString["402"] = "Invalid set value, element is a keyword";
	errorString["403"] = "Element is read only";
	errorString["404"] = "Element is write only";
	errorString["405"] = "Incorrect data type";
	return errorString[param];
    }
    
    function LMSGetDiagnostic (param) {
	return param;
    }
    
    this.LMSInitialize = LMSInitialize;
    this.LMSFinish = LMSFinish;
    this.LMSGetValue = LMSGetValue;
    this.LMSSetValue = LMSSetValue;
    this.LMSCommit = LMSCommit;
    this.LMSGetLastError = LMSGetLastError;
    this.LMSGetErrorString = LMSGetErrorString;
    this.LMSGetDiagnostic = LMSGetDiagnostic;
}

var API = new SCORMapi1_2();

//
// SCORM 2004 API Implementation
//
function SCORMapi2004() {

    function Initialize (param) {
	return SCORM_Call('Initialize',param);
    }
    
    function Terminate (param) {
	return SCORM_Call('Terminate',param);
    }
    
    function GetValue (element) {
	return SCORM_Call('GetValue',element);
    }
    
    function SetValue (element, value) {
	return SCORM_Call('SetValue',element,value);
    }
    
    function Commit (param) {
	return SCORM_Call('Commit',param);
    }
    
    function GetLastError () {
	return errorCode;
    }
    
    function GetErrorString (param) {
	var errorString = new Array();
	errorString["0"] = "No error";
	errorString["101"] = "General exception";
	errorString["102"] = "General Inizialization Failure";
	errorString["103"] = "Already Initialized";
	errorString["104"] = "Content Instance Terminated";
	errorString["111"] = "General Termination Failure";
	errorString["112"] = "Termination Before Inizialization";
	errorString["113"] = "Termination After Termination";
	errorString["122"] = "Retrieve Data Before Initialization";
	errorString["123"] = "Retrieve Data After Termination";
	errorString["132"] = "Store Data Before Inizialization";
	errorString["133"] = "Store Data After Termination";
	errorString["142"] = "Commit Before Inizialization";
	errorString["143"] = "Commit After Termination";
	errorString["201"] = "General Argument Error";
	errorString["301"] = "General Get Failure";
	errorString["351"] = "General Set Failure";
	errorString["391"] = "General Commit Failure";
	errorString["401"] = "Undefinited Data Model";
	errorString["402"] = "Unimplemented Data Model Element";
	errorString["403"] = "Data Model Element Value Not Initialized";
	errorString["404"] = "Data Model Element Is Read Only";
	errorString["405"] = "Data Model Element Is Write Only";
	errorString["406"] = "Data Model Element Type Mismatch";
	errorString["407"] = "Data Model Element Value Out Of Range";
	errorString["408"] = "Data Model Dependency Not Established";
	return errorString[param];
    }
    
    function GetDiagnostic (param) {
	return SCORM_Call('GetDiagnostic',param);
    }
    
    this.Initialize = Initialize;
    this.Terminate = Terminate;
    this.GetValue = GetValue;
    this.SetValue = SetValue;
    this.Commit = Commit;
    this.GetLastError = GetLastError;
    this.GetErrorString = GetErrorString;
    this.GetDiagnostic = GetDiagnostic;
}

var API_1484_11 = new SCORMapi2004();