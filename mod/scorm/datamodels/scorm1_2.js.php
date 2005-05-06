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
// SCORM 1.2 API Implementation
//
function SCORMapi1_2() {
    // Standard Data Type Definition
    CMIString255 = '^.{0,255}$';
    CMIString4096 = '^[.|\\n|\\r]{0,4096}$';
    CMITime = '^([0-9]{2}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMITimespan = '^([0-9]{2,4}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMIInteger = '^\\d+$';
    CMISInteger = '^-?([0-9]+)$';
    //CMIDecimal = '^([0-9]{0,3})?(\.[0-9]{1,2})?$';
    CMIDecimal = '^([0-9]{0,3})(\.[0-9]{1,2})?$';
    CMIIdentifier = '^\\w{0,255}$';
    CMIFeedback = CMIString255; // This must be redefined
    CMIIndex = '.(\\d+).';
    // Vocabulary Data Type Definition
    CMIStatus = '^passed|completed|failed|incomplete|browsed|not attempted$';
    CMIExit = '^time-out|suspend|logout|$';
    CMIType = '^true-false|choice|fill-in|matching|performance|sequencing|likert|numeric$';
    CMIResult = '^correct|wrong|unanticipated|neutral|[0-9]?(\.[0-9]{1,2})?$';
    // Children lists
    cmi_children = 'core, suspend_data, launch_data, comments, objectives, student_data, student_preference, interactions';
    core_children = 'student_id, student_name, lesson_location, credit, lesson_status, entry, score, total_time, lesson_mode, exit, session_time';
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
    // The SCORM 1.2 data model
    var datamodel =  {
	'cmi._children':{'defaultvalue':cmi_children, 'mod':'r', 'writeerror':'402'},
	'cmi._version':{'defaultvalue':'3.4', 'mod':'r', 'writeerror':'402'},
	'cmi.core._children':{'defaultvalue':core_children, 'mod':'r', 'writeerror':'402'},
	'cmi.core.student_id':{'defaultvalue':'<?php echo $userdata->student_id ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.student_name':{'defaultvalue':'<?php echo $userdata->student_name ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_location':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.lesson_location'})?$userdata->{'cmi.core.lesson_location'}:'' ?>', 'format':CMIString255, 'mod':'rw', 'writeerror':'405'},
	//'cmi.core.credit':{'defaultvalue':'<?php echo $userdata->credit ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.credit':{'defaultvalue':'credit', 'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.core.lesson_status'})?$userdata->{'cmi.core.lesson_status'}:'' ?>', 'format':CMIStatus, 'mod':'rw', 'writeerror':'405'},
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
	'cmi.objectives.n.score._children':{'pattern':CMIIndex, 'defaultvalue':score_children, 'mod':'r', 'writeerror':'402'},
	'cmi.objectives.n.score.raw':{'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.score.min':{'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.objectives.n.score.max':{'pattern':CMIIndex, 'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
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
	'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'w', 'readerror':'404', 'writeerror':'405'}
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
    
    if (cmi.core.lesson_status == '') {
	cmi.core.lesson_status = 'not attempted';
    } 
    
    //
    // API Methods definition
    //
    var Initialized = false;

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
        	Initialized = false;
        	return StoreData(cmi,true);
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
		elementmodel = element.replace(expression,'.n.');
		if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
	            if (eval('datamodel["'+elementmodel+'"].mod') != 'w') {
	            	element = element.replace(expression, "_$1.");
	            	//alert ('Element: '+element);
	            	elementIndexes = element.split('.');
			subelement = 'cmi';
			i = 1;
			while ((i < elementIndexes.length) && (typeof eval(subelement) != "undefined")) {
			    subelement += '.'+elementIndexes[i++];
			}
			//alert ('Element: '+subelement);
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
            errorCode = "301";
        }
	return "";
    }
    
    function LMSSetValue (element,value) {
	if (Initialized) {
	    if (element != "") {
	        //alert('LMSSetValue: '+element+'\nValue: '+value);
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
				//alert('Indexes: '+elementIndexes);
				subelement = 'cmi';
				for (i=1;i < elementIndexes.length-1;i++) {
				    elementIndex = elementIndexes[i];
				    //alert('Current: '+elementIndex+' Next: '+elementIndexes[i+1]);
				    if (elementIndexes[i+1].match(/^\d+$/)) {
				    	
				    	if ((typeof eval(subelement+'.'+elementIndex)) == "undefined") {
				    	    eval(subelement+'.'+elementIndex+' = new Object();');
					    eval(subelement+'.'+elementIndex+'._count = 0;');
					    //alert('Object: '+subelement+'.'+elementIndex);
					}
					//alert ('Count:'+eval(subelement+'.'+elementIndex+'._count'));
					if (elementIndexes[i+1] == eval(subelement+'.'+elementIndex+'._count')) {
					    //alert('Index:'+elementIndexes[i+1]);
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
				    //alert('Subelement: '+subelement);
				    if ((typeof eval(subelement)) == "undefined") {
					eval(subelement+' = new Object();');
					if (element.substr(0,14) == 'cmi.objectives') {
					    eval(subelement+'.score = new Object();');
					    eval(subelement+'.score._children = "raw,min,max";');
					    eval(subelement+'.score.raw = "";');
					    eval(subelement+'.score.min = "";');
					    eval(subelement+'.score.max = "";');
					}
				    }
				}
				element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);
				//alert('LMSSetValue: '+element+'\nModel: '+elementmodel+'\nValue: '+value+'\nMatches: '+matches);
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
				    	//alert('LMSSetValue: '+element+'\nModel: '+elementmodel+'\nValue: '+value);
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
				    //alert('LMSSetValue: '+element+'\nModel: '+elementmodel+'\nValue: '+value);
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
	//alert('LMSSetValue: '+element+'\nValue: '+value+'\nPattern: '+expression+'\nMatches: '+matches+'\nError Code: '+errorCode);
	return "false";
    }
    
    function LMSCommit (param) {
	if (param == "") {
            if (Initialized) {
		return StoreData(cmi,false);
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
			    //alert(element+'='+data[property]);
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
	//popupwin(datastring);
	var myRequest = NewHttpReq();
	result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>"+datastring);
	results = result.split('\n');
	//alert(results);
	errorCode = results[1];
	return results[0]; 
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
