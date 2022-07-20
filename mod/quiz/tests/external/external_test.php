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
 * Quiz module external functions tests.
 *
 * @package    mod_quiz
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

namespace mod_quiz\external;

use externallib_advanced_testcase;
use mod_quiz_external;
use mod_quiz_display_options;
use question_usage_by_activity;
use quiz;
use quiz_attempt;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Silly class to access mod_quiz_external internal methods.
 *
 * @package mod_quiz
 * @copyright 2016 Juan Leyva <juan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since  Moodle 3.1
 */
class testable_mod_quiz_external extends mod_quiz_external {

    /**
     * Public accessor.
     *
     * @param  array $params Array of parameters including the attemptid and preflight data
     * @param  bool $checkaccessrules whether to check the quiz access rules or not
     * @param  bool $failifoverdue whether to return error if the attempt is overdue
     * @return  array containing the attempt object and access messages
     */
    public static function validate_attempt($params, $checkaccessrules = true, $failifoverdue = true) {
        return parent::validate_attempt($params, $checkaccessrules, $failifoverdue);
    }

    /**
     * Public accessor.
     *
     * @param  array $params Array of parameters including the attemptid
     * @return  array containing the attempt object and display options
     */
    public static function validate_attempt_review($params) {
        return parent::validate_attempt_review($params);
    }
}

/**
 * Quiz module external functions tests
 *
 * @package    mod_quiz
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class external_test extends externallib_advanced_testcase {

    /**
     * Set up for every test
     */
    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $this->course = $this->getDataGenerator()->create_course();
        $this->quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $this->course->id));
        $this->context = \context_module::instance($this->quiz->cmid);
        $this->cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        // Allow student to receive messages.
        $coursecontext = \context_course::instance($this->course->id);
        assign_capability('mod/quiz:emailnotifysubmission', CAP_ALLOW, $this->teacherrole->id, $coursecontext, true);

        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Test that a sequential navigation quiz is not allowing to see questions in advance except if reviewing
     */
    public function test_sequential_navigation_view_attempt() {
        // Test user with full capabilities.
        $quiz = $this->prepare_sequential_quiz();
        $attemptobj = $this->create_quiz_attempt_object($quiz);
        $this->setUser($this->student);
        // Check out of sequence access for view.
        $this->assertNotEmpty(mod_quiz_external::view_attempt($attemptobj->get_attemptid(), 0, []));
        try {
            mod_quiz_external::view_attempt($attemptobj->get_attemptid(), 3, []);
            $this->fail('Exception expected due to out of sequence access.');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('quiz/Out of sequence access', $e->getMessage());
        }
    }

    /**
     * Test that a sequential navigation quiz is not allowing to see questions in advance for a student
     */
    public function test_sequential_navigation_attempt_summary() {
        // Test user with full capabilities.
        $quiz = $this->prepare_sequential_quiz();
        $attemptobj = $this->create_quiz_attempt_object($quiz);
        $this->setUser($this->student);
        // Check that we do not return other questions than the one currently viewed.
        $result = mod_quiz_external::get_attempt_summary($attemptobj->get_attemptid());
        $this->assertCount(1, $result['questions']);
        $this->assertStringContainsString('Question (1)', $result['questions'][0]['html']);
    }

    /**
     * Test that a sequential navigation quiz is not allowing to see questions in advance for student
     */
    public function test_sequential_navigation_get_attempt_data() {
        // Test user with full capabilities.
        $quiz = $this->prepare_sequential_quiz();
        $attemptobj = $this->create_quiz_attempt_object($quiz);
        $this->setUser($this->student);
        // Test invalid instance id.
        try {
            mod_quiz_external::get_attempt_data($attemptobj->get_attemptid(), 2);
            $this->fail('Exception expected due to out of sequence access.');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('quiz/Out of sequence access', $e->getMessage());
        }
        // Now we moved to page 1, we should see page 2 and 1 but not 0 or 3.
        $attemptobj->set_currentpage(1);
        // Test invalid instance id.
        try {
            mod_quiz_external::get_attempt_data($attemptobj->get_attemptid(), 0);
            $this->fail('Exception expected due to out of sequence access.');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('quiz/Out of sequence access', $e->getMessage());
        }

        try {
            mod_quiz_external::get_attempt_data($attemptobj->get_attemptid(), 3);
            $this->fail('Exception expected due to out of sequence access.');
        } catch (\moodle_exception $e) {
            $this->assertStringContainsString('quiz/Out of sequence access', $e->getMessage());
        }

        // Now we can see page 1.
        $result = mod_quiz_external::get_attempt_data($attemptobj->get_attemptid(), 1);
        $this->assertCount(1, $result['questions']);
        $this->assertStringContainsString('Question (2)', $result['questions'][0]['html']);
    }

    /**
     * Prepare quiz for sequential navigation tests
     *
     * @return quiz
     */
    private function prepare_sequential_quiz() {
        // Create a new quiz with 5 questions and one attempt started.
        // Create a new quiz with attempts.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = [
            'course' => $this->course->id,
            'sumgrades' => 2,
            'preferredbehaviour' => 'deferredfeedback',
            'navmethod' => QUIZ_NAVMETHOD_SEQ
        ];
        $quiz = $quizgenerator->create_instance($data);

        // Now generate the questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        for ($pageindex = 1; $pageindex <= 5; $pageindex++) {
            $question = $questiongenerator->create_question('truefalse', null, [
                'category' => $cat->id,
                'questiontext' => ['text' => "Question ($pageindex)"]
            ]);
            quiz_add_quiz_question($question->id, $quiz, $pageindex);
        }

        $quizobj = quiz::create($quiz->id, $this->student->id);
        // Set grade to pass.
        $item = \grade_item::fetch(array('courseid' => $this->course->id, 'itemtype' => 'mod',
            'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = 80;
        $item->update();
        return $quizobj;
    }

    /**
     * Create question attempt
     *
     * @param quiz $quizobj
     * @param int|null $userid
     * @param bool|null $ispreview
     * @return quiz_attempt
     * @throws \moodle_exception
     */
    private function create_quiz_attempt_object($quizobj, $userid = null, $ispreview = false) {
        global $USER;
        $timenow = time();
        // Now, do one attempt.
        $quba = \question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $attemptnumber = 1;
        if (!empty($USER->id)) {
            $attemptnumber = count(quiz_get_user_attempts($quizobj->get_quizid(), $USER->id)) + 1;
        }
        $attempt = quiz_create_attempt($quizobj, $attemptnumber, false, $timenow, $ispreview, $userid ?? $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, $attemptnumber, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);
        $attemptobj = quiz_attempt::create($attempt->id);
        return $attemptobj;
    }
}
