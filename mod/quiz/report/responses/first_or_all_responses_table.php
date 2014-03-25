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
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This is a table subclass for displaying the quiz responses report, showing first or all tries.
 *
 * @package   quiz_responses
 * @copyright 2014 The Open University
 * @author    Jamie Pratt <me@jamiep.org>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_first_or_all_responses_table extends quiz_last_responses_table {

    /**
     * The full question usage object for each try shown in report.
     *
     * @var question_usage_by_activity[]
     */
    protected $questionusagesbyactivity;

    protected function field_from_extra_data($tablerow, $slot, $field) {
        $questionattempt = $this->get_question_attempt($tablerow->usageid, $slot);
        switch($field) {
            case 'questionsummary' :
                return $questionattempt->get_question_summary();
            case 'responsesummary' :
                return $this->get_summary_after_try($tablerow, $slot);
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
                $newtablerow = clone($attempt);
                $newtablerow->lasttryforallparts = ($try == $maxtriesinanyslot);
                if ($try !== $maxtriesinanyslot) {
                    $newtablerow->state = quiz_attempt::IN_PROGRESS;
                }
                $newtablerow->try = $try;
                $newrawdata[] = $newtablerow;
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
     * Find the state for $slot given after this try.
     *
     * @param object $tablerow row data
     * @param int $slot Slot number.
     * @return question_state The question state after the attempt.
     */
    protected function slot_state($tablerow, $slot) {
        $qa = $this->get_question_attempt($tablerow->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$tablerow->try];
        if ($step === null) {
            return null;
        }
        if ($this->is_last_try($tablerow, $slot, $tablerow->try)) {
            // If this is the last try then the step with the try data does not contain the correct state. We need to
            // use the last step's state, after the attempt has been finished.
            return $qa->get_state();
        }
        return $step->get_state();
    }


    /**
     * Get the summary of the response after the try.
     *
     * @param object $tablerow row data
     * @param int $slot Slot number.
     * @return string summary for the question after this try.
     */
    public function get_summary_after_try($tablerow, $slot) {
        $qa = $this->get_question_attempt($tablerow->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$tablerow->try];
        if ($step === null) {
            return null;
        }
        $qtdata = $step->get_qt_data();
        return $qa->get_question()->summarise_response($qtdata);
    }

    /**
     * Has this question usage been flagged?
     *
     * @param int $questionusageid Question usage id.
     * @param int $slot Slot number
     * @return bool Has it been flagged?
     */
    protected function is_flagged($questionusageid, $slot) {
        return $this->get_question_attempt($questionusageid, $slot)->is_flagged();
    }

    /**
     * The grade for this slot after this try.
     *
     * @param object $tablerow attempt data from db.
     * @param int $slot Slot number.
     * @return float The fraction.
     */
    protected function slot_fraction($tablerow, $slot) {
        $qa = $this->get_question_attempt($tablerow->usageid, $slot);
        $submissionsteps = $qa->get_steps_with_submitted_response_iterator();
        $step = $submissionsteps[$tablerow->try];
        if ($step === null) {
            return null;
        }
        if ($this->is_last_try($tablerow, $slot, $tablerow->try)) {
            // If this is the last try then the step with the try data does not contain the correct fraction. We need to
            // use the last step's fraction, after the attempt has been finished.
            return $qa->get_fraction();
        }
        return $step->get_fraction();
    }

    /**
     * Is this the last try in the question attempt?
     *
     * @param object $tablerow attempt data from db.
     * @param int $slot Slot number
     * @param int $tryno try no
     * @return bool Is it the last try?
     */
    protected function is_last_try($tablerow, $slot, $tryno) {
        return $tryno == $this->get_no_of_tries($tablerow, $slot);
    }

    /**
     * How many tries were attempted at this question in this slot, during this usage?
     *
     * @param object $tablerow attempt data from db.
     * @param int $slot Slot number
     * @return int the number of tries in the question attempt for slot $slot.
     */
    public function get_no_of_tries($tablerow, $slot) {
        return count($this->get_question_attempt($tablerow->usageid, $slot)->get_steps_with_submitted_response_iterator());
    }


    /**
     * What is the step no this try was seen in?
     *
     * @param int $questionusageid The question usage id.
     * @param int $slot Slot number
     * @param int $tryno Try no
     * @return int the step no or zero if not found
     */
    protected function step_no_for_try($questionusageid, $slot, $tryno) {
        $qa = $this->get_question_attempt($questionusageid, $slot);
        return $qa->get_steps_with_submitted_response_iterator()->step_no_for_try($tryno);
    }

    public function col_checkbox($tablerow) {
        if ($tablerow->try != 1) {
            return '';
        } else {
            return parent::col_checkbox($tablerow);
        }
    }

    /**
     * Cell value function for email column. This extracts the contents for any cell in the email column from the row data.
     *
     * @param object $tablerow Row data.
     * @return string   What to put in the cell for this column, for this row data.
     */
    public function col_email($tablerow) {
        if ($tablerow->try != 1) {
            return '';
        } else {
            return $tablerow->email;
        }
    }

    /**
     * Cell value function for sumgrades column. This extracts the contents for any cell in the sumgrades column from the row data.
     *
     * @param object $tablerow Row data.
     * @return string   What to put in the cell for this column, for this row data.
     */
    public function col_sumgrades($tablerow) {
        if (!$tablerow->lasttryforallparts) {
            return '';
        } else {
            return parent::col_sumgrades($tablerow);
        }
    }


    public function col_state($tablerow) {
        if (!$tablerow->lasttryforallparts) {
            return '';
        } else {
            return parent::col_state($tablerow);
        }
    }

    public function get_row_class($tablerow) {
        if ($this->options->whichtries == question_attempt::ALL_TRIES && $tablerow->lasttryforallparts) {
            return 'lastrowforattempt';
        } else {
            return '';
        }
    }

    public function make_review_link($data, $tablerow, $slot) {
        if ($this->slot_state($tablerow, $slot) === null) {
            return $data;
        } else {
            return parent::make_review_link($data, $tablerow, $slot);
        }
    }
}


