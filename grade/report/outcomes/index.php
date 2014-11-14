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
 * The gradebook outcomes report
 *
 * @package   gradereport_outcomes
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

include_once('../../../config.php');
require_once($CFG->libdir . '/gradelib.php');
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);                   // course id

$PAGE->set_url('/grade/report/outcomes/index.php', array('id'=>$courseid));

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = context_course::instance($course->id);

require_capability('gradereport/outcomes:view', $context);

// First make sure we have proper final grades.
grade_regrade_final_grades($courseid);

// Grab all outcomes used in course.
$report_info = array();
$outcomes = grade_outcome::fetch_all_available($courseid);

// Will exclude grades of suspended users if required.
$defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
$showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
$showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);
if ($showonlyactiveenrol) {
    $suspendedusers = get_suspended_userids($context);
}

// Get grade_items that use each outcome.
foreach ($outcomes as $outcomeid => $outcome) {
    $report_info[$outcomeid]['items'] = $DB->get_records_select('grade_items', "outcomeid = ? AND courseid = ?", array($outcomeid, $courseid));
    $report_info[$outcomeid]['outcome'] = $outcome;

    // Get average grades for each item.
    if (is_array($report_info[$outcomeid]['items'])) {
        foreach ($report_info[$outcomeid]['items'] as $itemid => $item) {
            $params = array();
            $hidesuspendedsql = '';
            if ($showonlyactiveenrol && !empty($suspendedusers)) {
                list($notinusers, $params) = $DB->get_in_or_equal($suspendedusers, SQL_PARAMS_QM, null, false);
                $hidesuspendedsql = ' AND userid ' . $notinusers;
            }
            $params = array_merge(array($itemid), $params);

            $sql = "SELECT itemid, AVG(finalgrade) AS avg, COUNT(finalgrade) AS count
                      FROM {grade_grades}
                     WHERE itemid = ?".
                     $hidesuspendedsql.
                  "GROUP BY itemid";
            $info = $DB->get_records_sql($sql, $params);

            if (!$info) {
                unset($report_info[$outcomeid]['items'][$itemid]);
                continue;
            } else {
                $info = reset($info);
                $avg = round($info->avg, 2);
                $count = $info->count;
            }

            $report_info[$outcomeid]['items'][$itemid]->avg = $avg;
            $report_info[$outcomeid]['items'][$itemid]->count = $count;
        }
    }
}

$html = '<table class="generaltable boxaligncenter" width="90%" cellspacing="1" cellpadding="5" summary="Outcomes Report">' . "\n";
$html .= '<tr><th class="header c0" scope="col">' . get_string('outcomeshortname', 'grades') . '</th>';
$html .= '<th class="header c1" scope="col">' . get_string('courseavg', 'grades') . '</th>';
$html .= '<th class="header c2" scope="col">' . get_string('sitewide', 'grades') . '</th>';
$html .= '<th class="header c3" scope="col">' . get_string('activities', 'grades') . '</th>';
$html .= '<th class="header c4" scope="col">' . get_string('average', 'grades') . '</th>';
$html .= '<th class="header c5" scope="col">' . get_string('numberofgrades', 'grades') . '</th></tr>' . "\n";

$row = 0;
foreach ($report_info as $outcomeid => $outcomedata) {
    $rowspan = count($outcomedata['items']);
    // If there are no items for this outcome, rowspan will equal 0, which is not good.
    if ($rowspan == 0) {
        $rowspan = 1;
    }

    $shortname_html = '<tr class="r' . $row . '"><td class="cell c0" rowspan="' . $rowspan . '">' . $outcomedata['outcome']->shortname . "</td>\n";

    $sitewide = get_string('no');
    if (empty($outcomedata['outcome']->courseid)) {
        $sitewide = get_string('yes');
    }

    $sitewide_html = '<td class="cell c2" rowspan="' . $rowspan . '">' . $sitewide . "</td>\n";

    $outcomedata['outcome']->sum = 0;
    $scale = new grade_scale(array('id' => $outcomedata['outcome']->scaleid), false);

    $print_tr = false;
    $items_html = '';

    if (!empty($outcomedata['items'])) {
        foreach ($outcomedata['items'] as $itemid => $item) {
            if ($print_tr) {
                $row++;
                $items_html .= "<tr class=\"r$row\">\n";
            }

            $grade_item = new grade_item($item, false);

            if ($item->itemtype == 'mod') {
                $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
                $itemname = '<a href="'.$CFG->wwwroot.'/mod/'.$item->itemmodule.'/view.php?id='.$cm->id.'">'.format_string($cm->name, true, $cm->course).'</a>';
            } else {
                $itemname = $grade_item->get_name();
            }

            $outcomedata['outcome']->sum += $item->avg;
            $gradehtml = $scale->get_nearest_item($item->avg);

            $items_html .= "<td class=\"cell c3\">$itemname</td>"
                         . "<td class=\"cell c4\">$gradehtml ($item->avg)</td>"
                         . "<td class=\"cell c5\">$item->count</td></tr>\n";
            $print_tr = true;
        }
    } else {
        $items_html .= "<td class=\"cell c3\"> - </td><td class=\"cell c4\"> - </td><td class=\"cell c5\"> 0 </td></tr>\n";
    }

    // Calculate outcome average.
    if (is_array($outcomedata['items'])) {
        $count = count($outcomedata['items']);
        if ($count > 0) {
            $avg = $outcomedata['outcome']->sum / $count;
        } else {
            $avg = $outcomedata['outcome']->sum;
        }
        $avg_html = $scale->get_nearest_item($avg) . " (" . round($avg, 2) . ")\n";
    } else {
        $avg_html = ' - ';
    }

    $outcomeavg_html = '<td class="cell c1" rowspan="' . $rowspan . '">' . $avg_html . "</td>\n";

    $html .= $shortname_html . $outcomeavg_html . $sitewide_html . $items_html;
    $row++;
}

$html .= '</table>';

print_grade_page_head($courseid, 'report', 'outcomes');

echo $html;

$event = \gradereport_outcomes\event\grade_report_viewed::create(
    array(
        'context' => $context,
        'courseid' => $courseid,
    )
);
$event->trigger();

echo $OUTPUT->footer();
