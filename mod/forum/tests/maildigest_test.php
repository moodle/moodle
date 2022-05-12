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

use mod_forum_tests_cron_trait;
use mod_forum_tests_generator_trait;

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/cron_trait.php');
require_once(__DIR__ . '/generator_trait.php');

/**
 * The module forums external functions unit tests
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2013 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class maildigest_test extends \advanced_testcase {

    // Make use of the cron tester trait.
    use mod_forum_tests_cron_trait;

    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /**
     * Set up message and mail sinks, and set up other requirements for the
     * cron to be tested here.
     */
    public function setUp(): void {
        global $CFG;

        // Messaging is not compatible with transactions...
        $this->preventResetByRollback();

        // Catch all messages
        $this->messagesink = $this->redirectMessages();
        $this->mailsink = $this->redirectEmails();

        // Confirm that we have an empty message sink so far.
        $messages = $this->messagesink->get_messages();
        $this->assertEquals(0, count($messages));

        $messages = $this->mailsink->get_messages();
        $this->assertEquals(0, count($messages));

        // Tell Moodle that we've not sent any digest messages out recently.
        $CFG->digestmailtimelast = 0;

        // And set the digest sending time to a negative number - this has
        // the effect of making it 11pm the previous day.
        $CFG->digestmailtime = -1;

        // Forcibly reduce the maxeditingtime to a one second to ensure that
        // messages are sent out.
        $CFG->maxeditingtime = 1;

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();
    }

    /**
     * Clear the message sinks set up in this test.
     */
    public function tearDown(): void {
        $this->messagesink->clear();
        $this->messagesink->close();

        $this->mailsink->clear();
        $this->mailsink->close();
    }

    /**
     * Setup a user, course, and forums.
     *
     * @return stdClass containing the list of forums, courses, forumids,
     * and the user enrolled in them.
     */
    protected function helper_setup_user_in_course() {
        global $DB;

        $return = new \stdClass();
        $return->courses = new \stdClass();
        $return->forums = new \stdClass();
        $return->forumids = array();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $return->user = $user;

        // Create courses to add the modules.
        $return->courses->course1 = $this->getDataGenerator()->create_course();

        // Create forums.
        $record = new \stdClass();
        $record->course = $return->courses->course1->id;
        $record->forcesubscribe = 1;

        $return->forums->forum1 = $this->getDataGenerator()->create_module('forum', $record);
        $return->forumsids[] = $return->forums->forum1->id;

        $return->forums->forum2 = $this->getDataGenerator()->create_module('forum', $record);
        $return->forumsids[] = $return->forums->forum2->id;

        // Check the forum was correctly created.
        list ($test, $params) = $DB->get_in_or_equal($return->forumsids);

        // Enrol the user in the courses.
        // DataGenerator->enrol_user automatically sets a role for the user
        $this->getDataGenerator()->enrol_user($return->user->id, $return->courses->course1->id);

        return $return;
    }

    public function test_set_maildigest() {
        global $DB;

        $this->resetAfterTest(true);

        $helper = $this->helper_setup_user_in_course();
        $user = $helper->user;
        $course1 = $helper->courses->course1;
        $forum1 = $helper->forums->forum1;

        // Set to the user.
        self::setUser($helper->user);

        // Confirm that there is no current value.
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertFalse($currentsetting);

        // Test with each of the valid values:
        // 0, 1, and 2 are valid values.
        forum_set_user_maildigest($forum1, 0, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 0);

        forum_set_user_maildigest($forum1, 1, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 1);

        forum_set_user_maildigest($forum1, 2, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertEquals($currentsetting->maildigest, 2);

        // And the default value - this should delete the record again
        forum_set_user_maildigest($forum1, -1, $user);
        $currentsetting = $DB->get_record('forum_digests', array(
            'forum' => $forum1->id,
            'userid' => $user->id,
        ));
        $this->assertFalse($currentsetting);

        // Try with an invalid value.
        $this->expectException('moodle_exception');
        forum_set_user_maildigest($forum1, 42, $user);
    }

    public function test_get_user_digest_options_default() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $helper = $this->helper_setup_user_in_course();
        $user = $helper->user;
        $course1 = $helper->courses->course1;
        $forum1 = $helper->forums->forum1;

        // Set to the user.
        self::setUser($helper->user);

        // We test against these options.
        $digestoptions = array(
            '0' => get_string('emaildigestoffshort', 'mod_forum'),
            '1' => get_string('emaildigestcompleteshort', 'mod_forum'),
            '2' => get_string('emaildigestsubjectsshort', 'mod_forum'),
        );

        // The default settings is 0.
        $this->assertEquals(0, $user->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[0]));

        // Update the setting to 1.
        $USER->maildigest = 1;
        $this->assertEquals(1, $USER->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[1]));

        // Update the setting to 2.
        $USER->maildigest = 2;
        $this->assertEquals(2, $USER->maildigest);
        $options = forum_get_user_digest_options();
        $this->assertEquals($options[-1], get_string('emaildigestdefault', 'mod_forum', $digestoptions[2]));
    }

    public function test_get_user_digest_options_sorting() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $helper = $this->helper_setup_user_in_course();
        $user = $helper->user;
        $course1 = $helper->courses->course1;
        $forum1 = $helper->forums->forum1;

        // Set to the user.
        self::setUser($helper->user);

        // Retrieve the list of applicable options.
        $options = forum_get_user_digest_options();

        // The default option must always be at the top of the list.
        $lastoption = -2;
        foreach ($options as $value => $description) {
            $this->assertGreaterThan($lastoption, $value);
            $lastoption = $value;
        }
    }

    public function test_cron_no_posts() {
        global $DB;

        $this->resetAfterTest(true);

        // Initially the forum cron should generate no messages as we've made no posts.
        $expect = [];
        $this->queue_tasks_and_assert($expect);
    }

    /**
     * Sends several notifications to one user as:
     * * single messages based on a user profile setting.
     */
    public function test_cron_profile_single_mails() {
        global $DB;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;

        // Add 5 discussions to forum 1.
        $posts = [];
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Add 5 discussions to forum 2.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum2, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 0, array('id' => $user->id));

        // Set the maildigest preference for forum1 to default.
        forum_set_user_maildigest($forum1, -1, $user);

        // Set the maildigest preference for forum2 to default.
        forum_set_user_maildigest($forum2, -1, $user);

        // No digests mails should be sent, but 10 forum mails will be sent.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 10,
                'digests' => 0,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($user, $posts);
    }

    /**
     * Sends several notifications to one user as:
     * * daily digests coming from the user profile setting.
     */
    public function test_cron_profile_digest_email() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $posts = [];

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Add 5 discussions to forum 2.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum2, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // Set the maildigest preference for forum1 to default.
        forum_set_user_maildigest($forum1, -1, $user);

        // Set the maildigest preference for forum2 to default.
        forum_set_user_maildigest($forum2, -1, $user);

        // No digests mails should be sent, but 10 forum mails will be sent.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 0,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_digests_and_assert($user, $posts);
    }

    /**
     * Send digests to a user who cannot view fullnames
     */
    public function test_cron_digest_view_fullnames_off() {
        global $DB, $CFG;

        $CFG->fullnamedisplay = 'lastname';
        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $posts = [];

        // Add 1 discussions to forum 1.
        list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
        $posts[] = $post;

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // No digests mails should be sent, but 1 forum mails will be sent.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 0,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);
        $this->send_digests_and_assert($user, $posts);

        // The user does not, by default, have permission to view the fullname.
        $messagecontent = $this->messagesink->get_messages()[0]->fullmessage;

        // Assert that the expected name is present (lastname only).
        $this->assertStringContainsString(fullname($user, false), $messagecontent);

        // Assert that the full name is not present (firstname lastname only).
        $this->assertStringNotContainsString(fullname($user, true), $messagecontent);
    }

    /**
     * Send digests to a user who can view fullnames.
     */
    public function test_cron_digest_view_fullnames_on() {
        global $DB, $CFG;

        $CFG->fullnamedisplay = 'lastname';
        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $posts = [];
        assign_capability(
            'moodle/site:viewfullnames',
            CAP_ALLOW,
            $DB->get_field('role', 'id', ['shortname' => 'student']),
            \context_course::instance($course1->id)
        );

        // Add 1 discussions to forum 1.
        list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
        $posts[] = $post;

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // No digests mails should be sent, but 1 forum mails will be sent.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 0,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);
        $this->send_digests_and_assert($user, $posts);

        // The user does not, by default, have permission to view the fullname.
        // However we have given the user that capability so we expect to see both firstname and lastname.
        $messagecontent = $this->messagesink->get_messages()[0]->fullmessage;

        // Assert that the expected name is present (lastname only).
        $this->assertStringContainsString(fullname($user, false), $messagecontent);

        // Assert that the full name is also present (firstname lastname only).
        $this->assertStringContainsString(fullname($user, true), $messagecontent);
    }

    /**
     * Sends several notifications to one user as:
     * * daily digests coming from the per-forum setting; and
     * * single e-mails from the profile setting.
     */
    public function test_cron_mixed_email_1() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $posts = [];
        $digests = [];

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $digests[] = $post;
        }

        // Add 5 discussions to forum 2.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum2, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 0, array('id' => $user->id));

        // Set the maildigest preference for forum1 to digest.
        forum_set_user_maildigest($forum1, 1, $user);

        // Set the maildigest preference for forum2 to default (single).
        forum_set_user_maildigest($forum2, -1, $user);

        // One digest e-mail should be sent, and five individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 5,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($user, $posts);
        $this->send_digests_and_assert($user, $digests);
    }

    /**
     * Sends several notifications to one user as:
     * * single e-mails from the per-forum setting; and
     * * daily digests coming from the per-user setting.
     */
    public function test_cron_mixed_email_2() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $posts = [];
        $digests = [];

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $digests[] = $post;
        }

        // Add 5 discussions to forum 2.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum2, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // Set the maildigest preference for forum1 to digest.
        forum_set_user_maildigest($forum1, -1, $user);

        // Set the maildigest preference for forum2 to single.
        forum_set_user_maildigest($forum2, 0, $user);

        // One digest e-mail should be sent, and five individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 5,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_notifications_and_assert($user, $posts);
        $this->send_digests_and_assert($user, $digests);
    }

    /**
     * Sends several notifications to one user as:
     * * daily digests coming from the per-forum setting.
     */
    public function test_cron_forum_digest_email() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $fulldigests = [];
        $shortdigests = [];

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $fulldigests[] = $post;
        }

        // Add 5 discussions to forum 2.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum2, $user, ['mailnow' => 1]);
            $shortdigests[] = $post;
        }

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 0, array('id' => $user->id));

        // Set the maildigest preference for forum1 to digest (complete).
        forum_set_user_maildigest($forum1, 1, $user);

        // Set the maildigest preference for forum2 to digest (short).
        forum_set_user_maildigest($forum2, 2, $user);

        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_digests_and_assert($user, $fulldigests, $shortdigests);
    }

    /**
     * The digest being in the past is queued til the next day.
     */
    public function test_cron_digest_previous_day() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $fulldigests = [];
        $shortdigests = [];

        // Add 1 discussions to forum 1.
        list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
        $fulldigests[] = $post;

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // Set the digest time to midnight.
        $CFG->digestmailtime = 0;
        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $tasks = $DB->get_records('task_adhoc');
        $task = reset($tasks);
        $this->assertGreaterThanOrEqual(time(), $task->nextruntime);
    }

    /**
     * The digest being in the future is queued for today.
     */
    public function test_cron_digest_same_day() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $forum2 = $userhelper->forums->forum2;
        $fulldigests = [];
        $shortdigests = [];

        // Add 1 discussions to forum 1.
        list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
        $fulldigests[] = $post;

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, array('id' => $user->id));

        // Set the digest time to the future (magic, shouldn't work).
        $CFG->digestmailtime = 25;
        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $tasks = $DB->get_records('task_adhoc');
        $task = reset($tasks);
        $digesttime = usergetmidnight(time(), \core_date::get_server_timezone()) + ($CFG->digestmailtime * 3600);
        $this->assertLessThanOrEqual($digesttime, $task->nextruntime);
    }

    /**
     * Tests that if a new message is posted after the days digest time,
     * but before that days digests are sent a new task is created.
     */
    public function test_cron_digest_queue_next_before_current_processed() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $forum1 = $userhelper->forums->forum1;

        // Add 1 discussions to forum 1.
        $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);

        // Set the tested user's default maildigest setting.
        $DB->set_field('user', 'maildigest', 1, ['id' => $user->id]);

        // Set the digest time to the future (magic, shouldn't work).
        $CFG->digestmailtime = 25;
        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // Set the digest time to midnight.
        $CFG->digestmailtime = 0;

        // Add another discussions to forum 1.
        $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);

        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // There should now be two tasks queued.
        $tasks = $DB->get_records('task_adhoc');
        $this->assertCount(2, $tasks);

        // Add yet another another discussions to forum 1.
        $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);

        // One digest e-mail should be sent, and no individual notifications.
        $expect = [
            (object) [
                'userid' => $user->id,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        // There should still be two tasks queued.
        $tasks = $DB->get_records('task_adhoc');
        $this->assertCount(2, $tasks);
    }

    /**
     * The sending of a digest marks posts as read if automatic message read marking is set.
     */
    public function test_cron_digest_marks_posts_read() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Disable the 'Manual message read marking' option.
        $CFG->forum_usermarksread = false;

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $posts = [];

        // Set the tested user's default maildigest, trackforums, read tracking settings.
        $DB->set_field('user', 'maildigest', 1, ['id' => $user->id]);
        $DB->set_field('user', 'trackforums', 1, ['id' => $user->id]);
        set_user_preference('forum_markasreadonnotification', 1, $user->id);

        // Set the maildigest preference for forum1 to default.
        forum_set_user_maildigest($forum1, -1, $user);

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // There should be unread posts for the forum.
        $expectedposts = [
            $forum1->id => (object) [
                'id' => $forum1->id,
                'unread' => count($posts),
            ],
        ];
        $this->assertEquals($expectedposts, forum_tp_get_course_unread_posts($user->id, $course1->id));

        // One digest mail should be sent and no other messages.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 0,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_digests_and_assert($user, $posts);

        // Verify that there are no unread posts for any forums.
        $this->assertEmpty(forum_tp_get_course_unread_posts($user->id, $course1->id));
    }

    /**
     * The sending of a digest does not mark posts as read when manual message read marking is set.
     */
    public function test_cron_digest_leaves_posts_unread() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Enable the 'Manual message read marking' option.
        $CFG->forum_usermarksread = true;

        // Set up a basic user enrolled in a course.
        $userhelper = $this->helper_setup_user_in_course();
        $user = $userhelper->user;
        $course1 = $userhelper->courses->course1;
        $forum1 = $userhelper->forums->forum1;
        $posts = [];

        // Set the tested user's default maildigest, trackforums, read tracking settings.
        $DB->set_field('user', 'maildigest', 1, ['id' => $user->id]);
        $DB->set_field('user', 'trackforums', 1, ['id' => $user->id]);
        set_user_preference('forum_markasreadonnotification', 1, $user->id);

        // Set the maildigest preference for forum1 to default.
        forum_set_user_maildigest($forum1, -1, $user);

        // Add 5 discussions to forum 1.
        for ($i = 0; $i < 5; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum1, $user, ['mailnow' => 1]);
            $posts[] = $post;
        }

        // There should be unread posts for the forum.
        $expectedposts = [
            $forum1->id => (object) [
                'id' => $forum1->id,
                'unread' => count($posts),
            ],
        ];
        $this->assertEquals($expectedposts, forum_tp_get_course_unread_posts($user->id, $course1->id));

        // One digest mail should be sent and no other messages.
        $expect = [
            (object) [
                'userid' => $user->id,
                'messages' => 0,
                'digests' => 1,
            ],
        ];
        $this->queue_tasks_and_assert($expect);

        $this->send_digests_and_assert($user, $posts);

        // Verify that there are still the same unread posts for the forum.
        $this->assertEquals($expectedposts, forum_tp_get_course_unread_posts($user->id, $course1->id));
    }
}
