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
 * Library code used by quiz cron.
 *
 * @package   mod_quiz
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');


/**
 * This class holds all the code for automatically updating all attempts that have
 * gone over their time limit.
 *
 * @copyright 2012 the Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_overdue_attempt_updater {

    /**
     * Do the processing required.
     * @param int $timenow the time to consider as 'now' during the processing.
     * @param int $processto only process attempt with timecheckstate longer ago than this.
     * @return array with two elements, the number of attempt considered, and how many different quizzes that was.
     */
    public function update_overdue_attempts($timenow, $processto) {
        global $DB;

        $attemptstoprocess = $this->get_list_of_overdue_attempts($processto);

        $course = null;
        $quiz = null;
        $cm = null;

        $count = 0;
        $quizcount = 0;
        foreach ($attemptstoprocess as $attempt) {
            try {

                // If we have moved on to a different quiz, fetch the new data.
                if (!$quiz || $attempt->quiz != $quiz->id) {
                    $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz), '*', MUST_EXIST);
                    $cm = get_coursemodule_from_instance('quiz', $attempt->quiz);
                    $quizcount += 1;
                }

                // If we have moved on to a different course, fetch the new data.
                if (!$course || $course->id != $quiz->course) {
                    $course = $DB->get_record('course', array('id' => $quiz->course), '*', MUST_EXIST);
                }

                // Make a specialised version of the quiz settings, with the relevant overrides.
                $quizforuser = clone($quiz);
                $quizforuser->timeclose = $attempt->usertimeclose;
                $quizforuser->timelimit = $attempt->usertimelimit;

                // Trigger any transitions that are required.
                $attemptobj = new quiz_attempt($attempt, $quizforuser, $cm, $course);
                $attemptobj->handle_if_time_expired($timenow, false);
                $count += 1;

            } catch (moodle_exception $e) {
                // If an error occurs while processing one attempt, don't let that kill cron.
                mtrace("Error while processing attempt {$attempt->id} at {$attempt->quiz} quiz:");
                mtrace($e->getMessage());
                mtrace($e->getTraceAsString());
            }
        }

        $attemptstoprocess->close();
        return array($count, $quizcount);
    }

    /**
     * @return moodle_recordset of quiz_attempts that need to be processed because time has
     *     passed. The array is sorted by courseid then quizid.
     */
    public function get_list_of_overdue_attempts($processto) {
        global $DB;


        // SQL to compute timeclose and timelimit for each attempt:
        $quizausersql = quiz_get_attempt_usertime_sql();

        // This query should have all the quiz_attempts columns.
        return $DB->get_recordset_sql("
         SELECT quiza.*,
                quizauser.usertimeclose,
                quizauser.usertimelimit

           FROM {quiz_attempts} quiza
           JOIN {quiz} quiz ON quiz.id = quiza.quiz
           JOIN ( $quizausersql ) quizauser ON quizauser.id = quiza.id

          WHERE quiza.state IN ('inprogress', 'overdue')
            AND quiza.timecheckstate <= :processto
       ORDER BY quiz.course, quiza.quiz",

                array('processto' => $processto));
    }
}
