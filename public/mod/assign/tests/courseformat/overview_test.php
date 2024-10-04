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

namespace mod_assign\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Assignment overview integration.
 *
 * @covers \mod_assign\course\overview
 * @package    mod_assign
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->dirroot . '/mod/assign/tests/fixtures/testable_assign.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test get_actions_overview method.
     *
     * @covers ::get_actions_overview
     */
    public function test_get_actions_overview(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $assign = new \mod_assign_testable_assign($cm->context, $cm, $course);

        // Check for 0 submissions.
        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals(get_string('gradeverb'), $item->get_value());
        $this->assertEquals(0, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());

        // Simulate an assignment submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Check for 1 ungraded submission.
        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals(get_string('gradeverb'), $item->get_value());
        $this->assertEquals(1, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());

        // Check students cannot access submissions.
        $this->setUser($student);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertNull($item);
    }

    /**
     * Test get_due_date_overview method.
     *
     * @covers ::get_due_date_overview
     * @dataProvider get_due_date_overview_provider
     * @param int|null $timeincrement null if no due date, or due date increment.
     */
    public function test_get_due_date_overview(
        int|null $timeincrement,
    ): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        if ($timeincrement === null) {
            $expectedtime = null;
        } else {
            $expectedtime = $this->mock_clock_with_frozen()->time() + $timeincrement;
        }

        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'assignsubmission_onlinetext_enabled' => 1,
                'duedate' => !empty($expectedtime) ? $expectedtime : 0,
            ],
        );

        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $item = overviewfactory::create($cm)->get_due_date_overview();
        $this->assertEquals(get_string('duedate', 'assign'), $item->get_name());
        $this->assertEquals($expectedtime, $item->get_value());
    }

    /**
     * Provider for get_due_date_overview.
     *
     * @return array
     */
    public static function get_due_date_overview_provider(): array {
        return [
            'no_due' => [
                'timeincrement' => null,
            ],
            'past_due' => [
                'timeincrement' => -1 * (4 * DAYSECS),
            ],
            'future_due' => [
                'timeincrement' => (4 * DAYSECS),
            ],
        ];
    }

    /**
     * Test get_extra_submissions_overview method.
     *
     * @covers ::get_extra_submissions_overview
     */
    public function test_get_extra_submissions_overview(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student2');

        // Setup the assignment.
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'assignsubmission_onlinetext_enabled' => 1,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $assign = new \mod_assign_testable_assign($cm->context, $cm, $course);

        // Check teacher has 0 submissions.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissions', 'assign'), $item->get_name());
        $this->assertEquals(0, $item->get_value());

        // Simulate an assignment submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Check students cannot access submissions.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Check teacher has 1 submissions.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissions', 'assign'), $item->get_name());
        $this->assertEquals(1, $item->get_value());
    }

    /**
     * Test get_extra_submission_status_overview method.
     *
     * @covers ::get_extra_submission_status_overview
     */
    public function test_get_extra_submission_status_overview(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Setup the assignment.
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'assignsubmission_onlinetext_enabled' => 1,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $assign = new \mod_assign_testable_assign($cm->context, $cm, $course);

        // Check teacher does not has submission status.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Admin does not have submission status.
        $this->setAdminUser();
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Check student see the new status.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissionstatus', 'assign'), $item->get_name());
        $this->assertEquals(get_string('submissionstatus_new', 'assign'), $item->get_value());

        // Simulate an assignment submission.
        $this->setUser($student);
        $submission = $assign->get_user_submission($student->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Check student see the new status.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissionstatus', 'assign'), $item->get_name());
        $this->assertEquals(get_string('submissionstatus_submitted', 'assign'), $item->get_value());
    }

    /**
     * Test get_extra_submission_status_overview method in group submissions.
     *
     * @covers ::get_extra_submission_status_overview
     */
    public function test_get_extra_submission_status_overview_groups(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group, $student);
        groups_add_member($group, $student2);

        // Setup the assignment.
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'assignsubmission_onlinetext_enabled' => 1,
                'teamsubmission' => 1,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $assign = new \mod_assign_testable_assign($cm->context, $cm, $course);

        // Check teacher does not has submission status.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Admin does not have submission status.
        $this->setAdminUser();
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Check student see the new status.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissionstatus', 'assign'), $item->get_name());
        $this->assertEquals(get_string('submissionstatus_new', 'assign'), $item->get_value());

        // Simulate an assignment submission.
        $this->setUser($student2);
        $submission = $assign->get_group_submission($student2->id, $group->id, true);
        $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $assign->testable_update_submission($submission, $student2->id, true, false);
        $data = new \stdClass();
        $data->onlinetext_editor = [
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_MOODLE,
        ];
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);

        // Check student see the new status.
        $this->setUser($student);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submission_status_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissionstatus', 'assign'), $item->get_name());
        $this->assertEquals(get_string('submissionstatus_submitted', 'assign'), $item->get_value());
    }
}
