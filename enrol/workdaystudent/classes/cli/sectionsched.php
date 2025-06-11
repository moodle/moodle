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
require_once(__DIR__ . '/../workdaystudent.php');

// Get settings.
$s = workdaystudent::get_settings();

// Get the sections.
$periods = workdaystudent::get_current_periods($s);

$section = new stdClass();
$section->Section_Listing_ID = "LSUAM_Listing_BADM7160_001-LEC-SMB_LSUAM_SUMMER_1_2025";
$section->Meeting_Patterns = "Saturday | 8:00 AM - 5:00 PM; Friday | 1:00 PM - 5:00 PM";

                // If we have section components, add / update the schedule data.
                if (isset($section->Meeting_Patterns)) {

                    // Set this for easier use.
                    $mps = $section->Meeting_Patterns;

                    // Check to see if we have more than one meeting patterns.
                    if (str_contains($mps, ';')) {

                        // Split into two (or more) meeting patterns.
                        $mpsa = array_map('trim', explode(';', $mps));

                    // We do not have more than one meeting pattern.
                    } else {

                        // Return the original string as a single-item array.
                        $mpsa = [trim($input)];
                    }

                    // Loop through the meeting patterns array.
                    foreach ($mpsa as $mp) {

                        // Process the section schedule for this meeting pattern.
                        $schedule = workdaystudent::process_section_schedule($section, $mp);

                        // Add this meeting pattern to the DB.
                        $sectionschedule = workdaystudent::insert_update_section_schedule($schedule);
                    }
                }

