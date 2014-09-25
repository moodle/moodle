<?php

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once '../../../config.php';
require_once $CFG->dirroot.'/lib/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/singleview/lib.php';

$courseid = required_param('id', PARAM_INT);
$groupid  = optional_param('group', null, PARAM_INT);

// Making this work with profile reports
$userid   = optional_param('userid', null, PARAM_INT);

$default_type = $userid ? 'user' : 'select';

$itemid   = optional_param('itemid', $userid, PARAM_INT);
$itemtype = optional_param('item', $default_type, PARAM_TEXT);

$course_params = array('id' => $courseid);

$PAGE->set_url(new moodle_url('/grade/report/singleview/index.php', $course_params));

if (!$course = $DB->get_record('course', $course_params)) {
    print_error('nocourseid');
}

if (!in_array($itemtype, grade_report_singleview::valid_screens())) {
    print_error('notvalid', 'gradereport_singleview', '', $itemtype);
}

require_login($course);

$context = context_course::instance($course->id);

// This is the normal requirements
require_capability('gradereport/singleview:view', $context);
require_capability('moodle/grade:viewall', $context);
require_capability('moodle/grade:edit', $context);
// End permission

$gpr = new grade_plugin_return(array(
    'type' => 'report',
    'plugin' => 'singleview',
    'courseid' => $courseid
));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'singleview';

grade_regrade_final_grades($courseid);

$report = new grade_report_singleview(
    $courseid, $gpr, $context,
    $itemtype, $itemid, $groupid
);

$reportname = $report->screen->heading();

$pluginname = get_string('pluginname', 'gradereport_singleview');

$report_url = new moodle_url('/grade/report/grader/index.php', $course_params);
$edit_url = new moodle_url('/grade/report/singleview/index.php', $course_params);

$PAGE->navbar->ignore_active(true);

$PAGE->navbar->add(get_string('courses'));
$PAGE->navbar->add($course->shortname, new moodle_url('/course/view.php', $course_params));

$PAGE->navbar->add(get_string('gradeadministration', 'grades'));
$PAGE->navbar->add(get_string('pluginname', 'gradereport_grader'), $report_url);

if ($reportname != $pluginname) {
    $PAGE->navbar->add($pluginname, $edit_url);
    $PAGE->navbar->add($reportname);
} else {
    $PAGE->navbar->add($pluginname);
}

if ($data = data_submitted()) {
    $warnings = $report->process_data($data);

    if (empty($warnings)) {
        redirect($report_url);
    }
}

$graderrightnav = $graderleftnav = null;
if ($report->screen instanceof selectable_items
        && class_exists($report::classname($report->screen->item_type()))) { //should be ok for user and grade for now, allows other cross singleview nav too.

    $optionkeys = array_keys($report->screen->options());
    $optionitemid = array_shift($optionkeys); //just any one thanks

    $relreport = new grade_report_singleview(
                $courseid, $gpr, $context,
                $report->screen->item_type(), $optionitemid, $groupid
    );
    $reloptions = $relreport->screen->options();
    $reloptionssorting = array_keys($relreport->screen->options());

    $i = array_search($itemid, $reloptionssorting);
    $navparams = array('item' => $itemtype, 'id' => $courseid, 'group' => $groupid);
    if ($i>0) {
        $navparams['itemid'] = $reloptionssorting[$i-1];
        $link = new moodle_url('/grade/report/singleview/index.php', $navparams);
        $navprev=html_writer::link($link, $reloptions[$reloptionssorting[$i-1]]);
        $graderleftnav = html_writer::tag('small', $navprev, array('class' => 'itemnav previtem'));
    }
    if ($i<count($reloptionssorting)-1) {
        $navparams['itemid'] = $reloptionssorting[$i+1];
        $link = new moodle_url('/grade/report/singleview/index.php', $navparams);
        $navnext=html_writer::link($link, $reloptions[$reloptionssorting[$i+1]]);
        $graderrightnav = html_writer::tag('small', $navnext, array('class' => 'itemnav nextitem'));
    }
}

print_grade_page_head($course->id, 'report', 'singleview', $reportname);

if(!is_null($graderleftnav)) {
    echo $graderleftnav;
}
if(!is_null($graderrightnav)) {
    echo $graderrightnav;
}

if ($report->screen->supports_paging()) {
    echo $report->screen->pager();
}

if ($report->screen->display_group_selector()) {
    echo $report->group_selector;
}

if (!empty($warnings)) {
    foreach ($warnings as $warning) {
        echo $OUTPUT->notification($warning);
    }
}

echo $report->output();

if ($report->screen->supports_paging()) {
    echo $report->screen->pager();
}

if(!is_null($graderleftnav)) {
    echo $graderleftnav;
}
if(!is_null($graderrightnav)) {
    echo $graderrightnav;
}

echo $OUTPUT->footer();

?>
