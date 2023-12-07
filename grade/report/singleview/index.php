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

define('NO_OUTPUT_BUFFERING', true);

require_once('../../../config.php');
require_once($CFG->dirroot.'/lib/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/report/lib.php');

$courseid = required_param('id', PARAM_INT);
$groupid  = optional_param('group', null, PARAM_INT);

// Making this work with profile reports.
$userid   = optional_param('userid', null, PARAM_INT);
$itemid = optional_param('itemid', null, PARAM_INT);
$itemtype = optional_param('item', null, PARAM_TEXT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 100, PARAM_INT);

$edit = optional_param('edit', -1, PARAM_BOOL); // Sticky editing mode.

$courseparams = ['id' => $courseid];

$PAGE->set_pagelayout('report');
$PAGE->set_other_editing_capability('moodle/grade:edit');

if (!$course = $DB->get_record('course', $courseparams)) {
    throw new \moodle_exception('invalidcourseid');
}

require_login($course);

$context = context_course::instance($course->id);

// This is the normal requirements.
require_capability('gradereport/singleview:view', $context);
require_capability('moodle/grade:viewall', $context);
require_capability('moodle/grade:edit', $context);

$gpr = new grade_plugin_return([
    'type' => 'report',
    'plugin' => 'singleview',
    'courseid' => $courseid
]);

// Last selected report session tracking.
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = [];
}
$USER->grade_last_report[$course->id] = 'singleview';
// If the item type is not explicitly defined or not valid, try to use the last viewed one (obtain in from the session)
// or fallback to the user select (zero) state.
if (!$itemtype || !in_array($itemtype, \gradereport_singleview\report\singleview::valid_screens())) {
    $itemtype = isset($SESSION->gradereport_singleview["itemtype-{$context->id}"]) ?
        $SESSION->gradereport_singleview["itemtype-{$context->id}"] : 'user_select';
}

$currentgroup = $gpr->groupid;
// To make some other functions work better later.
if (!$currentgroup) {
    $currentgroup = null;
}

$lastvieweduseritemid = $SESSION->gradereport_singleview["useritem-{$context->id}"] ?? null;
$lastviewedgradeitemid = $SESSION->gradereport_singleview["gradeitem-{$context->id}"] ?? null;

switch ($itemtype) {
    case 'user_select':
        // If there is a stored user item (last viewed) in a session variable, bypass the user select zero state
        // and display this user item. Also, make sure that the stored last viewed user is part of the current
        // list of gradable users in this course.
        if ($lastvieweduseritemid &&
                array_key_exists($lastvieweduseritemid, grade_report::get_gradable_users($courseid, $currentgroup))) {
            $itemtype = 'user';
            $itemid = $lastvieweduseritemid;
        } else {
            $itemid = null;
        }
        break;
    case 'user':
        if (is_null($itemid)) {
            $itemid = $userid ?? $lastvieweduseritemid;
        }
        // If the item id (user id) cannot be defined or the user id is not part of the list of gradable users,
        // display the user select zero state.
        if (is_null($itemid) || !array_key_exists($itemid, grade_report::get_gradable_users($courseid, $currentgroup))) {
            $itemtype = 'user_select';
        }
        break;
    case 'grade_select':
        // If there is a stored grade item (last viewed) in a session variable, bypass the grade item select zero state
        // and display this grade item.
        if ($lastviewedgradeitemid) {
            $itemtype = 'grade';
            $itemid = $lastviewedgradeitemid;
        } else {
            $itemid = null;
        }
        break;
    case 'grade':
        // If there is a stored grade item (last viewed) in a session variable, use it.
        if (is_null($itemid) && $lastviewedgradeitemid) {
            $itemid = $lastviewedgradeitemid;
        }
        $gtree = new grade_tree($courseid, false, false, null, !$CFG->enableoutcomes);
        $gradeableitems = $gtree->get_items();
        // The item id (grade item id) cannot be defined, display the grade select zero state.
        if (is_null($itemid) || !array_key_exists($itemid, $gtree->get_items())) {
            $itemtype = 'grade_select';
        }
        break;
}

