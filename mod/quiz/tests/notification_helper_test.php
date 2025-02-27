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
final class notification_helper_test extends \advanced_testcase {
    /**
     * Run all the tasks related to the notifications.
     */
    protected function run_notification_helper_tasks(): void {
        $task = \core\task\manager::get_scheduled_task(\mod_quiz\task\queue_all_quiz_open_notification_tasks::class);
        $task->execute();
        $clock = \core\di::get(\core\clock::class);

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
        global $DB;
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
        $user6 = $generator->create_user(['suspended' => 1]);
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');
        $generator->enrol_user($user4->id, $course->id, 'student');
        $generator->enrol_user($user5->id, $course->id, 'teacher');
        $generator->enrol_user($user6->id, $course->id, 'student');

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

        // User6 should not be in the returned users because it is suspended.
        $this->assertArrayNotHasKey($user6->id, $users);

        // Let's add some availability conditions.
        $availability =
        [
            'op' => '&',
            'showc' => [true],
            'c' => [
                [
                    'type' => 'group',
                    'id' => (int)$group->id,
                ],
            ],
        ];
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
        $DB->set_field('course_modules', 'availability', json_encode($availability), ['id' => $cm->id]);

        // Rebuild course cache to apply changes.
        rebuild_course_cache($course->id, true);

        // Get the users after availability conditions of the given quiz.
        $users = notification_helper::get_users_within_quiz($quiz->id);

        // Returns only users matching availability conditions who are in the specified group.
        $this->assertCount(2, $users);
        ksort($users);
        $this->assertEquals([$user2->id, $user3->id], array_keys($users));
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
        $sink = $this->redirectMessages();

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
        $clock->bump(5);

        // Get the users within the date range.
        $quizzes = $helper::get_quizzes_within_date_range();
        foreach ($quizzes as $q) {
            $users = $helper::get_users_within_quiz($q->id);
        }
        $quizzes->close();

        // Run the tasks.
        $this->run_notification_helper_tasks();

        // Get the notifications that should have been created during the adhoc task.
        $this->assertCount(1, $sink->get_messages());

        // Check the subject matches.
        $messages = $sink->get_messages_by_component('mod_quiz');
        $message = reset($messages);
        $stringparams = ['timeopen' => userdate($users[$user1->id]->timeopen), 'quizname' => $quiz->name];
        $expectedsubject = get_string('quizopendatesoonsubject', 'mod_quiz', $stringparams);
        $this->assertEquals($expectedsubject, $message->subject);

        // Clear sink.
        $sink->clear();

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // There should be no notification because nothing has changed.
        $this->assertEmpty($sink->get_messages_by_component('mod_quiz'));

        // Let's modify the 'timeopen' for the quiz (it will still be within the 48 hour range).
        $updatedata = new \stdClass();
        $updatedata->id = $quiz->id;
        $updatedata->timeopen = $timeopen + HOURSECS;
        $DB->update_record('quiz', $updatedata);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // There should be a new notification because the 'timeopen' has been updated.
        $this->assertCount(1, $sink->get_messages_by_component('mod_quiz'));
        // Clear sink.
        $sink->clear();

        // Let's modify the 'timeopen' one more time and change the visibility.
        $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
        $DB->set_field('course_modules', 'visible', 0, ['id' => $cm->id]);

        $updatedata = new \stdClass();
        $updatedata->id = $quiz->id;
        $updatedata->timeopen = $timeopen + DAYSECS;
        $DB->update_record('quiz', $updatedata);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // There should not be a new notification because the quiz is not visible.
        $this->assertCount(0, $sink->get_messages_by_component('mod_quiz'));

        // Set back the visibility.
        $DB->set_field('course_modules', 'visible', 1, ['id' => $cm->id]);

        // Clear sink.
        $sink->clear();

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
        $clock->bump(5);

        // Run the tasks again.
        $this->run_notification_helper_tasks();

        // No new notification should have been sent.
        $this->assertEmpty($sink->get_messages_by_component('mod_quiz'));

        // Clear sink.
        $sink->clear();
    }
}
