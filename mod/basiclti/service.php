<?php
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains all necessary code to support basiclti services
 * like outcomes and roster access.
 *
 * @package basiclti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 * @author Charles Severance
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/basiclti/lib.php');
require_once($CFG->dirroot.'/mod/basiclti/locallib.php');
require_once($CFG->dirroot.'/mod/basiclti/OAuth.php');
require_once($CFG->dirroot.'/mod/basiclti/TrivialStore.php');

error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);

$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
$PAGE->set_url('/mod/basiclti/service.php');
$PAGE->set_pagetype('admin-setting-' . $section);
$PAGE->set_pagelayout('admin');
$PAGE->navigation->clear_cache();

function message_response($major, $severity, $minor=false, $message=false, $xml=false) {
    $lti_message_type = $_REQUEST['lti_message_type'];
    $retval = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"."\n" .
        "<message_response>\n" .
        "  <lti_message_type>$lti_message_type</lti_message_type>\n" .
        "  <statusinfo>\n" .
        "     <codemajor>$major</codemajor>\n" .
        "     <severity>$severity</severity>\n";
    if (! $codeminor === false) {
            $retval = $retval .  "     <codeminor>$minor</codeminor>\n";
    }
    $retval = $retval .
        "     <description>$message</description>\n" .
        "  </statusinfo>\n";
    if (! $xml === false) {
            $retval = $retval . $xml;
    }
    $retval = $retval . "</message_response>\n";
    return $retval;
}

function do_error($message) {
    print message_response('Fail', 'Error', false, $message);
    exit();
}

$lti_version = $_REQUEST['lti_version'];
if ($lti_version != "LTI-1p0") {
    do_error("Improperly formed message: wrong lti version: ".$lti_version);
}

$lti_message_type = $_REQUEST['lti_message_type'];
if (! isset($lti_message_type)) {
     do_error("Improperly formed message: no lti_message_type parameter");
}

$message_type = false;
if ($lti_message_type == "basic-lis-replaceresult" ||
    $lti_message_type == "basic-lis-createresult" ||
    $lti_message_type == "basic-lis-updateresult" ||
    $lti_message_type == "basic-lis-deleteresult" ||
    $lti_message_type == "basic-lis-readresult") {
      $sourcedid = $_REQUEST['sourcedid'];
      $message_type = "basicoutcome";
} else if ($lti_message_type == "basic-lti-loadsetting" ||
    $lti_message_type == "basic-lti-savesetting" ||
    $lti_message_type == "basic-lti-deletesetting") {
      $sourcedid = $_REQUEST['id'];
      $message_type = "toolsetting";
} else if ($lti_message_type == "basic-lis-readmembershipsforcontext") {
      $sourcedid = $_REQUEST['id'];
      $message_type = "roster";
}

if ($message_type == false) {
    do_error("Illegal lti_message_type");
}

if (!isset($sourcedid)) {
    do_error("sourcedid missing");
}
// Truncate to maximum length
$sourcedid = substr($sourcedid, 0, 2048);

try {
    $info = explode(':::', $sourcedid);
    if (! is_array($info)) {
        do_error("Bad sourcedid (1)");
    }
    $signature = $info[0];
    $userid = intval($info[1]);
    $placement = $info[2];
} catch (Exception $e) {
    do_error("Bad sourcedid (2)");
}

if (isset($signature) && isset($userid) && isset($placement)) {
    // OK
} else {
    do_error("Bad sourcedid (3)");
}

// Retrieve the Basic LTI placement
if (! $basiclti = $DB->get_record('basiclti', array('id'=>$placement))) {
    do_error("Bad sourcedid (4)");
}

$basiclti_types_config = (object)$basiclti_types_config;

$typeconfig = basiclti_get_type_config($basiclti->typeid);

if (isset($typeconfig) && isset($typeconfig['password'])) {
    // OK
} else {
    do_error("Unable to load type");
}

