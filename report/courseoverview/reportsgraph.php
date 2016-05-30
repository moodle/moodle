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
 * Graph
 *
 * @package    report
 * @subpackage courseoverview
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/statslib.php');
require_once($CFG->dirroot.'/lib/graphlib.php');

$report     = required_param('report', PARAM_INT);
$time       = required_param('time', PARAM_INT);
$numcourses = required_param('numcourses', PARAM_INT);

require_login();

require_capability('report/courseoverview:view', context_system::instance());

stats_check_uptodate();

$param = stats_get_parameters($time,$report,SITEID,STATS_MODE_RANKED);

if (!empty($param->sql)) {
    $sql = $param->sql;
} else {
    $sql = "SELECT courseid, $param->fields
              FROM {".'stats_'.$param->table."}
             WHERE timeend >= $param->timeafter AND stattype = 'activity' AND roleid = 0
          GROUP BY courseid
                   $param->extras
          ORDER BY $param->orderby";
}

$courses = $DB->get_records_sql($sql, $param->params, 0, $numcourses);

if (empty($courses)) {
    $PAGE->set_url('/report/courseoverview/index.php');
    print_error('statsnodata', 'error', $PAGE->url->out());
}


$graph = new graph(750,400);

$graph->parameter['legend'] = 'outside-right';
$graph->parameter['legend_size'] = 10;
$graph->parameter['x_axis_angle'] = 90;
$graph->parameter['title'] = false; // moodle will do a nicer job.
$graph->y_tick_labels = null;
$graph->offset_relation = null;
if ($report != STATS_REPORT_ACTIVE_COURSES) {
    $graph->parameter['y_decimal_left'] = 2;
}

foreach ($courses as $c) {
    $graph->x_data[] = $DB->get_field('course', 'shortname', array('id'=>$c->courseid));
    $graph->y_data['bar1'][] = $c->{$param->graphline};
}
$graph->y_order = array('bar1');
$graph->y_format['bar1'] = array('colour' => 'blue','bar' => 'fill','legend' => $param->{$param->graphline});

$graph->draw_stack();
