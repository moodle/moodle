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
 * Strings for component 'gradereport_forecast', language 'en'
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['eventgradereportviewed'] = 'Grade forecast report viewed';
$string['pluginname'] = 'Forecast report';
$string['forecast:view'] = 'View your own grade report';
$string['tablesummary'] = 'The table is arranged as a list of graded items including categories of graded items. When items are in a category they will be indicated as such.';

// settings
$string['enabled_for_students'] = 'Enable for students';
$string['enabled_for_students_desc'] = 'Enable students to view this report?';
$string['must_make_enabled'] = 'Must Make table enabled';
$string['must_make_enabled_desc'] = 'Enable the report to display a "must make" pop-up when only one grade input is missing from calculation';
$string['must_make_modal_heading'] = 'Projected Grades';
$string['must_make_modal_letter_column_heading'] = 'Final Letter Grade';
$string['must_make_modal_grade_column_heading'] = 'Assignment Score Needed';
$string['nopermissiontouseforecast'] = 'Projected Final Grade has been disabled for this course by your instructor.';
$string['debounce_wait_time'] = 'Debounce wait time';
$string['debounce_wait_time_desc'] = 'Number of milliseconds the system will wait on additional user input before calculating grades. Set this higher to reduce the number of requests coming in to the server.';