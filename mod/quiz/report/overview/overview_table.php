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
 * This file defines the quiz grades table.
 *
 * @package    quiz
 * @subpackage overview
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This is a table subclass for displaying the quiz grades report.
 *
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_report_overview_table extends quiz_attempt_report_table {

    protected $regradedqs = array();

    public function __construct($quiz, $context, $qmsubselect, $groupstudents,
            $students, $detailedmarks, $questions, $includecheckboxes, $reporturl, $displayoptions) {
        parent::__construct('mod-quiz-report-overview-report', $quiz , $context,
                $qmsubselect, $groupstudents, $students, $questions, $includecheckboxes,
                $reporturl, $displayoptions);
        $this->detailedmarks = $detailedmarks;
    }

    public function build_table() {
        global $DB;

        if ($this->rawdata) {
            $this->strtimeformat = str_replace(',', '', get_string('strftimedatetime'));
            parent::build_table();

            //end of adding data from attempts data to table / download
            //now add averages at bottom of table :
            $params = array($this->quiz->id);
            $averagesql = '
                    SELECT AVG(qg.grade) AS grade, COUNT(qg.grade) AS numaveraged
                    FROM {quiz_grades} qg
                    WHERE quiz = ?';

            $this->add_separator();
            if ($this->is_downloading()) {
                $namekey = 'lastname';
            } else {
                $namekey = 'fullname';
            }
            if ($this->groupstudents) {
                list($usql, $uparams) = $DB->get_in_or_equal($this->groupstudents);
                $record = $DB->get_record_sql($averagesql . ' AND qg.userid ' . $usql,
                        array_merge($params, $uparams));
                $groupaveragerow = array(
                        $namekey => get_string('groupavg', 'grades'),
                        'sumgrades' => $this->format_average($record),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade(
                                            $record->grade, $this->quiz->id, $this->context)));
                if ($this->detailedmarks && ($this->quiz->attempts == 1 || $this->qmsubselect)) {
                    $avggradebyq = $this->load_average_question_grades($this->groupstudents);
                    $groupaveragerow += $this->format_average_grade_for_questions($avggradebyq);
                }
                $this->add_data_keyed($groupaveragerow);
            }

            if ($this->students) {
                list($usql, $uparams) = $DB->get_in_or_equal($this->students);
                $record = $DB->get_record_sql($averagesql . ' AND qg.userid ' . $usql,
                        array_merge($params, $uparams));
                $overallaveragerow = array(
                        $namekey => get_string('overallaverage', 'grades'),
                        'sumgrades' => $this->format_average($record),
                        'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade(
                                            $record->grade, $this->quiz->id, $this->context)));
                if ($this->detailedmarks && ($this->quiz->attempts == 1 || $this->qmsubselect)) {
                    $avggradebyq = $this->load_average_question_grades($this->students);
                    $overallaveragerow += $this->format_average_grade_for_questions($avggradebyq);
                }
                $this->add_data_keyed($overallaveragerow);
            }
        }
    }

    protected function format_average_grade_for_questions($gradeaverages) {
        $row = array();
        if (!$gradeaverages) {
            $gradeaverages = array();
        }
        foreach ($this->questions as $question) {
            if (isset($gradeaverages[$question->slot]) && $question->maxmark > 0) {
                $record = $gradeaverages[$question->slot];
                $record->grade = quiz_rescale_grade(
                        $record->averagefraction * $question->maxmark, $this->quiz, false);
            } else {
                $record = new stdClass();
                $record->grade = null;
                $record->numaveraged = null;
            }
            $row['qsgrade' . $question->slot] = $this->format_average($record, true);
        }
        return $row;
    }

    /**
     * Format an entry in an average row.
     * @param object $record with fields grade and numaveraged
     */
    protected function format_average($record, $question = false) {
        if (is_null($record->grade)) {
            $average = '-';
        } else if ($question) {
            $average = quiz_format_question_grade($this->quiz, $record->grade);
        } else {
            $average = quiz_format_grade($this->quiz, $record->grade);
        }

        if ($this->download) {
            return $average;
        } else if (is_null($record->numaveraged)) {
            return html_writer::tag('span', html_writer::tag('span',
                    $average, array('class' => 'average')), array('class' => 'avgcell'));
        } else {
            return html_writer::tag('span', html_writer::tag('span',
                    $average, array('class' => 'average')) . ' ' . html_writer::tag('span',
                    '(' . $record->numaveraged . ')', array('class' => 'count')),
                    array('class' => 'avgcell'));
        }
    }

    protected function submit_buttons() {
        if (has_capability('mod/quiz:regrade', $this->context)) {
            echo '<input type="submit" name="regrade" value="' .
                    get_string('regradeselected', 'quiz_overview') . '"/>';
        }
        parent::submit_buttons();
    }

    public function col_sumgrades($attempt) {
        if (!$attempt->timefinish) {
            return '-';
        }

        $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
        if ($this->is_downloading()) {
            return $grade;
        }

        if (isset($this->regradedqs[$attempt->usageid])) {
            $newsumgrade = 0;
            $oldsumgrade = 0;
            foreach ($this->questions as $question) {
                if (isset($this->regradedqs[$attempt->usageid][$question->slot])) {
                    $newsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->newfraction * $question->maxmark;
                    $oldsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->oldfraction * $question->maxmark;
                } else {
                    $newsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                    $oldsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                }
            }
            $newsumgrade = quiz_rescale_grade($newsumgrade, $this->quiz);
            $oldsumgrade = quiz_rescale_grade($oldsumgrade, $this->quiz);
            $grade = html_writer::tag('del', $oldsumgrade) . '/' .
                    html_writer::empty_tag('br') . $newsumgrade;
        }
        return html_writer::link(new moodle_url('/mod/quiz/review.php',
                array('attempt' => $attempt->attempt)), $grade,
                array('title' => get_string('reviewattempt', 'quiz')));
    }

    /**
     * @param string $colname the name of the column.
     * @param object $attempt the row of data - see the SQL in display() in
     * mod/quiz/report/overview/report.php to see what fields are present,
     * and what they are called.
     * @return string the contents of the cell.
     */
    public function other_cols($colname, $attempt) {
        if (!preg_match('/^qsgrade(\d+)$/', $colname, $matches)) {
            return null;
        }
        $slot = $matches[1];
        $question = $this->questions[$slot];
        if (!isset($this->lateststeps[$attempt->usageid][$slot])) {
            return '-';
        }

        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        $state = question_state::get($stepdata->state);

        if ($question->maxmark == 0) {
            $grade = '-';
        } else if (is_null($stepdata->fraction)) {
            if ($state == question_state::$needsgrading) {
                $grade = get_string('requiresgrading', 'question');
            } else {
                $grade = '-';
            }
        } else {
            $grade = quiz_rescale_grade(
                    $stepdata->fraction * $question->maxmark, $this->quiz, 'question');
        }

        if ($this->is_downloading()) {
            return $grade;
        }

        if (isset($this->regradedqs[$attempt->usageid][$slot])) {
            $gradefromdb = $grade;
            $newgrade = quiz_rescale_grade(
                    $this->regradedqs[$attempt->usageid][$slot]->newfraction * $question->maxmark,
                    $this->quiz, 'question');
            $oldgrade = quiz_rescale_grade(
                    $this->regradedqs[$attempt->usageid][$slot]->oldfraction * $question->maxmark,
                    $this->quiz, 'question');

            $grade = html_writer::tag('del', $oldgrade) . '/' .
                    html_writer::empty_tag('br') . $newgrade;
        }

        return $this->make_review_link($grade, $attempt, $slot);
    }

    public function col_regraded($attempt) {
        if ($attempt->regraded == '') {
            return '';
        } else if ($attempt->regraded == 0) {
            return get_string('needed', 'quiz_overview');
        } else if ($attempt->regraded == 1) {
            return get_string('done', 'quiz_overview');
        }
    }

    protected function requires_latest_steps_loaded() {
        return $this->detailedmarks;
    }

    protected function is_latest_step_column($column) {
        if (preg_match('/^qsgrade([0-9]+)/', $column, $matches)) {
            return $matches[1];
        }
        return false;
    }

    protected function get_required_latest_state_fields($slot, $alias) {
        return "$alias.fraction * $alias.maxmark AS qsgrade$slot";
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        parent::query_db($pagesize, $useinitialsbar);

        if ($this->detailedmarks && has_capability('mod/quiz:regrade', $this->context)) {
            $this->regradedqs = $this->get_regraded_questions();
        }
    }

    /**
     * Load the average grade for each question, averaged over particular users.
     * @param array $userids the user ids to average over.
     */
    protected function load_average_question_grades($userids) {
        global $DB;

        $qmfilter = '';
        if ($this->quiz->attempts != 1) {
            $qmfilter = '(' . quiz_report_qm_filter_select($this->quiz, 'quiza') . ') AND ';
        }

        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'u');
        $params['quizid'] = $this->quiz->id;
        $qubaids = new qubaid_join(
                '{quiz_attempts} quiza',
                'quiza.uniqueid',
                "quiza.userid $usql AND quiza.quiz = :quizid",
                $params);

        $dm = new question_engine_data_mapper();
        return $dm->load_average_marks($qubaids, array_keys($this->questions));
    }

    /**
     * Get all the questions in all the attempts being displayed that need regrading.
     * @return array A two dimensional array $questionusageid => $slot => $regradeinfo.
     */
    protected function get_regraded_questions() {
        global $DB;

        $qubaids = $this->get_qubaids_condition();
        $regradedqs = $DB->get_records_select('quiz_overview_regrades',
                'questionusageid ' . $qubaids->usage_id_in(), $qubaids->usage_id_in_params());
        return quiz_report_index_by_keys($regradedqs, array('questionusageid', 'slot'));
    }
}
