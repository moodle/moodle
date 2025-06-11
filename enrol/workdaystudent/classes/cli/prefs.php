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
require_once(__DIR__ . '/../testwds.php');

$mshell = new stdClass();

$mshell->coursesection = "LSUAM_ACCT9000_00079591";
$mshell->period_year = "2025";
$mshell->period_type = "Summer";
$mshell->start_date = "1748322000";
$mshell->end_date = "1754888400";
$mshell->course_subject_abbreviation = "ACCT";
$mshell->course_subject = "Accounting";
$mshell->course_abbreviated_title = "Dissertation Research";
$mshell->course_number = "9000";
$mshell->academic_level = "Graduate";
$mshell->class_type = "Research";
$mshell->universal_id = "00079591";
$mshell->userid = "940501";
$mshell->username = "jlejune@lsu.edu";
$mshell->email = "jlejune@lsu.edu";
$mshell->preferred_firstname = "JJ";
$mshell->firstname = "Jonathan";
$mshell->preferred_lastname = NULL;
$mshell->lastname = "Lejune";
$mshell->delivery_mode = "On Campus";
$mshell->sectionids = "504,525";
$mshell->sections = "001-RES-SM,002-RES-SM";
$mshell->roles = "primary,primary";


$userprefs = workdaystudent::wds_get_faculty_preferences($mshell);
/*
$unwants = workdaystudent::wds_get_unwants($mshell);
$userprefs->unwants = array();
$userprefs->wants = array();

foreach($unwants as $unwant) {

    if ($unwant->unwanted === "1") {
        $userprefs->unwants[] = $unwant->sectionid;
    }

    if ($unwant->unwanted === "0") {
        $userprefs->wants[] = $unwant->sectionid;
    }
}
*/
var_dump($userprefs);
