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

// Set start time.
$processstart = microtime(true);

// Get settings.
$s = workdaystudent::get_settings();

$periods = workdaystudent::get_current_periods($s);

$numgrabbed = 0;

foreach($periods as $period) {
if ($period->academic_period_id != "LSUAM_SPRING_2024") {
    continue;
}

     workdaystudent::dtrace("Fetching potential course shells for $period->academic_period_id.");
     $shells = workdaystudent::get_potential_new_basic_shells($period);
     $shellcount = count($shells);
     foreach ($shells as $shell) {
         $cs = workdaystudent::create_update_shell($shell);
//         echo("$shell->period_year $shell->period_type $shell->course_subject_abbreviation $shell->course_number for $shell->firstname $shell->lastname\n");
     }
     workdaystudent::dtrace("Found $shellcount potential course shells for $period->academic_period_id.");
}
$processend = microtime(true);
$processtime = round($processend - $processstart, 2);
mtrace("Processing $numgrabbed sections took $processtime seconds.");
