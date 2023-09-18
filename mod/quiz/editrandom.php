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
 * Page for editing random questions.
 *
 * @package    mod_quiz
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_quiz\quiz_settings;
use mod_quiz\question\bank\random_question_view;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/mod/quiz/lib.php');

$slotid = required_param('slotid', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Get the quiz slot.
$slot = $DB->get_record('quiz_slots', ['id' => $slotid], '*', MUST_EXIST);
$quizobj = quiz_settings::create($slot->quizid);
$quiz = $quizobj->get_quiz();
$cm = $quizobj->get_cm();
$course = $quizobj->get_course();

require_login($course, false, $cm);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/mod/quiz/edit.php', ['cmid' => $cm->id]);
}

$url = new moodle_url('/mod/quiz/editrandom.php', ['slotid' => $slotid]);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->add_body_class('limitedwidth');

$setreference = $DB->get_record('question_set_references',
    ['itemid' => $slot->id, 'component' => 'mod_quiz', 'questionarea' => 'slot']);
$filterconditions = json_decode($setreference->filtercondition, true);

$params = $filterconditions;
$params['cmid'] = $cm->id;
$extraparams['view'] = random_question_view::class;

// Build required parameters.
[$contexts, $thispageurl, $cm, $pagevars, $extraparams] = build_required_parameters_for_custom_view($params, $extraparams);

$thiscontext = $quizobj->get_context();
$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);

// Create the editing form.
$mform = new mod_quiz\form\randomquestion_form(new moodle_url('/mod/quiz/editrandom.php'), ['contexts' => $contexts]);

// Set the form data.
$toform = new stdClass();
$toform->category = $filterconditions['filter']['category']['values'][0];
$includesubcategories = false;
if (!empty($filterconditions['filter']['category']['filteroptions']['includesubcategories'])) {
    $includesubcategories = true;
}
$toform->includesubcategories = $includesubcategories;
$toform->fromtags = [];
if (isset($filterconditions['tags'])) {
    $currentslottags = $filterconditions['tags'];
    foreach ($currentslottags as $slottag) {
        $toform->fromtags[] = $slottag;
    }
}

$toform->returnurl = $returnurl;
$toform->slotid = $slot->id;
if ($cm !== null) {
    $toform->cmid = $cm->id;
    $toform->courseid = $cm->course;
} else {
    $toform->courseid = $COURSE->id;
}

$mform->set_data($toform);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {
    list($newcatid, $newcontextid) = explode(',', $fromform->category);
    if ($newcatid != $category->id) {
        $contextid = $newcontextid;
    } else {
        $contextid = $category->contextid;
    }
    $setreference->questionscontextid = $contextid;

    // Set the filter conditions.
    $filtercondition = new stdClass();
    $filtercondition->questioncategoryid = $newcatid;
    $filtercondition->includingsubcategories = $fromform->includesubcategories;

    if (isset($fromform->fromtags)) {
        $tags = [];
        foreach ($fromform->fromtags as $tagstring) {
            list($tagid, $tagname) = explode(',', $tagstring);
            $tags[] = "{$tagid},{$tagname}";
        }
        if (!empty($tags)) {
            $filtercondition->tags = $tags;
        }
    }

    $setreference->filtercondition = json_encode($filtercondition);
    $DB->update_record('question_set_references', $setreference);

    redirect($returnurl);
}

$PAGE->set_title('Random question');
$PAGE->set_heading($COURSE->fullname);
$PAGE->navbar->add('Random question');

// Custom View.
$questionbank = new random_question_view($contexts, $thispageurl, $course, $cm, $params, $extraparams);

// Output.
$renderer = $PAGE->get_renderer('mod_quiz', 'edit');
$data = new \stdClass();
$data->questionbank = $renderer->question_bank_contents($questionbank, $params);
$data->cmid = $cm->id;
$data->slotid = $slot->id;
$data->returnurl = $returnurl;
$updateform = $OUTPUT->render_from_template('mod_quiz/update_filter_condition_form', $data);
$PAGE->requires->js_call_amd('mod_quiz/update_random_question_filter_condition', 'init');

// Display a heading, question editing form.
echo $OUTPUT->header();
$heading = get_string('randomediting', 'mod_quiz');
echo $OUTPUT->heading_with_help($heading, 'randomquestion', 'mod_quiz');
echo $updateform;
echo $OUTPUT->footer();
