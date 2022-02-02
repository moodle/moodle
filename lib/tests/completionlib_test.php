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
 * Completion tests.
 *
 * @package    core_completion
 * @category   phpunit
 * @copyright  2008 Sam Marshall
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/completionlib.php');

/**
 * Completion tests.
 *
 * @package    core_completion
 * @category   phpunit
 * @copyright  2008 Sam Marshall
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \completion_info
 */
class completionlib_test extends advanced_testcase {
    protected $course;
    protected $user;
    protected $module1;
    protected $module2;

    protected function mock_setup() {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        $DB = $this->createMock(get_class($DB));
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $USER = (object)array('id' => 314159);
    }

    /**
     * Create course with user and activities.
     */
    protected function setup_data() {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        $CFG->enablecompletion = true;

        // Create a course with activities.
        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $this->user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($this->user->id, $this->course->id);

        $this->module1 = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id));
        $this->module2 = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id));
    }

    /**
     * Asserts that two variables are equal.
     *
     * @param  mixed   $expected
     * @param  mixed   $actual
     * @param  string  $message
     * @param  float   $delta
     * @param  integer $maxDepth
     * @param  boolean $canonicalize
     * @param  boolean $ignoreCase
     */
    public static function assertEquals($expected, $actual, string $message = '', float $delta = 0, int $maxDepth = 10,
                                        bool $canonicalize = false, bool $ignoreCase = false): void {
        // Nasty cheating hack: prevent random failures on timemodified field.
        if (is_array($actual) && (is_object($expected) || is_array($expected))) {
            $actual = (object) $actual;
            $expected = (object) $expected;
        }
        if (is_object($expected) and is_object($actual)) {
            if (property_exists($expected, 'timemodified') and property_exists($actual, 'timemodified')) {
                if ($expected->timemodified + 1 == $actual->timemodified) {
                    $expected = clone($expected);
                    $expected->timemodified = $actual->timemodified;
                }
            }
        }
        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    /**
     * @covers ::is_enabled_for_site
     * @covers ::is_enabled
     */
    public function test_is_enabled() {
        global $CFG;
        $this->mock_setup();

        // Config alone.
        $CFG->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, completion_info::is_enabled_for_site());
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_ENABLED, completion_info::is_enabled_for_site());

        // Course.
        $course = (object)array('id' => 13);
        $c = new completion_info($course);
        $course->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled());
        $course->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_ENABLED, $c->is_enabled());
        $CFG->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled());

        // Course and CM.
        $cm = new stdClass();
        $cm->completion = COMPLETION_TRACKING_MANUAL;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled($cm));
        $CFG->enablecompletion = COMPLETION_ENABLED;
        $course->enablecompletion = COMPLETION_DISABLED;
        $this->assertEquals(COMPLETION_DISABLED, $c->is_enabled($cm));
        $course->enablecompletion = COMPLETION_ENABLED;
        $this->assertEquals(COMPLETION_TRACKING_MANUAL, $c->is_enabled($cm));
        $cm->completion = COMPLETION_TRACKING_NONE;
        $this->assertEquals(COMPLETION_TRACKING_NONE, $c->is_enabled($cm));
        $cm->completion = COMPLETION_TRACKING_AUTOMATIC;
        $this->assertEquals(COMPLETION_TRACKING_AUTOMATIC, $c->is_enabled($cm));
    }

    /**
     * @covers ::update_state
     */
    public function test_update_state() {
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('is_enabled', 'get_data', 'internal_get_state', 'internal_set_data',
                                       'user_can_override_completion'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));
        $cm = (object)array('id' => 13, 'course' => 42);

        // Not enabled, should do nothing.
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->update_state($cm);

        // Enabled, but current state is same as possible result, do nothing.
        $cm->completion = COMPLETION_TRACKING_AUTOMATIC;
        $c = $mockbuilder->getMock();
        $current = (object)array('completionstate' => COMPLETION_COMPLETE, 'overrideby' => null);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Enabled, but current state is a specific one and new state is just
        // complete, so do nothing.
        $c = $mockbuilder->getMock();
        $current->completionstate = COMPLETION_COMPLETE_PASS;
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Manual, change state (no change).
        $c = $mockbuilder->getMock();
        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_MANUAL);
        $current->completionstate = COMPLETION_COMPLETE;
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->will($this->returnValue($current));
        $c->update_state($cm, COMPLETION_COMPLETE);

        // Manual, change state (change).
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->will($this->returnValue($current));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_INCOMPLETE;
        $comparewith = new phpunit_constraint_object_is_equal_with_exceptions($changed);
        $comparewith->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $c->expects($this->once())
            ->method('internal_set_data')
            ->with($cm, $comparewith);
        $c->update_state($cm, COMPLETION_INCOMPLETE);

        // Auto, change state.
        $c = $mockbuilder->getMock();
        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_AUTOMATIC);
        $current = (object)array('completionstate' => COMPLETION_COMPLETE, 'overrideby' => null);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->will($this->returnValue($current));
        $c->expects($this->once())
            ->method('internal_get_state')
            ->will($this->returnValue(COMPLETION_COMPLETE_PASS));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_COMPLETE_PASS;
        $comparewith = new phpunit_constraint_object_is_equal_with_exceptions($changed);
        $comparewith->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $c->expects($this->once())
            ->method('internal_set_data')
            ->with($cm, $comparewith);
        $c->update_state($cm, COMPLETION_COMPLETE_PASS);

        // Manual tracking, change state by overriding it manually.
        $c = $mockbuilder->getMock();
        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_MANUAL);
        $current1 = (object)array('completionstate' => COMPLETION_INCOMPLETE, 'overrideby' => null);
        $current2 = (object)array('completionstate' => COMPLETION_COMPLETE, 'overrideby' => null);
        $c->expects($this->exactly(2))
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->exactly(1)) // Pretend the user has the required capability for overriding completion statuses.
            ->method('user_can_override_completion')
            ->will($this->returnValue(true));
        $c->expects($this->exactly(2))
            ->method('get_data')
            ->with($cm, false, 100)
            ->willReturnOnConsecutiveCalls($current1, $current2);
        $changed1 = clone($current1);
        $changed1->timemodified = time();
        $changed1->completionstate = COMPLETION_COMPLETE;
        $changed1->overrideby = 314159;
        $comparewith1 = new phpunit_constraint_object_is_equal_with_exceptions($changed1);
        $comparewith1->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $changed2 = clone($current2);
        $changed2->timemodified = time();
        $changed2->overrideby = null;
        $changed2->completionstate = COMPLETION_INCOMPLETE;
        $comparewith2 = new phpunit_constraint_object_is_equal_with_exceptions($changed2);
        $comparewith2->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $c->expects($this->exactly(2))
            ->method('internal_set_data')
            ->withConsecutive(
                array($cm, $comparewith1),
                array($cm, $comparewith2)
            );
        $c->update_state($cm, COMPLETION_COMPLETE, 100, true);
        // And confirm that the status can be changed back to incomplete without an override.
        $c->update_state($cm, COMPLETION_INCOMPLETE, 100);

        // Auto, change state via override, incomplete to complete.
        $c = $mockbuilder->getMock();
        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_AUTOMATIC);
        $current = (object)array('completionstate' => COMPLETION_INCOMPLETE, 'overrideby' => null);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once()) // Pretend the user has the required capability for overriding completion statuses.
            ->method('user_can_override_completion')
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->with($cm, false, 100)
            ->will($this->returnValue($current));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_COMPLETE;
        $changed->overrideby = 314159;
        $comparewith = new phpunit_constraint_object_is_equal_with_exceptions($changed);
        $comparewith->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $c->expects($this->once())
            ->method('internal_set_data')
            ->with($cm, $comparewith);
        $c->update_state($cm, COMPLETION_COMPLETE, 100, true);

        // Now confirm the status can be changed back from complete to incomplete using an override.
        $c = $mockbuilder->getMock();
        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_AUTOMATIC);
        $current = (object)array('completionstate' => COMPLETION_COMPLETE, 'overrideby' => 2);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->Once()) // Pretend the user has the required capability for overriding completion statuses.
            ->method('user_can_override_completion')
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->with($cm, false, 100)
            ->will($this->returnValue($current));
        $changed = clone($current);
        $changed->timemodified = time();
        $changed->completionstate = COMPLETION_INCOMPLETE;
        $changed->overrideby = 314159;
        $comparewith = new phpunit_constraint_object_is_equal_with_exceptions($changed);
        $comparewith->add_exception('timemodified', 'assertGreaterThanOrEqual');
        $c->expects($this->once())
            ->method('internal_set_data')
            ->with($cm, $comparewith);
        $c->update_state($cm, COMPLETION_INCOMPLETE, 100, true);
    }

    /**
     * Data provider for test_internal_get_state().
     *
     * @return array[]
     */
    public function internal_get_state_provider() {
        return [
            'View required, but not viewed yet' => [
                COMPLETION_VIEW_REQUIRED, 1, '', COMPLETION_INCOMPLETE
            ],
            'View not required and not viewed yet' => [
                COMPLETION_VIEW_NOT_REQUIRED, 1, '', COMPLETION_INCOMPLETE
            ],
            'View not required, grade required but no grade yet, $cm->modname not set' => [
                COMPLETION_VIEW_NOT_REQUIRED, 1, 'modname', COMPLETION_INCOMPLETE
            ],
            'View not required, grade required but no grade yet, $cm->course not set' => [
                COMPLETION_VIEW_NOT_REQUIRED, 1, 'course', COMPLETION_INCOMPLETE
            ],
            'View not required, grade not required' => [
                COMPLETION_VIEW_NOT_REQUIRED, 0, '', COMPLETION_COMPLETE
            ],
        ];
    }

    /**
     * Test for completion_info::get_state().
     *
     * @dataProvider internal_get_state_provider
     * @param int $completionview
     * @param int $completionusegrade
     * @param string $unsetfield
     * @param int $expectedstate
     * @covers ::internal_get_state
     */
    public function test_internal_get_state(int $completionview, int $completionusegrade, string $unsetfield, int $expectedstate) {
        $this->setup_data();

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $assigngenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_ENABLED,
            'completionview' => $completionview,
            'completionusegrade' => $completionusegrade,
        ]);

        $userid = $this->user->id;
        $this->setUser($userid);

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        if ($unsetfield) {
            unset($cm->$unsetfield);
        }
        // If view is required, but they haven't viewed it yet.
        $current = (object)['viewed' => COMPLETION_NOT_VIEWED];

        $completioninfo = new completion_info($this->course);
        $this->assertEquals($expectedstate, $completioninfo->internal_get_state($cm, $userid, $current));
    }

    /**
     * Provider for the test_internal_get_state_with_grade_criteria.
     *
     * @return array
     * @covers ::internal_get_state
     */
    public function test_internal_get_state_with_grade_criteria_provider() {
        return [
            "Passing grade enabled and achieve. State should be COMPLETION_COMPLETE_PASS" => [
                [
                    'completionusegrade' => 1,
                    'completionpassgrade' => 1,
                    'gradepass' => 50,
                ],
                50,
                COMPLETION_COMPLETE_PASS
            ],
            "Passing grade enabled and not achieve. State should be COMPLETION_COMPLETE_FAIL" => [
                [
                    'completionusegrade' => 1,
                    'completionpassgrade' => 1,
                    'gradepass' => 50,
                ],
                40,
                COMPLETION_COMPLETE_FAIL
            ],
            "Passing grade not enabled with passing grade set." => [
                [
                    'completionusegrade' => 1,
                    'gradepass' => 50,
                ],
                50,
                COMPLETION_COMPLETE_PASS
            ],
            "Passing grade not enabled with passing grade not set." => [
                [
                    'completionusegrade' => 1,
                ],
                90,
                COMPLETION_COMPLETE
            ],
            "Passing grade not enabled with passing grade not set. No submission made." => [
                [
                    'completionusegrade' => 1,
                ],
                null,
                COMPLETION_INCOMPLETE
            ],
        ];
    }

    /**
     * Tests that the right completion state is being set based on the grade criteria.
     *
     * @dataProvider test_internal_get_state_with_grade_criteria_provider
     * @param array $completioncriteria The completion criteria to use
     * @param int|null $studentgrade Grade to assign to student
     * @param int $expectedstate Expected completion state
     * @covers ::internal_get_state
     */
    public function test_internal_get_state_with_grade_criteria(array $completioncriteria, ?int $studentgrade, int $expectedstate) {
        $this->setup_data();

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $assigngenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_ENABLED,
        ] + $completioncriteria);

        $userid = $this->user->id;

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        $usercm = cm_info::create($cm, $userid);

        // Create a teacher account.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $this->course->id, 'editingteacher');
        // Log in as the teacher.
        $this->setUser($teacher);

        // Grade the student for this assignment.
        $assign = new assign($usercm->context, $cm, $cm->course);
        if ($studentgrade) {
            $data = (object)[
                'sendstudentnotifications' => false,
                'attemptnumber' => 1,
                'grade' => $studentgrade,
            ];
            $assign->save_grade($userid, $data);
        }

        // The target user already received a grade, so internal_get_state should be already complete.
        $completioninfo = new completion_info($this->course);
        $this->assertEquals($expectedstate, $completioninfo->internal_get_state($cm, $userid, null));
    }

    /**
     * Covers the case where internal_get_state() is being called for a user different from the logged in user.
     *
     * @covers ::internal_get_state
     */
    public function test_internal_get_state_with_different_user() {
        $this->setup_data();

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $assigngenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => 1,
        ]);

        $userid = $this->user->id;

        $cm = get_coursemodule_from_instance('assign', $assign->id);
        $usercm = cm_info::create($cm, $userid);

        // Create a teacher account.
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $this->course->id, 'editingteacher');
        // Log in as the teacher.
        $this->setUser($teacher);

        // Grade the student for this assignment.
        $assign = new assign($usercm->context, $cm, $cm->course);
        $data = (object)[
            'sendstudentnotifications' => false,
            'attemptnumber' => 1,
            'grade' => 90,
        ];
        $assign->save_grade($userid, $data);

        // The target user already received a grade, so internal_get_state should be already complete.
        $completioninfo = new completion_info($this->course);
        $this->assertEquals(COMPLETION_COMPLETE, $completioninfo->internal_get_state($cm, $userid, null));

        // As the teacher which does not have a grade in this cm, internal_get_state should return incomplete.
        $this->assertEquals(COMPLETION_INCOMPLETE, $completioninfo->internal_get_state($cm, $teacher->id, null));
    }

    /**
     * Test for internal_get_state() for an activity that supports custom completion.
     *
     * @covers ::internal_get_state
     */
    public function test_internal_get_state_with_custom_completion() {
        $this->setup_data();

        $choicerecord = [
            'course' => $this->course,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionsubmit' => COMPLETION_ENABLED,
        ];
        $choice = $this->getDataGenerator()->create_module('choice', $choicerecord);
        $cminfo = cm_info::create(get_coursemodule_from_instance('choice', $choice->id));

        $completioninfo = new completion_info($this->course);

        // Fetch completion for the user who hasn't made a choice yet.
        $completion = $completioninfo->internal_get_state($cminfo, $this->user->id, COMPLETION_INCOMPLETE);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completion);

        // Have the user make a choice.
        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);
        choice_user_submit_response($optionids[0], $choice, $this->user->id, $this->course, $cminfo);
        $completion = $completioninfo->internal_get_state($cminfo, $this->user->id, COMPLETION_INCOMPLETE);
        $this->assertEquals(COMPLETION_COMPLETE, $completion);
    }

    /**
     * @covers ::set_module_viewed
     */
    public function test_set_module_viewed() {
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('is_enabled', 'get_data', 'internal_set_data', 'update_state'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));
        $cm = (object)array('id' => 13, 'course' => 42);

        // Not tracking completion, should do nothing.
        $c = $mockbuilder->getMock();
        $cm->completionview = COMPLETION_VIEW_NOT_REQUIRED;
        $c->set_module_viewed($cm);

        // Tracking completion but completion is disabled, should do nothing.
        $c = $mockbuilder->getMock();
        $cm->completionview = COMPLETION_VIEW_REQUIRED;
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->set_module_viewed($cm);

        // Now it's enabled, we expect it to get data. If data already has
        // viewed, still do nothing.
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->with($cm, 0)
            ->will($this->returnValue((object)array('viewed' => COMPLETION_VIEWED)));
        $c->set_module_viewed($cm);

        // OK finally one that hasn't been viewed, now it should set it viewed
        // and update state.
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('get_data')
            ->with($cm, false, 1337)
            ->will($this->returnValue((object)array('viewed' => COMPLETION_NOT_VIEWED)));
        $c->expects($this->once())
            ->method('internal_set_data')
            ->with($cm, (object)array('viewed' => COMPLETION_VIEWED));
        $c->expects($this->once())
            ->method('update_state')
            ->with($cm, COMPLETION_COMPLETE, 1337);
        $c->set_module_viewed($cm, 1337);
    }

    /**
     * @covers ::count_user_data
     */
    public function test_count_user_data() {
        global $DB;
        $this->mock_setup();

        $course = (object)array('id' => 13);
        $cm = (object)array('id' => 42);

        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->once())
            ->method('get_field_sql')
            ->will($this->returnValue(666));

        $c = new completion_info($course);
        $this->assertEquals(666, $c->count_user_data($cm));
    }

    /**
     * @covers ::delete_all_state
     */
    public function test_delete_all_state() {
        global $DB;
        $this->mock_setup();

        $course = (object)array('id' => 13);
        $cm = (object)array('id' => 42, 'course' => 13);
        $c = new completion_info($course);

        // Check it works ok without data in session.
        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->once())
            ->method('delete_records')
            ->with('course_modules_completion', array('coursemoduleid' => 42))
            ->will($this->returnValue(true));
        $c->delete_all_state($cm);
    }

    /**
     * @covers ::reset_all_state
     */
    public function test_reset_all_state() {
        global $DB;
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('delete_all_state', 'get_tracked_users', 'update_state'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));
        $c = $mockbuilder->getMock();

        $cm = (object)array('id' => 13, 'course' => 42, 'completion' => COMPLETION_TRACKING_AUTOMATIC);

        /** @var $DB PHPUnit_Framework_MockObject_MockObject */
        $DB->expects($this->once())
            ->method('get_recordset')
            ->will($this->returnValue(
                new core_completionlib_fake_recordset(array((object)array('id' => 1, 'userid' => 100),
                    (object)array('id' => 2, 'userid' => 101)))));

        $c->expects($this->once())
            ->method('delete_all_state')
            ->with($cm);

        $c->expects($this->once())
            ->method('get_tracked_users')
            ->will($this->returnValue(array(
            (object)array('id' => 100, 'firstname' => 'Woot', 'lastname' => 'Plugh'),
            (object)array('id' => 201, 'firstname' => 'Vroom', 'lastname' => 'Xyzzy'))));

        $c->expects($this->exactly(3))
            ->method('update_state')
            ->withConsecutive(
                array($cm, COMPLETION_UNKNOWN, 100),
                array($cm, COMPLETION_UNKNOWN, 101),
                array($cm, COMPLETION_UNKNOWN, 201)
            );

        $c->reset_all_state($cm);
    }

    /**
     * Data provider for test_get_data().
     *
     * @return array[]
     */
    public function get_data_provider() {
        return [
            'No completion record' => [
                false, true, false, COMPLETION_INCOMPLETE
            ],
            'Not completed' => [
                false, true, true, COMPLETION_INCOMPLETE
            ],
            'Completed' => [
                false, true, true, COMPLETION_COMPLETE
            ],
            'Whole course, complete' => [
                true, true, true, COMPLETION_COMPLETE
            ],
            'Get data for another user, result should be not cached' => [
                false, false, true,  COMPLETION_INCOMPLETE
            ],
            'Get data for another user, including whole course, result should be not cached' => [
                true, false, true,  COMPLETION_INCOMPLETE
            ],
        ];
    }

    /**
     * Tests for completion_info::get_data().
     *
     * @dataProvider get_data_provider
     * @param bool $wholecourse Whole course parameter for get_data().
     * @param bool $sameuser Whether the user calling get_data() is the user itself.
     * @param bool $hasrecord Whether to create a course_modules_completion record.
     * @param int $completion The completion state expected.
     * @covers ::get_data
     */
    public function test_get_data(bool $wholecourse, bool $sameuser, bool $hasrecord, int $completion) {
        global $DB;

        $this->setup_data();
        $user = $this->user;

        $choicegenerator = $this->getDataGenerator()->get_plugin_generator('mod_choice');
        $choice = $choicegenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => true,
            'completionsubmit' => true,
        ]);

        $cm = get_coursemodule_from_instance('choice', $choice->id);

        // Let's manually create a course completion record instead of going through the hoops to complete an activity.
        if ($hasrecord) {
            $cmcompletionrecord = (object)[
                'coursemoduleid' => $cm->id,
                'userid' => $user->id,
                'completionstate' => $completion,
                'viewed' => 0,
                'overrideby' => null,
                'timemodified' => 0,
            ];
            $DB->insert_record('course_modules_completion', $cmcompletionrecord);
        }

        // Whether we expect for the returned completion data to be stored in the cache.
        $iscached = true;

        if (!$sameuser) {
            $iscached = false;
            $this->setAdminUser();
        } else {
            $this->setUser($user);
        }

        // Mock other completion data.
        $completioninfo = new completion_info($this->course);

        $result = $completioninfo->get_data($cm, $wholecourse, $user->id);

        // Course module ID of the returned completion data must match this activity's course module ID.
        $this->assertEquals($cm->id, $result->coursemoduleid);
        // User ID of the returned completion data must match the user's ID.
        $this->assertEquals($user->id, $result->userid);
        // The completion state of the returned completion data must match the expected completion state.
        $this->assertEquals($completion, $result->completionstate);

        // If the user has no completion record, then the default record should be returned.
        if (!$hasrecord) {
            $this->assertEquals(0, $result->id);
        }

        // Check that we are including relevant completion data for the module.
        if (!$wholecourse) {
            $this->assertTrue(property_exists($result, 'viewed'));
            $this->assertTrue(property_exists($result, 'customcompletion'));
        }
    }

    /**
     * @covers ::get_data
     */
    public function test_get_data_successive_calls(): void {
        global $DB;

        $this->setup_data();
        $this->setUser($this->user);

        $choicegenerator = $this->getDataGenerator()->get_plugin_generator('mod_choice');
        $choice = $choicegenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => true,
            'completionsubmit' => true,
        ]);

        $cm = get_coursemodule_from_instance('choice', $choice->id);

        // Let's manually create a course completion record instead of going through the hoops to complete an activity.
        $cmcompletionrecord = (object) [
            'coursemoduleid' => $cm->id,
            'userid' => $this->user->id,
            'completionstate' => COMPLETION_NOT_VIEWED,
            'viewed' => 0,
            'overrideby' => null,
            'timemodified' => 0,
        ];
        $DB->insert_record('course_modules_completion', $cmcompletionrecord);

        // Mock other completion data.
        $completioninfo = new completion_info($this->course);

        $modinfo = get_fast_modinfo($this->course);
        $results = [];
        foreach ($modinfo->cms as $testcm) {
            $result = $completioninfo->get_data($testcm, true);
            $this->assertTrue(property_exists($result, 'id'));
            $this->assertTrue(property_exists($result, 'coursemoduleid'));
            $this->assertTrue(property_exists($result, 'userid'));
            $this->assertTrue(property_exists($result, 'completionstate'));
            $this->assertTrue(property_exists($result, 'viewed'));
            $this->assertTrue(property_exists($result, 'overrideby'));
            $this->assertTrue(property_exists($result, 'timemodified'));
            $this->assertFalse(property_exists($result, 'other_cm_completion_data_fetched'));

            $this->assertEquals($testcm->id, $result->coursemoduleid);
            $this->assertEquals($this->user->id, $result->userid);
            $this->assertEquals(0, $result->viewed);

            $results[$testcm->id] = $result;
        }

        $result = $completioninfo->get_data($cm);
        $this->assertTrue(property_exists($result, 'customcompletion'));

        // The data should match when fetching modules individually.
        (cache::make('core', 'completion'))->purge();
        foreach ($modinfo->cms as $testcm) {
            $result = $completioninfo->get_data($testcm, false);
            $this->assertEquals($result, $results[$testcm->id]);
        }
    }

    /**
     * Tests for completion_info::get_other_cm_completion_data().
     *
     * @covers ::get_other_cm_completion_data
     */
    public function test_get_other_cm_completion_data() {
        global $DB;

        $this->setup_data();
        $user = $this->user;

        $this->setAdminUser();

        $choicegenerator = $this->getDataGenerator()->get_plugin_generator('mod_choice');
        $choice = $choicegenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionsubmit' => true,
        ]);

        $cmchoice = cm_info::create(get_coursemodule_from_instance('choice', $choice->id));

        $choice2 = $choicegenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
        ]);

        $cmchoice2 = cm_info::create(get_coursemodule_from_instance('choice', $choice2->id));

        $workshopgenerator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        $workshop = $workshopgenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            // Submission grade required.
            'completiongradeitemnumber' => 0,
            'completionpassgrade' => 1,
        ]);

        $cmworkshop = cm_info::create(get_coursemodule_from_instance('workshop', $workshop->id));

        $completioninfo = new completion_info($this->course);

        $method = new ReflectionMethod("completion_info", "get_other_cm_completion_data");
        $method->setAccessible(true);

        // Check that fetching data for a module with custom completion provides its info.
        $choicecompletiondata = $method->invoke($completioninfo, $cmchoice, $user->id);

        $this->assertArrayHasKey('customcompletion', $choicecompletiondata);
        $this->assertArrayHasKey('completionsubmit', $choicecompletiondata['customcompletion']);
        $this->assertEquals(COMPLETION_INCOMPLETE, $choicecompletiondata['customcompletion']['completionsubmit']);

        // Mock a choice answer so user has completed the requirement.
        $choicemockinfo = [
            'choiceid' => $cmchoice->instance,
            'userid' => $this->user->id
        ];
        $DB->insert_record('choice_answers', $choicemockinfo, false);

        // Confirm fetching again reflects the completion.
        $choicecompletiondata = $method->invoke($completioninfo, $cmchoice, $user->id);
        $this->assertEquals(COMPLETION_COMPLETE, $choicecompletiondata['customcompletion']['completionsubmit']);

        // Check that fetching data for a module with no custom completion still provides its grade completion status.
        $workshopcompletiondata = $method->invoke($completioninfo, $cmworkshop, $user->id);

        $this->assertArrayHasKey('completiongrade', $workshopcompletiondata);
        $this->assertArrayHasKey('passgrade', $workshopcompletiondata);
        $this->assertArrayNotHasKey('customcompletion', $workshopcompletiondata);
        $this->assertEquals(COMPLETION_INCOMPLETE, $workshopcompletiondata['completiongrade']);
        $this->assertEquals(COMPLETION_INCOMPLETE, $workshopcompletiondata['passgrade']);

        // Check that fetching data for a module with no completion conditions does not provide any data.
        $choice2completiondata = $method->invoke($completioninfo, $cmchoice2, $user->id);
        $this->assertEmpty($choice2completiondata);
    }

    /**
     * @covers ::internal_set_data
     */
    public function test_internal_set_data() {
        global $DB;
        $this->setup_data();

        $this->setUser($this->user);
        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id), $completionauto);
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $c = new completion_info($this->course);

        // 1) Test with new data.
        $data = new stdClass();
        $data->id = 0;
        $data->userid = $this->user->id;
        $data->coursemoduleid = $cm->id;
        $data->completionstate = COMPLETION_COMPLETE;
        $data->timemodified = time();
        $data->viewed = COMPLETION_NOT_VIEWED;
        $data->overrideby = null;

        $c->internal_set_data($cm, $data);
        $d1 = $DB->get_field('course_modules_completion', 'id', array('coursemoduleid' => $cm->id));
        $this->assertEquals($d1, $data->id);
        $cache = cache::make('core', 'completion');
        // Cache was not set for another user.
        $cachevalue = $cache->get("{$data->userid}_{$cm->course}");
        $this->assertEquals([
            'cacherev' => $this->course->cacherev,
            $cm->id => array_merge(
                (array) $data,
                ['other_cm_completion_data_fetched' => true]
            ),
        ],
        $cachevalue);

        // 2) Test with existing data and for different user.
        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id), $completionauto);
        $cm2 = get_coursemodule_from_instance('forum', $forum2->id);
        $newuser = $this->getDataGenerator()->create_user();

        $d2 = new stdClass();
        $d2->id = 7;
        $d2->userid = $newuser->id;
        $d2->coursemoduleid = $cm2->id;
        $d2->completionstate = COMPLETION_COMPLETE;
        $d2->timemodified = time();
        $d2->viewed = COMPLETION_NOT_VIEWED;
        $d2->overrideby = null;
        $c->internal_set_data($cm2, $d2);
        // Cache for current user returns the data.
        $cachevalue = $cache->get($data->userid . '_' . $cm->course);
        $this->assertEquals(array_merge(
            (array) $data,
            ['other_cm_completion_data_fetched' => true]
        ), $cachevalue[$cm->id]);

        // Cache for another user is not filled.
        $this->assertEquals(false, $cache->get($d2->userid . '_' . $cm2->course));

        // 3) Test where it THINKS the data is new (from cache) but actually in the database it has been set since.
        $forum3 = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id), $completionauto);
        $cm3 = get_coursemodule_from_instance('forum', $forum3->id);
        $newuser2 = $this->getDataGenerator()->create_user();
        $d3 = new stdClass();
        $d3->id = 13;
        $d3->userid = $newuser2->id;
        $d3->coursemoduleid = $cm3->id;
        $d3->completionstate = COMPLETION_COMPLETE;
        $d3->timemodified = time();
        $d3->viewed = COMPLETION_NOT_VIEWED;
        $d3->overrideby = null;
        $DB->insert_record('course_modules_completion', $d3);
        $c->internal_set_data($cm, $data);

        // 4) Test instant course completions.
        $dataactivity = $this->getDataGenerator()->create_module('data', array('course' => $this->course->id),
            array('completion' => 1));
        $cm = get_coursemodule_from_instance('data', $dataactivity->id);
        $c = new completion_info($this->course);
        $cmdata = get_coursemodule_from_id('data', $dataactivity->cmid);

        // Add activity completion criteria.
        $criteriadata = new stdClass();
        $criteriadata->id = $this->course->id;
        $criteriadata->criteria_activity = array();
        // Some activities.
        $criteriadata->criteria_activity[$cmdata->id] = 1;
        $class = 'completion_criteria_activity';
        $criterion = new $class();
        $criterion->update_config($criteriadata);

        $actual = $DB->get_records('course_completions');
        $this->assertEmpty($actual);

        $data->coursemoduleid = $cm->id;
        $c->internal_set_data($cm, $data);
        $actual = $DB->get_records('course_completions');
        $this->assertEquals(1, count($actual));
        $this->assertEquals($this->user->id, reset($actual)->userid);

        $data->userid = $newuser2->id;
        $c->internal_set_data($cm, $data, true);
        $actual = $DB->get_records('course_completions');
        $this->assertEquals(1, count($actual));
        $this->assertEquals($this->user->id, reset($actual)->userid);
    }

    /**
     * @covers ::get_progress_all
     */
    public function test_get_progress_all_few() {
        global $DB;
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('get_tracked_users'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));
        $c = $mockbuilder->getMock();

        // With few results.
        $c->expects($this->once())
            ->method('get_tracked_users')
            ->with(false,  array(),  0,  '',  '',  '',  null)
            ->will($this->returnValue(array(
                (object)array('id' => 100, 'firstname' => 'Woot', 'lastname' => 'Plugh'),
                (object)array('id' => 201, 'firstname' => 'Vroom', 'lastname' => 'Xyzzy'))));
        $DB->expects($this->once())
            ->method('get_in_or_equal')
            ->with(array(100, 201))
            ->will($this->returnValue(array(' IN (100, 201)', array())));
        $progress1 = (object)array('userid' => 100, 'coursemoduleid' => 13);
        $progress2 = (object)array('userid' => 201, 'coursemoduleid' => 14);
        $DB->expects($this->once())
            ->method('get_recordset_sql')
            ->will($this->returnValue(new core_completionlib_fake_recordset(array($progress1, $progress2))));

        $this->assertEquals(array(
                100 => (object)array('id' => 100, 'firstname' => 'Woot', 'lastname' => 'Plugh',
                    'progress' => array(13 => $progress1)),
                201 => (object)array('id' => 201, 'firstname' => 'Vroom', 'lastname' => 'Xyzzy',
                    'progress' => array(14 => $progress2)),
            ), $c->get_progress_all(false));
    }

    /**
     * @covers ::get_progress_all
     */
    public function test_get_progress_all_lots() {
        global $DB;
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('get_tracked_users'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));
        $c = $mockbuilder->getMock();

        $tracked = array();
        $ids = array();
        $progress = array();
        // With more than 1000 results.
        for ($i = 100; $i < 2000; $i++) {
            $tracked[] = (object)array('id' => $i, 'firstname' => 'frog', 'lastname' => $i);
            $ids[] = $i;
            $progress[] = (object)array('userid' => $i, 'coursemoduleid' => 13);
            $progress[] = (object)array('userid' => $i, 'coursemoduleid' => 14);
        }
        $c->expects($this->once())
            ->method('get_tracked_users')
            ->with(true,  3,  0,  '',  '',  '',  null)
            ->will($this->returnValue($tracked));
        $DB->expects($this->exactly(2))
            ->method('get_in_or_equal')
            ->withConsecutive(
                array(array_slice($ids, 0, 1000)),
                array(array_slice($ids, 1000))
            )
            ->willReturnOnConsecutiveCalls(
                array(' IN whatever', array()),
                array(' IN whatever2', array()));
        $DB->expects($this->exactly(2))
            ->method('get_recordset_sql')
            ->willReturnOnConsecutiveCalls(
                new core_completionlib_fake_recordset(array_slice($progress, 0, 1000)),
                new core_completionlib_fake_recordset(array_slice($progress, 1000)));

        $result = $c->get_progress_all(true, 3);
        $resultok = true;
        $resultok = $resultok && ($ids == array_keys($result));

        foreach ($result as $userid => $data) {
            $resultok = $resultok && $data->firstname == 'frog';
            $resultok = $resultok && $data->lastname == $userid;
            $resultok = $resultok && $data->id == $userid;
            $cms = $data->progress;
            $resultok = $resultok && (array(13, 14) == array_keys($cms));
            $resultok = $resultok && ((object)array('userid' => $userid, 'coursemoduleid' => 13) == $cms[13]);
            $resultok = $resultok && ((object)array('userid' => $userid, 'coursemoduleid' => 14) == $cms[14]);
        }
        $this->assertTrue($resultok);
        $this->assertCount(count($tracked), $result);
    }

    /**
     * @covers ::inform_grade_changed
     */
    public function test_inform_grade_changed() {
        $this->mock_setup();

        $mockbuilder = $this->getMockBuilder('completion_info');
        $mockbuilder->onlyMethods(array('is_enabled', 'update_state'));
        $mockbuilder->setConstructorArgs(array((object)array('id' => 42)));

        $cm = (object)array('course' => 42, 'id' => 13, 'completion' => 0, 'completiongradeitemnumber' => null);
        $item = (object)array('itemnumber' => 3,  'gradepass' => 1,  'hidden' => 0);
        $grade = (object)array('userid' => 31337,  'finalgrade' => 0,  'rawgrade' => 0);

        // Not enabled (should do nothing).
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(false));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled but still no grade completion required,  should still do nothing.
        $c = $mockbuilder->getMock();
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled and completion required but item number is wrong,  does nothing.
        $c = $mockbuilder->getMock();
        $cm = (object)array('course' => 42, 'id' => 13, 'completion' => 0, 'completiongradeitemnumber' => 7);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Enabled and completion required and item number right. It is supposed
        // to call update_state with the new potential state being obtained from
        // internal_get_grade_state.
        $c = $mockbuilder->getMock();
        $cm = (object)array('course' => 42, 'id' => 13, 'completion' => 0, 'completiongradeitemnumber' => 3);
        $grade = (object)array('userid' => 31337,  'finalgrade' => 1,  'rawgrade' => 0);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('update_state')
            ->with($cm, COMPLETION_COMPLETE_PASS, 31337)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, false);

        // Same as above but marked deleted. It is supposed to call update_state
        // with new potential state being COMPLETION_INCOMPLETE.
        $c = $mockbuilder->getMock();
        $cm = (object)array('course' => 42, 'id' => 13, 'completion' => 0, 'completiongradeitemnumber' => 3);
        $grade = (object)array('userid' => 31337,  'finalgrade' => 1,  'rawgrade' => 0);
        $c->expects($this->once())
            ->method('is_enabled')
            ->with($cm)
            ->will($this->returnValue(true));
        $c->expects($this->once())
            ->method('update_state')
            ->with($cm, COMPLETION_INCOMPLETE, 31337)
            ->will($this->returnValue(true));
        $c->inform_grade_changed($cm, $item, $grade, true);
    }

    /**
     * @covers ::internal_get_grade_state
     */
    public function test_internal_get_grade_state() {
        $this->mock_setup();

        $item = new stdClass;
        $grade = new stdClass;

        $item->gradepass = 4;
        $item->hidden = 0;
        $grade->rawgrade = 4.0;
        $grade->finalgrade = null;

        // Grade has pass mark and is not hidden,  user passes.
        $this->assertEquals(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item, $grade));

        // Same but user fails.
        $grade->rawgrade = 3.9;
        $this->assertEquals(
            COMPLETION_COMPLETE_FAIL,
            completion_info::internal_get_grade_state($item, $grade));

        // User fails on raw grade but passes on final.
        $grade->finalgrade = 4.0;
        $this->assertEquals(
            COMPLETION_COMPLETE_PASS,
            completion_info::internal_get_grade_state($item, $grade));

        // Item is hidden.
        $item->hidden = 1;
        $this->assertEquals(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item, $grade));

        // Item isn't hidden but has no pass mark.
        $item->hidden = 0;
        $item->gradepass = 0;
        $this->assertEquals(
            COMPLETION_COMPLETE,
            completion_info::internal_get_grade_state($item, $grade));
    }

    /**
     * @test ::get_activities
     */
    public function test_get_activities() {
        global $CFG;
        $this->resetAfterTest();

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        $CFG->enablecompletion = true;

        // Create a course with mixed auto completion data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $completionmanual = array('completion' => COMPLETION_TRACKING_MANUAL);
        $completionnone = array('completion' => COMPLETION_TRACKING_NONE);
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id), $completionauto);
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id), $completionauto);
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id), $completionmanual);

        $forum2 = $this->getDataGenerator()->create_module('forum', array('course' => $course->id), $completionnone);
        $page2 = $this->getDataGenerator()->create_module('page', array('course' => $course->id), $completionnone);
        $data2 = $this->getDataGenerator()->create_module('data', array('course' => $course->id), $completionnone);

        // Create data in another course to make sure it's not considered.
        $course2 = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $c2forum = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id), $completionauto);
        $c2page = $this->getDataGenerator()->create_module('page', array('course' => $course2->id), $completionmanual);
        $c2data = $this->getDataGenerator()->create_module('data', array('course' => $course2->id), $completionnone);

        $c = new completion_info($course);
        $activities = $c->get_activities();
        $this->assertCount(3, $activities);
        $this->assertTrue(isset($activities[$forum->cmid]));
        $this->assertSame($forum->name, $activities[$forum->cmid]->name);
        $this->assertTrue(isset($activities[$page->cmid]));
        $this->assertSame($page->name, $activities[$page->cmid]->name);
        $this->assertTrue(isset($activities[$data->cmid]));
        $this->assertSame($data->name, $activities[$data->cmid]->name);

        $this->assertFalse(isset($activities[$forum2->cmid]));
        $this->assertFalse(isset($activities[$page2->cmid]));
        $this->assertFalse(isset($activities[$data2->cmid]));
    }

    /**
     * @test ::has_activities
     */
    public function test_has_activities() {
        global $CFG;
        $this->resetAfterTest();

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        $CFG->enablecompletion = true;

        // Create a course with mixed auto completion data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $course2 = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $completionnone = array('completion' => COMPLETION_TRACKING_NONE);
        $c1forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id), $completionauto);
        $c2forum = $this->getDataGenerator()->create_module('forum', array('course' => $course2->id), $completionnone);

        $c1 = new completion_info($course);
        $c2 = new completion_info($course2);

        $this->assertTrue($c1->has_activities());
        $this->assertFalse($c2->has_activities());
    }

    /**
     * Test that data is cleaned up when we delete courses that are set as completion criteria for other courses
     *
     * @covers ::delete_course_completion_data
     * @covers ::delete_all_completion_data
     */
    public function test_course_delete_prerequisite() {
        global $DB;

        $this->setup_data();

        $courseprerequisite = $this->getDataGenerator()->create_course(['enablecompletion' => true]);

        $criteriadata = (object) [
            'id' => $this->course->id,
            'criteria_course' => [$courseprerequisite->id],
        ];

        /** @var completion_criteria_course $criteria */
        $criteria = completion_criteria::factory(['criteriatype' => COMPLETION_CRITERIA_TYPE_COURSE]);
        $criteria->update_config($criteriadata);

        // Sanity test.
        $this->assertTrue($DB->record_exists('course_completion_criteria', [
            'course' => $this->course->id,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_COURSE,
            'courseinstance' => $courseprerequisite->id,
        ]));

        // Deleting the prerequisite course should remove the completion criteria.
        delete_course($courseprerequisite, false);

        $this->assertFalse($DB->record_exists('course_completion_criteria', [
            'course' => $this->course->id,
            'criteriatype' => COMPLETION_CRITERIA_TYPE_COURSE,
            'courseinstance' => $courseprerequisite->id,
        ]));
    }

    /**
     * Test course module completion update event.
     *
     * @covers \core\event\course_module_completion_updated
     */
    public function test_course_module_completion_updated_event() {
        global $USER, $CFG;

        $this->setup_data();

        $this->setAdminUser();

        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $this->course->id), $completionauto);

        $c = new completion_info($this->course);
        $activities = $c->get_activities();
        $this->assertEquals(1, count($activities));
        $this->assertTrue(isset($activities[$forum->cmid]));
        $this->assertEquals($activities[$forum->cmid]->name, $forum->name);

        $current = $c->get_data($activities[$forum->cmid], false, $this->user->id);
        $current->completionstate = COMPLETION_COMPLETE;
        $current->timemodified = time();
        $sink = $this->redirectEvents();
        $c->internal_set_data($activities[$forum->cmid], $current);
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\core\event\course_module_completion_updated', $event);
        $this->assertEquals($forum->cmid,
            $event->get_record_snapshot('course_modules_completion', $event->objectid)->coursemoduleid);
        $this->assertEquals($current, $event->get_record_snapshot('course_modules_completion', $event->objectid));
        $this->assertEquals(context_module::instance($forum->cmid), $event->get_context());
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->user->id, $event->relateduserid);
        $this->assertInstanceOf('moodle_url', $event->get_url());
        $this->assertEventLegacyData($current, $event);
    }

    /**
     * Test course completed event.
     *
     * @covers \core\event\course_completed
     */
    public function test_course_completed_event() {
        global $USER;

        $this->setup_data();
        $this->setAdminUser();

        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $ccompletion = new completion_completion(array('course' => $this->course->id, 'userid' => $this->user->id));

        // Mark course as complete and get triggered event.
        $sink = $this->redirectEvents();
        $ccompletion->mark_complete();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\core\event\course_completed', $event);
        $this->assertEquals($this->course->id, $event->get_record_snapshot('course_completions', $event->objectid)->course);
        $this->assertEquals($this->course->id, $event->courseid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals($this->user->id, $event->relateduserid);
        $this->assertEquals(context_course::instance($this->course->id), $event->get_context());
        $this->assertInstanceOf('moodle_url', $event->get_url());
        $data = $ccompletion->get_record_data();
        $this->assertEventLegacyData($data, $event);
    }

    /**
     * Test course completed message.
     *
     * @covers \core\event\course_completed
     */
    public function test_course_completed_message() {
        $this->setup_data();
        $this->setAdminUser();

        $completionauto = array('completion' => COMPLETION_TRACKING_AUTOMATIC);
        $ccompletion = new completion_completion(array('course' => $this->course->id, 'userid' => $this->user->id));

        // Mark course as complete and get the message.
        $sink = $this->redirectMessages();
        $ccompletion->mark_complete();
        $messages = $sink->get_messages();
        $sink->close();

        $this->assertCount(1, $messages);
        $message = array_pop($messages);

        $this->assertEquals(core_user::get_noreply_user()->id, $message->useridfrom);
        $this->assertEquals($this->user->id, $message->useridto);
        $this->assertEquals('coursecompleted', $message->eventtype);
        $this->assertEquals(get_string('coursecompleted', 'completion'), $message->subject);
        $this->assertStringContainsString($this->course->fullname, $message->fullmessage);
    }

    /**
     * Test course completed event.
     *
     * @covers \core\event\course_completion_updated
     */
    public function test_course_completion_updated_event() {
        $this->setup_data();
        $coursecontext = context_course::instance($this->course->id);
        $coursecompletionevent = \core\event\course_completion_updated::create(
                array(
                    'courseid' => $this->course->id,
                    'context' => $coursecontext
                    )
                );

        // Mark course as complete and get triggered event.
        $sink = $this->redirectEvents();
        $coursecompletionevent->trigger();
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();

        $this->assertInstanceOf('\core\event\course_completion_updated', $event);
        $this->assertEquals($this->course->id, $event->courseid);
        $this->assertEquals($coursecontext, $event->get_context());
        $this->assertInstanceOf('moodle_url', $event->get_url());
        $expectedlegacylog = array($this->course->id, 'course', 'completion updated', 'completion.php?id='.$this->course->id);
        $this->assertEventLegacyLogData($expectedlegacylog, $event);
    }

    /**
     * @covers \completion_can_view_data
     */
    public function test_completion_can_view_data() {
        $this->setup_data();

        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $this->course->id);

        $this->setUser($student);
        $this->assertTrue(completion_can_view_data($student->id, $this->course->id));
        $this->assertFalse(completion_can_view_data($this->user->id, $this->course->id));
    }

    /**
     * Data provider for test_get_grade_completion().
     *
     * @return array[]
     */
    public function get_grade_completion_provider() {
        return [
            'Grade not required' => [false, false, null, null, null],
            'Grade required, but has no grade yet' => [true, false, null, null, COMPLETION_INCOMPLETE],
            'Grade required, grade received' => [true, true, null, null, COMPLETION_COMPLETE],
            'Grade required, passing grade received' => [true, true, 70, null, COMPLETION_COMPLETE_PASS],
            'Grade required, failing grade received' => [true, true, 80, null, COMPLETION_COMPLETE_FAIL],
        ];
    }

    /**
     * Test for \completion_info::get_grade_completion().
     *
     * @dataProvider get_grade_completion_provider
     * @param bool $completionusegrade Whether the test activity has grade completion requirement.
     * @param bool $hasgrade Whether to set grade for the user in this activity.
     * @param int|null $passinggrade Passing grade to set for the test activity.
     * @param string|null $expectedexception Expected exception.
     * @param int|null $expectedresult The expected completion status.
     * @covers ::get_grade_completion
     */
    public function test_get_grade_completion(bool $completionusegrade, bool $hasgrade, ?int $passinggrade,
        ?string $expectedexception, ?int $expectedresult) {
        $this->setup_data();

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $assigngenerator->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => $completionusegrade,
            'gradepass' => $passinggrade,
        ]);

        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign->id));
        if ($completionusegrade && $hasgrade) {
            $assigninstance = new assign($cm->context, $cm, $this->course);
            $grade = $assigninstance->get_user_grade($this->user->id, true);
            $grade->grade = 75;
            $assigninstance->update_grade($grade);
        }

        $completioninfo = new completion_info($this->course);
        if ($expectedexception) {
            $this->expectException($expectedexception);
        }
        $gradecompletion = $completioninfo->get_grade_completion($cm, $this->user->id);
        $this->assertEquals($expectedresult, $gradecompletion);
    }

    /**
     * Test the return value for cases when the activity module does not have associated grade_item.
     *
     * @covers ::get_grade_completion
     */
    public function test_get_grade_completion_without_grade_item() {
        global $DB;

        $this->setup_data();

        $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign')->create_instance([
            'course' => $this->course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => true,
            'gradepass' => 42,
        ]);

        $cm = cm_info::create(get_coursemodule_from_instance('assign', $assign->id));

        $DB->delete_records('grade_items', [
            'courseid' => $this->course->id,
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => $assign->id,
        ]);

        // Without the grade_item, the activity is considered incomplete.
        $completioninfo = new completion_info($this->course);
        $this->assertEquals(COMPLETION_INCOMPLETE, $completioninfo->get_grade_completion($cm, $this->user->id));

        // Once the activity is graded, the grade_item is automatically created.
        $assigninstance = new assign($cm->context, $cm, $this->course);
        $grade = $assigninstance->get_user_grade($this->user->id, true);
        $grade->grade = 40;
        $assigninstance->update_grade($grade);

        // The implicitly created grade_item does not have grade to pass defined so it is not distinguished.
        $this->assertEquals(COMPLETION_COMPLETE, $completioninfo->get_grade_completion($cm, $this->user->id));
    }

    /**
     * Test for aggregate_completions().
     *
     * @covers \aggregate_completions
     */
    public function test_aggregate_completions() {
        global $DB;
        $this->resetAfterTest(true);
        $time = time();

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        for ($i = 0; $i < 4; $i++) {
            $students[] = $this->getDataGenerator()->create_user();
        }

        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        foreach ($students as $student) {
            $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        }

        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
            array('completion' => 1));
        $cmdata = get_coursemodule_from_id('data', $data->cmid);

        // Add activity completion criteria.
        $criteriadata = new stdClass();
        $criteriadata->id = $course->id;
        $criteriadata->criteria_activity = array();
        // Some activities.
        $criteriadata->criteria_activity[$cmdata->id] = 1;
        $class = 'completion_criteria_activity';
        $criterion = new $class();
        $criterion->update_config($criteriadata);

        $this->setUser($teacher);

        // Mark activity complete for both students.
        $cm = get_coursemodule_from_instance('data', $data->id);
        $completioncriteria = $DB->get_record('course_completion_criteria', []);
        foreach ($students as $student) {
            $cmcompletionrecords[] = (object)[
                'coursemoduleid' => $cm->id,
                'userid' => $student->id,
                'completionstate' => 1,
                'viewed' => 0,
                'overrideby' => null,
                'timemodified' => 0,
            ];

            $usercompletions[] = (object)[
                'criteriaid' => $completioncriteria->id,
                'userid' => $student->id,
                'timecompleted' => $time,
            ];

            $cc = array(
                'course'    => $course->id,
                'userid'    => $student->id
            );
            $ccompletion = new completion_completion($cc);
            $completion[] = $ccompletion->mark_inprogress($time);
        }
        $DB->insert_records('course_modules_completion', $cmcompletionrecords);
        $DB->insert_records('course_completion_crit_compl', $usercompletions);

        // MDL-33320: for instant completions we need aggregate to work in a single run.
        $DB->set_field('course_completions', 'reaggregate', $time - 2);

        foreach ($students as $student) {
            $result = $DB->get_record('course_completions', ['userid' => $student->id, 'reaggregate' => 0]);
            $this->assertFalse($result);
        }

        aggregate_completions($completion[0]);

        $result1 = $DB->get_record('course_completions', ['userid' => $students[0]->id, 'reaggregate' => 0]);
        $result2 = $DB->get_record('course_completions', ['userid' => $students[1]->id, 'reaggregate' => 0]);
        $result3 = $DB->get_record('course_completions', ['userid' => $students[2]->id, 'reaggregate' => 0]);

        $this->assertIsObject($result1);
        $this->assertFalse($result2);
        $this->assertFalse($result3);

        aggregate_completions(0);

        foreach ($students as $student) {
            $result = $DB->get_record('course_completions', ['userid' => $student->id, 'reaggregate' => 0]);
            $this->assertIsObject($result);
        }
    }

    /**
     * Test for completion_completion::_save().
     *
     * @covers \completion_completion::_save
     */
    public function test_save() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($teacher);

        $cc = array(
            'course'    => $course->id,
            'userid'    => $student->id
        );
        $ccompletion = new completion_completion($cc);

        $completions = $DB->get_records('course_completions');
        $this->assertEmpty($completions);

        // We're testing a private method, so we need to setup reflector magic.
        $method = new ReflectionMethod($ccompletion, '_save');
        $method->setAccessible(true); // Allow accessing of private method.
        $completionid = $method->invoke($ccompletion);
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(count($completions), 1);
        $this->assertEquals(reset($completions)->id, $completionid);

        $ccompletion->id = 0;
        $method = new ReflectionMethod($ccompletion, '_save');
        $method->setAccessible(true); // Allow accessing of private method.
        $completionid = $method->invoke($ccompletion);
        $this->assertDebuggingCalled('Can not update data object, no id!');
        $this->assertNull($completionid);
    }

    /**
     * Test for completion_completion::mark_enrolled().
     *
     * @covers \completion_completion::mark_enrolled
     */
    public function test_mark_enrolled() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($teacher);

        $cc = array(
            'course'    => $course->id,
            'userid'    => $student->id
        );
        $ccompletion = new completion_completion($cc);

        $completions = $DB->get_records('course_completions');
        $this->assertEmpty($completions);

        $completionid = $ccompletion->mark_enrolled();
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(count($completions), 1);
        $this->assertEquals(reset($completions)->id, $completionid);

        $ccompletion->id = 0;
        $completionid = $ccompletion->mark_enrolled();
        $this->assertDebuggingCalled('Can not update data object, no id!');
        $this->assertNull($completionid);
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
    }

    /**
     * Test for completion_completion::mark_inprogress().
     *
     * @covers \completion_completion::mark_inprogress
     */
    public function test_mark_inprogress() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($teacher);

        $cc = array(
            'course'    => $course->id,
            'userid'    => $student->id
        );
        $ccompletion = new completion_completion($cc);

        $completions = $DB->get_records('course_completions');
        $this->assertEmpty($completions);

        $completionid = $ccompletion->mark_inprogress();
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
        $this->assertEquals(reset($completions)->id, $completionid);

        $ccompletion->id = 0;
        $completionid = $ccompletion->mark_inprogress();
        $this->assertDebuggingCalled('Can not update data object, no id!');
        $this->assertNull($completionid);
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
    }

    /**
     * Test for completion_completion::mark_complete().
     *
     * @covers \completion_completion::mark_complete
     */
    public function test_mark_complete() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($teacher);

        $cc = array(
            'course'    => $course->id,
            'userid'    => $student->id
        );
        $ccompletion = new completion_completion($cc);

        $completions = $DB->get_records('course_completions');
        $this->assertEmpty($completions);

        $completionid = $ccompletion->mark_complete();
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
        $this->assertEquals(reset($completions)->id, $completionid);

        $ccompletion->id = 0;
        $completionid = $ccompletion->mark_complete();
        $this->assertNull($completionid);
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
    }

    /**
     * Test for completion_criteria_completion::mark_complete().
     *
     * @covers \completion_criteria_completion::mark_complete
     */
    public function test_criteria_mark_complete() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        $this->setUser($teacher);

        $record = [
            'course'    => $course->id,
            'criteriaid'    => 1,
            'userid'    => $student->id,
            'timecompleted' => time()
        ];
        $completion = new completion_criteria_completion($record, DATA_OBJECT_FETCH_BY_KEY);

        $completions = $DB->get_records('course_completions');
        $this->assertEmpty($completions);

        $completionid = $completion->mark_complete($record['timecompleted']);
        $completions = $DB->get_records('course_completions');
        $this->assertEquals(1, count($completions));
        $this->assertEquals(reset($completions)->id, $completionid);
    }
}

class core_completionlib_fake_recordset implements Iterator {
    protected $closed;
    protected $values, $index;

    public function __construct($values) {
        $this->values = $values;
        $this->index = 0;
    }

    public function current() {
        return $this->values[$this->index];
    }

    public function key() {
        return $this->values[$this->index];
    }

    public function next() {
        $this->index++;
    }

    public function rewind() {
        $this->index = 0;
    }

    public function valid() {
        return count($this->values) > $this->index;
    }

    public function close() {
        $this->closed = true;
    }

    public function was_closed() {
        return $this->closed;
    }
}
