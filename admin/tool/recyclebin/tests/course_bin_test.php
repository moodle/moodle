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
 * Recycle bin tests.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Recycle bin course tests.
 *
 * @package    tool_recyclebin
 * @copyright  2015 University of Kent
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_recyclebin_course_bin_tests extends advanced_testcase {

    /**
     * @var stdClass $course
     */
    protected $course;

    /**
     * @var stdClass the quiz record
     */
    protected $quiz;

    /**
     * Setup for each test.
     */
    protected function setUp() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // We want the course bin to be enabled.
        set_config('coursebinenable', 1, 'tool_recyclebin');

        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->get_plugin_generator('mod_quiz')->create_instance(array(
            'course' => $this->course->id, 'grade' => 100.0, 'sumgrades' => 1
        ));
    }

    /**
     * Check that our hook is called when an activity is deleted.
     */
    public function test_pre_course_module_delete_hook() {
        global $DB;

        // Should have nothing in the recycle bin.
        $this->assertEquals(0, $DB->count_records('tool_recyclebin_course'));

        // Delete the course module.
        course_delete_module($this->quiz->cmid);

        // Now, run the course module deletion adhoc task.
        phpunit_util::run_all_adhoc_tasks();

        // Check the course module is now in the recycle bin.
        $this->assertEquals(1, $DB->count_records('tool_recyclebin_course'));

        // Try with the API.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        $this->assertEquals(1, count($recyclebin->get_items()));
    }

    /**
     * Test that we can restore recycle bin items.
     */
    public function test_restore() {
        global $DB;

        $startcount = $DB->count_records('course_modules');

        // Delete the course module.
        course_delete_module($this->quiz->cmid);

        // Try restoring.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }

        // Check that it was restored and removed from the recycle bin.
        $this->assertEquals($startcount, $DB->count_records('course_modules'));
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Test that we can delete recycle bin items.
     */
    public function test_delete() {
        global $DB;

        $startcount = $DB->count_records('course_modules');

        // Delete the course module.
        course_delete_module($this->quiz->cmid);

        // Now, run the course module deletion adhoc task.
        phpunit_util::run_all_adhoc_tasks();

        // Try purging.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->delete_item($item);
        }

        // Item was deleted, so no course module was restored.
        $this->assertEquals($startcount - 1, $DB->count_records('course_modules'));
        $this->assertEquals(0, count($recyclebin->get_items()));
    }

    /**
     * Test the cleanup task.
     */
    public function test_cleanup_task() {
        global $DB;

        set_config('coursebinexpiry', WEEKSECS, 'tool_recyclebin');

        // Delete the quiz.
        course_delete_module($this->quiz->cmid);

        // Now, run the course module deletion adhoc task.
        phpunit_util::run_all_adhoc_tasks();

        // Set deleted date to the distant past.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $item->timecreated = time() - WEEKSECS;
            $DB->update_record('tool_recyclebin_course', $item);
        }

        // Create another module we are going to delete, but not alter the time it was placed in the recycle bin.
        $book = $this->getDataGenerator()->get_plugin_generator('mod_book')->create_instance(array(
            'course' => $this->course->id));

        course_delete_module($book->cmid);

        // Now, run the course module deletion adhoc task.
        phpunit_util::run_all_adhoc_tasks();

        // Should have 2 items now.
        $this->assertEquals(2, count($recyclebin->get_items()));

        // Execute cleanup task.
        $this->expectOutputRegex("/\[tool_recyclebin\] Deleting item '\d+' from the course recycle bin/");
        $task = new \tool_recyclebin\task\cleanup_course_bin();
        $task->execute();

        // Should only have the book as it was not due to be deleted.
        $items = $recyclebin->get_items();
        $this->assertEquals(1, count($items));
        $deletedbook = reset($items);
        $this->assertEquals($book->name, $deletedbook->name);
    }

    /**
     * Tests that user data is restored when module is restored.
     */
    public function test_coursemodule_restore_with_userdata() {
        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $this->setUser($student);

        set_config('backup_auto_users', true, 'backup');
        $this->create_quiz_attempt($this->quiz, $student);

        // Delete quiz.
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);
        course_delete_module($cm->id);
        phpunit_util::run_all_adhoc_tasks();
        $quizzes = get_coursemodules_in_course('quiz', $this->course->id);
        $this->assertEquals(0, count($quizzes));

        // Restore quiz.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }
        $quizzes = get_coursemodules_in_course('quiz', $this->course->id);
        $this->assertEquals(1, count($quizzes));
        $cm = array_pop($quizzes);

        // Check if user quiz attempt data is restored.
        $attempts = quiz_get_user_attempts($cm->instance, $student->id);
        $this->assertEquals(1, count($attempts));
        $attempt = array_pop($attempts);
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertEquals($student->id, $attemptobj->get_userid());
        $this->assertEquals(true, $attemptobj->is_finished());
    }

    /**
     * Tests that user data is not restored when module is restored.
     */
    public function test_coursemodule_restore_without_userdata() {
        $student = $this->getDataGenerator()->create_and_enrol($this->course, 'student');
        $this->setUser($student);

        set_config('backup_auto_users', false, 'backup');
        $this->create_quiz_attempt($this->quiz, $student);

        // Delete quiz.
        $cm = get_coursemodule_from_instance('quiz', $this->quiz->id);
        course_delete_module($cm->id);
        phpunit_util::run_all_adhoc_tasks();
        $quizzes = get_coursemodules_in_course('quiz', $this->course->id);
        $this->assertEquals(0, count($quizzes));

        // Restore quiz.
        $recyclebin = new \tool_recyclebin\course_bin($this->course->id);
        foreach ($recyclebin->get_items() as $item) {
            $recyclebin->restore_item($item);
        }
        $quizzes = get_coursemodules_in_course('quiz', $this->course->id);
        $this->assertEquals(1, count($quizzes));
        $cm = array_pop($quizzes);

        // Check if user quiz attempt data is restored.
        $attempts = quiz_get_user_attempts($cm->instance, $student->id);
        $this->assertEquals(0, count($attempts));
    }

    /**
     * Add a question to quiz and create a quiz attempt.
     * @param \stdClass $quiz Quiz
     * @param \stdClass $student User
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function create_quiz_attempt($quiz, $student) {
        // Add Question.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $numq = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($numq->id, $quiz);

        // Create quiz attempt.
        $quizobj = quiz::create($quiz->id, $student->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        $tosubmit = array(1 => array('answer' => '0'));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish($timenow, false);
    }
}
