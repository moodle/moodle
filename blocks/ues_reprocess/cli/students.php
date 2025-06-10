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
require(__DIR__ . '/../../../config.php');
require_once('workdaystudent.php');

// Get settings.
$s = workdaystudent::get_settings();
$sportfield = $s->sportfield;

// If we want to grab all campuses.
// unset($s->campus);

// Gete the academic units.
// $students = workdaystudent::get_students($s, $periodid = 'LSUAM_FALL_2023', $studentid = '00365772');
$students = workdaystudent::get_students($s, $periodid = 'LSUAM_VETMED_SUMMER_2024', $studentid = '');

// Get and set some counts.
$studentcounter = 0;
$athletecounter = 0;

// Set up the sports array.
$sports = array();

mtrace("Beginning the process of populating the interstitial student db.");
if (is_array($students)) {
    foreach ($students as $student) {

        // Build out the email query.
        $esuffix = $s->campusname . '_Email';

        // GTFO if we don't have a UID or email.
        if (!isset($student->Universal_Id) || !isset($student->$esuffix)) {
            // Set these for logging.
            $uid = isset($student->Universal_Id) ? $student->Universal_Id : "";
            $email = isset($student->$esuffix) ? $student->$esuffix : "";

            // Log that something vital was missing.
            mtrace("\nMissing either UID: $uid or email: $email - $student->First_Name $student->Last_Name.");

            continue;
        }

        // Increment the student counter.
        $studentcounter++;

        // Populate the interstitial DB.
        $stu = workdaystudent::create_update_istudent($s, $student);

        // Populate the student metadata.
        $meta = workdaystudent::delete_insert_studentmeta($s, $stu, $student);
    }
$sportcount = count(array_unique($sports));

$timeend = microtime(true);
$elapsed = round($timeend - $timestart, 2);

mtrace("\nFinished populating the interstitial student db.");
mtrace("It took $elapsed seconds to find $athletecounter total athletes across $sportcount sports among $studentcounter students in $s->campusname.");
}
