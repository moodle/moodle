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
 * ORDERING question definition classes.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Represents an ORDERING question.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_ordering_question extends question_graded_automatically {

    public $answers;
    public $options;
    public $rightanswer;

    public function get_response_fieldname() {
        return 'response_'.$this->id;
    }

    public function format_questiontext($qa) {
        $text = parent::format_questiontext($qa);
        return stripslashes($text);
    }

    public function get_expected_data() {
        $name = $this->get_response_fieldname();
        return array($name => PARAM_TEXT);
    }

    public function get_correct_response() {
        if ($this->rightanswer===null) {
            $this->rightanswer = $this->get_ordering_answers();
            $this->rightanswer = array_keys($this->rightanswer);
            $this->rightanswer = implode(',', $this->rightanswer);
        }
        return array('answer' => $this->rightanswer);
    }

    public function summarise_response(array $response) {
    }

    public function classify_response(array $response) {
        if (array_key_exists('answer', $response)) {
            $responseclassid = ($response['answer'] ? 1 : 0);
            list($fraction) = $this->grade_response($response);
            return array($this->id => new question_classified_response($responseclassid, get_string('true', 'qtype_ordering'), $fraction));
        } else {
            return array($this->id => question_classified_response::no_response());
        }
    }

    public function get_ordering_options() {
        global $DB;
        if ($this->options===null) {
            $this->options = $DB->get_record('question_ordering', array('question' => $this->id));
            if ($this->options==false) {
                $this->options = (object)array(
                    'question'   => $this->id,
                    'logical'    => 0, // require all answers
                    'studentsee' => 0,
                    'correctfeedback' => '',
                    'incorrectfeedback' => '',
                    'partiallycorrectfeedback' => ''
                );
                $this->options->id = $DB->insert_record('question_ordering', $options);
            }
        }
        return $this->options;
    }

    public function get_ordering_answers() {
        global $CFG, $DB;
        if ($this->answers===null) {
            $this->answers = $DB->get_records('question_answers', array('question' => $this->id), 'fraction,id');
            if ($this->answers) {
                if (isset($CFG->passwordsaltmain)) {
                    $salt = $CFG->passwordsaltmain;
                } else {
                    $salt = ''; // complex_random_string()
                }
                foreach ($this->answers as $answerid => $answer) {
                    $this->answers[$answerid]->md5key = 'ordering_item_'.md5($salt.$answer->answer);
                }
            } else {
                $this->answers = array();
            }
        }
        return $this->answers;
    }

    public function is_complete_response(array $response) {
        global $CFG, $DB, $SESSION;

        $name = $this->get_response_fieldname();
        if (isset($response[$name])) {
            $responses = $response[$name];
        } else {
            $responses = optional_param($name, '', PARAM_TEXT);
        }
        $responses = preg_replace('[^a-zA-Z0-9,_-]', '', $responses);
        $responses = explode(',', $responses); // convert to array
        $responses = array_filter($responses); // remove blanks
        $responses = array_unique($responses); // remove duplicates

        foreach ($responses as $i => $response) {
            if (substr($response, 0, 14)=='ordering_item_') {
                $responses[$i] = substr($response, 14);
            } else {
                unset($responses[$i]); // remove invalid response
            }
        }

        $ordering = $this->get_ordering_options();
        $answers  = $this->get_ordering_answers();

        if ($ordering->logical==0) {
            $total = count($answers); // require all answers
        } else {
            $total = $ordering->studentsee + 2; // a subset of answers
        }

        if (isset($CFG->passwordsaltmain)) {
            $salt = $CFG->passwordsaltmain;
        } else {
            $salt = ''; // complex_random_string()
        }

        $validresponses = array();
        foreach ($answers as $answerid => $answer) {

            $response = md5($salt.$answer->answer);
            $sortorder = intval($answer->fraction);

            if (in_array($response, $responses)) {
                $answers[$answerid]->sortorder = $sortorder;
                $answers[$answerid]->response = $response;
                $validresponses[] = $response;
            } else {
                unset($answers[$answerid]); // this answer is not used
            }
        }

        // convert $answers to sequentially numbered array
        $answers = array_values($answers);

        // sort $answers by sortorder (not really necessary)
        usort($answers, array($this, 'usort_sortorder'));

        // remove invalid responses
        foreach ($responses as $i => $response) {
            if (! in_array($response, $validresponses)) {
                unset($responses[$i]);
            }
        }
        unset($validresponses);

        $correct = 0;
        foreach ($answers as $i => $answer) {
            if (isset($responses[$i]) && $answer->response==$responses[$i]) {
                $correct++;
            }
            $i++;
        }

        if ($total==0) {
            $grade = 0;
        } else {
            $grade = round($correct / $total, 5);
        }

        // we use $SESSION instead of accessing $_SESSION directly
        // $_SESSION['SESSION']->quiz_answer['q'.$this->id] = $grade;

        if (empty($SESSION->quiz_answer)) {
            $SESSION->quiz_answer = array();
        }
        $SESSION->quiz_answer['q'.$this->id] = $grade;

        return true;
    }

    public function usort_sortorder($a, $b) {
        if ($a->sortorder < $b->sortorder) {
            return -1;
        }
        if ($a->sortorder > $b->sortorder) {
            return 1;
        }
        return 0; // equal values
    }

    public function is_gradable_response(array $response) {
        return true;
    }

    public function get_validation_error(array $response) {
        return '';
    }

    public function is_same_response(array $prevresponse, array $newresponse) {

    }

    public function compute_final_grade($responses, $totaltries) {
        return 1;
    }

    public function grade_response(array $response) {
        global $SESSION;
        if (empty($SESSION->quiz_answer['q'.$this->id])) {
            $fraction = 0;
        } else {
            $fraction = $SESSION->quiz_answer['q'.$this->id];
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }
}
