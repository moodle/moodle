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

require_once('../../../config.php');
require_once('lib.php');
require_once('grade_import_form.php');
require_once('lang/en/gradeimport_kioskpearsonjson.php');
require_once('../../../lib/oauthlib.php');

$id = required_param('id', PARAM_INT); // course id

$PAGE->set_url(new moodle_url('/grade/import/kioskpearsonjson/index.php', array('id'=>$id)));
$PAGE->set_pagelayout('admin');



if (!$course = $DB->get_record('course', array('id'=>$id))) {
	print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($id);
require_capability('moodle/grade:import', $context);
require_capability('gradeimport/kioskpearsonjson:view', $context);

// print header
$strgrades = get_string('grades', 'grades');
$actionstr = get_string('pluginname', 'gradeimport_kioskpearsonjson');

if (!empty($CFG->gradepublishing)) {
	$CFG->gradepublishing = has_capability('gradeimport/kioskpearsonjson:publish', $context);
}



if ((!isset($CFG->mylabmastering_grade_sync_url) || trim($CFG->mylabmastering_grade_sync_url)==='')) {
	//Show error that Pearson Grade Sync URL is not set
	print_grade_page_head($COURSE->id, 'import', 'kioskpearsonjson', get_string('pagetitle', 'gradeimport_kioskpearsonjson'));
	echo 'We are unable to sync grades due to a configuration issue.  Please contact your Moodle administrator to have them verify the Pearson block is configured correctly. <a href="http://www.pearsonhighered.com/mlm/lms-help-for-educators/" target="_blank">More information</a>';
	echo $OUTPUT->footer();
}

$mform = new grade_import_form();

if ($data = $mform->get_data()) {
	print_grade_page_head($COURSE->id, 'import', 'kioskpearsonjson', get_string('pagetitle', 'gradeimport_kioskpearsonjson'));

	$gradeURL = $CFG->mylabmastering_grade_sync_url . '/items/' . $COURSE->id;

	// The data to send to the API
	$postData = array(
		'nothing' => 'at this time',
		'important' => 'at this time'
	);

	$oauthData = array(
		'oauth_consumer_key' => $CFG->mylabmastering_key,
		'oauth_nonce' => '5141207439707735494',
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_timestamp' => '1345817820',
		'oauth_token' => 'tpi',
		'oauth_version' => '1.0'
	);

        // BEGIN LSU helper instantiation fix.
        $oinstance = new oauth_helper($oauthData);

	$sig_array = array(
		'POST',
		preg_replace('/%7E/', '~', rawurlencode($gradeURL)),
		rawurlencode($oinstance->get_signable_parameters($oauthData)),
	);
        // END LSU helper instantiation fix.

	$base_string = implode('&', $sig_array);
	$secret = $CFG->mylabmastering_secret . '&';

	$base_string = implode('&', $sig_array);

	$sig = base64_encode(hash_hmac('sha1', $base_string, $secret, true));


	// Setup cURL
	$authHeader = 'Authorization: ' .
		'OAuth realm="http%3A%2F%2Ftpidev.pearsoncmg.com%2Fapi%2Ftools%2Fprofiles%2Fsearch", ' .
		'oauth_consumer_key="'. $CFG->mylabmastering_key .'", ' .
		'oauth_nonce="5141207439707735494", '.
		'oauth_signature="' . rawurlencode($sig) . '", '.
		'oauth_signature_method="HMAC-SHA1", ' .
		'oauth_timestamp="1345817820", ' .
		'oauth_token="tpi", ' .
		'oauth_version="1.0"';

	$contentTypeHeader = 'Content-Type: application/json';

	$c = new curl(array('cache'=>false));

	$c_opts = array(
		'CURLOPT_RETURNTRANSFER' => true,
		'CURLOPT_SSL_SSL_VERIFYPEER' => false
	);

	$c->setHeader(array($authHeader, $contentTypeHeader));

	$pearsonResponse = $c->post($gradeURL, json_encode($postData));

	if (empty($pearsonResponse)) {
		// some kind of an error happened
		//echo curl_error($ch);
		echo($c->error);
	} else {
		//grab the info from the request
		//$info = curl_getinfo($ch);

		if ($c->info['http_code'] == '200') {
			$items = json_decode($pearsonResponse);

			$error = '';
			$resultStats = import_kioskpearsonjson_grades($items, $course, $error);
			echo 'Congratulations! You successfully synced your Pearson grades. Click your Grader Report to see your Pearson assignments and student scores.';
			echo '<ul>';
			echo '<li>Status of sync: Success!</li>';
			echo '<li>Number of grades created: '. $resultStats->numGradesCreated . '</li>';
			echo '<li>Number of grades updated: '. $resultStats->numGradesUpdated . '</li>';
			echo '<li>Number of items created: '. $resultStats->numItemsCreated . '</li>';
			echo '<li>Number of items updated: '. $resultStats->numItemsUpdated . '</li>';
			echo '<li>Number of locked grades: '. $resultStats->numLockedGrades . '</li>';
			echo '</ul>';
		}
		else if ($c->info['http_code'] == '404') {
			echo 'We could not find any Pearson assignments to sync with your course.<br/><br/>';
			echo 'Please check your Pearson course help to make sure you have set up your Pearson course settings to sync grades.<br/><br/>';
			echo 'Your Pearson course may not support syncing grades with Moodle, click <a href="http://247pearsoned.custhelp.com/app/answers/detail/a_id/12108"  target="_blank">here</a> for more information.<br/><br/>';
			echo 'Please contact Pearson’s 24/7 <a href="http://247pearsoned.custhelp.com/"  target="_blank">Help Desk</a> if you continue to receive this error.';
		}
		//if something OTHER then success happens
		else {
			if (empty($c->info['http_code'])) {
				die("No HTTP code was returned");
			}
			else {
				// load the HTTP codes
				$http_codes = parse_ini_file("httpcodes.ini");

				// echo results
				echo "The server responded: <br />";
				echo $c->info['http_code'] . " " . $http_codes[$c->info['http_code']];
				echo "<br/><br/>";
				echo "The system is unable to sync grades due to a server issue. Please try again later.  If the issue continues, please contact Pearson’s 24/7 <a href=\"http://247pearsoned.custhelp.com/\">Help Desk</a>.";
			}
		}
	}

	echo $OUTPUT->footer();

	//curl_close($ch);
	die;
}

print_grade_page_head($COURSE->id, 'import', 'kioskpearsonjson', get_string('pagetitle', 'gradeimport_kioskpearsonjson'));

echo 'Click the ‘Sync MyLab & Mastering Grades’ button below to add assignments and grades from your Pearson MyLab & Mastering gradebook to your Moodle grades.';

$mform->display();

echo $OUTPUT->footer();


