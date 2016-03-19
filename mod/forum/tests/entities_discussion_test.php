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
 * The discussion entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\entities\discussion as discussion_entity;
use mod_forum\local\entities\post as post_entity;

/**
 * The discussion entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_forum_entities_discussion_testcase extends advanced_testcase {
    /**
     * Test the entity returns expected values.
     */
    public function test_entity() {
        $this->resetAfterTest();

        // In the past to ensure the time started is true.
        $time = time() + 10;
        $discussion = new discussion_entity(
            1,
            2,
            3,
            'test discussion',
            4,
            5,
            6,
            false,
            $time,
            $time,
            0,
            0,
            false
        );
        $firstpost = new post_entity(
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
        $notfirstpost = new post_entity(
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

        $this->assertEquals(1, $discussion->get_id());
        $this->assertEquals(2, $discussion->get_course_id());
        $this->assertEquals(3, $discussion->get_forum_id());
        $this->assertEquals('test discussion', $discussion->get_name());
        $this->assertEquals(4, $discussion->get_first_post_id());
        $this->assertEquals(5, $discussion->get_user_id());
        $this->assertEquals(6, $discussion->get_group_id());
        $this->assertEquals(false, $discussion->is_assessed());
        $this->assertEquals($time, $discussion->get_time_modified());
        $this->assertEquals($time, $discussion->get_user_modified());
        $this->assertEquals(0, $discussion->get_time_start());
        $this->assertEquals(0, $discussion->get_time_end());
        $this->assertEquals(false, $discussion->is_pinned());
        $this->assertEquals(true, $discussion->is_first_post($firstpost));
        $this->assertEquals(false, $discussion->is_first_post($notfirstpost));
        $this->assertEquals(true, $discussion->has_started());
        $this->assertEquals(true, $discussion->has_group());
    }
}
