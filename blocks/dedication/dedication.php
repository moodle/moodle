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

global $DB, $PAGE, $OUTPUT;

require_once("../../config.php");

// Input params.
$courseid = required_param('courseid', PARAM_INT);
$instanceid = required_param('instanceid', PARAM_INT);

// Require course login.
$course = $DB->get_record("course", array("id" => $courseid), '*', MUST_EXIST);
require_course_login($course);

// Require capability to use this plugin in block context.
$context = context_block::instance($instanceid);
require_capability('block/dedication:use', $context);

require_once('dedication_lib.php');

// Optional params from request or default values.
$action = optional_param('action', 'all', PARAM_ALPHANUM);
$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', false, PARAM_BOOL);

// Current url.
$pageurl = new moodle_url('/blocks/dedication/dedication.php');
$pageurl->params(array(
    'courseid' => $courseid,
    'instanceid' => $instanceid,
    'action' => $action,
    'id' => $id,
));

// Page format.
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_pagetype('course-view-' . $course->format);
$PAGE->navbar->add(get_string('pluginname', 'block_dedication'), new moodle_url('/blocks/dedication/dedication.php', array('courseid' => $courseid, 'instanceid' => $instanceid)));
$PAGE->set_url($pageurl);
$PAGE->set_title(get_string('pagetitle', 'block_dedication', $course->shortname));
$PAGE->set_heading($course->fullname);

// Load libraries.
require_once('dedication_form.php');

// Load calculate params from form, request or set default values.
$mform = new dedication_block_selection_form($pageurl, null, 'get');
if ($mform->is_submitted()) {
    // Params from form post.
    $formdata = $mform->get_data();
    $mintime = $formdata->mintime;
    $maxtime = $formdata->maxtime;
    $limit = $formdata->limit;
} else {
    // Params from request or default values.
    $mintime = optional_param('mintime', $course->startdate, PARAM_INT);
    $maxtime = optional_param('maxtime', time(), PARAM_INT);
    $limit = optional_param('limit', BLOCK_DEDICATION_DEFAULT_SESSION_LIMIT, PARAM_INT);
    $mform->set_data(array('mintime' => $mintime, 'maxtime' => $maxtime, 'limit' => $limit));
}

// Url with params for links inside tables.
$pageurl->params(array(
    'mintime' => $mintime,
    'maxtime' => $maxtime,
    'limit' => $limit,
));

// Object to store view data.
$view = new stdClass();
$view->header = array();

$tablestyles = block_dedication_utils::get_table_styles();
$view->table = new html_table();
$view->table->attributes = array('class' => $tablestyles['table_class'] . " table-$action");

