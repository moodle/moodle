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

namespace mod_assign;

use core\task\task_trait;
use mod_assign\task\queue_assignment_due_digest_notification_tasks_for_users;

/**
 * Test class for the assignment notification_helper.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_assign\notification_helper
 */
final class notification_helper_test extends \advanced_testcase {

    use task_trait;

    /**
     * Run all the tasks related to the 'due soon' notifications.
     */
    protected function run_due_soon_notification_helper_tasks(): void {
        $task = \core\task\manager::get_scheduled_task(\mod_assign\task\queue_all_assignment_due_soon_notification_tasks::class);
        $task->execute();
        $clock = \core\di::get(\core\clock::class);

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_assign\task\queue_assignment_due_soon_notification_tasks_for_users::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_assign\task\send_assignment_due_soon_notification_to_user::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }
    }

    /**
     * Test getting due soon assignments.
     */
    public function test_get_due_soon_assignments(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create an assignment with a due date < 48 hours.
        $course = $generator->create_course();
        $generator->create_module('assign', ['course' => $course->id, 'duedate' => $clock->time() + DAYSECS]);

        // Check that we have a result returned.
        $result = $helper::get_due_soon_assignments();
        $this->assertTrue($result->valid());
        $result->close();

        // Time travel 3 days into the future. We should have no assignments in range.
        $clock->bump(DAYSECS * 3);
        $result = $helper::get_due_soon_assignments();
        $this->assertFalse($result->valid());
        $result->close();
    }

    /**
     * Test getting users within an assignment that have a due date soon.
     */
    public function test_get_due_soon_users_within_assignment(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create a course and enrol some users.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $user6 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');
        $generator->enrol_user($user4->id, $course->id, 'student');
        $generator->enrol_user($user5->id, $course->id, 'student');
        $generator->enrol_user($user6->id, $course->id, 'teacher');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create an assignment with a due date < 48 hours.
        $duedate = $clock->time() + DAYSECS;
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // User1 will have a user override, giving them an extra 1 hour for 'duedate'.
        $userduedate = $duedate + HOURSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user1->id,
            'duedate' => $userduedate,
        ]);

        // User2 and user3 will have a group override, giving them an extra 2 hours for 'duedate'.
        $groupduedate = $duedate + (HOURSECS * 2);
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user2->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user3->id]);
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'groupid' => $group->id,
            'duedate' => $groupduedate,
        ]);

        // User4 will have a user override of one extra week, excluding them from the results.
        $userduedate = $duedate + WEEKSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user4->id,
            'duedate' => $userduedate,
        ]);

        $assignmentgenerator->create_submission([
            'userid' => $user5->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // There should be 3 users with the teacher excluded.
        $users = $helper::get_users_within_assignment($assignment->id, $helper::TYPE_DUE_SOON);
        $this->assertCount(3, $users);
    }

    /**
     * Test sending the assignment due soon notification to a user.
     */
    public function test_send_due_soon_notification_to_user(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();
        $sink = $this->redirectMessages();

        // Create a course and enrol a user.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');

        // Suspended user, should not receive notification.
        $user2 = $generator->create_user(['suspended' => 1]);
        $generator->enrol_user($user2->id, $course->id, 'student');

        // Nologin user, should not receive notification.
        $user3 = $generator->create_user(['auth' => 'nologin']);
        $generator->enrol_user($user3->id, $course->id, 'student');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create an assignment with a due date < 48 hours.
        $duedate = $clock->time() + DAYSECS;
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $clock->bump(5);

        // Run the tasks.
        $this->run_due_soon_notification_helper_tasks();

        // Get the assignment object.
        [$course, $assigncm] = get_course_and_cm_from_instance($assignment->id, 'assign');
        $cmcontext = \context_module::instance($assigncm->id);
        $assignmentobj = new \assign($cmcontext, $assigncm, $course);
        $duedate = $assignmentobj->get_instance($user1->id)->duedate;

        // Get the notifications that should have been created during the adhoc task.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);

        // Check the subject matches.
        $message = reset($messages);
        $stringparams = [
            'duedate' => userdate($duedate),
            'assignmentname' => $assignment->name,
        ];
        $expectedsubject = get_string('assignmentduesoonsubject', 'mod_assign', $stringparams);
        $this->assertEquals($expectedsubject, $message->subject);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_due_soon_notification_helper_tasks();

        // There should be no notification because nothing has changed.
        $this->assertEmpty($sink->get_messages_by_component('mod_assign'));

        // Let's modify the 'duedate' for the assignment (it will still be within the 48 hour range).
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + HOURSECS;
        $DB->update_record('assign', $updatedata);

        // Run the tasks again.
        $this->run_due_soon_notification_helper_tasks();

        // There should be a new notification because the 'duedate' has been updated.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $stringparams = [
            'duedate' => userdate($updatedata->duedate),
            'assignmentname' => $assignment->name,
        ];
        $expectedsubject = get_string('assignmentduesoonsubject', 'mod_assign', $stringparams);
        $this->assertEquals($expectedsubject, $message->subject);

        // Clear sink.
        $sink->clear();

        // Let's update the assignment visibility.
        $DB->set_field('course_modules', 'visible', 0, ['id' => $assigncm->id]);

        // Update the duedate to force a new notification.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + HOURSECS * 3;
        $DB->update_record('assign', $updatedata);

        // Run the tasks again.
        $this->run_due_soon_notification_helper_tasks();

        // There should not be a new notification the assignmnet is not visible.
        $this->assertEmpty($sink->get_messages_by_component('mod_assign'));

        // Update the visibility back to visible before the next assert.
        $DB->set_field('course_modules', 'visible', 1, ['id' => $assigncm->id]);

        // Clear sink.
        $sink->clear();

        // Let's modify the 'duedate' one more time.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + (HOURSECS * 2);
        $DB->update_record('assign', $updatedata);

        // This time, the user will submit the assignment.
        $assignmentgenerator->create_submission([
            'userid' => $user1->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
        ]);
        $clock->bump(5);

        // Run the tasks again.
        $this->run_due_soon_notification_helper_tasks();

        // There should only be one notifcation for submission (not for due soon).
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);
        $expectedsubject = get_string('submissionreceiptsmall', 'mod_assign', ['assignment' => $assignment->name]);
        $this->assertEquals($expectedsubject, reset($messages)->subject);

        // Clear sink.
        $sink->clear();
    }

    /**
     * Run all the tasks related to the 'overdue' notifications.
     */
    protected function run_overdue_notification_helper_tasks(): void {
        $task = \core\task\manager::get_scheduled_task(\mod_assign\task\queue_all_assignment_overdue_notification_tasks::class);
        $task->execute();
        $clock = \core\di::get(\core\clock::class);

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_assign\task\queue_assignment_overdue_notification_tasks_for_users::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_assign\task\send_assignment_overdue_notification_to_user::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }
    }

    /**
     * Test getting overdue assignments.
     */
    public function test_get_overdue_assignments(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create an overdue assignment.
        $course = $generator->create_course();
        $generator->create_module('assign', ['course' => $course->id, 'duedate' => $clock->time() - HOURSECS]);

        // Check that we have a result returned.
        $result = $helper::get_overdue_assignments();
        $this->assertTrue($result->valid());
        $result->close();

        // Time travel 2 hours into the future.
        // We should have no assignments found as we are only getting overdue assignments within a 2 hour window.
        $clock->bump(HOURSECS * 2);
        $result = $helper::get_overdue_assignments();
        $this->assertFalse($result->valid());
        $result->close();
    }

    /**
     * Test getting users within an assignment that is overdue.
     */
    public function test_get_overdue_users_within_assignment(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create a course and enrol some users.
        $course = $generator->create_course();
        $user1 = $generator->create_and_enrol($course, 'student');
        $user2 = $generator->create_and_enrol($course, 'student');
        $user3 = $generator->create_and_enrol($course, 'student');
        $user4 = $generator->create_and_enrol($course, 'student');
        $user5 = $generator->create_and_enrol($course, 'student');
        $user6 = $generator->create_and_enrol($course, 'student');
        $user7 = $generator->create_and_enrol($course, 'teacher');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create an overdue assignment.
        $duedate = $clock->time() - HOURSECS;
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // User1 will have a user override, giving them an extra minute for 'duedate'.
        $userduedate = $duedate + MINSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user1->id,
            'duedate' => $userduedate,
        ]);

        // User2 and user3 will have a group override, giving them an extra minute for 'duedate'.
        $groupduedate = $duedate + MINSECS;
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user2->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user3->id]);
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'groupid' => $group->id,
            'duedate' => $groupduedate,
        ]);

        // User4 will have a user override of one extra week, excluding them from the results.
        $userduedate = $duedate + WEEKSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user4->id,
            'duedate' => $userduedate,
        ]);

        // User5 will submit the assignment, excluding them from the results.
        $assignmentgenerator->create_submission([
            'userid' => $user5->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // User6 will have a cut-off date override that has already lapsed, excluding them from the results.
        $usercutoffdate = $clock->time() - MINSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user6->id,
            'cutoffdate' => $usercutoffdate,
        ]);

        // There should be 3 users with the teacher excluded.
        $users = $helper::get_users_within_assignment($assignment->id, $helper::TYPE_OVERDUE);
        $this->assertCount(3, $users);
        $this->assertArrayHasKey($user1->id, $users);
        $this->assertArrayHasKey($user2->id, $users);
        $this->assertArrayHasKey($user3->id, $users);
    }

    /**
     * Test sending the assignment overdue notification to a user.
     */
    public function test_send_overdue_notification_to_user(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $clock = $this->mock_clock_with_frozen();
        $sink = $this->redirectMessages();

        // Create a course and enrol a user.
        $course = $generator->create_course();
        $user1 = $generator->create_and_enrol($course, 'student');

        // Suspended user, should not receive notification.
        $user2 = $generator->create_user(['suspended' => 1]);
        $generator->enrol_user($user2->id, $course->id, 'student');

        // Nologin user, should not receive notification.
        $user3 = $generator->create_user(['auth' => 'nologin']);
        $generator->enrol_user($user3->id, $course->id, 'student');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create an assignment that is overdue.
        $duedate = $clock->time() - HOURSECS;
        $cutoffdate = $clock->time() + DAYSECS;
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'cutoffdate' => $cutoffdate,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $clock->bump(5);

        // Run the tasks.
        $this->run_overdue_notification_helper_tasks();

        // Get the notifications that should have been created during the adhoc task.
        $this->assertCount(1, $sink->get_messages());

        // Check the subject matches.
        $messages = $sink->get_messages_by_component('mod_assign');
        $message = reset($messages);
        $expectedsubject = get_string('assignmentoverduesubject', 'mod_assign', ['assignmentname' => $assignment->name]);
        $this->assertEquals($expectedsubject, $message->subject);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_overdue_notification_helper_tasks();

        // There should be no notification because nothing has changed.
        $this->assertEmpty($sink->get_messages_by_component('mod_assign'));

        // Let's modify the 'duedate' for the assignment (it will still be overdue).
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + MINSECS;
        $DB->update_record('assign', $updatedata);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_overdue_notification_helper_tasks();

        // There should be a new notification because the 'duedate' has been updated.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);
        $message = reset($messages);
        $expectedsubject = get_string('assignmentoverduesubject', 'mod_assign', ['assignmentname' => $assignment->name]);
        $this->assertEquals($expectedsubject, $message->subject);

        // Let's modify the 'cut-off date'.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->cutoffdate = $cutoffdate + MINSECS;
        $DB->update_record('assign', $updatedata);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_overdue_notification_helper_tasks();

        // There should be a new notification because the 'cut-off date' has been updated.
        $messages = $sink->get_messages_by_component('mod_assign');
        $message = reset($messages);
        $expectedsubject = get_string('assignmentoverduesubject', 'mod_assign', ['assignmentname' => $assignment->name]);
        $this->assertEquals($expectedsubject, $message->subject);

        // Let's update the assignment visibility.
        $cm = get_coursemodule_from_instance('assign', $assignment->id, $course->id);
        $DB->set_field('course_modules', 'visible', 0, ['id' => $cm->id]);

        // Update the duedate to force a new notification.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + (MINSECS * 3);
        $DB->update_record('assign', $updatedata);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_overdue_notification_helper_tasks();

        // There should not be a new notification because the assignment is not visible.
        $this->assertEmpty($sink->get_messages_by_component('mod_assign'));

        // Update the visibility back to visible before the next assert.
        $DB->set_field('course_modules', 'visible', 1, ['id' => $cm->id]);

        // Let's modify the 'duedate' one more time.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment->id;
        $updatedata->duedate = $duedate + (MINSECS * 2);
        $DB->update_record('assign', $updatedata);

        // This time, the user will submit the assignment.
        $assignmentgenerator->create_submission([
            'userid' => $user1->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_overdue_notification_helper_tasks();

        // No new notification should have been sent.
        $this->assertEmpty($sink->get_messages_by_component('mod_assign'));
    }

    /**
     * Run all the tasks related to the due digest notifications.
     */
    protected function run_due_digest_notification_helper_tasks(): void {
        $task = \core\task\manager::get_scheduled_task(\mod_assign\task\queue_all_assignment_due_digest_notification_tasks::class);
        $task->execute();
        $clock = \core\di::get(\core\clock::class);

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_assign\task\send_assignment_due_digest_notification_to_user::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }
    }

    /**
     * Test getting users for the due digest.
     */
    public function test_get_users_for_due_digest(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create a course and enrol some users.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();
        $user5 = $generator->create_user();
        $user6 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');
        $generator->enrol_user($user4->id, $course->id, 'student');
        $generator->enrol_user($user5->id, $course->id, 'student');
        $generator->enrol_user($user6->id, $course->id, 'teacher');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create an assignment with a due date 7 days from now (the due digest range).
        $duedate = $clock->time() + WEEKSECS;
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // User1 will have a user override, giving them an extra 1 day for 'duedate', excluding them from the results.
        $userduedate = $duedate + DAYSECS;
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'userid' => $user1->id,
            'duedate' => $userduedate,
        ]);

        // User2 and user3 will have a group override, giving them an extra 2 days for 'duedate', excluding them from the results.
        $groupduedate = $duedate + (DAYSECS * 2);
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user2->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user3->id]);
        $assignmentgenerator->create_override([
            'assignid' => $assignment->id,
            'groupid' => $group->id,
            'duedate' => $groupduedate,
        ]);

        // User4 will submit the assignment, excluding them from the results.
        $assignmentgenerator->create_submission([
            'userid' => $user4->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // There should be 1 user with the teacher excluded.
        $users = $helper::get_users_within_assignment($assignment->id, $helper::TYPE_DUE_DIGEST);
        $this->assertCount(1, $users);
    }

    /**
     * Test sending the assignment due digest notification to a user.
     */
    public function test_send_due_digest_notification_to_user(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $clock = $this->mock_clock_with_frozen();
        $sink = $this->redirectMessages();

        // Create a course and enrol a user.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');

        // Suspended user, should not receive notification.
        $user2 = $generator->create_user(['suspended' => 1]);
        $generator->enrol_user($user2->id, $course->id, 'student');

        // Nologin user, should not receive notification.
        $user3 = $generator->create_user(['auth' => 'nologin']);
        $generator->enrol_user($user3->id, $course->id, 'student');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create a few assignments with different due dates.
        $duedate1 = $clock->time() + WEEKSECS;
        $assignment1 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate1,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $duedate2 = $clock->time() + WEEKSECS;
        $assignment2 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate2,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $duedate3 = $clock->time() + WEEKSECS + DAYSECS;
        $assignment3 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate3,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        // Create an assignment with a visibility restriction.
        $duedate4 = $clock->time() + WEEKSECS;
        $assignment4 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $duedate4,
            'visible' => 0,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $clock->bump(5);

        // Run the tasks.
        $this->run_due_digest_notification_helper_tasks();

        // Get the notifications that should have been created during the adhoc task.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);

        // Check the message for the expected assignments.
        $message = reset($messages);
        $this->assertStringContainsString($assignment1->name, $message->fullmessagehtml);
        $this->assertStringContainsString($assignment2->name, $message->fullmessagehtml);
        $this->assertStringNotContainsString($assignment3->name, $message->fullmessagehtml);
        $this->assertStringNotContainsString($assignment4->name, $message->fullmessagehtml);

        // Check the message contains the formatted due date.
        $formatteddate = userdate($duedate1, get_string('strftimedaydate', 'langconfig'));
        $this->assertStringContainsString($formatteddate, $message->fullmessagehtml);

        // Check the subject matches.
        $expectedsubject = get_string('assignmentduedigestsubject', 'mod_assign', $message->subject);
        $this->assertEquals($expectedsubject, $message->subject);

        // Clear sink.
        $sink->clear();

        // Let's modify the due date for one of the assignment.
        $updatedata = new \stdClass();
        $updatedata->id = $assignment1->id;
        $updatedata->duedate = $duedate1 + DAYSECS;
        $DB->update_record('assign', $updatedata);

        // Run the tasks again.
        $this->run_due_digest_notification_helper_tasks();

        // Check the message for the expected assignments.
        $messages = $sink->get_messages_by_component('mod_assign');
        $message = reset($messages);
        $this->assertStringNotContainsString($assignment1->name, $message->fullmessagehtml);
        $this->assertStringContainsString($assignment2->name, $message->fullmessagehtml);
        $this->assertStringNotContainsString($assignment3->name, $message->fullmessagehtml);
        $this->assertStringNotContainsString($assignment4->name, $message->fullmessagehtml);

        // Clear sink.
        $sink->clear();

        // This time, the user will submit an assignment.
        $assignmentgenerator->create_submission([
            'userid' => $user1->id,
            'cmid' => $assignment2->cmid,
            'status' => 'submitted',
            'timemodified' => $clock->time(),
            'onlinetext' => 'Some text',
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $clock->bump(5);

        // Run the tasks again.
        $this->run_due_digest_notification_helper_tasks();

        // There are no assignments left to report, so no notification should have been sent.
        // There should only be one notifcation for submission.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);
        $expectedsubject = get_string('submissionreceiptsmall', 'mod_assign', ['assignment' => $assignment2->name]);
        $this->assertEquals($expectedsubject, reset($messages)->subject);

        // Clear sink.
        $sink->clear();
    }

    /**
     * Test sending the assignment due digest notification to users in groups with restricted access.
     */
    public function test_send_due_digest_notification_to_users_in_groups(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $clock = $this->mock_clock_with_incrementing();
        $sink = $this->redirectMessages();
        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create a course, users and enrol users.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');

        // Create groups and add users to groups.
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group1->id, 'userid' => $user1->id]);
        $generator->create_group_member(['groupid' => $group2->id, 'userid' => $user2->id]);

        // Create assignments.
        $assignment1 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $clock->time() + WEEKSECS,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $assignment2 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $clock->time() + WEEKSECS,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);
        $assignment3 = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'duedate' => $clock->time() + WEEKSECS,
            'submissiondrafts' => 0,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        // Set restricted access for assignment1 and assignment2 to groups.
        $availability = [
            'op' => '&',
            'showc' => [true],
            'c' => [
                [
                    'type' => 'group',
                    'id' => (int) $group1->id,
                ],
            ],
        ];
        $cm = get_coursemodule_from_instance('assign', $assignment1->id, $course->id);
        $DB->set_field('course_modules', 'availability', json_encode($availability), ['id' => $cm->id]);

        $availability = [
            'op' => '&',
            'showc' => [true],
            'c' => [
                [
                    'type' => 'group',
                    'id' => (int) $group2->id,
                ],
            ],
        ];
        $cm = get_coursemodule_from_instance('assign', $assignment2->id, $course->id);
        $DB->set_field('course_modules', 'availability', json_encode($availability), ['id' => $cm->id]);

        // Rebuild course cache to apply changes.
        rebuild_course_cache($course->id, true);

        // Run the tasks. We want to run all the adhoc tasks at the same time. So we will use the normal task runner.
        $this->execute_task('\mod_assign\task\queue_all_assignment_due_digest_notification_tasks');
        // Execute the remaining ad-hoc backup task.
        $this->start_output_buffering();
        $this->runAdhocTasks('\mod_assign\task\send_assignment_due_digest_notification_to_user');
        $this->stop_output_buffering();
        $messages = $sink->get_messages_by_component('mod_assign');

        // Process the messages.
        $processedmessages = [];
        foreach ($messages as $message) {
            $processedmessages[$message->useridto] = $message;
        }

        // Verify the messages.
        $this->assertCount(3, $processedmessages);
        // User1 should receive a message for assignment1 and assignment3.
        $this->assertArrayHasKey($user1->id, $processedmessages);
        $this->assertStringContainsString($assignment1->name, $processedmessages[$user1->id]->fullmessagehtml);
        $this->assertStringContainsString($assignment3->name, $processedmessages[$user1->id]->fullmessagehtml);
        $this->assertStringNotContainsString($assignment2->name, $processedmessages[$user1->id]->fullmessagehtml);
        // User2 should receive a message for assignment2 and assignment3.
        $this->assertArrayHasKey($user2->id, $processedmessages);
        $this->assertStringContainsString($assignment2->name, $processedmessages[$user2->id]->fullmessagehtml);
        $this->assertStringContainsString($assignment3->name, $processedmessages[$user2->id]->fullmessagehtml);
        $this->assertStringNotContainsString($assignment1->name, $processedmessages[$user2->id]->fullmessagehtml);
        // User3 should receive a message for assignment3 only.
        $this->assertArrayHasKey($user3->id, $processedmessages);
        $this->assertStringContainsString($assignment3->name, $processedmessages[$user3->id]->fullmessagehtml);
        $this->assertStringNotContainsString($assignment1->name, $processedmessages[$user3->id]->fullmessagehtml);
        $this->assertStringNotContainsString($assignment2->name, $processedmessages[$user3->id]->fullmessagehtml);
    }

    /**
     * Test sending the assignment notification to a user with a list of the submitted files.
     */
    public function test_send_notification_with_summary_to_user(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $sink = $this->redirectMessages();

        // Create a course and enrol a user.
        $course = $generator->create_course(['shortname' => 'A100']);
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');

        /** @var \mod_assign_generator $assignmentgenerator */
        $assignmentgenerator = $generator->get_plugin_generator('mod_assign');

        // Create activity.
        $assignment = $assignmentgenerator->create_instance([
            'course' => $course->id,
            'name' => 'Assignment 1',
            'submissiondrafts' => 0,
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 12,
            'assignsubmission_file_maxsizebytes' => 1024 * 1024,
            'assignsubmission_onlinetext_enabled' => 1,
        ]);

        $filename1 = 'submissionsample01.txt';
        $filename2 = 'submissionsample02.txt';
        $files = [
            "mod/assign/tests/fixtures/" . $filename1,
            "mod/assign/tests/fixtures/" . $filename2,
        ];

        // Generate submissions.
        $assignmentgenerator->create_submission([
            'userid' => $user1->id,
            'cmid' => $assignment->cmid,
            'status' => 'submitted',
            'file' => implode(',', $files),
            'onlinetext' => 'Some text example',
        ]);

        // Get the notifications.
        $messages = $sink->get_messages_by_component('mod_assign');
        $this->assertCount(1, $messages);
        $message = reset($messages);

        // Check the subject line and short message.
        $this->assertEquals('Assignment submission confirmation - Assignment 1', $message->subject);
        $this->assertEquals('Assignment submission confirmation - Assignment 1', $message->smallmessage);

        // Check the plain text message.
        $this->assertEquals('A100 -> Assignment -> Assignment 1
---------------------------------------------------------------------
You have submitted an assignment submission for \'Assignment 1\'.

You can see the status of your assignment submission:

    https://www.example.com/moodle/mod/assign/view.php?id=' . $assignment->cmid . '

Your submission contains:

Online text
(3 words)

File submissions
* submissionsample01.txt (42 bytes)
* submissionsample02.txt (42 bytes)


---------------------------------------------------------------------
', $message->fullmessage);

        $expectedfragments = [
            '<p>Your assignment submission for \'Assignment 1\' has been submitted.</p>',
            '<p>You can view your submission and check its status on the <a href="' .
                'https://www.example.com/moodle/mod/assign/view.php?id=' .
                $assignment->cmid . '">assignment page</a>.</p>',
            '<h2>Your submission contains:</h2>',
            '<h3>Online text</h3>',
            '<p>(3 words)</p>',
            '<h3>File submissions</h3>',
            '<li>submissionsample01.txt (42 bytes)</li>',
            '<li>submissionsample02.txt (42 bytes)</li>',
        ];
        foreach ($expectedfragments as $html) {
            $this->assertStringContainsString($html, $message->fullmessagehtml);
        }

        // Clear sink.
        $sink->clear();
    }
}
