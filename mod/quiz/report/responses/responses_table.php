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
 * This file defines the quiz responses report class.
 *
 * @package    quiz
 * @subpackage responses
 * @copyright  2008 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This is a table subclass for displaying the quiz responses report.
 *
 * @copyright  2008 Jean-Michel Vedrine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_report_responses_table extends quiz_attempt_report_table {

    public function __construct($quiz, $context, $qmsubselect, $qmfilter,
            $attemptsmode, $groupstudents, $students,
            $questions, $includecheckboxes, $reporturl, $displayoptions) {
        parent::__construct('mod-quiz-report-responses-report', $quiz, $context,
                $qmsubselect, $qmfilter, $attemptsmode, $groupstudents, $students,
                $questions, $includecheckboxes, $reporturl, $displayoptions);
    }

    public function build_table() {
        if ($this->rawdata) {
            $this->strtimeformat = str_replace(',', ' ', get_string('strftimedatetime'));
            parent::build_table();
        }
    }

    public function col_sumgrades($attempt) {
        if (!$attempt->timefinish) {
            return '-';
        }

        $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
        if ($this->is_downloading()) {
            return $grade;
        }

        $gradehtml = '<a href="review.php?q=' . $this->quiz->id . '&amp;attempt=' .
                $attempt->attempt . '">' . $grade . '</a>';
        return $gradehtml;
    }

    public function data_col($slot, $field, $attempt) {
        global $CFG;

        if ($attempt->usageid == 0) {
            return '-';
        }

        $question = $this->questions[$slot];
        if (!isset($this->lateststeps[$attempt->usageid][$slot])) {
            return '-';
        }

        $stepdata = $this->lateststeps[$attempt->usageid][$slot];

        if (is_null($stepdata->$field)) {
            $summary = '-';
        } else {
            $summary = trim($stepdata->$field);
        }

        if ($this->is_downloading() || $field != 'responsesummary') {
            return $summary;
        }

        return $this->make_review_link($summary, $attempt, $slot);
    }

    public function other_cols($colname, $attempt) {
        if (preg_match('/^question(\d+)$/', $colname, $matches)) {
            return $this->data_col($matches[1], 'questionsummary', $attempt);

        } else if (preg_match('/^response(\d+)$/', $colname, $matches)) {
            return $this->data_col($matches[1], 'responsesummary', $attempt);

        } else if (preg_match('/^right(\d+)$/', $colname, $matches)) {
            return $this->data_col($matches[1], 'rightanswer', $attempt);

        } else {
            return null;
        }
    }

    protected function requires_latest_steps_loaded() {
        return true;
    }

    protected function is_latest_step_column($column) {
        if (preg_match('/^(?:question|response|right)([0-9]+)/', $column, $matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * Get any fields that might be needed when sorting on date for a particular slot.
     * @param int $slot the slot for the column we want.
     * @param string $alias the table alias for latest state information relating to that slot.
     */
    protected function get_required_latest_state_fields($slot, $alias) {
        return "$alias.questionsummary AS question$slot,
                $alias.rightanswer AS right$slot,
                $alias.responsesummary AS response$slot";
    }
}
