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
    }

    /**
     * Test tearDown.
     */
    public function tearDown() {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
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
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // It should match the result of when it's called with the forum object.
        $this->assertFalse(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // And when the user is subscribed, we should also get the correct result.
        \mod_forum\subscriptions::subscribe_user($author->id, $forum);

        $this->assertTrue(forum_is_subscribed($author->id, $forum->id));
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();

        // It should match the result of when it's called with the forum object.
        $this->assertTrue(forum_is_subscribed($author->id, $forum));
        $this->assertEquals(1, count(phpunit_util::get_debugging_messages()));
        phpunit_util::reset_debugging();
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

}
