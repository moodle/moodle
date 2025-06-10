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

$pattern = ($_SERVER['argv']);
$pattern = isset($pattern[1]) ? $pattern[1] : 'all';

mtrace("Grabbing $pattern emails for updates.");

global $CFG;

require_once("$CFG->libdir/clilib.php");

// Require the magicness.
require_once('../classes/d1.php');
// require_once('d1class.php');
// require_once('courses.php');
// require_once('helpers.php');

// Get the token.
$downcounter = 0;
a:
$token = lsud1::get_token();
if (is_int($token)) {
    $downcounter++;
    $codes = lsud1::get_codes();
    $error = "$downcounter - " . $codes[$token];
    mtrace($error);
    sleep(1);
    goto a;
} else {
    echo("Token: ");
    print_r($token);
    echo("\n");
}

$users = scotty::get_emails($pattern);

echo("Matches, email, loginId, studentNumer, objectId\n");
$counter2 = 0;

foreach ($users as $user) {
    $counter2++;

    if ($counter2 % 100 == 0) {
        $token = lsud1::get_token();
        echo("Got new token: ");
        print_r($token);
        echo("\n");
    }

    $t1 = microtime(true);

    $userinfo = scotty::get_student_info($token, $user);

    $student = $userinfo->student[0];

    $t2 = microtime(true);

    $elapsed = $t2 - $t1;
    $elapsed = round($elapsed, 2);

    if (isset($student->studentNumber)) {
        $counter = 0;
        foreach($userinfo->student as $student) {

            if ($counter < 1) {
                echo"Single Match, ";
            } else {
               echo"Multiple Matches, ";
            }

            print_r($user->id);
            echo", ";

            print_r($user->email);
            echo", ";

            echo(isset($student->loginId) ? $student->loginId : '');
            echo", ";

            echo(isset($student->studentNumber) ? $student->studentNumber : '');
            echo", ";

            echo(isset($student->schoolPersonnelNumber) ? $student->schoolPersonnelNumber : '');
            echo", ";

            echo(isset($student->objectId) ? $student->objectId : '');
            echo", ";

            echo($elapsed);
            echo"\n";

            $counter++;
        }
    } else {
            echo"No Match, ";

            print_r($user->id);
            echo", ";

            print_r($user->email);
            echo", ";

            echo(isset($student->loginId) ? $student->loginId : '');
            echo", ";

            echo(isset($student->studentNumber) ? $student->studentNumber : '');
            echo", ";

            echo(isset($student->schoolPersonnelNumber) ? $student->schoolPersonnelNumber : '');
            echo", ";

            echo(isset($student->objectId) ? $student->objectId : '');
            echo", ";

            echo($elapsed);
            echo"\n";
    }
    $update = scotty::update_scotty($student, $user);
}

class scotty {

  public static function update_scotty($student, $user) {
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
//        mtrace("Updated scotty user id #$user->id with relevant info.");
    }
    return $update;

  }

  public static function get_emails($pattern) {
    global $DB;
    if ($pattern == "odd") {
        $idpat = ' AND id % 2 = 1 ';
    } else if ($pattern == "even") {
        $idpat = ' AND id % 2 = 0 ';
    } else {
        $idpat = '';
    }
    $sql = 'SELECT s.* FROM mdl_scotty_enr s
            WHERE s.updated = 0 '
            . $idpat
            . 'GROUP BY s.email
            ORDER BY s.id';
    $table = 'scotty_enr';
    $parms = null;
    // $users = $DB->get_records($table, $parms);
    $users = $DB->get_records_sql($sql);
    return $users;
  }

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

}

?>
