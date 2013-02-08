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
 * Multiple choice question definition classes.
 *
 * @package    qtype
 * @subpackage ordering
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for multiple choice questions. The parts that are common to
 * single select and multiple select.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_ordering_base extends question_graded_automatically {

}

class qtype_ordering_question extends question_graded_automatically {
    public $rightanswer;
    public $truefeedback;
    public $falsefeedback;
    public $trueanswerid;
    public $falseanswerid;

    public function get_expected_data() {
        return array('answer' => PARAM_INTEGER);
    }

    public function get_correct_response() {
        return array('answer' => (int) $this->rightanswer);
    }

    public function summarise_response(array $response) {

    }

    public function classify_response(array $response) {
        print_r ($response);
        echo "!2!";
        die();

        if (!array_key_exists('answer', $response)) {
            return array($this->id => question_classified_response::no_response());
        }
        list($fraction) = $this->grade_response($response);
        if ($response['answer']) {
            return array($this->id => new question_classified_response(1,
                    get_string('true', 'qtype_ordering'), $fraction));
        } else {
            return array($this->id => new question_classified_response(0,
                    get_string('false', 'qtype_ordering'), $fraction));
        }
    }

    public function is_complete_response(array $response) {
        global $DB;

        $answer = $_POST["q".$this->id];
        $orderdata = explode(",", $answer);
        $responses = array();
        $erroranswer = array();

        foreach ($orderdata as $orderdata_) {
            if (!empty($orderdata_))
                list($a1,$a2,$responses[]) = explode("_", $orderdata_);
        }

        //print_r($responses);

        $raw_grade = 0;

        $questionordering = $DB->get_record ("question_ordering", array("question" => $this->id));
        $questionanswers  = $DB->get_records ("question_answers", array("question" => $this->id), "id");

        $fractioncount = 1;

        foreach ($questionanswers as $questionanswersfractiontemp) {
            $questionanswers[$questionanswersfractiontemp->id]->fraction = $fractioncount;
            ++$fractioncount;
        }

        foreach ($questionanswers as $questionanswers_) {
            $questionanswersfraction[$questionanswers_->fraction] = $questionanswers_->id;
        }

        $erroranswer['main'] = 0;

        if (is_array($responses)) {
            if ($questionordering->logical == 0) {
                $nefr = 1;
                if ($questionordering->studentsee != 0) {
                    foreach ($questionanswersfraction as $frkey => $frvalue) {
                        if (in_array($frvalue, $responses)) {
                            $questionanswersfractionnew [$nefr] = $frvalue;
                            $nefr ++;
                        }
                    }

                    $questionanswersfraction = $questionanswersfractionnew;
                }

                for ($i = 0; $i <= count($responses) - 1; $i++) {
                    if ($responses[$i] != $questionanswersfraction[$i + 1]) {
                        $erroranswer['main'] ++;
                    }
                }

            } else if ($questionordering->logical == 1) {

                $erroranswer['main'] = 0;
                for ($i = 0; $i <= count($responses) - 1; $i++) {
                    if ($i != count($responses) -1) {
                        if ($questionanswers[$responses[$i]]->fraction > $questionanswers[$responses[$i + 1]]->fraction) {
                            $erroranswer['main'] ++;
                        }
                    }
                }
            } else if ($questionordering->logical == 2) {

                $fruits = $responses;

                sort($fruits);
                reset($fruits);

                if ($responses[0] != $fruits[0]) {
                    $erroranswer['main'] ++;
                }

                for ($i = 0; $i <= count($responses) - 1; $i++) {
                    if ($i != count($responses) -1) {
                        if ($questionanswers[$responses[$i]]->fraction < $questionanswers[$responses[$i + 1]]->fraction) {
                            $raznica = $questionanswers[$responses[$i + 1]]->fraction - $questionanswers[$responses[$i]]->fraction - 1;
                            if ($raznica >= 1) {
                                $alreadycount = "false";
                                for ($d = 1; $d <= $raznica; $d++) {
                                    if ($alreadycount == "false") {
                                        if (in_array($questionanswersfraction[$questionanswers[$responses[$i]]->fraction + $d] ,$responses)) {
                                            $alreadycount = "true";
                                            $erroranswer['main'] ++;
                                        }
                                    }
                                }
                            }
                        } else {
                            $erroranswer['main'] ++;
                        }
                    }
                }
            }


            if ($questionordering->logical == 0 || $questionordering->logical == 2)
                $allcount = count($responses);
            else
                $allcount = count($responses) - 1;

            $grade = ($allcount - $erroranswer['main']) / $allcount;
        } else {
            $state->raw_grade = 0;
            $state->grade = 0;
        }

        $_SESSION['SESSION']->quiz_answer['q'.$this->id] = $grade;

        return true;
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
        $fraction = $_SESSION['SESSION']->quiz_answer['q'.$this->id];
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {

    }
}
