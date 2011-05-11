<!--// hopefully fool ie IE proof way of getting DOM element
function safeGetElement(doc, el) {
    return doc.ids ? doc.ids[el] : doc.getElementById ? doc.getElementById(el) : doc.all[el];
}
// Find elements by class name
var aryClassElements = new Array();
function getNextElementByClassName( strClassName, obj ) {
    if ( obj.className == strClassName ) {
        aryClassElements[aryClassElements.length] = obj;
    }
    for ( var i = 0; i < obj.childNodes.length; i++ )
        getNextElementByClassName( strClassName, obj.childNodes[i] );
}

function getElementsByClassName( strClassName, obj ) {
    aryClassElements = new Array();
    getNextElementByClassName( strClassName, obj );
    if (aryClassElements.length > 0) {
        return aryClassElements[0];
    }
    else {
        return null;
    }
}

// retrieve cookie data
function getCookie (cookie_name){
    var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
    if ( results ) {
        return (unescape(results[2]));
    } else {
        return null;
    }
}

// retrieve the logging flag from a Cookie
function getLoggingActive () {
    var loggingActive = getCookie('SCORMLoggingActive');
    if (!loggingActive) {
        loggingActive = 'A';
    }
    return loggingActive;
}

// set the logging flag in a cookie
function setLoggingActive (flag) {
    new cookie("SCORMLoggingActive", flag, 365, "/").set();
}

// toggle the logging
function toggleLog () {
    if (getLoggingActive() == "A") {
        AppendToLog("Moodle Logging Deactivated", 0);
        setLoggingActive('N');
        logButton.innerHTML = '--><?php print_string('scormloggingoff','scorm') ?>';
    } else {
        setLoggingActive('A');
        AppendToLog("Moodle Logging Activated", 0);
        logButton.innerHTML = '<?php print_string('scormloggingon','scorm') ?>';
        logPopUpWindow.focus();
    }
}

