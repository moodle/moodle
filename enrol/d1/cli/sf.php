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
 * @package    enrol_d1
 * @copyright  2022 onwards LSUOnline & Continuing Education
 * @copyright  2022 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
    **********************************************************
    * This is only a test file and will not be used anywhere *
    **********************************************************
*/

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

// Include the main Moodle config.
require(__DIR__ . '/../../../config.php');

// This is so we can use the CFG var.
global $CFG;

// Grab the CLI parms for even/odd/all processing.
$args = ($_SERVER['argv']);

// Set the pattern.
$pattern = isset($args[1]) ? $args[1] : 'fn';

if ($pattern == "fn") {
    $text = "Updating first names to sync to SalesForce.";
} else if ($pattern == "sf") {
    $text = "Detachiong SalesForce and updating first names to D1.";
} 

mtrace("$text");

// Include the CLI lib so we can do this stuff via CLI.
require_once("$CFG->libdir/clilib.php");

// Require the main D1 class.
require_once('../classes/d1.php');

// Set the webservice token.
$wstoken = lsud1::get_token();

// Get emails for pulling and populating D1 info.
$students = itrulyhated1::get_students($pattern);

$counter = 0;

foreach ($students as $student) {

    // Increment the counter.
    $counter++;

    if($counter % 100 == 0) {
        // Set the webservice token.
        $wstoken = lsud1::get_token();
        mtrace("Fetched new token: $wstoken.");
    }

    // Get the student info from the D1 webservice.
    $returndata = itrulyhated1::set_student_gov($wstoken, $student, $pattern);

    // Update the student info with the returned data.
    $update = itrulyhated1::update_local($student, $returndata, $pattern);
}

class itrulyhated1 {

  public static function set_student_gov($token, $student, $pattern) {
    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/updateStudent?matchOn=studentNumber&_type=json';

    $xnumber = $student->x_number;
    $fn      = $student->firstname;

    // Set the POST body.
    if ($pattern == 'sf') {
        $body = '{"student": {"salesforceObjectId": "UNLINKED_' . $xnumber . '","firstName1": "' . $fn . ' (do not use)","studentNumber": "' . $xnumber . '"}}';
    } else {
        $body = '{"student": {"firstName1": "' . $fn . ' (do not use)","studentNumber": "' . $xnumber . '"}}';
    }

    // Set the POST header.
    $header = array('Content-Type: application/json',
            'sessionId: ' . $token);

    $curl = curl_init($url);

    // Set the CURL options.
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    // Gett the JSON response.
    $json_response = curl_exec($curl);

    // Set the HTTP code for debugging.
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    // Close the CURL handler.
    curl_close($curl);

    // Decode the response.
    $response = json_decode($json_response);

    // Return the response.
    return($response);
  }

  public static function get_sfcontact($token, $student) {

    $curl = curl_init();
    $xnumber = $student->x_number;

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://lsuonlinews.destinyone.moderncampus.net/webservice/InternalViewREST/searchStudent?informationLevel=Full&_type=json',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"studentSearchCriteria": {"studentNumber": "' . $xnumber . '", "searchType": "begin_with"}}',
      CURLOPT_HTTPHEADER => array(
        'sessionId: 45223C245995CC0970270A986A6B53DB',
        'Content-Type: application/json',
        'Cookie: JSESSIONID=' . $token
      ),
    ));

    $json_response = curl_exec($curl);

    curl_close($curl);

    $response = json_decode($json_response);

    return $response;
  }

  public static function get_students($pattern) {
    global $DB;

    if ($pattern == "fn") {
        $where = 'sf.updated = 0';
    } else if ($pattern == "sf") {
        $where = 'sf.updated = 1';
    }
    
    $sql = 'SELECT sf.* FROM mdl_scotty_salesforce sf
            WHERE ' . $where . '
            AND sf.x_number IS NOT NULL
            AND sf.x_number <> ""
            AND rule = "Remove"
            ORDER BY sf.id ASC';

    $students = $DB->get_records_sql($sql);

    $count = count($students);

    mtrace("Retuned $count students.");

    return $students;

  }

  public static function update_local($student, $returndata, $pattern) {
    global $DB;
    $table = 'scotty_salesforce';

    if (isset($returndata->updateStudentResult)) {
        $success = $returndata->updateStudentResult->responseCode == "Success" ? 'sf.updated + 1' : 'sf.updated';
        $status  = $returndata->updateStudentResult->responseCode;
        mtrace("Updated student: $student->x_number salesforce id with code: $status.");
    } else {
        $success = 'sf.updated';
        $status  = $returndata->SRSException->message;
        mtrace("Failed to update student: $student->x_number salesforce info with code: $status.");
    }

    if ($pattern == 'sf') {
        $sfcontact = "";
    } else {
        $token = lsud1::get_token();
        $sf = self::get_sfcontact($token, $student);
        $sfdata = $sf->student[0]->salesforceObjectId;
        $sfcontact = "sf.sfcontact = '$sfdata',";
    }

    $sql = "UPDATE mdl_scotty_salesforce sf
            SET sf.updated = $success,
            sf.status = '$status',
            " . $sfcontact . "
            updatedate = UNIX_TIMESTAMP()
            WHERE sf.id = $student->id";

//    $update = $DB->update_record($table, $dataobject);
      $update = $DB->execute($sql);

    if ($update) {
        mtrace("    Updated $student->x_number with relevant salesforce info.");
    }
    return $update;
  }

  public static function update_sf($student, $returndata) {
    global $DB;
    $table = 'scotty_salesforce';

    if (isset($returndata->updateStudentResult)) {
        $success = $returndata->updateStudentResult->responseCode == "Success" ? 1 : 0;
        $status  = $returndata->updateStudentResult->responseCode;
        mtrace("Updated student: $student->x_number salesforce contact id with code: $status.");
    } else {
        $success = 0;
        $status  = $returndata->SRSException->message;
        mtrace("Failed to update student: $student->x_number salesforce contact id with code: $status.");
    }

    $dataobject = array('id'         => $student->id,
                        'updated'    => $success,
                        'status'     => $status,
                        'updatedate' => time()
    );

    $update = $DB->update_record($table, $dataobject);

    if ($update) {
        mtrace("    Updated $student->x_number with relevant salesforce info.");
    }

    return $update;

  }
}
