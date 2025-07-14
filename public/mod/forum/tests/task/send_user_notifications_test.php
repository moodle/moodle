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

namespace mod_forum\task;

use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Unit tests for the send_user_notifications task in the forum module.
 *
 * This class contains test cases to ensure that the forum module's
 * send_user_notifications task functions as expected, particularly
 * when handling email notifications to users after forum posts.
 *
 * It tests different scenarios related to user email configurations,
 * such as when a user has an empty email address, when a user has exceeded
 * the bounce threshold, and how the system behaves when posts are attempted
 * to be sent under these conditions.
 *
 * Each test verifies that the appropriate exceptions are thrown, that
 * messages are correctly sent (or skipped), and that the task requeues
 * appropriately based on the user's email settings and other related conditions.
 *
 * @package    mod_forum
 * @copyright   2024 Waleed ul hassan <waleed.hassan@catalyst-eu.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class send_user_notifications_test extends \advanced_testcase {
    /**
     * Testcase to check send notification for post via email
     *
     * @covers \mod_forum\task\send_user_notifications
     * @dataProvider send_user_notifications_cases
     * @param array $userdata Test user for the case.
     * @param string $expectedstring Expected string during the test case.
     * @param array $expecteddebuggingstrings Expected debugging strings array.
     * @param bool $expectedassertion Expected adhoc task to be re queued or not.
     * @param array $userpreferences (optional) User preferences for the test case.
     * @throws InvalidArgumentException If the user data is invalid.
     * @throws RuntimeException If the notification fails to send.
     * @throws Exception For any other general errors.
     */
    public function test_send_user_notifications(
        array $userdata,
        string $expectedstring,
        array $expecteddebuggingstrings,
        bool $expectedassertion,
        array $userpreferences = [],
    ): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        $CFG->handlebounces = true;
        $this->resetAfterTest(true);
        $this->preventResetByRollback();
        $this->redirectEmails();

        // Creating a user.
        $user = $this->getDataGenerator()->create_user($userdata);
        // Set user preferences.
        foreach ($userpreferences as $name => $value) {
            set_user_preference($name, $value, $user);
        }

        // Create a course and a forum.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
            'forcesubscribe' => \FORUM_FORCESUBSCRIBE,
        ]);

        // Create a discussion in the forum.
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion([
            'course' => $course->id,
            'forum' => $forum->id,
            'userid' => $user->id,
            'message' => 'Test discussion',
        ]);
        // Create a post in the discussion.
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post([
            'course' => $course->id,
            'discussion' => $discussion->id,
            'userid' => $user->id,
            'message' => 'Test post',
        ]);

        // Setting placeholders for user id and post id.
        $expectedstring = sprintf($expectedstring, $user->id, $post->id, $user->id);
        $expecteddebuggingstrings = array_map(function($expecteddebuggingstring) use ($user) {
            return sprintf($expecteddebuggingstring, $user->id, $user->firstname . " " . $user->lastname);
        }, $expecteddebuggingstrings);

        // Enroll the user in the course.
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Trigger the send_user_notifications task.
        $task = new send_user_notifications();
        $task->set_userid($user->id);
        $task->set_custom_data($post->id);
        $this->expectOutputString($expectedstring);

        // Testing if an exception is thrown because the task is re queued if an exception is thrown in the adhoc task.
        $expectedexception = 'Error sending posts.';
        try {
            $task->execute();
        } catch (\Exception $ex) {
            $this->assertEquals($expectedexception, $ex->errorcode);
        }
        if (count($expecteddebuggingstrings)) {
            $this->assertdebuggingcalledcount(count($expecteddebuggingstrings), $expecteddebuggingstrings);
        }
    }
    /**
     * Data provider for test cases related to sending user notifications.
     *
     * This data provider generates various test cases for the `test_send_user_notifications` function.
     * Each test case consists of a user configuration, expected output strings, debugging messages, and assertions.
     *
     * @return array[] Array of test cases.
     */
    public static function send_user_notifications_cases(): array {

        return [
            [
                // Create a user with an empty email address.
                [
                    'email' => '',
                    'username' => 'testuser',
                ],
                "Sending messages to testuser (%d)\n" .
                    "  Failed to send post %d\n" .
                    "Sent 0 messages with 1 failures\n" .
                    "Failed to send emails for the user with ID %d" .
                    " due to an empty email address. Skipping re-queuing of the task.\n",
                [
                    "Can not send email to user without email: %d",
                    "Error calling message processor email",
                ],
                false,
            ],
            [
                // Create a user with bounce threshold.
                [
                    'email' => 'bounce@example.com',
                    'username' => 'bounceuser',
                ],
                "Sending messages to bounceuser (%d)\n" .
                    "  Failed to send post %d\n" .
                    "Sent 0 messages with 1 failures\n",
                [
                    "email_to_user: User %d (%s) is over bounce threshold! Not sending.",
                    "Error calling message processor email",
                ],
                true,
                [
                    'email_bounce_count' => 20,
                    'email_send_count' => 20,
                ],
            ],
        ];
    }
}
