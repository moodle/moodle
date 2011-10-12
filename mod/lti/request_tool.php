<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');

$instanceid = required_param('instanceid', PARAM_INT);

$lti = $DB->get_record('lti', array('id' => $instanceid));
$course = $DB->get_record('course', array('id' => $lti->course));

require_login($course);

require_capability('mod/lti:requesttooladd', get_context_instance(CONTEXT_COURSE, $lti->course));

$baseurl = lti_get_domain_from_url($lti->toolurl);

$url = new moodle_url('/mod/lti/request_tool.php', array('instanceid' => $instanceid));
$PAGE->set_url($url);

$pagetitle = strip_tags($course->shortname);
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();

//Add a tool type if one does not exist already
if(!lti_get_tool_by_url_match($lti->toolurl, $lti->course, LTI_TOOL_STATE_ANY)){
    //There are no tools (active, pending, or rejected) for the launch URL. Create a new pending tool
    $tooltype = new stdClass();
    $toolconfig = new stdClass();

    $toolconfig->lti_toolurl = lti_get_domain_from_url($lti->toolurl); 
    $toolconfig->lti_typename = $toolconfig->lti_toolurl;

    lti_add_type($tooltype, $toolconfig);
    
    echo get_string('lti_tool_request_added', 'lti');
} else {
    echo get_string('lti_tool_request_existing', 'lti');
}

echo $OUTPUT->footer();