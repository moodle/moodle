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
 * Version information
 *
 * @package    tool
 * @subpackage iomadmerge
 * @copyright  Derick Turner
 * @author     Derick Turner
 * @basedon    admin tool merge by:
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @author     Mike Holzer
 * @author     Forrest Gaston
 * @author     Juan Pablo Torres Herrera
 * @author     Jordi Pujol-Ahulló, SREd, Universitat Rovira i Virgili
 * @author     John Hoopes <hoopes@wisc.edu>, University of Wisconsin - Madison
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_iomadmerge_quiz_testcase extends advanced_testcase {
    /**
     * Configure the test.
     * Create two courses with a quiz in each.
     * Create two users.
     * Enrol the users onto the courses.
     */
    public function setUp(): void {
        global $CFG, $DB;
        require_once("$CFG->dirroot/admin/tool/iomadmerge/lib/iomadmergetool.php");
        $this->resetAfterTest(true);

        // Setup two users to merge.
        $this->user_remove = $this->getDataGenerator()->create_user();
        $this->user_keep   = $this->getDataGenerator()->create_user();

        // Create three courses.
        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();

        $this->quiz1 = $this->add_quiz_to_course($this->course1);
        $this->quiz2 = $this->add_quiz_to_course($this->course2);

        $maninstance1 = $DB->get_record('enrol', array(
            'courseid' => $this->course1->id,
            'enrol'    => 'manual'
        ), '*', MUST_EXIST);

        $maninstance2 = $DB->get_record('enrol', array(
            'courseid' => $this->course2->id,
            'enrol'    => 'manual'
        ), '*', MUST_EXIST);

        $manual = enrol_get_plugin('manual');

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        // Enrol the users on the courses.
        $manual->enrol_user($maninstance1, $this->user_remove->id, $studentrole->id);
        $manual->enrol_user($maninstance1, $this->user_keep->id, $studentrole->id);

        $manual->enrol_user($maninstance2, $this->user_remove->id, $studentrole->id);
        $manual->enrol_user($maninstance2, $this->user_keep->id, $studentrole->id);
    }

    /**
     * Utility method to add a quiz to a course.
     * @param $course
     * @return testable_assign
     */
    private function add_quiz_to_course($course) {
        // Add a quiz to the course.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $quiz          = $quizgenerator->create_instance(array(
            'course' => $course->id,
            'questionsperpage' => 0,
            'grade' => 100.0,
            'sumgrades' => 2
        ));

        // Create a couple of questions using test data in mod_quiz.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat               = $questiongenerator->create_question_category();
        $saq               = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        $numq              = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));

        // Add them to the quiz.
        quiz_add_quiz_question($saq->id, $quiz);
        quiz_add_quiz_question($numq->id, $quiz);

        return $quiz;
    }

    /**
     * Get an answer to the quiz that is 50% right.
     * @return array
     */
    private function get_fiftypercent_answers() {
        return array(
            1 => array('answer' => 'frog'),
            2 => array('answer' => '3.15')
        );
    }

    /**
     * Utility method to get the grade for a user.
     * @param $user
     * @param $quiz
     * @param $course
     * @return testable_assign
     */
    private function get_user_quiz_grade($user, $quiz, $course) {
        $gradebookgrades = \grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $user->id);
        $gradebookitem   = array_shift($gradebookgrades->items);
        $grade     = $gradebookitem->grades[$user->id];
        return $grade->str_grade;
    }

    /**
     * Get an answer to the quiz that is 100% right.
     * @return array
     */
    private function get_hundredpercent_answers() {
        return array(
            1 => array('answer' => 'frog'),
            2 => array('answer' => '3.14')
        );
    }

    /**
     * Utility method to submit an attempt on a quiz.
     * @param $quiz
     * @param $user
     * @param $answers
     * @return testable_assign
     */
    private function submit_quiz_attempt($quiz, $user, $answers) {
        // Create a quiz attempt for the user.
        $quizobj = quiz::create($quiz->id, $user->id);

        // Set up and start an attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $user->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_submitted_actions($timenow, false, $answers);

        $timefinish = time();

        // Finish the attempt.
        $attemptobj->process_finish($timefinish, false);

        return $timefinish;
    }

    /**
     * Have two users attempt the same quiz and then merge them.
     * @group tool_iomadmerge
     * @group tool_iomadmerge_quiz
     */
    public function test_mergeconflictingquizattempts() {
        global $DB;

        $this->submit_quiz_attempt($this->quiz1, $this->user_keep, $this->get_fiftypercent_answers());
        $this->submit_quiz_attempt($this->quiz1, $this->user_remove, $this->get_hundredpercent_answers());

        // User to keep gets 50%, user to remove gets 100%.
        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->user_keep, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->user_remove, $this->quiz1, $this->course1));

        set_config('quizattemptsaction', QuizAttemptsMerger::ACTION_RENUMBER, 'tool_iomadmerge');

        $mut = new IomadMergeTool();
        $mut->merge($this->user_keep->id, $this->user_remove->id);

        // User to remove should now have 100%.
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->user_keep, $this->quiz1, $this->course1));

        $user_remove = $DB->get_record('user', array('id' => $this->user_remove->id));
        $this->assertEquals(1, $user_remove->suspended);
    }

    /**
     * Have two users attempt different quizes and then merge them.
     * @group tool_iomadmerge
     * @group tool_iomadmerge_quiz
     */
    public function test_mergenonconflictingquizattempts() {
        global $DB;

        $this->submit_quiz_attempt($this->quiz1, $this->user_keep, $this->get_fiftypercent_answers());
        $this->submit_quiz_attempt($this->quiz2, $this->user_remove, $this->get_hundredpercent_answers());

        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->user_keep, $this->quiz1, $this->course1));
        $this->assertEquals('-', $this->get_user_quiz_grade($this->user_keep, $this->quiz2, $this->course2));
        $this->assertEquals('-', $this->get_user_quiz_grade($this->user_remove, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->user_remove, $this->quiz2, $this->course2));

        set_config('quizattemptsaction', QuizAttemptsMerger::ACTION_RENUMBER, 'tool_iomadmerge');

        $mut = new IomadMergeTool();
        $mut->merge($this->user_keep->id, $this->user_remove->id);

        $this->assertEquals('50.00', $this->get_user_quiz_grade($this->user_keep, $this->quiz1, $this->course1));
        $this->assertEquals('100.00', $this->get_user_quiz_grade($this->user_keep, $this->quiz2, $this->course2));

        $user_remove = $DB->get_record('user', array('id' => $this->user_remove->id));
        $this->assertEquals(1, $user_remove->suspended);
    }
}
