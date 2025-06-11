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

        // Begin processing academic periods.
        mtrace(" Begin processing academic periods for institutional units.");

        // Get settings. We ahve to do this several times as I overload them.
        $s = workdaystudent::get_settings();

        // Set the period processing start time.
        $periodstart = microtime(true);

        // Get the local academic units.
        $lunits = workdaystudent::get_local_units($s);

        // Set up the date parms.
        $parms = workdaystudent::get_dates();

        // Set these up for later.
        $numperiods = 0;
        $totalperiods = 0;

        // Loop through all the the  units.
        foreach($lunits as $unit) {

            mtrace("  Begin processing periods for $unit->academic_unit_code - " .
                "$unit->academic_unit_id: $unit->academic_unit.");

            // In case something stupid happens, only process institutional units.
            if ($unit->academic_unit_subtype == "Institution") {

                // Add the relavent options to the date parms.
                $parms['Institution!Academic_Unit_ID'] = $s->campus;
                $parms['format'] = 'json';

                // Build the url into settings.
                $s = workdaystudent::buildout_settings($s, "periods", $parms);

                // Get the academic periods.
                $periods = workdaystudent::get_data($s);

                $numperiods = count($periods);
                $totalperiods = $totalperiods + $numperiods;

                foreach ($periods as $period) {
                    workdaystudent::dtrace("   Processing $period->Academic_Period_ID: " .
                        "$period->Name for $unit->academic_unit_id: $unit->academic_unit.");

                    // Get ancillary dates for census and post grades.
                    $pdates = workdaystudent::get_period_dates($s, $period);


                    // Check to see if we have a matching period.
                    $ap = workdaystudent::insert_update_period($s, $period);

                    foreach ($pdates as $pdate) {
                        // Set the academic period id to the pdate.
                        $pdate->academic_period_id = $period->Academic_Period_ID;
                        // Check to see if we have a matching period date entry.
                        $date = workdaystudent::insert_update_period_date($s, $pdate);
                    }
                    workdaystudent::dtrace("   Finished processing $period->Academic_Period_ID: " .
                        "$period->Name for $unit->academic_unit_id: $unit->academic_unit.");
                }
            }

            if ($CFG->debugdisplay == 1) {
                mtrace("  Finished processing $numperiods periods for " .
                    "$unit->academic_unit_id: $unit->academic_unit.");
            } else {
                mtrace("\n  Finished processing $numperiods periods for " .
                    "$unit->academic_unit_id: $unit->academic_unit.");
            }

        }
        $periodsend = microtime(true);
        $periodstime = round($periodsend - $periodstart, 2);
        mtrace(" Finished processing $totalperiods periods in $periodstime seconds.");

