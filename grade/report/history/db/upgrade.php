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
 * Grade overview report upgrade steps.
 *
 * @package    gradereport_history
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade grade history report.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_gradereport_history_upgrade($oldversion) {

    if ($oldversion < 2019111801) {
        $perpageconfig = get_config('moodle', 'grade_report_historyperpage');

        // For existing installations with a non-integer 'per page' config, update the value to the default.
        if (!empty($perpageconfig) && filter_var($perpageconfig, FILTER_VALIDATE_INT) === false) {
            set_config('grade_report_historyperpage', 50);
        }

        upgrade_plugin_savepoint(true, 2019111801, 'gradereport', 'history');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
