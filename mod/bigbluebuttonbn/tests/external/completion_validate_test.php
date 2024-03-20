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
use mod_bigbluebuttonbn\test\testcase_helper_trait;
use require_login_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the completion_validate class.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\external\completion_validate
 */
class completion_validate_test extends \externallib_advanced_testcase {
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
     * @return mixed
     */
    protected function completion_validate(...$params) {
        $returnvalue = completion_validate::execute(...$params);

        return external_api::clean_returnvalue(completion_validate::execute_returns(), $returnvalue);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance() {
        $this->resetAfterTest();
        $result = $this->completion_validate(1234);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('warnings', $result);
        $this->assertEquals([
            'item' => 'mod_bigbluebuttonbn',
            'itemid' => 1234,
            'warningcode' => 'indexerrorbbtn',
            'message' => 'BigBlueButton ID 1234 is incorrect',
        ], $result['warnings'][0]);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);
        $this->expectException(require_login_exception::class);
        $this->completion_validate($instance->get_instance_id());
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

        $user = $generator->create_user($course);
        $this->setUser($user);
        $this->expectException(require_login_exception::class);
        $this->completion_validate($instance->get_instance_id());
    }

    /**
     * When login as a student
     */
    public function test_execute_with_valid_login_but_student() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $this->setUser($user);

        $returnvalue = $this->completion_validate($instance->get_instance_id());

        $this->assertArrayHasKey('warnings', $returnvalue);

        $this->assertEquals([
            'item' => 'mod_bigbluebuttonbn',
            'itemid' => $record->id,
            'warningcode' => 'nopermissions',
            'message' => 'Sorry, but you do not currently have permissions to do that (completion_validate).',
        ], $returnvalue['warnings'][0]);
    }

    /**
     * When login as a student
     */
    public function test_execute_with_valid_login_with_teacher() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'editingteacher');
        $this->setUser($user);

        $returnvalue = $this->completion_validate($instance->get_instance_id());

        $this->assertArrayHasKey('warnings', $returnvalue);
        $this->assertEmpty($returnvalue['warnings']);
    }
}

