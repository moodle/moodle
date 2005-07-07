<?php
    if (isset($userdata->status)) {
	if ($userdata->status == '') {
	    $userdata->entry = 'ab-initio';
	} else {
	    if (isset($userdata->{'cmi.core.exit'}) && ($userdata->{'cmi.core.exit'} == 'suspend')) {
		$userdata->entry = 'resume';
	    } else {
		$userdata->entry = '';		
	    }
	}
    }
?>
//
// SCORM 1.3 API Implementation
//
function SCORMapi1_3() {
    // Standard Data Type Definition
    CMIString255 = '^.{0,255}$';
    CMIString4096 = '^[.|\\n|\\r]{0,4096}$';
    CMITime = '^([0-9]{2}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMITimespan = '^([0-9]{2,4}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMIInteger = '^\\d+$';
    CMISInteger = '^-?([0-9]+)$';
    CMIDecimal = '^([0-9]{0,3})(\.[0-9]{1,2})?$';
    CMIIdentifier = '^\\w{0,255}$';
    CMIFeedback = CMIString255; // This must be redefined
    CMIIndex = '[._](\\d+).';
    // Vocabulary Data Type Definition
    CMIStatus = '^passed|completed|failed|incomplete|browsed|not attempted$';
    CMIExit = '^time-out|suspend|logout|$';
    CMIType = '^true-false|choice|fill-in|matching|performance|sequencing|likert|numeric$';
    CMIResult = '^correct|wrong|unanticipated|neutral|[0-9]?(\.[0-9]{1,2})?$';
    NAVEvent = '^previous|continue$';
    // Children lists
    cmi_children = 'core, suspend_data, launch_data, comments, objectives, student_data, student_preference, interactions';
    core_children = 'student_id, student_name, lesson_location, credit, lesson_status, entry, score, total_time, lesson_mode, exit, session_time';
    comments_children = 'comment, location, date_time';
    score_children = 'raw, min, max';
    objectives_children = 'id, score, status';
    student_data_children = 'mastery_score, max_time_allowed, time_limit_action';
    student_preference_children = 'audio, language, speed, text';
    interactions_children = 'id, objectives, time, type, correct_responses, weighting, student_response, result, latency';
    // Data ranges
    score_range = '0#100';
    audio_range = '-1#100';
    speed_range = '-100#100';
    text_range = '-1#1';
    // The SCORM 1.3 data model
    var datamodel =  {
	'cmi._children':{'defaultvalue':cmi_children, 'mod':'r', 'writeerror':'402'},
	'cmi._version':{'defaultvalue':'1.0', 'mod':'r', 'writeerror':'404'},
	'cmi.comments_from_learner._children':{'defaultvalue':comments_children, 'mod':'r', 'writeerror':'404'},
	'cmi.comments_from_learner._count':{'mod':'r', 'defaultvalue':'0', 'writeerror':'404'},
	'cmi.comments_from_learner.n.comment':{'format':CMIString4000, 'mod':'rw', 'readerror':'403', 'writeerror':'406'},
	'cmi.comments_from_learner.n.location':{'format':CMIString200, 'mod':'rw', 'readerror':'403'},
	'cmi.comments_from_learner.n.date_time':{'format':CMITime, 'mod':'rw', 'readerror':'403', 'writeerror':'406'},
	'cmi.comments_from_lms._children':{'defaultvalue':comments_children, 'mod':'r', 'writeerror':'404'},
	'cmi.comments_from_lms._count':{'mod':'r', 'defaultvalue':'0', 'writeerror':'404'},
	'cmi.comments_from_lms.n.comment':{'format':CMIString4000, 'mod':'r', 'writeerror':'404'},
	'cmi.comments_from_lms.n.location':{'format':CMIString200, 'mod':'r', 'writeerror':'404'},
	'cmi.comments_from_lms.n.date_time':{'format':CMITime, 'mod':'r', 'writeerror':'404'},
	'cmi.completition_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.completition_status'})?$userdata->{'cmi.completition_status'}:'unknown' ?>', 'format':CMIStatus, 'mod':'rw', 'writeerror':'406'},
	'cmi.core.student_id':{'defaultvalue':'<?php echo $userdata->student_id ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.student_name':{'defaultvalue':'<?php echo $userdata->student_name ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_location':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.lesson_location'})?$userdata->{'cmi.core.lesson_location'}:'' ?>', 'format':CMIString255, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.credit':{'defaultvalue':'<?php echo $userdata->credit ?>', 'mod':'r', 'writeerror':'403'},
	//'cmi.core.credit':{'defaultvalue':'credit', 'mod':'r', 'writeerror':'403'},  
	'cmi.core.entry':{'defaultvalue':'<?php echo $userdata->entry ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.score._children':{'defaultvalue':score_children, 'mod':'r', 'writeerror':'402'},
	'cmi.core.score.raw':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.score.raw'})?$userdata->{'cmi.core.score.raw'}:'' ?>', 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.score.max':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.score.max'})?$userdata->{'cmi.core.score.max'}:'' ?>', 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.score.min':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.score.min'})?$userdata->{'cmi.core.score.min'}:'' ?>', 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.total_time':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.total_time'})?$userdata->{'cmi.core.total_time'}:'00:00:00' ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_mode':{'defaultvalue':'<?php echo $userdata->mode ?>', 'mod':'r', 'writeerror':'405'},
	'cmi.core.exit':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.exit'})?$userdata->{'cmi.core.exit'}:'' ?>', 'format':CMIExit, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.core.session_time':{'format':CMITimespan, 'mod':'w', 'defaultvalue':'00:00:00', 'readerror':'404', 'writeerror':'405'},
	'cmi.suspend_data':{'defaultvalue':'<?php echo isset($userdata->{'cmi.suspend_data'})?$userdata->{'cmi.suspend_data'}:'' ?>', 'format':CMIString4096, 'mod':'rw', 'writeerror':'405'},
	'cmi.launch_data':{'defaultvalue':'<?php echo $userdata->datafromlms ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.comments':{'defaultvalue':'<?php echo isset($userdata->{'cmi.comments'})?$userdata->{'cmi.comments'}:'' ?>', 'format':CMIString4096, 'mod':'rw', 'writeerror':'405'},
	'cmi.comments_from_lms':{'mod':'r', 'writeerror':'403'},
	'cmi.objectives._children':{'defaultvalue':objectives_children, 'mod':'r', 'writeerror':'403'},
	'cmi.objectives._count':{'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
	'cmi.objectives.n.id':{'pattern':CMIIndex, 'format':CMIIdentifier, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.score._children':{'pattern':CMIIndex, 'mod':'r', 'writeerror':'402'},
	'cmi.objectives.n.score.raw':{'defaultvalue':'', 'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.score.min':{'defaultvalue':'', 'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.score.max':{'defaultvalue':'', 'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.status':{'pattern':CMIIndex, 'format':CMIStatus, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_data._children':{'defaultvalue':student_data_children, 'mod':'r', 'writeerror':'403'},
	'cmi.student_data.mastery_score':{'defaultvalue':'<?php echo $userdata->masteryscore ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.student_data.max_time_allowed':{'defaultvalue':'<?php echo $userdata->maxtimeallowed ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.student_data.time_limit_action':{'defaultvalue':'<?php echo $userdata->timelimitaction ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.student_preference._children':{'defaultvalue':student_preference_children, 'mod':'r', 'writeerror':'403'},
	'cmi.student_preference.audio':{'defaultvalue':'0', 'format':CMISInteger, 'range':audio_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.language':{'defaultvalue':'', 'format':CMIString255, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.speed':{'defaultvalue':'0', 'format':CMISInteger, 'range':speed_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.text':{'defaultvalue':'0', 'format':CMISInteger, 'range':text_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.interactions._children':{'defaultvalue':interactions_children, 'mod':'r', 'writeerror':'403'},
	'cmi.interactions._count':{'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
	'cmi.interactions.n.id':{'pattern':CMIIndex, 'format':CMIIdentifier, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.objectives._count':{'pattern':CMIIndex, 'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
	'cmi.interactions.n.objectives.n.id':{'pattern':CMIIndex, 'format':CMIIdentifier, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.time':{'pattern':CMIIndex, 'format':CMITime, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.type':{'pattern':CMIIndex, 'format':CMIType, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.correct_responses._count':{'pattern':CMIIndex, 'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
	'cmi.interactions.n.correct_responses.n.pattern':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.weighting':{'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.student_response':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.result':{'pattern':CMIIndex, 'format':CMIResult, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'nav.event':{'defaultvalue':'', 'format':NAVEvent, 'mod':'w', 'readerror':'404', 'writeerror':'405'}
    };
    //
    // Datamodel inizialization
    //
    var cmi = new Object();
	cmi.core = new Object();
	cmi.core.score = new Object();
	cmi.objectives = new Object();
	cmi.student_data = new Object();
	cmi.student_preference = new Object();
	cmi.interactions = new Object();

    // Navigation Object
    var nav = new Object();

    for (element in datamodel) {
	if (element.match(/\.n\./) == null) {
	    //alert (element+' = '+eval('datamodel["'+element+'"].defaultvalue'));
	    if ((typeof eval('datamodel["'+element+'"].defaultvalue')) != 'undefined') {
	        eval(element+' = datamodel["'+element+'"].defaultvalue;');
	    } else {
	        eval(element+' = "";');
	    }
	    //alert (element+' = '+eval(element));
	}
    }

<?php
    $count = 0;
    $objectives = '';
    foreach($userdata as $element => $value){
	if (substr($element,0,14) == 'cmi.objectives') {
	    preg_match('/.(\d+)./',$element,$matches);
	    $element = preg_replace('/.(\d+)./',"_\$1.",$element);
	    if ($matches[1] == $count) {
		$count++;
		$end = strpos($element,$matches[1])+strlen($matches[1]);
		$subelement = substr($element,0,$end);
		echo '    '.$subelement." = new Object();\n";
		echo '    '.$subelement.".score = new Object();\n";
	 	echo '    '.$subelement.".score._children = score_children;\n";
		echo '    '.$subelement.".score.raw = '';\n";
		echo '    '.$subelement.".score.min = '';\n";
		echo '    '.$subelement.".score.max = '';\n";
	    }
	    echo '    '.$element.' = \''.$value."';\n";
	}
    }
    if ($count > 0) {
	echo '    cmi.objectives._count = '.$count.";\n";
    }
?>

    if (cmi.core.lesson_status == '') {
	cmi.core.lesson_status = 'not attempted';
    } 
    
    //
    // API Methods definition
    //
    var Initialized = false;

    function Initialize (param) {
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
    
    function Terminate (param) {
        if (param == "") {
            if (Initialized) {
        	Initialized = false;
		result = StoreData(cmi,true);
		if (nav.event != '') {
		    if (nav.event == 'continue') {
		        setTimeout('top.nextSCO();',500);
		    } else {
		        setTimeout('top.prevSCO();',500);
		    }
		} else {
		    if (<?php echo $scorm->auto ?> == 1) {
			setTimeout('top.nextSCO();',500);
		    }
		}    
        	return result;
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
    
    function GetValue (element) {
	if (Initialized) {
	    if (element !="") {
		expression = new RegExp(CMIIndex,'g');
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval('datamodel["'+elementmodel+'"].mod') != 'w') {
	            	element = element.replace(expression, "_$1.");
	            	elementIndexes = element.split('.');
			subelement = 'cmi';
			i = 1;
			while ((i < elementIndexes.length) && (typeof eval(subelement) != "undefined")) {
			    subelement += '.'+elementIndexes[i++];
			}
	            	if (subelement == element) {
			    errorCode = "0";
			    return eval(element);
			} else {
			    errorCode = "401"; // Need to check if it is the right error code
			}
	            } else {
			errorCode = eval('datamodel["'+elementmodel+'"].readerror');
		    }
	        } else {
		    errorCode = "401"
		}
	    } else {
		errorCode = "201";
	    }
	} else {
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
    
    function SetValue (element,value) {
	if (Initialized) {
	    if (element != "") {
		expression = new RegExp(CMIIndex,'g');
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
			expression = new RegExp(eval('datamodel["'+elementmodel+'"].format'));
			value = value+'';
			matches = value.match(expression);
			if (matches != null) {
			    //Create dynamic data model element
			    if (element != elementmodel) {
				elementIndexes = element.split('.');
				subelement = 'cmi';
				for (i=1;i < elementIndexes.length-1;i++) {
				    elementIndex = elementIndexes[i];
				    if (elementIndexes[i+1].match(/^\d+$/)) {
				    	if ((typeof eval(subelement+'.'+elementIndex)) == "undefined") {
				    	    eval(subelement+'.'+elementIndex+' = new Object();');
					    eval(subelement+'.'+elementIndex+'._count = 0;');
					}
					if (elementIndexes[i+1] == eval(subelement+'.'+elementIndex+'._count')) {
					    eval(subelement+'.'+elementIndex+'._count++;');
					} 
					if (elementIndexes[i+1] > eval(subelement+'.'+elementIndex+'._count')) {
					    errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
					}
				    	subelement = subelement.concat('.'+elementIndex+'_'+elementIndexes[i+1]);
					i++;
				    } else {
					subelement = subelement.concat('.'+elementIndex);
				    }
				    if ((typeof eval(subelement)) == "undefined") {
					eval(subelement+' = new Object();');
					if (subelement.substr(0,14) == 'cmi.objectives') {
					    eval(subelement+'.score = new Object();');
					    eval(subelement+'.score._children = score_children;');
					    eval(subelement+'.score.raw = "";');
					    eval(subelement+'.score.min = "";');
					    eval(subelement+'.score.max = "";');
					}
				    }
				}
				element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);
			    }
			    //Store data
			    if (errorCode == "0") {
			    	if ((typeof eval('datamodel["'+elementmodel+'"].range')) != "undefined") {
				    range = eval('datamodel["'+elementmodel+'"].range');
				    ranges = range.split('#');
				    value = value*1.0;
				    if ((value >= ranges[0]) && (value <= ranges[1])) {
 				    	eval(element+'="'+value+'";');
				    	errorCode = "0";
	    			    	return "true";
				    } else {
		 			errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
				    }
			    	} else {
				    if (element == 'cmi.comments') {
				    	eval(element+'+="'+value+'";');
				    } else {
				    	eval(element+'="'+value+'";');
				    }
				    errorCode = "0";
	    			    return "true";
				}
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
    
    function Commit (param) {
        if (param == "") {
            if (Initialized) {
		return StoreData(cmi,false);
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
    
    function GetLastError () {
        return errorCode;
    }
    
    function GetErrorString (param) {
	if (param != "") {
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
	} else {
	   return "";
	}
    }
    
    function GetDiagnostic (param) {
	if (param == "") {
	    param = errorCode;
	}
	return param;
        return SCORM_Call('GetDiagnostic',param);
    }

    function TotalTime() {
        total_time = AddTime(cmi.core.total_time, cmi.core.session_time);
	return '&'+underscore('cmi.core.total_time')+'='+escape(total_time);
    }

    function CollectData(data,parent) {
	var datastring = '';
	for (property in data) {
	    if (typeof data[property] == 'object') {
		datastring += CollectData(data[property],parent+'.'+property);
	    } else {
		element = parent+'.'+property;
		expression = new RegExp(CMIIndex,'g');
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
			if (eval('datamodel["'+elementmodel+'"].defaultvalue') != data[property]) {
			    datastring += '&'+underscore(element)+'='+escape(data[property]);
			}
		    }
		}
	    }
	}
	return datastring;
    }

    function StoreData(data,storetotaltime) {
	if (storetotaltime) {
	    if (cmi.core.lesson_mode == 'normal') {
		if (cmi.core.credit == 'credit') {
		    cmi.core.lesson_status = 'completed';
		    if (cmi.student_data.mastery_score != '') {
			if (cmi.core.score.raw >= cmi.student_data.mastery_score) {
			    cmi.core.lesson_status = 'passed';
			} else {
			    cmi.core.lesson_status = 'failed';
			}
		    }
		}
	    }
	    if (cmi.core.lesson_mode == 'browse') {
		if (datamodel['cmi.core.lesson_status'].defaultvalue == '') {
		    cmi.core.lesson_status = 'browsed';
		}
	    }
	    datastring = CollectData(data,'cmi');
	    datastring += TotalTime();
	} else {
	    datastring = CollectData(data,'cmi');
	}
	datastring += '&scoid=<?php echo $sco->id ?>';
    //popupwin(datastring);
	var myRequest = NewHttpReq();
	result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>"+datastring);
	results = result.split('\n');
	errorCode = results[1];
	return results[0]; 
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

var API_1484_11 = new SCORMapi1_3();
