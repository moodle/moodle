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
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->dirroot . '/question/engine/upgrade/upgradelib.php');


class local_qeupgradehelper_attempt_upgrader extends question_engine_attempt_upgrader {
    public $quizid;
    public $attemptsdone = 0;
    public $attemptstodo;

    public function __construct($quizid, $attemptstodo) {
        $this->quizid = $quizid;
        $this->attemptstodo = $attemptstodo;
    }

    protected function get_quiz_ids() {
        return array($this->quizid => 1);
    }

    protected function print_progress($done, $outof, $quizid) {
    }

    protected function convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs) {
        $this->attemptsdone += 1;
        print_progress($this->attemptsdone, $this->attemptstodo);
        return parent::convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs);
    }

    protected function get_resettable_attempts($quiz) {
        global $CFG;
        return get_records_sql("
                SELECT
                    quiza.*

                FROM {$CFG->prefix}quiz_attempts quiza
                LEFT JOIN (
                    SELECT attempt, MAX(timestamp) AS time
                    FROM {$CFG->prefix}question_states
                    GROUP BY attempt
                ) AS oldtimemodified ON oldtimemodified.attempt = quiza.uniqueid
                LEFT JOIN (
                    SELECT qa.questionusageid, MAX(qas.timecreated) AS time
                    FROM {$CFG->prefix}question_attempts qa
                    JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
                    GROUP BY qa.questionusageid
                ) AS newtimemodified ON newtimemodified.questionusageid = quiza.uniqueid

                WHERE quiza.preview = 0
                  AND quiza.needsupgradetonewqe = 0
                  AND oldtimemodified.time >= newtimemodified.time
                  AND quiza.quiz = {$quiz->id}");
    }

    public function reset_all_resettable_attempts() {
        begin_sql();
        $quiz = get_record('quiz', 'id', $this->quizid);
        $attempts = $this->get_resettable_attempts($quiz);
        foreach ($attempts as $attempt) {
            $this->reset_attempt($quiz, $attempt);
        }
        commit_sql();
    }

    protected function reset_attempt($quiz, $attempt) {
        global $CFG;

        $this->attemptsdone += 1;
        print_progress($this->attemptsdone, $this->attemptstodo);

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

        delete_records_select('question_attempt_step_data', "attemptstepid IN (
                SELECT qas.id
                FROM {$CFG->prefix}question_attempts qa
                JOIN {$CFG->prefix}question_attempt_steps qas ON qas.questionattemptid = qa.id
                WHERE questionusageid = {$attempt->uniqueid})");
        delete_records_select('question_attempt_steps', "questionattemptid IN (
                SELECT qa.id
                FROM {$CFG->prefix}question_attempts qa
                WHERE questionusageid = {$attempt->uniqueid})");
        delete_records('question_attempts', 'questionusageid', $attempt->uniqueid);

        set_field('question_usages', 'preferredbehaviour', 'to_be_set_later',
                'id', $attempt->uniqueid);
        set_field('quiz_attempts', 'layout', $layout,
                'uniqueid', $attempt->uniqueid);
        set_field('quiz_attempts', 'needsupgradetonewqe', 1,
                'uniqueid', $attempt->uniqueid);
    }
}

class grabber_question_engine_attempt_upgrader extends question_engine_attempt_upgrader {
    public function __construct() {
        $this->questionloader = new question_engine_upgrade_question_loader(null);
    }

    public function format_var($name, $var) {
        $out = var_export($var, true);
        $out = str_replace('<', '&lt;', $out);
        $out = str_replace('ADOFetchObj::__set_state(array(', '(object) array(', $out);
        $out = str_replace('stdClass::__set_state(array(', '(object) array(', $out);
        $out = str_replace('array (', 'array(', $out);
        $out = preg_replace('/=> \n\s*/', '=> ', $out);
        $out = str_replace(')),', '),', $out);
        $out = str_replace('))', ')', $out);
        $out = preg_replace('/\n         (?! )/', "\n                        ", $out);
        $out = preg_replace('/\n       (?! )/',   "\n                        ", $out);
        $out = preg_replace('/\n      (?! )/',    "\n                    ", $out);
        $out = preg_replace('/\n     (?! )/',     "\n                ", $out);
        $out = preg_replace('/\n    (?! )/',      "\n                ", $out);
        $out = preg_replace('/\n   (?! )/',       "\n            ", $out);
        $out = preg_replace('/\n  (?! )/',        "\n            ", $out);
        $out = preg_replace('/\n(?! )/',          "\n        ", $out);
        return "        $name = $out;\n";
    }

    public function display_convert_attempt_input($quiz, $attempt, $question, $qsession, $qstates) {
        echo $this->format_var('$quiz', $quiz);
        echo $this->format_var('$attempt', $attempt);
        echo $this->format_var('$question', $question);
        echo $this->format_var('$qsession', $qsession);
        echo $this->format_var('$qstates', $qstates);
    }
}
