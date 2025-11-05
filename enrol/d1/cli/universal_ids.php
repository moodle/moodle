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

$users = itrulyhated1::get_d1_students_without_uids();

$t = 0;

foreach ($users as $user) {
    $sinfo = itrulyhated1::get_universal_id($wstoken, $user->email);

    $t++;

    if ($sinfo) {
        itrulyhated1::update_user_uid($user, $sinfo->uid);

        echo ("($t) $user->email : $sinfo->xnumber : $sinfo->uid\n");
    } else {
        echo ("($t) $user->email : $user->idnumber : 'not found'\n");
    }
}

class itrulyhated1 {

    public static function update_user_uid($user, $uid) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/user/profile/lib.php');

        // Set or update the custom profile field value.
        $user->profile_field_universalid = $uid;

        // Save it using the Moodle API.
        \profile_save_data($user);
    }

    public static function get_d1_students_without_uids() {
        global $DB;

        // Get the data needed.
        $s = lsud1::get_d1_settings();

        $parms = [
            'categories' => $s->categories,
            'uidfield1' => $s->d1_uidfield,
            'uidfield2' => $s->d1_uidfield
        ];

        $sql = "SELECT u.*
            FROM {user} u
                INNER JOIN {user_enrolments} ue ON ue.userid = u.id
                INNER JOIN {enrol} e ON e.id = ue.enrolid
                INNER JOIN {course} c ON c.id = e.courseid
                LEFT JOIN mdl_user_info_data uid ON uid.userid = u.id
                    AND uid.fieldid = :uidfield1
                LEFT JOIN mdl_user_info_field uif ON uif.id = uid.fieldid
                    AND uif.id = :uidfield2
            WHERE c.category IN (:categories)
                AND uid.id IS NULL
                AND e.enrol = 'd1'
                AND ue.status = 0
            GROUP BY u.id";

        $users = $DB->get_records_sql($sql, $parms);

        return $users;
  }

  public static function get_universal_id($token, $email) {

    // Get the data needed.
    $s = lsud1::get_d1_settings();

    // Set the URL.
    $url = $s->wsurl . '/webservice/InternalViewREST/searchStudent?informationLevel=Full&_type=json';

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

    // Get the 1 record.
    if (isset($response->student) && is_array($response->student)) {

        $studentinfo = $response->student[0];

        // Build the class.
        $student = new \stdClass();

        if (isset($studentinfo->udfValues->udfValue)) {

            // Loop through all the values.
            foreach ($studentinfo->udfValues->udfValue AS $wdfields) {

                // If we have a uid, get it.
                if ($wdfields->udfFieldSpec->udfFieldName == 'WD3') {
                    if (isset($wdfields->udfFieldValue) && $wdfields->udfFieldValue != '') {

                        // Populate the object.
                        $student->xnumber = $studentinfo->studentNumber;
                        $student->uid = $wdfields->udfFieldValue;

                        // Return the object.
                        return $student;
                    } else {
                        return false;
                    }
                }
            }
        }

    } else {
        return false;
    }

    // Return false.
    return false;
  }
}
