    // Standard Data Type Definition
    CMIString255 = '^.{0,255}$';
    CMIString4096 = '^[.|\\n|\\r]{0,4096}$';
    CMITime = '^([0-9]{2}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMITimespan = '^([0-9]{2,4}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
    CMIInteger = '^\\d+$';
    CMISInteger = '^-?([0-9]+)$';
    CMIDecimal = '^[0-9]?(\.[0-9]{1,2})?$';
    CMIIdentifier = '^\\w{0,255}$';
    CMIFeedback = CMIString255; // This must be redefined
    CMIIndex = '.\\d+.';
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
	'cmi.core.student_id':{'defaultvalue':'<?php echo $USER->username ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.student_name':{'defaultvalue':'<?php echo $USER->lastname.', '.$USER->firstname ?>', 'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_location':{'format':CMIString255, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.credit':{'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_status':{'format':CMIStatus, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.entry':{'mod':'r', 'writeerror':'403'},
	'cmi.core.score._children':{'defaultvalue':score_children, 'mod':'r', 'writeerror':'402'},
	'cmi.core.score.raw':{'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.score.max':{'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.score.min':{'format':CMIDecimal, 'range':score_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.core.total_time':{'mod':'r', 'writeerror':'403'},
	'cmi.core.lesson_mode':{'mod':'r', 'writeerror':'405'},
	'cmi.core.exit':{'format':CMIExit, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
	'cmi.core.session_time':{'format':CMITimespan, 'mod':'w', 'defaultvalue':'00:00:00', 'readerror':'404', 'writeerror':'405'},
	'cmi.suspend_data':{'format':CMIString4096, 'mod':'rw', 'writeerror':'405'},
	'cmi.launch_data':{'mod':'r', 'writeerror':'403'},
	'cmi.comments':{'format':CMIString4096, 'mod':'rw', 'writeerror':'405'},
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
	'cmi.student_data.mastery_score':{'mod':'r', 'writeerror':'403'},
	'cmi.student_data.max_time_allowed':{'mod':'r', 'writeerror':'403'},
	'cmi.student_data.time_limit_action':{'mod':'r', 'writeerror':'403'},
	'cmi.student_preference._children':{'defaultvalue':student_preference_children, 'mod':'r', 'writeerror':'403'},
	'cmi.student_preference.audio':{'format':CMISInteger, 'range':audio_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.language':{'format':CMIString255, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.speed':{'format':CMISInteger, 'range':speed_range, 'mod':'rw', 'writeerror':'405'},
	'cmi.student_preference.text':{'format':CMISInteger, 'range':text_range, 'mod':'rw', 'writeerror':'405'},
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
    // Datamodel inizialization
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