$report = new gradereport_singleview\report\singleview($courseid, $gpr, $context, $itemtype, $itemid);

$pageparams = [
    'id'        => $courseid,
    'userid'    => $userid,
    'itemid'    => $itemid,
    'item'      => $itemtype,
    'page'      => $page,
    'perpage'   => $perpage,
];

if (!is_null($groupid)) {
    $pageparams['group'] = $groupid;
}

$PAGE->set_url(new moodle_url('/grade/report/singleview/index.php', $pageparams));

// Build editing on/off button for themes that need it.
$button = '';
if ($PAGE->user_allowed_editing() && !$PAGE->theme->haseditswitch) {
    if ($edit != - 1) {
        $USER->editing = $edit;
    }

    // Page params for the turn editing on button.
    $options = $gpr->get_options();
    $button = $OUTPUT->edit_button(new moodle_url($PAGE->url, $options), 'get');
}

$reportname = $report->screen->heading();

if ($itemtype == 'user' || $itemtype == 'user_select') {
    $actionbar = new \gradereport_singleview\output\action_bar($context, $report, 'user');
} else if ($itemtype == 'grade' || $itemtype == 'grade_select') {
    $actionbar = new \gradereport_singleview\output\action_bar($context, $report, 'grade');
} else {
    $actionbar = new \core_grades\output\general_action_bar($context, new moodle_url('/grade/report/singleview/index.php',
        ['id' => $courseid]), 'report', 'singleview');
}

if ($itemtype == 'user') {
    print_grade_page_head($course->id, 'report', 'singleview', $reportname, false, $button,
        true, null, null, $report->screen->item, $actionbar);
} else {
    print_grade_page_head($course->id, 'report', 'singleview', $reportname, false, $button,
        true, null, null, null, $actionbar);
}

if ($data = data_submitted()) {
    // Must have a sesskey for all actions.
    require_sesskey();
    $result = $report->process_data($data);

    // If result is not null (because somedata was processed), warnings and success message should be displayed.
    if (!is_null($result)) {
        if (!empty($result->warnings)) {
            foreach ($result->warnings as $warning) {
                \core\notification::add($warning);
            }
        }

        // And notify the user of the success result.
        \core\notification::add(
            get_string('savegradessuccess', 'gradereport_singleview', count((array) $result->changecount)),
            \core\notification::SUCCESS
        );
    }
}

// Make sure we have proper final grades.
grade_regrade_final_grades_if_required($course);

echo $report->output();
// Save the screen state in a session variable as last viewed state.
$SESSION->gradereport_singleview["itemtype-{$context->id}"] = $itemtype;
if ($itemid) {
    $SESSION->gradereport_singleview["{$itemtype}item-{$context->id}"] = $itemid;
}

if (($itemtype !== 'select') && ($itemtype !== 'grade_select') &&($itemtype !== 'user_select')) {
    $item = (isset($userid)) ? $userid : $itemid;

    $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
    $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
    $showonlyactiveenrol = $showonlyactiveenrol || !has_capability('moodle/course:viewsuspendedusers', $context);

    $gui = new graded_users_iterator($course, null, $currentgroup);
    $gui->require_active_enrolment($showonlyactiveenrol);
    $gui->init();

    $userreportrenderer = $PAGE->get_renderer('gradereport_singleview');
    // Add previous/next user navigation.
    echo $userreportrenderer->report_navigation($gpr, $courseid, $context, $report, $groupid, $itemtype, $itemid);
}

$event = \gradereport_singleview\event\grade_report_viewed::create(
    [
        'context' => $context,
        'courseid' => $courseid,
        'relateduserid' => $USER->id,
    ]
);
$event->trigger();

echo $OUTPUT->footer();
