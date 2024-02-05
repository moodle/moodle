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
 * This page handles deleting quiz overrides
 *
 * @package    mod_quiz
 * @copyright  2010 Matt Petro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\form\edit_override_form;
use mod_quiz\quiz_settings;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

$overrideid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

$override = $DB->get_record('quiz_overrides', ['id' => $overrideid], '*', MUST_EXIST);
$quizobj = quiz_settings::create($override->quiz);
$quiz = $quizobj->get_quiz();
$cm = $quizobj->get_cm();
$course = $quizobj->get_course();
$context = $quizobj->get_context();
$manager = $quizobj->get_override_manager();

require_login($course, false, $cm);

// Check the user has the required capabilities to modify an override.
$manager->require_manage_capability();

if ($override->groupid) {
    if (!groups_group_visible($override->groupid, $course, $cm)) {
        throw new \moodle_exception('invalidoverrideid', 'quiz');
    }
} else {
    if (!groups_user_groups_visible($course, $override->userid, $cm)) {
        throw new \moodle_exception('invalidoverrideid', 'quiz');
    }
}

$url = new moodle_url('/mod/quiz/overridedelete.php', ['id' => $override->id]);
$confirmurl = new moodle_url($url, ['id' => $override->id, 'confirm' => 1]);
$cancelurl = new moodle_url('/mod/quiz/overrides.php', ['cmid' => $cm->id]);

if (!empty($override->userid)) {
    $cancelurl->param('mode', 'user');
}

// If confirm is set (PARAM_BOOL) then we have confirmation of intention to delete.
if ($confirm) {
    require_sesskey();
    $manager->delete_overrides(overrides: [$override]);
    redirect($cancelurl);
}

// Prepare the page to show the confirmation form.
$stroverride = get_string('override', 'quiz');
$title = get_string('deletecheck', null, $stroverride);

$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->set_attrs([
    "title" => format_string($quiz->name, true, ['context' => $context]),
    "description" => "",
    "hidecompletion" => true
]);
echo $OUTPUT->header();

if ($override->groupid) {
    $group = $DB->get_record('groups', ['id' => $override->groupid], 'id, name');
    $confirmstr = get_string("overridedeletegroupsure", "quiz", format_string($group->name, true, ['context' => $context]));
} else {
    $user = $DB->get_record('user', ['id' => $override->userid]);
    profile_load_custom_fields($user);

    $confirmstr = get_string('overridedeleteusersure', 'quiz',
            edit_override_form::display_user_name($user,
                    \core_user\fields::get_identity_fields($context)));
}

echo $OUTPUT->confirm($confirmstr, $confirmurl, $cancelurl);

echo $OUTPUT->footer();
