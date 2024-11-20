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
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 */
namespace mod_bigbluebuttonbn;

use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * Tests for the logger class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\logger
 * @coversDefaultClass \mod_bigbluebuttonbn\logger
 */
class logger_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Test delete instance logs
     */
    public function test_log_instance_deleted(): void {
        global $DB;

        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $instance = instance::get_from_instanceid($bbactivity->id);
        logger::log_instance_deleted($instance);

        $this->assertTrue($DB->record_exists('bigbluebuttonbn_logs', [
            'bigbluebuttonbnid' => $bbactivity->id,
            'log' => logger::EVENT_DELETE,
        ]));
    }

    /**
     * Test log method
     */
    public function test_log_recording_played_event(): void {
        global $DB;

        $this->resetAfterTest();
        list($bbactivitycontext, $bbactivitycm, $bbactivity) = $this->create_instance();
        $instance = instance::get_from_instanceid($bbactivity->id);

        logger::log_recording_played_event($instance, 1);
        $this->assertTrue($DB->record_exists('bigbluebuttonbn_logs', ['bigbluebuttonbnid' => $instance->get_instance_id()]));
    }
}
