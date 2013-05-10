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
 * The ordering question type.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering extends question_type {

    public function is_not_blank($value) {
        $value = trim($value);
        return ($value || $value==='0');
    }

    public function save_question_options($question) {
        global $DB;

        $result = new stdClass();

        // remove empty answers
        $question->answer = array_filter($question->answer, array($this, 'is_not_blank'));
        $question->answer = array_values($question->answer); // make keys sequential

        // count how many answers we have
        $countanswers = count($question->answer);

        // check at least two answers exist
        if ($countanswers < 2) {
            $result->notice = get_string('ordering_notenoughanswers', 'qtype_ordering', '2');
            return $result;
        }

        $question->feedback = range(1, $countanswers);

        if ($answerids = $DB->get_records('question_answers', array('question' => $question->id), 'id ASC', 'id,question')) {
            $answerids = array_keys($answerids);
        } else {
            $answerids = array();
        }

        // Insert all the new answers

        foreach ($question->answer as $i => $text) {
            $answer = (object)array(
                'question' => $question->id,
                'fraction' => ($i + 1), // start at 1
                'answer'   => $text,
                'answerformat' => FORMAT_MOODLE, // =0
                'feedback' => '',
                'feedbackformat' => FORMAT_MOODLE, // =0
            );

            if ($answer->id = array_shift($answerids)) {
                if (! $DB->update_record('question_answers', $answer)) {
                    $result->error = 'Could not update quiz answer! (id='.$answer->id.')';
                    return $result;
                }
            } else {
                if (! $answer->id = $DB->insert_record('question_answers', $answer)) {
                    $result->error = 'Could not insert quiz answer! ';
                    return $result;
                }
            }
        }

        // create $options for this ordering question
        $options = (object)array(
            'question' => $question->id,
            'logical' => $question->logical,
            'studentsee' => $question->studentsee,
            'correctfeedback' => $question->correctfeedback,
            'incorrectfeedback' => $question->incorrectfeedback,
            'partiallycorrectfeedback' => $question->partiallycorrectfeedback
        );

        // add/update $options for this ordering question
        if ($options->id = $DB->get_field('question_ordering', 'id', array('question' => $question->id))) {
            if (! $DB->update_record('question_ordering', $options)) {
                $result->error = 'Could not update quiz ordering options! (id='.$options->id.')';
                return $result;
            }
        } else {
            if (! $options->id = $DB->insert_record('question_ordering', $options)) {
                $result->error = 'Could not insert question ordering options!';
                return $result;
            }
        }

        // delete old answer records, if any
        if (count($answerids)) {
            $DB->delete_records_list('question_answers', 'id', $answerids);
        }

        return true;
    }

    public function get_question_options($question) {
        global $DB, $OUTPUT;

        // load the options
        if (! $question->options = $DB->get_record('question_ordering', array('question' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }

        // Load the answers - "fraction" is used to signify the order of the answers
        if (! $question->options->answers = $DB->get_records('question_answers', array('question' => $question->id), 'fraction ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for ordering question ' . $question->id . '!');
            return false;
        }

        //parent::get_question_options($question);
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