if ($message_type == "basicoutcome") {
    if ($typeconfig["acceptgrades"] == 1 ||
         ($typeconfig["acceptgrades"] == 2 && $basiclti->instructorchoiceacceptgrades == 1)) {
        // The placement is configured to accept grades
    } else {
        do_error("Not permitted (1)");
    }
} else if ($message_type == "toolsetting") {
    if ($typeconfig["allowsetting"] == 1 ||
         ($typeconfig["allowsetting"] == 2 && $basiclti->instructorchoiceallowsetting == 1)) {
        // OK
    } else {
        do_error("Not permitted (2)");
    }
} else if ($message_type == "roster") {
    if ($typeconfig["allowroster"] == 1 ||
         ($typeconfig["allowroster"] == 2 && $basiclti->instructorchoiceallowroster == 1)) {
        // OK
    } else {
        do_error("Not permitted (3)");
    }
}

// Retrieve the secret we use to sign lis_result_sourcedid
$placementsecret = $basiclti->placementsecret;
$oldplacementsecret = $basiclti->oldplacementsecret;
if (! isset($placementsecret)) {
    do_error("Not permitted (4)");
}

$suffix = ':::' . $userid . ':::' . $placement;
$plaintext = $placementsecret . $suffix;
$hashsig = hash('sha256', $plaintext, false);
if (($hashsig != $signature) && isset($oldplacementsecret) && (strlen($oldplacementsecret) > 1)) {
    $plaintext = $oldplacementsecret . $suffix;
    $hashsig = hash('sha256', $plaintext, false);
}

if ($hashsig != $signature) {
    do_error("Invalid sourcedid");
}

// Check the OAuth Signature
$oauth_secret = $typeconfig["password"];
$oauth_consumer_key = $typeconfig["resourcekey"];
if (! isset($oauth_secret)) {
    do_error("Not permitted (5)");
}
if (! isset($oauth_consumer_key)) {
    do_error("Not permitted (6)");
}

// Verify the message signature
$store = new TrivialOAuthDataStore();
$store->add_consumer($oauth_consumer_key, $oauth_secret);

$server = new OAuthServer($store);

$method = new OAuthSignatureMethod_HMAC_SHA1();
$server->add_signature_method($method);
$request = OAuthRequest::from_request();

$basestring = $request->get_signature_base_string();
try {
    $server->verify_request($request);
} catch (Exception $e) {
    do_error($e->getMessage());
}

if (! $course = $DB->get_record('course', array('id'=>$basiclti->course))) {
    do_error("Could not retrieve course");
}

// TODO: Check that user is in course

if (! $cm = get_coursemodule_from_instance("basiclti", $basiclti->id, $course->id)) {
    do_error("Course Module ID was incorrect");
}

// Lets store the grade
require_once($CFG->libdir.'/gradelib.php');

