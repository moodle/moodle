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

///////////////////
/// SHORTANSWER ///
///////////////////

/// QUESTION TYPE CLASS //////////////////

///
/// This class contains some special features in order to make the
/// question type embeddable within a multianswer (cloze) question
///
/**
 * @package questionbank
 * @subpackage questiontypes
 */
require_once("$CFG->dirroot/question/type/questiontype.php");

class question_shortanswer_qtype extends default_questiontype {

    function name() {
        return 'shortanswer';
    }

    function has_wildcards_in_responses($question, $subqid) {
        return true;
    }

    function extra_question_fields() {
        return array('question_shortanswer', 'answers', 'usecase');
    }

    function questionid_column_name() {
        return 'question';
    }

    function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_answers($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_answers($questionid, $contextid);
    }

    function save_question_options($question) {
        global $DB;
        $result = new stdClass;

        $context = $question->context;

        $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        // Insert all the new answers
        $answers = array();
        $maxfraction = -1;
        foreach ($question->answer as $key => $answerdata) {
            // Check for, and ignore, completely blank answer from the form.
            if (trim($answerdata) == '' && $question->fraction[$key] == 0 &&
                    html_is_blank($question->feedback[$key]['text'])) {
                continue;
            }

            // Update an existing answer if possible.
            $answer = array_shift($oldanswers);
            if (!$answer) {
                $answer = new stdClass();
                $answer->question = $question->id;
                $answer->answer = '';
                $answer->feedback = '';
                $answer->id = $DB->insert_record('question_answers', $answer);
            }

            $answer->answer   = trim($answerdata);
            $answer->fraction = $question->fraction[$key];
            $answer->feedback = $this->import_or_save_files($question->feedback[$key],
                    $context, 'question', 'answerfeedback', $answer->id);
            $answer->feedbackformat = $question->feedback[$key]['format'];
            $DB->update_record('question_answers', $answer);

            $answers[] = $answer->id;
            if ($question->fraction[$key] > $maxfraction) {
                $maxfraction = $question->fraction[$key];
            }
        }

        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        $question->answers = implode(',', $answers);
        $parentresult = parent::save_question_options($question);
        if ($parentresult !== null) {
            // Parent function returns null if all is OK
            return $parentresult;
        }

        // Perform sanity checks on fractional grades
        if ($maxfraction != 1) {
            $result->noticeyesno = get_string('fractionsnomax', 'quiz', $maxfraction * 100);
            return $result;
        }

        return true;
    }

    function print_question_formulation_and_controls(&$question, &$state, $cmoptions, $options) {
        global $CFG;
        $context = $this->get_context_by_category_id($question->category);
    /// This implementation is also used by question type 'numerical'
        $readonly = empty($options->readonly) ? '' : 'readonly="readonly"';
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $nameprefix = $question->name_prefix;

        /// Print question text and media

        $questiontext = format_text($question->questiontext,
                $question->questiontextformat,
                $formatoptions, $cmoptions->course);

        /// Print input controls

        if (isset($state->responses['']) && $state->responses['']!='') {
            $value = ' value="'.s($state->responses['']).'" ';
        } else {
            $value = ' value="" ';
        }
        $inputname = ' name="'.$nameprefix.'" ';

        $feedback = '';
        $class = '';
        $feedbackimg = '';

        if ($options->feedback) {
            $class = question_get_feedback_class(0);
            $feedbackimg = question_get_feedback_image(0);
            //this is OK for the first answer with a good response
            foreach($question->options->answers as $answer) {

                if ($this->test_response($question, $state, $answer)) {
                    // Answer was correct or partially correct.
                    $class = question_get_feedback_class($answer->fraction);
                    $feedbackimg = question_get_feedback_image($answer->fraction);
                    if ($answer->feedback) {
                        $answer->feedback = quiz_rewrite_question_urls($answer->feedback, 'pluginfile.php', $context->id, 'question', 'answerfeedback', array($state->attempt, $state->question), $answer->id);
                        $feedback = format_text($answer->feedback, $answer->feedbackformat, $formatoptions, $cmoptions->course);
                    }
                    break;
                }
            }
        }

        /// Removed correct answer, to be displayed later MDL-7496
        include($this->get_display_html_path());
    }

