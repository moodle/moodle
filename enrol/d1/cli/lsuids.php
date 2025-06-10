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

/*
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

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/d1.php');

global $CFG;

// Set the start time.
$timestart = microtime(true);

// Get the token.
$token = lsud1::get_token();

// Get this for future use.
$tokentime = microtime(true);

// Grab the students with missing LSUIDs.
$missings = idnumbers::get_missing_lsuids();

// Count the missings.
$mcount = count($missings);

// Implement some counters.
$tcounter = 0;
$mcounter = 0;
$ucounter = 0;

mtrace("Beginning process to update missing idnumbers for $mcount users.");

// Loop through them.
foreach ($missings as $missing) {

    mtrace(" Fetching LSUID for $missing->firstname $missing->lastname."); 

    $stimestart = microtime(true);

    // Increment the counter.
    $tcounter++;

    // Alias this.
    $d1id = $missing->d1id;

    // If our token is older than 300 seconds, get a new one and reset the timer.
    if (microtime(true) - $tokentime > 300) {
        mtrace("Expiring token: $token in courses as course foreach.");
        $token = lsud1::get_token();
        $tokentime = microtime(true);
        mtrace("We fetched a new token.");
    }

    // Get the student record from the webservice.
    $student = idnumbers::get_lsuid($token, $d1id);

    if (!isset($student->getStudentResult)) {
        mtrace("Could not connect to server, exiting until next run");
        die();
    }

    // Alias this.
    if (!isset($student->getStudentResult->student->studentNumber)) {
        $xnum = $missing->idnumber;
    } else {
        $xnum = $student->getStudentResult->student->studentNumber;
    }

    // If we have and LSUID and it's not an empty string, do stuff.
    if (isset($student->getStudentResult->student->schoolPersonnelNumber)
        && $student->getStudentResult->student->schoolPersonnelNumber != '') {

        // Increment the update counter.
        $ucounter++;

        // Alias this.
        $lsuid = $student->getStudentResult->student->schoolPersonnelNumber;
        mtrace("  $ucounter of $mcount - $missing->firstname $missing->lastname's LSUID: $lsuid.");
        // Set it.
        $updatedstu = idnumbers::set_lsuid($missing, $lsuid, $ucounter);
    } else {
        // Increment the counter.
        $mcounter++;
        mtrace("  $mcounter of $mcount - No LSUID found for $missing->firstname $missing->lastname ($d1id - $xnum).");
    }
    $elapsedtime = round(microtime(true) - $stimestart, 2);
    mtrace(" $missing->firstname $missing->lastname took $elapsedtime seconds to process."); 
}

$telapsedtime = round(microtime(true) - $timestart, 2);
mtrace("updated $ucounter out of $mcount students in $telapsedtime seconds."); 

class idnumbers {
    public static function get_missing_lsuids() {
        global $DB;

        // Build the SQL.
        $sql = 'SELECT * FROM {enrol_d1_students} WHERE lsuid NOT LIKE "89%" ORDER BY rand()';

        // Get the missing records.
        $missing = $DB->get_records_sql($sql);

        // Return them.
        return $missing;
    }

    public static function set_lsuid($missing, $lsuid, $ucounter) {
        global $DB;

        // Set the table.
        $table = 'enrol_d1_students';

        // Set this for update.
        $missing->lsuid = $lsuid;

        // Set this for update.
        $missing->timemodified = time();

        // Do the nasty.
        $success = $DB->update_record($table, $missing);

        // Log and return relevant data.
        if ($success) {
            mtrace("  $ucounter - Updated record: $missing->id with lsuid: $lsuid.");
            return $missing;
        } else {
            mtrace("  $ucounter - Failed to update record: $missing->id with lsuid: $lsuid.");
            return false;
        }
    }

    public static function get_lsuid($token, $d1id) {
        // Short circuit things if we don't have a token.
        if ($token == false) {
            return;
        }

        // Get the data needed.
        $s = lsud1::get_d1_settings();

        // Set the URL.
        $url = $s->wsurl . '/webservice/InternalViewRESTV2/student/objectId/' . $d1id . '?_type=json&informationLevel=full';

        // Set the header.
        $header = array('Content-Type: application/json', 'sessionId: ' . $token);

        // Initiate the curl handler.
        $curl = curl_init($url);

        // Se the CURL options.
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // Get the response.
        $json_response = curl_exec($curl);

        // close the curl handler.
        curl_close($curl);

        // Decode the response.
        $response = json_decode($json_response);

        // Return the response.
        return $response;
    }
}

