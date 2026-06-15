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
 * This page handles editing and creation of assign overrides
 *
 * @package   mod_assign
 * @copyright 2016 Ilya Tregubov
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use mod_assign\override_manager;

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/mod/assign/lib.php');
require_once($CFG->dirroot.'/mod/assign/locallib.php');
require_once($CFG->dirroot.'/mod/assign/override_form.php');


$cmid = optional_param('cmid', 0, PARAM_INT);
$overrideid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);
$reset = optional_param('reset', false, PARAM_BOOL);
$userid = optional_param('userid', null, PARAM_INT);
$userchange = optional_param('userchange', false, PARAM_BOOL);

$pagetitle = get_string('editoverride', 'assign');

$override = null;
if ($overrideid) {

    if (! $override = $DB->get_record('assign_overrides', array('id' => $overrideid))) {
        throw new moodle_exception('invalidoverrideid', 'assign');
    }

    list($course, $cm) = get_course_and_cm_from_instance($override->assignid, 'assign');

} else if ($cmid) {
    list($course, $cm) = get_course_and_cm_from_cmid($cmid, 'assign');

} else {
    throw new moodle_exception('invalidcoursemodule');
}

$url = new moodle_url('/mod/assign/overrideedit.php');
if ($action) {
    $url->param('action', $action);
}
if ($overrideid) {
    $url->param('id', $overrideid);
} else {
    $url->param('cmid', $cmid);
}

$PAGE->set_show_navigation_footer(false);
$PAGE->set_url($url);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
$assign = new assign($context, $cm, $course);
$assigninstance = $assign->get_instance($userid);
$shouldadduserid = $userid && !empty($course->relativedatesmode);
$shouldresetform = optional_param('resetbutton', 0, PARAM_ALPHA) || ($userchange && $action !== 'duplicate');

// Add or edit an override.
$manager = new override_manager($assign->get_instance(), $context);
$manager->require_manage_capability();

if ($overrideid) {
    // Editing an override.
    $data = clone $override;

    if ($override->groupid) {
        if (!groups_group_visible($override->groupid, $course, $cm)) {
            throw new moodle_exception('invalidoverrideid', 'assign');
        }
    } else {
        if (!groups_user_groups_visible($course, $override->userid, $cm)) {
            throw new moodle_exception('invalidoverrideid', 'assign');
        }
    }
} else {
    // Creating a new override.
    $data = new stdClass();
}

// Merge assign defaults with data.
$keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate', 'timelimit');
foreach ($keys as $key) {
    if (!isset($data->{$key}) || $reset) {
        $data->{$key} = $assigninstance->{$key};
    }
}

// Prepare reason editor data for existing overrides.
if (!empty($override) && isset($override->reason)) {
    $data->reason_editor = [
        'text' => $override->reason,
        'format' => $override->reasonformat ?? FORMAT_MOODLE,
    ];
}

// True if group-based override.
$groupmode = !empty($data->groupid) || ($action === 'addgroup' && empty($overrideid));

// If we are duplicating an override, then clear the user/group and override id
// since they will change.
if ($action === 'duplicate') {
    $override->id = $data->id = null;
    $override->userid = $data->userid = null;
    $override->groupid = $data->groupid = null;
    $pagetitle = get_string('duplicateoverride', 'assign');
}

if ($shouldadduserid) {
    $data->userid = $userid;
}

$overridelisturl = new moodle_url('/mod/assign/overrides.php', array('cmid' => $cm->id));
if (!$groupmode) {
    $overridelisturl->param('mode', 'user');
}

// Setup the form.
$mform = new assign_override_form($url, $cm, $assign, $context, $groupmode, $override, $userid);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($overridelisturl);

} else if ($shouldresetform) {
    $url->param('reset', true);
    if ($shouldadduserid) {
        $url->param('userid', $userid);
    }
    redirect($url);

} else if (!$userchange && $fromform = $mform->get_data()) {
    // Extract reason and reasonformat from editor field.
    if (isset($fromform->reason_editor)) {
        $fromform->reason = $fromform->reason_editor['text'] ?? '';
        $fromform->reasonformat = $fromform->reason_editor['format'] ?? FORMAT_MOODLE;
        unset($fromform->reason_editor);
    }

    // Prepare data for the manager.
    $overridedata = [
        'assignid' => $assigninstance->id,
        'userid' => !empty($fromform->userid) ? $fromform->userid : null,
        'groupid' => !empty($fromform->groupid) ? $fromform->groupid : null,
        'duedate' => $fromform->duedate ?? null,
        'cutoffdate' => $fromform->cutoffdate ?? null,
        'allowsubmissionsfromdate' => $fromform->allowsubmissionsfromdate ?? null,
        'timelimit' => $fromform->timelimit ?? null,
        'reason' => $fromform->reason ?? null,
        'reasonformat' => $fromform->reasonformat ?? FORMAT_MOODLE,
    ];

    // If updating an existing override, include the ID.
    if (!empty($override->id)) {
        $overridedata['id'] = $override->id;
    }

    // Determine if we need to recalculate grades.
    $recalculate = !empty($fromform->recalculatepenalty) && $fromform->recalculatepenalty === 'yes';

    // Save the override using the manager (handles recalculation internally).
    $ids = $manager->save_overrides([$overridedata], $recalculate);
    // There will be only one ID returned.
    $overrideid = $ids[0];

    if (!empty($fromform->submitbutton)) {
        redirect($overridelisturl);
    }

    // The user pressed the 'again' button, so redirect back to this page.
    $url->remove_params('cmid');
    $url->param('action', 'duplicate');
    $url->param('id', $overrideid);
    redirect($url);

}

// Print the form.
$PAGE->navbar->add($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->set_secondary_active_tab('mod_assign_useroverrides');
$activityheader = $PAGE->activityheader;
$activityheader->set_attrs([
    'description' => '',
    'hidecompletion' => true,
    'title' => $activityheader->is_title_allowed() ? format_string($assigninstance->name, true, ['context' => $context]) : ""
]);
echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
