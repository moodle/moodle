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
 * @package    mod_assign
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
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
     * @param int $groupmode The group mode of the course.
     * @param bool $teamsubmission Whether the assignment is a team submission.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_get_actions_overview')]
    public function test_get_actions_overview(int $groupmode, bool $teamsubmission): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'groupmode' => $groupmode,
            'groupmodeforce' => true,
        ]);

        // Setup the assignment.
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'course' => $course->id,
                'teamsubmission' => $teamsubmission,
                'assignsubmission_onlinetext_enabled' => 1,
                'submissiondrafts' => 1,
                'requireallteammemberssubmit' => 0,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $assign = new \mod_assign_testable_assign($cm->context, $cm, $course);

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $noneditinga = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $noneditingb = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $students = [];
        $allgroups = [
            'groupa' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupb' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupc' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupd' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
        ];
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $noneditinga->id,
        ]);
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupb']->id,
            'userid' => $noneditingb->id,
        ]);

        // Check teacher has 0 submissions.
        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals(get_string('view'), $item->get_value());
        $this->assertEquals(0, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());

        // Create 10 students:
        // - groupa (3): s1 to s3  - 2 submitted.
        // - groupb (5): s4 to s8  - 0 submitted.
        // - groupc (2): s9 to s10 - 1 submitted.
        // - groupd (0).
        for ($i = 0; $i < 10; $i++) {
            $group = $i < 3 ? $allgroups['groupa'] : ($i < 8 ? $allgroups['groupb'] : $allgroups['groupc']);
            $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $students['s' . ($i + 1)] = $student;
            if ($group) {
                $this->getDataGenerator()->create_group_member([
                    'groupid' => $group->id,
                    'userid' => $student->id,
                ]);
            }
            $this->setUser($student);
            if ($i < 2 || $i > 8) {
                $data = (object) [
                    'userid' => $student->id,
                    'onlinetext_editor' => [
                        'itemid' => file_get_unused_draft_itemid(),
                        'text' => 'My submission text',
                        'format' => FORMAT_HTML,
                    ],
                ];
                $assign->save_submission($data, $notices);
                $sink = $this->redirectMessages();
                $assign->submit_for_grading($data, []);
                $sink->close();
            }
        }

        // Check students cannot access submissions.
        $this->setUser($students['s1']);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertNull($item);

        $expectedteacher = $teamsubmission ? 2 : 3;
        $expectednoneditinga = $groupmode === SEPARATEGROUPS ? ($teamsubmission ? 1 : 2) : $expectedteacher;
        $expectednoneditingb = $groupmode === SEPARATEGROUPS ? 0 : $expectedteacher;

        // Check editing teacher submissions.
        $this->setUser($teacher);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals(get_string('gradeverb'), $item->get_value());
        $this->assertEquals($expectedteacher, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());

        // Check non-editing teacher submissions.
        $this->setUser($noneditinga);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals(get_string('gradeverb'), $item->get_value());
        $this->assertEquals($expectednoneditinga, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());

        // Check non-editing teacher with no submissions.
        $this->setUser($noneditingb);
        $item = overviewfactory::create($cm)->get_actions_overview();
        $this->assertEquals(get_string('actions'), $item->get_name());
        $this->assertEquals($expectednoneditingb > 0 ? get_string('gradeverb') : get_string('view'), $item->get_value());
        $this->assertEquals($expectednoneditingb, $item->get_alert_count());
        $this->assertEquals(get_string('numberofsubmissionsneedgrading', 'assign'), $item->get_alert_label());
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return \Generator The data provider array.
     */
    public static function provider_get_actions_overview(): \Generator {
        yield 'No groups - No team submission' => [
            'groupmode' => NOGROUPS,
            'teamsubmission' => false,
        ];
        yield 'No groups - Team submission' => [
            'groupmode' => NOGROUPS,
            'teamsubmission' => true,
        ];
        yield 'Visible groups - No team submission' => [
            'groupmode' => VISIBLEGROUPS,
            'teamsubmission' => false,
        ];
        yield 'Visible groups - Team submission' => [
            'groupmode' => VISIBLEGROUPS,
            'teamsubmission' => true,
        ];
        yield 'Separate groups - No team submission' => [
            'groupmode' => SEPARATEGROUPS,
            'teamsubmission' => false,
        ];
        yield 'Separate groups - Team submission' => [
            'groupmode' => SEPARATEGROUPS,
            'teamsubmission' => true,
        ];
    }

    /**
     * Test get_due_date_overview method.
     *
     * @param int|null $timeincrement null if no due date, or due date increment.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_due_date_overview_provider')]
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
     * @return \Generator
     */
    public static function get_due_date_overview_provider(): \Generator {
        yield 'no_due' => [
            'timeincrement' => null,
        ];
        yield 'past_due' => [
            'timeincrement' => -1 * (4 * DAYSECS),
        ];
        yield 'future_due' => [
            'timeincrement' => (4 * DAYSECS),
        ];
    }

    /**
     * Test get_extra_submissions_overview method.
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
     * Test get_extra_submissions_overview method with team submissions.
     */
    public function test_get_extra_submissions_overview_with_team_submissions(): void {
        $this->resetAfterTest();

        $groupmode = SEPARATEGROUPS;
        $course = $this->getDataGenerator()->create_course([
            'groupmode' => $groupmode,
            'groupmodeforce' => true,
        ]);

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

        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $nonediting = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $students = [];
        $allgroups = [
            'groupa' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupb' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
            'groupc' => $this->getDataGenerator()->create_group(['courseid' => $course->id]),
        ];
        $this->getDataGenerator()->create_group_member([
            'groupid' => $allgroups['groupa']->id,
            'userid' => $nonediting->id,
        ]);

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

        // Create 10 students:
        // - groupa (3): s1 to s3 - 3 submitted.
        // - groupb (5): s4 to s8 - 1 submitted.
        // - groupc (0).
        // - no group (2): s9 and s10 - 1 submitted.
        for ($i = 0; $i < 10; $i++) {
            $group = $i < 3 ? $allgroups['groupa'] : ($i < 8 ? $allgroups['groupb'] : null);
            $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
            $students['s' . ($i + 1)] = $student;
            if ($group) {
                $this->getDataGenerator()->create_group_member([
                    'groupid' => $group->id,
                    'userid' => $student->id,
                ]);
            }
            $this->setUser($student);
            if ($i < 3 || $i == 5 || $i > 8) {
                $data = (object) [
                    'userid' => $student->id,
                    'onlinetext_editor' => [
                        'itemid' => file_get_unused_draft_itemid(),
                        'text' => 'My submission text',
                        'format' => FORMAT_HTML,
                    ],
                ];
                $assign->save_submission($data, $notices);
                $sink = $this->redirectMessages();
                $assign->submit_for_grading($data, []);
                $sink->close();
            }
        }

        // Check students cannot access submissions.
        $this->setUser($students['s1']);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertNull($item);

        // Check editing teacher submissions.
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissions', 'assign'), $item->get_name());
        $this->assertEquals(3, $item->get_value());

        // Check non-editing teacher submissions.
        $this->setUser($nonediting);
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
        // Unless the admin is enrolled as a student.
        $this->getDataGenerator()->enrol_user(get_admin()->id, $course->id, 'student');
        $item = $method->invoke($overview);
        $this->assertEquals(get_string('submissionstatus', 'assign'), $item->get_name());

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
