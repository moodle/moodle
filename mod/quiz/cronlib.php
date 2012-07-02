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
     * @param int $processfrom the value of $processupto the last time update_overdue_attempts was
     *      called called and completed successfully.
     * @param int $processto only process attempt modifed longer ago than this.
     * @return array with two elements, the number of attempt considered, and how many different quizzes that was.
     */
    public function update_overdue_attempts($timenow, $processfrom, $processto) {
        global $DB;

        $attemptstoprocess = $this->get_list_of_overdue_attempts($processfrom, $processto);

        $course = null;
        $quiz = null;
        $cm = null;

        $count = 0;
        $quizcount = 0;
        foreach ($attemptstoprocess as $attempt) {
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
        }

        $attemptstoprocess->close();
        return array($count, $quizcount);
    }

    /**
     * @return moodle_recordset of quiz_attempts that need to be processed because time has
     *     passed. The array is sorted by courseid then quizid.
     */
    protected function get_list_of_overdue_attempts($processfrom, $processto) {
        global $DB;

        // This query should have all the quiz_attempts columns.
        return $DB->get_recordset_sql("
         SELECT quiza.*,
                group_by_results.usertimeclose,
                group_by_results.usertimelimit

           FROM (

         SELECT iquiza.id AS attemptid,
                quiz.course,
                quiz.graceperiod,
                COALESCE(quo.timeclose, MAX(qgo.timeclose), quiz.timeclose) AS usertimeclose,
                COALESCE(quo.timelimit, MAX(qgo.timelimit), quiz.timelimit) AS usertimelimit

           FROM {quiz_attempts} iquiza
           JOIN {quiz} quiz ON quiz.id = iquiza.quiz
      LEFT JOIN {quiz_overrides} quo ON quo.quiz = quiz.id AND quo.userid = iquiza.userid
      LEFT JOIN {quiz_overrides} qgo ON qgo.quiz = quiz.id
      LEFT JOIN {groups_members} gm ON gm.userid = iquiza.userid AND gm.groupid = qgo.groupid

          WHERE iquiza.state IN ('inprogress', 'overdue')
            AND iquiza.timemodified >= :processfrom
            AND iquiza.timemodified < :processto

       GROUP BY iquiza.id,
                quiz.course,
                quiz.timeclose,
                quiz.timelimit,
                quiz.graceperiod,
                quo.timeclose,
                quo.timelimit
        ) group_by_results
           JOIN {quiz_attempts} quiza ON quiza.id = group_by_results.attemptid

          WHERE (
                state = 'inprogress' AND (
                    (usertimeclose > 0 AND :timenow1 > usertimeclose) OR
                    (usertimelimit > 0 AND :timenow2 > quiza.timestart + usertimelimit)
                )
            )
          OR
            (
                state = 'overdue' AND (
                    (usertimeclose > 0 AND :timenow3 > graceperiod + usertimeclose) OR
                    (usertimelimit > 0 AND :timenow4 > graceperiod + quiza.timestart + usertimelimit)
                )
            )

       ORDER BY course, quiz",

                array('processfrom' => $processfrom, 'processto' => $processto,
                    'timenow1' => $processto, 'timenow2' => $processto,
                    'timenow3' => $processto, 'timenow4' => $processto));
    }
}