// globals for the log accumulation
var logString = "";
var logRow = 0;
var logPopUpWindow = "N";
var debugSCORMVersion = '<?php echo $scorm->version; ?>';
<?php
   $LMS_prefix = ($scorm->version == 'scorm_12' || $scorm->version == 'SCORM_1.2' || empty($scorm->version)) ? 'LMS' : '';
   $LMS_api = ($scorm->version == 'scorm_12' || $scorm->version == 'SCORM_1.2' || empty($scorm->version)) ? 'API' : 'API_1484_11';

   $LMS_elements = array();
   if ($scorm->version == 'scorm_12' || $scorm->version == 'SCORM_1.2' || empty($scorm->version)) {
       $LMS_elements = array(   'cmi.core._children',
                                'cmi.core.student_id',
                                'cmi.core.student_name',
                                'cmi.core.lesson_location',
                                'cmi.core.credit',
                                'cmi.core.lesson_status',
                                'cmi.core.entry',
                                'cmi.core._children',
                                'cmi.core.score.raw',
                                'cmi.core.score.max',
                                'cmi.core.score.min',
                                'cmi.core.total_time',
                                'cmi.core.lesson_mode',
                                'cmi.core.exit',
                                'cmi.core.session_time',
                                'cmi.suspend_data',
                                'cmi.launch_data',
                                'cmi.comments',
                                'cmi.comments_from_lms',
                                'cmi.objectives._count',
                                'cmi.objectives._children',
                                'cmi.objectives.n.id',
                                'cmi.objectives.n.score._children',
                                'cmi.objectives.n.score.raw',
                                'cmi.objectives.n.score.min',
                                'cmi.objectives.n.score.max',
                                'cmi.objectives.n.status',
                                'cmi.student_data._children',
                                'cmi.student_data.mastery_score',
                                'cmi.student_data.max_time_allowed',
                                'cmi.student_data.time_limit_action',
                                'cmi.student_preference._children',
                                'cmi.student_preference.audio',
                                'cmi.student_preference.language',
                                'cmi.student_preference.speed',
                                'cmi.student_preference.text',
                                'cmi.interactions._children',
                                'cmi.interactions._count',
                                'cmi.interactions.n.id',
                                'cmi.interactions.n.objectives._count',
                                'cmi.interactions.n.objectives.m.id',
                                'cmi.interactions.n.time',
                                'cmi.interactions.n.type',
                                'cmi.interactions.n.correct_responses._count',
                                'cmi.interactions.n.correct_responses.m.pattern',
                                'cmi.interactions.n.weighting',
                                'cmi.interactions.n.student_response',
                                'cmi.interactions.n.result',
                                'cmi.interactions.n.latency');
   } else {
       $LMS_elements = array(   'cmi._children',
                                'cmi._version',
                                'cmi.learner_id',
                                'cmi.learner_name',
                                'cmi.location',
                                'cmi.completion_status',
                                'cmi.completion_threshold',
                                'cmi.scaled_passing_score',
                                'cmi.progressive_measure',
                                'cmi.score._children',
                                'cmi.score.raw',
                                'cmi.score.max',
                                'cmi.score.min',
                                'cmi.score.scaled',
                                'cmi.total_time',
                                'cmi.time_limit_action',
                                'cmi.max_time_allowed',
                                'cmi.session_time',
                                'cmi.success_status',
                                'cmi.lesson_mode',
                                'cmi.entry',
                                'cmi.exit',
                                'cmi.credit',
                                'cmi.mode',
                                'cmi.suspend_data',
                                'cmi.launch_data',
                                'cmi.comments',
                                'cmi.comments_from_lms._children',
                                'cmi.comments_from_lms._count',
                                'cmi.comments_from_lms.n.comment',
                                'cmi.comments_from_lms.n.location',
                                'cmi.comments_from_lms.n.timestamp',
                                'cmi.comments_from_learner._children',
                                'cmi.comments_from_learner._count',
                                'cmi.comments_from_learner.n.comment',
                                'cmi.comments_from_learner.n.location',
                                'cmi.comments_from_learner.n.timestamp',
                                'cmi.objectives._count',
                                'cmi.objectives._children',
                                'cmi.objectives.n.id',
                                'cmi.objectives.n.score._children',
                                'cmi.objectives.n.score.raw',
                                'cmi.objectives.n.score.min',
                                'cmi.objectives.n.score.max',
                                'cmi.objectives.n.score.scaled',
                                'cmi.objectives.n.success_status',
                                'cmi.objectives.n.completion_status',
                                'cmi.objectives.n.progress_measure',
                                'cmi.objectives.n.description',
                                'cmi.student_data._children',
                                'cmi.student_data.mastery_score',
                                'cmi.student_data.max_time_allowed',
                                'cmi.student_data.time_limit_action',
                                'cmi.student_preference._children',
                                'cmi.student_preference.audio',
                                'cmi.student_preference.language',
                                'cmi.student_preference.speed',
                                'cmi.student_preference.text',
                                'cmi.interactions._children',
                                'cmi.interactions._count',
                                'cmi.interactions.n.id',
                                'cmi.interactions.n.objectives._count',
                                'cmi.interactions.n.objectives.m.id',
                                'cmi.interactions.n.time',
                                'cmi.interactions.n.type',
                                'cmi.interactions.n.correct_responses._count',
                                'cmi.interactions.n.correct_responses.m.pattern',
                                'cmi.interactions.n.weighting',
                                'cmi.interactions.n.learner_response',
                                'cmi.interactions.n.result',
                                'cmi.interactions.n.latency',
                                'cmi.interactions.n.description',
                                'adl.nav.request');
   }
?>

