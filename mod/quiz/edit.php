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
 * Page to edit quizzes
 *
 * This page generally has two columns:
 * The right column lists all available questions in a chosen category and
 * allows them to be edited or more to be added. This column is only there if
 * the quiz does not already have student attempts
 * The left column lists all questions that have been added to the current quiz.
 * The lecturer can add questions from the right hand list to the quiz or remove them
 *
 * The script also processes a number of actions:
 * Actions affecting a quiz:
 * up and down  Changes the order of questions and page breaks
 * addquestion  Adds a single question to the quiz
 * add          Adds several selected questions to the quiz
 * addrandom    Adds a certain number of random questions to the quiz
 * repaginate   Re-paginates the quiz
 * delete       Removes a question from the quiz
 * savechanges  Saves the order and grades for questions in the quiz
 *
 * @package    mod_quiz
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\quiz_settings;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/question/editlib.php');

$mdlscrollto = optional_param('mdlscrollto', '', PARAM_INT);

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
    question_edit_setup('editq', '/mod/quiz/edit.php', true);

$PAGE->set_url($thispageurl);
$PAGE->set_secondary_active_tab("mod_quiz_edit");

// You need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

// Get the course object and related bits.
$course = get_course($quiz->course);
$quizobj = new quiz_settings($quiz, $cm, $course);
$structure = $quizobj->get_structure();
$gradecalculator = $quizobj->get_grade_calculator();

$defaultcategoryobj = question_make_default_categories($contexts->all());
$defaultcategory = $defaultcategoryobj->id . ',' . $defaultcategoryobj->contextid;

$quizhasattempts = quiz_has_attempts($quiz->id);

// Process commands ============================================================.

// Get the list of question ids had their check-boxes ticked.
$selectedslots = [];
$params = (array) data_submitted();
foreach ($params as $key => $value) {
    if (preg_match('!^s([0-9]+)$!', $key, $matches)) {
        $selectedslots[] = $matches[1];
    }
}

$afteractionurl = new moodle_url($thispageurl);

if (optional_param('repaginate', false, PARAM_BOOL) && confirm_sesskey()) {
    // Re-paginate the quiz.
    $structure->check_can_be_edited();
    $questionsperpage = optional_param('questionsperpage', $quiz->questionsperpage, PARAM_INT);
    quiz_repaginate_questions($quiz->id, $questionsperpage );
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

if ($mdlscrollto) {
    $afteractionurl->param('mdlscrollto', $mdlscrollto);
}

if (($addquestion = optional_param('addquestion', 0, PARAM_INT)) && confirm_sesskey()) {
    // Add a single question to the current quiz.
    $structure->check_can_be_edited();
    quiz_require_question_use($addquestion);
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    quiz_add_quiz_question($addquestion, $quiz, $addonpage);
    quiz_delete_previews($quiz);
    $gradecalculator->recompute_quiz_sumgrades();
    $thispageurl->param('lastchanged', $addquestion);
    redirect($afteractionurl);
}

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    $structure->check_can_be_edited();
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    // Add selected questions to the current quiz.
    $rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) { // Parse input for question ids.
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
            quiz_require_question_use($key);
            quiz_add_quiz_question($key, $quiz, $addonpage);
        }
    }
    quiz_delete_previews($quiz);
    $gradecalculator->recompute_quiz_sumgrades();
    redirect($afteractionurl);
}

if ($addsectionatpage = optional_param('addsectionatpage', false, PARAM_INT)) {
    // Add a section to the quiz.
    $structure->check_can_be_edited();
    $structure->add_section_heading($addsectionatpage);
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

if ((optional_param('addrandom', false, PARAM_BOOL)) && confirm_sesskey()) {
    // Add random questions to the quiz.
    $structure->check_can_be_edited();
    $recurse = optional_param('recurse', 0, PARAM_BOOL);
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    $categoryid = required_param('categoryid', PARAM_INT);
    $randomcount = required_param('randomcount', PARAM_INT);
    quiz_add_random_questions($quiz, $addonpage, $categoryid, $randomcount, $recurse);

    quiz_delete_previews($quiz);
    $gradecalculator->recompute_quiz_sumgrades();
    redirect($afteractionurl);
}

if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {

    // If rescaling is required save the new maximum.
    $maxgrade = unformat_float(optional_param('maxgrade', '', PARAM_RAW_TRIMMED), true);
    if (is_float($maxgrade) && $maxgrade >= 0) {
        $gradecalculator->update_quiz_maximum_grade($maxgrade);
    }

    redirect($afteractionurl);
}

// Log this visit.
$event = \mod_quiz\event\edit_page_viewed::create([
    'courseid' => $course->id,
    'context' => $contexts->lowest(),
    'other' => [
        'quizid' => $quiz->id
    ]
]);
$event->trigger();

// End of process commands =====================================================.

$PAGE->set_pagelayout('incourse');
$PAGE->set_pagetype('mod-quiz-edit');

$output = $PAGE->get_renderer('mod_quiz', 'edit');

$PAGE->set_title(get_string('editingquizx', 'quiz', format_string($quiz->name)));
$PAGE->set_heading($course->fullname);
$PAGE->activityheader->disable();
$node = $PAGE->settingsnav->find('mod_quiz_edit', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}

// Add random question - result message.
if ($message = optional_param('message', '', PARAM_TEXT)) {
    core\notification::add($message, core\notification::SUCCESS);
}

echo $OUTPUT->header();
// Initialise the JavaScript.
$quizeditconfig = new stdClass();
$quizeditconfig->url = $thispageurl->out(true, ['qbanktool' => '0']);
$quizeditconfig->dialoglisteners = [];
$numberoflisteners = $DB->get_field_sql("
    SELECT COALESCE(MAX(page), 1)
      FROM {quiz_slots}
     WHERE quizid = ?", [$quiz->id]);

for ($pageiter = 1; $pageiter <= $numberoflisteners; $pageiter++) {
    $quizeditconfig->dialoglisteners[] = 'addrandomdialoglaunch_' . $pageiter;
}

$PAGE->requires->data_for_js('quiz_edit_config', $quizeditconfig);
$PAGE->requires->js_call_amd('core_question/question_engine');

// Questions wrapper start.
echo html_writer::start_tag('div', ['class' => 'mod-quiz-edit-content']);

echo $output->edit_page($quizobj, $structure, $contexts, $thispageurl, $pagevars);

// Questions wrapper end.
echo html_writer::end_tag('div');

echo $OUTPUT->footer();
