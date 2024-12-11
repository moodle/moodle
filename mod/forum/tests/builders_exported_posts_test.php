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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/generator_trait.php');

/**
 * The exported_posts builder tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class builders_exported_posts_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var \mod_forum\local\builders\exported_posts */
    private $builder;

    /**
     * Set up function for tests.
     */
    public function setUp(): void {
        parent::setUp();
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();

        $builderfactory = \mod_forum\local\container::get_builder_factory();
        $this->builder = $builderfactory->get_exported_posts_builder();
    }

    /**
     * Tear down function for tests.
     */
    public function tearDown(): void {
        // We must clear the subscription caches. This has to be done both before each test, and after in case of other
        // tests using these functions.
        \mod_forum\subscriptions::reset_forum_cache();
        parent::tearDown();
    }

    /**
     * Convert the stdClass values into their proper entity classes.
     *
     * @param stdClass[] $forums List of forums
     * @param stdClass[] $discussions List of discussions
     * @param stdClass[] $posts List of posts
     * @return array
     */
    private function convert_to_entities(array $forums, array $discussions, array $posts) {
        global $DB;
        $entityfactory = \mod_forum\local\container::get_entity_factory();

        return [
            // Forums.
            array_map(function($forum) use ($entityfactory, $DB) {
                $course = $DB->get_record('course', ['id' => $forum->course]);
                $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
                $context = \context_module::instance($coursemodule->id);
                return $entityfactory->get_forum_from_stdClass($forum, $context, $coursemodule, $course);
            }, $forums),
            // Discussions.
            array_map(function($discussion) use ($entityfactory) {
                return $entityfactory->get_discussion_from_stdClass($discussion);
            }, $discussions),
            // Posts.
            array_map(function($post) use ($entityfactory) {
                return $entityfactory->get_post_from_stdClass($post);
            }, $posts)
        ];
    }

    /**
     * Test the build function throws exception if not given all of the forums for
     * the list of posts.
     */
    public function test_build_throws_exception_on_missing_forums(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum2, $user);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2],
            [$post1, $post2]
        );

        $this->expectException('moodle_exception');
        $this->builder->build($user, [$forums[0]], $discussions, $posts);
    }

    /**
     * Test the build function throws exception if not given all of the discussions for
     * the list of posts.
     */
    public function test_build_throws_exception_on_missing_discussions(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum2, $user);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2],
            [$post1, $post2]
        );

        $this->expectException('moodle_exception');
        $this->builder->build($user, $forums, [$discussions[0]], $posts);
    }

    /**
     * Test the build function returns the exported posts in the order that the posts are
     * given.
     */
    public function test_build_returns_posts_in_order(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        $user1 = $datagenerator->create_and_enrol($course);
        $user2 = $datagenerator->create_and_enrol($course);

        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        $post7 = $this->helper_reply_to_post($post2, $user1);
        $post8 = $this->helper_reply_to_post($post2, $user2);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6, $post7, $post8]
        );

        // Randomly order the posts.
        shuffle($posts);

        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        $expectedpostids = array_map(function($post) {
            return $post->get_id();
        }, $posts);
        $actualpostids = array_map(function($exportedpost) {
            return (int) $exportedpost->id;
        }, $exportedposts);

        $this->assertEquals($expectedpostids, $actualpostids);
    }

    /**
     * Test the build function loads authors.
     */
    public function test_build_loads_authors(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user1 = $datagenerator->create_user();
        $user2 = $datagenerator->create_user();
        $user3 = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        // These 2 replies from user 3 won't be inlcuded in the export.
        $post7 = $this->helper_reply_to_post($post2, $user3);
        $post8 = $this->helper_reply_to_post($post2, $user3);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6]
        );

        $datagenerator->enrol_user($user1->id, $course->id);
        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        // We didn't include any posts from user 3 so we shouldn't see the authors
        // that match that user.
        $expectedids = [$user1->id, $user2->id];
        $actualids = array_unique(array_map(function($exportedpost) {
            return (int) $exportedpost->author->id;
        }, $exportedposts));

        sort($expectedids);
        sort($actualids);

        $this->assertEquals($expectedids, $actualids);
    }

    /**
     * Test the build function loads attachments.
     */
    public function test_build_loads_attachments(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user1 = $datagenerator->create_user();
        $user2 = $datagenerator->create_user();
        $user3 = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        $post7 = $this->helper_reply_to_post($post5, $user3);
        $post8 = $this->helper_reply_to_post($post5, $user3);
        $filestorage = get_file_storage();

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6, $post7, $post8]
        );

        // Add an attachment to a post in forum 1.
        $attachment1 = $filestorage->create_file_from_string(
            [
                'contextid' => $forums[0]->get_context()->id,
                'component' => 'mod_forum',
                'filearea'  => 'attachment',
                'itemid'    => $post1->id,
                'filepath'  => '/',
                'filename'  => 'example1.jpg',
            ],
            'image contents'
        );

        // Add an attachment to a post in forum 2.
        $attachment2 = $filestorage->create_file_from_string(
            [
                'contextid' => $forums[1]->get_context()->id,
                'component' => 'mod_forum',
                'filearea'  => 'attachment',
                'itemid'    => $post7->id,
                'filepath'  => '/',
                'filename'  => 'example2.jpg',
            ],
            'image contents'
        );

        // Enrol the user so that they can see the posts.
        $datagenerator->enrol_user($user1->id, $course->id);

        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        $expected = ['example1.jpg', 'example2.jpg'];
        $actual = array_reduce($exportedposts, function($carry, $exportedpost) {
            if (!empty($exportedpost->attachments)) {
                foreach ($exportedpost->attachments as $attachment) {
                    $carry[] = $attachment->filename;
                }
            }
            return $carry;
        }, []);

        sort($expected);
        sort($actual);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the build function loads author groups.
     */
    public function test_build_loads_author_groups(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user1 = $datagenerator->create_user();
        $user2 = $datagenerator->create_user();
        $user3 = $datagenerator->create_user();
        $course1 = $datagenerator->create_course();
        $course2 = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course1->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course1->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        $post7 = $this->helper_reply_to_post($post5, $user3);
        $post8 = $this->helper_reply_to_post($post5, $user3);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6, $post7, $post8]
        );

        // Enrol the user so that they can see the posts.
        $datagenerator->enrol_user($user1->id, $course1->id);
        $datagenerator->enrol_user($user1->id, $course2->id);
        $datagenerator->enrol_user($user2->id, $course1->id);
        $datagenerator->enrol_user($user2->id, $course2->id);
        $datagenerator->enrol_user($user3->id, $course1->id);
        $datagenerator->enrol_user($user3->id, $course2->id);

        $group1 = $datagenerator->create_group(['courseid' => $course1->id]);
        $group2 = $datagenerator->create_group(['courseid' => $course1->id]);
        // This group shouldn't be included in the results since it's in a different course.
        $group3 = $datagenerator->create_group(['courseid' => $course2->id]);

        $datagenerator->create_group_member(['userid' => $user1->id, 'groupid' => $group1->id]);
        $datagenerator->create_group_member(['userid' => $user2->id, 'groupid' => $group1->id]);
        $datagenerator->create_group_member(['userid' => $user1->id, 'groupid' => $group2->id]);
        $datagenerator->create_group_member(['userid' => $user1->id, 'groupid' => $group3->id]);

        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        $expected = [
            $user1->id => [$group1->id, $group2->id],
            $user2->id => [$group1->id],
            $user3->id => []
        ];
        $actual = array_reduce($exportedposts, function($carry, $exportedpost) {
            $author = $exportedpost->author;
            $authorid = $author->id;

            if (!isset($carry[$authorid])) {
                $carry[$authorid] = array_map(function($group) {
                    return $group['id'];
                }, $author->groups);
            }

            return $carry;
        }, []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the build function loads tags.
     */
    public function test_build_loads_tags(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user1 = $datagenerator->create_user();
        $user2 = $datagenerator->create_user();
        $user3 = $datagenerator->create_user();
        $course1 = $datagenerator->create_course();
        $course2 = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course1->id]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course1->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        $post7 = $this->helper_reply_to_post($post5, $user3);
        $post8 = $this->helper_reply_to_post($post5, $user3);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6, $post7, $post8]
        );

        // Enrol the user so that they can see the posts.
        $datagenerator->enrol_user($user1->id, $course1->id);
        $datagenerator->enrol_user($user1->id, $course2->id);
        $datagenerator->enrol_user($user2->id, $course1->id);
        $datagenerator->enrol_user($user2->id, $course2->id);
        $datagenerator->enrol_user($user3->id, $course1->id);
        $datagenerator->enrol_user($user3->id, $course2->id);

        \core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post1->id, $forums[0]->get_context(), ['foo', 'bar']);
        \core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post4->id, $forums[0]->get_context(), ['foo', 'baz']);
        \core_tag_tag::set_item_tags('mod_forum', 'forum_posts', $post7->id, $forums[1]->get_context(), ['bip']);

        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        $expected = [
            $post1->id => ['foo', 'bar'],
            $post4->id => ['foo', 'baz'],
            $post7->id => ['bip']
        ];
        $actual = array_reduce($exportedposts, function($carry, $exportedpost) {
            if (!empty($exportedpost->tags)) {
                $carry[$exportedpost->id] = array_map(function($tag) {
                    return $tag['displayname'];
                }, $exportedpost->tags);
            }

            return $carry;
        }, []);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the build function loads read_receipts.
     */
    public function test_build_loads_read_receipts(): void {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $user1 = $datagenerator->create_user(['trackforums' => 1]);
        $user2 = $datagenerator->create_user(['trackforums' => 0]);
        $course1 = $datagenerator->create_course();
        $course2 = $datagenerator->create_course();
        $forum1 = $datagenerator->create_module('forum', ['course' => $course1->id, 'trackingtype' => FORUM_TRACKING_OPTIONAL]);
        $forum2 = $datagenerator->create_module('forum', ['course' => $course1->id, 'trackingtype' => FORUM_TRACKING_OFF]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum1, $user1);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum1, $user2);
        $post3 = $this->helper_reply_to_post($post1, $user1);
        $post4 = $this->helper_reply_to_post($post1, $user2);
        [$discussion3, $post5] = $this->helper_post_to_forum($forum2, $user1);
        [$discussion4, $post6] = $this->helper_post_to_forum($forum2, $user2);
        $post7 = $this->helper_reply_to_post($post5, $user1);
        $post8 = $this->helper_reply_to_post($post5, $user1);

        [$forums, $discussions, $posts] = $this->convert_to_entities(
            [$forum1, $forum2],
            [$discussion1, $discussion2, $discussion3, $discussion4],
            [$post1, $post2, $post3, $post4, $post5, $post6, $post7, $post8]
        );

        // Enrol the user so that they can see the posts.
        $datagenerator->enrol_user($user1->id, $course1->id);
        $datagenerator->enrol_user($user1->id, $course2->id);
        $datagenerator->enrol_user($user2->id, $course1->id);
        $datagenerator->enrol_user($user2->id, $course2->id);

        forum_tp_add_read_record($user1->id, $post1->id);
        forum_tp_add_read_record($user1->id, $post4->id);
        forum_tp_add_read_record($user1->id, $post7->id);
        forum_tp_add_read_record($user2->id, $post1->id);
        forum_tp_add_read_record($user2->id, $post4->id);
        forum_tp_add_read_record($user2->id, $post7->id);

        // User 1 has tracking enabled.
        $exportedposts = $this->builder->build($user1, $forums, $discussions, $posts);

        $expected = [
            // Tracking set for forum 1 for user 1.
            $post1->id => false,
            $post2->id => true,
            $post3->id => true,
            $post4->id => false,
            // Tracking is off for forum 2 so everything should be null.
            $post5->id => null,
            $post6->id => null,
            $post7->id => null,
            $post8->id => null
        ];
        $actual = array_reduce($exportedposts, function($carry, $exportedpost) {
            $carry[$exportedpost->id] = $exportedpost->unread;
            return $carry;
        }, []);

        $this->assertEquals($expected, $actual);

        // User 2 has tracking disabled.
        $exportedposts = $this->builder->build($user2, $forums, $discussions, $posts);

        // Tracking is off for user 2 so everything should be null.
        $expected = [
            $post1->id => null,
            $post2->id => null,
            $post3->id => null,
            $post4->id => null,
            $post5->id => null,
            $post6->id => null,
            $post7->id => null,
            $post8->id => null
        ];
        $actual = array_reduce($exportedposts, function($carry, $exportedpost) {
            $carry[$exportedpost->id] = $exportedpost->unread;
            return $carry;
        }, []);

        $this->assertEquals($expected, $actual);
    }
}
