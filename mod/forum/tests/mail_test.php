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

namespace mod_forum;

use mod_forum_tests_generator_trait;
use mod_forum_tests_cron_trait;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');
require_once(__DIR__ . '/cron_trait.php');
require_once(__DIR__ . '/generator_trait.php');

/**
 * The forum module mail generation tests.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
final class mail_test extends \advanced_testcase {
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

    /** @var \phpunit_event_sink */
    protected $eventsink;

    public function setUp(): void {
        global $CFG;
        parent::setUp();

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
        parent::tearDown();
    }

    /**
     * Perform message inbound setup for the mod_forum reply handler.
     */
    protected function helper_spoof_message_inbound_setup() {
        global $CFG, $DB;
        // Setup the default Inbound Message mailbox settings.
        $CFG->messageinbound_domain = 'example.com';
        $CFG->messageinbound_enabled = true;

        // Must be no longer than 15 characters.
        $CFG->messageinbound_mailbox = 'moodlemoodle123';

        $record = $DB->get_record('messageinbound_handlers', array('classname' => '\mod_forum\message\inbound\reply_handler'));
        $record->enabled = true;
        $record->id = $DB->update_record('messageinbound_handlers', $record);
    }

    public function test_cron_message_includes_courseid(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->messagesink->close();
        $this->eventsink = $this->redirectEvents();
        $this->send_notifications_and_assert($author, [$post]);
        $events = $this->eventsink->get_events();
        $event = reset($events);

        $this->assertEquals($course->id, $event->other['courseid']);

        $this->send_notifications_and_assert($recipient, [$post]);
    }

    public function test_forced_subscription(): void {
        global $DB;
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create users enrolled in the course as students.
        list($author, $recipient, $unconfirmed, $deleted) = $this->helper_create_users($course, 4);

        // Make the third user unconfirmed (thence inactive) to make sure it does not break the notifications.
        $DB->set_field('user', 'confirmed', 0, ['id' => $unconfirmed->id]);

        // Mark the fourth user as deleted to make sure it does not break the notifications.
        $DB->set_field('user', 'deleted', 1, ['id' => $deleted->id]);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
            (object) [
                'userid' => $unconfirmed->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $deleted->id,
                'messages' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$post]);
        $this->send_notifications_and_assert($recipient, [$post]);
        $this->send_notifications_and_assert($unconfirmed, []);
        $this->send_notifications_and_assert($deleted, []);
    }

    /**
     * Ensure that for a forum with subscription disabled that standard users will not receive posts.
     */
    public function test_subscription_disabled_standard_users(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_DISALLOWSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Run cron and check that the expected number of users received the notification.
        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
    }

    /**
     * Ensure that for a forum with subscription disabled that a user subscribed to the forum will receive the post.
     */
    public function test_subscription_disabled_user_subscribed_forum(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_DISALLOWSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // A user with the manageactivities capability within the course can subscribe.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $roleids['student'], \context_course::instance($course->id));

        // Suscribe the recipient only.
        \mod_forum\subscriptions::subscribe_user($recipient->id, $forum);

        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $recipient->id,
            'forum'         => $forum->id,
        )));

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Run cron and check that the expected number of users received the notification.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    /**
     * Ensure that for a forum with subscription disabled that a user subscribed to the discussion will receive the
     * post.
     */
    public function test_subscription_disabled_user_subscribed_discussion(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_DISALLOWSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // A user with the manageactivities capability within the course can subscribe.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $roleids['student'], \context_course::instance($course->id));

        // Run cron and check that the expected number of users received the notification.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Subscribe the user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($recipient->id, $discussion);
        $this->helper_update_subscription_time($recipient, $discussion, -60);

        // Run cron and check that the expected number of users received the notification.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    /**
     * Ensure that for a forum with automatic subscription that users receive posts.
     */
    public function test_automatic(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$post]);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    /**
     * Ensure that private replies are not sent to users with an automatic subscription unless they are an expected
     * recipient.
     */
    public function test_automatic_with_private_reply(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
                'course' => $course->id,
                'forcesubscribe' => FORUM_INITIALSUBSCRIBE,
            ]);

        [$student, $otherstudent] = $this->helper_create_users($course, 2, 'student');
        [$teacher, $otherteacher] = $this->helper_create_users($course, 2, 'teacher');

        [$discussion, $post] = $this->helper_post_to_forum($forum, $student);
        $reply = $this->helper_post_to_discussion($forum, $discussion, $teacher, [
                'privatereplyto' => $student->id,
            ]);

        // The private reply is queued to all messages as reply visibility may change between queueing, and sending.
        $expect = [
            (object) [
                'userid' => $student->id,
                'messages' => 2,
            ],
            (object) [
                'userid' => $otherstudent->id,
                'messages' => 2,
            ],
            (object) [
                'userid' => $teacher->id,
                'messages' => 2,
            ],
            (object) [
                'userid' => $otherteacher->id,
                'messages' => 2,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // The actual messages sent will respect private replies.
        $this->send_notifications_and_assert($student, [$post, $reply]);
        $this->send_notifications_and_assert($teacher, [$post, $reply]);
        $this->send_notifications_and_assert($otherteacher, [$post, $reply]);
        $this->send_notifications_and_assert($otherstudent, [$post]);
    }

    public function test_optional(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
    }

    public function test_automatic_with_unsubscribed_user(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Unsubscribe the 'author' user from the forum.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    public function test_optional_with_subscribed_user(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Subscribe the 'recipient' user from the forum.
        \mod_forum\subscriptions::subscribe_user($recipient->id, $forum);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    public function test_automatic_with_unsubscribed_discussion(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Unsubscribe the 'author' user from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($recipient->id, $forum, $discussion->id));

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);
    }

    public function test_optional_with_subscribed_discussion(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $this->helper_update_post_time($post, -90);

        // Subscribe the 'recipient' user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($recipient->id, $discussion);
        $this->helper_update_subscription_time($recipient, $discussion, -60);

        // Initially we don't expect any user to receive this post as you cannot subscribe to a discussion until after
        // you have read it.
        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);

        // Have a user reply to the discussion.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -30);

        // We expect only one user to receive this post.
        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$reply]);
    }

    public function test_optional_with_subscribed_discussion_and_post(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $this->helper_update_post_time($post, -90);

        // Have a user reply to the discussion before we subscribed.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -75);

        // Subscribe the 'recipient' user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($recipient->id, $discussion);
        $this->helper_update_subscription_time($recipient, $discussion, -60);

        // Have a user reply to the discussion.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -30);

        // We expect only one user to receive this post.
        // The original post won't be received as it was written before the user subscribed.
        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$reply]);
    }

    public function test_automatic_with_subscribed_discussion_in_unsubscribed_forum(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $this->helper_update_post_time($post, -90);

        // Unsubscribe the 'author' user from the forum.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);

        // Then re-subscribe them to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);
        $this->helper_update_subscription_time($author, $discussion, -60);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, [$post]);

        // Now post a reply to the original post.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -30);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$reply]);
        $this->send_notifications_and_assert($recipient, [$reply]);
    }

    public function test_optional_with_unsubscribed_discussion_in_subscribed_forum(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Unsubscribe the 'recipient' user from the discussion.
        \mod_forum\subscriptions::subscribe_user($recipient->id, $forum);

        // Then unsubscribe them from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($recipient->id, $discussion);

        // We don't expect any users to receive this post.
        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $recipient->id,
                'messages' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
    }

    /**
     * Test that a user unsubscribed from a forum who has subscribed to a discussion, only receives posts made after
     * they subscribed to the discussion.
     */
    public function test_forum_discussion_subscription_forum_unsubscribed_discussion_subscribed_after_post(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        $expectedmessages = array();

        // Create a user enrolled in the course as a student.
        list($author) = $this->helper_create_users($course, 1);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $this->helper_update_post_time($post, -90);

        $expectedmessages[] = array(
            'id' => $post->id,
            'subject' => $post->subject,
            'count' => 0,
        );

        // Then subscribe the user to the discussion.
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion));
        $this->helper_update_subscription_time($author, $discussion, -60);

        // Then post a reply to the first discussion.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -30);

        $expect = [
            (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$reply]);
    }

    public function test_subscription_by_inactive_users(): void {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $u1, $u2, $u3) = $this->helper_create_users($course, 4);

        // Subscribe the three users to the forum.
        \mod_forum\subscriptions::subscribe_user($u1->id, $forum);
        \mod_forum\subscriptions::subscribe_user($u2->id, $forum);
        \mod_forum\subscriptions::subscribe_user($u3->id, $forum);

        // Make the first user inactive - suspended.
        $DB->set_field('user', 'suspended', 1, ['id' => $u1->id]);

        // Make the second user inactive - unable to log in.
        $DB->set_field('user', 'auth', 'nologin', ['id' => $u2->id]);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        $expect = [
            (object) [
                'userid' => $u1->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $u2->id,
                'messages' => 0,
            ],
            (object) [
                'userid' => $u3->id,
                'messages' => 1,
            ],
        ];

        $this->queue_tasks_and_assert($expect);
        $this->send_notifications_and_assert($u1, []);
        $this->send_notifications_and_assert($u2, []);
        $this->send_notifications_and_assert($u3, [$post]);
    }

    public function test_forum_message_inbound_multiple_posts(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        list($author) = $this->helper_create_users($course, 1);

        $expectedmessages = array();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $this->helper_update_post_time($post, -90);

        $expectedmessages[] = (object) [
            'id' => $post->id,
            'subject' => $post->subject,
            'count' => 0,
        ];

        // Then post a reply to the first discussion.
        $reply = $this->helper_post_to_discussion($forum, $discussion, $author);
        $this->helper_update_post_time($reply, -60);

        $expectedmessages[] = (object) [
            'id' => $reply->id,
            'subject' => $reply->subject,
            'count' => 1,
        ];

        // Ensure that messageinbound is enabled and configured for the forum handler.
        $this->helper_spoof_message_inbound_setup();

        $author->emailstop = '0';
        set_user_preference('message_provider_mod_forum_posts_enabled', 'email', $author);

        // Run cron and check that the expected number of users received the notification.
        // Clear the mailsink, and close the messagesink.
        $this->mailsink->clear();
        $this->messagesink->close();

        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => count($expectedmessages),
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, $expectedmessages);
        $messages = $this->mailsink->get_messages();

        // There should be the expected number of messages.
        $this->assertEquals(2, count($messages));

        foreach ($messages as $message) {
            $this->assertMatchesRegularExpression('/Reply-To: moodlemoodle123\+[^@]*@example.com/', $message->header);
        }
    }

    public function test_long_subject(): void {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as student.
        list($author) = $this->helper_create_users($course, 1);

        // Post a discussion to the forum.
        $subject = 'This is the very long forum post subject that somebody was very kind of leaving, it is intended to check if long subject comes in mail correctly. Thank you.';
        $a = (object)array('courseshortname' => $course->shortname, 'forumname' => $forum->name, 'subject' => $subject);
        $expectedsubject = get_string('postmailsubject', 'forum', $a);
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author, array('name' => $subject));

        // Run cron and check that the expected number of users received the notification.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$post]);
        $messages = $this->messagesink->get_messages_by_component('mod_forum');
        $message = reset($messages);
        $this->assertEquals($author->id, $message->useridfrom);
        $this->assertEquals($expectedsubject, $message->subject);
    }

    /**
     * dataProvider for test_forum_post_email_templates().
     */
    public static function forum_post_email_templates_provider(): array {
        // Base information, we'll build variations based on it.
        $base = array(
            'user' => array('firstname' => 'Love', 'lastname' => 'Moodle', 'mailformat' => 0, 'maildigest' => 0),
            'course' => array('shortname' => '101', 'fullname' => 'Moodle 101'),
            'forums' => array(
                array(
                    'name' => 'Moodle Forum',
                    'forumposts' => array(
                        array(
                            'name' => 'Hello Moodle',
                            'message' => 'Welcome to Moodle',
                            'messageformat' => FORMAT_MOODLE,
                            'attachments' => array(
                                array(
                                    'filename' => 'example.txt',
                                    'filecontents' => 'Basic information about the course'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'expectations' => array(
                array(
                    'subject' => '.*101.*Hello',
                    'contents' => array(
                        '~{$a',
                        '~&(amp|lt|gt|quot|\#039);(?!course)',
                        'Attachment example.txt:' . '\r*\n' .
                            'https://www.example.com/moodle/pluginfile.php/\d*/mod_forum/attachment/\d*/example.txt' . '\r*\n',
                        'Hello Moodle', 'Moodle Forum', 'Welcome.*Moodle', 'Love Moodle', '1\d1'
                    ),
                ),
            ),
        );

        // Build the text cases.
        $textcases = array('Text mail without ampersands, quotes or lt/gt' => array('data' => $base));

        // Single and double quotes everywhere.
        $newcase = $base;
        $newcase['user']['lastname'] = 'Moodle\'"';
        $newcase['course']['shortname'] = '101\'"';
        $newcase['forums'][0]['name'] = 'Moodle Forum\'"';
        $newcase['forums'][0]['forumposts'][0]['name'] = 'Hello Moodle\'"';
        $newcase['forums'][0]['forumposts'][0]['message'] = 'Welcome to Moodle\'"';
        $newcase['expectations'][0]['contents'] = array(
            'Attachment example.txt:', '~{\$a', '~&amp;(quot|\#039);', 'Love Moodle\'', '101\'', 'Moodle Forum\'"',
            'Hello Moodle\'"', 'Welcome to Moodle\'"');
        $textcases['Text mail with quotes everywhere'] = array('data' => $newcase);

        // Lt and gt everywhere. This case is completely borked because format_string()
        // strips tags with $CFG->formatstringstriptags and also escapes < and > (correct
        // for web presentation but not for text email). See MDL-19829.
        $newcase = $base;
        $newcase['user']['lastname'] = 'Moodle>';
        $newcase['course']['shortname'] = '101>';
        $newcase['forums'][0]['name'] = 'Moodle Forum>';
        $newcase['forums'][0]['forumposts'][0]['name'] = 'Hello Moodle>';
        $newcase['forums'][0]['forumposts'][0]['message'] = 'Welcome to Moodle>';
        $newcase['expectations'][0]['contents'] = array(
            'Attachment example.txt:', '~{\$a', '~&amp;gt;', 'Love Moodle>', '101>', 'Moodle Forum>',
            'Hello Moodle>', 'Welcome to Moodle>');
        $textcases['Text mail with gt and lt everywhere'] = array('data' => $newcase);

        // Ampersands everywhere. This case is completely borked because format_string()
        // escapes ampersands (correct for web presentation but not for text email). See MDL-19829.
        $newcase = $base;
        $newcase['user']['lastname'] = 'Moodle&';
        $newcase['course']['shortname'] = '101&';
        $newcase['forums'][0]['name'] = 'Moodle Forum&';
        $newcase['forums'][0]['forumposts'][0]['name'] = 'Hello Moodle&';
        $newcase['forums'][0]['forumposts'][0]['message'] = 'Welcome to Moodle&';
        $newcase['expectations'][0]['contents'] = array(
            'Attachment example.txt:', '~{\$a', '~&amp;amp;', 'Love Moodle&', '101&', 'Moodle Forum&',
            'Hello Moodle&', 'Welcome to Moodle&');
        $textcases['Text mail with ampersands everywhere'] = array('data' => $newcase);

        // Text+image message i.e. @@PLUGINFILE@@ token handling.
        $newcase = $base;
        $newcase['forums'][0]['forumposts'][0]['name'] = 'Text and image';
        $newcase['forums'][0]['forumposts'][0]['message'] = 'Welcome to Moodle, '
            .'@@PLUGINFILE@@/Screen%20Shot%202016-03-22%20at%205.54.36%20AM%20%281%29.png !';
        $newcase['expectations'][0]['subject'] = '.*101.*Text and image';
        $newcase['expectations'][0]['contents'] = array(
            '~{$a',
            '~&(amp|lt|gt|quot|\#039);(?!course)',
            'Attachment example.txt:' . '\r*\n' .
            'https://www.example.com/moodle/pluginfile.php/\d*/mod_forum/attachment/\d*/example.txt' .  '\r*\n' ,
            'Text and image', 'Moodle Forum',
            'Welcome to Moodle, *' . '\r*\n' . '.*'
                .'https://www.example.com/moodle/pluginfile.php/\d+/mod_forum/post/\d+/'
                .'Screen%20Shot%202016-03-22%20at%205\.54\.36%20AM%20%281%29\.png *' . '\r*\n' . '.*!',
            'Love Moodle', '1\d1');
        $textcases['Text mail with text+image message i.e. @@PLUGINFILE@@ token handling'] = array('data' => $newcase);

        // Now the html cases.
        $htmlcases = array();

        // New base for html cases, no quotes, lts, gts or ampersands.
        $htmlbase = $base;
        $htmlbase['user']['mailformat'] = 1;
        $htmlbase['expectations'][0]['contents'] = array(
            '~{\$a',
            '~&(amp|lt|gt|quot|\#039);(?!course|lang|version|iosappid|androidappid)',
            '<div class="attachments">( *\n *)?<a href',
            '<div class="subject">\n.*Hello Moodle', '>Moodle Forum', '>Welcome.*Moodle', '>Love Moodle', '>1\d1');
        $htmlcases['HTML mail without ampersands, quotes or lt/gt'] = array('data' => $htmlbase);

        // Single and double quotes, lt and gt, ampersands everywhere.
        $newcase = $htmlbase;
        $newcase['user']['lastname'] = 'Moodle\'">&';
        $newcase['course']['shortname'] = '101\'">&';
        $newcase['forums'][0]['name'] = 'Moodle Forum\'">&';
        $newcase['forums'][0]['forumposts'][0]['name'] = 'Hello Moodle\'">&';
        $newcase['forums'][0]['forumposts'][0]['message'] = 'Welcome to Moodle\'">&';
        $newcase['expectations'][0]['contents'] = array(
            '~{\$a',
            '~&amp;(amp|lt|gt|quot|\#039);',
            '<div class="attachments">( *\n *)?<a href',
            '<div class="subject">\n.*Hello Moodle\'"&gt;&amp;', '>Moodle Forum\'"&gt;&amp;',
            '>Welcome.*Moodle\'"&gt;&amp;', '>Love Moodle&\#039;&quot;&gt;&amp;', '>101\'"&gt;&amp');
        $htmlcases['HTML mail with quotes, gt, lt and ampersand  everywhere'] = array('data' => $newcase);

        // Text+image message i.e. @@PLUGINFILE@@ token handling.
        $newcase = $htmlbase;
        $newcase['forums'][0]['forumposts'][0]['name'] = 'HTML text and image';
        $newcase['forums'][0]['forumposts'][0]['message'] = '<p>Welcome to Moodle, '
            .'<img src="@@PLUGINFILE@@/Screen%20Shot%202016-03-22%20at%205.54.36%20AM%20%281%29.png"'
            .' alt="" width="200" height="393" class="img-fluid" />!</p>';
        $newcase['expectations'][0]['subject'] = '.*101.*HTML text and image';
        $newcase['expectations'][0]['contents'] = array(
            '~{\$a',
            '~&(amp|lt|gt|quot|\#039);(?!course|lang|version|iosappid|androidappid)',
            '<div class="attachments">( *\n *)?<a href',
            '<div class="subject">\n.*HTML text and image', '>Moodle Forum',
            '<p>Welcome to Moodle, '
            .'<img src="https://www.example.com/moodle/tokenpluginfile.php/[^/]*/\d+/mod_forum/post/\d+/'
                .'Screen%20Shot%202016-03-22%20at%205\.54\.36%20AM%20%281%29\.png"'
                .' alt="" width="200" height="393" class="img-fluid" />!</p>',
            '>Love Moodle', '>1\d1');
        $htmlcases['HTML mail with text+image message i.e. @@PLUGINFILE@@ token handling'] = array('data' => $newcase);

        return $textcases + $htmlcases;
    }

    /**
     * Verify forum emails body using templates to generate the expected results.
     *
     * @dataProvider forum_post_email_templates_provider
     * @param array $data provider samples.
     */
    public function test_forum_post_email_templates($data): void {
        global $DB;

        $this->resetAfterTest();

        // Create the course, with the specified options.
        $options = array();
        foreach ($data['course'] as $option => $value) {
            $options[$option] = $value;
        }
        $course = $this->getDataGenerator()->create_course($options);

        // Create the user, with the specified options and enrol in the course.
        $options = array();
        foreach ($data['user'] as $option => $value) {
            $options[$option] = $value;
        }
        $user = $this->getDataGenerator()->create_user($options);
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Create forums, always force susbscribed (for easy), with the specified options.
        $posts = array();
        foreach ($data['forums'] as $dataforum) {
            $forumposts = isset($dataforum['forumposts']) ? $dataforum['forumposts'] : array();
            unset($dataforum['forumposts']);
            $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
            foreach ($dataforum as $option => $value) {
                $options[$option] = $value;
            }
            $forum = $this->getDataGenerator()->create_module('forum', $options);

            // Create posts, always for immediate delivery (for easy), with the specified options.
            foreach ($forumposts as $forumpost) {
                $attachments = isset($forumpost['attachments']) ? $forumpost['attachments'] : array();
                unset($forumpost['attachments']);
                $postoptions = array('course' => $course->id, 'forum' => $forum->id, 'userid' => $user->id,
                    'mailnow' => 1, 'attachment' => !empty($attachments));
                foreach ($forumpost as $option => $value) {
                    $postoptions[$option] = $value;
                }
                list($discussion, $post) = $this->helper_post_to_forum($forum, $user, $postoptions);
                $posts[$post->subject] = $post; // Need this to verify cron output.

                // Add the attachments to the post.
                if ($attachments) {
                    $fs = get_file_storage();
                    foreach ($attachments as $attachment) {
                        $filerecord = array(
                            'contextid' => \context_module::instance($forum->cmid)->id,
                            'component' => 'mod_forum',
                            'filearea'  => 'attachment',
                            'itemid'    => $post->id,
                            'filepath'  => '/',
                            'filename'  => $attachment['filename']
                        );
                        $fs->create_file_from_string($filerecord, $attachment['filecontents']);
                    }
                    $DB->set_field('forum_posts', 'attachment', '1', array('id' => $post->id));
                }
            }
        }

        // Clear the mailsink and close the messagesink.
        // (surely setup should provide us this cleared but...)
        $this->mailsink->clear();
        $this->messagesink->close();

        $expect = [
            'author' => (object) [
                'userid' => $user->id,
                'messages' => count($posts),
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($user, $posts);

        // Get the mails.
        $mails = $this->mailsink->get_messages();

        // Start testing the expectations.
        $expectations = $data['expectations'];

        // Assert the number is the expected.
        $this->assertSame(count($expectations), count($mails));

        // Start processing mails, first localizing its expectations, then checking them.
        foreach ($mails as $mail) {
            // Find the corresponding expectation.
            $foundexpectation = null;
            foreach ($expectations as $key => $expectation) {
                // All expectations must have a subject for matching.
                if (!isset($expectation['subject'])) {
                    $this->fail('Provider expectation missing mandatory subject');
                }
                if (preg_match('!' . $expectation['subject'] . '!', $mail->subject)) {
                    // If we already had found the expectation, there are non-unique subjects. Fail.
                    if (isset($foundexpectation)) {
                        $this->fail('Multiple expectations found (by subject matching). Please make them unique.');
                    }
                    $foundexpectation = $expectation;
                    unset($expectations[$key]);
                }
            }
            // Arrived here, we should have found the expectations.
            $this->assertNotEmpty($foundexpectation, 'Expectation not found for the mail');

            // If we have found the expectation and have contents to match, let's do it.
            if (isset($foundexpectation) and isset($foundexpectation['contents'])) {
                $mail->body = quoted_printable_decode($mail->body);
                if (!is_array($foundexpectation['contents'])) { // Accept both string and array.
                    $foundexpectation['contents'] = array($foundexpectation['contents']);
                }
                foreach ($foundexpectation['contents'] as $content) {
                    if (strpos($content, '~') !== 0) {
                        $this->assertMatchesRegularExpression('#' . $content . '#m', $mail->body);
                    } else {
                        preg_match('#' . substr($content, 1) . '#m', $mail->body, $matches);
                        $this->assertDoesNotMatchRegularExpression('#' . substr($content, 1) . '#m', $mail->body);
                    }
                }
            }
        }

        // Finished, there should not be remaining expectations.
        $this->assertCount(0, $expectations);
    }

    /**
     * Ensure that posts already mailed are not re-sent.
     */
    public function test_already_mailed(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $DB->set_field('forum_posts', 'mailed', 1);

        // No posts shoudl be considered.
        $this->queue_tasks_and_assert([]);

        // No notifications should be queued.
        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
    }

    /**
     * Ensure that posts marked mailnow are not suspect to the maxeditingtime.
     */
    public function test_mailnow(): void {
        global $CFG, $DB;

        // Update the maxeditingtime to 1 day so that posts won't be sent.
        $CFG->maxeditingtime = DAYSECS;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Post a discussion to the forum.
        list($discussion, $postmailednow) = $this->helper_post_to_forum($forum, $author, ['mailnow' => 1]);

        // Only the mailnow post should be considered.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // No notifications should be queued.
        $this->send_notifications_and_assert($author, [$postmailednow]);
        $this->send_notifications_and_assert($recipient, [$postmailednow]);
    }

    /**
     * Ensure that if a user has no permission to view a post, then it is not sent.
     */
    public function test_access_coursemodule_hidden(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Create one users enrolled in the course as an editing teacher.
        list($editor) = $this->helper_create_users($course, 1, 'editingteacher');

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Hide the coursemodule.
        set_coursemodule_visible($forum->cmid, 0);

        // Only the mailnow post should be considered.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
            'editor' => (object) [
                'userid' => $editor->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // No notifications should be queued.
        $this->send_notifications_and_assert($author, [], true);
        $this->send_notifications_and_assert($recipient, [], true);
        $this->send_notifications_and_assert($editor, [$post], true);
    }

    /**
     * Ensure that if a user loses permission to view a post after it is queued, that it is not sent.
     */
    public function test_access_coursemodule_hidden_after_queue(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Create one users enrolled in the course as an editing teacher.
        list($editor) = $this->helper_create_users($course, 1, 'editingteacher');

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Only the mailnow post should be considered.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
            'editor' => (object) [
                'userid' => $editor->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // Hide the coursemodule.
        set_coursemodule_visible($forum->cmid, 0);

        // No notifications should be queued for the students.
        $this->send_notifications_and_assert($author, [], true);
        $this->send_notifications_and_assert($recipient, [], true);

        // The editing teacher should still receive the post.
        $this->send_notifications_and_assert($editor, [$post]);
    }

    /**
     * Ensure that messages are not sent until the timestart.
     */
    public function test_access_before_timestart(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Create one users enrolled in the course as an editing teacher.
        list($editor) = $this->helper_create_users($course, 1, 'editingteacher');

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Update the discussion to have a timestart in the future.
        $DB->set_field('forum_discussions', 'timestart', time() + DAYSECS);

        // None should be sent.
        $this->queue_tasks_and_assert([]);

        // No notifications should be queued for any user.
        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
        $this->send_notifications_and_assert($editor, []);

        // Update the discussion to have a timestart in the past.
        $DB->set_field('forum_discussions', 'timestart', time() - DAYSECS);

        // Now should be sent to all.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
            'editor' => (object) [
                'userid' => $editor->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // No notifications should be queued for any user.
        $this->send_notifications_and_assert($author, [$post]);
        $this->send_notifications_and_assert($recipient, [$post]);
        $this->send_notifications_and_assert($editor, [$post]);
    }

    /**
     * Ensure that messages are not sent after the timeend.
     */
    public function test_access_after_timeend(): void {
        global $CFG, $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Create one users enrolled in the course as an editing teacher.
        list($editor) = $this->helper_create_users($course, 1, 'editingteacher');

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Update the discussion to have a timestart in the past.
        $DB->set_field('forum_discussions', 'timeend', time() - DAYSECS);

        // None should be sent.
        $this->queue_tasks_and_assert([]);

        // No notifications should be queued for any user.
        $this->send_notifications_and_assert($author, []);
        $this->send_notifications_and_assert($recipient, []);
        $this->send_notifications_and_assert($editor, []);

        // Update the discussion to have a timestart in the past.
        $DB->set_field('forum_discussions', 'timeend', time() + DAYSECS);

        // Now should be sent to all.
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'recipient' => (object) [
                'userid' => $recipient->id,
                'messages' => 1,
            ],
            'editor' => (object) [
                'userid' => $editor->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // No notifications should be queued for any user.
        $this->send_notifications_and_assert($author, [$post]);
        $this->send_notifications_and_assert($recipient, [$post]);
        $this->send_notifications_and_assert($editor, [$post]);
    }

    /**
     * Test notification comes with customdata.
     */
    public function test_notification_customdata(): void {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        list($author) = $this->helper_create_users($course, 1);
        list($commenter) = $this->helper_create_users($course, 1);

        // New posts should not have Re: in the subject.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $expect = [
            'author' => (object) [
                'userid' => $author->id,
                'messages' => 1,
            ],
            'commenter' => (object) [
                'userid' => $commenter->id,
                'messages' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($author, [$post]);
        $this->send_notifications_and_assert($commenter, [$post]);
        $messages = $this->messagesink->get_messages_by_component('mod_forum');
        $messages = reset($messages);
        $customdata = json_decode($messages->customdata);
        $this->assertEquals($forum->id, $customdata->instance);
        $this->assertEquals($forum->cmid, $customdata->cmid);
        $this->assertEquals($post->id, $customdata->postid);
        $this->assertEquals($discussion->id, $customdata->discussionid);
        $this->assertObjectHasProperty('notificationiconurl', $customdata);
        $this->assertObjectHasProperty('actionbuttons', $customdata);
        $this->assertCount(1, (array) $customdata->actionbuttons);
    }
}
