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

define('DEFAULT_PAGE_SIZE', 20);
define('SHOW_ALL_PAGE_SIZE', 5000);

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$contextid    = optional_param('contextid', 0, PARAM_INT); // One of this or.
$courseid     = optional_param('id', 0, PARAM_INT); // This are required.
$newcourse    = optional_param('newcourse', false, PARAM_BOOL);
$selectall    = optional_param('selectall', false, PARAM_BOOL); // When rendering checkboxes against users mark them all checked.
$roleid       = optional_param('roleid', 0, PARAM_INT);
$groupparam   = optional_param('group', 0, PARAM_INT);

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

// Get the currently applied filters.
$filtersapplied = optional_param_array('unified-filters', [], PARAM_NOTAGS);
$filterwassubmitted = optional_param('unified-filter-submitted', 0, PARAM_BOOL);

// If they passed a role make sure they can view that role.
if ($roleid) {
    $viewableroles = get_profile_roles($context);

    // Check if the user can view this role.
    if (array_key_exists($roleid, $viewableroles)) {
        $filtersapplied[] = USER_FILTER_ROLE . ':' . $roleid;
    } else {
        $roleid = 0;
    }
}

// Default group ID.
$groupid = false;
$canaccessallgroups = has_capability('moodle/site:accessallgroups', $context);
if ($course->groupmode != NOGROUPS) {
    if ($canaccessallgroups) {
        // Change the group if the user can access all groups and has specified group in the URL.
        if ($groupparam) {
            $groupid = $groupparam;
        }
    } else {
        // Otherwise, get the user's default group.
        $groupid = groups_get_course_group($course, true);
        if ($course->groupmode == SEPARATEGROUPS && !$groupid) {
            // The user is not in the group so show message and exit.
            echo $OUTPUT->notification(get_string('notingroup'));
            echo $OUTPUT->footer();
            exit;
        }
    }
}
$hasgroupfilter = false;
$lastaccess = 0;
$searchkeywords = [];
$enrolid = 0;
$status = -1;
foreach ($filtersapplied as $filter) {
    $filtervalue = explode(':', $filter, 2);
    $value = null;
    if (count($filtervalue) == 2) {
        $key = clean_param($filtervalue[0], PARAM_INT);
        $value = clean_param($filtervalue[1], PARAM_INT);
    } else {
        // Search string.
        $key = USER_FILTER_STRING;
        $value = clean_param($filtervalue[0], PARAM_TEXT);
    }

    switch ($key) {
        case USER_FILTER_ENROLMENT:
            $enrolid = $value;
            break;
        case USER_FILTER_GROUP:
            $groupid = $value;
            $hasgroupfilter = true;
            break;
        case USER_FILTER_LAST_ACCESS:
            $lastaccess = $value;
            break;
        case USER_FILTER_ROLE:
            $roleid = $value;
            break;
        case USER_FILTER_STATUS:
            // We only accept active/suspended statuses.
            if ($value == ENROL_USER_ACTIVE || $value == ENROL_USER_SUSPENDED) {
                $status = $value;
            }
            break;
        default:
            // Search string.
            $searchkeywords[] = $value;
            break;
    }
}
// If course supports groups we may need to set a default.
if (!empty($groupid)) {
    if ($canaccessallgroups) {
        // User can access all groups, let them filter by whatever was selected.
        $filtersapplied[] = USER_FILTER_GROUP . ':' . $groupid;
    } else if (!$filterwassubmitted && $course->groupmode == VISIBLEGROUPS) {
        // If we are in a course with visible groups and the user has not submitted anything and does not have
        // access to all groups, then set a default group.
        $filtersapplied[] = USER_FILTER_GROUP . ':' . $groupid;
    } else if (!$hasgroupfilter && $course->groupmode != VISIBLEGROUPS) {
        // The user can't access all groups and has not set a group filter in a course where the groups are not visible
        // then apply a default group filter.
        $filtersapplied[] = USER_FILTER_GROUP . ':' . $groupid;
    } else if (!$hasgroupfilter) { // No need for the group id to be set.
        $groupid = false;
    }
}

if ($groupid && ($course->groupmode != SEPARATEGROUPS || $canaccessallgroups)) {
    $grouprenderer = $PAGE->get_renderer('core_group');
    $groupdetailpage = new \core_group\output\group_details($groupid);
    echo $grouprenderer->group_details($groupdetailpage);
}

// Manage enrolments.
$manager = new course_enrolment_manager($PAGE, $course);
$enrolbuttons = $manager->get_manual_enrol_buttons();
$enrolrenderer = $PAGE->get_renderer('core_enrol');
$enrolbuttonsout = '';
foreach ($enrolbuttons as $enrolbutton) {
    $enrolbuttonsout .= $enrolrenderer->render($enrolbutton);
}
echo html_writer::div($enrolbuttonsout, 'pull-right');

// Should use this variable so that we don't break stuff every time a variable is added or changed.
$baseurl = new moodle_url('/user/index.php', array(
        'contextid' => $context->id,
        'id' => $course->id,
        'perpage' => $perpage));

