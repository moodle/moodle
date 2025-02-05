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

use mod_forum_external;
use mod_forum_tests_generator_trait;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/forum/externallib.php');
require_once(__DIR__ . '/generator_trait.php');

/**
 * The discussion_list vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class vaults_discussion_list_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var \mod_forum\local\vaults\discussion_list */
    private $vault;

    /**
     * Set up function for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $this->vault = $vaultfactory->get_discussions_in_forum_vault();
    }

    /**
     * Test get_from_id.
     */
    public function test_get_from_id(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $vault = $this->vault;
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion, $post] = $this->helper_post_to_forum($forum, $user);

        $discussionlist = $vault->get_from_id($discussion->id);

        $this->assertEquals($discussion->id, $discussionlist->get_discussion()->get_id());
        $this->assertEquals($post->id, $discussionlist->get_first_post()->get_id());
        $this->assertEquals($user->id, $discussionlist->get_first_post_author()->get_id());
        $this->assertEquals($user->id, $discussionlist->get_latest_post_author()->get_id());
    }

    /**
     * Test get_from_forum_id.
     */
    public function test_get_from_forum_id(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $vault = $this->vault;
        $user = $datagenerator->create_user();
        self::setUser($user);
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->getDataGenerator()->enrol_user($user->id, $course->id, null, 'manual');

        $this->assertEquals([], $vault->get_from_forum_id($forum->id, true, $user->id, null,
            0, 0));

        $now = time();
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 10, 'timemodified' => 1]);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 9, 'timemodified' => 2]);
        [$hiddendiscussion, $post3] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now + 10, 'timemodified' => 3]);
        [$discussion3, $post4] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 8, 'timemodified' => 4]);

        // By default orders the discussions by last post.
        $summaries = array_values($vault->get_from_forum_id($forum->id, false, null, null,
            0, 0));
        $this->assertCount(3, $summaries);
        $this->assertEquals($discussion3->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[2]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null, null,
            0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, false, $user->id, null,
            0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null, null,
            1, 0));
        $this->assertCount(1, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null, null,
            1, 1));
        $this->assertCount(1, $summaries);
        $this->assertEquals($discussion3->id, $summaries[0]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null, null,
            1, 2));
        $this->assertCount(1, $summaries);
        $this->assertEquals($discussion2->id, $summaries[0]->get_discussion()->get_id());

        // Create 2 replies for $post1.
        $this->helper_reply_to_post($post1, $user);
        $this->helper_reply_to_post($post1, $user);
        // Create 3 replies for $post2.
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        // Create 1 reply for $post3.
        $this->helper_reply_to_post($post3, $user);

        // Sort discussions by last post DESC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_LASTPOST_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions by last post ASC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_LASTPOST_ASC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion3->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions by replies DESC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_REPLIES_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion2->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions by replies ASC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_REPLIES_ASC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion3->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions by discussion created DESC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_CREATED_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion3->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions by discussion created ASC.
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_CREATED_ASC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions when there is a pinned discussion.
        $this->pin_discussion($discussion1);
        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_LASTPOST_ASC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_LASTPOST_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, null,
            $vault::SORTORDER_REPLIES_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[3]->get_discussion()->get_id());

        // Sort discussions where there is a pinned discussion and several starred discussions.
        $this->star_discussion($discussion3, 1);
        $this->star_discussion($hiddendiscussion, 1);

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, $user->id,
            $vault::SORTORDER_REPLIES_DESC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id($forum->id, true, $user->id,
            $vault::SORTORDER_REPLIES_ASC, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion3->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[3]->get_discussion()->get_id());
    }

    /**
     * Test get_from_forum_id_and_group_id.
     */
    public function test_get_from_forum_id_and_group_id(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $vault = $this->vault;
        $user = $datagenerator->create_user();
        self::setUser($user);
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, null, 'manual');

        $this->assertEquals([], $vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true, $user->id,
            null, 0, 0));

        $now = time();
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 10, 'timemodified' => $now + 1]);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 9, 'timemodified' => $now + 2]);
        [$hiddendiscussion, $post3] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now + 10, 'timemodified' => $now + 3]
        );
        [$groupdiscussion1, $post4] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now - 8, 'timemodified' => $now + 4, 'groupid' => 1]
        );
        [$groupdiscussion2, $post5] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now - 7, 'timemodified' => $now + 5, 'groupid' => 2]
        );
        [$hiddengroupdiscussion, $post6] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now + 11, 'timemodified' => $now + 6, 'groupid' => 3]
        );

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            null, null, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[5]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], false,
            $user->id, null, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[5]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            null, null, 1, 0));
        $this->assertCount(1, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            null, null, 1, 1));
        $this->assertCount(1, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            null, null, 1, 2));
        $this->assertCount(1, $summaries);
        $this->assertEquals($groupdiscussion2->id, $summaries[0]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], false,
            null, null, 0, 0));
        $this->assertCount(4, $summaries);
        $this->assertEquals($groupdiscussion2->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[3]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [], true,
            null, null, 0, 0));
        $this->assertCount(3, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[2]->get_discussion()->get_id());

        // Add 4 replies to $post1.
        $this->helper_reply_to_post($post1, $user);
        $this->helper_reply_to_post($post1, $user);
        $this->helper_reply_to_post($post1, $user);
        $this->helper_reply_to_post($post1, $user);
        // Add 5 replies to $post2.
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        $this->helper_reply_to_post($post2, $user);
        // Add 3 replies to $post3.
        $this->helper_reply_to_post($post3, $user);
        $this->helper_reply_to_post($post3, $user);
        $this->helper_reply_to_post($post3, $user);
        // Add 2 replies to $post4.
        $this->helper_reply_to_post($post4, $user);
        $this->helper_reply_to_post($post4, $user);
        // Add 1 reply to $post5.
        $this->helper_reply_to_post($post5, $user);

        // Sort discussions by last post DESC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_LASTPOST_DESC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions by last post ASC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_LASTPOST_ASC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions by replies DESC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_REPLIES_DESC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($discussion2->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions by replies ASC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_REPLIES_ASC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions by discussion created DESC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_CREATED_DESC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions by discussion created ASC.
        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true,
            $user->id, $vault::SORTORDER_CREATED_ASC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[5]->get_discussion()->get_id());

        // Sort discussions when there is a pinned discussion.
        $this->pin_discussion($discussion1);
        $this->pin_discussion($hiddendiscussion);

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true, null,
            $vault::SORTORDER_LASTPOST_DESC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($hiddendiscussion->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($discussion1->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[5]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true, null,
            $vault::SORTORDER_LASTPOST_ASC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[5]->get_discussion()->get_id());

        $summaries = array_values($vault->get_from_forum_id_and_group_id($forum->id, [1, 2, 3], true, null,
            $vault::SORTORDER_REPLIES_DESC, 0, 0));
        $this->assertCount(6, $summaries);
        $this->assertEquals($discussion1->id, $summaries[0]->get_discussion()->get_id());
        $this->assertEquals($hiddendiscussion->id, $summaries[1]->get_discussion()->get_id());
        $this->assertEquals($discussion2->id, $summaries[2]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion1->id, $summaries[3]->get_discussion()->get_id());
        $this->assertEquals($groupdiscussion2->id, $summaries[4]->get_discussion()->get_id());
        $this->assertEquals($hiddengroupdiscussion->id, $summaries[5]->get_discussion()->get_id());
    }

    /**
     * Test get_total_discussion_count_from_forum_id.
     */
    public function test_get_total_discussion_count_from_forum_id(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $vault = $this->vault;
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals(0, $vault->get_total_discussion_count_from_forum_id($forum->id, true,
            null));

        $now = time();
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 10, 'timemodified' => 1]);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 9, 'timemodified' => 2]);
        [$hiddendiscussion, $post3] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now + 10, 'timemodified' => 3]);

        $this->assertEquals(2, $vault->get_total_discussion_count_from_forum_id($forum->id, false,
            null));
        $this->assertEquals(3, $vault->get_total_discussion_count_from_forum_id($forum->id, true,
            null));
        $this->assertEquals(3, $vault->get_total_discussion_count_from_forum_id($forum->id, false,
            $user->id));
    }

    /**
     * Test get_total_discussion_count_from_forum_id_and_group_id.
     */
    public function test_get_total_discussion_count_from_forum_id_and_group_id(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $vault = $this->vault;
        $user = $datagenerator->create_user();
        self::setUser($user);
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $vault->get_from_forum_id($forum->id, true, null,
            null, 0, 0));

        $now = time();
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 10, 'timemodified' => 1]);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now - 9, 'timemodified' => 2]);
        [$hiddendiscussion, $post3] = $this->helper_post_to_forum($forum, $user, ['timestart' => $now + 10, 'timemodified' => 3]);
        [$groupdiscussion1, $post4] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now - 8, 'timemodified' => 4, 'groupid' => 1]
        );
        [$groupdiscussion2, $post5] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now - 7, 'timemodified' => 5, 'groupid' => 2]
        );
        [$hiddengroupdiscussion, $post6] = $this->helper_post_to_forum(
            $forum,
            $user,
            ['timestart' => $now + 11, 'timemodified' => 6, 'groupid' => 3]
        );

        $this->assertEquals(6, $vault->get_total_discussion_count_from_forum_id_and_group_id($forum->id, [1, 2, 3],
            true, null));
        $this->assertEquals(6, $vault->get_total_discussion_count_from_forum_id_and_group_id(
            $forum->id,
            [1, 2, 3],
            false,
            $user->id
        ));
        $this->assertEquals(4, $vault->get_total_discussion_count_from_forum_id_and_group_id($forum->id, [1, 2, 3],
            false, null));
        $this->assertEquals(3, $vault->get_total_discussion_count_from_forum_id_and_group_id($forum->id, [],
            true, null));
    }

    /**
     * Pin a duscussion.
     *
     * @param \stdClass $discussion
     */
    private function pin_discussion(\stdClass $discussion) {
        global $DB;

        $DB->update_record('forum_discussions',
            (object) array('id' => $discussion->id, 'pinned' => FORUM_DISCUSSION_PINNED));
    }

    /**
     * Star a duscussion.
     *
     * @param \stdClass $discussion
     * @param bool     $targetstate The new starred state of the discussion (0 => unstar, 1 => star)
     */
    private function star_discussion(\stdClass $discussion, bool $targetstate) {
        mod_forum_external::toggle_favourite_state($discussion->id, $targetstate);
    }
}
