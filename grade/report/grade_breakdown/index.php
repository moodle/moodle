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
require_once $CFG->dirroot.'/grade/report/grade_breakdown/lib.php';

$courseid = required_param('id', PARAM_INT);
$gradeid  = optional_param('grade', null, PARAM_INT);
$groupid  = optional_param('group', null, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);

$context = context_course::instance($course->id);
// This is the normal requirements
require_capability('gradereport/grade_breakdown:view', $context);

// Are they a teacher?
$isteacher = has_capability('moodle/grade:viewall', $context);

// Graded roles
$gradedroles = explode(',', $CFG->gradebookroles);

$hasaccess = false;
// If the user is a "student" (graded role), and the teacher allowed them
// to view the report

$allowstudents = grade_get_setting(
    $courseid,
    'report_grade_breakdown_allowstudents',
    $CFG->grade_report_grade_breakdown_allowstudents
);

if (!$isteacher && $allowstudents) {
    $userroles = get_user_roles($context, $USER->id);

    foreach ($userroles as $role) {
        if (in_array($role->roleid, $gradedroles)) {
            $hasaccess = true;
            break;
        }
    }
}
// End permission

$s = function($key, $a = null) {
    return get_string($key, 'gradereport_grade_breakdown');
};

$gpr = new grade_plugin_return(array(
    'type' => 'report', 'plugin' => 'grade_breakdown', 'courseid' => $courseid
));

$reportname = $s('pluginname');

$PAGE->set_context($context);
$url = new moodle_url('/grade/report/grade_breakdown/index.php', ['id' => $courseid]);
$PAGE->set_url($url);

if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grade_breakdown';

grade_regrade_final_grades($course->id);

print_grade_page_head(
    $course->id,
    'report',
    $active_plugin = 'grade_breakdown',
    $heading = $reportname,
    $return = false,
    $buttons = false,
    $shownavigation = true,
    $headerhelpidentifier = null,
    $headerhelpcomponent = null,
    $user = null,
    $actionbar = null,    
);


// The current user does not have access to view this report
if (!$hasaccess && !$isteacher) {
    echo $OUTPUT->heading($s('teacher_disabled'));
    echo $OUTPUT->footer();
    exit;
}

// Find the number of users in the course
$users = array();

if (COUNT($gradedroles) > 1) {
    foreach ($gradedroles as $gradedrole) {
        $users = $users + get_role_users(
            $gradedrole, $context, false, '',
            'u.id, u.lastname, u.firstname', null, $groupid
        );
    }
} else {
    $gradedrole = implode($gradedroles);
    $users = get_role_users(
        $gradedrole, $context, false, '',
        'u.id', 'u.lastname, u.firstname', null, $groupid
    );
}
$numusers = count($users);

// The student has access, but they still are unable to view it
// if there is 10 or less student enrollments in the class
if (!$isteacher && $numusers <= 10) {
    echo $OUTPUT->heading($s('size_disabled'));
    echo $OUTPUT->footer();
    exit;
}

$report = new grade_report_grade_breakdown(
    $courseid, $gpr, $context, $gradeid, $groupid
);

$report->setup_grade_items();
$report->setup_groups();

echo '<div class="selectors">
        '. ($isteacher ? $report->group_selector : '') . $report->grade_selector . '
      </div>';

$report->print_table();

echo $OUTPUT->footer();

?>