    function get_display_html_path() {
        global $CFG;
        return $CFG->dirroot.'/question/type/shortanswer/display.html';
    }

    function check_response(&$question, &$state) {
        foreach($question->options->answers as $aid => $answer) {
            if ($this->test_response($question, $state, $answer)) {
                return $aid;
            }
        }
        return false;
    }

    function compare_responses($question, $state, $teststate) {
        if (isset($state->responses['']) && isset($teststate->responses[''])) {
            return $state->responses[''] === $teststate->responses[''];
        }
        return false;
    }

    function test_response(&$question, $state, $answer) {
        // Trim the response before it is saved in the database. See MDL-10709
        $state->responses[''] = trim($state->responses['']);
        return $this->compare_string_with_wildcard($state->responses[''],
                $answer->answer, !$question->options->usecase);
    }

    function compare_string_with_wildcard($string, $pattern, $ignorecase) {
        // Break the string on non-escaped asterisks.
        $bits = preg_split('/(?<!\\\\)\*/', $pattern);
        // Escape regexp special characters in the bits.
        $excapedbits = array();
        foreach ($bits as $bit) {
            $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
        }
        // Put it back together to make the regexp.
        $regexp = '|^' . implode('.*', $excapedbits) . '$|u';

        // Make the match insensitive if requested to.
        if ($ignorecase) {
            $regexp .= 'i';
        }

        return preg_match($regexp, trim($string));
    }

    /**
     * @param string response is a response.
     * @return formatted response
     */
    function format_response($response, $format){
        return s($response);
    }

    /*
     * Override the parent class method, to remove escaping from asterisks.
     */
    function get_correct_responses(&$question, &$state) {
        $response = parent::get_correct_responses($question, $state);
        if (is_array($response)) {
            $response[''] = str_replace('\*', '*', $response['']);
        }
        return $response;
    }
    /**
     * @param object $question
     * @return mixed either a integer score out of 1 that the average random
     * guess by a student might give or an empty string which means will not
     * calculate.
     */
    function get_random_guess_score($question) {
        $answers = &$question->options->answers;
        foreach($answers as $aid => $answer) {
            if ('*' == trim($answer->answer)){
                return $answer->fraction;
            }
        }
        return 0;
    }

