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
$users = itrulyhated1::get_emails();

$counter = 0;

foreach ($users as $user) {

    // Increment the counter.
    $counter++;

    if($counter % 100 == 0) {
        // Set the webservice token.
        $wstoken = lsud1::get_token();
        mtrace("Fetched new token: $wstoken.");
    }

    // Get the student info from the D1 webservice.
    $userinfo = itrulyhated1::get_student_info($wstoken, $user);

      if (isset($userinfo->student[0])) {
        // Grab the 1st user returned as it's an array of one.
        $student = $userinfo->student[0];

        // Update the user info with the returned data.
        $update = itrulyhated1::fast_update_local($student, $user);

     }

}

class itrulyhated1 {

  public static function get_student_info($token, $user) {
    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/searchStudent?informationLevel=Full&_type=json';

    $email = $user->email;

    // Set the POST body.
    $body = '{"studentSearchCriteria": {"email": "' . $email . '", "emailIsPreferred": "Y", "searchType": "begin_with"}}';

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

  public static function get_emails() {
    global $DB;
    $sql = 'SELECT s.* FROM mdl_scotty_enr s
            WHERE s.updated = 0
            # AND s.logonid IS NULL
            GROUP BY s.email
            ORDER BY s.id DESC';
    $table = 'scotty_enr';
    $users = $DB->get_records_sql($sql);

    $count = count($users);

    mtrace("Fetched $count users.");

    return $users;
  }

  public static function update_local($student, $user) {
    global $DB;
    $table = 'scotty_enr';

    $dataobject = array('id'         => $user->id,
                        'logonid'    => isset($student->loginId) ? $student->loginId : null,
                        'x_number'   => isset($student->studentNumber) ? $student->studentNumber : null,
                        'd1_id'      => isset($student->objectId) ? $student->objectId : null,
                        'lsuid'      => isset($student->schoolPersonnelNumber) ? $student->schoolPersonnelNumber : null,
                        'updated'    => 1,
                        'updatedate' => time()
    );

    $update = $DB->update_record($table, $dataobject);
    if ($update) {
        mtrace("    Updated $user->email with relevant info.");
    }
    return $update;

  }

  public static function fast_update_local($student, $user) {
    global $DB;

    $sspn = isset($student->schoolPersonnelNumber) ? $student->schoolPersonnelNumber : '';

    if (substr($user->lsuid, 0, 2) == 89) {
        $lsuid  = 's.lsuid = "' . $user->lsuid . '"';
    } else if(substr($sspn, 0, 2) == 89) {
        $lsuid  = 's.lsuid = "' . $student->schoolPersonnelNumber . '"';
    } else {
        $lsuid = 's.lsuid = null';
    }

    $logonid = isset($student->loginId) ?  $student->loginId : null;
    $xnumber = isset($student->studentNumber) ?  $student->studentNumber : null;
    $d1id    = isset($student->objectId) ?  $student->objectId : null;

    $sql = 'UPDATE mdl_scotty_enr s
            SET s.logonid = "' . $logonid . '",
                s.x_number = "' . $xnumber . '",
                s.d1_id = "' . $d1id . '",
                ' . $lsuid . ',
                s.updated = 1,
                s.updatedate = UNIX_TIMESTAMP()
            WHERE s.email = "' . $user->email . '"';

    $update = $DB->execute($sql);
    if ($update) {
        mtrace("    Updated $user->email with relevant info.");
    }
    return $update;

  }

}
