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
 * The questiontype class for the multiple choice question type.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

if (class_exists('question_type')) {
    $register_questiontype = false;
} else {
    $register_questiontype = true; // Moodle 2.0
    require_once(__DIR__.'/legacy/20.php');
}

/**
 * The multiple choice question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering extends question_type {
    /*
    public function get_question_options($question) {
        global $DB, $OUTPUT;
        $question->options = $DB->get_record('question_ordering',
                array('question' => $question->id), '*', MUST_EXIST);
        parent::get_question_options($question);
    }
    */

    public function save_question_options($question) {
        global $DB, $OUTPUT;

        foreach ($question->answer as $answerkey => $answervalue) {
            $question->fraction[] = $answerkey + 1;
        }

        $result = new stdClass;
        if (!$oldanswers = $DB->get_records('question_answers', array('question'=>$question->id), 'id ASC')) {
            $oldanswers = array();
        }

        // following hack to check at least two answers exist
        $answercount = 0;
        foreach ($question->answer as $key=>$dataanswer) {
            if ($dataanswer != "") {
                $answercount++;
            }
        }
        $answercount += count($oldanswers);
        if ($answercount < 2) { // check there are at lest 2 answers for multiple choice
            $result->notice = get_string("ordering_notenoughanswers", "qtype_ordering", "2");
            return $result;
        }

        // Insert all the new answers

        $totalfraction = 0;
        $maxfraction = -1;

        $answers = array();

        foreach ($question->answer as $key => $dataanswer) {
            if ($dataanswer != "") {
                if ($answer = array_shift($oldanswers)) {  // Existing answer, so reuse it
                    $answer->answer     = $dataanswer;
                    $answer->fraction   = $question->fraction[$key];
                    if (!$DB->update_record("question_answers", $answer)) {
                        $result->error = "Could not update quiz answer! (id=$answer->id)";
                        return $result;
                    }
                } else {
                    unset($answer);
                    $answer->answer   = $dataanswer;
                    $answer->question = $question->id;
                    $answer->fraction = number_format($question->fraction[$key], 7);
                    $answer->feedback = "";
                    //echo '<pre>';
                    //print_r ($answer);
                    if (!$answer->id = $DB->insert_record("question_answers", $answer)) {
                        $result->error = "Could not insert quiz answer! ";
                        return $result;
                    }
                }
                $answers[] = $answer->id;

                if ($question->fraction[$key] > 0) {                 // Sanity checks
                    $totalfraction += $question->fraction[$key];
                }
                if ($question->fraction[$key] > $maxfraction) {
                    $maxfraction = $question->fraction[$key];
                }
            }
        }

        $update = true;
        $options = $DB->get_record("question_ordering", array("question" => $question->id));
        if (!$options) {
            $update = false;
            $options = new stdClass;
            $options->question = $question->id;

        }
        $options->logical = $question->logical;
        $options->studentsee = $question->studentsee;
        $options->correctfeedback = $question->correctfeedback;
        $options->partiallycorrectfeedback = $question->partiallycorrectfeedback;
        $options->incorrectfeedback = $question->incorrectfeedback;

        if ($update) {
            if (!$DB->update_record("question_ordering", $options)) {
                $result->error = "Could not update quiz ordering options! (id=$options->id)";
                return $result;
            }
        } else {
            if (!$DB->insert_record("question_ordering", $options)) {
                $result->error = "Could not insert quiz ordering options!";
                return $result;
            }
        }

        // delete old answer records
        if (!empty($oldanswers)) {
            foreach($oldanswers as $oa) {
                $DB->delete_records('question_answers', array('id' => $oa->id));
            }
        }

        /// Perform sanity checks on fractional grades
        return true;
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = $DB->get_record('question_ordering',
                array('question' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }
        // Load the answers
        if (!$question->options->answers = $DB->get_records('question_answers',
                array('question' =>  $question->id), 'id ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for truefalse question ' .
                    $question->id . '!');
            return false;
        }

        return true;
    }
/*
    protected function initialise_question_instance(question_definition $question, $questiondata) {
        parent::initialise_question_instance($question, $questiondata);
        $answers = $questiondata->options->answers;

        if ($answers[$questiondata->options->trueanswer]->fraction > 0.99) {
            $question->rightanswer = true;
        } else {
            $question->rightanswer = false;
        }

        $question->truefeedback =  $answers[$questiondata->options->trueanswer]->feedback;
        $question->falsefeedback = $answers[$questiondata->options->falseanswer]->feedback;
        $question->truefeedbackformat =
                $answers[$questiondata->options->trueanswer]->feedbackformat;
        $question->falsefeedbackformat =
                $answers[$questiondata->options->falseanswer]->feedbackformat;
        $question->trueanswerid =  $questiondata->options->trueanswer;
        $question->falseanswerid = $questiondata->options->falseanswer;
    }
    */

    public function delete_question($questionid, $contextid) {
        global $DB;
        $DB->delete_records('question_ordering', array('question' => $questionid));

        parent::delete_question($questionid, $contextid);
    }

}

// NEW LINES START
if ($register_questiontype) {
    class question_ordering_qtype extends qtype_ordering {
        function name() {
            return 'ordering';
        }
    }
    question_register_questiontype(new question_ordering_qtype());
}
// NEW LINES STOP
