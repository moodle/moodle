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

defined('MOODLE_INTERNAL') || die();

/////////////////
/// TRUEFALSE ///
/////////////////

/// QUESTION TYPE CLASS //////////////////
/**
 * @package questionbank
 * @subpackage questiontypes
 */
class question_truefalse_qtype extends default_questiontype {

    function name() {
        return 'truefalse';
    }

    function save_question_options($question) {
        global $DB;
        $result = new stdClass;
        $context = $question->context;

        // Fetch old answer ids so that we can reuse them
        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Save the true answer - update an existing answer if possible.
        $answer = array_shift($oldanswers);
        if (!$answer) {
            $answer = new stdClass();
            $answer->question = $question->id;
            $answer->answer = '';
            $answer->feedback = '';
            $answer->id = $DB->insert_record('question_answers', $answer);
        }

        $answer->answer   = get_string('true', 'quiz');
        $answer->fraction = $question->correctanswer;
        $answer->feedback = $this->import_or_save_files($question->feedbacktrue,
                $context, 'question', 'answerfeedback', $answer->id);
        $answer->feedbackformat = $question->feedbacktrue['format'];
        $DB->update_record('question_answers', $answer);
        $trueid = $answer->id;

        // Save the false answer - update an existing answer if possible.
        $answer = array_shift($oldanswers);
        if (!$answer) {
            $answer = new stdClass();
            $answer->question = $question->id;
            $answer->answer = '';
            $answer->feedback = '';
            $answer->id = $DB->insert_record('question_answers', $answer);
        }

        $answer->answer   = get_string('false', 'quiz');
        $answer->fraction = 1 - (int)$question->correctanswer;
        $answer->feedback = $this->import_or_save_files($question->feedbackfalse,
                $context, 'question', 'answerfeedback', $answer->id);
        $answer->feedbackformat = $question->feedbackfalse['format'];
        $DB->update_record('question_answers', $answer);
        $falseid = $answer->id;

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        // Save question options in question_truefalse table
        if ($options = $DB->get_record('question_truefalse', array('question' => $question->id))) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->trueanswer  = $trueid;
            $options->falseanswer = $falseid;
            $DB->update_record('question_truefalse', $options);
        } else {
            $options = new stdClass();
            $options->question    = $question->id;
            $options->trueanswer  = $trueid;
            $options->falseanswer = $falseid;
            $DB->insert_record('question_truefalse', $options);
        }

