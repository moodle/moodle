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
 * The post vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/generator_trait.php');

/**
 * The post vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_forum\local\vaults\post
 */
class mod_forum_vaults_post_testcase extends advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var \mod_forum\local\vaults\post */
    private $vault;

    /**
     * Set up function for tests.
     */
    public function setUp() {
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $this->vault = $vaultfactory->get_post_vault();
    }

    /**
     * Teardown for all tests.
     */
    public function tearDown() {
        unset($this->vault);
    }

    /**
     * Test get_from_id.
     */
    public function test_get_from_id() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion, $post] = $this->helper_post_to_forum($forum, $user);

        $postentity = $this->vault->get_from_id($post->id);

        $this->assertEquals($post->id, $postentity->get_id());
    }

    /**
     * Test get_from_discussion_id.
     *
     * @covers ::get_from_discussion_id
     * @covers ::<!public>
     */
    public function test_get_from_discussion_id() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        [$discussion2, $post4] = $this->helper_post_to_forum($forum, $user);

        $entities = array_values($this->vault->get_from_discussion_id($discussion1->id));

        $this->assertCount(3, $entities);
        $this->assertEquals($post1->id, $entities[0]->get_id());
        $this->assertEquals($post2->id, $entities[1]->get_id());
        $this->assertEquals($post3->id, $entities[2]->get_id());

        $entities = array_values($this->vault->get_from_discussion_id($discussion1->id + 1000));
        $this->assertCount(0, $entities);
    }

    /**
     * Test get_from_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_from_discussion_ids
     */
    public function test_get_from_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_from_discussion_ids([]));
    }

    /**
     * Test get_from_discussion_ids.
     *
     * @covers ::get_from_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_from_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        [$discussion2, $post4] = $this->helper_post_to_forum($forum, $user);

        $entities = array_values($this->vault->get_from_discussion_ids([$discussion1->id]));
        usort($entities, function($a, $b) {
            return $a <=> $b;
        });
        $this->assertCount(3, $entities);
        $this->assertEquals($post1->id, $entities[0]->get_id());
        $this->assertEquals($post2->id, $entities[1]->get_id());
        $this->assertEquals($post3->id, $entities[2]->get_id());

        $entities = array_values($this->vault->get_from_discussion_ids([$discussion1->id, $discussion2->id]));
        usort($entities, function($a, $b) {
            return $a <=> $b;
        });
        $this->assertCount(4, $entities);
        $this->assertEquals($post1->id, $entities[0]->get_id());
        $this->assertEquals($post2->id, $entities[1]->get_id());
        $this->assertEquals($post3->id, $entities[2]->get_id());
        $this->assertEquals($post4->id, $entities[3]->get_id());
    }

    /**
     * Test get_replies_to_post.
     *
     * @covers ::get_replies_to_post
     * @covers ::<!public>
     */
    public function test_get_replies_to_post() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $forumgenerator = $datagenerator->get_plugin_generator('mod_forum');
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        // Create a post with the same created time as the parent post to ensure
        // we've covered every possible scenario.
        $post2 = $forumgenerator->create_post((object) [
            'discussion' => $post1->discussion,
            'parent' => $post1->id,
            'userid' => $user->id,
            'mailnow' => 1,
            'subject' => 'Some subject',
            'created' => $post1->created
        ]);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);

        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $post1 = $entityfactory->get_post_from_stdclass($post1);
        $post2 = $entityfactory->get_post_from_stdclass($post2);
        $post3 = $entityfactory->get_post_from_stdclass($post3);
        $post4 = $entityfactory->get_post_from_stdclass($post4);

        $entities = $this->vault->get_replies_to_post($post1);
        $this->assertCount(3, $entities);
        $this->assertEquals($post2->get_id(), $entities[0]->get_id());
        $this->assertEquals($post3->get_id(), $entities[1]->get_id());
        $this->assertEquals($post4->get_id(), $entities[2]->get_id());

        $entities = $this->vault->get_replies_to_post($post2);
        $this->assertCount(1, $entities);
        $this->assertEquals($post4->get_id(), $entities[0]->get_id());

        $entities = $this->vault->get_replies_to_post($post3);
        $this->assertCount(0, $entities);
    }

    /**
     * Test get_reply_count_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_reply_count_for_discussion_ids
     */
    public function test_get_reply_count_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids([]);
        $this->assertCount(0, $counts);
    }

    /**
     * Test get_reply_count_for_discussion_ids.
     *
     * @covers ::get_reply_count_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_reply_count_for_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);

        $counts = $this->vault->get_reply_count_for_discussion_ids([$discussion1->id]);
        $this->assertCount(1, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids([$discussion1->id, $discussion2->id]);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids([$discussion1->id, $discussion2->id, $discussion3->id]);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);

        $counts = $this->vault->get_reply_count_for_discussion_ids([
            $discussion1->id,
            $discussion2->id,
            $discussion3->id,
            $discussion3->id + 1000
        ]);
        $this->assertCount(2, $counts);
        $this->assertEquals(3, $counts[$discussion1->id]);
        $this->assertEquals(1, $counts[$discussion2->id]);
    }

    /**
     * Test get_unread_count_for_discussion_ids.
     *
     * @covers ::get_unread_count_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_unread_count_for_discussion_ids() {
        global $CFG;
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $otheruser = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);

        $modgenerator = $this->getDataGenerator()->get_plugin_generator('mod_forum');
        $post7 = $modgenerator->create_post((object) [
            'discussion' => $post5->discussion,
            'parent' => $post5->id,
            'userid' => $user->id,
            'mailnow' => 1,
            'subject' => 'old post',
            // Two days ago which makes it an "old post".
            'modified' => time() - 172800
        ]);

        forum_tp_add_read_record($user->id, $post1->id);
        forum_tp_add_read_record($user->id, $post4->id);
        $CFG->forum_oldpostdays = 1;

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [$discussion1->id]);
        $this->assertCount(1, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [$discussion1->id, $discussion2->id]);
        $this->assertCount(2, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($user, [
            $discussion1->id,
            $discussion2->id,
            $discussion2->id + 1000
        ]);
        $this->assertCount(2, $counts);
        $this->assertEquals(2, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);

        $counts = $this->vault->get_unread_count_for_discussion_ids($otheruser, [$discussion1->id, $discussion2->id]);
        $this->assertCount(2, $counts);
        $this->assertEquals(4, $counts[$discussion1->id]);
        $this->assertEquals(2, $counts[$discussion2->id]);
    }

    /**
     * Test get_unread_count_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_unread_count_for_discussion_ids
     */
    public function test_get_unread_count_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_unread_count_for_discussion_ids($user, [], false));
    }

    /**
     * Test get_latest_post_id_for_discussion_ids.
     *
     * @covers ::get_latest_post_id_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_latest_post_id_for_discussion_ids() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $post4 = $this->helper_reply_to_post($post2, $user);
        [$discussion2, $post5] = $this->helper_post_to_forum($forum, $user);
        $post6 = $this->helper_reply_to_post($post5, $user);
        [$discussion3, $post7] = $this->helper_post_to_forum($forum, $user);

        $ids = $this->vault->get_latest_post_id_for_discussion_ids([$discussion1->id]);
        $this->assertCount(1, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]);

        $ids = $this->vault->get_latest_post_id_for_discussion_ids([$discussion1->id, $discussion2->id]);
        $this->assertCount(2, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]);
        $this->assertEquals($post6->id, $ids[$discussion2->id]);

        $ids = $this->vault->get_latest_post_id_for_discussion_ids([$discussion1->id, $discussion2->id, $discussion3->id]);
        $this->assertCount(3, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]);
        $this->assertEquals($post6->id, $ids[$discussion2->id]);
        $this->assertEquals($post7->id, $ids[$discussion3->id]);

        $ids = $this->vault->get_latest_post_id_for_discussion_ids([
            $discussion1->id,
            $discussion2->id,
            $discussion3->id,
            $discussion3->id + 1000
        ]);
        $this->assertCount(3, $ids);
        $this->assertEquals($post4->id, $ids[$discussion1->id]);
        $this->assertEquals($post6->id, $ids[$discussion2->id]);
        $this->assertEquals($post7->id, $ids[$discussion3->id]);
    }

    /**
     * Test get_latest_post_id_for_discussion_ids when no discussion ids were provided.
     *
     * @covers ::get_latest_post_id_for_discussion_ids
     * @covers ::<!public>
     */
    public function test_get_latest_post_id_for_discussion_ids_empty() {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);

        $this->assertEquals([], $this->vault->get_latest_post_id_for_discussion_ids([]));
    }
}
