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
 * The module forums tests
 *
 * @package    mod_forum
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/forum/lib.php');

class mod_forum_subscriptions_testcase extends advanced_testcase {

    /**
     * Test setUp.
     */
    public function setUp() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();
    }

    /**
     * Test tearDown.
     */
    public function tearDown() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();
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
        global $DB;
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        // Create a discussion in the forum, and then add a post to that discussion.
        $record = new stdClass();
        $record->course = $forum->course;
        $record->userid = $author->id;
        $record->forum = $forum->id;
        $discussion = $generator->create_discussion($record);

        // Retrieve the post which was created by create_discussion.
        $post = $DB->get_record('forum_posts', array('discussion' => $discussion->id));

        return array($discussion, $post);
    }

    public function test_subscription_modes() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        \mod_forum\subscriptions::set_subscription_mode($forum->id, FORUM_FORCESUBSCRIBE);
        $forum = $DB->get_record('forum', array('id' => $forum->id));
        $this->assertEquals(FORUM_FORCESUBSCRIBE, \mod_forum\subscriptions::get_subscription_mode($forum));
        $this->assertTrue(\mod_forum\subscriptions::is_forcesubscribed($forum));
        $this->assertFalse(\mod_forum\subscriptions::is_subscribable($forum));
        $this->assertFalse(\mod_forum\subscriptions::subscription_disabled($forum));

        \mod_forum\subscriptions::set_subscription_mode($forum->id, FORUM_DISALLOWSUBSCRIBE);
        $forum = $DB->get_record('forum', array('id' => $forum->id));
        $this->assertEquals(FORUM_DISALLOWSUBSCRIBE, \mod_forum\subscriptions::get_subscription_mode($forum));
        $this->assertTrue(\mod_forum\subscriptions::subscription_disabled($forum));
        $this->assertFalse(\mod_forum\subscriptions::is_subscribable($forum));
        $this->assertFalse(\mod_forum\subscriptions::is_forcesubscribed($forum));

        \mod_forum\subscriptions::set_subscription_mode($forum->id, FORUM_INITIALSUBSCRIBE);
        $forum = $DB->get_record('forum', array('id' => $forum->id));
        $this->assertEquals(FORUM_INITIALSUBSCRIBE, \mod_forum\subscriptions::get_subscription_mode($forum));
        $this->assertTrue(\mod_forum\subscriptions::is_subscribable($forum));
        $this->assertFalse(\mod_forum\subscriptions::subscription_disabled($forum));
        $this->assertFalse(\mod_forum\subscriptions::is_forcesubscribed($forum));

        \mod_forum\subscriptions::set_subscription_mode($forum->id, FORUM_CHOOSESUBSCRIBE);
        $forum = $DB->get_record('forum', array('id' => $forum->id));
        $this->assertEquals(FORUM_CHOOSESUBSCRIBE, \mod_forum\subscriptions::get_subscription_mode($forum));
        $this->assertTrue(\mod_forum\subscriptions::is_subscribable($forum));
        $this->assertFalse(\mod_forum\subscriptions::subscription_disabled($forum));
        $this->assertFalse(\mod_forum\subscriptions::is_forcesubscribed($forum));
    }

    /**
     * Test fetching unsubscribable forums.
     */
    public function test_unsubscribable_forums() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        // Create a user enrolled in the course as a student.
        list($user) = $this->helper_create_users($course, 1);

        // Must be logged in as the current user.
        $this->setUser($user);

        // Without any subscriptions, there should be nothing returned.
        $result = \mod_forum\subscriptions::get_unsubscribable_forums();
        $this->assertEquals(0, count($result));

        // Create the forums.
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forceforum = $this->getDataGenerator()->create_module('forum', $options);
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_DISALLOWSUBSCRIBE);
        $disallowforum = $this->getDataGenerator()->create_module('forum', $options);
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $chooseforum = $this->getDataGenerator()->create_module('forum', $options);
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $initialforum = $this->getDataGenerator()->create_module('forum', $options);

        // At present the user is only subscribed to the initial forum.
        $result = \mod_forum\subscriptions::get_unsubscribable_forums();
        $this->assertEquals(1, count($result));

        // Ensure that the user is enrolled in all of the forums except force subscribed.
        \mod_forum\subscriptions::subscribe_user($user->id, $disallowforum);
        \mod_forum\subscriptions::subscribe_user($user->id, $chooseforum);

        $result = \mod_forum\subscriptions::get_unsubscribable_forums();
        $this->assertEquals(3, count($result));

        // Hide the forums.
        set_coursemodule_visible($forceforum->cmid, 0);
        set_coursemodule_visible($disallowforum->cmid, 0);
        set_coursemodule_visible($chooseforum->cmid, 0);
        set_coursemodule_visible($initialforum->cmid, 0);
        $result = \mod_forum\subscriptions::get_unsubscribable_forums();
        $this->assertEquals(0, count($result));

        // Add the moodle/course:viewhiddenactivities capability to the student user.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $context = \context_course::instance($course->id);
        assign_capability('moodle/course:viewhiddenactivities', CAP_ALLOW, $roleids['student'], $context);
        $context->mark_dirty();

        // All of the unsubscribable forums should now be listed.
        $result = \mod_forum\subscriptions::get_unsubscribable_forums();
        $this->assertEquals(3, count($result));
    }

    /**
     * Test that toggling the forum-level subscription for a different user does not affect their discussion-level
     * subscriptions.
     */
    public function test_forum_subscribe_toggle_as_other() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        list($author) = $this->helper_create_users($course, 1);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Check that the user is currently not subscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Check that the user is unsubscribed from the discussion too.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // Check that we have no records in either of the subscription tables.
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribing to the forum should create a record in the subscriptions table, but not the forum discussion
        // subscriptions table.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing should remove the record from the forum subscriptions table, and not modify the forum
        // discussion subscriptions table.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // The same thing should happen calling the deprecated versions of
        // these functions.
        // Subscribing to the forum should create a record in the subscriptions table, but not the forum discussion
        // subscriptions table.
        forum_subscribe($author->id, $forum->id);
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing should remove the record from the forum subscriptions table, and not modify the forum
        // discussion subscriptions table.
        forum_unsubscribe($author->id, $forum->id);
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Enroling the user in the discussion should add one record to the forum discussion table without modifying the
        // form subscriptions.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing should remove the record from the forum subscriptions table, and not modify the forum
        // discussion subscriptions table.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Re-subscribe to the discussion so that we can check the effect of forum-level subscriptions.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribing to the forum should have no effect on the forum discussion subscriptions table if the user did
        // not request the change themself.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing from the forum should have no effect on the forum discussion subscriptions table if the user
        // did not request the change themself.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribing to the forum should remove the per-discussion subscription preference if the user requested the
        // change themself.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum, null, true);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Now unsubscribe from the current discussion whilst being subscribed to the forum as a whole.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing from the forum should remove the per-discussion subscription preference if the user requested the
        // change themself.
        \mod_forum\subscriptions::unsubscribe_user($author->id, $forum, null, true);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribe to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribe to the forum without removing the discussion preferences.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Unsubscribing from the discussion should result in a change.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

    }

    /**
     * Test that a user unsubscribed from a forum is not subscribed to it's discussions by default.
     */
    public function test_forum_discussion_subscription_forum_unsubscribed() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create users enrolled in the course as students.
        list($author) = $this->helper_create_users($course, 1);

        // Check that the user is currently not subscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Check that the user is unsubscribed from the discussion too.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));
    }

    /**
     * Test that the act of subscribing to a forum subscribes the user to it's discussions by default.
     */
    public function test_forum_discussion_subscription_forum_subscribed() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create users enrolled in the course as students.
        list($author) = $this->helper_create_users($course, 1);

        // Enrol the user in the forum.
        // If a subscription was added, we get the record ID.
        $this->assertInternalType('int', \mod_forum\subscriptions::subscribe_user($author->id, $forum));

        // If we already have a subscription when subscribing the user, we get a boolean (true).
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user($author->id, $forum));

        // Check that the user is currently subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Check that the user is subscribed to the discussion too.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));
    }

    /**
     * Test that a user unsubscribed from a forum can be subscribed to a discussion.
     */
    public function test_forum_discussion_subscription_forum_unsubscribed_discussion_subscribed() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        list($author) = $this->helper_create_users($course, 1);

        // Check that the user is currently not subscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Attempting to unsubscribe from the discussion should not make a change.
        $this->assertFalse(\mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion));

        // Then subscribe them to the discussion.
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion));

        // Check that the user is still unsubscribed from the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But subscribed to the discussion.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));
    }

    /**
     * Test that a user subscribed to a forum can be unsubscribed from a discussion.
     */
    public function test_forum_discussion_subscription_forum_subscribed_discussion_unsubscribed() {
        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author) = $this->helper_create_users($course, 2);

        // Enrol the student in the forum.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);

        // Check that the user is currently subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Then unsubscribe them from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But unsubscribed from the discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));
    }

    /**
     * Test the effect of toggling the discussion subscription status when subscribed to the forum.
     */
    public function test_forum_discussion_toggle_forum_subscribed() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author) = $this->helper_create_users($course, 2);

        // Enrol the student in the forum.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);

        // Check that the user is currently subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Check that the user is initially subscribed to that discussion.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // An attempt to subscribe again should result in a falsey return to indicate that no change was made.
        $this->assertFalse(\mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion));

        // And there should be no discussion subscriptions (and one forum subscription).
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // Then unsubscribe them from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // An attempt to unsubscribe again should result in a falsey return to indicate that no change was made.
        $this->assertFalse(\mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion));

        // And there should be a discussion subscriptions (and one forum subscription).
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // But unsubscribed from the discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be a record in the discussion subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And one in the forum subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // Now subscribe the user again to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // And is subscribed to the discussion again.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be no record in the discussion subscription tracking table.
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And one in the forum subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // And unsubscribe again.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But unsubscribed from the discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be a record in the discussion subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And one in the forum subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // And subscribe the user again to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // And is subscribed to the discussion again.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be no record in the discussion subscription tracking table.
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And one in the forum subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // And unsubscribe again.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But unsubscribed from the discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be a record in the discussion subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And one in the forum subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // Now unsubscribe the user from the forum.
        $this->assertTrue(\mod_forum\subscriptions::unsubscribe_user($author->id, $forum, null, true));

        // This removes both the forum_subscriptions, and the forum_discussion_subs records.
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $author->id,
            'forum'         => $forum->id,
        )));

        // And should have reset the discussion cache value.
        $result = \mod_forum\subscriptions::fetch_discussion_subscription($forum->id, $author->id);
        $this->assertInternalType('array', $result);
        $this->assertFalse(isset($result[$discussion->id]));
    }

    /**
     * Test the effect of toggling the discussion subscription status when unsubscribed from the forum.
     */
    public function test_forum_discussion_toggle_forum_unsubscribed() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create two users enrolled in the course as students.
        list($author) = $this->helper_create_users($course, 2);

        // Check that the user is currently unsubscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $author);

        // Check that the user is initially unsubscribed to that discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // Then subscribe them to the discussion.
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion));

        // An attempt to subscribe again should result in a falsey return to indicate that no change was made.
        $this->assertFalse(\mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion));

        // Check that the user is still unsubscribed from the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But subscribed to the discussion.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be a record in the discussion subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // Now unsubscribe the user again from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still unsubscribed from the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // And is unsubscribed from the discussion again.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be no record in the discussion subscription tracking table.
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And subscribe the user again to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($author->id, $discussion);

        // Check that the user is still unsubscribed from the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // And is subscribed to the discussion again.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be a record in the discussion subscription tracking table.
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));

        // And unsubscribe again.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($author->id, $discussion);

        // Check that the user is still unsubscribed from the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum));

        // Check the deprecated function too.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // But unsubscribed from the discussion.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($author->id, $forum, $discussion->id));

        // There should be no record in the discussion subscription tracking table.
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $author->id,
            'discussion'    => $discussion->id,
        )));
    }

    /**
     * Test that the deprecated forum_is_subscribed accepts numeric forum IDs.
     */
    public function test_forum_is_subscribed_numeric() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a students.
        list($author) = $this->helper_create_users($course, 1);

        // Check that the user is currently unsubscribed to the forum.
        $this->assertFalse(forum_is_subscribed($author->id, $forum->id));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // It should match the result of when it's called with the forum object.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // And when the user is subscribed, we should also get the correct result.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);

        $this->assertTrue(forum_is_subscribed($author->id, $forum->id));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();

        // It should match the result of when it's called with the forum object.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count($this->getDebuggingMessages()));
        $this->resetDebugging();
    }

    /**
     * Test that the correct users are returned when fetching subscribed users from a forum where users can choose to
     * subscribe and unsubscribe.
     */
    public function test_fetch_subscribed_users_subscriptions() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Create a course, with a forum. where users are initially subscribed.
        $course = $this->getDataGenerator()->create_course();
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some user enrolled in the course as a student.
        $usercount = 5;
        $users = $this->helper_create_users($course, $usercount);

        // All users should be subscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));

        // Subscribe the guest user too to the forum - they should never be returned by this function.
        $this->getDataGenerator()->enrol_user($CFG->siteguest, $course->id);
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));

        // Unsubscribe 2 users.
        $unsubscribedcount = 2;
        for ($i = 0; $i < $unsubscribedcount; $i++) {
            \mod_forum\subscriptions::unsubscribe_user($users[$i]->id, $forum);
        }

        // The subscription count should now take into account those users who have been unsubscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount - $unsubscribedcount, count($subscribers));
    }

    /**
     * Test that the correct users are returned hwen fetching subscribed users from a forum where users are forcibly
     * subscribed.
     */
    public function test_fetch_subscribed_users_forced() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum. where users are initially subscribed.
        $course = $this->getDataGenerator()->create_course();
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some user enrolled in the course as a student.
        $usercount = 5;
        $users = $this->helper_create_users($course, $usercount);

        // All users should be subscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));
    }

    /**
     * Test that unusual combinations of discussion subscriptions do not affect the subscribed user list.
     */
    public function test_fetch_subscribed_users_discussion_subscriptions() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum. where users are initially subscribed.
        $course = $this->getDataGenerator()->create_course();
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some user enrolled in the course as a student.
        $usercount = 5;
        $users = $this->helper_create_users($course, $usercount);

        list($discussion, $post) = $this->helper_post_to_forum($forum, $users[0]);

        // All users should be subscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, null, null, true);
        $this->assertEquals($usercount, count($subscribers));

        \mod_forum\subscriptions::unsubscribe_user_from_discussion($users[0]->id, $discussion);

        // All users should be subscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));

        // All users should be subscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, null, null, true);
        $this->assertEquals($usercount, count($subscribers));

        // Manually insert an extra subscription for one of the users.
        $record = new stdClass();
        $record->userid = $users[2]->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->preference = time();
        $DB->insert_record('forum_discussion_subs', $record);

        // The discussion count should not have changed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount, count($subscribers));
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, null, null, true);
        $this->assertEquals($usercount, count($subscribers));

        // Unsubscribe 2 users.
        $unsubscribedcount = 2;
        for ($i = 0; $i < $unsubscribedcount; $i++) {
            \mod_forum\subscriptions::unsubscribe_user($users[$i]->id, $forum);
        }

        // The subscription count should now take into account those users who have been unsubscribed.
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount - $unsubscribedcount, count($subscribers));
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, null, null, true);
        $this->assertEquals($usercount - $unsubscribedcount, count($subscribers));

        // Now subscribe one of those users back to the discussion.
        $subscribeddiscussionusers = 1;
        for ($i = 0; $i < $subscribeddiscussionusers; $i++) {
            \mod_forum\subscriptions::subscribe_user_to_discussion($users[$i]->id, $discussion);
        }
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum);
        $this->assertEquals($usercount - $unsubscribedcount, count($subscribers));
        $subscribers = \mod_forum\subscriptions::fetch_subscribed_users($forum, 0, null, null, true);
        $this->assertEquals($usercount - $unsubscribedcount + $subscribeddiscussionusers, count($subscribers));
    }

    /**
     * Test whether a user is force-subscribed to a forum.
     */
    public function test_force_subscribed_to_forum() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleids['student']);

        // Check that the user is currently subscribed to the forum.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum));

        // Remove the allowforcesubscribe capability from the user.
        $cm = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($cm->id);
        assign_capability('mod/forum:allowforcesubscribe', CAP_PROHIBIT, $roleids['student'], $context);
        $context->mark_dirty();
        $this->assertFalse(has_capability('mod/forum:allowforcesubscribe', $context, $user->id));

        // Check that the user is no longer subscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum));
    }

    /**
     * Test that the subscription cache can be pre-filled.
     */
    public function test_subscription_cache_prefill() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some users.
        $users = $this->helper_create_users($course, 20);

        // Reset the subscription cache.
        \mod_forum\subscriptions::reset_forum_cache();

        // Filling the subscription cache should only use a single query.
        $startcount = $DB->perf_get_reads();
        $this->assertNull(\mod_forum\subscriptions::fill_subscription_cache($forum->id));
        $postfillcount = $DB->perf_get_reads();
        $this->assertEquals(1, $postfillcount - $startcount);

        // Now fetch some subscriptions from that forum - these should use
        // the cache and not perform additional queries.
        foreach ($users as $user) {
            $this->assertTrue(\mod_forum\subscriptions::fetch_subscription_cache($forum->id, $user->id));
        }
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(0, $finalcount - $postfillcount);
    }

    /**
     * Test that the subscription cache can filled user-at-a-time.
     */
    public function test_subscription_cache_fill() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some users.
        $users = $this->helper_create_users($course, 20);

        // Reset the subscription cache.
        \mod_forum\subscriptions::reset_forum_cache();

        // Filling the subscription cache should only use a single query.
        $startcount = $DB->perf_get_reads();

        // Fetch some subscriptions from that forum - these should not use the cache and will perform additional queries.
        foreach ($users as $user) {
            $this->assertTrue(\mod_forum\subscriptions::fetch_subscription_cache($forum->id, $user->id));
        }
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(20, $finalcount - $startcount);
    }

    /**
     * Test that the discussion subscription cache can filled course-at-a-time.
     */
    public function test_discussion_subscription_cache_fill_for_course() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        // Create the forums.
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_DISALLOWSUBSCRIBE);
        $disallowforum = $this->getDataGenerator()->create_module('forum', $options);
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $chooseforum = $this->getDataGenerator()->create_module('forum', $options);
        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $initialforum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some users and keep a reference to the first user.
        $users = $this->helper_create_users($course, 20);
        $user = reset($users);

        // Reset the subscription caches.
        \mod_forum\subscriptions::reset_forum_cache();

        $startcount = $DB->perf_get_reads();
        $result = \mod_forum\subscriptions::fill_subscription_cache_for_course($course->id, $user->id);
        $this->assertNull($result);
        $postfillcount = $DB->perf_get_reads();
        $this->assertEquals(1, $postfillcount - $startcount);
        $this->assertFalse(\mod_forum\subscriptions::fetch_subscription_cache($disallowforum->id, $user->id));
        $this->assertFalse(\mod_forum\subscriptions::fetch_subscription_cache($chooseforum->id, $user->id));
        $this->assertTrue(\mod_forum\subscriptions::fetch_subscription_cache($initialforum->id, $user->id));
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(0, $finalcount - $postfillcount);

        // Test for all users.
        foreach ($users as $user) {
            $result = \mod_forum\subscriptions::fill_subscription_cache_for_course($course->id, $user->id);
            $this->assertFalse(\mod_forum\subscriptions::fetch_subscription_cache($disallowforum->id, $user->id));
            $this->assertFalse(\mod_forum\subscriptions::fetch_subscription_cache($chooseforum->id, $user->id));
            $this->assertTrue(\mod_forum\subscriptions::fetch_subscription_cache($initialforum->id, $user->id));
        }
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(count($users), $finalcount - $postfillcount);
    }

    /**
     * Test that the discussion subscription cache can be forcibly updated for a user.
     */
    public function test_discussion_subscription_cache_prefill() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some users.
        $users = $this->helper_create_users($course, 20);

        // Post some discussions to the forum.
        $discussions = array();
        $author = $users[0];
        for ($i = 0; $i < 20; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
            $discussions[] = $discussion;
        }

        // Unsubscribe half the users from the half the discussions.
        $forumcount = 0;
        $usercount = 0;
        foreach ($discussions as $data) {
            if ($forumcount % 2) {
                continue;
            }
            foreach ($users as $user) {
                if ($usercount % 2) {
                    continue;
                }
                \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);
                $usercount++;
            }
            $forumcount++;
        }

        // Reset the subscription caches.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();

        // Filling the discussion subscription cache should only use a single query.
        $startcount = $DB->perf_get_reads();
        $this->assertNull(\mod_forum\subscriptions::fill_discussion_subscription_cache($forum->id));
        $postfillcount = $DB->perf_get_reads();
        $this->assertEquals(1, $postfillcount - $startcount);

        // Now fetch some subscriptions from that forum - these should use
        // the cache and not perform additional queries.
        foreach ($users as $user) {
            $result = \mod_forum\subscriptions::fetch_discussion_subscription($forum->id, $user->id);
            $this->assertInternalType('array', $result);
        }
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(0, $finalcount - $postfillcount);
    }

    /**
     * Test that the discussion subscription cache can filled user-at-a-time.
     */
    public function test_discussion_subscription_cache_fill() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create some users.
        $users = $this->helper_create_users($course, 20);

        // Post some discussions to the forum.
        $discussions = array();
        $author = $users[0];
        for ($i = 0; $i < 20; $i++) {
            list($discussion, $post) = $this->helper_post_to_forum($forum, $author);
            $discussions[] = $discussion;
        }

        // Unsubscribe half the users from the half the discussions.
        $forumcount = 0;
        $usercount = 0;
        foreach ($discussions as $data) {
            if ($forumcount % 2) {
                continue;
            }
            foreach ($users as $user) {
                if ($usercount % 2) {
                    continue;
                }
                \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);
                $usercount++;
            }
            $forumcount++;
        }

        // Reset the subscription caches.
        \mod_forum\subscriptions::reset_forum_cache();
        \mod_forum\subscriptions::reset_discussion_cache();

        $startcount = $DB->perf_get_reads();

        // Now fetch some subscriptions from that forum - these should use
        // the cache and not perform additional queries.
        foreach ($users as $user) {
            $result = \mod_forum\subscriptions::fetch_discussion_subscription($forum->id, $user->id);
            $this->assertInternalType('array', $result);
        }
        $finalcount = $DB->perf_get_reads();
        $this->assertEquals(20, $finalcount - $startcount);
    }

    /**
     * Test that after toggling the forum subscription as another user,
     * the discussion subscription functionality works as expected.
     */
    public function test_forum_subscribe_toggle_as_other_repeat_subscriptions() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        list($user) = $this->helper_create_users($course, 1);

        // Post a discussion to the forum.
        list($discussion, $post) = $this->helper_post_to_forum($forum, $user);

        // Confirm that the user is currently not subscribed to the forum.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum));

        // Confirm that the user is unsubscribed from the discussion too.
        $this->assertFalse(\mod_forum\subscriptions::is_subscribed($user->id, $forum, $discussion->id));

        // Confirm that we have no records in either of the subscription tables.
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $user->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $user->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribing to the forum should create a record in the subscriptions table, but not the forum discussion
        // subscriptions table.
        \mod_forum\subscriptions::subscribe_user($user->id, $forum);
        $this->assertEquals(1, $DB->count_records('forum_subscriptions', array(
            'userid'        => $user->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(0, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $user->id,
            'discussion'    => $discussion->id,
        )));

        // Now unsubscribe from the discussion. This should return true.
        $this->assertTrue(\mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion));

        // Attempting to unsubscribe again should return false because no change was made.
        $this->assertFalse(\mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion));

        // Subscribing to the discussion again should return truthfully as the subscription preference was removed.
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion));

        // Attempting to subscribe again should return false because no change was made.
        $this->assertFalse(\mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion));

        // Now unsubscribe from the discussion. This should return true once more.
        $this->assertTrue(\mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion));

        // And unsubscribing from the forum but not as a request from the user should maintain their preference.
        \mod_forum\subscriptions::unsubscribe_user($user->id, $forum);

        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $user->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $user->id,
            'discussion'    => $discussion->id,
        )));

        // Subscribing to the discussion should return truthfully because a change was made.
        $this->assertTrue(\mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion));
        $this->assertEquals(0, $DB->count_records('forum_subscriptions', array(
            'userid'        => $user->id,
            'forum'         => $forum->id,
        )));
        $this->assertEquals(1, $DB->count_records('forum_discussion_subs', array(
            'userid'        => $user->id,
            'discussion'    => $discussion->id,
        )));
    }

    /**
     * Test that providing a context_module instance to is_subscribed does not result in additional lookups to retrieve
     * the context_module.
     */
    public function test_is_subscribed_cm() {
        global $DB;

        $this->resetAfterTest(true);

        // Create a course, with a forum.
        $course = $this->getDataGenerator()->create_course();

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_FORCESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Create a user enrolled in the course as a student.
        list($user) = $this->helper_create_users($course, 1);

        // Retrieve the $cm now.
        $cm = get_fast_modinfo($forum->course)->instances['forum'][$forum->id];

        // Reset get_fast_modinfo.
        get_fast_modinfo(0, 0, true);

        // Call is_subscribed without passing the $cmid - this should result in a lookup and filling of some of the
        // caches. This provides us with consistent data to start from.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum));
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum));

        // Make a note of the number of DB calls.
        $basecount = $DB->perf_get_reads();

        // Call is_subscribed - it should give return the correct result (False), and result in no additional queries.
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum, null, $cm));

        // The capability check does require some queries, so we don't test it directly.
        // We don't assert here because this is dependant upon linked code which could change at any time.
        $suppliedcmcount = $DB->perf_get_reads() - $basecount;

        // Call is_subscribed without passing the $cmid now - this should result in a lookup.
        get_fast_modinfo(0, 0, true);
        $basecount = $DB->perf_get_reads();
        $this->assertTrue(\mod_forum\subscriptions::is_subscribed($user->id, $forum));
        $calculatedcmcount = $DB->perf_get_reads() - $basecount;

        // There should be more queries than when we performed the same check a moment ago.
        $this->assertGreaterThan($suppliedcmcount, $calculatedcmcount);
    }

}
