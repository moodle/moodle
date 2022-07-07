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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$slotid = required_param('slotid', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);

// Get the quiz slot.
$slot = $DB->get_record('quiz_slots', array('id' => $slotid));
if (!$slot || empty($slot->questioncategoryid)) {
    print_error('invalidrandomslot', 'mod_quiz');
}

if (!$quiz = $DB->get_record('quiz', array('id' => $slot->quizid))) {
    print_error('invalidquizid', 'quiz');
}

$cm = get_coursemodule_from_instance('quiz', $slot->quizid, $quiz->course);

require_login($cm->course, false, $cm);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/mod/quiz/edit.php', array('cmid' => $cm->id));
}

$url = new moodle_url('/mod/quiz/editrandom.php', array('slotid' => $slotid));
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');

if (!$question = $DB->get_record('question', array('id' => $slot->questionid))) {
    print_error('questiondoesnotexist', 'question', $returnurl);
}

$qtypeobj = question_bank::get_qtype('random');

// Validate the question category.
if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
    print_error('categorydoesnotexist', 'question', $returnurl);
}

// Check permissions.
question_require_capability_on($question, 'edit');

$thiscontext = context_module::instance($cm->id);
$contexts = new question_edit_contexts($thiscontext);

// Create the question editing form.
$mform = new mod_quiz\form\randomquestion_form(new moodle_url('/mod/quiz/editrandom.php'),
        array('contexts' => $contexts));

// Send the question object and a few more parameters to the form.
$toform = fullclone($question);
$toform->category = "{$category->id},{$category->contextid}";
$toform->includesubcategories = $slot->includingsubcategories;
$toform->fromtags = array();
$currentslottags = quiz_retrieve_slot_tags($slot->id);
foreach ($currentslottags as $slottag) {
    $toform->fromtags[] = "{$slottag->tagid},{$slottag->tagname}";
}
$toform->returnurl = $returnurl;

if ($cm !== null) {
    $toform->cmid = $cm->id;
    $toform->courseid = $cm->course;
} else {
    $toform->courseid = $COURSE->id;
}

$toform->slotid = $slotid;

$mform->set_data($toform);

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {

    // If we are moving a question, check we have permission to move it from
    // whence it came. Where we are moving to is validated by the form.
    list($newcatid, $newcontextid) = explode(',', $fromform->category);
    if (!empty($question->id) && $newcatid != $question->category) {
        $contextid = $newcontextid;
        question_require_capability_on($question, 'move');
    } else {
        $contextid = $category->contextid;
    }

    $question = $qtypeobj->save_question($question, $fromform);

    // We need to save some data into the quiz_slots table.
    $slot->questioncategoryid = $fromform->category;
    $slot->includingsubcategories = $fromform->includesubcategories;

    $DB->update_record('quiz_slots', $slot);

    $tags = [];
    foreach ($fromform->fromtags as $tagstring) {
        list($tagid, $tagname) = explode(',', $tagstring);
        $tags[] = (object) [
            'id' => $tagid,
            'name' => $tagname
        ];
    }

    $recordstokeep = [];
    $recordstoinsert = [];
    $searchableslottags = array_map(function($slottag) {
        return ['tagid' => $slottag->tagid, 'tagname' => $slottag->tagname];
    }, $currentslottags);

    foreach ($tags as $tag) {
        if ($key = array_search(['tagid' => $tag->id, 'tagname' => $tag->name], $searchableslottags)) {
            // If found, $key would be the id field in the quiz_slot_tags table.
            // Therefore, there was no need to check !== false here.
            $recordstokeep[] = $key;
        } else {
            $recordstoinsert[] = (object)[
                'slotid' => $slot->id,
                'tagid' => $tag->id,
                'tagname' => $tag->name
            ];
        }
    }

    // Now, delete the remaining records.
    if (!empty($recordstokeep)) {
        list($select, $params) = $DB->get_in_or_equal($recordstokeep, SQL_PARAMS_QM, 'param', false);
        array_unshift($params, $slot->id);
        $DB->delete_records_select('quiz_slot_tags', "slotid = ? AND id $select", $params);
    } else {
        $DB->delete_records('quiz_slot_tags', array('slotid' => $slot->id));
    }

    // And now, insert the extra records if there is any.
    if (!empty($recordstoinsert)) {
        $DB->insert_records('quiz_slot_tags', $recordstoinsert);
    }

    // Purge this question from the cache.
    question_bank::notify_question_edited($question->id);

    $returnurl->param('lastchanged', $question->id);
    redirect($returnurl);
}

$streditingquestion = $qtypeobj->get_heading();
$PAGE->set_title($streditingquestion);
$PAGE->set_heading($COURSE->fullname);
$PAGE->navbar->add($streditingquestion);

// Display a heading, question editing form and possibly some extra content needed for
// for this question type.
echo $OUTPUT->header();
$heading = get_string('randomediting', 'mod_quiz');
echo $OUTPUT->heading_with_help($heading, 'randomquestion', 'mod_quiz');

$mform->display();

echo $OUTPUT->footer();
