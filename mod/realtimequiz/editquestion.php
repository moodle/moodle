<?php
// This file is part of the Realtime Quiz plugin for Moodle - http://moodle.org/
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
 * Edit a single question
 *
 * @package   mod_realtimequiz
 * @copyright 2013 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
global $DB, $CFG, $OUTPUT, $PAGE;
require_once($CFG->dirroot.'/mod/realtimequiz/editquestion_form.php');

$quizid = required_param('quizid', PARAM_INT);
$questionid = optional_param('questionid', 0, PARAM_INT);
$numanswers = optional_param('numanswers', 4, PARAM_INT);
$addanswers = optional_param('addanswers', false, PARAM_BOOL);
if ($addanswers) {
    $numanswers += 3;
}

$quiz = $DB->get_record('realtimequiz', ['id' => $quizid], '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('realtimequiz', $quiz->id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

$url = new moodle_url('/mod/realtimequiz/editquestion.php', ['quizid' => $quizid]);
if ($questionid) {
    $url->param('questionid', $questionid);
}
$PAGE->set_url($url);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/realtimequiz:editquestions', $context);

if ($questionid) {
    $question = $DB->get_record('realtimequiz_question', ['id' => $questionid, 'quizid' => $quizid], '*', MUST_EXIST);
    $question->questionid = $question->id;
    $question->answers = $DB->get_records('realtimequiz_answer', ['questionid' => $question->id], 'id');
    $question->answercorrect = 0;
    $question->answertext = [];
    $question->answerid = [];
    $i = 1;
    foreach ($question->answers as $answer) {
        if ($answer->correct) {
            $question->answercorrect = $i;
        }
        $question->answertext[$i] = $answer->answertext;
        $question->answerid[$i] = $answer->id;
        $i++;
    }
    $heading = get_string('edittingquestion', 'mod_realtimequiz');
} else {
    $question = new stdClass();
    $question->id = 0;
    $question->quizid = $quiz->id;
    $question->questionnum = $DB->count_records('realtimequiz_question', ['quizid' => $quiz->id]) + 1;
    $question->questiontext = '';
    $question->questiontextformat = FORMAT_HTML;
    $question->questiontime = 0;
    $question->answers = [];
    $question->answercorrect = 1;
    $heading = get_string('addingquestion', 'mod_realtimequiz');
}

$maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes);
$editoroptions = [
    'subdirs' => false, 'maxbytes' => $maxbytes, 'maxfiles' => -1,
    'changeformat' => 0, 'context' => $context, 'noclean' => 0,
    'trusttext' => true,
];

$numanswers = max(count($question->answers), $numanswers);
$form = new realtimequiz_editquestion_form(null, ['editoroptions' => $editoroptions, 'numanswers' => $numanswers]);

$question = file_prepare_standard_editor($question, 'questiontext', $editoroptions, $context,
                                         'mod_realtimequiz', 'question', $question->id);
$form->set_data($question);

$return = new moodle_url('/mod/realtimequiz/edit.php', ['quizid' => $quiz->id]);
if ($form->is_cancelled()) {
    redirect($return);
}

if ($data = $form->get_data()) {
    if (isset($data->save) || isset($data->saveadd)) {
        $updquestion = (object)[
            'quizid' => $quizid,
            'questionnum' => $question->questionnum,
            'questiontext' => 'toupdate',
            'questiontextformat' => FORMAT_HTML,
            'questiontime' => $data->questiontime,
        ];

        if (!empty($question->id)) {
            $updquestion->id = $question->id;
        } else {
            $updquestion->id = $DB->insert_record('realtimequiz_question', $updquestion);
        }

        // Save the attached files (now we know we have got a question id).
        $data = file_postupdate_standard_editor($data, 'questiontext', $editoroptions, $context, 'mod_realtimequiz',
                                                'question', $updquestion->id);
        $updquestion->questiontext = $data->questiontext;
        $updquestion->questiontextformat = $data->questiontextformat;

        $DB->update_record('realtimequiz_question', $updquestion);

        // Save each of the answers.
        foreach ($data->answertext as $pos => $answertext) {
            $updanswer = new stdClass();
            $updanswer->answertext = $answertext;
            $updanswer->correct = ($pos == $data->answercorrect) ? 1 : 0;
            if (!empty($question->answerid[$pos])) {
                $updanswer->id = $question->answerid[$pos];
                $oldanswer = $question->answers[$updanswer->id];
                $changed = $updanswer->answertext != $oldanswer->answertext;
                $changed = $changed || $updanswer->correct != $oldanswer->correct;
                if ($changed) {
                    if ($updanswer->answertext === "") {
                        $DB->delete_records('realtimequiz_answer', ['id' => $updanswer->id]);
                    } else {
                        $DB->update_record('realtimequiz_answer', $updanswer);
                    }
                }
            } else if ($updanswer->answertext !== "") {
                $updanswer->questionid = $updquestion->id;
                $updanswer->id = $DB->insert_record('realtimequiz_answer', $updanswer);
            }
        }

        if (isset($data->saveadd)) {
            redirect(new moodle_url('/mod/realtimequiz/editquestion.php', ['quizid' => $quizid]));
        }

        redirect($return);
    }
}

$jsmodule = [
    'name' => 'mod_realtimequiz',
    'fullpath' => new moodle_url('/mod/realtimequiz/editquestions.js'),
];
$PAGE->requires->js_init_call('M.mod_realtimequiz.init_editpage', [], false, $jsmodule);

$PAGE->set_heading($heading.$question->questionnum);
$PAGE->set_title(get_string('pluginname', 'mod_realtimequiz'));

echo $OUTPUT->header();
if ($CFG->branch < 400) {
    echo $OUTPUT->heading($heading.$question->questionnum);
}

$form->display();

echo $OUTPUT->footer();
