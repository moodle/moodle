// 
// SCORM API 1.2 Implementation
//

function SCORMapi() {
    var cmi= new Object();
    var nav = new Object();

    var errorCode = "0";
    
    var Initialized = false;

    function LMSInitialize (param) {
	if (param != "") {
	    errorCode = "201";
	    return "false";
	}
	if (!Initialized) {
	    Initialized = true;
	    errorCode = "0";
	    
	    //
	    // CMI Initialization SCORM 1.2
	    //
	    cmi.core = new Object();
	    cmi.core._children = "student_id,student_name,lesson_location,credit,lesson_status,exit,entry,session_time,total_time,lesson_mode,score,suspend_data,launch_data";
	    cmi.core.student_id = "<?php echo $USER->username; ?>";
	    cmi.core.student_name = "<?php echo $USER->lastname.", ".$USER->firstname; ?>";
	    cmi.core.lesson_location = "<?php echo $sco_user->cmi_core_lesson_location; ?>";
	    cmi.core.credit = "<?php if ($mode != 'normal') {
	    				 echo "no-credit";
	    			     } else {
					 echo "credit";
				     }?>";
	    cmi.core.lesson_status = "<?php echo $sco_user->cmi_core_lesson_status; ?>";
	    cmi.core.exit = "<?php echo $sco_user->cmi_core_exit ?>";
	    cmi.core.entry = "<?php if ($sco_user->cmi_core_lesson_status == 'not attempted') {
					echo 'ab-initio'; 
				    } else {
					if ($sco_user->cmi_core_lesson_status != 'completed') {
					    echo 'resume'; 
				    	} else {
					    echo '';
					}
				    }?>";
	    cmi.core.session_time = "00:00:00";
	    cmi.core.total_time = "<?php echo $sco_user->cmi_core_total_time; ?>";
	    cmi.core.lesson_mode = "<?php echo $mode; ?>";
	    cmi.core.score = new Object();
	    cmi.core.score._children = "raw,min,max";
	    cmi.core.score.raw = "<?php echo $sco_user->cmi_core_score_raw; ?>";
	    cmi.core.score.min = "";
	    cmi.core.score.max = "";
	    cmi.suspend_data = "<?php echo $sco_user->cmi_suspend_data; ?>";
	    cmi.launch_data = "<?php echo $sco->datafromlms; ?>";
	    cmi.comments = "";
	    cmi.comments_from_lms = "";
	    //
	    // end CMI Initialization
	    //
	    
	    // Navigation Object
	    <?php 
	        if ($scorm->auto) {
	    	    echo 'nav.event = "continue";'."\n";
	    	} else {
	            echo 'nav.event = "";'."\n";
	        }
	    ?>

	    return "true";
	} else {
	    errorCode = "101";
	    return "false";
	}
    }
    
    function LMSGetValue (param) {
	if (Initialized) {
	    //top.alert("GET "+param);
	    switch (param) {
		case "cmi.core._children":
		case "cmi.core.student_id":
		case "cmi.core.student_name":
		case "cmi.core.lesson_location":
		case "cmi.core.credit":
		case "cmi.core.lesson_status":
		case "cmi.core.entry":
		case "cmi.core.total_time":
		case "cmi.core.lesson_mode":
		case "cmi.core.score._children":
		case "cmi.core.score.raw":
		case "cmi.core.score.min":
		case "cmi.core.score.max":
		case "cmi.launch_data":
		case "cmi.suspend_data":
		case "cmi.comments":
		case "cmi.comments_from_lms":
		    errorCode = "0";
		    return eval(param);
		break;
		case "cmi.core.exit":
		case "cmi.core.session_time":
		    errorCode = "404";
		    return "";
		break;
		default:
		    errorCode = "401";
		    param = param.replace(/.(\d+)./g,"[$1].");
		    
		    children = param.match(/._children$/);
		    if (children != null) {
		    	objType = typeof eval(children[1]);
		    	//alert (param+" :"+objType);
		    	if (objType  != "undefined") {
		    	    
		            errorCode = "202";
		    	}
		    }
		    
		    counted = param.match(/._count$/);
		    if (counted != null) {
		    	objType = typeof eval(counted[1]);
		    	//alert (param+" :"+objType);
		    	if (objType != "undefined") {
		            errorCode = "203";
		    	}
		    }
		    //alert(param+": "+errorCode);
		    return "";  
		break;
	    }
	} else {
	    errorCode = "301";
	    return "";
	}
    }
    
    function LMSSetValue (param,value) {
	if (Initialized) {
	    //top.alert("SET "+param+" = "+value);
	    switch (param) {
		case "cmi.core.session_time":
		    if (typeof(value) == "string") {
		        var parsedtime = value.match(/^([0-9]{2,4}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$/);
		        if (parsedtime != null) {
		            //top.alert(parsedtime);
		            if (((parsedtime.length == 4) || (parsedtime.length == 5)) && (parsedtime[2]>=0) && (parsedtime[2]<=59) && (parsedtime[3]>=0) && (parsedtime[3]<=59)) {
		                eval(param+'="'+value+'";');
		        	errorCode = "0";
		        	return "true";
		            } else {
		            	errorCode = "405";
		            	return "false";
		       	    }
		       	} else {
		       	    errorCode = "405";
		            return "false";
		       	}
		    } else {
		        errorCode = "405";
		        return "false";
		    }
		break;
		case "cmi.core.lesson_status":
		    if ((value!="passed")&&(value!="completed")&&(value!="failed")&&(value!="incomplete")&&(value!="browsed")) {
			errorCode = "405";
			return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = "0";
		    return "true";
		break;
		case "cmi.core.score.raw":
		case "cmi.core.score.min":
		case "cmi.core.score.max":
		    if (value != "") {
		    	if ((parseFloat(value,10)).toString() != value) {
			    errorCode = "405";
			    return "false";
		    	} else {
		    	    rawvalue = parseFloat(value,10);
		            if ((rawvalue<0) || (rawvalue>100)) {
		           	errorCode = "405";
		           	return "false";
		            }
		    	}
		    }
		    eval(param+'="'+value+'";');
		    errorCode = "0";
		    return "true";
		break;
		case "cmi.core.exit":
		    if ((value!="time-out")&&(value!="suspend")&&(value!="logout")&&(value!="")) {
			errorCode = "405";
			return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = "0";
		    return "true";
		break;
		case "cmi.core.lesson_location":
		    if (value.length > 255) {
		        errorCode = "405";
		        return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = "0";
		    return "true";
		break;
		case "cmi.suspend_data":
		case "cmi.comments":
		    if (value.length > 4096) {
		        errorCode = "405";
		        return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = "0";
		    return "true";
		break;
		case "cmi.core._children":
		case "cmi.core.score._children":
		    errorCode = "402";
		    return "false";
		break;
		case "cmi.core.student_id":
		case "cmi.core.student_name":
		case "cmi.core.credit":
		case "cmi.core.entry":
		case "cmi.core.total_time":
		case "cmi.core.lesson_mode":
		case "cmi.launch_data":
		case "cmi.comments_from_lms":
		    errorCode = "403";
		    return "false";
		break;
		case "nav.event":
		    if ((value == "previous") || (value == "continue")) {
		        eval(param+'="'+value+'";');
		    	errorCode = "0";
		    	return "true";
		    } else {
		        erroCode = "405";
		        return "false";
		    }
		break;	
		default:
		    errorCode = "401";  //This is more correct but may have problem with some SCOes
		    //errorCode = "0"; // With this disable any possible SCO errors alert
		    return "false";
		break;
	    }
	} else {
	    errorCode = "301";
	    return "false";
	}
    }

    function LMSCommit (param) {
	if (param != "") {
	    errorCode = "201";
	    return "false";
	}
	if (Initialized) {
	    if (<?php echo $navObj ?>cmi.document.theform) {
		cmiform = <?php echo $navObj ?>cmi.document.forms[0];
		cmiform.scoid.value = "<?php echo $sco->id; ?>";
		cmiform.cmi_core_lesson_location.value = cmi.core.lesson_location;
		cmiform.cmi_core_lesson_status.value = cmi.core.lesson_status;
		cmiform.cmi_core_exit.value = cmi.core.exit;
		cmiform.cmi_core_score_raw.value = cmi.core.score.raw;
		cmiform.cmi_suspend_data.value = cmi.suspend_data;
		cmiform.submit();
	    }
	    errorCode = "0";
	    return "true";
	} else {
	    errorCode = "301";
	    return "false";
	}
    }
    
    function LMSFinish (param) {
	if (param != "") {
	    errorCode = "201";
	    return "false";
	}
	if (!Initialized) {
	    errorCode = "301";
	    return "false";
	} else {
	    Initialized = false;
	    errorCode = "0";
	    cmi.core.total_time = AddTime(cmi.core.total_time, cmi.core.session_time);
	    //top.alert(cmi.core.total_time);
	    if (<?php echo $navObj ?>cmi.document.theform) {
		cmiform = <?php echo $navObj ?>cmi.document.forms[0];
		cmiform.scoid.value = "<?php echo $sco->id; ?>";
		cmiform.cmi_core_total_time.value = cmi.core.total_time;
		cmiform.submit();
		
	    }
            if (nav.event != "") {
            <?php
		if ($sco != $last) {
	            echo "setTimeout('top.changeSco(nav.event);',500);\n";
		} else {
		    echo "exitloc = '".$CFG->wwwroot."/mod/scorm/view.php?id=".$cm->id."';\n";
		    echo "setTimeout('top.location = exitloc;',500);\n";
		} 
	    ?>
	    }
	    return "true";
	}    
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
	
    function AddTime (first, second) {
	var sFirst = first.split(":");
	var sSecond = second.split(":");
	var change = 0;
	
	var secs = (Math.round((parseFloat(sFirst[2],10)+parseFloat(sSecond[2],10))*100))/100; 	//Seconds
	if (secs > 60) {
	    secs = secs - 60;
	    change = 1;
	} else {
	    change = 0;
	}
	if (Math.floor(secs) < 10) secs = "0" + secs.toString();
	
	mins = parseInt(sFirst[1],10)+parseInt(sSecond[1],10)+change; 	//Minutes
	if (mins > 60) 
	    change = 1;
	else
	    change = 0;
	if (mins < 10) mins = "0" + mins.toString();
	    
	hours = parseInt(sFirst[0],10)+parseInt(sSecond[0],10)+change; 	//Hours
	if (hours < 10) hours = "0" + hours.toString();
	
	return hours + ":" + mins + ":" + secs;
    }
    
    this.LMSInitialize = LMSInitialize;
    this.LMSGetValue = LMSGetValue;
    this.LMSSetValue = LMSSetValue;
    this.LMSCommit = LMSCommit;
    this.LMSFinish = LMSFinish;
    this.LMSGetLastError = LMSGetLastError;
    this.LMSGetErrorString = LMSGetErrorString;
    this.LMSGetDiagnostic = LMSGetDiagnostic;
}

var API = new SCORMapi();