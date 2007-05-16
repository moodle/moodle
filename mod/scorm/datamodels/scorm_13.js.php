<?php
    if (isset($userdata->status)) {
        //if ($userdata->status == ''&& (!(($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))&& !($userdata->{'adl.nav.request'} == 'suspendAll'))||($userdata->{'cmi.exit'} == 'normal')) {      //antes solo llegaba esta línea hasta el &&
        if (!isset($userdata->{'cmi.exit'}) || (($userdata->{'cmi.exit'} == 'time-out') || ($userdata->{'cmi.exit'} == 'normal'))) { 
                $userdata->entry = 'ab-initio';
        } else {
            //if ((isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout')))||(($userdata->{'adl.nav.request'} == 'suspendAll')&& isset($userdata->{'adl.nav.request'}) )) {
            if (isset($userdata->{'cmi.exit'}) && (($userdata->{'cmi.exit'} == 'suspend') || ($userdata->{'cmi.exit'} == 'logout'))) {
                $userdata->entry = 'resume';
            } else {
                $userdata->entry = '';
            }
        }
    }
?>
//    var cmi = new Object(); // Used need to debug cmi content (if you uncomment this, you must comment the definition inside SCORMapi1_3)

//
// SCORM 1.3 API Implementation
//
function SCORMapi1_3() {
    // Standard Data Type Definition
    CMIString200 = '^.{0,200}$';
    CMIString250 = '^.{0,250}$';
    CMILangString250 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,250}$)?';
    CMIString1000 = '^.{0,1500}$';
    CMIString4000 = '^.{0,4000}$';
    CMILangString4000 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,4000}$)?';
    CMIString64000 = '^.{0,64000}$';
    CMILang = '^([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?$|^$';
    CMITime = '^(19[7-9]{1}[0-9]{1}|20[0-2]{1}[0-9]{1}|203[0-8]{1})((-(0[1-9]{1}|1[0-2]{1}))((-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1}))(T([0-1]{1}[0-9]{1}|2[0-3]{1})((:[0-5]{1}[0-9]{1})((:[0-5]{1}[0-9]{1})((\\.[0-9]{1,2})((Z|([+|-]([0-1]{1}[0-9]{1}|2[0-3]{1})))(:[0-5]{1}[0-9]{1})?)?)?)?)?)?)?)?$';
    CMITimespan = '^P(\\d+Y)?(\\d+M)?(\\d+D)?(T(((\\d+H)(\\d+M)?(\\d+(\.\\d{1,2})?S)?)|((\\d+M)(\\d+(\.\\d{1,2})?S)?)|((\\d+(\.\\d{1,2})?S))))?$';
    CMIInteger = '^\\d+$';
    CMISInteger = '^-?([0-9]+)$';
    CMIDecimal = '^-?([0-9]{1,4})(\\.[0-9]{1,18})?$';
    CMIIdentifier = '^\\S{0,200}[a-zA-Z0-9]$';
    CMILongIdentifier = '^\\S{0,4000}[a-zA-Z0-9]$';
    CMIFeedback = CMIString200; // This must be redefined
    CMIIndex = '[._](\\d+).';
    CMIIndexStore = '.N(\\d+).';
    // Vocabulary Data Type Definition
    CMICStatus = '^completed$|^incomplete$|^not attempted$|^unknown$';
    CMISStatus = '^passed$|^failed$|^unknown$';
    CMIExit = '^time-out$|^suspend$|^logout$|^normal$|^$';
    CMIType = '^true-false$|^choice$|^(long-)?fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$|^other$';
    CMIResult = '^correct$|^incorrect$|^unanticipated$|^neutral$|^-?([0-9]{1,4})(\\.[0-9]{1,18})?$';
    NAVEvent = '^previous$|^continue$|^exit$|^exitAll$|^abandon$|^abandonAll$|^suspendAll$|^{target=\\S{0,200}[a-zA-Z0-9]}choice$';
    NAVBoolean = '^unknown$|^true$|^false$';
    NAVTarget = '^previous$|^continue$|^choice.{target=\\S{0,200}[a-zA-Z0-9]}$'
    // Children lists
    cmi_children = '_version, comments_from_learner, comments_from_lms, completion_status, credit, entry, exit, interactions, launch_data, learner_id, learner_name, learner_preference, location, max_time_allowed, mode, objectives, progress_measure, scaled_passing_score, score, session_time, success_status, suspend_data, time_limit_action, total_time';
    comments_children = 'comment, timestamp, location';
    score_children = 'max, raw, scaled, min';
    objectives_children = 'progress_measure, completion_status, success_status, description, score, id';
    student_data_children = 'mastery_score, max_time_allowed, time_limit_action';
    student_preference_children = 'audio_level, audio_captioning, delivery_speed, language';
    interactions_children = 'id, type, objectives, timestamp, correct_responses, weighting, learner_response, result, latency, description';
    // Data ranges
    scaled_range = '-1#1';
    audio_range = '0#*';
    speed_range = '0#*';
    text_range = '-1#1';
    progress_range = '0#1';
    // The SCORM 1.3 data model
    var datamodel =  {
        'cmi._children':{'defaultvalue':cmi_children, 'mod':'r'},
        'cmi._version':{'defaultvalue':'1.0', 'mod':'r'},
        'cmi.comments_from_learner._children':{'defaultvalue':comments_children, 'mod':'r'},
        'cmi.comments_from_learner._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.comments_from_learner.n.comment':{'format':CMILangString4000, 'mod':'rw'},
        'cmi.comments_from_learner.n.location':{'format':CMIString250, 'mod':'rw'},
        'cmi.comments_from_learner.n.timestamp':{'format':CMITime, 'mod':'rw'},
        'cmi.comments_from_lms._children':{'defaultvalue':comments_children, 'mod':'r'},
        'cmi.comments_from_lms._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.comments_from_lms.n.comment':{'format':CMILangString4000, 'mod':'r'},
        'cmi.comments_from_lms.n.location':{'format':CMIString250, 'mod':'r'},
        'cmi.comments_from_lms.n.timestamp':{'format':CMITime, 'mod':'r'},
        'cmi.completion_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.completion_status'})?$userdata->{'cmi.completion_status'}:'unknown' ?>', 'format':CMICStatus, 'mod':'rw'},
        'cmi.completion_threshold':{'defaultvalue':<?php echo isset($userdata->threshold)?'\''.$userdata->threshold.'\'':'null' ?>, 'mod':'r'},
        'cmi.credit':{'defaultvalue':'<?php echo isset($userdata->credit)?$userdata->credit:'' ?>', 'mod':'r'},
        'cmi.entry':{'defaultvalue':'<?php echo $userdata->entry ?>', 'mod':'r'},
        'cmi.exit':{'defaultvalue':'<?php echo isset($userdata->{'cmi.exit'})?$userdata->{'cmi.exit'}:'' ?>', 'format':CMIExit, 'mod':'w'},
        'cmi.interactions._children':{'defaultvalue':interactions_children, 'mod':'r'},
        'cmi.interactions._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.interactions.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.interactions.n.type':{'pattern':CMIIndex, 'format':CMIType, 'mod':'rw'},
        'cmi.interactions.n.objectives._count':{'pattern':CMIIndex, 'mod':'r', 'defaultvalue':'0'},
        'cmi.interactions.n.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.interactions.n.timestamp':{'pattern':CMIIndex, 'format':CMITime, 'mod':'rw'},
        'cmi.interactions.n.correct_responses._count':{'defaultvalue':'0', 'pattern':CMIIndex, 'mod':'r'},
        'cmi.interactions.n.correct_responses.n.pattern':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
        'cmi.interactions.n.weighting':{'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.interactions.n.learner_response':{'pattern':CMIIndex, 'format':CMIFeedback, 'mod':'rw'},
        'cmi.interactions.n.result':{'pattern':CMIIndex, 'format':CMIResult, 'mod':'rw'},
        'cmi.interactions.n.latency':{'pattern':CMIIndex, 'format':CMITimespan, 'mod':'rw'},
        'cmi.interactions.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
        'cmi.launch_data':{'defaultvalue':<?php echo isset($userdata->datafromlms)?'\''.$userdata->datafromlms.'\'':'null' ?>, 'mod':'r'},
        'cmi.learner_id':{'defaultvalue':'<?php echo $userdata->student_id ?>', 'mod':'r'},
        'cmi.learner_name':{'defaultvalue':'<?php echo addslashes($userdata->student_name) ?>', 'mod':'r'},
        'cmi.learner_preference._children':{'defaultvalue':student_preference_children, 'mod':'r'},
        'cmi.learner_preference.audio_level':{'defaultvalue':'1', 'format':CMIDecimal, 'range':audio_range, 'mod':'rw'},
        'cmi.learner_preference.language':{'defaultvalue':'', 'format':CMILang, 'mod':'rw'},
        'cmi.learner_preference.delivery_speed':{'defaultvalue':'1', 'format':CMIDecimal, 'range':speed_range, 'mod':'rw'},
        'cmi.learner_preference.audio_captioning':{'defaultvalue':'0', 'format':CMISInteger, 'range':text_range, 'mod':'rw'},
        'cmi.location':{'defaultvalue':<?php echo isset($userdata->{'cmi.location'})?'\''.$userdata->{'cmi.location'}.'\'':'null' ?>, 'format':CMIString1000, 'mod':'rw'},
        'cmi.max_time_allowed':{'defaultvalue':<?php echo isset($userdata->maxtimeallowed)?'\''.$userdata->maxtimeallowed.'\'':'null' ?>, 'mod':'r'},
        'cmi.mode':{'defaultvalue':'<?php echo $userdata->mode ?>', 'mod':'r'},
        'cmi.objectives._children':{'defaultvalue':objectives_children, 'mod':'r'},
        'cmi.objectives._count':{'mod':'r', 'defaultvalue':'0'},
        'cmi.objectives.n.id':{'pattern':CMIIndex, 'format':CMILongIdentifier, 'mod':'rw'},
        'cmi.objectives.n.score._children':{'defaultvalue':score_children, 'pattern':CMIIndex, 'mod':'r'},
        'cmi.objectives.n.score.scaled':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
        'cmi.objectives.n.score.raw':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.score.min':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.score.max':{'defaultvalue':null, 'pattern':CMIIndex, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.objectives.n.success_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMISStatus, 'mod':'rw'},
        'cmi.objectives.n.completion_status':{'defaultvalue':'unknown', 'pattern':CMIIndex, 'format':CMICStatus, 'mod':'rw'},
        'cmi.objectives.n.progress_measure':{'defaultvalue':null, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
        'cmi.objectives.n.description':{'pattern':CMIIndex, 'format':CMILangString250, 'mod':'rw'},
        'cmi.progress_measure':{'defaultvalue':<?php echo isset($userdata->{'cmi.progess_measure'})?'\''.$userdata->{'cmi.progress_measure'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':progress_range, 'mod':'rw'},
        'cmi.scaled_passing_score':{'defaultvalue':<?php echo isset($userdata->{'cmi.scaled_passing_score'})?'\''.$userdata->{'cmi.scaled_passing_score'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'r'},
        'cmi.score._children':{'defaultvalue':score_children, 'mod':'r'},
        'cmi.score.scaled':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.scaled'})?'\''.$userdata->{'cmi.score.scaled'}.'\'':'null' ?>, 'format':CMIDecimal, 'range':scaled_range, 'mod':'rw'},
        'cmi.score.raw':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.raw'})?'\''.$userdata->{'cmi.score.raw'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.score.min':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.min'})?'\''.$userdata->{'cmi.score.min'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.score.max':{'defaultvalue':<?php echo isset($userdata->{'cmi.score.max'})?'\''.$userdata->{'cmi.score.max'}.'\'':'null' ?>, 'format':CMIDecimal, 'mod':'rw'},
        'cmi.session_time':{'format':CMITimespan, 'mod':'w', 'defaultvalue':'PT0H0M0S'},
        'cmi.success_status':{'defaultvalue':'<?php echo isset($userdata->{'cmi.success_status'})?$userdata->{'cmi.success_status'}:'unknown' ?>', 'format':CMISStatus, 'mod':'rw'},
        'cmi.suspend_data':{'defaultvalue':<?php echo isset($userdata->{'cmi.suspend_data'})?'\''.$userdata->{'cmi.suspend_data'}.'\'':'null' ?>, 'format':CMIString64000, 'mod':'rw'},
        'cmi.time_limit_action':{'defaultvalue':<?php echo isset($userdata->timelimitaction)?'\''.$userdata->timelimitaction.'\'':'null' ?>, 'mod':'r'},
        'cmi.total_time':{'defaultvalue':'<?php echo isset($userdata->{'cmi.total_time'})?$userdata->{'cmi.total_time'}:'PT0H0M0S' ?>', 'mod':'r'},
        'adl.nav.request':{'defaultvalue':'_none_', 'format':NAVEvent, 'mod':'rw'}
    };
    //
    // Datamodel inizialization
    //
    var cmi = new Object();
        cmi.comments_from_learner = new Object();
        cmi.comments_from_learner._count = 0;
        cmi.comments_from_lms = new Object();
        cmi.comments_from_lms._count = 0;
        cmi.interactions = new Object();
        cmi.interactions._count = 0;
        cmi.learner_preference = new Object();
        cmi.objectives = new Object();
        cmi.objectives._count = 0;
        cmi.score = new Object();

    // Navigation Object
    var adl = new Object();
        adl.nav = new Object();
        adl.nav.request_valid = new Array();

    for (element in datamodel) {
        if (element.match(/\.n\./) == null) {
            if ((typeof eval('datamodel["'+element+'"].defaultvalue')) != 'undefined') {
                eval(element+' = datamodel["'+element+'"].defaultvalue;');
            } else {
                eval(element+' = "";');
            }
        }
    }

<?php
    $count = 0;
    $objectives = '';
    foreach($userdata as $element => $value){
        if (substr($element,0,14) == 'cmi.objectives') {
            preg_match('/\.(\d+)\./',$element,$matches);
            $element = preg_replace('/\.(\d+)\./',".N\$1.",$element);
            if ($matches[1] == $count) {
                $count++;
                $end = strpos($element,$matches[1])+strlen($matches[1]);
                $subelement = substr($element,0,$end);
                echo '    '.$subelement." = new Object();\n";
                echo '    '.$subelement.".score = new Object();\n";
                echo '    '.$subelement.".score._children = score_children;\n";
            }
            echo '    '.$element.' = \''.$value."';\n";
        }
    }
    if ($count > 0) {
        echo '    cmi.objectives._count = '.$count.";\n";
    }
?>

    if (cmi.completion_status == '') {
        cmi.completion_status = 'not attempted';
    } 
    
    //
    // API Methods definition
    //
    var Initialized = false;
    var Terminated = false;
    var diagnostic = "";

    function Initialize (param) {
        errorCode = "0";
        if (param == "") {
            if ((!Initialized) && (!Terminated)) {
                <?php 
                    if (debugging('',DEBUG_DEVELOPER)) {
                        echo 'alert("Initialized SCORM 1.3");';
                    }
                ?>
                Initialized = true;
                errorCode = "0";
                return "true";
            } else {
                if (Initialized) {
                    errorCode = "103";
                } else {
                    errorCode = "104";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("Initialize: "+GetErrorString(errorCode));';
            }
        ?>
        return "false";
    }
    
    function Terminate (param) {
        errorCode = "0";
        if (param == "") {
            if ((Initialized) && (!Terminated)) {
                <?php 
                    if (debugging('',DEBUG_DEVELOPER)) {
                        echo 'alert("Terminated SCORM 1.3");';
                    }
                ?>
                Initialized = false;
                Terminated = true;
                result = StoreData(cmi,true);
                if (adl.nav.request != '_none_') {
                    switch (adl.nav.request) {
                        case 'continue':
                            setTimeout('top.nextSCO();',500);
                        break;
                        case 'previous':
                            setTimeout('top.prevSCO();',500);
                        break;
                        case 'choice':
                        break;
                        case 'exit':
                        break;
                        case 'exitAll':
                        break;
                        case 'abandon':
                        break;
                        case 'abandonAll':
                        break;
                    }
                } else {
                    if (<?php echo $scorm->auto ?> == 1) {
                        setTimeout('top.nextSCO();',500);
                    }
                }    
                return "true";
            } else {
                if (Terminated) {
                    errorCode = "113";
                } else {
                    errorCode = "112";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("Terminate: "+GetErrorString(errorCode));';
            }
        ?>
        return "false";
    }
    
    function GetValue (element) {
        errorCode = "0";
        diagnostic = "";
        if ((Initialized) && (!Terminated)) {
            if (element !="") {
                expression = new RegExp(CMIIndex,'g');
                elementmodel = element.replace(expression,'.n.');

                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'w') {

                        element = element.replace(/\.(\d+)\./, ".N$1.");
                        element = element.replace(/\.(\d+)\./, ".N$1.");

                        elementIndexes = element.split('.');
                        subelement = element.substr(0,3);
                        i = 1;

                        while ((i < elementIndexes.length) && (typeof eval(subelement) != "undefined")) {
                            subelement += '.'+elementIndexes[i++];
                        }

                        if (subelement == element) {

                            if ((typeof eval(subelement) != "undefined") && (eval(subelement) != null)) {
                                errorCode = "0";
                                <?php 
                                    if (debugging('',DEBUG_DEVELOPER)) {
                                        echo 'alert("GetValue("+element+") -> "+eval(element));';
                                    }
                                ?>
                                return eval(element);
                            } else {
                                errorCode = "403";
                            }
                        } else {
                            errorCode = "301";
                        }
                    } else {
                        //errorCode = eval('datamodel["'+elementmodel+'"].readerror');
                        errorCode = "405";
                    }
                } else {
                    childrenstr = '._children';
                    countstr = '._count';
                    if (elementmodel.substr(elementmodel.length-childrenstr.length,elementmodel.length) == childrenstr) {
                        parentmodel = elementmodel.substr(0,elementmodel.length-childrenstr.length);
                        if ((typeof eval('datamodel["'+parentmodel+'"]')) != "undefined") {
                            errorCode = "301";
                            diagnostic = "Data Model Element Does Not Have Children";
                        } else {
                            errorCode = "401";
                        }
                    } else if (elementmodel.substr(elementmodel.length-countstr.length,elementmodel.length) == countstr) {
                        parentmodel = elementmodel.substr(0,elementmodel.length-countstr.length);
                        if ((typeof eval('datamodel["'+parentmodel+'"]')) != "undefined") {
                            errorCode = "301";
                            diagnostic = "Data Model Element Cannot Have Count";
                        } else {
                            errorCode = "401";
                        }
                    } else {
                        parentmodel = 'adl.nav.request_valid.';
                        if (element.substr(0,parentmodel.length) == parentmodel) {
                            if (element.substr(parentmodel.length).match(NAVTarget) == null) {
                                errorCode = "301";
                            } else {
                                if (adl.nav.request == element.substr(parentmodel.length)) {
                                    return "true";
                                } else if (adl.nav.request == '_none_') {
                                    return "unknown";
                                } else {
                                    return "false";
                                }
                            }
                        } else {
                            errorCode = "401";
                        }
                    }
                }
            } else {
                errorCode = "301";
            }
        } else {
            if (Terminated) {                
                errorCode = "123";
            } else {
                errorCode = "122";
            }
        }
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("GetValue("+element+") -> "+GetErrorString(errorCode));';
            }
        ?>
        return "";
    }
    
    function SetValue (element,value) {
        errorCode = "0";
        diagnostic = "";
        if ((Initialized) && (!Terminated)) {
            if (element != "") {
                expression = new RegExp(CMIIndex,'g');
                elementmodel = element.replace(expression,'.n.');
                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
                        expression = new RegExp(eval('datamodel["'+elementmodel+'"].format'));
                        value = value+'';
                        matches = value.match(expression);
                        if ((matches != null) && ((matches.join('').length > 0) || (value.length == 0))) {

                            //Create dynamic data model element

                            if (element != elementmodel) {
                                elementIndexes = element.split('.');
                                subelement = 'cmi';
                                parentelement = 'cmi';
                                for (i=1;(i < elementIndexes.length-1) && (errorCode=="0");i++) {
                                    elementIndex = elementIndexes[i];
                                    if (elementIndexes[i+1].match(/^\d+$/)) {
                                        if ((parseInt(elementIndexes[i+1]) > 0) && (elementIndexes[i+1].charAt(0) == 0)) {
                                            // Index has a leading 0 (zero), this is not a number
                                            errorCode = "351";
                                        }
                                        parentelement = subelement+'.'+elementIndex;
                                        if ((typeof eval(parentelement) == "undefined") || (typeof eval(parentelement+'._count') == "undefined")) {
                                            errorCode="408";
                                        } else {
                                            if (elementIndexes[i+1] > eval(parentelement+'._count')) {

                                                errorCode = "351";
                                                diagnostic = "Data Model Element Collection Set Out Of Order";
                                            }
                                            subelement = subelement.concat('.'+elementIndex+'.N'+elementIndexes[i+1]);
                                            i++;

                                            if (((typeof eval(subelement)) == "undefined") && (i < elementIndexes.length-2)) {
                                                errorCode="408";
                                            }
                                        }
                                    } else {
                                        subelement = subelement.concat('.'+elementIndex);
                                    }
                                }



                                if (errorCode == "0") {

                                    element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);
                                    elemlen = element.length;

                                    if (((typeof eval(subelement)) == "undefined") && (errorCode == "0")) {
                                        parentmodel = 'cmi.objectives';
                                        if (subelement.substr(0,parentmodel.length) == parentmodel) {

                                             if ((elementmodel==parentmodel+'.n.id') && (errorCode=="0")) { 

                                                //This is a parentmodel.n.id element
                                                if (!duplicatedID(parentmodel,value)) {
                                                    if (elementIndexes[elementIndexes.length-2] == eval(parentmodel+'._count')) {
                                                        eval(parentmodel+'._count++;');
                                                        eval(subelement+' = new Object();');
                                                        subobject = eval(subelement);
                                                        subobject.success_status = datamodel["cmi.objectives.n.success_status"].defaultvalue;
                                                        subobject.completion_status = datamodel["cmi.objectives.n.completion_status"].defaultvalue;
                                                        subobject.progress_measure = datamodel["cmi.objectives.n.progress_measure"].defaultvalue;
                                                        subobject.score = new Object();
                                                        subobject.score._children = score_children;
                                                        subobject.score.scaled = datamodel["cmi.objectives.n.score.scaled"].defaultvalue;
                                                        subobject.score.raw = datamodel["cmi.objectives.n.score.raw"].defaultvalue;
                                                        subobject.score.min = datamodel["cmi.objectives.n.score.min"].defaultvalue;
                                                        subobject.score.max = datamodel["cmi.objectives.n.score.max"].defaultvalue;
                                                    }
                                                } else {
                                                    errorCode="351";
                                                    diagnostic = "Data Model Element ID Already Exists";
                                                }
                                            } else {


                                                if (typeof eval(subelement) == "undefined") {
                                                    errorCode="408";
                                                } else {
                                                    if (duplicatedID(parentmodel,value)) {
                                                        errorCode="351";
                                                        diagnostic = "Data Model Element ID Already Exists";
                                                    }
                                                }
                                            }
                                        } else {

                                            parentmodel = 'cmi.interactions';

                                            if (subelement.substr(0,parentmodel.length) == parentmodel) {

                                                if ((elementmodel==parentmodel+'.n.id') && (errorCode=="0")) { 

                                                    //This is a parentmodel.n.id element
                                                    if (!duplicatedID(parentmodel,value)) {

                                                        if (elementIndexes[elementIndexes.length-2] == eval(parentmodel+'._count')) {

                                                            eval(parentmodel+'._count++;');

                                                            eval(subelement+' = new Object();');

                                                            subobject = eval(subelement);
                                                            subobject.objectives = new Object();
                                                            subobject.objectives._count = 0;
                                                            //subobject.correct_responses = new Object();
                                                            //subobject.correct_responses._count = 0;

                                                            
                                                        } 
                                                    } else {

                                                        errorCode="351";
                                                        diagnostic = "Data Model Element ID Already Exists";
                                                    }
                                                } else {
//alert(element+"\n"+subelement);
                                                    if ((elementmodel=='cmi.interactions.n.learner_response') && (typeof eval(parentelement+'.type') == "undefined")) {
                                                        errorCode="408";
                                                    }
                                                    if (typeof eval(subelement) == "undefined") {
                                                        if ((elementmodel=='cmi.interactions.n.objectives.n.id') && (typeof eval(parentelement) != "undefined")) {
                                                           if (!duplicatedID(parentelement,value)) {
                                                               if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                                   eval(parentelement+'._count++;');

                                                                   eval(subelement+' = new Object();');
                                                               }
                                                           } else {

                                                               errorCode="351";
                                                               diagnostic = "Data Model Element ID Already Exists";
                                                           }
                                                        } else 
                                                        if ((elementmodel=='cmi.interactions.n.correct_responses.n.pattern') && (typeof eval(parentelement) != "undefined")) {
                                                            if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                                eval(parentelement+'._count++;');

                                                                eval(subelement+' = new Object();');
                                                            }
                                                        } else {
                                                            errorCode="408";
                                                        }
                                                    } else {
//alert('element = '+element+"\nparentmodel = "+parentmodel+"\nparentelement = "+parentelement+"\nvalue = "+value);
                                                        if ((elementmodel==parentmodel+'.n.type') && (errorCode=="0")) { 
                                                            subobject = eval(subelement);
                                                            subobject.correct_responses = new Object();
                                                            subobject.correct_responses._count = 0;
                                                        } else {
                                                            errorCode="408";
                                                        }
                                                    }
                                                }
                                            } else {

                                                if (errorCode == "0") {
                                                    if (elementIndexes[elementIndexes.length-2] == eval(parentelement+'._count')) {
                                                        eval(parentelement+'._count++;');
                                                        eval(subelement+' = new Object();');
                                                    } 
                                                }
                                            }
                                        }
                                     } else {

                                         parentmodel = 'cmi.objectives';
                                         if (subelement.substr(0,parentmodel.length) == parentmodel) {
                                             if ((elementmodel==parentmodel+'.n.id') && (errorCode=="0")) { 

                                                 if (eval(element) != value) {
                                                     errorCode = "351";
                                                     diagnostic = "Write Once Violation";
                                                 }
                                             }
                                         } else {

                                             parentmodel = 'cmi.interactions';
                                             if (subelement.substr(0,parentmodel.length) == parentmodel) {
                                                 if ((elementmodel==parentmodel+'.n.id') && (errorCode=="0")) { 
                                                     if (eval(element) != value) {
                                                         errorCode = "351";
                                                         diagnostic = "Write Once Violation";
                                                     }
                                                 }
                                             }
                                         }
                                     }
                                }
                            }
                            //Store data
                            if (errorCode == "0") {

                                if ((typeof eval('datamodel["'+elementmodel+'"].range')) != "undefined") {
                                    range = eval('datamodel["'+elementmodel+'"].range');
                                    ranges = range.split('#');
                                    value = value*1.0;
                                    if (value >= ranges[0]) {
                                        if ((ranges[1] == '*') || (value <= ranges[1])) {
                                            eval(element+'=value;');
                                            errorCode = "0";
                                            <?php 
                                                if (debugging('',DEBUG_DEVELOPER)) {
                                                    echo 'alert("SetValue("+element+","+value+") -> OK");';
                                                }
                                            ?>
                                            return "true";
                                        } else {
                                            errorCode = '407';
                                        }
                                    } else {
                                        errorCode = '407';
                                    }
                                } else {
                                    eval(element+'=value;');
                                    errorCode = "0"; 
                                    <?php 
                                        if (debugging('',DEBUG_DEVELOPER)) {
                                            echo 'alert("SetValue("+element+","+value+") -> OK");';
                                        }
                                    ?>
                                    return "true";
                                }
                            }
                        } else {
                            //errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
                            errorCode = "406";
                        }
                    } else {
                        //errorCode = eval('datamodel["'+elementmodel+'"].writeerror');
                        errorCode = "404";
                    }
                } else {
                    errorCode = "401"
                }
            } else {
                errorCode = "351";
            }
        } else {
            if (Terminated) {
                errorCode = "133";
            } else {
                errorCode = "132";
            }
        }
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("SetValue("+element+","+value+") -> "+GetErrorString(errorCode));';
            }
        ?>
        return "false";
    }
    
    function Commit (param) {
        errorCode = "0";
        if (param == "") {
            if ((Initialized) && (!Terminated)) {
                result = StoreData(cmi,false);
                <?php 
                    if (debugging('',DEBUG_DEVELOPER)) {
                        echo 'alert("Data Commited");';
                    }
                ?>
                return "true";
            } else {
                if (Terminated) {
                    errorCode = "143";
                } else {
                    errorCode = "142";
                }
            }
        } else {
            errorCode = "201";
        }
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("Commit: "+GetErrorString(errorCode));';
            }
        ?>
        return "false";
    }
    
    function GetLastError () {
        return errorCode;
    }
    
    function GetErrorString (param) {
        if (param != "") {
            var errorString = "";
            switch(param) {
                case "0":
                    errorString = "No error";
                break;
                case "101":
                    errorString = "General exception";
                break;
                case "102":
                    errorString = "General Inizialization Failure";
                break;
                case "103":
                    errorString = "Already Initialized";
                break;
                case "104":
                    errorString = "Content Instance Terminated";
                break;
                case "111":
                    errorString = "General Termination Failure";
                break;
                case "112":
                    errorString = "Termination Before Inizialization";
                break;
                case "113":
                    errorString = "Termination After Termination";
                break;
                case "122":
                    errorString = "Retrieve Data Before Initialization";
                break;
                case "123":
                    errorString = "Retrieve Data After Termination";
                break;
                case "132":
                    errorString = "Store Data Before Inizialization";
                break;
                case "133":
                    errorString = "Store Data After Termination";
                break;
                case "142":
                    errorString = "Commit Before Inizialization";
                break;
                case "143":
                    errorString = "Commit After Termination";
                break;
                case "201":
                    errorString = "General Argument Error";
                break;
                case "301":
                    errorString = "General Get Failure";
                break;
                case "351":
                    errorString = "General Set Failure";
                break;
                case "391":
                    errorString = "General Commit Failure";
                break;
                case "401":
                    errorString = "Undefinited Data Model";
                break;
                case "402":
                    errorString = "Unimplemented Data Model Element";
                break;
                case "403":
                    errorString = "Data Model Element Value Not Initialized";
                break;
                case "404":
                    errorString = "Data Model Element Is Read Only";
                break;
                case "405":
                    errorString = "Data Model Element Is Write Only";
                break;
                case "406":
                    errorString = "Data Model Element Type Mismatch";
                break;
                case "407":
                    errorString = "Data Model Element Value Out Of Range";
                break;
                case "408":
                    errorString = "Data Model Dependency Not Established";
                break;
            }
            return errorString;
        } else {
           return "";
        }
    }
    
    function GetDiagnostic (param) {
        if (diagnostic != "") {
            return diagnostic;
        }
        return param;
    }

    function duplicatedID (element, value) {
        var found = false;
        var elements = eval(element+'._count');
        for (n=0;(n<elements) && (!found);n++) {
            if (eval(element+'.N'+n+'.id') == value) {
                found = true;
            }
        } 
        return found;
    }

    function getElementModel(element) {
        if (typeof datamodel[element] != "undefined") {
            return element;
        } else {
            expression = new RegExp(CMIIndex,'g');
            elementmodel = element.replace(expression,'.n.');
            if (typeof datamodel[elementmodel] != "undefined") {
                return elementmodel;
            }
        }
    }

    function AddTime (first, second) {
        <?php 
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'alert("AddTime: "+first+" + "+second);';
            }
        ?>
        var timestring = 'P';
        var matchexpr = /^P((\d+)Y)?((\d+)M)?((\d+)D)?(T((\d+)H)?((\d+)M)?((\d+(\.\d{1,2})?)S)?)?$/;
        var firstarray = first.match(matchexpr);
        var secondarray = second.match(matchexpr);
        if ((firstarray != null) && (secondarray != null)) {
            var secs = parseFloat(firstarray[13],10)+parseFloat(secondarray[13],10);  //Seconds
            change = Math.floor(secs / 60);
            secs = secs - (change * 60);
            mins = parseInt(firstarray[11],10)+parseInt(secondarray[11],10)+change;   //Minutes
            change = Math.floor(mins / 60);
            mins = mins - (change * 60);
            hours = parseInt(firstarray[10],10)+parseInt(secondarray[10],10)+change;  //Hours
            change = Math.floor(hours / 24);
            hours = hours - (change * 24);
            days = parseInt(firstarray[6],10)+parseInt(secondarray[6],10)+change; // Days
            months = parseInt(firstarray[4],10)+parseInt(secondarray[4],10)
            years = parseInt(firstarray[2],10)+parseInt(secondarray[2],10)
        }
        if (years > 0) {
            timestring += years + 'Y';
        }
        if (months > 0) {
            timestring += months + 'M';
        }
        if (days > 0) {
            timestring += days + 'D';
        }
        if ((hours > 0) || (mins > 0) || (secs > 0)) {
            timestring += 'T';
            if (hours > 0) {
                timestring += hours + 'H';
            }
            if (mins > 0) {
                timestring += mins + 'M';
            }
            if (secs > 0) {
                timestring += secs + 'S';
            }
        }
        return timestring;
    }

    function TotalTime() {
        total_time = AddTime(cmi.total_time, cmi.session_time);
        return '&'+underscore('cmi.total_time')+'='+encodeURIComponent(total_time);
    }

    function CollectData(data,parent) {
        var datastring = '';
        for (property in data) {
            if (typeof data[property] == 'object') {
                datastring += CollectData(data[property],parent+'.'+property);
            } else {
                element = parent+'.'+property;
                expression = new RegExp(CMIIndexStore,'g');
                elementmodel = element.replace(expression,'.n.');
                if ((typeof eval('datamodel["'+elementmodel+'"]')) != "undefined") {
                    if (eval('datamodel["'+elementmodel+'"].mod') != 'r') {
                        elementstring = '&'+underscore(element)+'='+encodeURIComponent(data[property]);
                        if ((typeof eval('datamodel["'+elementmodel+'"].defaultvalue')) != "undefined") {
                            if (eval('datamodel["'+elementmodel+'"].defaultvalue') != data[property]) {
                                datastring += elementstring;
                            }
                        } else {
                            datastring += elementstring;
                        }
                    }
                }
            }
        }
        return datastring;
    }

    function StoreData(data,storetotaltime) {
        datastring = '';
        if (storetotaltime) {
            if (cmi.mode == 'normal') {
                if (cmi.credit == 'credit') {
                    if ((cmi.completion_threshold != null) && (cmi.progress_measure != null)) {
                        if (cmi.progress_measure >= cmi.completion_threshold) {
                            cmi.completion_status = 'completed';
                        } else {
                            cmi.completion_status = 'incomplete';
                        }
                    }
                    if ((cmi.scaled_passed_score != null) && (cmi.score.scaled != '')) {
                        if (cmi.score.scaled >= cmi.scaled_passed_score) {
                            cmi.success_status = 'passed';
                        } else {
                            cmi.success_status = 'failed';
                        }
                    }
                }
            }
            datastring += TotalTime();
        }
        datastring += CollectData(data,'cmi');
        element = 'adl.nav.request';
        navrequest = eval(element) != datamodel[element].defaultvalue ? '&'+underscore(element)+'='+encodeURIComponent(eval(element)) : '';
        datastring += navrequest;
        datastring += '&attempt=<?php echo $attempt ?>';
        datastring += '&scoid=<?php echo $scoid ?>';
        <?php
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'popupwin(datastring);';
            }
        ?>
        var myRequest = NewHttpReq();
        result = DoRequest(myRequest,"<?php p($CFG->wwwroot) ?>/mod/scorm/datamodel.php","id=<?php p($id) ?>&sesskey=<?php p($USER->sesskey) ?>"+datastring);
        <?php
            if (debugging('',DEBUG_DEVELOPER)) {
                echo 'popupwin(result);';
            }
        ?>
        results = result.split('\n');
        if ((results.length > 2) && (navrequest != '')) {
            eval(results[2]);
        }
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
    this.version = '1.0';
}

var API_1484_11 = new SCORMapi1_3();
