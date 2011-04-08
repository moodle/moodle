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

//////////////////
///   ESSAY   ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class question_essay_qtype extends default_questiontype {

    function name() {
        return 'essay';
    }

    function is_manual_graded() {
        return true;
    }

    function save_question_options($question) {
        global $DB;
        $context = $question->context;

        $answer = $DB->get_record('question_answers', array('question' => $question->id));
        if (!$answer) {
            $answer = new stdClass;
            $answer->question = $question->id;
            $answer->answer = '';
            $answer->feedback = '';
            $answer->id = $DB->insert_record('question_answers', $answer);
        }

        $answer->feedback = $question->feedback['text'];
        $answer->feedbackformat = $question->feedback['format'];
        $answer->answer = $answer->feedback;
        $answer->answerformat = $question->feedback['format'];
        $answer->fraction = $question->fraction;

        $answer->feedback = $this->import_or_save_files($question->feedback,
                $context, 'question', 'answerfeedback', $answer->id);
        $answer->answer = $answer->feedback;
        $DB->update_record('question_answers', $answer);

        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;

        $context = $this->get_context_by_category_id($question->category);

        $answers  = &$question->options->answers;
        $readonly = empty($options->readonly) ? '' : 'disabled="disabled"';

        // Only use the rich text editor for the first essay question on a page.

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para    = false;

        $inputname = $question->name_prefix;
        $stranswer = get_string("answer", "quiz").': ';

        /// set question text and media
        $questiontext = format_text($question->questiontext,
                                   $question->questiontextformat,
                                   $formatoptions, $cmoptions->course);

        // feedback handling
        $feedback = '';
        if ($options->feedback && !empty($answers)) {
            foreach ($answers as $answer) {
                $feedback = quiz_rewrite_question_urls($answer->feedback, 'pluginfile.php',
                        $context->id, 'question', 'answerfeedback', array($state->attempt, $state->question), $answer->id);
                $feedback = format_text($feedback, $answer->feedbackformat, $formatoptions, $cmoptions->course);
            }
        }

        // get response value
        if (isset($state->responses[''])) {
            $value = $state->responses[''];
        } else {
            $value = '';
        }

        // answer
        if (empty($options->readonly)) {
            // the student needs to type in their answer so print out a text editor
            $answer = print_textarea(can_use_html_editor(), 18, 80, 630, 400,
                    $inputname, $value, $cmoptions->course, true);
        } else {
            // it is read only, so just format the students answer and output it
            $safeformatoptions = new stdClass;
            $safeformatoptions->para = false;
            $answer = format_text($value, FORMAT_MOODLE,
                                  $safeformatoptions, $cmoptions->course);
            $answer = '<div class="answerreview">' . $answer . '</div>';
        }

        include("$CFG->dirroot/question/type/essay/display.html");
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        // All grading takes place in Manual Grading

        $state->responses[''] = clean_param($state->responses[''], PARAM_CLEAN);

        $state->raw_grade = 0;
        $state->penalty = 0;

        return true;
    }

    /**
     * @param string response is a response.
     * @return formatted response
     */
    function format_response($response, $format) {
        $safeformatoptions = new stdClass();
        $safeformatoptions->para = false;
        return s(html_to_text(format_text($response, FORMAT_MOODLE, $safeformatoptions), 0, false));
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $form->questiontext = "What is the purpose of life?";
        $form->feedback = "feedback";
        $form->generalfeedback = "General feedback";
        $form->fraction = 0;
        $form->penalty = 0;

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
    }

    function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        if ($component == 'question' && $filearea == 'answerfeedback') {

            $answerid = reset($args); // itemid is answer id.
            $answers = &$question->options->answers;
            if (isset($state->responses[''])) {
                $response = $state->responses[''];
            } else {
                $response = '';
            }

            return $options->feedback && !empty($response);

        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }
    // Restore method not needed.
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_essay_qtype());
