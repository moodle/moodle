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
require(__DIR__ . '/../../../config.php');
require_once('workdaystudent.php');

// Get settings.
$s = workdaystudent::get_settings();

// If we want to grab all campuses.
// unset($s->campus);

$parms = array();
$parms['Academic_Period!Academic_Period_ID'] = 'LSUAM_VETMED_SUMMER_2024';

// Set up some timing.
$grabstart = microtime(true);

// Get the sections.
$sections = workdaystudent::get_sections($s, $parms);

// Time how long grabbing the data from teh WS took.
$grabend = microtime(true);
$grabtime = round($grabend - $grabstart, 2);
$numgrabbed = count($sections);
mtrace("Fetched $numgrabbed sections in $grabtime seconds.");

// Set up some timing.
$processstart = microtime(true);

/*
foreach ($sections as $section) {
    if (isset($section->Instructor_Info)) {
        $cc = count($section->Instructor_Info);
        mtrace("\n$cc - $section->Section_Listing_ID");
        var_dump($section->Instructor_Info);
    }
}
die();
*/

foreach ($sections as $section) {

    $sec = workdaystudent::insert_update_section($section);

    if (!isset($section->Instructor_Info)) {
        mtrace(" - No instructors in $section->Section_Listing_ID.");
        $enrollment = workdaystudent::insert_update_teacher_enrollment($section->Section_Listing_ID, $tid = null, $role = null, 'unenroll');
    } else if (count($section->Instructor_Info) > 1) {
        mtrace(" - More than 1 instructor in $section->Section_Listing_ID.");
        foreach ($section->Instructor_Info as $teacher) {
            $secid = $section->Section_Listing_ID;
            $tid = $teacher->Instructor_ID;
            $pmi = isset($section->PMI_Universal_ID) ? $section->PMI_Universal_ID : null;
            $status = 'enroll';

            if (!is_null($pmi)) {
                $role = $tid == $pmi ? 'primary' : 'teacher';
                $tid = $tid == $pmi ? $pmi : $tid;
                mtrace("Primary instructor $pmi found!");
            } else {
                mtrace("More than one instructor in $secid and $tid is non-primary.");
                $role = 'teacher';
            }

            $iteacher = workdaystudent::create_update_iteacher($s, $teacher);

            $enrollment = workdaystudent::insert_update_teacher_enrollment($secid, $tid, $role, $status);
        }
    } else {
        $teacher = $section->Instructor_Info[0];
        $secid = $section->Section_Listing_ID;
        $tid = $teacher->Instructor_ID;
        $pmi = isset($section->PMI_Universal_ID) ? $section->PMI_Universal_ID : null;
        $status = 'enroll';

        if (!is_null($pmi)) {
            $role = $tid == $pmi ? 'primary' : 'teacher';
            $tid = $tid == $pmi ? $pmi : $tid;
            mtrace("Primary instructor $pmi found!");
        } else {
            mtrace("Sole instructor in $secid and $tid is non-primary.");
            $role = 'teacher';
        }

        $iteacher = workdaystudent::create_update_iteacher($s, $teacher);

        $enrollment = workdaystudent::insert_update_teacher_enrollment($secid, $tid, $role, $status);
    }
}

$processend = microtime(true);
$processtime = round($processend - $processstart, 2);
mtrace("Processing $numgrabbed sections took $processtime seconds.");
