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

// If we want to grab all campuses.
// unset($s->campus);

// Set the academic period.
$parms = array();
$parms['Academic_Period!Academic_Period_ID'] = 'LSUAM_FALL_2024';

// Gete the courses.
$dates = workdaystudent::get_pg_dates($s, $parms);

var_dump($dates);
die();

foreach($dates as $date) {
$formatteddateobj = workdaystudent::format_pg_date($date);
echo("Type: $formatteddateobj->Date_Control\n");
echo("  Academic Level: $formatteddateobj->Acad_Level\n");
echo("  Date: $formatteddateobj->Date\n\n");
/*

object(stdClass)#79 (10) {
  ["Academic_Calendar"]=>
  string(35) "LSU Baton Rouge - Academic Calendar"
  ["Acad_Level"]=>
  string(13) "Undergraduate"
  ["Date_Control"]=>
  string(11) "Census Date"
  ["Academic_Unit_Level_Configuration"]=>
  string(39) "LSUAM - LSU Baton Rouge / Undergraduate"
  ["Academic_Year"]=>
  string(29) "2024-2025 LSUAM Academic Year"
  ["Institution"]=>
  string(23) "LSUAM - LSU Baton Rouge"
  ["Academic_Period"]=>
  string(42) "Fall Semester 2024 (08/26/2024-12/14/2024)"
  ["Date_Control_ID"]=>
  string(63) "ACADEMIC_PERIOD_CONTROL_DATE-6-7024608a36f61001b77f4b9c40520000"
  ["AcadLevel_ID"]=>
  string(31) "ACADEMIC_UNIT_CONFIGURATION-6-3"
  ["Date"]=>
  string(25) "2024-09-13T00:00:00-05:00"


*/
}