// add each entry to the log, or setup the log pane first time round
// The code written into the header is based on the ADL test suite API interaction code
// and various examples of test wrappers out in the community
function UpdateLog(s) {
    var s1 = '<html><head><style>\n'
        + 'body {font-family: Arial, Helvetica, Sans-Serif;font-size: xx-small;'
        + 'margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px; '
        + 'background-color: ffffff;}\n'
        + '.even {background-color: ffffff; width: 100%;}\n'
        + '.odd {background-color: e8f2fe; width: 100%;}\n'
        + '.error {background-color: ffffff; color: red; width: 100%;}\n'
        + '<\/style>'
        + '<script>\n'
        + 'var LMSVersion = \'<?php echo $scorm->version; ?>\';\n'
        + ' \n'
        + 'function checkLMSVersion() {  \n'
        + '    if (this.document.body.childNodes.length > 0) { \n'
        + '        if (this.document.body.lastChild.id == LMSVersion) { \n'
        + '            return true; \n'
        + '        } \n'
        + '    }; \n'
        + '    alert(\'LMS Version: \' + this.document.body.lastChild.id + \n'
        + '          \' does not equal: \' + LMSVersion +  \n'
        + '          \' so API calls will fail - did navigate to another SCORM package?\'); \n'
        + '    return false; \n'
        + '} \n'
        + ' \n'
        + 'var saveElement = ""; \n'
        + 'function setAPIValue() {  \n'
        + '  document.elemForm.API_ELEMENT.value = document.elemForm.ELEMENT_LIST.value; \n'
        + '  saveElement = document.elemForm.API_ELEMENT.value; \n'
        + '} \n'
        + '  \n'
        + 'var _Debug = false;  // set this to false to turn debugging off  \n'
        + '  \n'
        + '// Define exception/error codes  \n'
        + 'var _NoError = 0;  \n'
        + 'var _GeneralException = 101;  \n'
        + 'var _ServerBusy = 102;  \n'
        + 'var _InvalidArgumentError = 201;  \n'
        + 'var _ElementCannotHaveChildren = 202;  \n'
        + 'var _ElementIsNotAnArray = 203;  \n'
        + 'var _NotInitialized = 301;  \n'
        + 'var _NotImplementedError = 401;  \n'
        + 'var _InvalidSetValue = 402;  \n'
        + 'var _ElementIsReadOnly = 403;  \n'
        + 'var _ElementIsWriteOnly = 404;  \n'
        + 'var _IncorrectDataType = 405;  \n'
        + '  \n'
        + '// local variable definitions  \n'
        + 'var apiHandle = null;  \n'
        + 'var API = null;  \n'
        + 'var findAPITries = 0;  \n'
        + '  \n'
        + '  \n'
        + 'function doLMSInitialize() { \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSInitialize was not successful.");  \n'
        + '      return "false";  \n'
        + '   }  \n'
        + '   var result = api.<?php echo $LMS_prefix; ?>Initialize("");  \n'
        + '   if (result.toString() != "true") {  \n'
        + '      var err = ErrorHandler();  \n'
        + '   }  \n'
        + '   return result.toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSFinish() {  \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSFinish was not successful.");  \n'
        + '      return "false";  \n'
        + '   } else {  \n'
        + '      // call the LMSFinish function that should be implemented by the API  \n'
        + '      var result = api.<?php echo $LMS_prefix; ?>Finish("");  \n'
        + '      if (result.toString() != "true") {  \n'
        + '         var err = ErrorHandler();  \n'
        + '      }  \n'
        + '   }  \n'
        + '   return result.toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSTerminate() {  \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nTerminate was not successful.");  \n'
        + '      return "false";  \n'
        + '   } else {  \n'
        + '      // call the Terminate function that should be implemented by the API  \n'
        + '      var result = api.Terminate("");  \n'
        + '      if (result.toString() != "true") {  \n'
        + '         var err = ErrorHandler();  \n'
        + '      }  \n'
        + '   }  \n'
        + '   return result.toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSGetValue(name) {  \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSGetValue was not successful.");  \n'
        + '      return "";  \n'
        + '   } else {  \n'
        + '      var value = api.<?php echo $LMS_prefix; ?>GetValue(name);  \n'
        + '      var errCode = api.<?php echo $LMS_prefix; ?>GetLastError().toString();  \n'
        + '      if (errCode != _NoError) {  \n'
        + '         // an error was encountered so display the error description  \n'
        + '         var errDescription = api.<?php echo $LMS_prefix; ?>GetErrorString(errCode);  \n'
        + '         alert("<?php echo $LMS_prefix; ?>GetValue("+name+") failed. \\n"+ errDescription);  \n'
        + '         return "";  \n'
        + '      } else {  \n'
        + '         return value.toString();  \n'
        + '      }  \n'
        + '   }  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSSetValue(name, value) {  \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSSetValue was not successful.");  \n'
        + '      return;  \n'
        + '   } else {  \n'
        + '      var result = api.<?php echo $LMS_prefix; ?>SetValue(name, value);  \n'
        + '      if (result.toString() != "true") {  \n'
        + '         var err = ErrorHandler();  \n'
        + '      }  \n'
        + '   }  \n'
        + '   return;  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSCommit() {  \n'
        + '   checkLMSVersion(); \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSCommit was not successful.");  \n'
        + '      return "false";  \n'
        + '   } else {  \n'
        + '      var result = api.<?php echo $LMS_prefix; ?>Commit("");  \n'
        + '      if (result != "true") {  \n'
        + '         var err = ErrorHandler();  \n'
        + '      }  \n'
        + '   }  \n'
        + '   return result.toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSGetLastError() {  \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSGetLastError was not successful.");  \n'
        + '      //since we can\'t get the error code from the LMS, return a general error  \n'
        + '      return _GeneralError;  \n'
        + '   }  \n'
        + '   return api.<?php echo $LMS_prefix; ?>GetLastError().toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSGetErrorString(errorCode) {  \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSGetErrorString was not successful.");  \n'
        + '   }  \n'
        + '   return api.<?php echo $LMS_prefix; ?>GetErrorString(errorCode).toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function doLMSGetDiagnostic(errorCode) {  \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSGetDiagnostic was not successful.");  \n'
        + '   }  \n'
        + '   return api.<?php echo $LMS_prefix; ?>GetDiagnostic(errorCode).toString();  \n'
        + '}  \n'
        + '  \n'
        + 'function LMSIsInitialized() {  \n'
        + '   // there is no direct method for determining if the LMS API is initialized  \n'
        + '   // for example an LMSIsInitialized function defined on the API so we\'ll try  \n'
        + '   // a simple LMSGetValue and trap for the LMS Not Initialized Error  \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nLMSIsInitialized() failed.");  \n'
        + '      return false;  \n'
        + '   } else {  \n'
        + '      var value = api.<?php echo $LMS_prefix; ?>GetValue("cmi.core.student_name");  \n'
        + '      var errCode = api.<?php echo $LMS_prefix; ?>GetLastError().toString();  \n'
        + '      if (errCode == _NotInitialized) {  \n'
        + '         return false;  \n'
        + '      } else {  \n'
        + '         return true;  \n'
        + '      }  \n'
        + '   }  \n'
        + '}  \n'
        + '  \n'
        + 'function ErrorHandler() {  \n'
        + '   var api = getAPIHandle();  \n'
        + '   if (api == null) {  \n'
        + '      alert("Unable to locate the LMS\'s API Implementation.\\nCannot determine LMS error code.");  \n'
        + '      return;  \n'
        + '   }  \n'
        + '   // check for errors caused by or from the LMS  \n'
        + '   var errCode = api.<?php echo $LMS_prefix; ?>GetLastError().toString();  \n'
        + '   if (errCode != _NoError) {  \n'
        + '      // an error was encountered so display the error description  \n'
        + '      var errDescription = api.<?php echo $LMS_prefix; ?>GetErrorString(errCode);  \n'
        + '      if (_Debug == true) {  \n'
        + '         errDescription += "\\n";  \n'
        + '         errDescription += api.<?php echo $LMS_prefix; ?>GetDiagnostic(null);  \n'
        + '         // by passing null to LMSGetDiagnostic, we get any available diagnostics  \n'
        + '         // on the previous error.  \n'
        + '      }  \n'
        + '      alert(errDescription);  \n'
        + '   }  \n'
        + '   return errCode;  \n'
        + '}  \n'
        + '  \n'
        + 'function getAPIHandle() {  \n'
        + '   if (apiHandle == null) {  \n'
        + '      apiHandle = getAPI();  \n'
        + '   }  \n'
        + '   return apiHandle;  \n'
        + '}  \n'
        + '  \n'
        + 'function findAPI(win) {  \n'
        + '   while ((win.<?php echo $LMS_api; ?> == null) && (win.parent != null) && (win.parent != win)) {  \n'
        + '      findAPITries++;  \n'
        + '      // Note: 7 is an arbitrary number, but should be more than sufficient  \n'
        + '      if (findAPITries > 7) {  \n'
        + '         alert("Error finding API -- too deeply nested.");  \n'
        + '         return null;  \n'
        + '      }  \n'
        + '      win = win.parent;  \n'
        + '   }  \n'
        + '   return win.<?php echo $LMS_api; ?>;  \n'
        + '}  \n'
        + '  \n'
        + 'function getAPI() {  \n'
        + '   var theAPI = findAPI(window);  \n'
        + '   if ((theAPI == null) && (window.opener != null) && (typeof(window.opener) != "undefined")) {  \n'
        + '      theAPI = findAPI(window.opener);  \n'
        + '   }  \n'
        + '   if (theAPI == null) {  \n'
        + '      alert("Unable to find an API adapter");  \n'
        + '   }  \n'
        + '   return theAPI  \n'
        + '}  \n'
        + '  \n'
        + '   function tryLMSInitialize() {  \n'
        + '      var result = doLMSInitialize();  \n'
        + '      var msg;  \n'
        + '      if(result == "true") {  \n'
        + '         msg = "<?php echo $LMS_prefix; ?>Initialize Successful!";  \n'
        + '      } else {  \n'
        + '         var err = doLMSGetLastError();  \n'
        + '         var errString = doLMSGetErrorString(err);  \n'
        + '         msg = "<?php echo $LMS_prefix; ?>Initialize Failed! Error Code: "+err;  \n'
        + '         msg += " Error Description: " + errString;  \n'
        + '      }  \n'
        + '      document.initForm.msgtxt.value= msg;  \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSCommit() {  \n'
        + '      var result = doLMSCommit();  \n'
        + '      var msg;  \n'
        + '      if(result == "true") {  \n'
        + '         msg = "<?php echo $LMS_prefix; ?>Commit was Successful!";  \n'
        + '      } else {  \n'
        + '         var err = doLMSGetLastError();  \n'
        + '         var errString = doLMSGetErrorString(err);  \n'
        + '         var msg = "<?php echo $LMS_prefix; ?>Commit Failed! Error Code: "+err;  \n'
        + '         msg += " Error Description: " + errString;  \n'
        + '      }  \n'
        + '      document.otherForm.msgtxt.value = msg; \n'
        + '      document.elemForm.API_ELEMENT.value = saveElement; \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSFinish() {  \n'
        + '      // set now, in case the SCO is unloaded on LMSFinish  \n'
        + '      doLMSSetValue("cmi.core.lesson_status", "completed");  \n'
        + '      doLMSSetValue("cmi.core.exit", "");  \n'
        + '      doLMSSetValue("cmi.core.session_time", "00:00:30");  \n'
        + '      var result = doLMSFinish();  \n'
        + '      var msg;  \n'
        + '      if(result == "true") {  \n'
        + '         msg = "LMSFinish Successful!";  \n'
        + '         document.otherForm.msgtxt.value = msg;  \n'
        + '      } else {  \n'
        + '         var err = doLMSGetLastError();  \n'
        + '         var errString = doLMSGetErrorString(err);  \n'
        + '         var msg = "LMSFinish Failed! Error Code: "+err;  \n'
        + '         msg += " Error Description: " + errString;  \n'
        + '         document.otherForm.msgtxt.value = msg;  \n'
        + '      }  \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSTerminate() {  \n'
        + '      var result = doLMSTerminate();  \n'
        + '      var msg;  \n'
        + '      if(result == "true") {  \n'
        + '         msg = "Terminate Successful!";  \n'
        + '         document.otherForm.msgtxt.value = msg;  \n'
        + '      } else {  \n'
        + '         var err = doLMSGetLastError();  \n'
        + '         var errString = doLMSGetErrorString(err);  \n'
        + '         var msg = "Terminate Failed! Error Code: "+err;  \n'
        + '         msg += " Error Description: " + errString;  \n'
        + '         document.otherForm.msgtxt.value = msg;  \n'
        + '      }  \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSGetValue() {  \n'
        + '     var value = document.elemForm.API_ELEMENT.value;  \n'
        + '     var msg;  \n'
        + '     var result = doLMSGetValue(value);  \n'
        + '     var err = doLMSGetLastError();  \n'
        + '     var errString = doLMSGetErrorString(err);  \n'
        + '     msg = "<?php echo $LMS_prefix; ?>GetValue Returned: " + result;  \n'
        + '     msg += "\\nError Code: " + err;  \n'
        + '     msg += "\\nError Description: " + errString;  \n'
        + '     document.elemForm.msgtxt.value = msg;  \n'
        + '     document.elemForm.API_ELEMENT.value = saveElement; \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSSetValue() {  \n'
        + '     // Get the element that is to be set  \n'
        + '     var setValue = document.elemForm.SET_VAL.value;  \n'
        + '     var item = document.elemForm.API_ELEMENT.value;  \n'
        + '     var msg;  \n'
        + '     var api = getAPIHandle();  \n'
        + '     if (api == null) {  \n'
        + '        alert("Unable to locate the LMS\'s API Implementation.\\n"+  \n'
        + '              "<?php echo $LMS_prefix; ?>SetValue was not successful.");  \n'
        + '        return false;  \n'
        + '     }  \n'
        + '     // Try to set the element  \n'
        + '     var result = api.<?php echo $LMS_prefix; ?>SetValue( item, setValue );  \n'
        + '     var err = doLMSGetLastError();  \n'
        + '     var errString = doLMSGetErrorString(err);  \n'
        + '     msg = "<?php echo $LMS_prefix; ?>SetValue returned: " + result;  \n'
        + '     msg += "\\nError Code: " + err;  \n'
        + '     msg += "\\nError Description: " + errString;  \n'
        + '     document.elemForm.msgtxt.value = msg;  \n'
        + '     document.elemForm.API_ELEMENT.value = saveElement; \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSGetLastError() {  \n'
        + '      var err = doLMSGetLastError();  \n'
        + '      document.otherForm.msgtxt.value = "<?php echo $LMS_prefix; ?>GetLastError returned Error Code:  " + err;  \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSGetErrorString() {  \n'
        + '      var err = doLMSGetLastError();  \n'
        + '      var errString = doLMSGetErrorString(err);  \n'
        + '      document.otherForm.msgtxt.value = "<?php echo $LMS_prefix; ?>GetErrorString returned:  " + errString;  \n'
        + '   }  \n'
        + '  \n'
        + '   function tryLMSGetDiagnostic() {  \n'
        + '      var err = doLMSGetLastError();  \n'
        + '      var diagnostic = doLMSGetDiagnostic(err);  \n'
        + '      document.otherForm.msgtxt.value = "<?php echo $LMS_prefix; ?>GetDiagnostic returned:  " + diagnostic;  \n'
        + '   } \n'
        + ' \n'
        + '</script>\n'
        + '<\/head><body STYLE="background-color: ffffff; color: black"'
        + 'marginwidth="0" leftmargin="0" hspace="0">'
        + '<h1>SCORM Debugging interface</h1>'
        + '<h2>SCORM Version Detected: <?php echo $scorm->version; ?></h2>'
        + '<input type="hidden" id="mod-scorm-logstate" name="mod-scorm-logstate" value="A" \/>'
        + '<form name="initForm" onsubmit="return false;">'
        + '   <table width="100%" border="0">'
        + '      <tr>'
        + '         <td>'
        + '            <input type = "button" value = "Call <?php echo $LMS_prefix; ?>Initialize()" onclick = "tryLMSInitialize();" id="Initialize" name="Initialize" />'
        + '         </td>'
        + '         <td>'
        + '            <label>Result: </label><input type="text" name="msgtxt" id="msgtxt" size="80" readonly value="NotCalled" />'
        + '         </td>'
        + '      </tr>'
        + '   </table>'
        + '</form>'
        + '<hr />'
        + '<form name="elemForm" id="elemForm" onsubmit="return false;">'
        + '   <table width="100%" border="0">'
        + '      <tr>'
        + '         <td><b>Select Data Model Element to Get or Set</b> &nbsp;&nbsp;&nbsp;&nbsp;'
        + '            <select name = "ELEMENT_LIST" id="ELEMENT_LIST" onchange="setAPIValue()">'
        + '               <option value="NONE">--None Selected--</option> <option value="">******************************************</option>'
<?php
                          foreach ($LMS_elements as $element) {
                              echo ' + \'               <option value="'.$element.'">'.$element.'</option>\\n\'';
                          }
?>
        + '            </select>'
        + '            <input type="text" name="API_ELEMENT" id="API_ELEMENT" size="40"><br />'
        + '            <br />'
        + '            <label><b>Select API Function to Call</b></label> &nbsp;&nbsp;&nbsp;&nbsp; <input type = "button" value = "<?php echo $LMS_prefix; ?>GetValue()"'
        + '                         onclick = "tryLMSGetValue();" id="lmsGetButton"'
        + '                         name="lmsGetButton">&nbsp;&nbsp;-- OR --&nbsp;&nbsp;'
        + '            <input type="button" value="<?php echo $LMS_prefix; ?>SetValue()"'
        + '                          onclick="tryLMSSetValue();" id="lmsSetButton"'
        + '                          name="lmsSetButton">'
        + '            <label><b>&nbsp; value to Set: </b></label>&nbsp; <input type="text" name="SET_VAL" id="SET_VAL" size="25">'
        + '            <br />'
        + '            <label>Result: </label><br />'
        + '            <textarea name="msgtxt" id="msgtxt" rows="2" cols="150" wrap="VIRTUAL" readonly>None</textarea>'
        + '         </td>'
        + '      </tr>'
        + '   </table>'
        + '</form>'
        + '<hr />'
        + '<form name="otherForm" onsubmit="return false;">'
        + '   <h3>Additional API Functions</h3>'
        + '   <table width="100%" border="0">'
        + '      <tr>'
        + '         <td><input type="button"'
        + '             value="<?php echo $LMS_prefix; ?>GetLastError()  "'
        + '             onclick="tryLMSGetLastError();"'
        + '             id="lastErrorButton"'
        + '             name="lastErrorButton">'
        + '            <input type="button"'
        + '             value="<?php echo $LMS_prefix; ?>GetErrorString()  "'
        + '             onclick="tryLMSGetErrorString();"'
        + '             id="getErrorStringButton"'
        + '             name="getErrorStringButton">'
        + '            <input type="button"'
        + '             value="<?php echo $LMS_prefix; ?>GetDiagnostic()  "'
        + '             onclick="tryLMSGetDiagnostic();"'
        + '             id="getDiagnosticButton"'
        + '             name="getDiagnosticButton">'
        + '            <input type="button"'
        + '             value="<?php echo $LMS_prefix; ?>Commit()  "'
        + '             onclick="tryLMSCommit();"'
        + '             id="commitButton"'
        + '             name="commitButton">'
        + '            <input type="button"'
        + '             value="<?php echo $scorm->version == 'scorm_12' ? 'LMSFinish' : 'Terminate'; ?>()  "'
        + '             onclick="try<?php echo $scorm->version == 'scorm_12' ? 'LMSFinish' : 'LMSTerminate'; ?>();"'
        + '             id="finishButton"'
        + '             name="finishButton">'
        + '         </td>'
        + '    </tr>'
        + '    <tr>'
        + '         <td>'
        + '         <label>Result: </label><br />'
        + '            <textarea name="msgtxt" id="msgtxt" rows="2" cols="150" wrap="VIRTUAL" readonly>None</textarea>'
        + '     </td>'
        + '      </tr>'
        + '   </table>'
        + '</form>'
        + '<h3 id="mod-scorm-marker">SCORM API Activity Log<\/h3>';

    // Is logging active?
    if (getLoggingActive() != "A") {
        return;
    }

    var popupdoc = '';
    logString += s;
    if (logPopUpWindow != 'N' && !logPopUpWindow.closed) {
        popupdoc = logPopUpWindow.document;
        popupdoc.body.innerHTML += s;
    } else {
        logPopUpWindow = open( '', 'scormlogpopupwindow', '' );
        popupdoc = logPopUpWindow.document;
        // Is logging active?
        var marker = safeGetElement(popupdoc, 'mod-scorm-marker');
        if (marker) {
            popupdoc.body.innerHTML += s;
        } else {
            popupdoc.open();
            popupdoc.write(s1);
            popupdoc.write(logString);
            popupdoc.write('<\/body><\/html>')
            popupdoc.close();
            popupdoc.title = 'SCORM API Activity Log';
            logPopUpWindow.focus();
        }
    }
    if (popupdoc.body && popupdoc.body.childNodes.length > 0) {
        popupdoc.body.lastChild.scrollIntoView();
    };
}

