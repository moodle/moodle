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

function SCORM_Call (call,param) {
    if (arguments.length < 2) {
    	alert ("Invalid SCORM_Call function call: too few arguments.\nYou need pass at least 2 parameters");
    }
    var myRequest = NewHttpReq();
    result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php?id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>&call="+call+param);
    //alert('Call: '+call+'\nParam: '+param+'\nResult: '+result);
    results = result.split('\n');
    
    errorCode = results[1];
    return results[0];
}

//
// SCORM 1.2 API Implementation
//
function SCORMapi1_2() {
    var Initialized = false;
    <?php include_once ('datamodels/scorm1_2.js'); ?>

    function LMSInitialize (param) {
        if (param == "") {
            if (!Initialized) {
        	Initialized = true;
        	errorCode = "0";
		return "true";
            } else {
        	errorCode = "101";
	    }
        } else {
            errorCode = "201";
        }
        return "false";
    }
    
    function LMSFinish (param) {
	if (param == "") {
            if (Initialized) {
		LMSCommit("");
        	Initialized = false;
        	errorCode = "0";
        	return "true";
            } else {
        	errorCode = "301";
            }
        } else {
            errorCode = "201";
	}
    }
    
    function LMSGetValue (element) {
	if (Initialized) {
	    if (element !="") {
		expression = new RegExp(CMIIndex,'g');
		element = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+element+'"]')) != "undefined") {
	            if (eval('datamodel["'+element+'"].mod') != 'w') {
			errorCode = "0";
			return eval(element);
	            } else {
			errorCode = eval('datamodel["'+element+'"].readerror');
		    }
	        } else {
		    errorCode = "401"
		}
	    } else {
		errorCode = "201";
	    }
	} else {
            errorCode = "301";
        }
	return "";
    }
    
    function LMSSetValue (element,value) {
	if (Initialized) {
	    if (element != "") {
		expression = new RegExp(CMIIndex,'g');
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
			expression.compile(eval('datamodel["'+elementmodel+'"].format'));
			value = value+'';
			matches = value.match(expression);
			if (matches != null) {
			    //Create dynamic data model element
			    if (element != elementmodel) {
				elementIndexes = element.split('.');
				subelement = 'cmi';
				for (i=1;i<elementIndexes.length-1;i++) {
				    elementIndex = elementIndexes[i];
				    if (elementIndexes[i+1].match(/^\d+$/)) {
					//alert(eval(subelement+'.'+elementIndex+'._count')+1.0); 
					//if (elementIndexes[i+1] > eval(subelement+'.'+elementIndex+'._count')) {
					//    if (elementIndexes[i+1] == eval(subelement+'.'+elementIndex+'._count')) {
					//	eval(subelement+'.'+elementIndex+'._count')+1.0;
					//    }
				            subelement = subelement.concat('.'+elementIndex+'_'+elementIndexes[i+1]);
					    i++;
					//} 
				    } else {
					subelement = subelement.concat('.'+elementIndex);
				    }
				    
				    if ((typeof eval(subelement)) == "undefined") {
					eval(subelement+' = new Object();');
					/*if (elementIndexes[i].match(/^\d+$/)) {
					    alert(subelement.substring(0,subelement.length-elementIndexes[i]-1));
					    if ((typeof eval(subelement.substring(0,subelement.length-elementIndexes[i]-1))) == "undefined") {
						// create new count
					    }
					} */
				    }
				}
				element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);
				//alert('LMSSetValue: '+element+'\nModel: '+elementmodel+'\nValue: '+value+'\nMatches: '+matches);
			    }
			    //Store data
			    if ((typeof eval('datamodel["'+elementmodel+'"].range')) != "undefined") {
				range = eval('datamodel["'+elementmodel+'"].range');
				ranges = range.split('#');
				value = value+0.0;
				if ((value >= ranges[0]) && (value <= ranges[1])) {
 				    eval(element+'="'+value+'";');
				    errorCode = "0";
	    			    return "true";
				} else {
		 		    errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
				}
			    } else {
				eval(element+'="'+value+'";');
				errorCode = "0";
	    			return "true";
			    }
			} else {
			    errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
			}
	            } else {
			errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
		    }
	        } else {
		    errorCode = "401"
		}
	    } else {
		errorCode = "201";
	    }
	} else {
            errorCode = "301";
        }
	alert('LMSSetValue: '+element+'\nValue: '+value+'\nPattern: '+expression+'\nMatches: '+matches+'\nError Code: '+errorCode);
	return "false";
    }
    
    function LMSCommit (param) {
	if (param == "") {
            if (Initialized) {
        	errorCode = "0";
        	return "true";
            } else {
        	errorCode = "301";
	    }
        } else {
            errorCode = "201";
        }
        return "false";
    }
    
    function LMSGetLastError () {
	return errorCode;
    }
    
    function LMSGetErrorString (param) {
	if (param != "") {
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
	} else {
	   return "";
	}
    }
    
    function LMSGetDiagnostic (param) {
	if (param == "") {
	    param = errorCode;
	}
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
