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
 * The forum module mail generation tests for groups.
 *
 * @package    mod_forum
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once(__DIR__ . '/cron_trait.php');
require_once(__DIR__ . '/generator_trait.php');

/**
 * The forum module mail generation tests for groups.
 *
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_qanda_testcase extends advanced_testcase {
    // Make use of the cron tester trait.
    use mod_forum_tests_cron_trait;

    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /**
     * @var \phpunit_message_sink
     */
    protected $messagesink;

    /**
     * @var \phpunit_mailer_sink
     */
    protected $mailsink;

    public function setUp(): void {
        global $CFG;

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();

        // Messaging is not compatible with transactions...
        $this->preventResetByRollback();

        // Catch all messages.
        $this->messagesink = $this->redirectMessages();
        $this->mailsink = $this->redirectEmails();

        // Forcibly reduce the maxeditingtime to a second in the past to
        // ensure that messages are sent out.
        $CFG->maxeditingtime = -1;
    }

    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $this->messagesink->clear();
        $this->messagesink->close();
        unset($this->messagesink);

        $this->mailsink->clear();
        $this->mailsink->close();
        unset($this->mailsink);
    }

    /**
     * Test that a user who has not posted in a q&a forum does not receive
     * notificatinos.
     */
    public function test_user_has_not_posted() {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
            'forcesubscribe' => FORUM_INITIALSUBSCRIBE,
            'groupmode' => SEPARATEGROUPS,
            'type' => 'qanda',
        ]);

        // Create three students:
        // - author, enrolled in group A; and
        // - recipient, enrolled in group B; and
        // - other, enrolled in the course, but no groups.
        list($author, $recipient, $otheruser) = $this->helper_create_users($course, 3);

        // Create one editing teacher, not in any group but with accessallgroups capability.
        list($editingteacher) = $this->helper_create_users($course, 1, 'editingteacher');

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $editingteacher);
        $reply = $this->helper_reply_to_post($post, $author);
        $otherreply = $this->helper_reply_to_post($post, $recipient);
        $DB->execute("UPDATE {forum_posts} SET modified = modified - 1");
        $DB->execute("UPDATE {forum_posts} SET created = created - 1");
        $DB->execute("UPDATE {forum_discussions} SET timemodified = timemodified - 1");

        // Only the author, recipient, and teachers should receive.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 3,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 3,
            ],
            'otheruser' => (object) [
                'userid' => $otheruser->id,
                'messages' => 3,
            ],
            'editingteacher' => (object) [
                'userid' => $editingteacher->id,
                'messages' => 3,
            ],
        ];
        $this->queue_tasks_and_assert($expect);
        $posts = [$post, $reply, $otherreply];

        // No notifications should be queued.
        $this->send_notifications_and_assert($author, $posts);
        $this->send_notifications_and_assert($recipient, $posts);
        $this->send_notifications_and_assert($otheruser, [$post]);
        $this->send_notifications_and_assert($editingteacher, $posts);
    }
}