// Beginning of actual grade processing
if ($message_type == "basicoutcome") {
    $source = 'mod/basiclti';
    $courseid = $course->id;
    $itemtype = 'mod';
    $itemmodule = 'basiclti';
    $iteminstance =  $basiclti->id;

    if ($lti_message_type == "basic-lis-readresult") {
        unset($grade);
        $thegrade = grade_get_grades($courseid, $itemtype, $itemmodule, $iteminstance, $userid);
        // print_r($thegrade->items[0]->grades);
        if (isset($thegrade) && is_array($thegrade->items[0]->grades)) {
            foreach ($thegrade->items[0]->grades as $agrade) {
                $grade = $agrade->grade;
                break;
            }
        }
        if (! isset($grade)) {
            do_error("Unable to read grade");
        }

        $result = "  <result>\n" .
            "     <resultscore>\n" .
            "        <textstring>" .
            htmlspecialchars($grade/100.0) .
            "</textstring>\n" .
            "     </resultscore>\n" .
            "  </result>\n";
        print message_response('Success', 'Status', false, "Grade read", $result);
        exit();
    }

    if ($lti_message_type == "basic-lis-deleteresult") {
        $params = array();
        $params['itemname'] = $basiclti->name;

        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = null;

        grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, 0, $grade, array('deleted'=>1));
    } else {
        if (isset($_REQUEST['result_resultscore_textstring'])) {
            $gradeval = floatval($_REQUEST['result_resultscore_textstring']);
            if ($gradeval <= 1.0 && $gradeval >= 0.0) {
                $gradeval = $gradeval * 100.0;
            }
        } else {
            do_error('Missing Grade');
        }
        $params = array();
        $params['itemname'] = $basiclti->name;

        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = $gradeval;

        grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, 0, $grade, $params);
    }

    print message_response('Success', 'Status', 'fullsuccess', 'Grade updated');

} else if ($lti_message_type == "basic-lti-loadsetting") {
    $xml = "  <setting>\n" .
           "     <value>".htmlspecialchars($basiclti->setting)."</value>\n" .
           "  </setting>\n";
    print message_response('Success', 'Status', 'fullsuccess', 'Setting retrieved', $xml);
} else if ($lti_message_type == "basic-lti-savesetting") {
    $setting = $_REQUEST['setting'];
    if (! isset($setting)) {
        do_error('Missing setting value');
    }
    $record = $DB->get_record('basiclti', array('id'=>$basiclti->id));
    $record->setting = $setting;
    $success = $DB->update_record('basiclti', $record);
    if ($success) {
        print message_response('Success', 'Status', 'fullsuccess', 'Setting updated');
    } else {
        do_error("Error updating error");
    }
} else if ($lti_message_type == "basic-lti-deletesetting") {
    $record = $DB->get_record('basiclti', array('id'=>$basiclti->id));
    $record->setting = '';
    $success = $DB->update_record('basiclti', $record);
    if ($success) {
        print message_response('Success', 'Status', 'fullsuccess', 'Setting deleted');
    } else {
        do_error("Error updating error");
    }
} else if ($message_type == "roster") {
    if (! $course = $DB->get_record('course', array('id'=>$basiclti->course))) {
        do_error("Could not retrieve course");
    }
    if (! $context = get_context_instance(CONTEXT_COURSE, $course->id)) {
        do_error("Could not retrieve context");
    }
    $sql = 'SELECT u.id, u.username, u.firstname, u.lastname, u.email, ro.shortname
        FROM  '.$CFG->prefix.'role_assignments ra
        JOIN  '.$CFG->prefix.'user AS u ON ra.userid = u.id
        JOIN  '.$CFG->prefix.'role ro ON ra.roleid = ro.id
        WHERE ra.contextid = '.$context->id;
    $userlist = $DB->get_recordset_sql($sql);
    $xml = "  <memberships>\n";
    foreach ($userlist as $user) {
        $role = "Learner";
        if ($user->shortname == 'editingteacher' || $user->shortname == 'admin') {
            $role = 'Instructor';
        }
        $userxml = "    <member>\n".
                   "      <user_id>".htmlspecialchars($user->id)."</user_id>\n".
                   "      <roles>$role</roles>\n";
        if ($typeconfig["sendname"] == 1 ||
             ($typeconfig["sendname"] == 2 && $basiclti->instructorchoicesendname == 1)) {
            if (isset($user->firstname)) {
                $userxml .=  "      <person_name_given>".htmlspecialchars($user->firstname)."</person_name_given>\n";
            }
            if (isset($user->lastname)) {
                $userxml .=  "      <person_name_family>".htmlspecialchars($user->lastname)."</person_name_family>\n";
            }
        }
        if ($typeconfig["sendemailaddr"] == 1 ||
             ($typeconfig["sendemailaddr"] == 2 && $basiclti->instructorchoicesendemailaddr == 1)) {
            if (isset($user->email)) {
                $userxml .=  "      <person_contact_email_primary>".htmlspecialchars($user->email)."</person_contact_email_primary>\n";
            }
        }
        $placementsecret = $basiclti->placementsecret;
        if (isset($placementsecret)) {
            $suffix = ':::' . $user->id . ':::' . $basiclti->id;
            $plaintext = $placementsecret . $suffix;
            $hashsig = hash('sha256', $plaintext, false);
            $sourcedid = $hashsig . $suffix;
        }
        if ($typeconfig["acceptgrades"] == 1 ||
             ($typeconfig["acceptgrades"] == 2 && $basiclti->instructorchoiceacceptgrades == 1)) {
            if (isset($sourcedid)) {
                $userxml .=  "      <lis_result_sourcedid>".htmlspecialchars($sourcedid)."</lis_result_sourcedid>\n";
            }
        }
        $userxml .= "    </member>\n";
        $xml .= $userxml;
    }
    $xml .= "  </memberships>\n";
    print message_response('Success', 'Status', 'fullsuccess', 'Roster retreived', $xml);

}

