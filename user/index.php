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
 * Lists all the users within a given course.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/notes/lib.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->dirroot.'/enrol/locallib.php');

use core_table\local\filter\filter;
use core_table\local\filter\integer_filter;
use core_table\local\filter\string_filter;

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$newcourse    = optional_param('newcourse', false, PARAM_BOOL);
$roleid       = optional_param('roleid', 0, PARAM_INT);
$urlgroupid   = optional_param('group', 0, PARAM_INT);

$PAGE->set_url('/user/index.php', array(
        'page' => $page,
        'perpage' => $perpage,
        'contextid' => $contextid,
        'id' => $courseid,
        'newcourse' => $newcourse));

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
    if ($context->contextlevel != CONTEXT_COURSE) {
        print_error('invalidcontext');
    }
    $course = $DB->get_record('course', array('id' => $context->instanceid), '*', MUST_EXIST);
} else {
    $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
    $context = context_course::instance($course->id, MUST_EXIST);
}
// Not needed anymore.
unset($contextid);
unset($courseid);

require_login($course);

$systemcontext = context_system::instance();
$isfrontpage = ($course->id == SITEID);

$frontpagectx = context_course::instance(SITEID);

if ($isfrontpage) {
    $PAGE->set_pagelayout('admin');
    course_require_view_participants($systemcontext);
} else {
    $PAGE->set_pagelayout('incourse');
    course_require_view_participants($context);
}

// Trigger events.
user_list_view($course, $context);

$bulkoperations = has_capability('moodle/course:bulkmessaging', $context);

