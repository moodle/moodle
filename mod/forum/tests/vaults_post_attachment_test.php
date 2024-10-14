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
 * The post_attachment vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class vaults_post_attachment_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /**
     * Test get_attachments_for_posts.
     */
    public function test_get_attachments_for_posts(): void {
        $this->resetAfterTest();

        $filestorage = get_file_storage();
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $vault = $vaultfactory->get_post_attachment_vault();
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($coursemodule->id);
        [$discussion, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $attachment1 = $filestorage->create_file_from_string(
            [
                'contextid' => $context->id,
                'component' => 'mod_forum',
                'filearea'  => 'attachment',
                'itemid'    => $post1->id,
                'filepath'  => '/',
                'filename'  => 'example1.jpg',
            ],
            'image contents'
        );
        $attachment2 = $filestorage->create_file_from_string(
            [
                'contextid' => $context->id,
                'component' => 'mod_forum',
                'filearea'  => 'attachment',
                'itemid'    => $post2->id,
                'filepath'  => '/',
                'filename'  => 'example2.jpg',
            ],
            'image contents'
        );

        $post1 = $entityfactory->get_post_from_stdClass($post1);
        $post2 = $entityfactory->get_post_from_stdClass($post2);
        $post3 = $entityfactory->get_post_from_stdClass($post3);

        $results = $vault->get_attachments_for_posts(\context_system::instance(), [$post1, $post2, $post3]);
        $this->assertCount(3, $results);
        $this->assertEquals([], $results[$post1->get_id()]);
        $this->assertEquals([], $results[$post2->get_id()]);
        $this->assertEquals([], $results[$post3->get_id()]);

        $results = $vault->get_attachments_for_posts($context, [$post1]);
        $this->assertCount(1, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());

        $results = $vault->get_attachments_for_posts($context, [$post1, $post2]);
        $this->assertCount(2, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());
        $this->assertEquals($attachment2->get_filename(), $results[$post2->get_id()][0]->get_filename());

        $results = $vault->get_attachments_for_posts($context, [$post1, $post2, $post3]);
        $this->assertCount(3, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());
        $this->assertEquals($attachment2->get_filename(), $results[$post2->get_id()][0]->get_filename());
        $this->assertEquals([], $results[$post3->get_id()]);
    }

    /**
     * Test get_inline_attachments_for_posts.
     */
    public function test_get_inline_attachments_for_posts(): void {
        $this->resetAfterTest();

        $filestorage = get_file_storage();
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $vault = $vaultfactory->get_post_attachment_vault();
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($coursemodule->id);
        [$discussion, $post1] = $this->helper_post_to_forum($forum, $user);
        $post2 = $this->helper_reply_to_post($post1, $user);
        $post3 = $this->helper_reply_to_post($post1, $user);
        $attachment1 = $filestorage->create_file_from_string(
            [
                'contextid' => $context->id,
                'component' => 'mod_forum',
                'filearea'  => 'post',
                'itemid'    => $post1->id,
                'filepath'  => '/',
                'filename'  => 'example1.jpg',
            ],
            'image contents'
        );
        $attachment2 = $filestorage->create_file_from_string(
            [
                'contextid' => $context->id,
                'component' => 'mod_forum',
                'filearea'  => 'post',
                'itemid'    => $post2->id,
                'filepath'  => '/',
                'filename'  => 'example2.jpg',
            ],
            'image contents'
        );

        $post1 = $entityfactory->get_post_from_stdClass($post1);
        $post2 = $entityfactory->get_post_from_stdClass($post2);
        $post3 = $entityfactory->get_post_from_stdClass($post3);

        $results = $vault->get_inline_attachments_for_posts(\context_system::instance(), [$post1, $post2, $post3]);
        $this->assertCount(3, $results);
        $this->assertEquals([], $results[$post1->get_id()]);
        $this->assertEquals([], $results[$post2->get_id()]);
        $this->assertEquals([], $results[$post3->get_id()]);

        $results = $vault->get_inline_attachments_for_posts($context, [$post1]);
        $this->assertCount(1, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());

        $results = $vault->get_inline_attachments_for_posts($context, [$post1, $post2]);
        $this->assertCount(2, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());
        $this->assertEquals($attachment2->get_filename(), $results[$post2->get_id()][0]->get_filename());

        $results = $vault->get_inline_attachments_for_posts($context, [$post1, $post2, $post3]);
        $this->assertCount(3, $results);
        $this->assertEquals($attachment1->get_filename(), $results[$post1->get_id()][0]->get_filename());
        $this->assertEquals($attachment2->get_filename(), $results[$post2->get_id()][0]->get_filename());
        $this->assertEquals([], $results[$post3->get_id()]);
    }
}
