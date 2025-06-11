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

namespace mod_assign\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/assign/tests/externallib_advanced_testcase.php');

/**
 * Test the start_submission external function.
 *
 * @package    mod_assign
 * @category   test
 * @covers     \mod_assign\external\start_submission
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class start_submission_test extends \mod_assign\externallib_advanced_testcase {
    /** @var \stdClass $course New course created to hold the assignments */
    protected $course = null;

    /**
     * Called before every test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
    }

    /**
     * Test start_submission if assignment doesn't exist matching id.
     */
    public function test_start_submission_with_invalid_assign_id(): void {
        $this->expectException(\dml_exception::class);
        start_submission::execute(123);
    }

    /**
     * Test start_submission if user is not able to access activity or course.
     */
    public function test_start_submission_when_user_has_no_capability_to_view_assignment(): void {
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $generator->create_instance(['course' => $this->course->id]);

        $this->expectException(\require_login_exception::class);
        start_submission::execute($assign->id);
    }

    /**
     * Test start_submission if assignment cut off date has elapsed.
     */
    public function test_start_submission_when_assignment_past_due_date(): void {
        $fiveminago = time() - 300;
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status(
                false, ['cutoffdate' => $fiveminago]);
        $result = start_submission::execute($instance->id);
        $filteredwarnings = array_filter($result['warnings'], function($warning) {
            return $warning['warningcode'] === 'submissionnotopen';
        });
        $this->assertCount(1, $filteredwarnings);
        $this->assertEquals(0, $result['submissionid']);
        $warning = array_pop($filteredwarnings);
        $this->assertEquals($instance->id, $warning['itemid']);
        $this->assertEquals('This assignment is not open for submissions', $warning['item']);
    }

    /**
     * Test start_submission if time limit is disabled.
     */
    public function test_start_submission_when_time_limit_disabled(): void {
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status();
        $result = start_submission::execute($instance->id);
        $filteredwarnings = array_filter($result['warnings'], function($warning) {
            return $warning['warningcode'] === 'timelimitnotenabled';
        });
        $this->assertCount(1, $filteredwarnings);
        $this->assertEquals(0, $result['submissionid']);
        $warning = array_pop($filteredwarnings);
        $this->assertEquals($instance->id, $warning['itemid']);
        $this->assertEquals('Time limit is not enabled for assignment.', $warning['item']);
    }

    /**
     * Test start_submission if time limit is not set for assignment.
     */
    public function test_start_submission_when_time_limit_not_set(): void {
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status();
        $result = start_submission::execute($instance->id);
        $filteredwarnings = array_filter($result['warnings'], function($warning) {
            return $warning['warningcode'] === 'timelimitnotenabled';
        });
        $this->assertCount(1, $filteredwarnings);
        $this->assertEquals(0, $result['submissionid']);
        $warning = array_pop($filteredwarnings);
        $this->assertEquals($instance->id, $warning['itemid']);
        $this->assertEquals('Time limit is not enabled for assignment.', $warning['item']);
    }

    /**
     * Test start_submission if user already has open submission.
     */
    public function test_start_submission_when_submission_already_open(): void {
        global $DB;
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status(
            false, ['timelimit' => 300]);
        $submission = $assign->get_user_submission($student1->id, true);
        $submission->timestarted = time();
        $DB->update_record('assign_submission', $submission);
        $result = start_submission::execute($instance->id);
        $filteredwarnings = array_filter($result['warnings'], function($warning) {
            return $warning['warningcode'] === 'opensubmissionexists';
        });
        $this->assertCount(1, $filteredwarnings);
        $this->assertEquals(0, $result['submissionid']);
        $warning = array_pop($filteredwarnings);
        $this->assertEquals($instance->id, $warning['itemid']);
        $this->assertEquals('Open assignment submission already exists.', $warning['item']);
    }

    /**
     * Test start_submission if user has already submitted with no additional attempts available.
     */
    public function test_start_submission_with_no_attempts_available(): void {
        global $DB;
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status(
            false, ['timelimit' => 300]);
        $submission = $assign->get_user_submission($student1->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $DB->update_record('assign_submission', $submission);
        $result = start_submission::execute($instance->id);
        $filteredwarnings = array_filter($result['warnings'], function($warning) {
            return $warning['warningcode'] === 'submissionnotopen';
        });
        $this->assertCount(1, $filteredwarnings);
        $this->assertEquals(0, $result['submissionid']);
        $warning = array_pop($filteredwarnings);
        $this->assertEquals($instance->id, $warning['itemid']);
        $this->assertEquals('This assignment is not open for submissions', $warning['item']);
    }

    /**
     * Test start_submission if user has no open submissions.
     */
    public function test_start_submission_with_new_submission(): void {
        global $DB;
        set_config('enabletimelimit', '1', 'assign');
        list($assign, $instance, $student1, $student2, $teacher, $g1, $g2) = $this->create_submission_for_testing_status(
            false, ['timelimit' => 300]);
        // Clear all current submissions.
        $DB->delete_records('assign_submission', ['assignment' => $instance->id]);
        $result = start_submission::execute($instance->id);
        $this->assertCount(0, $result['warnings']);
        $this->assertNotEmpty($result['submissionid']);
    }
}
