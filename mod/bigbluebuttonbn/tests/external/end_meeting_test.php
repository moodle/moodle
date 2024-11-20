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

namespace mod_bigbluebuttonbn\external;

use core_external\external_api;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\meeting;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use moodle_exception;
use require_login_exception;
use core_external\restricted_context_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the update_course class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \mod_bigbluebuttonbn\external\end_meeting
 */
class end_meeting_test extends \externallib_advanced_testcase {
    use testcase_helper_trait;
    /**
     * Setup for test
     */
    public function setUp(): void {
        parent::setUp();
        $this->initialise_mock_server();
    }

    /**
     * Helper
     *
     * @param mixed ...$params
     * @return array|bool|mixed
     */
    protected function end_meeting(...$params) {
        $returnvalue = end_meeting::execute(...$params);

        return external_api::clean_returnvalue(end_meeting::execute_returns(), $returnvalue);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance(): void {
        $this->resetAfterTest();
        $this->expectException(moodle_exception::class);
        $endmeeting = $this->end_meeting(1234, 5678);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $this->expectException(require_login_exception::class);
        $this->end_meeting($instance->get_instance_id(), 0);
    }

    /**
     * Test execute API CALL with invalid login
     */
    public function test_execute_with_invalid_login(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_user();
        $this->setUser($user);

        $this->expectException(require_login_exception::class);
        $this->end_meeting($instance->get_instance_id(), 0);
    }

    /**
     * When login as a student
     */
    public function test_execute_with_student_login(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $this->setUser($user);

        $this->expectException(restricted_context_exception::class);
        $this->end_meeting($instance->get_instance_id(), 0);
    }

    /**
     * Test execute admin logic
     */
    public function test_execute_with_admin_login(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $plugingenerator = $generator->get_plugin_generator('mod_bigbluebuttonbn');
        $plugingenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
        ]);

        $this->setAdminUser();

        $result = $this->end_meeting($instance->get_instance_id(), 0);
        $this->assertIsArray($result);

        // TODO Check that the meeting was ended on the remote.
    }
    /**
     * Test execute admin logic
     */
    public function test_execute_end_meeting_already_ended(): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $plugingenerator = $generator->get_plugin_generator('mod_bigbluebuttonbn');
        $plugingenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
        ]);

        // Then end the meeting.
        // Execute the end command.
        $meeting = new meeting($instance);
        $meeting->end_meeting();

        $this->setAdminUser();

        $result = $this->end_meeting($instance->get_instance_id(), 0);
        $this->assertIsArray($result);

        // TODO Check that the meeting was ended on the remote.
    }
}
