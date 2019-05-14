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
 * The post_read_receipt_collection entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\post_read_receipt_collection as collection_entity;
use mod_forum\local\entities\post as post_entity;

/**
 * The post_read_receipt_collection entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_entities_post_read_receipt_collection_testcase extends advanced_testcase {
    /**
     * Test the entity returns expected values.
     */
    public function test_entity() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $missingpost = new post_entity(
            4,
            1,
            0,
            1,
            time(),
            time(),
            true,
            'post subject',
            'post message',
            1,
            true,
            false,
            0,
            false,
            false,
            false
        );
        $post = new post_entity(
            1,
            1,
            0,
            1,
            time(),
            time(),
            true,
            'post subject',
            'post message',
            1,
            true,
            false,
            0,
            false,
            false,
            false
        );
        $collection = new collection_entity([
            (object) [
                'postid' => 1,
                'userid' => $user->id + 1
            ],
            (object) [
                'postid' => 1,
                'userid' => $user->id
            ],
            (object) [
                'postid' => 4,
                'userid' => $user->id + 1
            ]
        ]);

        $this->assertEquals(true, $collection->has_user_read_post($user, $post));
        $this->assertEquals(false, $collection->has_user_read_post($user, $missingpost));
    }
}
