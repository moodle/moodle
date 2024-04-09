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
 * Page to edit complex grading setups for quizzes
 *
 * For quizzes with basic grading, everything can be done on edit.php.
 * However, more advanced options are possible, if you come to this
 * separate page.
 *
 * @package   mod_quiz
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\output\edit_grading_page;
use mod_quiz\output\edit_nav_actions;
use mod_quiz\quiz_settings;

// The require_login check is done in question_edit_setup, but the automated checker can't see this.
// phpcs:ignore moodle.Files.RequireLogin.Missing
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/editlib.php');

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', '/mod/quiz/editgrading.php', true);

$PAGE->set_url($thispageurl);
$PAGE->set_secondary_active_tab('mod_quiz_edit');

// You need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

// Get the course object and related bits.
$course = get_course($quiz->course);
$quizobj = new quiz_settings($quiz, $cm, $course);
$structure = $quizobj->get_structure();
$editpage = new edit_grading_page($structure);

$quizhasattempts = quiz_has_attempts($quiz->id);

// Initialise output.
$PAGE->set_pagelayout('incourse');

$output = $PAGE->get_renderer('mod_quiz', 'edit');

$PAGE->set_title(get_string('editingquizx', 'quiz', format_string($quiz->name)));
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();
$PAGE->set_secondary_active_tab('mod_quiz_edit');
$tertiarynav = new edit_nav_actions($cmid, edit_nav_actions::GRADING);

$PAGE->requires->js_call_amd('mod_quiz/edit_multiple_grades', 'init');

// Do output.
echo $output->header();
echo $output->render($tertiarynav);
echo $output->render($editpage);
echo $output->footer();
