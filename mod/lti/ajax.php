<?php

require_once(dirname(__FILE__) . "/../../config.php");
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);

require_login($courseid, false);

$action = required_param('action', PARAM_TEXT);

$response = new stdClass();

switch($action){
    case 'find_tool_config':
        $toolurl = required_param('toolurl', PARAM_RAW);
        
        $tool = lti_get_tool_by_url_match($toolurl, $courseid);
        
        if(!empty($tool)){
            $response->toolid = $tool->id;
            $response->toolname = htmlspecialchars($tool->name);
        }
        
        break;
}

echo json_encode($response);

die;