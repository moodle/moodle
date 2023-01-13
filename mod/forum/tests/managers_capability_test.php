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

use mod_forum\local\container;
use mod_forum\local\entities\forum;
use mod_forum\local\managers\capability as capability_manager;
use mod_forum_tests_generator_trait;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/generator_trait.php');

/**
 * The capability manager tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_forum\local\managers\capability
 */
class managers_capability_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var stdClass */
    private $user;

    /** @var \mod_forum\local\factories\entity */
    private $entityfactory;

    /** @var \mod_forum\local\factories\manager */
    private $managerfactory;

    /** @var stdClass */
    private $course;

    /** @var stdClass */
    private $forumrecord;

    /** @var stdClass */
    private $coursemodule;

    /** @var context */
    private $context;

    /** @var int */
    private $roleid;

    /** @var \mod_forum\local\entities\discussion */
    private $discussion;

    /** @var stdClass */
    private $discussionrecord;

    /** @var \mod_forum\local\entities\post */
    private $post;

    /** @var stdClass */
    private $postrecord;

    /**
     * Setup function before each test.
     */
    public function setUp(): void {
        global $DB;

        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $datagenerator = $this->getDataGenerator();
        $this->user = $datagenerator->create_user();
        $this->managerfactory = container::get_manager_factory();
        $this->entityfactory = container::get_entity_factory();
        $this->course = $datagenerator->create_course();
        $this->forumrecord = $datagenerator->create_module('forum', ['course' => $this->course->id]);
        $this->coursemodule = get_coursemodule_from_instance('forum', $this->forumrecord->id);
        $this->context = \context_module::instance($this->coursemodule->id);
        $this->roleid = $DB->get_field('role', 'id', ['shortname' => 'teacher'], MUST_EXIST);

        $datagenerator->enrol_user($this->user->id, $this->course->id, 'teacher');
        [$discussion, $post] = $this->helper_post_to_forum($this->forumrecord, $this->user, ['timemodified' => time() - 100]);
        $this->discussion = $this->entityfactory->get_discussion_from_stdClass($discussion);
        $this->discussionrecord = $discussion;
        $this->post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $post, ['timecreated' => time() - 100])
        );
        $this->postrecord = $post;

        $this->setUser($this->user);
    }

    /**
     * Tear down function after each test.
     */
    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
    }

    /**
     * Helper function to create a forum entity.
     *
     * @param array $forumproperties List of properties to override the prebuilt forum
     * @return forum
     */
    private function create_forum(array $forumproperties = []) {
        $forumrecord = (object) array_merge((array) $this->forumrecord, $forumproperties);
        return $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $this->context,
            $this->coursemodule,
            $this->course
        );
    }

    /**
     * Helper function to assign a capability to the prebuilt role (teacher).
     *
     * @param string $capability Name of the capability
     * @param context|null $context The context to assign the capability in
     */
    private function give_capability($capability, $context = null) {
        $context = $context ?? $this->context;
        assign_capability($capability, CAP_ALLOW, $this->roleid, $context->id, true);
    }

    /**
     * Helper function to prevent a capability to the prebuilt role (teacher).
     *
     * @param string $capability Name of the capability
     * @param context|null $context The context to assign the capability in
     */
    private function prevent_capability($capability, $context = null) {
        $context = $context ?? $this->context;
        assign_capability($capability, CAP_PREVENT, $this->roleid, $context->id, true);
    }

    /**
     * Test can_subscribe_to_forum.
     *
     * @covers ::can_subscribe_to_forum
     */
    public function test_can_subscribe_to_forum() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $guestuser = $this->getDataGenerator()->create_user();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_subscribe_to_forum($guestuser));
        $this->assertTrue($capabilitymanager->can_subscribe_to_forum($this->user));
    }

    /**
     * Test can_create_discussions.
     *
     * @covers ::can_create_discussions
     */
    public function test_can_create_discussions() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $guestuser = $this->getDataGenerator()->create_user();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_create_discussions($guestuser));

        $this->prevent_capability('mod/forum:startdiscussion');
        $this->assertFalse($capabilitymanager->can_create_discussions($user));

        $this->give_capability('mod/forum:startdiscussion');
        $this->assertTrue($capabilitymanager->can_create_discussions($user));

        $forum = $this->create_forum(['type' => 'news']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:addnews');
        $this->assertFalse($capabilitymanager->can_create_discussions($user));

        $this->give_capability('mod/forum:addnews');
        $this->assertTrue($capabilitymanager->can_create_discussions($user));

        $forum = $this->create_forum(['type' => 'qanda']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:addquestion');
        $this->assertFalse($capabilitymanager->can_create_discussions($user));

        $this->give_capability('mod/forum:addquestion');
        $this->assertTrue($capabilitymanager->can_create_discussions($user));

        // Test a forum in group mode.
        $forumrecord = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $this->course->id, 'groupmode' => SEPARATEGROUPS]
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forumrecord->id);
        $context = \context_module::instance($coursemodule->id);
        $forum = $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $context,
            $coursemodule,
            $this->course
        );

        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_create_discussions($user));

        $this->give_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_create_discussions($user));

        $this->prevent_capability('moodle/site:accessallgroups', $context);
        $this->assertFalse($capabilitymanager->can_create_discussions($user));

        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $this->assertTrue($capabilitymanager->can_create_discussions($user, $group->id));

        // Test if cut off date is reached.
        $now = time();
        $forum = $this->create_forum(['cutoffdate' => $now + 86400 , 'blockafter' => 5, 'blockperiod' => 86400]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $this->prevent_capability('mod/forum:postwithoutthrottling');
        $this->assertTrue($capabilitymanager->can_create_discussions($user));

        $forum = $this->create_forum(['cutoffdate' => $now + 86400 , 'blockafter' => 1, 'blockperiod' => 86400]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $this->prevent_capability('mod/forum:postwithoutthrottling');
        $this->assertFalse($capabilitymanager->can_create_discussions($user));
    }

    /**
     * Test can_access_all_groups.
     *
     * @covers ::can_access_all_groups
     */
    public function test_can_access_all_groups() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('moodle/site:accessallgroups');
        $this->assertFalse($capabilitymanager->can_access_all_groups($user));

        $this->give_capability('moodle/site:accessallgroups');
        $this->assertTrue($capabilitymanager->can_access_all_groups($user));
    }

    /**
     * Test can_access_group.
     *
     * @covers ::can_access_group
     */
    public function test_can_access_group() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);

        $this->prevent_capability('moodle/site:accessallgroups');
        $this->assertFalse($capabilitymanager->can_access_group($user, $group->id));

        $this->give_capability('moodle/site:accessallgroups');
        $this->assertTrue($capabilitymanager->can_access_group($user, $group->id));

        $this->prevent_capability('moodle/site:accessallgroups');
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);
        $this->assertTrue($capabilitymanager->can_access_group($user, $group->id));
    }

    /**
     * Test can_view_discussions.
     *
     * @covers ::can_view_discussions
     */
    public function test_can_view_discussions() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:viewdiscussion');
        $this->assertFalse($capabilitymanager->can_view_discussions($user));

        $this->give_capability('mod/forum:viewdiscussion');
        $this->assertTrue($capabilitymanager->can_view_discussions($user));
    }

    /**
     * Test can_move_discussions.
     *
     * @covers ::can_move_discussions
     */
    public function test_can_move_discussions() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:movediscussions');
        $this->assertFalse($capabilitymanager->can_move_discussions($user));

        $this->give_capability('mod/forum:movediscussions');
        $this->assertTrue($capabilitymanager->can_move_discussions($user));

        $forum = $this->create_forum(['type' => 'single']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_move_discussions($user));
    }

    /**
     * Test can_pin_discussions.
     *
     * @covers ::can_pin_discussions
     */
    public function test_can_pin_discussions() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:pindiscussions');
        $this->assertFalse($capabilitymanager->can_pin_discussions($user));

        $this->give_capability('mod/forum:pindiscussions');
        $this->assertTrue($capabilitymanager->can_pin_discussions($user));
    }

    /**
     * Test can_split_discussions.
     *
     * @covers ::can_split_discussions
     */
    public function test_can_split_discussions() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:splitdiscussions');
        $this->assertFalse($capabilitymanager->can_split_discussions($user));

        $this->give_capability('mod/forum:splitdiscussions');
        $this->assertTrue($capabilitymanager->can_split_discussions($user));

        $forum = $this->create_forum(['type' => 'single']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_split_discussions($user));
    }

    /**
     * Test can_export_discussions.
     *
     * @covers ::can_export_discussions
     */
    public function test_can_export_discussions() {
        global $CFG;
        $this->resetAfterTest();

        $CFG->enableportfolios = true;
        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:exportdiscussion');
        $this->assertFalse($capabilitymanager->can_export_discussions($user));

        $this->give_capability('mod/forum:exportdiscussion');
        $this->assertTrue($capabilitymanager->can_export_discussions($user));

        $CFG->enableportfolios = false;

        $this->assertFalse($capabilitymanager->can_export_discussions($user));
    }

    /**
     * Test can_manually_control_post_read_status.
     *
     * @covers ::can_manually_control_post_read_status
     */
    public function test_can_manually_control_post_read_status() {
        global $CFG, $DB;
        $this->resetAfterTest();

        $CFG->forum_usermarksread = true;
        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $cache = \cache::make('mod_forum', 'forum_is_tracked');

        $user->trackforums = true;
        $prefid = $DB->insert_record('forum_track_prefs', ['userid' => $user->id, 'forumid' => $forum->get_id()]);
        $this->assertFalse($capabilitymanager->can_manually_control_post_read_status($user));
        $cache->purge();

        $DB->delete_records('forum_track_prefs', ['id' => $prefid]);
        $this->assertTrue($capabilitymanager->can_manually_control_post_read_status($user));
        $cache->purge();

        $CFG->forum_usermarksread = false;

        $this->assertFalse($capabilitymanager->can_manually_control_post_read_status($user));
    }

    /**
     * Test must_post_before_viewing_discussion.
     *
     * @covers ::must_post_before_viewing_discussion
     */
    public function test_must_post_before_viewing_discussion() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $discussion = $this->discussion;
        $newuser = $this->getDataGenerator()->create_user();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $this->getDataGenerator()->enrol_user($newuser->id, $this->course->id, 'teacher');

        $this->assertFalse($capabilitymanager->must_post_before_viewing_discussion($newuser, $discussion));

        $forum = $this->create_forum(['type' => 'qanda']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:viewqandawithoutposting');
        $this->assertTrue($capabilitymanager->must_post_before_viewing_discussion($newuser, $discussion));

        $this->give_capability('mod/forum:viewqandawithoutposting');
        $this->assertFalse($capabilitymanager->must_post_before_viewing_discussion($newuser, $discussion));

        $this->prevent_capability('mod/forum:viewqandawithoutposting');
        // The pre-generated user has a pre-generated post in the disussion already.
        $this->assertFalse($capabilitymanager->must_post_before_viewing_discussion($user, $discussion));
    }

    /**
     * Test can_subscribe_to_discussion.
     *
     * @covers ::can_subscribe_to_discussion
     */
    public function test_can_subscribe_to_discussion() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $discussion = $this->discussion;
        $guestuser = $this->getDataGenerator()->create_user();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_subscribe_to_discussion($guestuser, $discussion));
        $this->assertTrue($capabilitymanager->can_subscribe_to_discussion($this->user, $discussion));
    }

    /**
     * Test can_move_discussion.
     *
     * @covers ::can_move_discussion
     */
    public function test_can_move_discussion() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $discussion = $this->discussion;
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:movediscussions');
        $this->assertFalse($capabilitymanager->can_move_discussion($user, $discussion));

        $this->give_capability('mod/forum:movediscussions');
        $this->assertTrue($capabilitymanager->can_move_discussion($user, $discussion));

        $forum = $this->create_forum(['type' => 'single']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_move_discussion($user, $discussion));
    }

    /**
     * Test can_pin_discussion.
     *
     * @covers ::can_pin_discussion
     */
    public function test_can_pin_discussion() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $discussion = $this->discussion;
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:pindiscussions');
        $this->assertFalse($capabilitymanager->can_pin_discussion($user, $discussion));

        $this->give_capability('mod/forum:pindiscussions');
        $this->assertTrue($capabilitymanager->can_pin_discussion($user, $discussion));
    }

    /**
     * Test can_post_in_discussion.
     *
     * @covers ::can_post_in_discussion
     */
    public function test_can_post_in_discussion() {
        $this->resetAfterTest();

        $discussion = $this->discussion;
        $user = $this->user;

        // Locked discussions.
        $lockedforum = $this->create_forum(['lockdiscussionafter' => 1]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($lockedforum);

        $this->give_capability('mod/forum:canoverridediscussionlock');
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->prevent_capability('mod/forum:canoverridediscussionlock');
        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        // News forum.
        $newsforum = $this->create_forum(['type' => 'news']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($newsforum);

        $this->give_capability('mod/forum:replynews');
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->prevent_capability('mod/forum:replynews');
        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        // General forum.
        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('mod/forum:replypost');
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->prevent_capability('mod/forum:replypost');
        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        // Forum in separate group mode.
        $forumrecord = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $this->course->id, 'groupmode' => SEPARATEGROUPS]
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forumrecord->id);
        $context = \context_module::instance($coursemodule->id);
        $forum = $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $context,
            $coursemodule,
            $this->course
        );
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->prevent_capability('moodle/site:accessallgroups', $context);
        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['groupid' => $group->id])
        );

        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        // Forum in visible group mode.
        $forumrecord = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $this->course->id, 'groupmode' => VISIBLEGROUPS]
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forumrecord->id);
        $context = \context_module::instance($coursemodule->id);
        $forum = $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $context,
            $coursemodule,
            $this->course
        );
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->prevent_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['groupid' => $group->id])
        );

        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));

        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $now = time();
        $forum = $this->create_forum(['cutoffdate' => $now + 86400 , 'blockafter' => 5, 'blockperiod' => 86400]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $this->prevent_capability('mod/forum:postwithoutthrottling');
        $this->give_capability('mod/forum:replypost');
        $this->assertTrue($capabilitymanager->can_post_in_discussion($user, $discussion));

        $forum = $this->create_forum(['cutoffdate' => $now + 86400 , 'blockafter' => 1, 'blockperiod' => 86400]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $this->prevent_capability('mod/forum:postwithoutthrottling');
        $this->assertFalse($capabilitymanager->can_post_in_discussion($user, $discussion));
    }

    /**
     * Test can_edit_post.
     *
     * @covers ::can_edit_post
     */
    public function test_can_edit_post() {
        global $CFG;

        $this->resetAfterTest();

        $forum = $this->create_forum();
        $discussion = $this->discussion;
        // The generated post is created 100 seconds in the past.
        $post = $this->post;
        $user = $this->user;
        $otheruser = $this->getDataGenerator()->create_user();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:editanypost');

        // 200 seconds to edit.
        $CFG->maxeditingtime = 200;
        $this->assertTrue($capabilitymanager->can_edit_post($user, $discussion, $post));

        // 10 seconds to edit. No longer in editing time.
        $CFG->maxeditingtime = 10;
        $this->assertFalse($capabilitymanager->can_edit_post($user, $discussion, $post));

        // Can edit outside of editing time with this capability.
        $this->give_capability('mod/forum:editanypost');
        $this->assertTrue($capabilitymanager->can_edit_post($user, $discussion, $post));

        $CFG->maxeditingtime = 200;
        $this->assertFalse($capabilitymanager->can_edit_post($otheruser, $discussion, $post));

        $this->prevent_capability('mod/forum:editanypost');

        // News forum.
        $forum = $this->create_forum(['type' => 'news']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        // Discussion hasn't started yet.
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['timestart' => time() + 100])
        );

        $this->assertFalse($capabilitymanager->can_edit_post($user, $discussion, $post));

        // Back to a discussion that has started.
        $discussion = $this->discussion;
        // Post is a reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5])
        );

        $this->assertFalse($capabilitymanager->can_edit_post($user, $discussion, $post));

        $post = $this->post;
        // Discussion has started and post isn't a reply so we can edit it.
        $this->assertTrue($capabilitymanager->can_edit_post($user, $discussion, $post));

        // Single forum.
        $forum = $this->create_forum(['type' => 'single']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        // Create a new post that definitely isn't the first post of the discussion.
        // Only the author, and a user with editanypost can edit it.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['id' => $post->get_id() + 100])
        );
        $this->give_capability('mod/forum:editanypost');
        $this->assertTrue($capabilitymanager->can_edit_post($user, $discussion, $post));
        $this->assertFalse($capabilitymanager->can_edit_post($otheruser, $discussion, $post));

        $post = $this->post;
        // Set the first post of the discussion to our post.
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['firstpost' => $post->get_id()])
        );

        $this->prevent_capability('moodle/course:manageactivities');
        $this->assertFalse($capabilitymanager->can_edit_post($user, $discussion, $post));

        $this->give_capability('moodle/course:manageactivities');
        $this->assertTrue($capabilitymanager->can_edit_post($user, $discussion, $post));
    }

    /**
     * Test can_delete_post.
     *
     * @covers ::can_delete_post
     */
    public function test_can_delete_post() {
        global $CFG;

        $this->resetAfterTest();

        // Single forum.
        $forum = $this->create_forum(['type' => 'single']);
        // The generated post is created 100 seconds in the past.
        $post = $this->post;
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        // Set the first post of the discussion to our post.
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['firstpost' => $post->get_id()])
        );

        // Can't delete the first post of a single discussion forum.
        $this->assertFalse($capabilitymanager->can_delete_post($user, $discussion, $post));

        // Set the first post of the discussion to something else.
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['firstpost' => $post->get_id() - 1])
        );

        $this->assertTrue($capabilitymanager->can_delete_post($user, $discussion, $post));

        // Back to a general forum.
        $forum = $this->create_forum();
        $this->prevent_capability('mod/forum:deleteanypost');
        $this->give_capability('mod/forum:deleteownpost');
        // 200 second editing time to make sure our post is still within it.
        $CFG->maxeditingtime = 200;

        // Make the post owned by someone else.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['userid' => $user->id - 1])
        );

        // Can't delete someone else's post.
        $this->assertFalse($capabilitymanager->can_delete_post($user, $discussion, $post));
        // Back to our post.
        $post = $this->post;

        // Not in editing time.
        $CFG->maxeditingtime = 10;
        $this->assertFalse($capabilitymanager->can_delete_post($user, $discussion, $post));

        $CFG->maxeditingtime = 200;
        // Remove the capability to delete own post.
        $this->prevent_capability('mod/forum:deleteownpost');
        $this->assertFalse($capabilitymanager->can_delete_post($user, $discussion, $post));

        $this->give_capability('mod/forum:deleteownpost');
        $this->assertTrue($capabilitymanager->can_delete_post($user, $discussion, $post));

        $this->give_capability('mod/forum:deleteanypost');
        $CFG->maxeditingtime = 10;
        $this->assertTrue($capabilitymanager->can_delete_post($user, $discussion, $post));
    }

    /**
     * Test can_split_post.
     *
     * @covers ::can_split_post
     */
    public function test_can_split_post() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $discussion = $this->discussion;
        $post = $this->post;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        // Make the post a reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5])
        );

        $this->prevent_capability('mod/forum:splitdiscussions');
        $this->assertFalse($capabilitymanager->can_split_post($user, $discussion, $post));

        $this->give_capability('mod/forum:splitdiscussions');
        $this->assertTrue($capabilitymanager->can_split_post($user, $discussion, $post));

        // Make the post have no parent.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 0])
        );

        $this->assertFalse($capabilitymanager->can_split_post($user, $discussion, $post));

        $forum = $this->create_forum(['type' => 'single']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        // Make the post a reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5])
        );

        // Can't split a single discussion forum.
        $this->assertFalse($capabilitymanager->can_split_post($user, $discussion, $post));

        // Make the post a private reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5, 'privatereplyto' => $user->id])
        );

        // Can't split at a private reply.
        $this->assertFalse($capabilitymanager->can_split_post($user, $discussion, $post));
    }

    /**
     * Test can_reply_to_post.
     *
     * @covers ::can_reply_to_post
     */
    public function test_can_reply_to_post() {
        $this->resetAfterTest();

        $discussion = $this->discussion;
        $user = $this->user;
        $post = $this->post;

        // Locked discussions.
        $lockedforum = $this->create_forum(['lockdiscussionafter' => 1]);
        $capabilitymanager = $this->managerfactory->get_capability_manager($lockedforum);

        $this->give_capability('mod/forum:canoverridediscussionlock');
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->prevent_capability('mod/forum:canoverridediscussionlock');
        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        // News forum.
        $newsforum = $this->create_forum(['type' => 'news']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($newsforum);

        $this->give_capability('mod/forum:replynews');
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->prevent_capability('mod/forum:replynews');
        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        // General forum.
        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('mod/forum:replypost');
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->prevent_capability('mod/forum:replypost');
        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        // Forum in separate group mode.
        $forumrecord = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $this->course->id, 'groupmode' => SEPARATEGROUPS]
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forumrecord->id);
        $context = \context_module::instance($coursemodule->id);
        $forum = $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $context,
            $coursemodule,
            $this->course
        );
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->prevent_capability('moodle/site:accessallgroups', $context);
        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['groupid' => $group->id])
        );

        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        // Forum in visible group mode.
        $forumrecord = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $this->course->id, 'groupmode' => VISIBLEGROUPS]
        );
        $coursemodule = get_coursemodule_from_instance('forum', $forumrecord->id);
        $context = \context_module::instance($coursemodule->id);
        $forum = $this->entityfactory->get_forum_from_stdClass(
            $forumrecord,
            $context,
            $coursemodule,
            $this->course
        );
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->prevent_capability('moodle/site:accessallgroups', $context);
        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $group = $this->getDataGenerator()->create_group(['courseid' => $this->course->id]);
        $discussion = $this->entityfactory->get_discussion_from_stdClass(
            (object) array_merge((array) $this->discussionrecord, ['groupid' => $group->id])
        );

        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $this->assertTrue($capabilitymanager->can_reply_to_post($user, $discussion, $post));

        // Make the post a private reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5, 'privatereplyto' => $user->id])
        );

        // Can't reply to a a private reply.
        $this->assertFalse($capabilitymanager->can_reply_to_post($user, $discussion, $post));
    }

    /**
     * Test for \mod_forum\local\managers\capability::can_reply_to_post() involving Q & A forums.
     */
    public function test_can_reply_to_post_in_qanda_forum() {
        global $CFG;

        $this->resetAfterTest();

        // Set max editing time to 10 seconds.
        $CFG->maxeditingtime = 10;

        $qandaforum = $this->create_forum(['type' => 'qanda']);
        $datagenerator = $this->getDataGenerator();
        $capabilitymanager = $this->managerfactory->get_capability_manager($qandaforum);

        // Student 1.
        $student1 = $datagenerator->create_user(['firstname' => 'S1']);
        $datagenerator->enrol_user($student1->id, $this->course->id, 'student');
        // Confirm Student 1 can reply to the question.
        $this->assertTrue($capabilitymanager->can_reply_to_post($student1, $this->discussion, $this->post));

        // Student 2.
        $student2 = $datagenerator->create_user(['firstname' => 'S2']);
        $datagenerator->enrol_user($student2->id, $this->course->id, 'student');
        // Confirm Student 2 can reply to the question.
        $this->assertTrue($capabilitymanager->can_reply_to_post($student2, $this->discussion, $this->post));

        // Reply to the question as student 1.
        $now = time();
        $options = ['parent' => $this->post->get_id(), 'created' => $now - 100];
        $student1post = $this->helper_post_to_discussion($this->forumrecord, $this->discussionrecord, $student1, $options);
        $student1postentity = $this->entityfactory->get_post_from_stdClass($student1post);

        // Confirm Student 2 cannot reply student 1's answer yet.
        $this->assertFalse($capabilitymanager->can_reply_to_post($student2, $this->discussion, $student1postentity));

        // Reply to the question as student 2.
        $this->helper_post_to_discussion($this->forumrecord, $this->discussionrecord, $student2, $options);

        // Reinitialise capability manager first to ensure we don't return cached values.
        $capabilitymanager = $this->managerfactory->get_capability_manager($qandaforum);

        // Confirm Student 2 can now reply to student 1's answer.
        $this->assertTrue($capabilitymanager->can_reply_to_post($student2, $this->discussion, $student1postentity));
    }

    /**
     * Ensure that can_reply_privately_to_post works as expected.
     *
     * @covers ::can_reply_privately_to_post
     */
    public function test_can_reply_privately_to_post() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $discussion = $this->discussion;
        $user = $this->user;
        $post = $this->post;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        // Without the capability, and with a standard post, it is not possible to reply privately.
        $this->prevent_capability('mod/forum:postprivatereply');
        $this->assertFalse($capabilitymanager->can_reply_privately_to_post($this->user, $post));

        // With the capability, and a standard post, it is possible to reply privately.
        $this->give_capability('mod/forum:postprivatereply');
        $this->assertTrue($capabilitymanager->can_reply_privately_to_post($this->user, $post));

        // Make the post a private reply.
        $post = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, ['parent' => 5, 'privatereplyto' => $user->id])
        );

        // Can't ever reply to a a private reply.
        $this->assertFalse($capabilitymanager->can_reply_privately_to_post($user, $post));
    }

    /**
     * Ensure that can_view_post works as expected.
     *
     * @covers ::can_view_post
     */
    public function test_can_view_post() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $user = $this->user;
        $otheruser = $this->getDataGenerator()->create_user();

        $discussion = $this->discussion;
        $post = $this->post;

        $postproperties = ['parent' => $post->get_id(), 'userid' => $otheruser->id, 'privatereplyto' => $otheruser->id];
        $privatepost = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, $postproperties)
        );

        $this->prevent_capability('mod/forum:readprivatereplies');
        $this->assertFalse($capabilitymanager->can_view_post($user, $discussion, $privatepost));
    }

    /**
     * Ensure that can_view_post_shell considers private replies correctly.
     *
     * @covers ::can_view_post_shell
     */
    public function test_can_view_post_shell() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $user = $this->user;
        $otheruser = $this->getDataGenerator()->create_user();

        $discussion = $this->discussion;
        $post = $this->post;

        $postproperties = ['parent' => $post->get_id(), 'userid' => $user->id, 'privatereplyto' => $user->id];
        $privatepostfrommetome = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, $postproperties)
        );

        $postproperties = ['parent' => $post->get_id(), 'userid' => $user->id, 'privatereplyto' => $otheruser->id];
        $privatepostfrommetoother = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, $postproperties)
        );

        $postproperties = ['parent' => $post->get_id(), 'userid' => $otheruser->id, 'privatereplyto' => $user->id];
        $privatepostfromothertome = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, $postproperties)
        );

        $postproperties = ['parent' => $post->get_id(), 'userid' => $otheruser->id, 'privatereplyto' => $otheruser->id];
        $privatepostfromothertoother = $this->entityfactory->get_post_from_stdClass(
            (object) array_merge((array) $this->postrecord, $postproperties)
        );

        // Can always view public replies, and private replies by me or to me.
        $this->prevent_capability('mod/forum:readprivatereplies');
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $post));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfrommetome));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfrommetoother));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfromothertome));
        $this->assertFalse($capabilitymanager->can_view_post_shell($this->user, $privatepostfromothertoother));

        $this->give_capability('mod/forum:readprivatereplies');
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $post));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfrommetome));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfrommetoother));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfromothertome));
        $this->assertTrue($capabilitymanager->can_view_post_shell($this->user, $privatepostfromothertoother));
    }

    /**
     * Test can_export_post.
     *
     * @covers ::can_export_post
     */
    public function test_can_export_post() {
        global $CFG;
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $post = $this->post;
        $user = $this->user;
        $otheruser = $this->getDataGenerator()->create_user();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->getDataGenerator()->enrol_user($otheruser->id, $this->course->id, 'teacher');

        $CFG->enableportfolios = true;
        $this->give_capability('mod/forum:exportpost');

        $this->assertTrue($capabilitymanager->can_export_post($otheruser, $post));

        $CFG->enableportfolios = false;
        $this->assertFalse($capabilitymanager->can_export_post($otheruser, $post));

        $CFG->enableportfolios = true;
        $this->prevent_capability('mod/forum:exportpost');
        // Can't export another user's post without the exportpost capavility.
        $this->assertFalse($capabilitymanager->can_export_post($otheruser, $post));

        $this->give_capability('mod/forum:exportownpost');
        // Can export own post with the exportownpost capability.
        $this->assertTrue($capabilitymanager->can_export_post($user, $post));

        $this->prevent_capability('mod/forum:exportownpost');
        $this->assertFalse($capabilitymanager->can_export_post($user, $post));
    }

    /**
     * Test can_view_participants.
     *
     * @covers ::can_view_participants
     */
    public function test_can_view_participants() {
        $this->resetAfterTest();

        $discussion = $this->discussion;
        $user = $this->user;
        $otheruser = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($otheruser->id, $this->course->id, 'teacher');

        $this->prevent_capability('moodle/course:viewparticipants');
        $this->prevent_capability('moodle/course:enrolreview');
        $this->prevent_capability('mod/forum:viewqandawithoutposting');

        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->assertFalse($capabilitymanager->can_view_participants($otheruser, $discussion));

        $this->give_capability('moodle/course:viewparticipants');
        $this->assertTrue($capabilitymanager->can_view_participants($otheruser, $discussion));

        $this->prevent_capability('moodle/course:viewparticipants');
        $this->give_capability('moodle/course:enrolreview');
        $this->assertTrue($capabilitymanager->can_view_participants($otheruser, $discussion));

        $forum = $this->create_forum(['type' => 'qanda']);
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        // Q and A forum requires the user to post before they can view it.
        $this->prevent_capability('mod/forum:viewqandawithoutposting');
        $this->assertFalse($capabilitymanager->can_view_participants($otheruser, $discussion));

        // This user has posted.
        $this->assertTrue($capabilitymanager->can_view_participants($user, $discussion));
    }

    /**
     * Test can_view_hidden_posts.
     *
     * @covers ::can_view_hidden_posts
     */
    public function test_can_view_hidden_posts() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('mod/forum:viewhiddentimedposts');
        $this->assertFalse($capabilitymanager->can_view_hidden_posts($user));

        $this->give_capability('mod/forum:viewhiddentimedposts');
        $this->assertTrue($capabilitymanager->can_view_hidden_posts($user));
    }

    /**
     * Test can_manage_forum.
     *
     * @covers ::can_manage_forum
     */
    public function test_can_manage_forum() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->prevent_capability('moodle/course:manageactivities');
        $this->assertFalse($capabilitymanager->can_manage_forum($user));

        $this->give_capability('moodle/course:manageactivities');
        $this->assertTrue($capabilitymanager->can_manage_forum($user));
    }

    /**
     * Test can_manage_tags.
     *
     * @covers ::can_manage_tags
     */
    public function test_can_manage_tags() {
        global $DB;
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $user = $this->user;
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);
        $context = \context_system::instance();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'user'], MUST_EXIST);

        assign_capability('moodle/tag:manage', CAP_PREVENT, $roleid, $context->id, true);
        $this->assertFalse($capabilitymanager->can_manage_tags($user));

        assign_capability('moodle/tag:manage', CAP_ALLOW, $roleid, $context->id, true);
        $this->assertTrue($capabilitymanager->can_manage_tags($user));
    }

    /**
     * Ensure that the can_view_any_private_reply works as expected.
     *
     * @covers ::can_view_any_private_reply
     */
    public function test_can_view_any_private_reply() {
        $this->resetAfterTest();

        $forum = $this->create_forum();
        $capabilitymanager = $this->managerfactory->get_capability_manager($forum);

        $this->give_capability('mod/forum:readprivatereplies');
        $this->assertTrue($capabilitymanager->can_view_any_private_reply($this->user));
        $this->prevent_capability('mod/forum:readprivatereplies');
        $this->assertFalse($capabilitymanager->can_view_any_private_reply($this->user));
    }


    /**
     * Test delete a post with ratings.
     */
    public function test_validate_delete_post_with_ratings() {
        global $DB;
        $this->resetAfterTest(true);

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->created =
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        // Add rating.
        $post = $DB->get_record('forum_posts', array('discussion' => $discussion->id));
        $post->totalscore = 80;
        $DB->update_record('forum_posts', $post);

        $vaultfactory = container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $postvault = $vaultfactory->get_post_vault();

        $postentity = $postvault->get_from_id($post->id);
        $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
        $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
        $capabilitymanager = $this->managerfactory->get_capability_manager($forumentity);

        $this->setUser($user);
        $this->expectExceptionMessage(get_string('couldnotdeleteratings', 'rating'));
        $capabilitymanager->validate_delete_post($user, $discussionentity, $postentity, false);
    }

    /**
     * Test delete a post with replies.
     */
    public function test_validate_delete_post_with_replies() {
        global $DB;
        $this->resetAfterTest(true);

        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $user = $this->getDataGenerator()->create_user();
        $role = $DB->get_record('role', array('shortname' => 'student'), '*', MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        // Add a discussion.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->created =
        $discussion = $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_discussion($record);

        $parentpost = $DB->get_record('forum_posts', array('discussion' => $discussion->id));
        // Add a post.
        $record = new \stdClass();
        $record->course = $course->id;
        $record->userid = $user->id;
        $record->forum = $forum->id;
        $record->discussion = $discussion->id;
        $record->parent = $parentpost->id;
        $this->getDataGenerator()->get_plugin_generator('mod_forum')->create_post($record);

        $vaultfactory = container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $postvault = $vaultfactory->get_post_vault();

        $postentity = $postvault->get_from_id($parentpost->id);
        $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
        $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
        $capabilitymanager = $this->managerfactory->get_capability_manager($forumentity);

        $this->setUser($user);
        // Get reply count.
        $replycount = $postvault->get_reply_count_for_post_id_in_discussion_id(
            $user, $postentity->get_id(), $discussionentity->get_id(), true);
        $this->expectExceptionMessage(get_string('couldnotdeletereplies', 'forum'));
        $capabilitymanager->validate_delete_post($user, $discussionentity, $postentity, $replycount);
    }

}