        return true;
    }

    /**
    * Loads the question type specific options for the question.
    */
    function get_question_options(&$question) {
        global $DB, $OUTPUT;
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = $DB->get_record('question_truefalse', array('question' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }
        // Load the answers
        if (!$question->options->answers = $DB->get_records('question_answers', array('question' =>  $question->id), 'id ASC')) {
           echo $OUTPUT->notification('Error: Missing question answers for truefalse question ' . $question->id . '!');
           return false;
        }

        return true;
    }

    function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_truefalse', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

    function compare_responses($question, $state, $teststate) {
        if (isset($state->responses['']) && isset($teststate->responses[''])) {
            return $state->responses[''] === $teststate->responses[''];
        } else if (isset($teststate->responses['']) && $teststate->responses[''] === '' &&
                !isset($state->responses[''])) {
            // Nothing selected in the past, and nothing selected now.
            return true;
        }
        return false;
    }

    function get_correct_responses(&$question, &$state) {
        // The correct answer is the one which gives full marks
        foreach ($question->options->answers as $answer) {
            if (((int) $answer->fraction) === 1) {
                return array('' => $answer->id);
            }
        }
        return null;
    }

    /**
    * Prints the main content of the question including any interactions
    */
    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
        $context = $this->get_context_by_category_id($question->category);

        $readonly = $options->readonly ? ' disabled="disabled"' : '';

        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;

        // Print question formulation
        $questiontext = format_text($question->questiontext,
                         $question->questiontextformat,
                         $formatoptions, $cmoptions->course);

        $answers = &$question->options->answers;
        $trueanswer = &$answers[$question->options->trueanswer];
        $falseanswer = &$answers[$question->options->falseanswer];
        $correctanswer = ($trueanswer->fraction == 1) ? $trueanswer : $falseanswer;

        $trueclass = '';
        $falseclass = '';
        $truefeedbackimg = '';
        $falsefeedbackimg = '';

        // Work out which radio button to select (if any)
        if (isset($state->responses[''])) {
            $response = $state->responses[''];
        } else {
            $response = '';
        }
        $truechecked = ($response == $trueanswer->id) ? ' checked="checked"' : '';
        $falsechecked = ($response == $falseanswer->id) ? ' checked="checked"' : '';

        // Work out visual feedback for answer correctness.
        if ($options->feedback) {
            if ($truechecked) {
                $trueclass = question_get_feedback_class($trueanswer->fraction);
            } else if ($falsechecked) {
                $falseclass = question_get_feedback_class($falseanswer->fraction);
            }
        }
        if ($options->feedback || $options->correct_responses) {
            if (isset($answers[$response])) {
                $truefeedbackimg = question_get_feedback_image($trueanswer->fraction, !empty($truechecked) && $options->feedback);
                $falsefeedbackimg = question_get_feedback_image($falseanswer->fraction, !empty($falsechecked) && $options->feedback);
            }
        }

        $inputname = ' name="'.$question->name_prefix.'" ';
        $trueid    = $question->name_prefix.'true';
        $falseid   = $question->name_prefix.'false';

        $radiotrue = '<input type="radio"' . $truechecked . $readonly . $inputname
            . 'id="'.$trueid . '" value="' . $trueanswer->id . '" /><label for="'.$trueid . '">'
            . s($trueanswer->answer) . '</label>';
        $radiofalse = '<input type="radio"' . $falsechecked . $readonly . $inputname
            . 'id="'.$falseid . '" value="' . $falseanswer->id . '" /><label for="'.$falseid . '">'
            . s($falseanswer->answer) . '</label>';

        $feedback = '';
        if ($options->feedback and isset($answers[$response])) {
            $chosenanswer = $answers[$response];
            $chosenanswer->feedback = quiz_rewrite_question_urls($chosenanswer->feedback, 'pluginfile.php', $context->id, 'question', 'answerfeedback', array($state->attempt, $state->question), $chosenanswer->id);
            $feedback = format_text($chosenanswer->feedback, $chosenanswer->feedbackformat, $formatoptions, $cmoptions->course);
        }

        include("$CFG->dirroot/question/type/truefalse/display.html");
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

            return $options->feedback && isset($answers[$response]) && $answerid == $response;

        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }

    function grade_responses(&$question, &$state, $cmoptions) {
        if (isset($state->responses['']) && isset($question->options->answers[$state->responses['']])) {
            $state->raw_grade = $question->options->answers[$state->responses['']]->fraction * $question->maxgrade;
        } else {
            $state->raw_grade = 0;
        }
        // Only allow one attempt at the question
        $state->penalty = 1 * $question->maxgrade;

        // mark the state as graded
        $state->event = ($state->event ==  QUESTION_EVENTCLOSE) ? QUESTION_EVENTCLOSEANDGRADE : QUESTION_EVENTGRADE;

        return true;
    }

    function get_actual_response($question, $state) {
        if (isset($question->options->answers[$state->responses['']])) {
            $responses[] = $question->options->answers[$state->responses['']]->answer;
        } else {
            $responses[] = '';
        }
        return $responses;
    }
    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    function get_random_guess_score($question) {
        return 0.5;
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;

        $form->questiontext = "This question is really stupid";
        $form->penalty = 1;
        $form->defaultgrade = 1;
        $form->correctanswer = 0;
        $form->feedbacktrue = 'Can you justify such a hasty judgment?';
        $form->feedbackfalse = 'Wisdom has spoken!';

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
    }
}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_truefalse_qtype());
