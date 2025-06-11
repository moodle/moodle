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
 * The post read receipt collection vault tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class vaults_post_read_receipt_collection_test extends \advanced_testcase {
    // Make use of the test generator trait.
    use mod_forum_tests_generator_trait;

    /**
     * Test get_from_user_id_and_post_ids.
     */
    public function test_get_from_user_id_and_post_ids(): void {
        global $DB;
        $this->resetAfterTest();

        $entityfactory = \mod_forum\local\container::get_entity_factory();
        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $vault = $vaultfactory->get_post_read_receipt_collection_vault();
        $datagenerator = $this->getDataGenerator();
        $user = $datagenerator->create_user();
        $otheruser = $datagenerator->create_user();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', ['course' => $course->id]);
        [$discussion1, $post1] = $this->helper_post_to_forum($forum, $user);
        [$discussion2, $post2] = $this->helper_post_to_forum($forum, $user);
        $post3 = $this->helper_reply_to_post($post2, $user);

        $DB->insert_record('forum_read', [
            'userid' => $user->id,
            'forumid' => $forum->id,
            'discussionid' => $discussion1->id,
            'postid' => $post1->id,
            'firstread' => time(),
            'lastread' => time()
        ]);
        // Receipt for other user.
        $DB->insert_record('forum_read', [
            'userid' => $otheruser->id,
            'forumid' => $forum->id,
            'discussionid' => $discussion1->id,
            'postid' => $post1->id,
            'firstread' => time(),
            'lastread' => time()
        ]);
        $DB->insert_record('forum_read', [
            'userid' => $user->id,
            'forumid' => $forum->id,
            'discussionid' => $discussion2->id,
            'postid' => $post3->id,
            'firstread' => time(),
            'lastread' => time()
        ]);

        $post1 = $entityfactory->get_post_from_stdClass($post1);
        $post2 = $entityfactory->get_post_from_stdClass($post2);
        $post3 = $entityfactory->get_post_from_stdClass($post3);
        $collection = $vault->get_from_user_id_and_post_ids($user->id, [$post1->get_id(), $post2->get_id()]);

        // True because there is a read receipt for this user.
        $this->assertTrue($collection->has_user_read_post($user, $post1));
        // False because other user wasn't included in the fetch.
        $this->assertFalse($collection->has_user_read_post($otheruser, $post1));
        // No read receipt.
        $this->assertFalse($collection->has_user_read_post($user, $post2));
        // Wasn't included in fetch.
        $this->assertFalse($collection->has_user_read_post($user, $post3));
    }
}
