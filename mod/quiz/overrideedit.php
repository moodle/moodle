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
 * This page handles editing and creation of quiz overrides
 *
 * @package   mod_quiz
 * @copyright 2010 Matt Petro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\form\edit_override_form;
use mod_quiz\quiz_settings;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

$cmid = optional_param('cmid', 0, PARAM_INT);
$overrideid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);
$reset = optional_param('reset', false, PARAM_BOOL);

$override = null;
if ($overrideid) {
    $override = $DB->get_record('quiz_overrides', ['id' => $overrideid], '*', MUST_EXIST);
    $quizobj = quiz_settings::create($override->quiz);
} else {
    $quizobj = quiz_settings::create_for_cmid($cmid);
}

$quiz = $quizobj->get_quiz();
$cm = $quizobj->get_cm();
$course = $quizobj->get_course();
$context = $quizobj->get_context();
$manager = $quizobj->get_override_manager();

$url = new moodle_url('/mod/quiz/overrideedit.php');
if ($action) {
    $url->param('action', $action);
}
if ($overrideid) {
    $url->param('id', $overrideid);
} else {
    $url->param('cmid', $cmid);
}

$PAGE->set_url($url);

// Activate the secondary nav tab.
$PAGE->set_secondary_active_tab("mod_quiz_useroverrides");

require_login($course, false, $cm);

// Add or edit an override.
$manager->require_manage_capability();

if ($overrideid) {
    // Editing an override.
    $data = clone $override;

    if ($override->groupid) {
        if (!groups_group_visible($override->groupid, $course, $cm)) {
            throw new \moodle_exception('invalidoverrideid', 'quiz');
        }
    } else {
        if (!groups_user_groups_visible($course, $override->userid, $cm)) {
            throw new \moodle_exception('invalidoverrideid', 'quiz');
        }
    }
} else {
    // Creating a new override.
    $data = new stdClass();
}

// Merge quiz defaults with data.
$keys = ['timeopen', 'timeclose', 'timelimit', 'attempts', 'password'];
foreach ($keys as $key) {
    if (!isset($data->{$key}) || $reset) {
        $data->{$key} = $quiz->{$key};
    }
}

// If we are duplicating an override, then clear the user/group and override id
// since they will change.
if ($action === 'duplicate') {
    $override->id = null;
    $override->userid = null;
    $override->groupid = null;
}

// True if group-based override.
$groupmode = !empty($data->groupid) || ($action === 'addgroup' && empty($overrideid));

$overridelisturl = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $cm->id]);
if (!$groupmode) {
    $overridelisturl->param('mode', 'user');
}

// Setup the form.
$mform = new edit_override_form($url, $cm, $quiz, $context, $groupmode, $override);
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($overridelisturl);

} else if (optional_param('resetbutton', 0, PARAM_ALPHA)) {
    $url->param('reset', true);
    redirect($url);

} else if ($fromform = $mform->get_data()) {
    // Only include id when editing (i.e. action is empty).
    if (empty($action) && !empty($overrideid)) {
        $fromform->id = $overrideid;
    }

    // Process the data.
    $id = $manager->save_override((array) $fromform);

    if (!empty($fromform->submitbutton)) {
        redirect($overridelisturl);
    }

    // The user pressed the 'again' button, so redirect back to this page.
    $url->remove_params('cmid');
    $url->param('action', 'duplicate');
    $url->param('id', $id);
    redirect($url);

}

// Print the form.
$pagetitle = get_string('editoverride', 'quiz');
$PAGE->navbar->add($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->set_attrs([
    "title" => format_string($quiz->name, true, ['context' => $context]),
    "description" => "",
    "hidecompletion" => true
]);
echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
