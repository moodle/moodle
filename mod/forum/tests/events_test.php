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
 * Tests for forum events.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for forum events.
 *
 * @package    mod_forum
 * @category   test
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_events_testcase extends advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $this->resetAfterTest();
    }

    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Ensure course_searched event validates that searchterm is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'searchterm' value must be set in other.
     */
    public function test_course_searched_searchterm_validation() {
        $course = $this->getDataGenerator()->create_course();
        $coursectx = context_course::instance($course->id);
        $params = array(
            'context' => $coursectx,
        );

        \mod_forum\event\course_searched::create($params);
    }

    /**
     * Ensure course_searched event validates that context is the correct level.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_COURSE.
     */
    public function test_course_searched_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);
        $params = array(
            'context' => $context,
            'other' => array('searchterm' => 'testing'),
        );

        \mod_forum\event\course_searched::create($params);
    }

    /**
     * Test course_searched event.
     */
    public function test_course_searched() {

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $coursectx = context_course::instance($course->id);
        $searchterm = 'testing123';

        $params = array(
            'context' => $coursectx,
            'other' => array('searchterm' => $searchterm),
        );

        // Create event.
        $event = \mod_forum\event\course_searched::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

         // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\course_searched', $event);
        $this->assertEquals($coursectx, $event->get_context());
        $expected = array($course->id, 'forum', 'search', "search.php?id={$course->id}&amp;search={$searchterm}", $searchterm);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure discussion_created event validates that forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_discussion_created_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
        );

        \mod_forum\event\discussion_created::create($params);
    }

    /**
     * Ensure discussion_created event validates that the context is the correct level.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_created_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
        );

        \mod_forum\event\discussion_created::create($params);
    }

    /**
     * Test discussion_created event.
     */
    public function test_discussion_created() {

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => array('forumid' => $forum->id),
        );

        // Create the event.
        $event = \mod_forum\event\discussion_created::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_created', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'add discussion', "discuss.php?d={$discussion->id}", $discussion->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure discussion_updated event validates that forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_discussion_updated_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
        );

        \mod_forum\event\discussion_updated::create($params);
    }

    /**
     * Ensure discussion_created event validates that the context is the correct level.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_updated_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
        );

        \mod_forum\event\discussion_updated::create($params);
    }

    /**
     * Test discussion_created event.
     */
    public function test_discussion_updated() {

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => array('forumid' => $forum->id),
        );

        // Create the event.
        $event = \mod_forum\event\discussion_updated::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_updated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure discussion_deleted event validates that forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_discussion_deleted_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
        );

        \mod_forum\event\discussion_deleted::create($params);
    }

    /**
     * Ensure discussion_deleted event validates that context is of the correct level.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_deleted_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
        );

        \mod_forum\event\discussion_deleted::create($params);
    }

    /**
     * Test discussion_deleted event.
     */
    public function test_discussion_deleted() {

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => array('forumid' => $forum->id),
        );

        $event = \mod_forum\event\discussion_deleted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'delete discussion', "view.php?id={$forum->cmid}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure discussion_moved event validates that fromforumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'fromforumid' value must be set in other.
     */
    public function test_discussion_moved_fromforumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $toforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $context = context_module::instance($toforum->cmid);

        $params = array(
            'context' => $context,
            'other' => array('toforumid' => $toforum->id)
        );

        \mod_forum\event\discussion_moved::create($params);
    }

    /**
     * Ensure discussion_moved event validates that toforumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'toforumid' value must be set in other.
     */
    public function test_discussion_moved_toforumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $fromforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $toforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($toforum->cmid);

        $params = array(
            'context' => $context,
            'other' => array('fromforumid' => $fromforum->id)
        );

        \mod_forum\event\discussion_moved::create($params);
    }

    /**
     * Ensure discussion_moved event validates that the context level is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_moved_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $fromforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $toforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $fromforum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $discussion->id,
            'other' => array('fromforumid' => $fromforum->id, 'toforumid' => $toforum->id)
        );

        \mod_forum\event\discussion_moved::create($params);
    }

    /**
     * Test discussion_moved event.
     */
    public function test_discussion_moved() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $fromforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $toforum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $fromforum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $context = context_module::instance($toforum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $discussion->id,
            'other' => array('fromforumid' => $fromforum->id, 'toforumid' => $toforum->id)
        );

        $event = \mod_forum\event\discussion_moved::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_moved', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'move discussion', "discuss.php?d={$discussion->id}",
            $discussion->id, $toforum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }


    /**
     * Ensure discussion_viewed event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_viewed_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $discussion->id,
        );

        \mod_forum\event\discussion_viewed::create($params);
    }

    /**
     * Test discussion_viewed event.
     */
    public function test_discussion_viewed() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $discussion->id,
        );

        $event = \mod_forum\event\discussion_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'view discussion', "discuss.php?d={$discussion->id}",
            $discussion->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure course_module_viewed event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_course_module_viewed_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $forum->id,
        );

        \mod_forum\event\course_module_viewed::create($params);
    }

    /**
     * Test the course_module_viewed event.
     */
    public function test_course_module_viewed() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $forum->id,
        );

        $event = \mod_forum\event\course_module_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'view forum', "view.php?f={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure subscription_created event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_subscription_created_forumid_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscription_created::create($params);
    }

    /**
     * Ensure subscription_created event validates that the relateduserid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_subscription_created_relateduserid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $forum->id,
        );

        \mod_forum\event\subscription_created::create($params);
    }

    /**
     * Ensure subscription_created event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_subscription_created_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscription_created::create($params);
    }

    /**
     * Test the subscription_created event.
     */
    public function test_subscription_created() {
        // Setup test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $context = context_module::instance($forum->cmid);

        // Add a subscription.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $subscription = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_subscription($record);

        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        $event = \mod_forum\event\subscription_created::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_created', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'subscribe', "view.php?f={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/subscribers.php', array('id' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure subscription_deleted event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_subscription_deleted_forumid_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscription_deleted::create($params);
    }

    /**
     * Ensure subscription_deleted event validates that the relateduserid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_subscription_deleted_relateduserid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $forum->id,
        );

        \mod_forum\event\subscription_deleted::create($params);
    }

    /**
     * Ensure subscription_deleted event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_subscription_deleted_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscription_deleted::create($params);
    }

    /**
     * Test the subscription_deleted event.
     */
    public function test_subscription_deleted() {
        // Setup test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $context = context_module::instance($forum->cmid);

        // Add a subscription.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $subscription = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_subscription($record);

        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        $event = \mod_forum\event\subscription_deleted::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'unsubscribe', "view.php?f={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/subscribers.php', array('id' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure readtracking_enabled event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_readtracking_enabled_forumid_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\readtracking_enabled::create($params);
    }

    /**
     * Ensure readtracking_enabled event validates that the relateduserid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_readtracking_enabled_relateduserid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $forum->id,
        );

        \mod_forum\event\readtracking_enabled::create($params);
    }

    /**
     * Ensure readtracking_enabled event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_readtracking_enabled_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\readtracking_enabled::create($params);
    }

    /**
     * Test the readtracking_enabled event.
     */
    public function test_readtracking_enabled() {
        // Setup test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        $event = \mod_forum\event\readtracking_enabled::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\readtracking_enabled', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'start tracking', "view.php?f={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     *  Ensure readtracking_disabled event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_readtracking_disabled_forumid_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\readtracking_disabled::create($params);
    }

    /**
     *  Ensure readtracking_disabled event validates that the relateduserid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_readtracking_disabled_relateduserid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $forum->id,
        );

        \mod_forum\event\readtracking_disabled::create($params);
    }

    /**
     *  Ensure readtracking_disabled event validates that the contextlevel is correct
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_readtracking_disabled_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\readtracking_disabled::create($params);
    }

    /**
     *  Test the readtracking_disabled event.
     */
    public function test_readtracking_disabled() {
        // Setup test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        $event = \mod_forum\event\readtracking_disabled::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\readtracking_disabled', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'stop tracking', "view.php?f={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     *  Ensure subscribers_viewed event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_subscribers_viewed_forumid_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscribers_viewed::create($params);
    }

    /**
     *  Ensure subscribers_viewed event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_subscribers_viewed_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_system::instance(),
            'other' => array('forumid' => $forum->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\subscribers_viewed::create($params);
    }

    /**
     *  Test the subscribers_viewed event.
     */
    public function test_subscribers_viewed() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'other' => array('forumid' => $forum->id),
        );

        $event = \mod_forum\event\subscribers_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscribers_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'view subscribers', "subscribers.php?id={$forum->id}", $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure user_report_viewed event validates that the reportmode is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'reportmode' value must be set in other.
     */
    public function test_user_report_viewed_reportmode_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        $params = array(
            'context' => context_course::instance($course->id),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\user_report_viewed::create($params);
    }

    /**
     * Ensure user_report_viewed event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be either CONTEXT_SYSTEM, CONTEXT_COURSE or CONTEXT_USER.
     */
    public function test_user_report_viewed_contextlevel_validation() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'other' => array('reportmode' => 'posts'),
            'relateduserid' => $user->id,
        );

        \mod_forum\event\user_report_viewed::create($params);
    }

    /**
     *  Ensure user_report_viewed event validates that the relateduserid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_user_report_viewed_relateduserid_validation() {

        $params = array(
            'context' => context_system::instance(),
            'other' => array('reportmode' => 'posts'),
        );

        \mod_forum\event\user_report_viewed::create($params);
    }

    /**
     * Test the user_report_viewed event.
     */
    public function test_user_report_viewed() {
        // Setup test data.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);

        $params = array(
            'context' => $context,
            'relateduserid' => $user->id,
            'other' => array('reportmode' => 'discussions'),
        );

        $event = \mod_forum\event\user_report_viewed::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\user_report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'user report',
            "user.php?id={$user->id}&amp;mode=discussions&amp;course={$course->id}", $user->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     *  Ensure post_created event validates that the postid is set.
     */
    public function test_post_created_postid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'other' => array('forumid' => $forum->id, 'forumtype' => $forum->type, 'discussionid' => $discussion->id)
        );

        \mod_forum\event\post_created::create($params);
    }

    /**
     * Ensure post_created event validates that the discussionid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'discussionid' value must be set in other.
     */
    public function test_post_created_discussionid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_created::create($params);
    }

    /**
     *  Ensure post_created event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_post_created_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_created::create($params);
    }

    /**
     * Ensure post_created event validates that the forumtype is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumtype' value must be set in other.
     */
    public function test_post_created_forumtype_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id)
        );

        \mod_forum\event\post_created::create($params);
    }

    /**
     *  Ensure post_created event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_post_created_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_created::create($params);
    }

    /**
     * Test the post_created event.
     */
    public function test_post_created() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        $event = \mod_forum\event\post_created::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_created', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'add post', "discuss.php?d={$discussion->id}#p{$post->id}",
            $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id));
        $url->set_anchor('p'.$event->objectid);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test the post_created event for a single discussion forum.
     */
    public function test_post_created_single() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id, 'type' => 'single'));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        $event = \mod_forum\event\post_created::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_created', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'add post', "view.php?f={$forum->id}#p{$post->id}",
            $forum->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $url->set_anchor('p'.$event->objectid);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     *  Ensure post_deleted event validates that the postid is set.
     */
    public function test_post_deleted_postid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'other' => array('forumid' => $forum->id, 'forumtype' => $forum->type, 'discussionid' => $discussion->id)
        );

        \mod_forum\event\post_deleted::create($params);
    }

    /**
     * Ensure post_deleted event validates that the discussionid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'discussionid' value must be set in other.
     */
    public function test_post_deleted_discussionid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_deleted::create($params);
    }

    /**
     *  Ensure post_deleted event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_post_deleted_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_deleted::create($params);
    }

    /**
     * Ensure post_deleted event validates that the forumtype is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumtype' value must be set in other.
     */
    public function test_post_deleted_forumtype_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id)
        );

        \mod_forum\event\post_deleted::create($params);
    }

    /**
     *  Ensure post_deleted event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_post_deleted_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_deleted::create($params);
    }

    /**
     * Test post_deleted event.
     */
    public function test_post_deleted() {
        global $DB;

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $cm = get_coursemodule_from_instance('forum', $forum->id, $forum->course);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // When creating a discussion we also create a post, so get the post.
        $discussionpost = $DB->get_records('forum_posts');
        // Will only be one here.
        $discussionpost = reset($discussionpost);

        // Add a few posts.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $posts = array();
        $posts[$discussionpost->id] = $discussionpost;
        for ($i = 0; $i < 3; $i++) {
            $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);
            $posts[$post->id] = $post;
        }

        // Delete the last post and capture the event.
        $lastpost = end($posts);
        $sink = $this->redirectEvents();
        forum_delete_post($lastpost, true, $course, $cm, $forum);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the events contain the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_deleted', $event);
        $this->assertEquals(context_module::instance($forum->cmid), $event->get_context());
        $expected = array($course->id, 'forum', 'delete post', "discuss.php?d={$discussion->id}", $lastpost->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Delete the whole discussion and capture the events.
        $sink = $this->redirectEvents();
        forum_delete_discussion($discussion, true, $course, $cm, $forum);
        $events = $sink->get_events();
        // We will have 4 events. One for the discussion, another one for the discussion topic post, and two for the posts.
        $this->assertCount(4, $events);

        // Loop through the events and check they are valid.
        foreach ($events as $event) {
            if ($event instanceof \mod_forum\event\discussion_deleted) {
                // Check that the event contains the expected values.
                $this->assertEquals($event->objectid, $discussion->id);
                $this->assertEquals(context_module::instance($forum->cmid), $event->get_context());
                $expected = array($course->id, 'forum', 'delete discussion', "view.php?id={$forum->cmid}",
                    $forum->id, $forum->cmid);
                $this->assertEventLegacyLogData($expected, $event);
                $url = new \moodle_url('/mod/forum/view.php', array('id' => $forum->cmid));
                $this->assertEquals($url, $event->get_url());
                $this->assertEventContextNotUsed($event);
                $this->assertNotEmpty($event->get_name());
            } else {
                $post = $posts[$event->objectid];
                // Check that the event contains the expected values.
                $this->assertInstanceOf('\mod_forum\event\post_deleted', $event);
                $this->assertEquals($event->objectid, $post->id);
                $this->assertEquals(context_module::instance($forum->cmid), $event->get_context());
                $expected = array($course->id, 'forum', 'delete post', "discuss.php?d={$discussion->id}", $post->id, $forum->cmid);
                $this->assertEventLegacyLogData($expected, $event);
                $url = new \moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id));
                $this->assertEquals($url, $event->get_url());
                $this->assertEventContextNotUsed($event);
                $this->assertNotEmpty($event->get_name());
            }
        }
    }

    /**
     * Test post_deleted event for a single discussion forum.
     */
    public function test_post_deleted_single() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id, 'type' => 'single'));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        $event = \mod_forum\event\post_deleted::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_deleted', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'delete post', "view.php?f={$forum->id}", $post->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Ensure post_updated event validates that the discussionid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'discussionid' value must be set in other.
     */
    public function test_post_updated_discussionid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_updated::create($params);
    }

    /**
     * Ensure post_updated event validates that the forumid is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_post_updated_forumid_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_updated::create($params);
    }

    /**
     * Ensure post_updated event validates that the forumtype is set.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumtype' value must be set in other.
     */
    public function test_post_updated_forumtype_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id)
        );

        \mod_forum\event\post_updated::create($params);
    }

    /**
     *  Ensure post_updated event validates that the contextlevel is correct.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_post_updated_context_validation() {
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $params = array(
            'context' => context_system::instance(),
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        \mod_forum\event\post_updated::create($params);
    }

    /**
     * Test post_updated event.
     */
    public function test_post_updated() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        $event = \mod_forum\event\post_updated::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_updated', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'update post', "discuss.php?d={$discussion->id}#p{$post->id}",
            $post->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/discuss.php', array('d' => $discussion->id));
        $url->set_anchor('p'.$event->objectid);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test post_updated event.
     */
    public function test_post_updated_single() {
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id, 'type' => 'single'));
        $user = $this->getDataGenerator()->create_user();

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $post->id,
            'other' => array('discussionid' => $discussion->id, 'forumid' => $forum->id, 'forumtype' => $forum->type)
        );

        $event = \mod_forum\event\post_updated::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\post_updated', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, 'forum', 'update post', "view.php?f={$forum->id}#p{$post->id}",
            $post->id, $forum->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $url = new \moodle_url('/mod/forum/view.php', array('f' => $forum->id));
        $url->set_anchor('p'.$post->id);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test discussion_subscription_created event.
     */
    public function test_discussion_subscription_created() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();

        // Trigger the event by subscribing the user to the forum discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_created', $event);


        $cm = get_coursemodule_from_instance('forum', $discussion->forum);
        $context = \context_module::instance($cm->id);
        $this->assertEquals($context, $event->get_context());

        $url = new \moodle_url('/mod/forum/subscribe.php', array(
            'id' => $forum->id,
            'd' => $discussion->id
        ));

        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test validation of discussion_subscription_created event.
     */
    public function test_discussion_subscription_created_validation() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        $event = \mod_forum\event\discussion_subscription_created::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
    }

    /**
     * Test contextlevel validation of discussion_subscription_created event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_subscription_created_validation_contextlevel() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => \context_course::instance($course->id),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        // Without an invalid context.
        \mod_forum\event\discussion_subscription_created::create($params);
    }

    /**
     * Test discussion validation of discussion_subscription_created event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'discussion' value must be set in other.
     */
    public function test_discussion_subscription_created_validation_discussion() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        // Without the discussion.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
            )
        );

        \mod_forum\event\discussion_subscription_created::create($params);
    }

    /**
     * Test forumid validation of discussion_subscription_created event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_discussion_subscription_created_validation_forumid() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        // Without the forumid.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'discussion' => $discussion->id,
            )
        );

        \mod_forum\event\discussion_subscription_created::create($params);
    }

    /**
     * Test relateduserid validation of discussion_subscription_created event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_discussion_subscription_created_validation_relateduserid() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        // Without the relateduserid.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        \mod_forum\event\discussion_subscription_created::create($params);
    }

    /**
     * Test discussion_subscription_deleted event.
     */
    public function test_discussion_subscription_deleted() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();

        // Trigger the event by unsubscribing the user to the forum discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_deleted', $event);


        $cm = get_coursemodule_from_instance('forum', $discussion->forum);
        $context = \context_module::instance($cm->id);
        $this->assertEquals($context, $event->get_context());

        $url = new \moodle_url('/mod/forum/subscribe.php', array(
            'id' => $forum->id,
            'd' => $discussion->id
        ));

        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test validation of discussion_subscription_deleted event.
     */
    public function test_discussion_subscription_deleted_validation() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = \mod_forum\subscriptions::FORUM_DISCUSSION_UNSUBSCRIBED;

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => $context,
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        $event = \mod_forum\event\discussion_subscription_deleted::create($params);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Without an invalid context.
        $params['context'] = \context_course::instance($course->id);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('Context level must be CONTEXT_MODULE.');
        \mod_forum\event\discussion_deleted::create($params);

        // Without the discussion.
        unset($params['discussion']);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The \'discussion\' value must be set in other.');
        \mod_forum\event\discussion_deleted::create($params);

        // Without the forumid.
        unset($params['forumid']);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The \'forumid\' value must be set in other.');
        \mod_forum\event\discussion_deleted::create($params);

        // Without the relateduserid.
        unset($params['relateduserid']);
        $this->expectException('coding_exception');
        $this->expectExceptionMessage('The \'relateduserid\' value must be set in other.');
        \mod_forum\event\discussion_deleted::create($params);
    }

    /**
     * Test contextlevel validation of discussion_subscription_deleted event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage Context level must be CONTEXT_MODULE.
     */
    public function test_discussion_subscription_deleted_validation_contextlevel() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        $params = array(
            'context' => \context_course::instance($course->id),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        // Without an invalid context.
        \mod_forum\event\discussion_subscription_deleted::create($params);
    }

    /**
     * Test discussion validation of discussion_subscription_deleted event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'discussion' value must be set in other.
     */
    public function test_discussion_subscription_deleted_validation_discussion() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        // Without the discussion.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'forumid' => $forum->id,
            )
        );

        \mod_forum\event\discussion_subscription_deleted::create($params);
    }

    /**
     * Test forumid validation of discussion_subscription_deleted event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'forumid' value must be set in other.
     */
    public function test_discussion_subscription_deleted_validation_forumid() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        // Without the forumid.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'relateduserid' => $user->id,
            'other' => array(
                'discussion' => $discussion->id,
            )
        );

        \mod_forum\event\discussion_subscription_deleted::create($params);
    }

    /**
     * Test relateduserid validation of discussion_subscription_deleted event.
     *
     * @expectedException        coding_exception
     * @expectedExceptionMessage The 'relateduserid' must be set.
     */
    public function test_discussion_subscription_deleted_validation_relateduserid() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // The user is not subscribed to the forum. Insert a new discussion subscription.
        $subscription = new \stdClass();
        $subscription->userid  = $user->id;
        $subscription->forum = $forum->id;
        $subscription->discussion = $discussion->id;
        $subscription->preference = time();

        $subscription->id = $DB->insert_record('forum_discussion_subs', $subscription);

        $context = context_module::instance($forum->cmid);

        // Without the relateduserid.
        $params = array(
            'context' => context_module::instance($forum->cmid),
            'objectid' => $subscription->id,
            'other' => array(
                'forumid' => $forum->id,
                'discussion' => $discussion->id,
            )
        );

        \mod_forum\event\discussion_subscription_deleted::create($params);
    }

    /**
     * Test that the correct context is used in the events when subscribing
     * users.
     */
    public function test_forum_subscription_page_context_valid() {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $options = array('course' => $course->id, 'forcesubscribe' => FORUM_CHOOSESUBSCRIBE);
        $forum = $this->getDataGenerator()->create_module('forum', $options);
        $quiz = $this->getDataGenerator()->create_module('quiz', $options);

        // Add a discussion.
        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $forum->id;
        $record['userid'] = $user->id;
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add a post.
        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        // Set up the default page event to use this forum.
        $PAGE = new moodle_page();
        $cm = get_coursemodule_from_instance('forum', $discussion->forum);
        $context = \context_module::instance($cm->id);
        $PAGE->set_context($context);
        $PAGE->set_cm($cm, $course, $forum);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();

        // Trigger the event by subscribing the user to the forum.
        \mod_forum\subscriptions::subscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user to the forum.
        \mod_forum\subscriptions::unsubscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by subscribing the user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

        // Now try with the context for a different module (quiz).
        $PAGE = new moodle_page();
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $quizcontext = \context_module::instance($cm->id);
        $PAGE->set_context($quizcontext);
        $PAGE->set_cm($cm, $course, $quiz);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();

        // Trigger the event by subscribing the user to the forum.
        \mod_forum\subscriptions::subscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user to the forum.
        \mod_forum\subscriptions::unsubscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by subscribing the user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

        // Now try with the course context - the module context should still be used.
        $PAGE = new moodle_page();
        $coursecontext = \context_course::instance($course->id);
        $PAGE->set_context($coursecontext);

        // Trigger the event by subscribing the user to the forum.
        \mod_forum\subscriptions::subscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user to the forum.
        \mod_forum\subscriptions::unsubscribe_user($user->id, $forum);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by subscribing the user to the discussion.
        \mod_forum\subscriptions::subscribe_user_to_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_created', $event);
        $this->assertEquals($context, $event->get_context());

        // Trigger the event by unsubscribing the user from the discussion.
        \mod_forum\subscriptions::unsubscribe_user_from_discussion($user->id, $discussion);

        $events = $sink->get_events();
        $sink->clear();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_forum\event\discussion_subscription_deleted', $event);
        $this->assertEquals($context, $event->get_context());

    }

    /**
     * Test mod_forum_observer methods.
     */
    public function test_observers() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $forumgen = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $course = $this->getDataGenerator()->create_course();
        $trackedrecord = array('course' => $course->id, 'type' => 'general', 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $untrackedrecord = array('course' => $course->id, 'type' => 'general');
        $trackedforum = $this->getDataGenerator()->create_module('forum', $trackedrecord);
        $untrackedforum = $this->getDataGenerator()->create_module('forum', $untrackedrecord);

        // Used functions don't require these settings; adding
        // them just in case there are APIs changes in future.
        $user = $this->getDataGenerator()->create_user(array(
            'maildigest' => 1,
            'trackforums' => 1
        ));

        $manplugin = enrol_get_plugin('manual');
        $manualenrol = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'));
        $student = $DB->get_record('role', array('shortname' => 'student'));

        // The role_assign observer does it's job adding the forum_subscriptions record.
        $manplugin->enrol_user($manualenrol, $user->id, $student->id);

        // They are not required, but in a real environment they are supposed to be required;
        // adding them just in case there are APIs changes in future.
        set_config('forum_trackingtype', 1);
        set_config('forum_trackreadposts', 1);

        $record = array();
        $record['course'] = $course->id;
        $record['forum'] = $trackedforum->id;
        $record['userid'] = $user->id;
        $discussion = $forumgen->create_discussion($record);

        $record = array();
        $record['discussion'] = $discussion->id;
        $record['userid'] = $user->id;
        $post = $forumgen->create_post($record);

        forum_tp_add_read_record($user->id, $post->id);
        forum_set_user_maildigest($trackedforum, 2, $user);
        forum_tp_stop_tracking($untrackedforum->id, $user->id);

        $this->assertEquals(1, $DB->count_records('forum_subscriptions'));
        $this->assertEquals(1, $DB->count_records('forum_digests'));
        $this->assertEquals(1, $DB->count_records('forum_track_prefs'));
        $this->assertEquals(1, $DB->count_records('forum_read'));

        // The course_module_created observer does it's job adding a subscription.
        $forumrecord = array('course' => $course->id, 'type' => 'general', 'forcesubscribe' => FORUM_INITIALSUBSCRIBE);
        $extraforum = $this->getDataGenerator()->create_module('forum', $forumrecord);
        $this->assertEquals(2, $DB->count_records('forum_subscriptions'));

        $manplugin->unenrol_user($manualenrol, $user->id);

        $this->assertEquals(0, $DB->count_records('forum_digests'));
        $this->assertEquals(0, $DB->count_records('forum_subscriptions'));
        $this->assertEquals(0, $DB->count_records('forum_track_prefs'));
        $this->assertEquals(0, $DB->count_records('forum_read'));
    }

}
