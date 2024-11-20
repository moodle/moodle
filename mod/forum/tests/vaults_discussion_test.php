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
 * The discussion vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vaults_discussion_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /** @var \mod_forum\local\vaults\discussion */
    private $vault;

    /**
     * Set up function for tests.
     */
    public function setUp(): void {
        parent::setUp();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $this->vault = $vaultfactory->get_discussion_vault();
    }

    /**
     * Test get_from_id.
     */
    public function test_get_from_id(): void {
        $this->resetAfterTest();

        $vault = $this->vault;
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussionrecord, $post] = $this->helper_post_to_forum($forum, $user);

        $discussion = $vault->get_from_id($discussionrecord->id);

        $this->assertEquals($discussionrecord->id, $discussion->get_id());
    }

    /**
     * Test get_first_discussion_in_forum.
     */
    public function test_get_first_discussion_in_forum(): void {
        $this->resetAfterTest();

        $vault = $this->vault;
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($coursemodule->id);
        $forumentity = $entityfactory->get_forum_from_stdClass($forum, $context, $coursemodule, $course);

        $this->assertEquals(null, $vault->get_first_discussion_in_forum($forumentity));

        [$discussion1, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 2]);
        [$discussion2, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 1]);
        [$discussion3, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 3]);

        $discussionentity = $vault->get_first_discussion_in_forum($forumentity);
        $this->assertEquals($discussion2->id, $discussionentity->get_id());
    }

    /**
     * Test get_all_discussions_in_forum
     */
    public function test_get_all_discussions_in_forum(): void {
        $this->resetAfterTest();

        $vault = $this->vault;
        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = \context_module::instance($coursemodule->id);
        $forumentity = $entityfactory->get_forum_from_stdClass($forum, $context, $coursemodule, $course);

        $this->assertEquals([], $vault->get_all_discussions_in_forum($forumentity));

        [$discussion1, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 2]);
        [$discussion2, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 1]);
        [$discussion3, $post] = $this->helper_post_to_forum($forum, $user, ['timemodified' => 3]);

        $discussionentity = $vault->get_all_discussions_in_forum($forumentity);
        $this->assertArrayHasKey($discussion1->id, $discussionentity); // Order is not guaranteed, so just verify element existence.
        $this->assertArrayHasKey($discussion2->id, $discussionentity);
        $this->assertArrayHasKey($discussion3->id, $discussionentity);
    }
}
