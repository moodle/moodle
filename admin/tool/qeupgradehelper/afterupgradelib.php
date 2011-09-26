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
 * Question engine upgrade helper library code that relies on other parts of the
 * new question engine code.
 *
 * @package    tool
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/question/engine/upgrade/upgradelib.php');


class tool_qeupgradehelper_attempt_upgrader extends question_engine_attempt_upgrader {
    public $quizid;
    public $attemptsdone = 0;
    public $attemptstodo;

    public function __construct($quizid, $attemptstodo) {
        $this->quizid = $quizid;
        $this->attemptstodo = $attemptstodo;
    }

    protected function get_quiz_ids() {
        return array($this->quizid);
    }

    protected function print_progress($done, $outof, $quizid) {
    }

    protected function convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs) {
        $this->attemptsdone += 1;
        return parent::convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs);
    }

    protected function reset_progress($done, $outof) {
        if (is_null($this->progressbar)) {
            $this->progressbar = new progress_bar('qe2reset');
            $this->progressbar->create();
        }

        gc_collect_cycles(); // This was really helpful in PHP 5.2. Perhaps remove.
        $a = new stdClass();
        $a->done = $done;
        $a->outof = $outof;
        $this->progressbar->update($done, $outof,
                get_string('resettingquizattemptsprogress', 'tool_qeupgradehelper', $a));
    }

    protected function get_resettable_attempts($quiz) {
        global $DB;
        return $DB->get_records_sql("
                SELECT
                    quiza.*

                FROM {quiz_attempts} quiza
                LEFT JOIN (
                    SELECT attempt, MAX(timestamp) AS time
                    FROM {question_states}
                    GROUP BY attempt
                ) AS oldtimemodified ON oldtimemodified.attempt = quiza.uniqueid
                LEFT JOIN (
                    SELECT qa.questionusageid, MAX(qas.timecreated) AS time
                    FROM {question_attempts} qa
                    JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                    GROUP BY qa.questionusageid
                ) AS newtimemodified ON newtimemodified.questionusageid = quiza.uniqueid

                WHERE quiza.preview = 0
                  AND quiza.needsupgradetonewqe = 0
                  AND (newtimemodified.time IS NULL OR oldtimemodified.time >= newtimemodified.time)
                  AND quiza.quiz = :quizid", array('quizid' => $quiz->id));
    }

    public function reset_all_resettable_attempts() {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $quiz = $DB->get_record('quiz', array('id' => $this->quizid));
        $attempts = $this->get_resettable_attempts($quiz);
        foreach ($attempts as $attempt) {
            $this->reset_attempt($quiz, $attempt);
        }

        $transaction->allow_commit();
    }

    protected function reset_attempt($quiz, $attempt) {
        global $DB;

        $this->attemptsdone += 1;
        $this->reset_progress($this->attemptsdone, $this->attemptstodo);

        $questionids = explode(',', $quiz->questions);
        $slottoquestionid = array(0 => 0);
        foreach ($questionids as $questionid) {
            if ($questionid) {
                $slottoquestionid[] = $questionid;
            }
        }

        $slotlayout = explode(',', $attempt->layout);
        $oldlayout = array();
        $ok = true;
        foreach ($slotlayout as $slot) {
            if (array_key_exists($slot, $slottoquestionid)) {
                $oldlayout[] = $slottoquestionid[$slot];
            } else if (in_array($slot, $questionids)) {
                // OK there was probably a problem during the original upgrade.
                $oldlayout[] = $slot;
            } else {
                $ok = false;
                break;
            }
        }

        if ($ok) {
            $layout = implode(',', $oldlayout);
        } else {
            $layout = $attempt->layout;
        }

        $DB->delete_records_select('question_attempt_step_data', "attemptstepid IN (
                SELECT qas.id
                FROM {question_attempts} qa
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                WHERE questionusageid = :uniqueid)",
                array('uniqueid' => $attempt->uniqueid));
        $DB->delete_records_select('question_attempt_steps', "questionattemptid IN (
                SELECT qa.id
                FROM {question_attempts} qa
                WHERE questionusageid = :uniqueid)",
                array('uniqueid' => $attempt->uniqueid));
        $DB->delete_records('question_attempts',
                array('questionusageid' => $attempt->uniqueid));

        $DB->set_field('question_usages', 'preferredbehaviour', 'to_be_set_later',
                array('id' => $attempt->uniqueid));
        $DB->set_field('quiz_attempts', 'layout', $layout,
                array('uniqueid' => $attempt->uniqueid));
        $DB->set_field('quiz_attempts', 'needsupgradetonewqe', 1,
                array('uniqueid' => $attempt->uniqueid));
    }
}
