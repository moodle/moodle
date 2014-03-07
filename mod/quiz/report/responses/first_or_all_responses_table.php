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
 * This file defines the quiz responses table for showing first or all tries at a question.
 *
 * @package   quiz_responses
 * @copyright 2014 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This is a table subclass for displaying the quiz responses report, showing first or all tries.
 *
 * @copyright 2008 Jean-Michel Vedrine
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_first_or_all_responses_table extends quiz_last_responses_table {

    /**
     * The full question usage object for each attempt shown in report.
     *
     * @var question_usage_by_activity[]
     */
    protected $questionusagesbyactivity;

    protected function field_from_extra_data($attempt, $slot, $field) {
        $questionattempt = $this->get_question_attempt($attempt->usageid, $slot);
        switch($field) {
            case 'questionsummary' :
                return $questionattempt->get_question_summary();
            case 'responsesummary' :
                return $this->get_summary_after_try($attempt, $slot);
            case 'rightanswer' :
                return $questionattempt->get_right_answer_summary();
            default :
                throw new coding_exception('Unknown question attempt field.');
        }
    }


    protected function load_extra_data() {
        if (count($this->rawdata) === 0) {
            return;
        }
        $qubaids = $this->get_qubaids_condition();
        $dm = new question_engine_data_mapper();
        $this->questionusagesbyactivity = $dm->load_questions_usages_by_activity($qubaids);

        // Insert an extra field in attempt data and extra rows where necessary.
        $newrawdata = array();
        foreach ($this->rawdata as $attempt) {
            $maxtriesinanyslot = 1;
            foreach ($this->questionusagesbyactivity[$attempt->usageid]->get_slots() as $slot) {
                $tries = $this->get_no_of_tries($attempt, $slot);
                $maxtriesinanyslot = max($maxtriesinanyslot, $tries);
            }
            for ($try = 1; $try <= $maxtriesinanyslot; $try++) {
                $newattemptrow = clone($attempt);
                $newattemptrow->lasttryforallparts = ($try == $maxtriesinanyslot);
                if ($try !== $maxtriesinanyslot) {
                    $newattemptrow->state = quiz_attempt::IN_PROGRESS;
                }
                $newattemptrow->try = $try;
                $newrawdata[] = $newattemptrow;
                if ($this->options->whichtries == question_attempt::FIRST_TRY) {
                    break;
                }
            }
        }
        $this->rawdata = $newrawdata;
    }

    /**
     * Return the question attempt object.
     *
     * @param int $questionusagesid
     * @param int $slot
     * @return question_attempt
     */
    protected function get_question_attempt($questionusagesid, $slot) {
        return $this->questionusagesbyactivity[$questionusagesid]->get_question_attempt($slot);
    }

    /**
     * @param object $attempt row data
     * @param int $slot
     * @return question_state
     */
    protected function slot_state($attempt, $slot) {
        $qa = $this->get_question_attempt($attempt->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$attempt->try];
        if ($step === null) {
            return null;
        }
        if ($this->is_last_try($attempt, $slot, $attempt->try)) {
            // If this is the last try then the step with the try data does not contain the correct state. We need to
            // use the last step's state, after the attempt has been finished.
            return $qa->get_state();
        }
        return $step->get_state();
    }


    /**
     * @param object $attempt row data
     * @param int $slot
     * @return string summary for the question after this try.
     */
    public function get_summary_after_try($attempt, $slot) {
        $qa = $this->get_question_attempt($attempt->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$attempt->try];
        if ($step === null) {
            return null;
        }
        $qtdata = $step->get_qt_data();
        return $qa->get_question()->summarise_response($qtdata);
    }

    /**
     * @param int $questionusageid
     * @param int $slot
     * @return bool
     */
    protected function is_flagged($questionusageid, $slot) {
        return $this->get_question_attempt($questionusageid, $slot)->is_flagged();
    }

    /**
     * @param object $attempt attempt data from db.
     * @param int $slot
     * @return float
     */
    protected function slot_fraction($attempt, $slot) {
        $qa = $this->get_question_attempt($attempt->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$attempt->try];
        if ($step === null) {
            return null;
        }
        if ($this->is_last_try($attempt, $slot, $attempt->try)) {
            // If this is the last try then the step with the try data does not contain the correct fraction. We need to
            // use the last step's fraction, after the attempt has been finished.
            return $qa->get_fraction();
        }
        return $step->get_fraction();
    }

    /**
     * Is this the last try in the question attempt?
     *
     * @param object $attempt attempt data from db.
     * @param int $slot
     * @param int $tryno try no
     * @return bool
     */
    protected function is_last_try($attempt, $slot, $tryno) {
        return $tryno == $this->get_no_of_tries($attempt, $slot);
    }

    /**
     * @param object $attempt attempt data from db.
     * @param int $slot
     * @return int the number of tries in the question attempt for slot $slot.
     */
    public function get_no_of_tries($attempt, $slot) {
        return count($this->get_question_attempt($attempt->usageid, $slot)->get_steps_with_submitted_response_iterator());
    }


    /**
     * @param int $questionusageid
     * @param int $slot
     * @param int $tryno
     * @return int the step no or zero if not found
     */
    protected function step_no_for_try($questionusageid, $slot, $tryno) {
        return $this->get_question_attempt($questionusageid, $slot)->get_steps_with_submitted_response_iterator()->step_no_for_try($tryno);
    }

    public function col_checkbox($attempt) {
        if ($attempt->try != 1) {
            return '';
        } else {
            return parent::col_checkbox($attempt);
        }
    }

    public function col_email($attempt) {
        if ($attempt->try != 1) {
            return '';
        } else {
            return $attempt->email;
        }
    }

    public function col_sumgrades($attempt) {
        if (!$attempt->lasttryforallparts) {
            return '';
        } else {
            return parent::col_sumgrades($attempt);
        }
    }


    public function col_state($attempt) {
        if (!$attempt->lasttryforallparts) {
            return '';
        } else {
            return parent::col_state($attempt);
        }
    }

    public function get_row_class($attempt) {
        if ($this->options->whichtries == question_attempt::ALL_TRIES && $attempt->lasttryforallparts) {
            return 'lastrowforattempt';
        } else {
            return '';
        }
    }

    public function make_review_link($data, $attempt, $slot) {
        if ($this->slot_state($attempt, $slot) === null) {
            return $data;
        } else {
            return parent::make_review_link($data, $attempt, $slot);
        }
    }
}


