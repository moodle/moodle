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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$slotid = required_param('slotid', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Get the quiz slot.
$slot = $DB->get_record('quiz_slots', ['id' => $slotid]);
if (!$slot) {
    new moodle_exception('invalidrandomslot', 'mod_quiz');
}

if (!$quiz = $DB->get_record('quiz', ['id' => $slot->quizid])) {
    new moodle_exception('invalidquizid', 'quiz');
}

$cm = get_coursemodule_from_instance('quiz', $slot->quizid, $quiz->course);

require_login($cm->course, false, $cm);

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
$filterconditions = json_decode($setreference->filtercondition);

// Validate the question category.
if (!$category = $DB->get_record('question_categories', ['id' => $filterconditions->questioncategoryid])) {
    new moodle_exception('categorydoesnotexist', 'question', $returnurl);
}

// Check permissions.
$catcontext = context::instance_by_id($category->contextid);
require_capability('moodle/question:useall', $catcontext);

$thiscontext = context_module::instance($cm->id);
$contexts = new core_question\local\bank\question_edit_contexts($thiscontext);

// Create the editing form.
$mform = new mod_quiz\form\randomquestion_form(new moodle_url('/mod/quiz/editrandom.php'), ['contexts' => $contexts]);

// Set the form data.
$toform = new stdClass();
$toform->category = "{$category->id},{$category->contextid}";
$toform->includesubcategories = $filterconditions->includingsubcategories;
$toform->fromtags = array();
if (isset($filterconditions->tags)) {
    $currentslottags = $filterconditions->tags;
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

// Display a heading, question editing form.
echo $OUTPUT->header();
$heading = get_string('randomediting', 'mod_quiz');
echo $OUTPUT->heading_with_help($heading, 'randomquestion', 'mod_quiz');

$mform->display();

echo $OUTPUT->footer();
