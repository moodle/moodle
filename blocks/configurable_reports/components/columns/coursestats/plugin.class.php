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
 * Class plugin_coursestats
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_coursestats extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('coursestats', 'block_configurable_reports');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['courses'];
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
     * execute
     *
     * @param object $data
     * @param object $row
     * @param object $user
     * @param int $courseid
     * @param int $starttime
     * @param int $endtime
     * @return int|string
     */
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB;

        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc...

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

        $limit = 0;
        $numericroles = array_filter($data->roles, 'is_numeric');

        switch ($data->stat) {
            case 'activityview':
                $total = 'SUM(stat1)';
                $stattype = 'activity';
                $extrasql = " AND roleid IN (" . implode(',', $numericroles ). ")";
                break;
            case 'activitypost':
                $total = 'SUM(stat2)';
                $stattype = 'activity';
                $extrasql = " AND roleid IN (" . implode(',', $numericroles) . ")";
                break;
            case 'activeenrolments':
                $total = 'stat2';
                $stattype = 'enrolments';
                $extrasql = " ORDER BY timeend DESC";
                $limit = 1;
                break;
            case 'totalenrolments':
            default:
                $total = 'stat1';
                $stattype = 'enrolments';
                $extrasql = " ORDER BY timeend DESC";
                $limit = 1;
        }
        $sql = "SELECT $total as total FROM {stats_daily} WHERE stattype = ? AND courseid = ?";
        $params = [$stattype, $row->id];

        if ($starttime && $endtime) {
            $starttime = usergetmidnight($starttime) + 24 * 60 * 60;
            $endtime = usergetmidnight($endtime) + 24 * 60 * 60;
            $sql .= " AND timeend >= ? AND timeend <= ?";
            $params = array_merge($params, [$starttime, $endtime]);
        }

        $sql .= $extrasql;

        if ($res = $DB->get_records_sql($sql, $params, 0, $limit)) {
            $res = array_shift($res);
            if ($res->total != null) {
                return $res->total;
            }

            return 0;
        }

        return $stat;
    }

}

