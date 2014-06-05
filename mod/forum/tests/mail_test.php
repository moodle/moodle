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
 * The forum module mail generation tests.
 *
 * @package    mod_forum
 * @category   external
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

class mod_forum_mail_testcase extends advanced_testcase {

    protected $helper;

    public function setUp() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $helper = new stdClass();

        // Messaging is not compatible with transactions...
        $this->preventResetByRollback();

        // Catch all messages.
        $helper->messagesink = $this->redirectMessages();
        $helper->mailsink = $this->redirectEmails();

        // Confirm that we have an empty message sink so far.
        $messages = $helper->messagesink->get_messages();
        $this->assertEquals(0, count($messages));

        $messages = $helper->mailsink->get_messages();
        $this->assertEquals(0, count($messages));

        // Forcibly reduce the maxeditingtime to a one second to ensure that
        // messages are sent out.
        $CFG->maxeditingtime = 1;

        // Ensure that we don't prevent e-mail as this will cause unit test failures.
        $CFG->noemailever = false;

        $this->helper = $helper;
    }

    public function tearDown() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $this->helper->messagesink->clear();
        $this->helper->messagesink->close();

        $this->helper->mailsink->clear();
        $this->helper->mailsink->close();
    }

    /**
     * Helper to create the required number of users in the specified
     * course.
     * Users are enrolled as students.
     *
     * @param stdClass $course The course object
     * @param integer $count The number of users to create
     * @return array The users created
     */
    protected function helper_create_users($course, $count) {
        $users = array();

        for ($i = 0; $i < $count; $i++) {
            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Create a new discussion and post within the specified forum, as the
     * specified author.
     *
     * @param stdClass $forum The forum to post in
     * @param stdClass $author The author to post as
     * @param array An array containing the discussion object, and the post object
     */
    protected function helper_post_to_forum($forum, $author) {
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        // Create a discussion in the forum, and then add a post to that discussion.
        $record = new stdClass();
        $record->course = $forum->course;
        $record->userid = $author->id;
        $record->forum = $forum->id;
        $discussion = $generator->create_discussion($record);

        $record = new stdClass();
        $record->course = $forum->course;
        $record->userid = $author->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->mailnow = 1;
        $post = $generator->create_post($record);

        return array($discussion, $post);
    }

    /**
     * Run the forum cron, and check that the specified post was sent the
     * specified number of times.
     *
     * @param stdClass $post The forum post object
     * @param integer $expected The number of times that the post should have been sent
     * @return array An array of the messages caught by the message sink
     */
    protected function helper_run_cron_check_count($post, $expected) {
        // Clear the sinks before running cron.
        $this->helper->messagesink->clear();
        $this->helper->mailsink->clear();

        // Cron daily uses mtrace, turn on buffering to silence output.
        $this->expectOutputRegex("/{$expected} users were sent post {$post->id}, '{$post->subject}'/");
        forum_cron();

        // Now check the results in the message sink.
        $messages = $this->helper->messagesink->get_messages();

        // There should be the expected number of messages.
        $this->assertEquals($expected, count($messages));

        return $messages;
    }

    public function test_forced_subscription() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // We expect both users to receive this post.
        $expected = 2;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we saw messages for both users.
        $this->assertTrue($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_subscription_disabled() {
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

        // We expect both users to receive this post.
        $expected = 0;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        // A user with the manageactivities capability within the course can subscribe.
        $expected = 1;
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        assign_capability('moodle/course:manageactivities', CAP_ALLOW, $roleids['student'], context_course::instance($course->id));
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);

        $this->assertEquals($expected, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // Run cron and check that the expected number of users received the notification.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $messages = $this->helper_run_cron_check_count($post, $expected);

        // Unsubscribe the user again.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);

        $expected = 0;
        $this->assertEquals($expected, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // Run cron and check that the expected number of users received the notification.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        $messages = $this->helper_run_cron_check_count($post, $expected);

        // And unsubscribe the user from a new discussion
        $expected = 1;
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);
        $messages = $this->helper_run_cron_check_count($post, $expected);
    }

    public function test_automatic() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // We expect both users to receive this post.
        $expected = 2;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we saw messages for both users.
        $this->assertTrue($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_optional() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // We expect both users to receive this post.
        $expected = 0;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);
    }

    public function test_automatic_with_unsubscribed_user() {
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

        // We expect only one user to receive this post.
        $expected = 1;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we only saw one user.
        $this->assertFalse($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_optional_with_subscribed_user() {
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

        // We expect only one user to receive this post.
        $expected = 1;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we only saw one user.
        $this->assertFalse($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_automatic_with_unsubscribed_discussion() {
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

        // We expect only one user to receive this post.
        $expected = 1;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we only saw one user.
        $this->assertFalse($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_optional_with_subscribed_discussion() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author, $recipient) = $this->helper_create_users($course, 2);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Subscribe the 'recipient' user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($recipient->id, $discussion);

        // We expect only one user to receive this post.
        $expected = 1;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we only saw one user.
        $this->assertFalse($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_automatic_with_subscribed_discussion_in_unsubscribed_forum() {
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
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);

        // Then re-subscribe them to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);

        // We expect two users to receive this post.
        $expected = 2;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);

        $seenauthor = false;
        $seenrecipient = false;
        foreach ($messages as $message) {
            // They should both be from our user.
            $this->assertEquals($author->id, $message->useridfrom);

            if ($message->useridto == $author->id) {
                $seenauthor = true;
            } else if ($message->useridto = $recipient->id) {
                $seenrecipient = true;
            }
        }

        // Check we only saw one user.
        $this->assertTrue($seenauthor);
        $this->assertTrue($seenrecipient);
    }

    public function test_optional_with_unsubscribed_discussion_in_subscribed_forum() {
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
        $expected = 0;

        // Run cron and check that the expected number of users received the notification.
        $messages = $this->helper_run_cron_check_count($post, $expected);
    }
}
