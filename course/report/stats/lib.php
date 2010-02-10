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
 * This file contains functions used by the log reports
 *
 * This file is also required by /admin/reports/stats/index.php.
 *
 * @package course-report
 * @copyright  1999 onwards  Martin Dougiamas  http://moodle.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/lib/statslib.php');

function report_stats_mode_menu($course, $mode, $time, $url) {
    global $CFG, $OUTPUT;
    /*
    $reportoptions = stats_get_report_options($course->id, $mode);
    $timeoptions = report_stats_timeoptions($mode);
    if (empty($timeoptions)) {
        print_error('nostatstodisplay', '', $CFG->wwwroot.'/course/view.php?id='.$course->id);
    }
    */

    $options = array();
    $options[STATS_MODE_GENERAL] = get_string('statsmodegeneral');
    $options[STATS_MODE_DETAILED] = get_string('statsmodedetailed');
    if (has_capability('coursereport/stats:view', get_context_instance(CONTEXT_SYSTEM))) {
        $options[STATS_MODE_RANKED] = get_string('reports');
    }
    $popupurl = $url."?course=$course->id&time=$time";
    $select = new single_select(new moodle_url($popupurl), 'mode', $options, $mode, null);
    $select->formid = 'switchmode';
    echo $OUTPUT->render($select);
}


function report_stats_timeoptions($mode) {
    global $CFG, $DB;

    if ($mode == STATS_MODE_DETAILED) {
        $earliestday = $DB->get_field_sql('SELECT timeend FROM {stats_user_daily} ORDER BY timeend');
        $earliestweek = $DB->get_field_sql('SELECT timeend FROM {stats_user_weekly} ORDER BY timeend');
        $earliestmonth = $DB->get_field_sql('SELECT timeend FROM {stats_user_monthly} ORDER BY timeend');
    } else {
        $earliestday = $DB->get_field_sql('SELECT timeend FROM {stats_daily} ORDER BY timeend');
        $earliestweek = $DB->get_field_sql('SELECT timeend FROM {stats_weekly} ORDER BY timeend');
        $earliestmonth = $DB->get_field_sql('SELECT timeend FROM {stats_monthly} ORDER BY timeend');
    }


    if (empty($earliestday)) $earliestday = time();
    if (empty($earliestweek)) $earliestweek = time();
    if (empty($earliestmonth)) $earliestmonth = time();

    $now = stats_get_base_daily();
    $lastweekend = stats_get_base_weekly();
    $lastmonthend = stats_get_base_monthly();

    return stats_get_time_options($now,$lastweekend,$lastmonthend,$earliestday,$earliestweek,$earliestmonth);
}

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function stats_report_extend_navigation($navigation, $course, $context) {
    global $CFG, $OUTPUT;
    if (has_capability('coursereport/stats:view', $context)) {
        if (!empty($CFG->enablestats)) {
            $url = new moodle_url('/course/report/stats/index.php', array('id'=>$course->id));
            $navigation->add(get_string('stats'), $url, navigation_node::TYPE_SETTING, null, null, $OUTPUT->pix_url('i/report'));
        }
    }
}