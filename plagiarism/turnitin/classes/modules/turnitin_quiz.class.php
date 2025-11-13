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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// phpcs:disable moodle.Commenting.TodoComment
// TODO: Split out all module specific code from plagiarism/turnitin/lib.php.

/**
 * Class turnitin_quiz
 * @package   plagiarism_turnitin
 * @copyright 2012 iParadigms LLC *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class turnitin_quiz {

    /**
     * @var string
     */
    private $modname;
    /**
     * @var string
     */
    public $gradestable;
    /**
     * @var string
     */
    public $filecomponent;

    /**
     *
     */
    public function __construct() {
        $this->modname = 'quiz';
        $this->gradestable = 'grade_grades';
        $this->filecomponent = 'mod_'.$this->modname;
    }

    /**
     * Check whether the user is a tutor
     *
     * @param context $context The context
     * @return bool
     * @throws coding_exception
     */
    public function is_tutor($context) {
        return has_capability($this->get_tutor_capability(), $context);
    }

    /**
     * Whether the user is enrolled on the course and has the capability to submit quiz attempts
     *
     * @param context $context The context
     * @param int $userid The user id
     * @return bool
     * @throws coding_exception
     */
    public function user_enrolled_on_course($context, $userid) {
        return has_capability('mod/'.$this->modname.':attempt', $context, $userid);
    }

    /**
     * Get the author of the quiz attempt
     *
     * @param int $itemid The item id
     * @return void
     */
    public function get_author($itemid = 0) {
        return;
    }

    /**
     * Whether the user has the capability to grade
     *
     * @return string
     */
    public function get_tutor_capability() {
        return 'mod/'.$this->modname.':grade';
    }

    /**
     * Set the content of the quiz attempt
     *
     * @param array $linkarray The link array
     * @return mixed
     */
    public function set_content($linkarray) {
        return $linkarray['content'];
    }

    /**
     * Get the current grade query
     *
     * @param int $userid The user id
     * @param int $moduleid The module id
     * @param int $itemid The item id
     * @return false|mixed|stdClass
     * @throws dml_exception
     */
    public function get_current_gradequery($userid, $moduleid, $itemid = 0) {
        global $DB;

        $currentgradequery = $DB->get_record('grade_grades', ['userid' => $userid, 'itemid' => $itemid]);
        return $currentgradequery;
    }

    // Work out the mark to set.

    /**
     * Calculate the mark for a question attempt based on the grade given for the answer in TFS.
     *
     * @param float $grade The grade given for the answer in TFS
     * @param float $questionmaxmark The maximum mark for the question
     * @param float $quizgrade The total grade for the quiz
     * @return float|int
     */
    public function calculate_mark($grade, $questionmaxmark, $quizgrade) {
        $mark = $grade * ($questionmaxmark / $quizgrade);
        return ($mark < 0) ? 0 : (float)$mark;
    }

    /**
     * Set a new mark for a question attempt based on the grade given for the answer in TFS.
     *
     * @param int $attemptid The attempt id
     * @param string $identifier The question slot identifier
     * @param int $userid The user id
     * @param float $grade The grade given for the answer in TFS
     * @param float $quizgrade The total grade for the quiz
     * @return void
     * @throws dml_exception
     * @throws dml_transaction_exception
     */
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

            $oldidentifier = sha1($answerslot);
            $newidentifier = sha1('quiz_attempt user'.$attempt->get_userid().' cm'.$attempt->get_cmid().
                                  ' slot'.$slot.' attempt'.$attempt->get_attempt_number());

            if ($identifier == $oldidentifier || $identifier == $newidentifier) {
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
