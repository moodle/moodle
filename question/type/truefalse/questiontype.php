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
 * Question type class for the true-false question type.
 *
 * @package qtype_truefalse
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * The true-false question type class.
 *
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_truefalse extends question_type {
    public function save_question_options($question) {
        $result = new stdClass;

        // fetch old answer ids so that we can reuse them
        if (!$oldanswers = get_records("question_answers", "question", $question->id, "id ASC")) {
            $oldanswers = array();
        }

        // Save answer 'True'
        if ($true = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $true->answer   = get_string("true", "quiz");
            $true->fraction = $question->correctanswer;
            $true->feedback = $question->feedbacktrue;
            if (!update_record("question_answers", $true)) {
                $result->error = "Could not update quiz answer \"true\")!";
                return $result;
            }
        } else {
            unset($true);
            $true->answer   = get_string("true", "quiz");
            $true->question = $question->id;
            $true->fraction = $question->correctanswer;
            $true->feedback = $question->feedbacktrue;
            if (!$true->id = insert_record("question_answers", $true)) {
                $result->error = "Could not insert quiz answer \"true\")!";
                return $result;
            }
        }

        // Save answer 'False'
        if ($false = array_shift($oldanswers)) {  // Existing answer, so reuse it
            $false->answer   = get_string("false", "quiz");
            $false->fraction = 1 - (int)$question->correctanswer;
            $false->feedback = $question->feedbackfalse;
            if (!update_record("question_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        } else {
            unset($false);
            $false->answer   = get_string("false", "quiz");
            $false->question = $question->id;
            $false->fraction = 1 - (int)$question->correctanswer;
            $false->feedback = $question->feedbackfalse;
            if (!$false->id = insert_record("question_answers", $false)) {
                $result->error = "Could not insert quiz answer \"false\")!";
                return $result;
            }
        }

        // delete any leftover old answer records (there couldn't really be any, but who knows)
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                delete_records('question_answers', 'id', $oa->id);
            }
        }

        // Save question options in question_truefalse table
        if ($options = get_record("question_truefalse", "question", $question->id)) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->trueanswer  = $true->id;
            $options->falseanswer = $false->id;
            if (!update_record("question_truefalse", $options)) {
                $result->error = "Could not update quiz truefalse options! (id=$options->id)";
                return $result;
            }
        } else {
            unset($options);
            $options->question    = $question->id;
            $options->trueanswer  = $true->id;
            $options->falseanswer = $false->id;
            if (!insert_record("question_truefalse", $options)) {
                $result->error = "Could not insert quiz truefalse options!";
                return $result;
            }
        }

        $this->save_hints($question);

        return true;
    }

    /**
    * Loads the question type specific options for the question.
    */
    public function get_question_options($question) {
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = get_record('question_truefalse', 'question', $question->id)) {
            notify('Error: Missing question options!');
            return false;
        }

        parent::get_question_options($question);

        return true;
    }

    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $answers = $questiondata->options->answers;
        if ($answers[$questiondata->options->trueanswer]->fraction > 0.99) {
            $question->rightanswer = true;
        } else {
            $question->rightanswer = false;
        }
        $question->truefeedback = $answers[$questiondata->options->trueanswer]->feedback;
        $question->falsefeedback = $answers[$questiondata->options->falseanswer]->feedback;
    }

    /**
    * Deletes question from the question-type specific tables
    *
    * @return boolean Success/Failure
    * @param object $question  The question being deleted
    */
    public function delete_question($questionid) {
        delete_records("question_truefalse", "question", $questionid);
        return parent::delete_question($questionid);
    }

    function get_random_guess_score($questiondata) {
        return 0.5;
    }

    function get_possible_responses($questiondata) {
        return array(
            $questiondata->id => array(
                0 => new question_possible_response(get_string('false', 'qtype_truefalse'),
                        $questiondata->options->answers[
                        $questiondata->options->falseanswer]->fraction),
                1 => new question_possible_response(get_string('true', 'qtype_truefalse'),
                        $questiondata->options->answers[
                        $questiondata->options->trueanswer]->fraction),
                null => question_possible_response::no_response()
            )
        );
    }

/// BACKUP FUNCTIONS ////////////////////////////

    /*
     * Backup the data in a truefalse question
     *
     * This is used in question/backuplib.php
     */
    public function backup($bf,$preferences,$question,$level=6) {

        $status = true;

        $truefalses = get_records("question_truefalse","question",$question,"id");
        //If there are truefalses
        if ($truefalses) {
            //Iterate over each truefalse
            foreach ($truefalses as $truefalse) {
                $status = fwrite ($bf,start_tag("TRUEFALSE",$level,true));
                //Print truefalse contents
                fwrite ($bf,full_tag("TRUEANSWER",$level+1,false,$truefalse->trueanswer));
                fwrite ($bf,full_tag("FALSEANSWER",$level+1,false,$truefalse->falseanswer));
                $status = fwrite ($bf,end_tag("TRUEFALSE",$level,true));
            }
            //Now print question_answers
            $status = question_backup_answers($bf,$preferences,$question);
        }
        return $status;
    }

/// RESTORE FUNCTIONS /////////////////

    /*
     * Restores the data in the question
     *
     * This is used in question/restorelib.php
     */
    public function restore($old_question_id,$new_question_id,$info,$restore) {

        $status = true;

        //Get the truefalse array
        if (array_key_exists('TRUEFALSE', $info['#'])) {
            $truefalses = $info['#']['TRUEFALSE'];
        } else {
            $truefalses = array();
        }

        //Iterate over truefalse
        for($i = 0; $i < sizeof($truefalses); $i++) {
            $tru_info = $truefalses[$i];

            //Now, build the question_truefalse record structure
            $truefalse = new stdClass;
            $truefalse->question = $new_question_id;
            $truefalse->trueanswer = backup_todb($tru_info['#']['TRUEANSWER']['0']['#']);
            $truefalse->falseanswer = backup_todb($tru_info['#']['FALSEANSWER']['0']['#']);

            ////We have to recode the trueanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->trueanswer);
            if ($answer) {
                $truefalse->trueanswer = $answer->new_id;
            }

            ////We have to recode the falseanswer field
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$truefalse->falseanswer);
            if ($answer) {
                $truefalse->falseanswer = $answer->new_id;
            }

            //The structure is equal to the db, so insert the question_truefalse
            $newid = insert_record ("question_truefalse", $truefalse);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }

            if (!$newid) {
                $status = false;
            }
        }

        return $status;
    }

    public function restore_recode_answer($state, $restore) {
        //answer may be empty
        if ($state->answer) {
            $answer = backup_getid($restore->backup_unique_code,"question_answers",$state->answer);
            if ($answer) {
                return $answer->new_id;
            } else {
                echo 'Could not recode truefalse answer id '.$state->answer.' for state '.$state->oldid.'<br />';
            }
        }
    }

    /**
     * Runs all the code required to set up and save an essay question for testing purposes.
     * Alternate DB table prefix may be used to facilitate data deletion.
     */
    public function generate_test($name, $courseid = null) {
        list($form, $question) = parent::generate_test($name, $courseid);
        $question->category = $form->category;

        $form->questiontext = "This question is really stupid";
        $form->penalty = 1;
        $form->defaultmark = 1;
        $form->correctanswer = 0;
        $form->feedbacktrue = array('Can you justify such a hasty judgment?');
        $form->feedbackfalse = array('Wisdom has spoken!');

        if ($courseid) {
            $course = get_record('course', 'id', $courseid);
        }

        return $this->save_question($question, $form, $course);
    }
}
