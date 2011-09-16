<?php
require_once(dirname(__FILE__) . "/../../config.php");
require_once($CFG->dirroot.'/mod/lti/locallib.php');
require_once($CFG->dirroot.'/mod/lti/servicelib.php');

$rawbody = file_get_contents("php://input");
$xml = new SimpleXMLElement($rawbody);

$body = $xml->imsx_POXBody;
foreach($body->children() as $child){
    $messagetype = $child->getName();
}

switch($messagetype){
    case 'replaceResultRequest':
        $parsed = lti_parse_grade_replace_message($xml);
       
        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));
        
        lti_verify_sourcedid($ltiinstance, $parsed);
        lti_verify_message($ltiinstance, $rawbody);
        
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
        
        lti_verify_sourcedid($ltiinstance, $parsed);
        lti_verify_message($ltiinstance, $rawbody);
        
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
        
        lti_verify_sourcedid($ltiinstance, $parsed);
        lti_verify_message($ltiinstance, $rawbody);
        
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


echo print_r(apache_request_headers(), true);

echo '<br />';

echo file_get_contents("php://input");