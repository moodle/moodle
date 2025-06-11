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
 * @package    ues_reprocess
 * @copyright  2024 onwards LSUOnline & Continuing Education
 * @copyright  2024 onwards Robert Russo
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Make sure this can only run via CLI.
define('CLI_SCRIPT', true);

$timestart = microtime(true);

// Include the main Moodle config.
require_once(__DIR__ . '/../../../../config.php');

// Include the Workday Student helper class.
require_once(__DIR__ . '/../workdaystudent.php');

// Get settings.
$s = workdaystudent::get_settings();
$sportfield = $s->sportfield;

// If we want to grab all campuses.
// unset($s->campus);

// Gete the academic units.
$periods = workdaystudent::get_current_periods($s);

// Create the object.
$forcedperiod = new stdClass();
$forcedperiod->academic_period_id = "LSUAM_SPRING_2024";

// Add the object to the array using array_push.
// array_push($periods, $forcedperiod);

// Get and set some counts.
$studentcounter = 0;

// Truncate metadata because it's WAY faster than deleting rows that don't exist and updating existing data.
// $truncated = workdaystudent::truncate_studentmeta();

// Loop through the periods.
foreach ($periods as $period) {

    mtrace("Fetching students in $period->academic_period_id.");
    // Set some things up for future.
    $athletecounter = 0;
    $numathletes = 0;

    // Set the webservice start time.
    $wsstime = microtime(true);

    // Get students.
    $students = workdaystudent::get_students($s, $periodid = $period->academic_period_id, $studentid = '');

    // Set the webservice end time.
    $wsetime = microtime(true);

    // Calculate how long the webservice took to connect and return data.
    $wselapsed = round($wsetime - $wsstime, 2);

    mtrace("Beginning the process of populating the interstitial student db for $period->academic_period_id.");

    // IF we get some data, do some shit.
    if (is_array($students)) {

        // How many students did we get?
        $records = count($students);

        // Set up the sports array.
        $sports = array();
        mtrace("It took $wselapsed seconds to pull $records students in $period->academic_period_id from the webservice.");

        // Loop through the students and insert / update their data.
        foreach ($students as $student) {
var_dump($student);
die();

            // Build out the email address suffix.
            $esuffix = $s->campusname . '_Email';

            // If the default suffix does not exist, look for others.
            if (isset($student->$esuffix)) {
                // We have a default email. Grab it like you want it.
                $email = isset($student->$esuffix) ? $student->$esuffix : null;
            } else {
                workdaystudent::dtrace("We found a non-default or missing email for $student->Universal_Id - $student->First_Name $student->Last_Name.");
                // We do not have a default suffix, build one out based on institution.
                $esuffix = workdaystudent::get_suffix_from_institution($student) . '_Email';
                // Set email accordingly.
                $email = isset($student->$esuffix) ? $student->$esuffix : null;
            }

            // GTFO if we don't have a UID or email.
            if (!isset($student->Universal_Id) || is_null($email)) {
                // Set these for logging.
                $uid = isset($student->Universal_Id) ? $student->Universal_Id : "Missing UID";
                $email = isset($email) && !is_null($email) ? $email : "Missing Email";

                // Log that something vital was missing.
                mtrace("\nMissing either UID: $uid or email: $email - $student->First_Name $student->Last_Name.");

                continue;
            }

            // Increment the student counter.
            $studentcounter++;

            // Populate the interstitial DB.
            $stu = workdaystudent::create_update_istudent($s, $student);

            // Populate the student metadata.
            $meta = workdaystudent::insert_all_studentmeta($s, $stu, $student, $period);

            // Add the reabove response to the number of athletes.
            $numathletes = $numathletes + $meta;
        }

        // Count how many sports we have.
        $sportcount = count(array_unique($sports));

        // Get the end time.
        $timeend = microtime(true);

        // Calculate the elapsed time.
        $elapsed = round($timeend - $timestart, 2);

        mtrace("\nFinished populating the interstitial student db for $period->academic_period_id.");
        mtrace("It took $elapsed seconds to find and process $numathletes athletes across $studentcounter students in $s->campusname for $period->academic_period_id.");
    }
}
