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

// Include the main Moodle config.
require_once(__DIR__ . '/../../../../config.php');

// Include the Workday Student helper class.
require_once(__DIR__ . '/../workdaystudent.php');

// Set up some timing.
$processstart = microtime(true);

// Get settings.
$s = workdaystudent::get_settings();

// Get the sections.
$periods = workdaystudent::get_current_periods($s);

// Get a count for later.
$numgrabbed = count($periods);

mtrace("Fetched $numgrabbed periods to enroll.");

// Build the unenroll array.
$unenrolls = array();
$unenrolls[] = 'Dropped';
$unenrolls[] = 'Enrollment Cancelled';
$unenrolls[] = 'Enrollment Rescinded';
$unenrolls[] = 'Not Approved';
$unenrolls[] = 'Unregistered';
$unenrolls[] = 'Withdrawn';

// Build the enroll array.
$enrolls = array();
$enrolls[] = 'Enrolled';
$enrolls[] = 'Registered';

// Build the do nothing array.
$donothings = array();
$donothings[] = 'Auto Drop from Waitlist on Enroll';
$donothings[] = 'Completed';
$donothings[] = 'Enrolled - Pending Approval';
$donothings[] = 'Enrolled - Pending Prerequisites';
$donothings[] = 'Promoted';
$donothings[] = 'Waitlist - Closed';
$donothings[] = 'Waitlisted';
$donothings[] = 'Waitlisted - Pending Approval';

foreach ($periods as $period) {
    // Log that we're starting.
    mtrace("\nProcessing enrollments for $period->academic_period_id.");

    // Set some times.
    $periodstart = microtime(true);
    $enrollmentstart = $periodstart;

    // Fetch the actual enrollments for the period.
    $enrollments = workdaystudent::get_period_enrollments($s, $period, null);

    // Set some times.
    $enrollmentend = microtime(true);
    $enrollmentelapsed = round($enrollmentend - $enrollmentstart, 2);

    // Count the number of enrollments.
    $enrollmentcount = count($enrollments);

    // Log how long it took to fetch enrollments.
    mtrace("The webservice took $enrollmentelapsed secdonds to fetch $enrollmentcount enrollments in $period->academic_period_id.");

    // Loop through the enrollments.
    foreach ($enrollments as $enrollment) {
        // Process the enrollment in question.
        $as = workdaystudent::insert_update_student_enrollment($s, $enrollment, $unenrolls, $enrolls, $donothings);
    }

    // Set some times.
    $periodend = microtime(true);
    $periodelapsed = round($periodend - $periodstart, 2);

    // Log how long it took to process the period and how many enrollments were processed.
    mtrace("We took $periodelapsed seconds to process $enrollmentcount enrollments in $period->academic_period_id.");
}

$processend = microtime(true);
$processtime = round($processend - $processstart, 2);
// TODO: DEAL WITH TIMES.
mtrace("Processing $numgrabbed periods took $processtime seconds.");
