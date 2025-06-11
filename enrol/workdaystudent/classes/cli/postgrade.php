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

// Get settings.
$s = workdaystudent::get_settings();

// Set the start time.
$starttime = microtime(true);

// Set some stuff.
$sectionlistingid = 'LSUAM_Listing_SPAN2101_001-LEC-FA_LSUAM_FALL_2024';

$gradetype = "interim";

$grades = array();

$grade22 = new stdClass();
$grade22->section_listing_id = 'LSUAM_Listing_SPAN2101_001-LEC-FA_LSUAM_FALL_2024';
$grade22->universal_id = '00080142';
$grade22->grade_id = 'LSUAM_A_PLUS_UNDERGRADUATE';

$grades[] = $grade22;

$grade21 = new stdClass();
$grade21->section_listing_id = 'LSUAM_Listing_SPAN2101_001-LEC-FA_LSUAM_FALL_2024';
$grade21->universal_id = '00265760';
$grade21->grade_id = 'LSUAM_B_PLUS_UNDERGRADUATE';

$grades[] = $grade21;

$grade20 = new stdClass();
$grade20->section_listing_id = 'LSUAM_Listing_SPAN2101_001-LEC-FA_LSUAM_FALL_2024';
$grade20->universal_id = '00180378';
$grade20->grade_id = 'LSUAM_C_PLUS_UNDERGRADUATE';

$grades[] = $grade20;

$grade19 = new stdClass();
$grade19->section_listing_id = 'LSUAM_Listing_SPAN2101_001-LEC-FA_LSUAM_FALL_2024';
$grade19->universal_id = '00204979';
$grade19->grade_id = 'LSUAM_D_PLUS_UNDERGRADUATE';

$grades[] = $grade19;

/*
$grade18 = new stdClass();
$grade18->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade18->universal_id = '00207201';
$grade18->grade_id = 'LSUAM_C_PLUS_UNDERGRADUATE';

$grades[] = $grade18;

$grade17 = new stdClass();
$grade17->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade17->universal_id = '00405552';
$grade17->grade_id = 'LSUAM_C_MINUS_UNDERGRADUATE';

$grades[] = $grade17;

$grade16 = new stdClass();
$grade16->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade16->universal_id = '00197330';
$grade16->grade_id = 'LSUAM_D_PLUS_UNDERGRADUATE';

$grades[] = $grade16;

$grade15 = new stdClass();
$grade15->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade15->universal_id = '00200961';
$grade15->grade_id = 'LSUAM_C_UNDERGRADUATE';

$grades[] = $grade15;

$grade14 = new stdClass();
$grade14->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade14->universal_id = '00069150';
$grade14->grade_id = 'LSUAM_C_UNDERGRADUATE';

$grades[] = $grade14;

$grade13 = new stdClass();
$grade13->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade13->universal_id = '00217628';
$grade13->grade_id = 'LSUAM_C_UNDERGRADUATE';

$grades[] = $grade13;

$grade12 = new stdClass();
$grade12->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade12->universal_id = '00264924';
$grade12->grade_id = 'LSUAM_C_UNDERGRADUATE';

$grades[] = $grade12;

$grade11 = new stdClass();
$grade11->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade11->universal_id = '00080834';
$grade11->grade_id = 'LSUAM_A_UNDERGRADUATE';

$grades[] = $grade11;

$grade10 = new stdClass();
$grade10->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade10->universal_id = '00294890';
$grade10->grade_id = 'LSUAM_B_UNDERGRADUATE';

$grades[] = $grade10;

$grade9 = new stdClass();
$grade9->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade9->universal_id = '00143159';
$grade9->grade_id = 'LSUAM_C_PLUS_UNDERGRADUATE';

$grades[] = $grade9;

$grade8 = new stdClass();
$grade8->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade8->universal_id = '00230982';
$grade8->grade_id = 'LSUAM_A_MINUS_UNDERGRADUATE';

$grades[] = $grade8;

$grade7 = new stdClass();
$grade7->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade7->universal_id = '00079002';
$grade7->grade_id = 'LSUAM_D_PLUS_UNDERGRADUATE';

$grades[] = $grade7;

$grade6 = new stdClass();
$grade6->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade6->universal_id = '00263689';
$grade6->grade_id = 'LSUAM_A_PLUS_UNDERGRADUATE';

$grades[] = $grade6;

$grade5 = new stdClass();
$grade5->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade5->universal_id = '00076859';
$grade5->grade_id = 'LSUAM_A_PLUS_UNDERGRADUATE';

$grades[] = $grade5;

$grade4 = new stdClass();
$grade4->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade4->universal_id = '00223951';
$grade4->grade_id = 'LSUAM_C_PLUS_UNDERGRADUATE';

$grades[] = $grade4;

$grade3 = new stdClass();

$grade3->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade3->universal_id = '00074297';
$grade3->grade_id = 'LSUAM_B_MINUS_UNDERGRADUATE';

$grades[] = $grade3;

$grade2 = new stdClass();

$grade2->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade2->universal_id = '00234869';
$grade2->grade_id = 'LSUAM_A_MINUS_UNDERGRADUATE';

$grades[] = $grade2;

$grade1 = new stdClass();

$grade1->section_listing_id = 'LSUAM_Listing_THTR1020_001-LEC-FA_LSUAM_FALL_2024';
$grade1->universal_id = '00260743';
$grade1->grade_id = 'LSUAM_A_PLUS_UNDERGRADUATE';

$grades[] = $grade1;
*/

