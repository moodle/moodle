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
 * This file contains functions used by the course overview report.
 *
 * @package    report_courseoverview
 * @copyright  2016 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/statslib.php');

/**
 * Gather course overview data and print the chart.
 *
 * @param int $report represents the report type field on the course overview report filter.
 * @param int $time represents the time period field on the course overview report filter.
 * @param int $numcourses represents the number of courses field on the course overview report filter.
 * @return void
 */
function report_courseoverview_print_chart($report, $time, $numcourses) {
    global $DB, $OUTPUT, $PAGE;

    $param = stats_get_parameters($time, $report, SITEID, STATS_MODE_RANKED);
    if (!empty($param->sql)) {
        $sql = $param->sql;
    } else {
        $sql = "SELECT courseid, $param->fields
                  FROM {" . 'stats_' . $param->table . "}
                 WHERE timeend >= $param->timeafter
                   AND stattype = 'activity'
                   AND roleid = 0
              GROUP BY courseid
                   $param->extras
              ORDER BY $param->orderby";
    }
    $courses = $DB->get_records_sql($sql, $param->params, 0, $numcourses);

    if (empty($courses)) {
        $PAGE->set_url('/report/courseoverview/index.php');
        print_error('statsnodata', 'error', $PAGE->url->out());
    }

    $data = [];
    $i = 0;
    foreach ($courses as $c) {
        $data['labels'][$i] = $DB->get_field('course', 'shortname', array('id' => $c->courseid));

        // Line3 represents the third column of the report table except for the most active users report.
        // It is a float number and can be participation radio or activity per user.
        if (isset($c->line3)) {
            $data['series'][$i] = round($c->line3, 2);
        } else {
            $data['series'][$i] = $c->{$param->graphline};
        }
        $i++;
    }

    $chart = new \core\chart_bar();
    $series = new \core\chart_series($param->{$param->graphline}, $data['series']);
    $chart->add_series($series);
    $chart->set_labels($data['labels']);

    echo $OUTPUT->render($chart);
}
