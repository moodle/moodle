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
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.
class turnitin_quiz {

    private $modname;
    public $gradestable;
    public $filecomponent;

    public function __construct() {
        $this->modname = 'quiz';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':attempt', $context, $userid);
    }

    public function get_author($itemid = 0) {
        return;
    }

    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':grade';
    }

    public function set_content($linkarray) {
        return $linkarray['content'];
    }

    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', array('userid' => $userid, 'itemid' => $itemid));
        return $currentgradequery;
    }

    // Work out the mark to set.
    public function calculate_mark($grade, $questionmaxmark, $quizgrade) {
        $mark = $grade * ($questionmaxmark / $quizgrade);
        return ($mark < 0) ? 0 : (float)$mark;
    }

    // Set a new mark for a question attempt based on the grade given for the answer in TFS.
    public function update_mark($attemptid, $identifier, $userid, $grade, $quizgrade) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();
        if (class_exists('\mod_quiz\quiz_attempt')) {
            $quizattemptclass = '\mod_quiz\quiz_attempt';
        } else {
            $quizattemptclass = 'quiz_attempt';
        }

        $attempt = $quizattemptclass::create($attemptid);
        $quba = question_engine::load_questions_usage_by_activity($attempt->get_uniqueid());

        // Loop through each question slot.
        foreach ($attempt->get_slots() as $slot) {
            $answer = $attempt->get_question_attempt($slot)->get_response_summary();
            // Check if this is the slot the mark is for by matching content.

            $answerslot = $answer ? $answer.$slot : $slot;
            if (sha1($answerslot) == $identifier) {
                // Translate the TFS grade to a mark for the question.
                $questionmaxmark = $attempt->get_question_attempt($slot)->get_max_mark();

                $mark = $this->calculate_mark($grade, $questionmaxmark, $quizgrade);
                $quba->get_question_attempt($slot)->manual_grade(
                    'Graded using Turnitin Feedback Studio', $mark, FORMAT_HTML);
            }
        }

        // Save changes.
        question_engine::save_questions_usage_by_activity($quba);

        $update = new stdClass();
        $update->id = $attemptid;
        $update->timemodified = time();
        $update->sumgrades = $quba->get_total_mark();
        $DB->update_record('quiz_attempts', $update);

        if (class_exists('\mod_quiz\grade_calculator')) {
            // Support Moodle 4.3+.
            $attempt->get_quizobj()->get_grade_calculator()->recompute_final_grade($userid);
        } else {
            // Support older Moodle versions.
            quiz_save_best_grade($attempt->get_quiz(), $userid);
        }

        $transaction->allow_commit();
    }

}