// Initial post grades and grab resulting data.
$data = workdaystudent::post_grade($s, $grades, $gradetype, $sectionlistingid);

// Short circuit shit.
if ($data == "error") {
    return;
}

// If we get a response, deal with it.
if (!empty($data)) {

    if (is_object($data)) {
        // Get and parse any errors that may exist for later.
        $errors = workdaystudent::parseerrors($data->xmlstring);

        // Load the XML string into SimpleXML
        $xmltp = simplexml_load_string($data->xmlstring);

        // XPath query to get the faultcode using the correct namespace
        $xpath = $xmltp->xpath('//SOAP-ENV:Fault/faultstring');

        // If a faultcode was found, output it
        if ($xpath) {
            mtrace("Status Code: $data->error - Reason: " . $xpath[0]);
        } else {
            // Get and parse any errors that may exist for later.
            $errors = workdaystudent::parseerrors($data);

            echo "Faultcode not found!";
        }
    }

    if (!empty($errors)) {
        // Set up the failures array for future use.
        $failures = array();

        // Loop through the errors.
        foreach ($errors as $error) {
            $errindex = $error->index;

            if (is_numeric($errindex)) {
                // Build the new object.
                $stugrade = new stdClass();

                // Set the object to the corresponding grades entry.
                $stugrade = $grades[$errindex - 1];
             
                // Add the retreived error message.
                $stugrade->errormessage = $error->message;

                // Add the object to the failrures array.
                $failures[] = $stugrade;

                // Remove the corresponding grades entry.
                unset($grades[$errindex - 1]);
            } else {
                $cc = workdaystudent::pg_section_status($data->xmlstring);
                if ($cc) {
                    mtrace("All students have grades for the course $sectionlistingid.");
                }
            }
        }
    } else {
        // Set the results for later.
        $results = $grades;
    }

    // If we had failures, post the remaining grades.
    if (isset($failures)) {

        // Deal with the remaining grades.
        if (!empty($grades)) {
            // Repost the remaining grade items.
            $data2 = workdaystudent::post_grade($s, $grades, $gradetype, $sectionlistingid);

            // For sanity's sake, parse any remaining errors, this should always be empty.
            $errors2 = workdaystudent::parseerrors($data2);
        }

        // Merge the failures and reamaining posted grades.
        $results = isset($failures) ? array_merge($failures, $grades) : $grades;
    }

    // Merge the failures and reamaining posted grades.
    $results = isset($failures) ? array_merge($failures, $grades) : $grades;
} else {
    $results = $grades;
}

mtrace("Posting grades for $sectionlistingid.");

// If we don't have any errors, do stuff.
if (isset($results) && (!isset($errors2) || empty($errors2))) {
    // Loop through the data.
    foreach ($results as $result) {
        if (isset($result->errormessage)) {
            // If we have an error, return it.
            mtrace("  $result->universal_id - $result->errormessage");
        } else {
            $today = date('Y-m-d');
            // Otherwise assume we're good.
            mtrace("  $result->universal_id - $result->grade_id Grade successfully posted for $result->section_listing_id on $today.");
        }
    }
// OMG we still are having issues, which should be impossible. Freak out.
} else {
    mtrace("This should never happen: ");
    var_dump($errors2);
}

// Build out the time it took to run.
$elapsedtime = round(microtime(true) - $starttime, 2);

mtrace("Finished posting " . count($results) . " grades for $sectionlistingid in $elapsedtime seconds.");