//add an individual log entry
function AppendToLog(s, rc) {
    var sStyle;
    if (rc != 0) {
        sStyle = 'class="error';
    } else if (logRow % 2 != 0) {
        sStyle = 'class="even"';
    } else {
        sStyle = 'class="odd"';
    }
    sStyle += '"';
    var now = new Date();
    now.setTime( now.getTime() );
    s = '<div ' + sStyle + ' id="<?php echo $scorm->version;?>">' + now.toGMTString() + ': ' + s + '<\/div>';
    UpdateLog(s);
    // switch colours for a new section of work
    if (s.match(/Commit|Loaded|Initialize|Terminate|Finish|Moodle SCORM|Moodle Logging/)) {
        logRow++;
    }
}

// format a log entry
function LogAPICall(func, nam, val, rc) {
    // drop call to GetLastError for the time being - it produces too much chatter
    if (func.match(/GetLastError/)) {
        return;
    }
    var s = func + '("' + nam + '"';
    if (val != null && ! (func.match(/GetValue|GetLastError/))) {
        s += ', "' + val + '"';
    }
    s += ')';
    if (func.match(/GetValue/)) {
        s += ' - ' + val;
    }
    s += ' => ' + String(rc);
    AppendToLog(s, rc);
}


// Add in a JS controlled link for toggling the Debug logging
var logButton = document.createElement('a');
logButton.id = 'mod-scorm-log-toggle';
logButton.name = 'logToggle';
logButton.href = 'javascript:toggleLog();';
if (getLoggingActive() == "A") {
    logButton.innerHTML = '<?php print_string('scormloggingon','scorm') ?>';
} else {
    logButton.innerHTML = '<?php print_string('scormloggingoff','scorm') ?>';
}
var content = safeGetElement(document, 'scormpage');
content.insertBefore(logButton, content.firstChild);

