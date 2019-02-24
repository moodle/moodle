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
 * Contains a test class for the message helper.
 *
 * @package core_message
 * @category test
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/message/tests/messagelib_test.php');

/**
 * Tests for the message helper class.
 *
 * @package core_message
 * @category test
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_helper_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest(true);
    }

    public function test_get_member_info_ordering() {
        // Create a conversation with several users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
                $user3->id,
                $user4->id,
            ],
            'Group conversation'
        );

        // Verify that the member information comes back in the same order that we specified in the input array.
        $memberinfo = \core_message\helper::get_member_info($user1->id, [$user3->id, $user4->id, $user2->id]);
        $this->assertEquals($user3->id, array_shift($memberinfo)->id);
        $this->assertEquals($user4->id, array_shift($memberinfo)->id);
        $this->assertEquals($user2->id, array_shift($memberinfo)->id);
    }
}
