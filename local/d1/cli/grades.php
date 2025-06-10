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
 * @package    local_d1
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

// Require the magicness.
require(__DIR__ . '/../../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once('../classes/d1.php');

// Grab the CLI parms for even/odd/all processing.
$args = ($_SERVER['argv']);

// Set the pattern.
$pattern = isset($args[1]) ? $args[1] : 'all';

// Set the cso var.
$cso = isset($args[2]) ? $args[2] : 1;
$cso = (bool) $cso;

mtrace("Posting $pattern grades to D1.");

// We need this.
global $CFG;

// Set the count of D1 being down.
$downcounter = 0;

// Build a horrific goto.
a:

// Grab the token.
$token = lsupgd1::get_token();

// If we don't get a token.
if (is_int($token)) {
    // Increment the count.
    $downcounter++;

    // Get error codes.
    $codes = lsupgd1::get_codes();

    // Define the error.
    $error = "$downcounter - " . $codes[$token];

    mtrace($error);

    // Wait a full second before trying to get another token.
    sleep(1);

    // Bathe afterward.
    goto a;

// We got a token!
} else {
    mtrace("Token: $token");
}

// Grab the grades from the DB.
$grades = gradeposter::get_grades($pattern, $cso);

// Set the header in case you want to use this as a CSV for some reason.
mtrace("id, x_number, coursenumber, sectionnumber, csobjectid, grade, gradedate, poststatus, reasonmodified, elapsed time");

// Set the second counter.
$counter2 = 0;

// Loop through the grades.
foreach ($grades as $grade) {
    $gradedate = date_create($grade->gradedate);
    $grade->gradedate = date_format($gradedate,"d M Y");

    if ($grade->grade == "Withdrawal" || $grade->grade == "No Show") {
        $dropper = gradeposter::drop_student($token, $grade);
        $updated = gradeposter::update_status($grade->sid, "1", $grade->grade);
        mtrace("Dropped student: $grade->x_number from course $grade->coursenumber - $grade->sectionnumber on date: $grade->gradedate with reason code: $grade->grade.");
        continue;
    }

    // Increment the coutner2.
    $counter2++;

    // Get a new token every 100 rows.
    if ($counter2 % 100 == 0) {
        $token = lsupgd1::get_token();
        mtrace("Got new token: $token.");
    }

    // Set the time.
    $t1 = microtime(true);

    // Get course section objectids for courses.
    if (!$cso) {
        // Get the course section objectid.
        $csobjectid = lsupgd1::get_cs_objectid($grade->coursenumber, $grade->sectionnumber);

        // If we have a course section object id, do some more stuff
        if (is_int($csobjectid)) {
            // Write the course section objectid to the grades DB.
            mtrace("We retreived csobjectid: $csobjectid for $grade->coursenumber - $grade->sectionnumber.");
            $cswritten = gradeposter::write_cs_objectid($grade, $csobjectid);
        }
    } else {
        // Actually build out the CSV.
        print_r($grade->sid);
        echo", ";
        print_r($grade->x_number);
        echo", ";
        print_r($grade->coursenumber);
        echo", ";
        print_r($grade->sectionnumber);
        echo", ";
        print_r($grade->csobjectid);
        echo", ";
        print_r($grade->grade);
        echo", ";
        print_r(!is_null($grade->gradedate) ? $grade->gradedate : null);
        echo", ";

        // We have enough to post the grade to D1 so…do it.
        $posted = lsupgd1::post_update_grade($token, $grade->x_number, $grade->csobjectid, $grade->grade, $grade->gradedate);

        // If the post was successful or not, vary the rest of the data.
        if (isset($posted->createOrUpdateStudentFinalGradeResult->status) && $posted->createOrUpdateStudentFinalGradeResult->status == "OK") {
            $poststatus = "posted to d1";
            echo("1");
            echo", $poststatus, ";

            // Update the grades DB that the grade was posted.
            $updated = gradeposter::update_status($grade->sid, "1", $poststatus);
        } else if (isset($posted->SRSException->errorCode) && $posted->SRSException->message == '[Student already has a final grade]') {
             $poststatus = "posted to d1 again";
            echo("1");
            echo", $poststatus, ";

            // Update the grades DB that the grade was NOT posted.
            $updated = gradeposter::update_status($grade->sid, "1", $poststatus);
        } else {

             $poststatus = $posted->SRSException->message;
            echo("0");
            echo", $poststatus, ";

            // Update the grades DB that the grade was NOT posted.
            $updated = gradeposter::update_status($grade->sid, "0", $poststatus);
        }

        // Set the time.
        $t2 = microtime(true);

        // Calculate and round the elapsed time.
        $elapsed = $t2 - $t1;
        $elapsed = round($elapsed, 2);

        // Let us know in the CSV how long that row took.
        echo($elapsed);
        echo"\n";
    }
}

class gradeposter {

  public static function update_status($sid, $poststatus, $reasonmodified) {
    // Grab the global DB functionality from core moodle.
    global $DB;

    // Set the table to use.
    $table = 'scotty_grades';

    // Get the time.
    $t = time();

    // Time posted is based on if we actually posted or not.
    $timeposted   = $poststatus ? $t : null;

    // Timemodified is a formatted time string.
    $date = date("Y-m-d h:i:s", $t);

    // Build the data object.
    $dataobject = array('id'             => $sid,
                        'poststatus'     => $poststatus,
                        'timeposted'     => $timeposted,
                        'reasonmodified' => $reasonmodified,
                        'timemodified'   => $date
    );

    // Update the table accordinly.
    $update = $DB->update_record($table, $dataobject);

    return $update;

  }

  public static function write_cs_objectid($grade, $csobjectid) {
    // Grab the global DB functionality from core moodle.
    global $DB;

    // Define the update SQL to write the csobjectid for all matching course sections.
    $sql = 'UPDATE mdl_scotty_grades msg
              SET msg.csobjectid = ' . $csobjectid . ',
              msg.reasonmodified = "csobjectid fetched",
              msg.timemodified = CONVERT_TZ(NOW(),"SYSTEM","America/Chicago")
              WHERE msg.coursenumber = "' . $grade->coursenumber . '"
                AND msg.sectionnumber = "' . $grade->sectionnumber . '"';

    // Run the SQL.
    $update = $DB->execute($sql);

    // If this was successful, let us know.
    if ($update) {
        mtrace("Updated scotty_grades csobjectid to $csobjectid for all course sections matching $grade->coursenumber - $grade->sectionnumber.");
    } else {
        mtrace("* Failed to update scotty_grades csobjectid: $csobjectid for all course sections matching $grade->coursenumber - $grade->sectionnumber.");
    }
    return $update;

  }

  public static function get_grades($pattern, $cso) {
    global $DB;
    // Run against odd, even, or all.
    if ($pattern == "odd") {
        $idpat = ' AND id % 2 = 1 ';
    } else if ($pattern == "even") {
        $idpat = ' AND id % 2 = 0 ';
    } else {
        $idpat = '';
    }

    // Check to see if we're grabbing 
    if ($cso == 'true') {
        $cso1 = ' csobjectid,';
        $cso2 = ' AND s.csobjectid IS NOT NULL ';
    } else {
        $cso1 = '';
        $cso2 = ' AND s.csobjectid IS NULL 
            GROUP BY s.coursenumber, s.sectionnumber ';
    }

    // Build the SQL.
    $sql = 'SELECT s.id AS sid,
              x_number,
              coursenumber,
              sectionnumber, '
              . $cso1 . '
              grade,
              gradedate
            FROM mdl_scotty_grades s
            WHERE s.poststatus = 0 '
            . $idpat . $cso2 . '
#            AND reasonmodified <> "[finalGrade value mismatch with section grading sheet template grade type for transcript grade]"
            ORDER BY s.coursenumber ASC,
              s.sectionnumber ASC,
              s.x_number ASC';

    // Get the grades where the above parms a re met.
    $grades = $DB->get_records_sql($sql);

    // Return the grades.
    return $grades;
  }

    public static function get_student_info($token, $user) {
        // Get the data needed.
        $s = lsupgd1::get_d1_settings();

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


    public static function drop_student($token, $grade) {
        // Get the data needed.
        $s = lsupgd1::get_d1_settings();

        // Set the URL.
        $url = $s->wsurl . '/webservice/InternalViewREST/dropStudentFromSection?_type=json';

        // Set the POST body.
        $body = '<dropStudentFromSectionRequestDetail>
                     <attributeValue>' . $grade->x_number . '</attributeValue>
                     <courseNumber>' . $grade->coursenumber . '</courseNumber>
                     <dropReason>' . $grade->grade . '</dropReason>
                     <refundMode>None</refundMode>
                     <dropDate>' . $grade->gradedate . '</dropDate>
                     <matchOn>studentNumber</matchOn>
                     <sectionNumber>' . $grade->sectionnumber . '</sectionNumber>
                 </dropStudentFromSectionRequestDetail>';

        // Set the POST header.
        $header = array(    'Content-Type: application/xml', 'sessionId: ' . $token);

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

}

?>