    /**
    * Prints the score obtained and maximum score available plus any penalty
    * information
    *
    * This function prints a summary of the scoring in the most recently
    * graded state (the question may not have been submitted for marking at
    * the current state). The default implementation should be suitable for most
    * question types.
    * @param object $question The question for which the grading details are
    *                         to be rendered. Question type specific information
    *                         is included. The maximum possible grade is in
    *                         ->maxgrade.
    * @param object $state    The state. In particular the grading information
    *                          is in ->grade, ->raw_grade and ->penalty.
    * @param object $cmoptions
    * @param object $options  An object describing the rendering options.
    */
    function print_question_grading_details(&$question, &$state, $cmoptions, $options) {
        /* The default implementation prints the number of marks if no attempt
        has been made. Otherwise it displays the grade obtained out of the
        maximum grade available and a warning if a penalty was applied for the
        attempt and displays the overall grade obtained counting all previous
        responses (and penalties) */

        global $QTYPES ;
        // MDL-7496 show correct answer after "Incorrect"
        $correctanswer = '';
        if ($correctanswers =  $QTYPES[$question->qtype]->get_correct_responses($question, $state)) {
            if ($options->readonly && $options->correct_responses) {
                $delimiter = '';
                if ($correctanswers) {
                    foreach ($correctanswers as $ca) {
                        $correctanswer .= $delimiter.$ca;
                        $delimiter = ', ';
                    }
                }
            }
        }

        if (QUESTION_EVENTDUPLICATE == $state->event) {
            echo ' ';
            print_string('duplicateresponse', 'quiz');
        }
        if ($question->maxgrade > 0 && $options->scores) {
            if (question_state_is_graded($state->last_graded)) {
                // Display the grading details from the last graded state
                $grade = new stdClass;
                $grade->cur = question_format_grade($cmoptions, $state->last_graded->grade);
                $grade->max = question_format_grade($cmoptions, $question->maxgrade);
                $grade->raw = question_format_grade($cmoptions, $state->last_graded->raw_grade);
                // let student know wether the answer was correct
                $class = question_get_feedback_class($state->last_graded->raw_grade /
                        $question->maxgrade);
                echo '<div class="correctness ' . $class . '">' . get_string($class, 'quiz');
                if ($correctanswer  != '' && ($class == 'partiallycorrect' || $class == 'incorrect')) {
                    echo ('<div class="correctness">');
                    print_string('correctansweris', 'quiz', s($correctanswer));
                    echo ('</div>');
                }
                echo '</div>';

                echo '<div class="gradingdetails">';
                // print grade for this submission
                print_string('gradingdetails', 'quiz', $grade) ;
                // A unit penalty for numerical was applied so display it
                // a temporary solution for unit rendering in numerical
                // waiting for the new question engine code for a permanent one
                if(isset($state->options->raw_unitpenalty) && $state->options->raw_unitpenalty > 0.0 ){
                    echo ' ';
                    print_string('unitappliedpenalty','qtype_numerical',question_format_grade($cmoptions, $state->options->raw_unitpenalty * $question->maxgrade ));
                }
                if ($cmoptions->penaltyscheme) {
                    // print details of grade adjustment due to penalties
                    if ($state->last_graded->raw_grade > $state->last_graded->grade){
                        echo ' ';
                        print_string('gradingdetailsadjustment', 'quiz', $grade);
                    }
                    // print info about new penalty
                    // penalty is relevant only if the answer is not correct and further attempts are possible
                    if (($state->last_graded->raw_grade < $question->maxgrade) and (QUESTION_EVENTCLOSEANDGRADE != $state->event)) {
                        if ('' !== $state->last_graded->penalty && ((float)$state->last_graded->penalty) > 0.0) {
                            echo ' ' ;
                            print_string('gradingdetailspenalty', 'quiz', question_format_grade($cmoptions, $state->last_graded->penalty));
                        } else {
                            /* No penalty was applied even though the answer was
                            not correct (eg. a syntax error) so tell the student
                            that they were not penalised for the attempt */
                            echo ' ';
                            print_string('gradingdetailszeropenalty', 'quiz');
                        }
                    }
                }
                echo '</div>';
            }
        }
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    function generate_test($name, $courseid = null) {
        global $DB;
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;

        $form->questiontext = "What is the purpose of life, the universe, and everything";
        $form->generalfeedback = "Congratulations, you may have solved my biggest problem!";
        $form->penalty = 0.1;
        $form->usecase = false;
        $form->defaultgrade = 1;
        $form->noanswers = 3;
        $form->answer = array('42', 'who cares?', 'Be happy');
        $form->fraction = array(1, 0.6, 0.8);
        $form->feedback = array('True, but what does that mean?', 'Well you do, dont you?', 'Yes, but thats not funny...');
        $form->correctfeedback = 'Excellent!';
        $form->incorrectfeedback = 'Nope!';
        $form->partiallycorrectfeedback = 'Not bad';

        if ($courseid) {
            $course = $DB->get_record('course', array('id' => $courseid));
        }

        return $this->save_question($question, $form);
    }

    function check_file_access($question, $state, $options, $contextid, $component,
            $filearea, $args) {
        if ($component == 'question' && $filearea == 'answerfeedback') {
            $answers = &$question->options->answers;
            if (isset($state->responses[''])) {
                $response = $state->responses[''];
            } else {
                $response = '';
            }
            $answerid = reset($args); // itemid is answer id.
            if (empty($options->feedback)) {
                return false;
            }
            foreach($answers as $answer) {
                if ($this->test_response($question, $state, $answer)) {
                    return true;
                }
            }
            return false;

        } else {
            return parent::check_file_access($question, $state, $options, $contextid, $component,
                    $filearea, $args);
        }
    }

}
//// END OF CLASS ////

//////////////////////////////////////////////////////////////////////////
//// INITIATION - Without this line the question type is not in use... ///
//////////////////////////////////////////////////////////////////////////
question_register_questiontype(new question_shortanswer_qtype());