// Render the unified filter.
$renderer = $PAGE->get_renderer('core_user');
echo $renderer->unified_filter($course, $context, $filtersapplied, $baseurl);

echo '<div class="userlist">';

// Add filters to the baseurl after creating unified_filter to avoid losing them.
foreach (array_unique($filtersapplied) as $filterix => $filter) {
    $baseurl->param('unified-filters[' . $filterix . ']', $filter);
}
$participanttable = new \core_user\participants_table($course->id, $groupid, $lastaccess, $roleid, $enrolid, $status,
    $searchkeywords, $bulkoperations, $selectall);
$participanttable->define_baseurl($baseurl);

// Do this so we can get the total number of rows.
ob_start();
$participanttable->out($perpage, true);
$participanttablehtml = ob_get_contents();
ob_end_clean();

echo html_writer::tag('p', get_string('participantscount', 'moodle', $participanttable->totalrows));

if ($bulkoperations) {
    echo '<form action="action_redir.php" method="post" id="participantsform">';
    echo '<div>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="hidden" name="returnto" value="'.s($PAGE->url->out(false)).'" />';
}

echo $participanttablehtml;

$perpageurl = clone($baseurl);
$perpageurl->remove_params('perpage');
if ($perpage == SHOW_ALL_PAGE_SIZE && $participanttable->totalrows > DEFAULT_PAGE_SIZE) {
    $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');

} else if ($participanttable->get_page_size() < $participanttable->totalrows) {
    $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
    echo $OUTPUT->container(html_writer::link($perpageurl, get_string('showall', '', $participanttable->totalrows)),
        array(), 'showall');
}

if ($bulkoperations) {
    echo '<br /><div class="buttons">';

    if ($participanttable->get_page_size() < $participanttable->totalrows) {
        $perpageurl = clone($baseurl);
        $perpageurl->remove_params('perpage');
        $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
        $perpageurl->param('selectall', true);
        $showalllink = $perpageurl;
    } else {
        $showalllink = false;
    }

    echo html_writer::start_tag('div', array('class' => 'btn-group'));
    if ($participanttable->get_page_size() < $participanttable->totalrows) {
        // Select all users, refresh page showing all users and mark them all selected.
        $label = get_string('selectalluserswithcount', 'moodle', $participanttable->totalrows);
        echo html_writer::tag('input', "", array('type' => 'button', 'id' => 'checkall', 'class' => 'btn btn-secondary',
                'value' => $label, 'data-showallink' => $showalllink));
        // Select all users, mark all users on page as selected.
        echo html_writer::tag('input', "", array('type' => 'button', 'id' => 'checkallonpage', 'class' => 'btn btn-secondary',
        'value' => get_string('selectallusersonpage')));
    } else {
        echo html_writer::tag('input', "", array('type' => 'button', 'id' => 'checkallonpage', 'class' => 'btn btn-secondary',
        'value' => get_string('selectall')));
    }

    echo html_writer::tag('input', "", array('type' => 'button', 'id' => 'checknone', 'class' => 'btn btn-secondary',
        'value' => get_string('deselectall')));
    echo html_writer::end_tag('div');
    $displaylist = array();
    $displaylist['#messageselect'] = get_string('messageselectadd');
    if (!empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context) && $context->id != $frontpagectx->id) {
        $displaylist['#addgroupnote'] = get_string('addnewnote', 'notes');
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

    echo $OUTPUT->help_icon('withselectedusers');
    echo html_writer::tag('label', get_string("withselectedusers"), array('for' => 'formactionid'));
    echo html_writer::select($displaylist, 'formaction', '', array('' => 'choosedots'), array('id' => 'formactionid'));

    echo '<input type="hidden" name="id" value="'.$course->id.'" />';
    echo '<noscript style="display:inline">';
    echo '<div><input type="submit" value="'.get_string('ok').'" /></div>';
    echo '</noscript>';
    echo '</div></div>';
    echo '</form>';

    $options = new stdClass();
    $options->courseid = $course->id;
    $options->noteStateNames = note_get_state_names();
    $options->stateHelpIcon = $OUTPUT->help_icon('publishstate', 'notes');
    $PAGE->requires->js_call_amd('core_user/participants', 'init', [$options]);
}

echo '</div>';  // Userlist.

$enrolrenderer = $PAGE->get_renderer('core_enrol');
echo '<div class="pull-right">';
foreach ($enrolbuttons as $enrolbutton) {
    echo $enrolrenderer->render($enrolbutton);
}
echo '</div>';

if ($newcourse == 1) {
    $str = get_string('proceedtocourse', 'enrol');
    // Floated left so it goes under the enrol users button on mobile.
    // The margin is to make it line up with the enrol users button when they are both on the same line.
    $classes = 'm-y-1 pull-xs-left';
    $url = course_get_url($course);
    echo $OUTPUT->single_button($url, $str, 'GET', array('class' => $classes));
}

echo $OUTPUT->footer();
