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
    public function setUp() {
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
        $this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $this->studentrole->id, 'manual');
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course->id, $this->teacherrole->id, 'manual');
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
        $allusersfields = array('id', 'coursemodule', 'course', 'name', 'intro', 'introformat', 'timeopen', 'timeclose',
                                'grademethod', 'section', 'visible', 'groupmode', 'groupingid');
        $userswithaccessfields = array('timelimit', 'attempts', 'attemptonlast', 'grademethod', 'decimalpoints',
                                        'questiondecimalpoints', 'reviewattempt', 'reviewcorrectness', 'reviewmarks',
                                        'reviewspecificfeedback', 'reviewgeneralfeedback', 'reviewrightanswer',
                                        'reviewoverallfeedback', 'questionsperpage', 'navmethod', 'sumgrades', 'grade',
                                        'browsersecurity', 'delay1', 'delay2', 'showuserpicture', 'showblocks',
                                        'completionattemptsexhausted', 'completionpass', 'autosaveperiod', 'hasquestions',
                                        'hasfeedback', 'overduehandling', 'graceperiod', 'preferredbehaviour', 'canredoquestions');
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

        $quiz2->coursemodule = $quiz2->cmid;
        $quiz2->introformat = 1;
        $quiz2->section = 0;
        $quiz2->visible = true;
        $quiz2->groupmode = 0;
        $quiz2->groupingid = 0;
        $quiz2->hasquestions = 0;
        $quiz2->hasfeedback = 0;
        $quiz2->autosaveperiod = get_config('quiz', 'autosaveperiod');

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

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $tosubmit = array(1 => array('answer' => '3.14'));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);

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

        // Create a new quiz with attempts.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $this->course->id,
                      'sumgrades' => 1);
        $quiz = $quizgenerator->create_instance($data);
        $context = context_module::instance($quiz->cmid);

        try {
            mod_quiz_external::start_attempt($quiz->id);
            $this->fail('Exception expected due to missing questions.');
        } catch (moodle_quiz_exception $e) {
            $this->assertEquals('noquestionsfound', $e->errorcode);
        }

        // Create a question.
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

}
