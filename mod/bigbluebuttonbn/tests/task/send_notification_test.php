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

namespace mod_bigbluebuttonbn\task;

use advanced_testcase;

/**
 * Class containing the scheduled task for lti module.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2019 onwards, Blindside Networks Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\task\send_notification
 * @coversDefaultClass \mod_bigbluebuttonbn\task\send_notification
 */
class send_notification_test extends advanced_testcase {
    /**
     * Test that the debug message is correctly output.
     *
     */
    public function test_generate_message(): void {
        $this->resetAfterTest();
        $task = new send_notification();
        $message = $task->generate_message();
        $this->assertEquals("Attempted to run deprecated implementation of send_notification task.", $message);
    }
}
