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
class mod_quiz_external_testcase extends externallib_advanced_testcase {

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
        $this->context = context_module::instance($this->quiz->cmid);
        $this->cm = get_coursemodule_from_instance('quiz', $this->quiz->id);

        // Create users.
        $this->student = self::getDataGenerator()->create_user();
        $this->teacher = self::getDataGenerator()->create_user();

        // Users enrolments.
        $this->studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        // Allow student to receive messages.
        $coursecontext = context_course::instance($this->course->id);
        assign_capability('mod/quiz:emailnotifysubmission', CAP_ALLOW, $this->teacherrole->id, $coursecontext, true);

        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
    }

    /**
     * Create a quiz with questions including a started or finished attempt optionally
     *
     * @param  boolean $startattempt whether to start a new attempt
     * @param  boolean $finishattempt whether to finish the new attempt
     * @param  string $behaviour the quiz preferredbehaviour, defaults to 'deferredfeedback'.
     * @param  boolean $includeqattachments whether to include a question that supports attachments, defaults to false.
     * @return array array containing the quiz, context and the attempt
     */
    private function create_quiz_with_questions($startattempt = false, $finishattempt = false, $behaviour = 'deferredfeedback',
            $includeqattachments = false) {

        // Create a new quiz with attempts.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id,
                      'sumgrades' => 2,
                      'preferredbehaviour' => $behaviour);
        $quiz = $quizgenerator->create_instance($data);
        $context = context_module::instance($quiz->cmid);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        if ($includeqattachments) {
            $question = $questiongenerator->create_question('essay', null, array('category' => $cat->id, 'attachments' => 1,
                'attachmentsrequired' => 1));
            quiz_add_quiz_question($question->id, $quiz);
        }

        $quizobj = quiz::create($quiz->id, $this->student->id);

        // Set grade to pass.
        $item = grade_item::fetch(array('courseid' => $this->course->id, 'itemtype' => 'mod',
                                        'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = 80;
        $item->update();

        if ($startattempt or $finishattempt) {
            // Now, do one attempt.
            $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
            $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

            $timenow = time();
            $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $this->student->id);
            quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
            quiz_attempt_save_started($quizobj, $quba, $attempt);
            $attemptobj = quiz_attempt::create($attempt->id);

            if ($finishattempt) {
                // Process some responses from the student.
                $tosubmit = array(1 => array('answer' => '3.14'));
                $attemptobj->process_submitted_actions(time(), false, $tosubmit);

                // Finish the attempt.
                $attemptobj->process_finish(time(), false);
            }
            return array($quiz, $context, $quizobj, $attempt, $attemptobj, $quba);
        } else {
            return array($quiz, $context, $quizobj);
        }

    }

    /*
     * Test get quizzes by courses
     */
    public function test_mod_quiz_get_quizzes_by_courses() {
        global $DB;

        // Create additional course.
        $course2 = self::getDataGenerator()->create_course();

        // Second quiz.
        $record = new stdClass();
        $record->course = $course2->id;
        $record->intro = '<button>Test with HTML allowed.</button>';
        $quiz2 = self::getDataGenerator()->create_module('quiz', $record);

        // Execute real Moodle enrolment as we'll call unenrol() method on the instance later.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->enrol_user($instance2, $this->student->id, $this->studentrole->id);

        self::setUser($this->student);

        $returndescription = mod_quiz_external::get_quizzes_by_courses_returns();

        // Create what we expect to be returned when querying the two courses.
        // First for the student user.
        $allusersfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'introfiles', 'timeopen',
                                'timeclose', 'grademethod', 'section', 'visible', 'groupmode', 'groupingid',
                                'attempts', 'timelimit', 'grademethod', 'decimalpoints', 'questiondecimalpoints', 'sumgrades',
                                'grade', 'preferredbehaviour', 'hasfeedback');
        $userswithaccessfields = array('attemptonlast', 'reviewattempt', 'reviewcorrectness', 'reviewmarks',
                                        'reviewspecificfeedback', 'reviewgeneralfeedback', 'reviewrightanswer',
                                        'reviewoverallfeedback', 'questionsperpage', 'navmethod',
                                        'browsersecurity', 'delay1', 'delay2', 'showuserpicture', 'showblocks',
                                        'completionattemptsexhausted', 'completionpass', 'autosaveperiod', 'hasquestions',
                                        'overduehandling', 'graceperiod', 'canredoquestions', 'allowofflineattempts');
        $managerfields = array('shuffleanswers', 'timecreated', 'timemodified', 'password', 'subnet');

        // Add expected coursemodule and other data.
        $quiz1 = $this->quiz;
        $quiz1->coursemodule = $quiz1->cmid;
        $quiz1->introformat = 1;
        $quiz1->section = 0;
        $quiz1->visible = true;
        $quiz1->groupmode = 0;
        $quiz1->groupingid = 0;
        $quiz1->hasquestions = 0;
        $quiz1->hasfeedback = 0;
        $quiz1->autosaveperiod = get_config('quiz', 'autosaveperiod');
        $quiz1->introfiles = [];

        $quiz2->coursemodule = $quiz2->cmid;
        $quiz2->introformat = 1;
        $quiz2->section = 0;
        $quiz2->visible = true;
        $quiz2->groupmode = 0;
        $quiz2->groupingid = 0;
        $quiz2->hasquestions = 0;
        $quiz2->hasfeedback = 0;
        $quiz2->autosaveperiod = get_config('quiz', 'autosaveperiod');
        $quiz2->introfiles = [];

        foreach (array_merge($allusersfields, $userswithaccessfields) as $field) {
            $expected1[$field] = $quiz1->{$field};
            $expected2[$field] = $quiz2->{$field};
        }

        $expectedquizzes = array($expected2, $expected1);

        // Call the external function passing course ids.
        $result = mod_quiz_external::get_quizzes_by_courses(array($course2->id, $this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);

        $this->assertEquals($expectedquizzes, $result['quizzes']);
        $this->assertCount(0, $result['warnings']);

        // Call the external function without passing course id.
        $result = mod_quiz_external::get_quizzes_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedquizzes, $result['quizzes']);
        $this->assertCount(0, $result['warnings']);

        // Unenrol user from second course and alter expected quizzes.
        $enrol->unenrol_user($instance2, $this->student->id);
        array_shift($expectedquizzes);

        // Call the external function without passing course id.
        $result = mod_quiz_external::get_quizzes_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedquizzes, $result['quizzes']);

        // Call for the second course we unenrolled the user from, expected warning.
        $result = mod_quiz_external::get_quizzes_by_courses(array($course2->id));
        $this->assertCount(1, $result['warnings']);
        $this->assertEquals('1', $result['warnings'][0]['warningcode']);
        $this->assertEquals($course2->id, $result['warnings'][0]['itemid']);

        // Now, try as a teacher for getting all the additional fields.
        self::setUser($this->teacher);

        foreach ($managerfields as $field) {
            $expectedquizzes[0][$field] = $quiz1->{$field};
        }

        $result = mod_quiz_external::get_quizzes_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedquizzes, $result['quizzes']);

        // Admin also should get all the information.
        self::setAdminUser();

        $result = mod_quiz_external::get_quizzes_by_courses(array($this->course->id));
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertEquals($expectedquizzes, $result['quizzes']);

        // Now, prevent access.
        $enrol->enrol_user($instance2, $this->student->id);

        self::setUser($this->student);

        $quiz2->timeclose = time() - DAYSECS;
        $DB->update_record('quiz', $quiz2);

        $result = mod_quiz_external::get_quizzes_by_courses();
        $result = external_api::clean_returnvalue($returndescription, $result);
        $this->assertCount(2, $result['quizzes']);
        // We only see a limited set of fields.
        $this->assertCount(4, $result['quizzes'][0]);
        $this->assertEquals($quiz2->id, $result['quizzes'][0]['id']);
        $this->assertEquals($quiz2->coursemodule, $result['quizzes'][0]['coursemodule']);
        $this->assertEquals($quiz2->course, $result['quizzes'][0]['course']);
        $this->assertEquals($quiz2->name, $result['quizzes'][0]['name']);
        $this->assertEquals($quiz2->course, $result['quizzes'][0]['course']);

        $this->assertFalse(isset($result['quizzes'][0]['timelimit']));

    }

    /**
     * Test test_view_quiz
     */
    public function test_view_quiz() {
        global $DB;

        // Test invalid instance id.
        try {
            mod_quiz_external::view_quiz(0);
            $this->fail('Exception expected due to invalid mod_quiz instance id.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test not-enrolled user.
        $usernotenrolled = self::getDataGenerator()->create_user();
        $this->setUser($usernotenrolled);
        try {
            mod_quiz_external::view_quiz($this->quiz->id);
            $this->fail('Exception expected due to not enrolled user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_quiz_external::view_quiz($this->quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_quiz_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\course_module_viewed', $event);
        $this->assertEquals($this->context, $event->get_context());
        $moodlequiz = new \moodle_url('/mod/quiz/view.php', array('id' => $this->cm->id));
        $this->assertEquals($moodlequiz, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/quiz:view', CAP_PROHIBIT, $this->studentrole->id, $this->context->id);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        try {
            mod_quiz_external::view_quiz($this->quiz->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (moodle_exception $e) {
            $this->assertEquals('requireloginerror', $e->errorcode);
        }

    }

    /**
     * Test get_user_attempts
     */
    public function test_get_user_attempts() {

        // Create a quiz with one attempt finished.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true, true);

        $this->setUser($this->student);
        $result = mod_quiz_external::get_user_attempts($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(1, $result['attempts']);
        $this->assertEquals($attempt->id, $result['attempts'][0]['id']);
        $this->assertEquals($quiz->id, $result['attempts'][0]['quiz']);
        $this->assertEquals($this->student->id, $result['attempts'][0]['userid']);
        $this->assertEquals(1, $result['attempts'][0]['attempt']);

        // Test filters. Only finished.
        $result = mod_quiz_external::get_user_attempts($quiz->id, 0, 'finished', false);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(1, $result['attempts']);
        $this->assertEquals($attempt->id, $result['attempts'][0]['id']);

        // Test filters. All attempts.
        $result = mod_quiz_external::get_user_attempts($quiz->id, 0, 'all', false);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(1, $result['attempts']);
        $this->assertEquals($attempt->id, $result['attempts'][0]['id']);

        // Test filters. Unfinished.
        $result = mod_quiz_external::get_user_attempts($quiz->id, 0, 'unfinished', false);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(0, $result['attempts']);

        // Start a new attempt, but not finish it.
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 2, false, $timenow, false, $this->student->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Test filters. All attempts.
        $result = mod_quiz_external::get_user_attempts($quiz->id, 0, 'all', false);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(2, $result['attempts']);

        // Test filters. Unfinished.
        $result = mod_quiz_external::get_user_attempts($quiz->id, 0, 'unfinished', false);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(1, $result['attempts']);

        // Test manager can see user attempts.
        $this->setUser($this->teacher);
        $result = mod_quiz_external::get_user_attempts($quiz->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(1, $result['attempts']);
        $this->assertEquals($this->student->id, $result['attempts'][0]['userid']);

        $result = mod_quiz_external::get_user_attempts($quiz->id, $this->student->id, 'all');
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_attempts_returns(), $result);

        $this->assertCount(2, $result['attempts']);
        $this->assertEquals($this->student->id, $result['attempts'][0]['userid']);

        // Invalid parameters.
        try {
            mod_quiz_external::get_user_attempts($quiz->id, $this->student->id, 'INVALID_PARAMETER');
            $this->fail('Exception expected due to missing capability.');
        } catch (invalid_parameter_exception $e) {
            $this->assertEquals('invalidparameter', $e->errorcode);
        }
    }

    /**
     * Test get_user_best_grade
     */
    public function test_get_user_best_grade() {
        global $DB;

        $this->setUser($this->student);

        $result = mod_quiz_external::get_user_best_grade($this->quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_best_grade_returns(), $result);

        // No grades yet.
        $this->assertFalse($result['hasgrade']);
        $this->assertTrue(!isset($result['grade']));

        $grade = new stdClass();
        $grade->quiz = $this->quiz->id;
        $grade->userid = $this->student->id;
        $grade->grade = 8.9;
        $grade->timemodified = time();
        $grade->id = $DB->insert_record('quiz_grades', $grade);

        $result = mod_quiz_external::get_user_best_grade($this->quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_best_grade_returns(), $result);

        // Now I have grades.
        $this->assertTrue($result['hasgrade']);
        $this->assertEquals(8.9, $result['grade']);

        // We should not see other users grades.
        $anotherstudent = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotherstudent->id, $this->course->id, $this->studentrole->id, 'manual');

        try {
            mod_quiz_external::get_user_best_grade($this->quiz->id, $anotherstudent->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Teacher must be able to see student grades.
        $this->setUser($this->teacher);

        $result = mod_quiz_external::get_user_best_grade($this->quiz->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_user_best_grade_returns(), $result);

        $this->assertTrue($result['hasgrade']);
        $this->assertEquals(8.9, $result['grade']);

        // Invalid user.
        try {
            mod_quiz_external::get_user_best_grade($this->quiz->id, -1);
            $this->fail('Exception expected due to missing capability.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }

        // Remove the created data.
        $DB->delete_records('quiz_grades', array('id' => $grade->id));

    }
    /**
     * Test get_combined_review_options.
     * This is a basic test, this is already tested in mod_quiz_display_options_testcase.
     */
    public function test_get_combined_review_options() {
        global $DB;

        // Create a new quiz with attempts.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id,
                      'sumgrades' => 1);
        $quiz = $quizgenerator->create_instance($data);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        $quizobj = quiz::create($quiz->id, $this->student->id);

        // Set grade to pass.
        $item = grade_item::fetch(array('courseid' => $this->course->id, 'itemtype' => 'mod',
                                        'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = 80;
        $item->update();

        // Start the passing attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        $this->setUser($this->student);

        $result = mod_quiz_external::get_combined_review_options($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_combined_review_options_returns(), $result);

        // Expected values.
        $expected = array(
            "someoptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 0),
                array("name" => "marks", "value" => 2),
            ),
            "alloptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 0),
                array("name" => "marks", "value" => 2),
            ),
            "warnings" => [],
        );

        $this->assertEquals($expected, $result);

        // Now, finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish($timenow, false);

        $expected = array(
            "someoptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 1),
                array("name" => "marks", "value" => 2),
            ),
            "alloptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 1),
                array("name" => "marks", "value" => 2),
            ),
            "warnings" => [],
        );

        // We should see now the overall feedback.
        $result = mod_quiz_external::get_combined_review_options($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_combined_review_options_returns(), $result);
        $this->assertEquals($expected, $result);

        // Start a new attempt, but not finish it.
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 2, false, $timenow, false, $this->student->id);
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        $expected = array(
            "someoptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 1),
                array("name" => "marks", "value" => 2),
            ),
            "alloptions" => array(
                array("name" => "feedback", "value" => 1),
                array("name" => "generalfeedback", "value" => 1),
                array("name" => "rightanswer", "value" => 1),
                array("name" => "overallfeedback", "value" => 0),
                array("name" => "marks", "value" => 2),
            ),
            "warnings" => [],
        );

        $result = mod_quiz_external::get_combined_review_options($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_combined_review_options_returns(), $result);
        $this->assertEquals($expected, $result);

        // Teacher, for see student options.
        $this->setUser($this->teacher);

        $result = mod_quiz_external::get_combined_review_options($quiz->id, $this->student->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_combined_review_options_returns(), $result);

        $this->assertEquals($expected, $result);

        // Invalid user.
        try {
            mod_quiz_external::get_combined_review_options($quiz->id, -1);
            $this->fail('Exception expected due to missing capability.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }
    }

    /**
     * Test start_attempt
     */
    public function test_start_attempt() {
        global $DB;

        // Create a new quiz with questions.
        list($quiz, $context, $quizobj) = $this->create_quiz_with_questions();

        $this->setUser($this->student);

        // Try to open attempt in closed quiz.
        $quiz->timeopen = time() - WEEKSECS;
        $quiz->timeclose = time() - DAYSECS;
        $DB->update_record('quiz', $quiz);
        $result = mod_quiz_external::start_attempt($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::start_attempt_returns(), $result);

        $this->assertEquals([], $result['attempt']);
        $this->assertCount(1, $result['warnings']);

        // Now with a password.
        $quiz->timeopen = 0;
        $quiz->timeclose = 0;
        $quiz->password = 'abc';
        $DB->update_record('quiz', $quiz);

        try {
            mod_quiz_external::start_attempt($quiz->id, array(array("name" => "quizpassword", "value" => 'bad')));
            $this->fail('Exception expected due to invalid passwod.');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('passworderror', 'quizaccess_password'), $e->errorcode);
        }

        // Now, try everything correct.
        $result = mod_quiz_external::start_attempt($quiz->id, array(array("name" => "quizpassword", "value" => 'abc')));
        $result = external_api::clean_returnvalue(mod_quiz_external::start_attempt_returns(), $result);

        $this->assertEquals(1, $result['attempt']['attempt']);
        $this->assertEquals($this->student->id, $result['attempt']['userid']);
        $this->assertEquals($quiz->id, $result['attempt']['quiz']);
        $this->assertCount(0, $result['warnings']);
        $attemptid = $result['attempt']['id'];

        // We are good, try to start a new attempt now.

        try {
            mod_quiz_external::start_attempt($quiz->id, array(array("name" => "quizpassword", "value" => 'abc')));
            $this->fail('Exception expected due to attempt not finished.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('attemptstillinprogress', $e->errorcode);
        }

        // Finish the started attempt.

        // Process some responses from the student.
        $timenow = time();
        $attemptobj = quiz_attempt::create($attemptid);
        $tosubmit = array(1 => array('answer' => '3.14'));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attemptid);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);

        // We should be able to start a new attempt.
        $result = mod_quiz_external::start_attempt($quiz->id, array(array("name" => "quizpassword", "value" => 'abc')));
        $result = external_api::clean_returnvalue(mod_quiz_external::start_attempt_returns(), $result);

        $this->assertEquals(2, $result['attempt']['attempt']);
        $this->assertEquals($this->student->id, $result['attempt']['userid']);
        $this->assertEquals($quiz->id, $result['attempt']['quiz']);
        $this->assertCount(0, $result['warnings']);

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/quiz:attempt', CAP_PROHIBIT, $this->studentrole->id, $context->id);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        try {
            mod_quiz_external::start_attempt($quiz->id);
            $this->fail('Exception expected due to missing capability.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

    }

    /**
     * Test validate_attempt
     */
    public function test_validate_attempt() {
        global $DB;

        // Create a new quiz with one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true);

        $this->setUser($this->student);

        // Invalid attempt.
        try {
            $params = array('attemptid' => -1, 'page' => 0);
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to invalid attempt id.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Test OK case.
        $params = array('attemptid' => $attempt->id, 'page' => 0);
        $result = testable_mod_quiz_external::validate_attempt($params);
        $this->assertEquals($attempt->id, $result[0]->get_attempt()->id);
        $this->assertEquals([], $result[1]);

        // Test with preflight data.
        $quiz->password = 'abc';
        $DB->update_record('quiz', $quiz);

        try {
            $params = array('attemptid' => $attempt->id, 'page' => 0,
                            'preflightdata' => array(array("name" => "quizpassword", "value" => 'bad')));
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to invalid passwod.');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('passworderror', 'quizaccess_password'), $e->errorcode);
        }

        // Now, try everything correct.
        $params['preflightdata'][0]['value'] = 'abc';
        $result = testable_mod_quiz_external::validate_attempt($params);
        $this->assertEquals($attempt->id, $result[0]->get_attempt()->id);
        $this->assertEquals([], $result[1]);

        // Page out of range.
        $DB->update_record('quiz', $quiz);
        $params['page'] = 4;
        try {
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to page out of range.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('Invalid page number', $e->errorcode);
        }

        $params['page'] = 0;
        // Try to open attempt in closed quiz.
        $quiz->timeopen = time() - WEEKSECS;
        $quiz->timeclose = time() - DAYSECS;
        $DB->update_record('quiz', $quiz);

        // This should work, ommit access rules.
        testable_mod_quiz_external::validate_attempt($params, false);

        // Get a generic error because prior to checking the dates the attempt is closed.
        try {
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to passed dates.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('attempterror', $e->errorcode);
        }

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $attemptobj->process_finish(time(), false);

        try {
            testable_mod_quiz_external::validate_attempt($params, false);
            $this->fail('Exception expected due to attempt finished.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('attemptalreadyclosed', $e->errorcode);
        }

        // Test user with no capabilities.
        // We need a explicit prohibit since this capability is only defined in authenticated user and guest roles.
        assign_capability('mod/quiz:attempt', CAP_PROHIBIT, $this->studentrole->id, $context->id);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        try {
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to missing permissions.');
        } catch (required_capability_exception $e) {
            $this->assertEquals('nopermissions', $e->errorcode);
        }

        // Now try with a different user.
        $this->setUser($this->teacher);

        $params['page'] = 0;
        try {
            testable_mod_quiz_external::validate_attempt($params);
            $this->fail('Exception expected due to not your attempt.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('notyourattempt', $e->errorcode);
        }
    }

    /**
     * Test get_attempt_data
     */
    public function test_get_attempt_data() {
        global $DB;

        $timenow = time();
        // Create a new quiz with one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true);

        // Set correctness mask so questions state can be fetched only after finishing the attempt.
        $DB->set_field('quiz', 'reviewcorrectness', mod_quiz_display_options::IMMEDIATELY_AFTER, array('id' => $quiz->id));

        $quizobj = $attemptobj->get_quizobj();
        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();

        $this->setUser($this->student);

        // We receive one question per page.
        $result = mod_quiz_external::get_attempt_data($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_data_returns(), $result);

        $this->assertEquals($attempt, (object) $result['attempt']);
        $this->assertEquals(1, $result['nextpage']);
        $this->assertCount(0, $result['messages']);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals(1, $result['questions'][0]['slot']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals('numerical', $result['questions'][0]['type']);
        $this->assertArrayNotHasKey('state', $result['questions'][0]);  // We don't receive the state yet.
        $this->assertEquals(get_string('notyetanswered', 'question'), $result['questions'][0]['status']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertEquals(0, $result['questions'][0]['page']);
        $this->assertEmpty($result['questions'][0]['mark']);
        $this->assertEquals(1, $result['questions'][0]['maxmark']);
        $this->assertEquals(1, $result['questions'][0]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);

        // Now try the last page.
        $result = mod_quiz_external::get_attempt_data($attempt->id, 1);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_data_returns(), $result);

        $this->assertEquals($attempt, (object) $result['attempt']);
        $this->assertEquals(-1, $result['nextpage']);
        $this->assertCount(0, $result['messages']);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals(2, $result['questions'][0]['slot']);
        $this->assertEquals(2, $result['questions'][0]['number']);
        $this->assertEquals('numerical', $result['questions'][0]['type']);
        $this->assertArrayNotHasKey('state', $result['questions'][0]);  // We don't receive the state yet.
        $this->assertEquals(get_string('notyetanswered', 'question'), $result['questions'][0]['status']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertEquals(1, $result['questions'][0]['page']);
        $this->assertEquals(1, $result['questions'][0]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);

        // Finish previous attempt.
        $attemptobj->process_finish(time(), false);

        // Now we should receive the question state.
        $result = mod_quiz_external::get_attempt_review($attempt->id, 1);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_review_returns(), $result);
        $this->assertEquals('gaveup', $result['questions'][0]['state']);

        // Change setting and expect two pages.
        $quiz->questionsperpage = 4;
        $DB->update_record('quiz', $quiz);
        quiz_repaginate_questions($quiz->id, $quiz->questionsperpage);

        // Start with new attempt with the new layout.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 2, false, $timenow, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // We receive two questions per page.
        $result = mod_quiz_external::get_attempt_data($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_data_returns(), $result);
        $this->assertCount(2, $result['questions']);
        $this->assertEquals(-1, $result['nextpage']);

        // Check questions looks good.
        $found = 0;
        foreach ($questions as $question) {
            foreach ($result['questions'] as $rquestion) {
                if ($rquestion['slot'] == $question->slot) {
                    $this->assertTrue(strpos($rquestion['html'], "qid=$question->id") !== false);
                    $found++;
                }
            }
        }
        $this->assertEquals(2, $found);

    }

    /**
     * Test get_attempt_data with blocked questions.
     * @since 3.2
     */
    public function test_get_attempt_data_with_blocked_questions() {
        global $DB;

        // Create a new quiz with one attempt started and using immediatefeedback.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(
                true, false, 'immediatefeedback');

        $quizobj = $attemptobj->get_quizobj();

        // Make second question blocked by the first one.
        $structure = $quizobj->get_structure();
        $slots = $structure->get_slots();
        $structure->update_question_dependency(end($slots)->id, true);

        $quizobj->preload_questions();
        $quizobj->load_questions();
        $questions = $quizobj->get_questions();

        $this->setUser($this->student);

        // We receive one question per page.
        $result = mod_quiz_external::get_attempt_data($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_data_returns(), $result);

        $this->assertEquals($attempt, (object) $result['attempt']);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals(1, $result['questions'][0]['slot']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals(false, $result['questions'][0]['blockedbyprevious']);

        // Now try the last page.
        $result = mod_quiz_external::get_attempt_data($attempt->id, 1);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_data_returns(), $result);

        $this->assertEquals($attempt, (object) $result['attempt']);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals(2, $result['questions'][0]['slot']);
        $this->assertEquals(2, $result['questions'][0]['number']);
        $this->assertEquals(true, $result['questions'][0]['blockedbyprevious']);
    }

    /**
     * Test get_attempt_summary
     */
    public function test_get_attempt_summary() {

        $timenow = time();
        // Create a new quiz with one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true);

        $this->setUser($this->student);
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        // Check the state, flagged and mark data is correct.
        $this->assertEquals('todo', $result['questions'][0]['state']);
        $this->assertEquals('todo', $result['questions'][1]['state']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals(2, $result['questions'][1]['number']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertFalse($result['questions'][1]['flagged']);
        $this->assertEmpty($result['questions'][0]['mark']);
        $this->assertEmpty($result['questions'][1]['mark']);
        $this->assertEquals(1, $result['questions'][0]['sequencecheck']);
        $this->assertEquals(1, $result['questions'][1]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][1]['lastactiontime']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);
        $this->assertEquals(false, $result['questions'][1]['hasautosavedstep']);

        // Submit a response for the first question.
        $tosubmit = array(1 => array('answer' => '3.14'));
        $attemptobj->process_submitted_actions(time(), false, $tosubmit);
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        // Check it's marked as completed only the first one.
        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals('todo', $result['questions'][1]['state']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals(2, $result['questions'][1]['number']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertFalse($result['questions'][1]['flagged']);
        $this->assertEmpty($result['questions'][0]['mark']);
        $this->assertEmpty($result['questions'][1]['mark']);
        $this->assertEquals(2, $result['questions'][0]['sequencecheck']);
        $this->assertEquals(1, $result['questions'][1]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][1]['lastactiontime']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);
        $this->assertEquals(false, $result['questions'][1]['hasautosavedstep']);

    }

    /**
     * Test save_attempt
     */
    public function test_save_attempt() {

        $timenow = time();
        // Create a new quiz with one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true);

        // Response for slot 1.
        $prefix = $quba->get_field_prefix(1);
        $data = array(
            array('name' => 'slots', 'value' => 1),
            array('name' => $prefix . ':sequencecheck',
                    'value' => $attemptobj->get_question_attempt(1)->get_sequence_check_count()),
            array('name' => $prefix . 'answer', 'value' => 1),
        );

        $this->setUser($this->student);

        $result = mod_quiz_external::save_attempt($attempt->id, $data);
        $result = external_api::clean_returnvalue(mod_quiz_external::save_attempt_returns(), $result);
        $this->assertTrue($result['status']);

        // Now, get the summary.
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        // Check it's marked as completed only the first one.
        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals('todo', $result['questions'][1]['state']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals(2, $result['questions'][1]['number']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertFalse($result['questions'][1]['flagged']);
        $this->assertEmpty($result['questions'][0]['mark']);
        $this->assertEmpty($result['questions'][1]['mark']);
        $this->assertEquals(1, $result['questions'][0]['sequencecheck']);
        $this->assertEquals(1, $result['questions'][1]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][1]['lastactiontime']);
        $this->assertEquals(true, $result['questions'][0]['hasautosavedstep']);
        $this->assertEquals(false, $result['questions'][1]['hasautosavedstep']);

        // Now, second slot.
        $prefix = $quba->get_field_prefix(2);
        $data = array(
            array('name' => 'slots', 'value' => 2),
            array('name' => $prefix . ':sequencecheck',
                    'value' => $attemptobj->get_question_attempt(1)->get_sequence_check_count()),
            array('name' => $prefix . 'answer', 'value' => 1),
        );

        $result = mod_quiz_external::save_attempt($attempt->id, $data);
        $result = external_api::clean_returnvalue(mod_quiz_external::save_attempt_returns(), $result);
        $this->assertTrue($result['status']);

        // Now, get the summary.
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        // Check it's marked as completed only the first one.
        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals(1, $result['questions'][0]['sequencecheck']);
        $this->assertEquals('complete', $result['questions'][1]['state']);
        $this->assertEquals(1, $result['questions'][1]['sequencecheck']);

    }

    /**
     * Test process_attempt
     */
    public function test_process_attempt() {
        global $DB;

        $timenow = time();
        // Create a new quiz with three questions and one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true, false,
            'deferredfeedback', true);

        // Response for slot 1.
        $prefix = $quba->get_field_prefix(1);
        $data = array(
            array('name' => 'slots', 'value' => 1),
            array('name' => $prefix . ':sequencecheck',
                    'value' => $attemptobj->get_question_attempt(1)->get_sequence_check_count()),
            array('name' => $prefix . 'answer', 'value' => 1),
        );

        $this->setUser($this->student);

        $result = mod_quiz_external::process_attempt($attempt->id, $data);
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $result['state']);

        $result = mod_quiz_external::get_attempt_data($attempt->id, 2);

        // Now, get the summary.
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);
        $this->assertDebuggingCalled(); // Expect $PAGE->set_url debugging.

        // Check it's marked as completed only the first one.
        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals('todo', $result['questions'][1]['state']);
        $this->assertEquals(1, $result['questions'][0]['number']);
        $this->assertEquals(2, $result['questions'][1]['number']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertFalse($result['questions'][1]['flagged']);
        $this->assertEmpty($result['questions'][0]['mark']);
        $this->assertEmpty($result['questions'][1]['mark']);
        $this->assertEquals(2, $result['questions'][0]['sequencecheck']);
        $this->assertEquals(2, $result['questions'][0]['sequencecheck']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertGreaterThanOrEqual($timenow, $result['questions'][0]['lastactiontime']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);
        $this->assertEquals(false, $result['questions'][0]['hasautosavedstep']);

        // Now, second slot.
        $prefix = $quba->get_field_prefix(2);
        $data = array(
            array('name' => 'slots', 'value' => 2),
            array('name' => $prefix . ':sequencecheck',
                    'value' => $attemptobj->get_question_attempt(1)->get_sequence_check_count()),
            array('name' => $prefix . 'answer', 'value' => 1),
            array('name' => $prefix . ':flagged', 'value' => 1),
        );

        $result = mod_quiz_external::process_attempt($attempt->id, $data);
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $result['state']);

        // Now, get the summary.
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        // Check it's marked as completed the two first questions.
        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals('complete', $result['questions'][1]['state']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertTrue($result['questions'][1]['flagged']);

        // Add files in the attachment response.
        $draftitemid = file_get_unused_draft_itemid();
        $filerecordinline = array(
            'contextid' => context_user::instance($this->student->id)->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $draftitemid,
            'filepath'  => '/',
            'filename'  => 'faketxt.txt',
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'fake txt contents 1.');

        // Last slot.
        $prefix = $quba->get_field_prefix(3);
        $data = array(
            array('name' => 'slots', 'value' => 3),
            array('name' => $prefix . ':sequencecheck',
                    'value' => $attemptobj->get_question_attempt(1)->get_sequence_check_count()),
            array('name' => $prefix . 'answer', 'value' => 'Some test'),
            array('name' => $prefix . 'answerformat', 'value' => FORMAT_HTML),
            array('name' => $prefix . 'attachments', 'value' => $draftitemid),
        );

        $result = mod_quiz_external::process_attempt($attempt->id, $data);
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::IN_PROGRESS, $result['state']);

        // Now, get the summary.
        $result = mod_quiz_external::get_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_summary_returns(), $result);

        $this->assertEquals('complete', $result['questions'][0]['state']);
        $this->assertEquals('complete', $result['questions'][1]['state']);
        $this->assertEquals('complete', $result['questions'][2]['state']);
        $this->assertFalse($result['questions'][0]['flagged']);
        $this->assertTrue($result['questions'][1]['flagged']);
        $this->assertFalse($result['questions'][2]['flagged']);

        // Check submitted files are there.
        $this->assertCount(1, $result['questions'][2]['responsefileareas']);
        $this->assertEquals('attachments', $result['questions'][2]['responsefileareas'][0]['area']);
        $this->assertCount(1, $result['questions'][2]['responsefileareas'][0]['files']);
        $this->assertEquals($filerecordinline['filename'], $result['questions'][2]['responsefileareas'][0]['files'][0]['filename']);

        // Finish the attempt.
        $sink = $this->redirectMessages();
        $result = mod_quiz_external::process_attempt($attempt->id, array(), true);
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::FINISHED, $result['state']);
        $messages = $sink->get_messages();
        $message = reset($messages);
        $sink->close();
        // Test customdata.
        if (!empty($message->customdata)) {
            $customdata = json_decode($message->customdata);
            $this->assertEquals($quizobj->get_quizid(), $customdata->instance);
            $this->assertEquals($quizobj->get_cmid(), $customdata->cmid);
            $this->assertEquals($attempt->id, $customdata->attemptid);
            $this->assertObjectHasAttribute('notificationiconurl', $customdata);
        }

        // Start new attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 2, false, $timenow, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 2, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Force grace period, attempt going to overdue.
        $quiz->timeclose = $timenow - 10;
        $quiz->graceperiod = 60;
        $quiz->overduehandling = 'graceperiod';
        $DB->update_record('quiz', $quiz);

        $result = mod_quiz_external::process_attempt($attempt->id, array());
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::OVERDUE, $result['state']);

        // Force grace period for time limit.
        $quiz->timeclose = 0;
        $quiz->timelimit = 1;
        $quiz->graceperiod = 60;
        $quiz->overduehandling = 'graceperiod';
        $DB->update_record('quiz', $quiz);

        $timenow = time();
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $attempt = quiz_create_attempt($quizobj, 3, 2, $timenow - 10, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 2, $timenow - 10);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        $result = mod_quiz_external::process_attempt($attempt->id, array());
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::OVERDUE, $result['state']);

        // New attempt.
        $timenow = time();
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $attempt = quiz_create_attempt($quizobj, 4, 3, $timenow, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 3, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Force abandon.
        $quiz->timeclose = $timenow - HOURSECS;
        $DB->update_record('quiz', $quiz);

        $result = mod_quiz_external::process_attempt($attempt->id, array());
        $result = external_api::clean_returnvalue(mod_quiz_external::process_attempt_returns(), $result);
        $this->assertEquals(quiz_attempt::ABANDONED, $result['state']);

    }

    /**
     * Test validate_attempt_review
     */
    public function test_validate_attempt_review() {
        global $DB;

        // Create a new quiz with one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true);

        $this->setUser($this->student);

        // Invalid attempt, invalid id.
        try {
            $params = array('attemptid' => -1);
            testable_mod_quiz_external::validate_attempt_review($params);
            $this->fail('Exception expected due invalid id.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Invalid attempt, not closed.
        try {
            $params = array('attemptid' => $attempt->id);
            testable_mod_quiz_external::validate_attempt_review($params);
            $this->fail('Exception expected due not closed attempt.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('attemptclosed', $e->errorcode);
        }

        // Test ok case (finished attempt).
        list($quiz, $context, $quizobj, $attempt, $attemptobj) = $this->create_quiz_with_questions(true, true);

        $params = array('attemptid' => $attempt->id);
        testable_mod_quiz_external::validate_attempt_review($params);

        // Teacher should be able to view the review of one student's attempt.
        $this->setUser($this->teacher);
        testable_mod_quiz_external::validate_attempt_review($params);

        // We should not see other students attempts.
        $anotherstudent = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($anotherstudent->id, $this->course->id, $this->studentrole->id, 'manual');

        $this->setUser($anotherstudent);
        try {
            $params = array('attemptid' => $attempt->id);
            testable_mod_quiz_external::validate_attempt_review($params);
            $this->fail('Exception expected due missing permissions.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('noreviewattempt', $e->errorcode);
        }
    }


    /**
     * Test get_attempt_review
     */
    public function test_get_attempt_review() {
        global $DB;

        // Create a new quiz with two questions and one attempt finished.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true, true);

        // Add feedback to the quiz.
        $feedback = new stdClass();
        $feedback->quizid = $quiz->id;
        $feedback->feedbacktext = 'Feedback text 1';
        $feedback->feedbacktextformat = 1;
        $feedback->mingrade = 49;
        $feedback->maxgrade = 100;
        $feedback->id = $DB->insert_record('quiz_feedback', $feedback);

        $feedback->feedbacktext = 'Feedback text 2';
        $feedback->feedbacktextformat = 1;
        $feedback->mingrade = 30;
        $feedback->maxgrade = 48;
        $feedback->id = $DB->insert_record('quiz_feedback', $feedback);

        $result = mod_quiz_external::get_attempt_review($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_review_returns(), $result);

        // Two questions, one completed and correct, the other gave up.
        $this->assertEquals(50, $result['grade']);
        $this->assertEquals(1, $result['attempt']['attempt']);
        $this->assertEquals('finished', $result['attempt']['state']);
        $this->assertEquals(1, $result['attempt']['sumgrades']);
        $this->assertCount(2, $result['questions']);
        $this->assertEquals('gradedright', $result['questions'][0]['state']);
        $this->assertEquals(1, $result['questions'][0]['slot']);
        $this->assertEquals('gaveup', $result['questions'][1]['state']);
        $this->assertEquals(2, $result['questions'][1]['slot']);

        $this->assertCount(1, $result['additionaldata']);
        $this->assertEquals('feedback', $result['additionaldata'][0]['id']);
        $this->assertEquals('Feedback', $result['additionaldata'][0]['title']);
        $this->assertEquals('Feedback text 1', $result['additionaldata'][0]['content']);

        // Only first page.
        $result = mod_quiz_external::get_attempt_review($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_review_returns(), $result);

        $this->assertEquals(50, $result['grade']);
        $this->assertEquals(1, $result['attempt']['attempt']);
        $this->assertEquals('finished', $result['attempt']['state']);
        $this->assertEquals(1, $result['attempt']['sumgrades']);
        $this->assertCount(1, $result['questions']);
        $this->assertEquals('gradedright', $result['questions'][0]['state']);
        $this->assertEquals(1, $result['questions'][0]['slot']);

         $this->assertCount(1, $result['additionaldata']);
        $this->assertEquals('feedback', $result['additionaldata'][0]['id']);
        $this->assertEquals('Feedback', $result['additionaldata'][0]['title']);
        $this->assertEquals('Feedback text 1', $result['additionaldata'][0]['content']);

    }

    /**
     * Test test_view_attempt
     */
    public function test_view_attempt() {
        global $DB;

        // Create a new quiz with two questions and one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true, false);

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_quiz_external::view_attempt($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_attempt_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\attempt_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Now, force the quiz with QUIZ_NAVMETHOD_SEQ (sequencial) navigation method.
        $DB->set_field('quiz', 'navmethod', QUIZ_NAVMETHOD_SEQ, array('id' => $quiz->id));
        // Quiz requiring preflightdata.
        $DB->set_field('quiz', 'password', 'abcdef', array('id' => $quiz->id));
        $preflightdata = array(array("name" => "quizpassword", "value" => 'abcdef'));

        // See next page.
        $result = mod_quiz_external::view_attempt($attempt->id, 1, $preflightdata);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_attempt_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(2, $events);

        // Try to go to previous page.
        try {
            mod_quiz_external::view_attempt($attempt->id, 0);
            $this->fail('Exception expected due to try to see a previous page.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('Out of sequence access', $e->errorcode);
        }

    }

    /**
     * Test test_view_attempt_summary
     */
    public function test_view_attempt_summary() {
        global $DB;

        // Create a new quiz with two questions and one attempt started.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true, false);

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_quiz_external::view_attempt_summary($attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_attempt_summary_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\attempt_summary_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodlequiz = new \moodle_url('/mod/quiz/summary.php', array('attempt' => $attempt->id));
        $this->assertEquals($moodlequiz, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Quiz requiring preflightdata.
        $DB->set_field('quiz', 'password', 'abcdef', array('id' => $quiz->id));
        $preflightdata = array(array("name" => "quizpassword", "value" => 'abcdef'));

        $result = mod_quiz_external::view_attempt_summary($attempt->id, $preflightdata);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_attempt_summary_returns(), $result);
        $this->assertTrue($result['status']);

    }

    /**
     * Test test_view_attempt_summary
     */
    public function test_view_attempt_review() {
        global $DB;

        // Create a new quiz with two questions and one attempt finished.
        list($quiz, $context, $quizobj, $attempt, $attemptobj, $quba) = $this->create_quiz_with_questions(true, true);

        // Test user with full capabilities.
        $this->setUser($this->student);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $result = mod_quiz_external::view_attempt_review($attempt->id, 0);
        $result = external_api::clean_returnvalue(mod_quiz_external::view_attempt_review_returns(), $result);
        $this->assertTrue($result['status']);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_quiz\event\attempt_reviewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodlequiz = new \moodle_url('/mod/quiz/review.php', array('attempt' => $attempt->id));
        $this->assertEquals($moodlequiz, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

    }

    /**
     * Test get_quiz_feedback_for_grade
     */
    public function test_get_quiz_feedback_for_grade() {
        global $DB;

        // Add feedback to the quiz.
        $feedback = new stdClass();
        $feedback->quizid = $this->quiz->id;
        $feedback->feedbacktext = 'Feedback text 1';
        $feedback->feedbacktextformat = 1;
        $feedback->mingrade = 49;
        $feedback->maxgrade = 100;
        $feedback->id = $DB->insert_record('quiz_feedback', $feedback);
        // Add a fake inline image to the feedback text.
        $filename = 'shouldbeanimage.jpg';
        $filerecordinline = array(
            'contextid' => $this->context->id,
            'component' => 'mod_quiz',
            'filearea'  => 'feedback',
            'itemid'    => $feedback->id,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        $feedback->feedbacktext = 'Feedback text 2';
        $feedback->feedbacktextformat = 1;
        $feedback->mingrade = 30;
        $feedback->maxgrade = 49;
        $feedback->id = $DB->insert_record('quiz_feedback', $feedback);

        $result = mod_quiz_external::get_quiz_feedback_for_grade($this->quiz->id, 50);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_feedback_for_grade_returns(), $result);
        $this->assertEquals('Feedback text 1', $result['feedbacktext']);
        $this->assertEquals($filename, $result['feedbackinlinefiles'][0]['filename']);
        $this->assertEquals(FORMAT_HTML, $result['feedbacktextformat']);

        $result = mod_quiz_external::get_quiz_feedback_for_grade($this->quiz->id, 30);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_feedback_for_grade_returns(), $result);
        $this->assertEquals('Feedback text 2', $result['feedbacktext']);
        $this->assertEquals(FORMAT_HTML, $result['feedbacktextformat']);

        $result = mod_quiz_external::get_quiz_feedback_for_grade($this->quiz->id, 10);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_feedback_for_grade_returns(), $result);
        $this->assertEquals('', $result['feedbacktext']);
        $this->assertEquals(FORMAT_MOODLE, $result['feedbacktextformat']);
    }

    /**
     * Test get_quiz_access_information
     */
    public function test_get_quiz_access_information() {
        global $DB;

        // Create a new quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id);
        $quiz = $quizgenerator->create_instance($data);

        $this->setUser($this->student);

        // Default restrictions (none).
        $result = mod_quiz_external::get_quiz_access_information($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_access_information_returns(), $result);

        $expected = array(
            'canattempt' => true,
            'canmanage' => false,
            'canpreview' => false,
            'canreviewmyattempts' => true,
            'canviewreports' => false,
            'accessrules' => [],
            // This rule is always used, even if the quiz has no open or close date.
            'activerulenames' => ['quizaccess_openclosedate'],
            'preventaccessreasons' => [],
            'warnings' => []
        );

        $this->assertEquals($expected, $result);

        // Now teacher, different privileges.
        $this->setUser($this->teacher);
        $result = mod_quiz_external::get_quiz_access_information($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_access_information_returns(), $result);

        $expected['canmanage'] = true;
        $expected['canpreview'] = true;
        $expected['canviewreports'] = true;
        $expected['canattempt'] = false;
        $expected['canreviewmyattempts'] = false;

        $this->assertEquals($expected, $result);

        $this->setUser($this->student);
        // Now add some restrictions.
        $quiz->timeopen = time() + DAYSECS;
        $quiz->timeclose = time() + WEEKSECS;
        $quiz->password = '123456';
        $DB->update_record('quiz', $quiz);

        $result = mod_quiz_external::get_quiz_access_information($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_access_information_returns(), $result);

        // Access limited by time and password.
        $this->assertCount(3, $result['accessrules']);
        // Two rule names, password and open/close date.
        $this->assertCount(2, $result['activerulenames']);
        $this->assertCount(1, $result['preventaccessreasons']);

    }

    /**
     * Test get_attempt_access_information
     */
    public function test_get_attempt_access_information() {
        global $DB;

        $this->setAdminUser();

        // Create a new quiz with attempts.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id,
                      'sumgrades' => 2);
        $quiz = $quizgenerator->create_instance($data);

        // Create some questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        $question = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        // Add new question types in the category (for the random one).
        $question = $questiongenerator->create_question('truefalse', null, array('category' => $cat->id));
        $question = $questiongenerator->create_question('essay', null, array('category' => $cat->id));

        quiz_add_random_questions($quiz, 0, $cat->id, 1, false);

        $quizobj = quiz::create($quiz->id, $this->student->id);

        // Set grade to pass.
        $item = grade_item::fetch(array('courseid' => $this->course->id, 'itemtype' => 'mod',
                                        'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = 80;
        $item->update();

        $this->setUser($this->student);

        // Default restrictions (none).
        $result = mod_quiz_external::get_attempt_access_information($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_access_information_returns(), $result);

        $expected = array(
            'isfinished' => false,
            'preventnewattemptreasons' => [],
            'warnings' => []
        );

        $this->assertEquals($expected, $result);

        // Limited attempts.
        $quiz->attempts = 1;
        $DB->update_record('quiz', $quiz);

        // Now, do one attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);

        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $this->student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $tosubmit = array(1 => array('answer' => '3.14'));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);

        // Can we start a new attempt? We shall not!
        $result = mod_quiz_external::get_attempt_access_information($quiz->id, $attempt->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_attempt_access_information_returns(), $result);

        // Now new attemps allowed.
        $this->assertCount(1, $result['preventnewattemptreasons']);
        $this->assertFalse($result['ispreflightcheckrequired']);
        $this->assertEquals(get_string('nomoreattempts', 'quiz'), $result['preventnewattemptreasons'][0]);

    }

    /**
     * Test get_quiz_required_qtypes
     */
    public function test_get_quiz_required_qtypes() {
        $this->setAdminUser();

        // Create a new quiz.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id);
        $quiz = $quizgenerator->create_instance($data);

        // Create some questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');

        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        $question = $questiongenerator->create_question('shortanswer', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        // Add new question types in the category (for the random one).
        $question = $questiongenerator->create_question('truefalse', null, array('category' => $cat->id));
        $question = $questiongenerator->create_question('essay', null, array('category' => $cat->id));

        quiz_add_random_questions($quiz, 0, $cat->id, 1, false);

        $this->setUser($this->student);

        $result = mod_quiz_external::get_quiz_required_qtypes($quiz->id);
        $result = external_api::clean_returnvalue(mod_quiz_external::get_quiz_required_qtypes_returns(), $result);

        $expected = array(
            'questiontypes' => ['essay', 'numerical', 'random', 'shortanswer', 'truefalse'],
            'warnings' => []
        );

        $this->assertEquals($expected, $result);

    }
}
