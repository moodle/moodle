<?php
require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lti/OAuthBody.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

define('LTI_ITEM_TYPE', 'mod');
define('LTI_ITEM_MODULE', 'lti');
define('LTI_SOURCE', 'mod/lti');

function lti_get_response_xml($codemajor, $description, $messageref, $messagetype){
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><imsx_POXEnvelopeResponse />');
    $xml->addAttribute('xmlns', 'http://www.imsglobal.org/lis/oms1p0/pox');
    
    $headerinfo = $xml->addChild('imsx_POXHeader')
                      ->addChild('imsx_POXResponseHeaderInfo');
    
    $headerinfo->addChild('imsx_version', 'V1.0');
    $headerinfo->addChild('imsx_messageIdentifier', (string)mt_rand());
    
    $statusinfo = $headerinfo->addChild('imsx_statusInfo');
    $statusinfo->addchild('imsx_codeMajor', $codemajor);
    $statusinfo->addChild('imsx_severity', 'status');
    $statusinfo->addChild('imsx_description', $description);
    $statusinfo->addChild('imsx_messageRefIdentifier', $messageref);
    
    $xml->addChild('imsx_POXBody')
        ->addChild($messagetype);
    
    return $xml;
}

function lti_parse_message_id($xml){
    $node = $xml->imsx_POXHeader->imsx_POXRequestHeaderInfo->imsx_messageIdentifier;
    $messageid = (string)$node;
    
    return $messageid;
}

function lti_parse_grade_replace_message($xml){
    $node = $xml->imsx_POXBody->replaceResultRequest->resultRecord->sourcedGUID->sourcedId;
    $resultjson = json_decode((string)$node);
    
    $node = $xml->imsx_POXBody->replaceResultRequest->resultRecord->result->resultScore->textString;
    $grade = floatval((string)$node);
    
    $parsed = new stdClass();
    $parsed->gradeval = $grade * 100;
    $parsed->instanceid = $resultjson->data->instanceid;
    $parsed->userid = $resultjson->data->userid;
    $parsed->messageid = lti_parse_message_id($xml);
    
    return $parsed;
}

function lti_parse_grade_read_message($xml){
    $node = $xml->imsx_POXBody->readResultRequest->resultRecord->sourcedGUID->sourcedId;
    $resultjson = json_decode((string)$node);
        
    $parsed = new stdClass();
    $parsed->instanceid = $resultjson->data->instanceid;
    $parsed->userid = $resultjson->data->userid;
    $parsed->messageid = lti_parse_message_id($xml);
    
    return $parsed;
}

function lti_parse_grade_delete_message($xml){
    $node = $xml->imsx_POXBody->deleteResultRequest->resultRecord->sourcedGUID->sourcedId;
    $resultjson = json_decode((string)$node);
    
    $parsed = new stdClass();
    $parsed->instanceid = $resultjson->data->instanceid;
    $parsed->userid = $resultjson->data->userid;
    $parsed->messageid = lti_parse_message_id($xml);
    
    return $parsed;
}

function lti_update_grade($ltiinstance, $userid, $gradeval){
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    
    $params = array();
    $params['itemname'] = $ltiinstance->name;

    $grade = new stdClass();
    $grade->userid   = $userid;
    $grade->rawgrade = $gradeval;

    $status = grade_update(LTI_SOURCE, $ltiinstance->course, LTI_ITEM_TYPE, LTI_ITEM_MODULE, $ltiinstance->id, 0, $grade, $params);    

    return $status == GRADE_UPDATE_OK;
}

function lti_read_grade($ltiinstance, $userid){
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    
    $grades = grade_get_grades($ltiinstance->course, LTI_ITEM_TYPE, LTI_ITEM_MODULE, $ltiinstance->id, $userid);
    
    if (isset($grades) && is_array($grades->items[0]->grades)) {
        foreach ($grades->items[0]->grades as $agrade) {
            $grade = $agrade->grade;
            break;
        }
    }
    
    if(isset($grade)){
        return $grade;
    }
}

function lti_delete_grade($ltiinstance, $userid){
    $grade = new stdClass();
    $grade->userid   = $userid;
    $grade->rawgrade = null;

    $status = grade_update(LTI_SOURCE, $ltiinstance->course, LTI_ITEM_TYPE, LTI_ITEM_MODULE, $ltiinstance->id, 0, $grade, array('deleted'=>1));
    
    return $status == GRADE_UPDATE_OK || $status == GRADE_UPDATE_ITEM_DELETED; //grade_update seems to return ok now, but could reasonably return deleted in the future
}

function lti_verify_message($ltiinstance){
    //Use the key / secret configured on the tool, or look it up from the admin config
    if(empty($ltiinstance->resourcekey) || empty($ltiinstance->password)){
        if($ltiinstance->typeid){
            $typeid = $ltiinstance->typeid;
        } else {
            $tool = lti_get_tool_by_url_match($ltiinstance->toolurl);

            if(!$tool){
                throw new Exception('Tool configuration not found for tool instance ' . $ltiinstance->id);
            }
            
            $typeid = $tool->id;
        }

        $typeconfig = lti_get_type_config($typeid);//Consider only fetching the 2 necessary settings here
        
        $key = $typeconfig['resourcekey'];
        $secret = $typeconfig['password'];
    } else {
        $key = $ltiinstance->resourcekey;
        $secret = $ltiinstance->password;
    }
    
    handleOAuthBodyPOST($key, $secret);
}

$xmlfragment = file_get_contents("php://input");
$xml = new SimpleXMLElement($xmlfragment);

$body = $xml->imsx_POXBody;
foreach($body->children() as $child){
    $messagetype = $child->getName();
}

switch($messagetype){
    case 'replaceResultRequest':
        $parsed = lti_parse_grade_replace_message($xml);
       
        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));
        
        lti_verify_message($ltiinstance);
        
        $gradestatus = lti_update_grade($ltiinstance, $parsed->userid, $parsed->gradeval);
        
        $responsexml = lti_get_response_xml(
                $gradestatus ? 'success' : 'error', 
                'Grade replace response',
                $parsed->messageid,
                'replaceResultResponse'
        );
        
        echo $responsexml->asXML();
        
        break;
    
    case 'readResultRequest':
        $parsed = lti_parse_grade_read_message($xml);
        
        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));
        
        lti_verify_message($ltiinstance);
        
        $grade = lti_read_grade($ltiinstance, $parsed->userid);
        
        $responsexml = lti_get_response_xml(
                isset($grade) ? 'success' : 'error',
                'Result read',
                $parsed->messageid,
                'readResultResponse'
        );
        
        $node = $responsexml->imsx_POXBody->readResultResponse;
        $node->addChild('result')
             ->addChild('resultScore')
             ->addChild('textString', isset($grade) ? $grade : '');
        
        echo $responsexml->asXML();
        
        break;
    
    case 'deleteResultRequest':
        $parsed = lti_parse_grade_delete_message($xml);
        
        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));
        
        lti_verify_message($ltiinstance);
        
        $gradestatus = lti_delete_grade($ltiinstance, $parsed->userid);
        
        $responsexml = lti_get_response_xml(
                $gradestatus ? 'success' : 'error', 
                'Grade delete request', 
                $parsed->messageid, 
                'deleteResultResponse'
        );
 
        echo $responsexml->asXML();
        
        break;
}


//echo print_r(apache_request_headers(), true);

//echo '<br />';

//echo file_get_contents("php://input");