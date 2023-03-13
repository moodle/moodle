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
 * report_coursesize tasks
 *
 * @package   report_coursesize
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_coursesize\task;

/**
 * report_async tasks
 *
 * @package   report_coursesize
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_async extends \core\task\scheduled_task {

    /**
     * Get task name
     */
    public function get_name() {
        return get_string('pluginname', 'report_coursesize');
    }

    /**
     * Execute task
     */
    public function execute() {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/report/coursesize/locallib.php');

        if (get_config('report_coursesize', 'calcmethod') == 'cron') {

            mtrace("Generating report_coursesize cache...");
            set_time_limit(0);

            // First we delete the old data, then we re-populate it, wrap in a transaction to help keep it together.
            $transaction = $DB->start_delegated_transaction();

            // Clean up cache table.
            $DB->delete_records('report_coursesize');

            // Generate report_coursesize table.
            $basesql = report_coursesize_filesize_sql();
            $sql = "INSERT INTO {report_coursesize} (course, filesize) $basesql ";
            $DB->execute($sql);

            // Now calculate size of backups.
            $basesql = report_coursesize_backupsize_sql();

            $sql = "UPDATE {report_coursesize} rc
                    SET backupsize = (SELECT bf.filesize FROM ($basesql) bf WHERE bf.course = rc.course)";
            $DB->execute($sql);

            $transaction->allow_commit();

            // Ignore the result now. The call will only cache the data internally.
            report_coursesize_get_usersizes();
            set_config('coursesizeupdated', time(), 'report_coursesize');

            mtrace("report_coursesize cache updated.");
        }
        // Check if the path ends with a "/" otherwise an exception will be thrown.
        $sitedatadir = $CFG->dataroot;
        if (is_dir($sitedatadir)) {
            // Only append a "/" if it doesn't already end with one.
            if (substr($sitedatadir, -1) !== '/') {
                $sitedatadir .= '/';
            }
        }

        // Total files usage either hasn't been stored, or is out of date.
        $totalusage = get_directory_size($sitedatadir);
        set_config('filessize', $totalusage, 'report_coursesize');
        set_config('filessizeupdated', time(), 'report_coursesize');

        mtrace("report_coursesize overall directory size updated");
    }
}
