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
 * A scheduled task for Report Custom SQL, to run the scheduled reports.
 *
 * @package report_lsusql
 * @copyright 2015 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_lsusql\task;

/**
 * A scheduled task for Report Custom SQL, to run the scheduled reports.
 *
 * @copyright 2015 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class run_reports extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'report_lsusql');
    }

    /**
     * Function to be run periodically according to the moodle cron
     * This function searches for things that need to be done, such
     * as sending out mail, toggling flags etc ...
     *
     * Runs any automatically scheduled reports weekly or monthly.
     *
     * @return boolean
     */
    public function execute() {
        global $CFG, $DB;

        require_once(dirname(__FILE__) . '/../../locallib.php');

        $timenow = time();

        list($startofthisweek, $startoflastweek) = report_lsusql_get_week_starts($timenow);
        list($startofthismonth) = report_lsusql_get_month_starts($timenow);

        mtrace("... Looking for old temp CSV files to delete.");
        $numdeleted = report_lsusql_delete_old_temp_files($startoflastweek);
        if ($numdeleted) {
            mtrace("... $numdeleted old temporary files deleted.");
        }

        // Get daily scheduled reports.
        $dailyreportstorun = report_lsusql_get_ready_to_run_daily_reports($timenow);

        // Get weekly and monthly scheduled reports.
        $scheduledreportstorun = $DB->get_records_select('report_lsusql_queries',
                                            "(runable = 'weekly' AND lastrun < :startofthisweek) OR
                                             (runable = 'monthly' AND lastrun < :startofthismonth) OR
                                             (runable = 'asap')",
                                            array('startofthisweek' => $startofthisweek,
                                                  'startofthismonth' => $startofthismonth), 'lastrun');

        // All reports ready to run.
        $reportstorun = array_merge($dailyreportstorun, $scheduledreportstorun);

        foreach ($reportstorun as $report) {
            mtrace("... Running report " . report_lsusql_plain_text_report_name($report));
            try {
                report_lsusql_generate_csv($report, $timenow, false);
            } catch (\Exception $e) {
                $info = get_exception_info($e);
                mtrace("... REPORT FAILED " . $info->message);
                if (!empty($info->debuginfo)) {
                    mtrace("\nDebug info: $info->debuginfo");
                }
                if (!empty($info->backtrace)) {
                    mtrace("\nStack trace: " . format_backtrace($info->backtrace, true));
                }
            }
        }
    }
}