switch ($action) {
    case 'user':
        $userid = required_param('id', PARAM_INT);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        if (!is_enrolled(context_course::instance($course->id), $user)) {
            print_error('usernotincourse');
        }

        $dm = new block_dedication_manager($course, $mintime, $maxtime, $limit);
        if ($download) {
            $dm->download_user_dedication($user);
            exit;
        }

        // Table formatting & total count.
        $totaldedication = 0;
        $rows = $dm->get_user_dedication($user);
        foreach ($rows as $index => $row) {
            $totaldedication += $row->dedicationtime;
            $rows[$index] = array(
                userdate($row->start_date),
                block_dedication_utils::format_dedication($row->dedicationtime),
                block_dedication_utils::format_ips($row->ips),
            );
        }

        $view->header[] = get_string('userdedication', 'block_dedication', $OUTPUT->user_picture($user, array('courseid' => $course->id)) . fullname($user));
        $view->header[] = get_string('period', 'block_dedication', (object) array('mintime' => userdate($mintime), 'maxtime' => userdate($maxtime)));
        $view->header[] = get_string('perioddiff', 'block_dedication', format_time($maxtime - $mintime));
        $view->header[] = get_string('totaldedication', 'block_dedication', block_dedication_utils::format_dedication($totaldedication));
        $view->header[] = get_string('meandedication', 'block_dedication', block_dedication_utils::format_dedication(count($rows) ? $totaldedication / count($rows) : 0));

        $view->table->head = array(get_string('sessionstart', 'block_dedication'), get_string('sessionduration', 'block_dedication'), 'IP');
        $view->table->data = $rows;
        break;

    case 'group':
    case 'all':
    default:
        $groups = groups_get_all_groups($course->id);

        if ($action == 'group') {
            $groupid = required_param('id', PARAM_INT);
            if (groups_group_exists($groupid)) {
                $students = groups_get_members($groupid);
            } else {
                // TODO: PUT ERROR STRING NO GROUP.
            }
        } else {
            // Get all students in this course or ordered by group.
            if ($course->groupmode == NOGROUPS) {
                $students = get_enrolled_users(context_course::instance($course->id));
            } else {
                $students = array();
                foreach ($groups as $group) {
                    $members = groups_get_members($group->id);
                    $students = array_replace($students, $members);
                }
                // Empty groups or missconfigured, get all students anyway
                if (!$students) {
                    $students = get_enrolled_users(context_course::instance($course->id));
                }
            }
        }

        if (!$students) {
            print_error('noparticipants');
        }
        $dm = new block_dedication_manager($course, $mintime, $maxtime, $limit);
        $rows = $dm->get_students_dedication($students);
        if ($download) {
            $dm->download_students_dedication($rows);
            exit;
        }

        // Table formatting & total count.
        $totaldedication = 0;
        foreach ($rows as $index => $row) {
            $totaldedication += $row->dedicationtime;
            $userurl = new moodle_url($pageurl, array('action' => 'user', 'id' => $row->user->id));
            $groupurl = new moodle_url($pageurl, array('action' => 'group', 'id' => $row->groupid));
            $rows[$index] = array(
                $OUTPUT->user_picture($row->user, array('courseid' => $course->id)),
                html_writer::link($userurl, $row->user->firstname),
                html_writer::link($userurl, $row->user->lastname),
                html_writer::link($groupurl, isset($groups[$row->groupid]) ? $groups[$row->groupid]->name : ''),
                block_dedication_utils::format_dedication($row->dedicationtime),
                $row->connectionratio
            );
        }

        if ($action == 'group') {
            $view->header[] = get_string('dedicationgroup', 'block_dedication', $groups[$groupid]->name);
        } else {
            $view->header[] = get_string('dedicationall', 'block_dedication');
        }
        $view->header[] = get_string('period', 'block_dedication', (object) array('mintime' => userdate($mintime), 'maxtime' => userdate($maxtime)));
        $view->header[] = get_string('perioddiff', 'block_dedication', format_time($maxtime - $mintime));
        $view->header[] = get_string('totaldedication', 'block_dedication', block_dedication_utils::format_dedication($totaldedication));
        $view->header[] = get_string('meandedication', 'block_dedication', block_dedication_utils::format_dedication(count($rows) ? $totaldedication / count($rows) : 0));

        $view->table->head = array('', get_string('firstname'), get_string('lastname'), get_string('group'),
            get_string('dedicationrow', 'block_dedication'), get_string('connectionratiorow', 'block_dedication'));
        $view->table->data = $rows;
        break;
}

// START PAGE: layout, headers, title, boxes...
echo $OUTPUT->header();

// Form.
$mform->display();

echo $OUTPUT->box_start();

foreach ($view->header as $header) {
    echo $OUTPUT->heading($header, 4);
}

// Download button.
echo html_writer::start_tag('div', array('class' => 'download-dedication'));
echo $OUTPUT->single_button(new moodle_url($pageurl, array('download' => true)), get_string('downloadexcel'), 'get');
echo html_writer::end_tag('div');

// Format table headers if they exists.
if (!empty($view->table->head)) {
    $headers = array();
    foreach ($view->table->head as $header) {
        $cell = new html_table_cell($header);
        $cell->style = $tablestyles['header_style'];
        $headers[] = $cell;
    }
    $view->table->head = $headers;
}
echo html_writer::table($view->table);

// END PAGE.
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
