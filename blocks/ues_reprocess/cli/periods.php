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

// Gete the academic units.
$lunits = workdaystudent::get_local_units($s);

// Fetch 
$endpoint = 'periods';

$parms = workdaystudent::get_dates();

foreach($lunits as $unit) {
    if ($unit->academic_unit_subtype == "Institution") {

        // Set the parms.
        $parms['Institution!Academic_Unit_ID'] = $s->campus;
        $parms['format'] = 'json';

        // Build the url into settings.
        $s = workdaystudent::buildout_settings($s, $endpoint, $parms);

        // Get the academic periods.
        $periods = workdaystudent::get_data($s);

        foreach ($periods as $period) {
            // Check to see if we have a matching period.
            $ap = workdaystudent::insert_update_period($s, $period);
        }
    }
}
