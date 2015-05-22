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
 * Displays the Single view
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->dirroot.'/lib/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/singleview/lib.php');

$courseid = required_param('id', PARAM_INT);
$groupid  = optional_param('group', null, PARAM_INT);

// Making this work with profile reports.
$userid   = optional_param('userid', null, PARAM_INT);

$defaulttype = $userid ? 'user' : 'select';

$itemid   = optional_param('itemid', $userid, PARAM_INT);
$itemtype = optional_param('item', $defaulttype, PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 100, PARAM_INT);

$courseparams = array('id' => $courseid);
$PAGE->set_url(new moodle_url('/grade/report/singleview/index.php', $courseparams));
$PAGE->set_pagelayout('incourse');

if (!$course = $DB->get_record('course', $courseparams)) {
    print_error('nocourseid');
}

require_login($course);

if (!in_array($itemtype, gradereport_singleview::valid_screens())) {
    print_error('notvalid', 'gradereport_singleview', '', $itemtype);
}

$context = context_course::instance($course->id);

// This is the normal requirements.
require_capability('gradereport/singleview:view', $context);
require_capability('moodle/grade:viewall', $context);
require_capability('moodle/grade:edit', $context);

$gpr = new grade_plugin_return(array(
    'type' => 'report',
    'plugin' => 'singleview',
    'courseid' => $courseid
));

// Last selected report session tracking.
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'singleview';

// First make sure we have proper final grades -
// this must be done before constructing of the grade tree.
grade_regrade_final_grades($courseid);

$report = new gradereport_singleview($courseid, $gpr, $context, $itemtype, $itemid);

$reportname = $report->screen->heading();

$pluginname = get_string('pluginname', 'gradereport_singleview');

$pageparams = array(
    'id' => $courseid,
    'itemid' => $itemid,
    'item' => $itemtype,
    'userid' => $userid,
    'group' => $groupid,
    'page' => $page,
    'perpage' => $perpage
);

$currentpage = new moodle_url('/grade/report/singleview/index.php', $pageparams);

if ($data = data_submitted()) {
    $PAGE->set_pagelayout('redirect');
    $PAGE->set_title(get_string('savegrades', 'gradereport_singleview'));
    echo $OUTPUT->header();

    require_sesskey(); // Must have a sesskey for all actions.
    $result = $report->process_data($data);

    if (!empty($result->warnings)) {
        foreach ($result->warnings as $warning) {
            echo $OUTPUT->notification($warning);
        }
    }
    echo $OUTPUT->notification(get_string('savegradessuccess', 'gradereport_singleview', count ((array)$result->changecount)));
    echo $OUTPUT->continue_button($currentpage);
    echo $OUTPUT->footer();
    die();
}

$PAGE->set_pagelayout('report');
if ($itemtype == 'user') {
    print_grade_page_head($course->id, 'report', 'singleview', $reportname, false, false, true, null, null, $report->screen->item);
} else {
    print_grade_page_head($course->id, 'report', 'singleview', $reportname);
}

$graderrightnav = $graderleftnav = null;

$options = $report->screen->options();

if (!empty($options)) {

    $optionkeys = array_keys($options);
    $optionitemid = array_shift($optionkeys);

    $relreport = new gradereport_singleview(
                $courseid, $gpr, $context,
                $report->screen->item_type(), $optionitemid
    );
    $reloptions = $relreport->screen->options();
    $reloptionssorting = array_keys($relreport->screen->options());

    $i = array_search($itemid, $reloptionssorting);
    $navparams = array('item' => $itemtype, 'id' => $courseid, 'group' => $groupid);
    if ($i > 0) {
        $navparams['itemid'] = $reloptionssorting[$i - 1];
        $link = new moodle_url('/grade/report/singleview/index.php', $navparams);
        $navprev = html_writer::link($link, $OUTPUT->larrow() . ' ' . $reloptions[$reloptionssorting[$i - 1]]);
        $graderleftnav = html_writer::tag('div', $navprev, array('class' => 'itemnav previtem'));
    }
    if ($i < count($reloptionssorting) - 1) {
        $navparams['itemid'] = $reloptionssorting[$i + 1];
        $link = new moodle_url('/grade/report/singleview/index.php', $navparams);
        $navnext = html_writer::link($link, $reloptions[$reloptionssorting[$i + 1]] . ' ' . $OUTPUT->rarrow());
        $graderrightnav = html_writer::tag('div', $navnext, array('class' => 'itemnav nextitem'));
    }
}

if (!is_null($graderleftnav)) {
    echo $graderleftnav;
}
if (!is_null($graderrightnav)) {
    echo $graderrightnav;
}

if ($report->screen->supports_paging()) {
    echo $report->screen->pager();
}

if ($report->screen->display_group_selector()) {
    echo $report->group_selector;
}

echo $report->output();

if ($report->screen->supports_paging()) {
    echo $report->screen->pager();
}

if (!is_null($graderleftnav)) {
    echo $graderleftnav;
}
if (!is_null($graderrightnav)) {
    echo $graderrightnav;
}

$event = \gradereport_singleview\event\grade_report_viewed::create(
    array(
        'context' => $context,
        'courseid' => $courseid,
        'relateduserid' => $USER->id,
    )
);
$event->trigger();

echo $OUTPUT->footer();
