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
 * Grader report upgrade steps.
 *
 * @package    gradereport_grader
 * @copyright  2023 Ilya Tregubov <ilya@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade grader report.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_gradereport_grader_upgrade(int $oldversion): bool {
    global $DB;

    if ($oldversion < 2023032100) {
        // Remove grade_report_showquickfeedback, grade_report_enableajax, grade_report_showeyecons,
        // grade_report_showlocks, grade_report_showanalysisicon preferences for every user.
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showquickfeedback']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_enableajax']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showeyecons']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showlocks']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showanalysisicon']);

        // Remove grade_report_showactivityicons, grade_report_showcalculations preferences for every user.
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showactivityicons']);
        $DB->delete_records('user_preferences', ['name' => 'grade_report_showcalculations']);

        // The grade_report_showquickfeedback, grade_report_enableajax, grade_report_showeyecons,
        // grade_report_showlocks, grade_report_showanalysisicon settings have been removed.
        unset_config('grade_report_showquickfeedback');
        unset_config('grade_report_enableajax');
        unset_config('grade_report_showeyecons');
        unset_config('grade_report_showlocks');
        unset_config('grade_report_showanalysisicon');

        // The grade_report_showactivityicons, grade_report_showcalculations settings have been removed.
        unset_config('grade_report_showactivityicons');
        unset_config('grade_report_showcalculations');

        // Main savepoint reached.
        upgrade_plugin_savepoint(true, 2023032100, 'gradereport', 'grader');
    }

    if ($oldversion < 2023032700) {
        unset_config('grade_report_studentsperpage');
        upgrade_plugin_savepoint(true, 2023032700, 'gradereport', 'grader');
    }

    if ($oldversion < 2023032800) {
        // Remove plugin entry created by previously incorrect 2023032100 savepoint.
        $DB->delete_records('config_plugins', ['plugin' => 'grade_gradereport_grader']);
        upgrade_plugin_savepoint(true, 2023032800, 'gradereport', 'grader');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
