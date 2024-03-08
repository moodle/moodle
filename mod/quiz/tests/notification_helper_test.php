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

namespace mod_quiz;

/**
 * Test class for the quiz notification helper.
 *
 * @package    mod_quiz
 * @category   test
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_quiz\notification_helper
 */
class notification_helper_test extends \advanced_testcase {
    /**
     * Run all the tasks related to the notifications.
     */
    public function run_notification_helper_tasks(): void {
        $task = \core\task\manager::get_scheduled_task(\mod_quiz\task\queue_all_quiz_open_notification_tasks::class);
        $task->execute();
        $clock = $this->mock_clock_with_frozen();

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_quiz\task\queue_quiz_open_notification_tasks_for_users::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }

        $adhoctask = \core\task\manager::get_next_adhoc_task($clock->time());
        if ($adhoctask) {
            $this->assertInstanceOf(\mod_quiz\task\send_quiz_open_soon_notification_to_user::class, $adhoctask);
            $adhoctask->execute();
            \core\task\manager::adhoc_task_complete($adhoctask);
        }
    }

    /**
     * Test getting quizzes with a 'timeopen' date within the date range.
     */
    public function test_get_quizzes_within_date_range(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create a quiz with an open date < 48 hours.
        $course = $generator->create_course();
        $generator->create_module('quiz', ['course' => $course->id, 'timeopen' => $clock->time() + DAYSECS]);

        // Check that we have a result returned.
        $result = $helper::get_quizzes_within_date_range();
        $this->assertTrue($result->valid());
        $result->close();

        // Time travel 3 days into the future. We should have no quizzes in range.
        $clock->bump(DAYSECS * 3);
        $result = $helper::get_quizzes_within_date_range();
        $this->assertFalse($result->valid());
        $result->close();
    }

    /**
     * Test getting users within a quiz that are within our date range.
     */
    public function test_get_users_within_quiz(): void {
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
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');
        $generator->enrol_user($user4->id, $course->id, 'student');
        $generator->enrol_user($user5->id, $course->id, 'teacher');

        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');

        // Create a quiz with an open date < 48 hours.
        $timeopen = $clock->time() + DAYSECS;
        $quiz = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timeopen,
        ]);

        // User1 will have a user specific override, giving them an extra 1 hour for 'timeopen'.
        $usertimeopen = $timeopen + HOURSECS;
        $quizgenerator->create_override([
            'quiz' => $quiz->id,
            'userid' => $user1->id,
            'timeopen' => $usertimeopen,
        ]);

        // User2 and user3 will have a group override, giving them an extra 2 hours for 'timeopen'.
        $grouptimeopen = $timeopen + (HOURSECS * 2);
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user2->id]);
        $generator->create_group_member(['groupid' => $group->id, 'userid' => $user3->id]);
        $quizgenerator->create_override([
            'quiz' => $quiz->id,
            'groupid' => $group->id,
            'timeopen' => $grouptimeopen,
        ]);

        // Get the users within the date range.
        $quizzes = $helper::get_quizzes_within_date_range();
        foreach ($quizzes as $q) {
            $users = $helper::get_users_within_quiz($q->id);
        }
        $quizzes->close();

        // User1 has the 'user' override and its 'timeopen' date has been updated.
        $this->assertEquals($usertimeopen, $users[$user1->id]->timeopen);
        $this->assertEquals('user', $users[$user1->id]->overridetype);

        // User2 and user3 have the 'group' override and their 'timeopen' date has been updated.
        $this->assertEquals($grouptimeopen, $users[$user2->id]->timeopen);
        $this->assertEquals('group', $users[$user2->id]->overridetype);
        $this->assertEquals($grouptimeopen, $users[$user3->id]->timeopen);
        $this->assertEquals('group', $users[$user3->id]->overridetype);

        // User4 is unchanged.
        $this->assertEquals($timeopen, $users[$user4->id]->timeopen);
        $this->assertEquals('none', $users[$user4->id]->overridetype);

        // User5 should not be in the returned users because they are a teacher.
        $this->assertArrayNotHasKey($user5->id, $users);
    }

    /**
     * Test sending the quiz open soon notification to a user.
     */
    public function test_send_notification_to_user(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $helper = \core\di::get(notification_helper::class);
        $clock = $this->mock_clock_with_frozen();

        // Create a course and enrol a user.
        $course = $generator->create_course();
        $user1 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');

        /** @var \mod_quiz_generator $quizgenerator */
        $quizgenerator = $generator->get_plugin_generator('mod_quiz');

        // Create a quiz with an open date < 48 hours.
        $timeopen = $clock->time() + DAYSECS;
        $quiz = $quizgenerator->create_instance([
            'course' => $course->id,
            'timeopen' => $timeopen,
        ]);

        // Get the users within the date range.
        $quizzes = $helper::get_quizzes_within_date_range();
        foreach ($quizzes as $q) {
            $users = $helper::get_users_within_quiz($q->id);
        }
        $quizzes->close();

        // Run the tasks.
        $this->run_notification_helper_tasks();

        // Get the notifications that should have been created during the adhoc task.
        $notifications = $DB->get_records('notifications', ['useridto' => $user1->id]);
        $this->assertCount(1, $notifications);

        // Check the subject matches.
        $stringparams = ['timeopen' => userdate($users[$user1->id]->timeopen), 'quizname' => $quiz->name];
        $expectedsubject = get_string('quizopendatesoonsubject', 'mod_quiz', $stringparams);
        $this->assertEquals($expectedsubject, reset($notifications)->subject);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // There should still only be one notification because nothing has changed.
        $notifications = $DB->get_records('notifications', ['useridto' => $user1->id]);
        $this->assertCount(1, $notifications);

        // Let's modify the 'timeopen' for the quiz (it will still be within the 48 hour range).
        $updatedata = new \stdClass();
        $updatedata->id = $quiz->id;
        $updatedata->timeopen = $timeopen + HOURSECS;
        $DB->update_record('quiz', $updatedata);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // There should now be two notifications.
        $notifications = $DB->get_records('notifications', ['useridto' => $user1->id]);
        $this->assertCount(2, $notifications);

        // Let's modify the 'timeopen' one more time.
        $updatedata = new \stdClass();
        $updatedata->id = $quiz->id;
        $updatedata->timeopen = $timeopen + (HOURSECS * 2);
        $DB->update_record('quiz', $updatedata);

        // This time, the user will submit an attempt.
        $DB->insert_record('quiz_attempts', [
            'quiz' => $quiz->id,
            'userid' => $user1->id,
            'state' => 'finished',
            'timestart' => $clock->time(),
            'timecheckstate' => 0,
            'layout' => '',
            'uniqueid' => 123,
        ]);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // No new notification should have been sent.
        $notifications = $DB->get_records('notifications', ['useridto' => $user1->id]);
        $this->assertCount(2, $notifications);
    }
}
