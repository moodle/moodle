<?php

//This page is used to handle the return back to Moodle from the tool provider

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/lti/lib.php');

$courseid = required_param('course', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);

$errormsg = optional_param('lti_errormsg', '', PARAM_RAW);
$unsigned = optional_param('unsigned', '0', PARAM_INT);

$launchcontainer = optional_param('launch_container', LTI_LAUNCH_CONTAINER_WINDOW, PARAM_INT);

$course = $DB->get_record('course', array('id' => $courseid));

require_login($course);

if(!empty($errormsg)){
    $url = new moodle_url('/mod/lti/return.php', array('course' => $courseid));
    $PAGE->set_url($url);
    
    $pagetitle = strip_tags($course->shortname);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);
    
    //Avoid frame-in-frame action
    if($launchcontainer == LTI_LAUNCH_CONTAINER_EMBED || $launchcontainer == LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS) {
        $PAGE->set_pagelayout('embedded');
    } else {
        $PAGE->set_pagelayout('incourse');
    }
    
    echo $OUTPUT->header();
    
    echo get_string('lti_launch_error', 'lti');
    
    echo htmlspecialchars($errormsg);

    $canaddtools = has_capability('mod/lti:addcoursetool', get_context_instance(CONTEXT_COURSE, $courseid));
    
    if($unsigned == 1 && $canaddtools){
        echo '<br /><br />';
        
        $links = new stdClass();
        $coursetooleditor = new moodle_url('/mod/lti/instructor_edit_tool_type.php', array('course' => $courseid, 'action' => 'add'));
        $links->course_tool_editor = $coursetooleditor->out(false);
        
        $adminrequesturl = new moodle_url('/mod/lti/request_tool.php', array('instanceid' => $instanceid));
        $links->admin_request_url = $adminrequesturl->out(false);
        
        echo get_string('lti_launch_error_unsigned_help', 'lti', $links);
        
        echo get_string('lti_launch_error_tool_request', 'lti', $links);
    }
    
    echo $OUTPUT->footer();
} else {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    $url = $courseurl->out();
    
    //Avoid frame-in-frame action
    if($launchcontainer == LTI_LAUNCH_CONTAINER_EMBED || $launchcontainer == LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS) {
        //Output a page containing some script to break out of frames and redirect them
        
        echo '<html><body>';
        
        $script = <<<SCRIPT
            <script type='text/javascript'>
            //<![CDATA[
                if(window != top){
                    top.location.href = '{$url}';
                }
            //]]
            </script>
SCRIPT;
        
        $clickhere = get_string('return_to_course', 'lti', (object)array('link' => $url));

        $noscript = <<<NOSCRIPT
            <noscript>
                {$clickhere}
            </noscript>
NOSCRIPT;
                    
        echo $script;
        echo $noscript;
                    
        echo '</body></html>';
    } else {
        //If no error, take them back to the course
        redirect($url);
    }
}