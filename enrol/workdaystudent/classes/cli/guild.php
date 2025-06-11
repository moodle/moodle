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
unset($s->campus);

// Gete the courses.
$guilds = workdaystudent::get_guild($s);

/*

object(stdClass)#75 (8) {
  ["Financial_Aid_Period_Record"]=>
  string(49) "Online Fall Semester 2024 (08/26/2024-12/09/2024)"
  ["SFPR_Student"]=>
  string(22) "Keegan Cook (00353771)"
  ["Student_Financials_Period_Record"]=>
  string(74) "Keegan Cook (00353771) - Online Fall Semester 2024 (08/26/2024-12/09/2024)"
  ["Student_Course_Registrations_group"]=>
  array(2) {
    [0]=>
    object(stdClass)#76 (7) {
      ["Course_Number"]=>
      string(5) "7020E"
      ["Section_Listing_ID"]=>
      string(60) "LSUAM_Listing_BADM7020E_001-LEC-FA1_LSUAM_ONLINE_FALL_1_2024"
      ["SCR_Academic_Period"]=>
      string(46) "Online First Fall 2024 (08/26/2024-10/14/2024)"
      ["Course_Section_Abbreviation"]=>
      string(4) "BADM"
      ["Academic_Period_ID"]=>
      string(24) "LSUAM_ONLINE_FALL_1_2024"
      ["Section_Number"]=>
      string(11) "001-LEC-FA1"
      ["Course"]=>
      string(34) "BADM 7020E - Managerial Statistics"
    }
    [1]=>
    object(stdClass)#77 (7) {
      ["Course_Number"]=>
      string(5) "7030E"
      ["Section_Listing_ID"]=>
      string(60) "LSUAM_Listing_BADM7030E_001-LEC-FA1_LSUAM_ONLINE_FALL_1_2024"
      ["SCR_Academic_Period"]=>
      string(46) "Online First Fall 2024 (08/26/2024-10/14/2024)"
      ["Course_Section_Abbreviation"]=>
      string(4) "BADM"
      ["Academic_Period_ID"]=>
      string(24) "LSUAM_ONLINE_FALL_1_2024"
      ["Section_Number"]=>
      string(11) "001-LEC-FA1"
      ["Course"]=>
      string(33) "BADM 7030E - Financial Accounting"
    }
  }
  ["SFPR_Academic_Period"]=>
  string(49) "Online Fall Semester 2024 (08/26/2024-12/09/2024)"
  ["Guild_Contract"]=>
  string(46) "LSUAM | Guild Education | 2024-2025 Award Year"
  ["SFPR_StudentName"]=>
  string(11) "Keegan Cook"
  ["SFPR_UID"]=>
  string(8) "00353771"
}

*/

foreach ($guilds as $guild) {
    $guild = workdaystudent::get_uid_sfpr($guild);
        echo("Student: $guild->SFPR_StudentName - $guild->SFPR_UID\n");
    foreach ($guild->Student_Course_Registrations_group as $registration) {
        echo("  Academic Period: $registration->Academic_Period_ID\n\n");
        echo("  Section Listing ID: $registration->Section_Listing_ID\n");
        echo("  Department: $registration->Course_Section_Abbreviation\n");
        echo("  Course Number: $registration->Course_Number\n");
        echo("  Section: $registration->Section_Number\n");
    }
    echo("\n");
}
