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

require(__DIR__ . '/../../../config.php');

global $CFG;

require_once("$CFG->libdir/clilib.php");

// Require the magicness.
require_once('../classes/d1.php');

$courseid = ('22229');
$enrollments = grouper::get_enrolls($courseid);

foreach($enrollments as $enrollment) {

    $student = grouper::get_groupnumber($enrollment);
    $grouper = array();

    echo"D1ID: ";
    print_r($student->getStudentResult->student->objectId);
    echo" - XNumber: ";
    print_r($student->getStudentResult->student->lmsPersonId);
    echo" - username: ";
    print_r($student->getStudentResult->student->loginId);
    echo" - Courseid: $courseid";

    if (isset($student->getStudentResult->student->enrolmentGroups)) {
        $groupnumbers = array();
        if (is_array($student->getStudentResult->student->enrolmentGroups->enrolmentGroup)) {
            foreach($student->getStudentResult->student->enrolmentGroups->enrolmentGroup as $group) {
                $groupnumbers[] = $group->groupNumber;
            }
        } else {
            $groupnumbers[] = $student->getStudentResult->student->enrolmentGroups->enrolmentGroup->groupNumber;
        }
        if (is_array($groupnumbers)) {
            foreach ($groupnumbers as $groupnumber) {
                $groupinfo = grouper::get_groupinfo($groupnumber);
                $groupname = $groupinfo->getGroupResult->group->name;
                $grouper[$groupname] = $groupnumber;
            }
        }

if (isset($grouper["Guild Education"])) {
echo" - Guild Education Group Number: ";
print_r($grouper["Guild Education"]);
}
    }
echo"\n";
}
// Get the token.

class grouper {

    public static function get_moodlers($courseid) {

    }

    public static function get_groupnumber($enrollment) {
        // Get the data needed.
        $s = lsud1::get_d1_settings();

        // Get a token.
        $token = lsud1::get_token();

        // Get the D1 ID for later.
        $d1id = $enrollment->studentId;

        $student = lsud1::get_username($token, $d1id);

        return $student;

    }

    public static function get_enrolls($courseid) {
        global $DB;

        // Set the array up.
        $parms = array('id' => $courseid);

        // Get the course object.
        $course = $DB->get_record('course', $parms, $fields = '*');

        // Get the course name from the idnumber.
        $cn = lsud1::get_coursename($course->idnumber);

        // Get the course section number from the idnumber.
        $cs = lsud1::get_mcoursesection($course->idnumber);

        // Get a token.
        $token = lsud1::get_token();

        $stuff               = new stdClass();
        $stuff->token        = $token;
        $stuff->classname    = $cn;
        $stuff->classsection = $cs;

        // Grab the enrollments based on the given info.
        $enrollments = lsud1::get_section_enrollment($token, $cn, $cs);

        return $enrollments->getClassListResult->studentListItems->studentListItem;
    }

    public static function get_groupinfo($groupnumber) {
        // Get the data needed.
        $s = lsud1::get_d1_settings();

        // Get the token.
        $token = lsud1::get_token();

        // Set the URL.
        $url = $s->wsurl . '/webservice/InternalViewREST/getGroup?_type=json'; 

        // Set the POST body.
        $body = '{"getGroupRequestDetail": {"attributeValue": "' . $groupnumber . '", "matchOn": "groupNumber"}}';

        // Set the POST header.
        $header = array('sessionId: ' . $token, 'Content-Type: application/json');

        // Initiate the CURL handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        // Get the JSON response.
        $json_response = curl_exec($curl);

        // Set the HTTP code for debugging.
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the CURL handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        if ($s->debugging == 1) {
            // Write this out to a file for debugging.
            $fp = fopen($groupnumber . '.json', 'w');
            fwrite($fp, $json_response);
            fclose($fp);
        }

        // Return the response.
        return($response);
   }
}
