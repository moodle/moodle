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

    /** fields from "qtype_ordering_options" */
    public $correctfeedback;
    public $correctfeedbackformat;
    public $incorrectfeedback;
    public $incorrectfeedbackformat;
    public $partiallycorrectfeedback;
    public $partiallycorrectfeedbackformat;

    /** records from "question_answers" table */
    public $answers;

    /** records from "qtype_ordering_options" table */
    public $options;

    /** array of answerids in correct order */
    public $correctresponse;

    /** array current order of answerids */
    public $currentresponse;

    public function start_attempt(question_attempt_step $step, $variant) {
        $this->answers = $this->get_ordering_answers();
        $this->options = $this->get_ordering_options();

        $countanswers = count($this->answers);

        // sanitize "selecttype"
        $selecttype = $this->options->selecttype;
        $selecttype = max(0, $selecttype);
        $selecttype = min(2, $selecttype);

        // sanitize "selectcount"
        $selectcount = $this->options->selectcount;
        $selectcount = max(3, $selectcount);
        $selectcount = min($countanswers, $selectcount);

        // ensure consistency between "selecttype" and "selectcount"
        switch (true) {
            case ($selecttype==0): $selectcount = $countanswers; break;
            case ($selectcount==$countanswers): $selecttype = 0; break;
        }

        // extract answer ids
        switch ($selecttype) {
            case 0: // all
                $answerids = array_keys($this->answers);
                break;

            case 1: // random subset
                $answerids = array_rand($this->answers, $selectcount);
                break;

            case 2: // contiguous subset
                $answerids = array_keys($this->answers);
                $offset = mt_rand(0, $countanswers - $selectcount);
                $answerids = array_slice($answerids, $offset, $selectcount, true);
                break;
        }

        $this->correctresponse = $answerids;
        $step->set_qt_var('_correctresponse', implode(',', $this->correctresponse));

        shuffle($answerids);
        $this->currentresponse = $answerids;
        $step->set_qt_var('_currentresponse', implode(',', $this->currentresponse));
    }

    public function apply_attempt_state(question_attempt_step $step) {
        $this->answers = $this->get_ordering_answers();
        $this->options = $this->get_ordering_options();
        $this->currentresponse = array_filter(explode(',', $step->get_qt_var('_currentresponse')));
        $this->correctresponse = array_filter(explode(',', $step->get_qt_var('_correctresponse')));
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
        $correctresponse = $this->correctresponse;
        foreach ($correctresponse as $position => $answerid) {
            $answer = $this->answers[$answerid];
            $correctresponse[$position] = $answer->md5key;
        }
        $name = $this->get_response_fieldname();
        return array($name => implode(',', $correctresponse));
    }

    public function summarise_response(array $response) {
        return '';
    }

    public function classify_response(array $response) {
        return array();
    }

    public function is_complete_response(array $response) {
        return true;
    }

    public function is_gradable_response(array $response) {
        return true;
    }

    public function get_validation_error(array $response) {
        return '';
    }

    public function is_same_response(array $old, array $new) {
        $name = $this->get_response_fieldname();
        return (isset($old[$name]) && isset($new[$name]) && $old[$name]==$new[$name]);
    }

    public function grade_response(array $response) {
        $this->update_current_response($response);
        $countcorrect = 0;
        $countanswers = 0;
        $correctresponse = $this->correctresponse;
        $currentresponse = $this->currentresponse;
        foreach ($currentresponse as $position => $answerid) {
            if ($correctresponse[$position]==$answerid) {
                $countcorrect++;
            }
            $countanswers++;
        }
        if ($countanswers==0) {
            $fraction = 0;
        } else {
            $fraction = ($countcorrect / $countanswers);
        }
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

    public function check_file_access($qa, $options, $component, $filearea, $args, $forcedownload) {
        return parent::check_file_access($qa, $options, $component, $filearea, $args, $forcedownload);
    }

    ////////////////////////////////////////////////////////////////////
    // custom methods
    ////////////////////////////////////////////////////////////////////

    public function get_response_fieldname() {
        return 'response_'.$this->id;
    }

    public function update_current_response($response) {
        $name = $this->get_response_fieldname();
        if (isset($response[$name])) {
            $ids = explode(',', $response[$name]);
            foreach ($ids as $i => $id) {
                foreach ($this->answers as $answer) {
                    if ($id==$answer->md5key) {
                        $ids[$i] = $answer->id;
                        break;
                    }
                }
            }
            $this->currentresponse = $ids;
        }
    }

    public function get_ordering_options() {
        global $DB;
        if ($this->options===null) {
            $this->options = $DB->get_record('qtype_ordering_options', array('questionid' => $this->id));
            if (empty($this->options)) {
                $this->options = (object)array(
                    'questionid' => $this->id,
                    'selecttype' => 0, // all answers
                    'selectcount' => 0,
                    'correctfeedback' => '',
                    'correctfeedbackformat' => FORMAT_MOODLE, // =0
                    'incorrectfeedback' => '',
                    'incorrectfeedbackformat' => FORMAT_MOODLE, // =0
                    'partiallycorrectfeedback' => '',
                    'partiallycorrectfeedbackformat' => FORMAT_MOODLE // =0
                );
                $this->options->id = $DB->insert_record('qtype_ordering_options', $this->options);
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
}
