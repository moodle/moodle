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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_userstats
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_userstats extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('userstats', 'block_configurable_reports');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['users'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return format_string($data->columname);
    }

    /**
     * Execute
     *
     * @param object $data
     * @param object $row
     * @param object $user
     * @param int $courseid
     * @param int $starttime
     * @param int $endtime
     * @return string
     */
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $CFG;
        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc.
        $stat = '--';

        $filterstarttime = optional_param('filter_starttime', 0, PARAM_RAW);
        $filterendtime = optional_param('filter_endtime', 0, PARAM_RAW);

        // Do not apply filters in timeline report (filters yet applied).
        if ($starttime && $endtime) {
            $filterstarttime = 0;
            $filterendtime = 0;
        }

        if ($filterstarttime && $filterendtime) {
            $filterstarttime = make_timestamp($filterstarttime['year'], $filterstarttime['month'], $filterstarttime['day']);
            $filterendtime = make_timestamp($filterendtime['year'], $filterendtime['month'], $filterendtime['day']);
        }

        $starttime = ($filterstarttime) ? $filterstarttime : $starttime;
        $endtime = ($filterendtime) ? $filterendtime : $endtime;

        if ($data->stat === 'coursededicationtime') {
            $sql = "userid = ?";
            $params = [$row->id];

            require_once($CFG->dirroot . "/blocks/configurable_reports/locallib.php");

            [$uselegacyreader, $useinternalreader, $logtable] = cr_logging_info();

            $logs = [];
            if ($uselegacyreader) {

                if ($courseid != 1) {
                    $sql .= " AND course = ?";
                    $params = array_merge($params, [$courseid]);
                }

                if ($starttime && $endtime) {
                    $starttime = usergetmidnight($starttime) + 24 * 60 * 60;
                    $endtime = usergetmidnight($endtime) + 24 * 60 * 60;
                    $sql .= " AND time >= ? AND time <= ?";
                    $params = array_merge($params, [$starttime, $endtime]);
                }

                $logs = $DB->get_records_select("log", $sql, $params, "time ASC", "id,time");

            } else if ($useinternalreader) {

                if ($courseid != 1) {
                    $sql .= " AND courseid = ?";
                    $params = array_merge($params, [$courseid]);
                }

                if ($starttime && $endtime) {
                    $starttime = usergetmidnight($starttime) + 24 * 60 * 60;
                    $endtime = usergetmidnight($endtime) + 24 * 60 * 60;
                    $sql .= " AND timecreated >= ? AND timecreated <= ?";
                    $params = array_merge($params, [$starttime, $endtime]);
                }

                $logs = $DB->get_records_select($logtable, $sql, $params, "timecreated ASC", "id,timecreated as time");
            }

            // Code from Course Dedication Block.
            if ($logs) {

                // This should be a config value in some where.
                $limitinseconds = (!empty($data->sessionlimittime)) ? $data->sessionlimittime : 30 * 60;

                $previouslog = array_shift($logs);
                $previouslogtime = $previouslog->time;
                $sessionstart = $previouslogtime;
                $totaldedication = 0;

                foreach ($logs as $log) {
                    if (($log->time - $previouslogtime) > $limitinseconds) {
                        $dedication = $previouslogtime - $sessionstart;
                        $totaldedication += $dedication;
                        $sessionstart = $log->time;
                    }
                    $previouslogtime = $log->time;
                }

                $dedication = $previouslogtime - $sessionstart;
                $totaldedication += $dedication;
                if ($totaldedication) {
                    return format_time($totaldedication);
                } else {
                    return 0;
                }
            }

            // Code from Course Dedication Block.
            return 0;
        }

        switch ($data->stat) {
            case 'activityview':
                $total = 'statsreads';
                $stattype = 'activity';
                break;
            case 'activitypost':
                $total = 'statswrites';
                $stattype = 'activity';
                break;
            case 'logins':
            default:
                $total = 'statsreads';
                $stattype = 'logins';
        }
        $sql = "SELECT SUM($total) as total FROM {stats_user_daily} WHERE stattype = ? AND userid = ?";
        $params = [$stattype, $row->id];

        if ($courseid != SITEID && $data->stat !== 'logins') {
            $sql .= " AND courseid = ?";
            $params[] = $courseid;
        }

        if ($starttime && $endtime) {
            $starttime = usergetmidnight($starttime) + 24 * 60 * 60;
            $endtime = usergetmidnight($endtime) + 24 * 60 * 60;
            $sql .= " AND timeend >= $starttime AND timeend <= $endtime";
            $params = array_merge($params, [$starttime, $endtime]);
        }

        if ($res = $DB->get_records_sql($sql, $params)) {
            $res = array_shift($res);
            if ($res->total != null) {
                return $res->total;
            }

            return 0;
        }

        return $stat;
    }

}