$PAGE->set_title("$course->shortname: ".get_string('participants'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);
$PAGE->add_body_class('path-user');                     // So we can style it independently.
$PAGE->set_other_editing_capability('moodle/course:manageactivities');

// Expand the users node in the settings navigation when it exists because those pages
// are related to this one.
$node = $PAGE->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
if ($node) {
    $node->force_open();
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('participants'));

$filterset = new \core_user\table\participants_filterset();
$filterset->add_filter(new integer_filter('courseid', filter::JOINTYPE_DEFAULT, [(int)$course->id]));

$participanttable = new \core_user\table\participants("user-index-participants-{$course->id}");

$canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
$filtergroupids = $urlgroupid ? [$urlgroupid] : [];

// Force group filtering if user should only see a subset of groups' users.
if ($course->groupmode == SEPARATEGROUPS && !$canaccessallgroups) {
    $filtergroupids = array_keys(groups_get_all_groups($course->id, $USER->id));

    if (empty($filtergroupids)) {
        // The user is not in a group so show message and exit.
        echo $OUTPUT->notification(get_string('notingroup'));
        echo $OUTPUT->footer();
        exit();
    }
}

// Apply groups filter if included in URL or forced due to lack of capabilities.
if (!empty($filtergroupids)) {
    $filterset->add_filter(new integer_filter('groups', filter::JOINTYPE_DEFAULT, $filtergroupids));
}

// Display single group information if requested in the URL.
if ($urlgroupid > 0 && ($course->groupmode != SEPARATEGROUPS || $canaccessallgroups)) {
    $grouprenderer = $PAGE->get_renderer('core_group');
    $groupdetailpage = new \core_group\output\group_details($urlgroupid);
    echo $grouprenderer->group_details($groupdetailpage);
}

// Filter by role if passed via URL (used on profile page).
if ($roleid) {
    $viewableroles = get_profile_roles($context);

    // Apply filter if the user can view this role.
    if (array_key_exists($roleid, $viewableroles)) {
        $filterset->add_filter(new integer_filter('roles', filter::JOINTYPE_DEFAULT, [$roleid]));
    }
}

// Manage enrolments.
$manager = new course_enrolment_manager($PAGE, $course);
$enrolbuttons = $manager->get_manual_enrol_buttons();
$enrolrenderer = $PAGE->get_renderer('core_enrol');
$enrolbuttonsout = '';
foreach ($enrolbuttons as $enrolbutton) {
    $enrolbuttonsout .= $enrolrenderer->render($enrolbutton);
}

echo html_writer::div($enrolbuttonsout, 'd-flex justify-content-end', [
    'data-region' => 'wrapper',
    'data-table-uniqueid' => $participanttable->uniqueid,
]);

// Render the user filters.
$userrenderer = $PAGE->get_renderer('core_user');
echo $userrenderer->participants_filter($context, $participanttable->uniqueid);

echo '<div class="userlist">';

// Do this so we can get the total number of rows.
ob_start();
$participanttable->set_filterset($filterset);
$participanttable->out($perpage, true);
$participanttablehtml = ob_get_contents();
ob_end_clean();

echo html_writer::start_tag('form', [
    'action' => 'action_redir.php',
    'method' => 'post',
    'id' => 'participantsform',
    'data-course-id' => $course->id,
    'data-table-unique-id' => $participanttable->uniqueid,
    'data-table-default-per-page' => ($perpage < DEFAULT_PAGE_SIZE) ? $perpage : DEFAULT_PAGE_SIZE,
]);
echo '<div>';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input type="hidden" name="returnto" value="'.s($PAGE->url->out(false)).'" />';

echo html_writer::tag(
    'p',
    get_string('countparticipantsfound', 'core_user', $participanttable->totalrows),
    [
        'data-region' => 'participant-count',
    ]
);

echo $participanttablehtml;

$perpageurl = new moodle_url('/user/index.php', [
    'contextid' => $context->id,
    'id' => $course->id,
]);
$perpagesize = DEFAULT_PAGE_SIZE;
$perpagevisible = false;
$perpagestring = '';

if ($perpage == SHOW_ALL_PAGE_SIZE && $participanttable->totalrows > DEFAULT_PAGE_SIZE) {
    $perpageurl->param('perpage', $participanttable->totalrows);
    $perpagesize = SHOW_ALL_PAGE_SIZE;
    $perpagevisible = true;
    $perpagestring = get_string('showperpage', '', DEFAULT_PAGE_SIZE);
} else if ($participanttable->get_page_size() < $participanttable->totalrows) {
    $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
    $perpagesize = SHOW_ALL_PAGE_SIZE;
    $perpagevisible = true;
    $perpagestring = get_string('showall', '', $participanttable->totalrows);
}

$perpageclasses = '';
if (!$perpagevisible) {
    $perpageclasses = 'hidden';
}
echo $OUTPUT->container(html_writer::link(
    $perpageurl,
    $perpagestring,
    [
        'data-action' => 'showcount',
        'data-target-page-size' => $perpagesize,
        'class' => $perpageclasses,
    ]
), [], 'showall');

$bulkoptions = (object) [
    'uniqueid' => $participanttable->uniqueid,
];

if ($bulkoperations) {
    echo '<br /><div class="buttons"><div class="form-inline">';

    echo html_writer::start_tag('div', array('class' => 'btn-group'));

    if ($participanttable->get_page_size() < $participanttable->totalrows) {
        // Select all users, refresh table showing all users and mark them all selected.
        $label = get_string('selectalluserswithcount', 'moodle', $participanttable->totalrows);
        echo html_writer::empty_tag('input', [
            'type' => 'button',
            'id' => 'checkall',
            'class' => 'btn btn-secondary',
            'value' => $label,
            'data-target-page-size' => $participanttable->totalrows,
        ]);
    }
    echo html_writer::end_tag('div');
    $displaylist = array();
    if (!empty($CFG->messaging) && has_all_capabilities(['moodle/site:sendmessage', 'moodle/course:bulkmessaging'], $context)) {
        $displaylist['#messageselect'] = get_string('messageselectadd');
    }
    if (!empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context) && $context->id != $frontpagectx->id) {
        $displaylist['#addgroupnote'] = get_string('addnewnote', 'notes');
    }

    $params = ['operation' => 'download_participants'];

    $downloadoptions = [];
    $formats = core_plugin_manager::instance()->get_plugins_of_type('dataformat');
    foreach ($formats as $format) {
        if ($format->is_enabled()) {
            $params = ['operation' => 'download_participants', 'dataformat' => $format->name];
            $url = new moodle_url('bulkchange.php', $params);
            $downloadoptions[$url->out(false)] = get_string('dataformat', $format->component);
        }
    }

    if (!empty($downloadoptions)) {
        $displaylist[] = [get_string('downloadas', 'table') => $downloadoptions];
    }

    if ($context->id != $frontpagectx->id) {
        $instances = $manager->get_enrolment_instances();
        $plugins = $manager->get_enrolment_plugins(false);
        foreach ($instances as $key => $instance) {
            if (!isset($plugins[$instance->enrol])) {
                // Weird, some broken stuff in plugin.
                continue;
            }
            $plugin = $plugins[$instance->enrol];
            $bulkoperations = $plugin->get_bulk_operations($manager);

            $pluginoptions = [];
            foreach ($bulkoperations as $key => $bulkoperation) {
                $params = ['plugin' => $plugin->get_name(), 'operation' => $key];
                $url = new moodle_url('bulkchange.php', $params);
                $pluginoptions[$url->out(false)] = $bulkoperation->get_title();
            }
            if (!empty($pluginoptions)) {
                $name = get_string('pluginname', 'enrol_' . $plugin->get_name());
                $displaylist[] = [$name => $pluginoptions];
            }
        }
    }

    $selectactionparams = array(
        'id' => 'formactionid',
        'class' => 'ml-2',
        'data-action' => 'toggle',
        'data-togglegroup' => 'participants-table',
        'data-toggle' => 'action',
        'disabled' => 'disabled'
    );
    $label = html_writer::tag('label', get_string("withselectedusers"),
            ['for' => 'formactionid', 'class' => 'col-form-label d-inline']);
    $select = html_writer::select($displaylist, 'formaction', '', ['' => 'choosedots'], $selectactionparams);
    echo html_writer::tag('div', $label . $select);

    echo '<input type="hidden" name="id" value="' . $course->id . '" />';
    echo '<div class="d-none" data-region="state-help-icon">' . $OUTPUT->help_icon('publishstate', 'notes') . '</div>';
    echo '</div></div></div>';

    $bulkoptions->noteStateNames = note_get_state_names();
}
echo '</form>';

$PAGE->requires->js_call_amd('core_user/participants', 'init', [$bulkoptions]);
echo '</div>';  // Userlist.

$enrolrenderer = $PAGE->get_renderer('core_enrol');
// Need to re-generate the buttons to avoid having elements with duplicate ids on the page.
$enrolbuttons = $manager->get_manual_enrol_buttons();
$enrolbuttonsout = '';
foreach ($enrolbuttons as $enrolbutton) {
    $enrolbuttonsout .= $enrolrenderer->render($enrolbutton);
}
echo html_writer::div($enrolbuttonsout, 'd-flex justify-content-end', [
    'data-region' => 'wrapper',
    'data-table-uniqueid' => $participanttable->uniqueid,
]);

if ($newcourse == 1) {
    $str = get_string('proceedtocourse', 'enrol');
    // The margin is to make it line up with the enrol users button when they are both on the same line.
    $classes = 'my-1';
    $url = course_get_url($course);
    echo $OUTPUT->single_button($url, $str, 'GET', array('class' => $classes));
}

echo $OUTPUT->footer();
