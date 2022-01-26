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

use external_api;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the update_course class.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_bigbluebuttonbn\external\can_join
 */
class can_join_test extends \externallib_advanced_testcase {
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
     * @param ... $params
     * @return mixed
     */
    protected function can_join(...$params) {
        $canjoin = can_join::execute(...$params);

        return external_api::clean_returnvalue(can_join::execute_returns(), $canjoin);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance() {
        $canjoin = $this->can_join(1234, 5678);

        $this->assertIsArray($canjoin);
        $this->assertArrayHasKey('can_join', $canjoin);
        $this->assertEquals(false, $canjoin['can_join']);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $this->expectException(moodle_exception::class);
        $this->can_join($instance->get_cm_id());
    }

    /**
     * Test execute API CALL with invalid login
     */
    public function test_execute_with_invalid_login() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_user();
        $this->setUser($user);

        $this->expectException(moodle_exception::class);
        $this->can_join($instance->get_cm_id());
    }

    /**
     * When login as a student
     */
    public function test_execute_with_valid_login() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $this->setUser($user);

        $canjoin = $this->can_join($instance->get_cm_id());

        $this->assertIsArray($canjoin);
        $this->assertArrayHasKey('can_join', $canjoin);
        $this->assertEquals(true, $canjoin['can_join']);
    }
}
