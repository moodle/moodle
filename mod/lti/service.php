<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * LTI web service endpoints
 *
 * @package    mod
 * @subpackage lti
 * @copyright  Copyright (c) 2011 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Chris Scribner
 */

require_once(dirname(__FILE__) . "/../../config.php");
require_once($CFG->dirroot.'/mod/lti/locallib.php');
require_once($CFG->dirroot.'/mod/lti/servicelib.php');

// TODO: Switch to core oauthlib once implemented - MDL-30149
use moodle\mod\lti as lti;

$rawbody = file_get_contents("php://input");

foreach (getallheaders() as $name => $value) {
    if ($name === 'Authorization') {
        // TODO: Switch to core oauthlib once implemented - MDL-30149
        $oauthparams = lti\OAuthUtil::split_header($value);

        $consumerkey = $oauthparams['oauth_consumer_key'];
        break;
    }
}

if (empty($consumerkey)) {
    throw new Exception('Consumer key is missing.');
}

$sharedsecret = lti_verify_message($consumerkey, lti_get_shared_secrets_by_key($consumerkey), $rawbody);

if ($sharedsecret === false) {
    throw new Exception('Message signature not valid');
}

$xml = new SimpleXMLElement($rawbody);

$body = $xml->imsx_POXBody;
foreach ($body->children() as $child) {
    $messagetype = $child->getName();
}

switch ($messagetype) {
    case 'replaceResultRequest':
        try {
            $parsed = lti_parse_grade_replace_message($xml);
        } catch (Exception $e) {
            $responsexml = lti_get_response_xml(
                'failure',
                $e->getMessage(),
                uniqid(),
                'replaceResultResponse');

            echo $responsexml->asXML();
            break;
        }

        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));

        lti_verify_sourcedid($ltiinstance, $parsed);

        $gradestatus = lti_update_grade($ltiinstance, $parsed->userid, $parsed->launchid, $parsed->gradeval);

        $responsexml = lti_get_response_xml(
                $gradestatus ? 'success' : 'failure',
                'Grade replace response',
                $parsed->messageid,
                'replaceResultResponse'
        );

        echo $responsexml->asXML();

        break;

    case 'readResultRequest':
        $parsed = lti_parse_grade_read_message($xml);

        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));

        //Getting the grade requires the context is set
        $context = context_course::instance($ltiinstance->course);
        $PAGE->set_context($context);

        lti_verify_sourcedid($ltiinstance, $parsed);

        $grade = lti_read_grade($ltiinstance, $parsed->userid);

        $responsexml = lti_get_response_xml(
                isset($grade) ? 'success' : 'failure',
                'Result read',
                $parsed->messageid,
                'readResultResponse'
        );

        $node = $responsexml->imsx_POXBody->readResultResponse;
        $node = $node->addChild('result')->addChild('resultScore');
        $node->addChild('language', 'en');
        $node->addChild('textString', isset($grade) ? $grade : '');

        echo $responsexml->asXML();

        break;

    case 'deleteResultRequest':
        $parsed = lti_parse_grade_delete_message($xml);

        $ltiinstance = $DB->get_record('lti', array('id' => $parsed->instanceid));

        lti_verify_sourcedid($ltiinstance, $parsed);

        $gradestatus = lti_delete_grade($ltiinstance, $parsed->userid);

        $responsexml = lti_get_response_xml(
                $gradestatus ? 'success' : 'failure',
                'Grade delete request',
                $parsed->messageid,
                'deleteResultResponse'
        );

        echo $responsexml->asXML();

        break;

    default:
        //Fire an event if we get a web service request which we don't support directly.
        //This will allow others to extend the LTI services, which I expect to be a common
        //use case, at least until the spec matures.
        $data = new stdClass();
        $data->body = $rawbody;
        $data->xml = $xml;
        $data->messagetype = $messagetype;
        $data->consumerkey = $consumerkey;
        $data->sharedsecret = $sharedsecret;

        //If an event handler handles the web service, it should set this global to true
        //So this code knows whether to send an "operation not supported" or not.
        global $lti_web_service_handled;
        $lti_web_service_handled = false;

        events_trigger('lti_unknown_service_api_call', $data);

        if (!$lti_web_service_handled) {
            $responsexml = lti_get_response_xml(
                'unsupported',
                'unsupported',
                 lti_parse_message_id($xml),
                 $messagetype
            );

            echo $responsexml->asXML();
        }

        break;
}


//echo print_r(apache_request_headers(), true);

//echo '<br />';

//echo file_get_contents("php://input");
