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
 * Custom behat functions
 *
 * @package   report_outline
 * @copyright 2017 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Class behat_report_outline custom Behat steps for report_outline.
 */
class behat_report_outline extends behat_base {
    /**
     * This is a horrible, horrible hack, but it is not clear how else a range of log entries can be produced to test the
     * filtering of the log entries.
     *
     * @Given /^the log timestamp for "(?P<username>(?:[^"]|\\")*)" and "(?P<activity_idnumber>(?:[^"]|\\")*)" is set to "(?P<date>(?:[^"]|\\")*)"$/
     * @param string $username
     * @param string $activityidnumber
     * @param string $date
     */
    public function the_log_timestamp_for_and_is_set_to($username, $activityidnumber, $date) {
        global $DB;

        // Get the name of the log table.
        $lm = get_log_manager();
        $readers = $lm->get_readers('\\core\\log\\sql_internal_table_reader');
        $reader = reset($readers);
        $table = $reader->get_internal_log_table_name();

        // Find the log entry.
        $cmrec = $DB->get_record('course_modules', ['idnumber' => $activityidnumber], '*', MUST_EXIST);
        $modname = $DB->get_field('modules', 'name', ['id' => $cmrec->module], MUST_EXIST);
        $userid = $DB->get_field('user', 'id', ['username' => $username], MUST_EXIST);

        $cond = [
            'userid' => $userid,
            'component' => 'mod_'.$modname,
            'target' => 'course_module',
            'action' => 'viewed',
            'contextinstanceid' => $cmrec->id,
        ];
        $logentries = $DB->get_records($table, $cond, 'timecreated DESC', 'id', 0, 1);
        $logentry = reset($logentries);

        // Update the timecreated for the entry.
        $timestamp = strtotime($date);
        $DB->set_field($table, 'timecreated', $timestamp, ['id' => $logentry->id]);
    }
}