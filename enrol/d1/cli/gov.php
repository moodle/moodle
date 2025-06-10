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

// Include the CLI lib so we can do this stuff via CLI.
require_once("$CFG->libdir/clilib.php");

// Require the main D1 class.
require_once('../classes/d1.php');

// Set the webservice token.
$wstoken = lsud1::get_token();

// Get emails for pulling and populating D1 info.
$students = itrulyhated1::get_students();

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
    $returndata = itrulyhated1::set_student_gov($wstoken, $student);

    // Update the student info with the returned data.
    $update = itrulyhated1::update_local($student, $returndata);
}

class itrulyhated1 {

  public static function set_student_gov($token, $student) {
    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/updateStudent?matchOn=studentNumber&_type=json';

    $xnumber = $student->x_number;
    $gov     = $student->gov;

    // Set the POST body.

    $body = '{"student": {"socialSecurityNum": "' . $gov . '","studentNumber": "' . $xnumber . '"}}';

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

  public static function get_students() {
    global $DB;
    $sql = 'SELECT g.* FROM mdl_scotty_gov g
            WHERE g.updated = 0
            AND g.x_number IS NOT NULL
            ORDER BY g.id ASC';

    $students = $DB->get_records_sql($sql);
    $count = count($students);

    mtrace("Retuned $count students.");

    return $students;

  }

  public static function update_local($student, $returndata) {
    global $DB;
    $table = 'scotty_gov';

    if (isset($returndata->updateStudentResult)) {
        $success = $returndata->updateStudentResult->responseCode == "Success" ? 1 : 0;
        $status  = $returndata->updateStudentResult->responseCode;
        mtrace("Updated student: $student->x_number gov id with code: $status.");
    } else {
        $success = 0;
        $status  = $returndata->SRSException->message;
        mtrace("Failed to update student: $student->x_number gov id with code: $status.");
    }

    $dataobject = array('id'         => $student->id,
                        'updated'    => $success,
                        'status'     => $status,
                        'updatedate' => time()
    );

    $update = $DB->update_record($table, $dataobject);

    if ($update) {
        mtrace("    Updated $student->x_number with relevant government info.");
    }

    return $update;

  }
}
