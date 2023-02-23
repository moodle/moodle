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
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Tests for the get_join_url class.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\external\get_join_url
 */
class get_join_url_test extends \externallib_advanced_testcase {
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
    protected function get_join_url(...$params) {
        $getjoinurl = get_join_url::execute(...$params);

        return external_api::clean_returnvalue(get_join_url::execute_returns(), $getjoinurl);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance() {
        $this->expectExceptionMessageMatches('/No such instance.*/');
        $joinurl = $this->get_join_url(1234, 5678);

        $this->assertIsArray($joinurl);
        $this->assertArrayNotHasKey('join_url', $joinurl);
        $this->assertEquals(false, $joinurl['join_url']);
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
        $this->get_join_url($instance->get_cm_id());
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
        $this->get_join_url($instance->get_cm_id());
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
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Make sure the meeting is running (this is not the default with the mock server).
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id()
        ]);

        $this->setUser($user);

        $joinurl = $this->get_join_url($instance->get_cm_id());

        $this->assertIsArray($joinurl);
        $this->assertArrayHasKey('join_url', $joinurl);
        $this->assertEmpty($joinurl['warnings']);
        $this->assertNotNull($joinurl['join_url']);
    }

    /**
     * Check that URL are different depending on the group.
     */
    public function test_execute_with_group() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $g1 = $generator->create_group(['courseid' => $course->id]);
        $g2 = $generator->create_group(['courseid' => $course->id]);
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');
        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Make sure the meeting is running (this is not the default with the mock server).
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id()
        ]);

        $this->setUser($user);

        $joinurlnogroup = $this->get_join_url($instance->get_cm_id());
        $joinurlnogroupv2 = $this->get_join_url($instance->get_cm_id());
        $joinurlg1 = $this->get_join_url($instance->get_cm_id(), $g1->id);
        $joinurlg2 = $this->get_join_url($instance->get_cm_id(), $g2->id);

        foreach ([$joinurlnogroup, $joinurlnogroupv2, $joinurlg1, $joinurlg2] as $join) {
            $this->assertIsArray($join);
            $this->assertArrayHasKey('join_url', $join);
            $this->assertEmpty($join['warnings']);
            $this->assertNotNull($join['join_url']);
        }
        $this->assertNotEquals($joinurlnogroup['join_url'], $joinurlg1['join_url']);
        $this->assertNotEquals($joinurlg2['join_url'], $joinurlg1['join_url']);
    }

    /**
     * Check that we return the same URL once meeting is started.
     */
    public function test_execute_with_same_url() {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $user = $generator->create_and_enrol($course, 'student');

        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Make sure the meeting is running (this is not the default with the mock server).
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id()
        ]);

        $this->setUser($user);

        $joinurl = $this->get_join_url($instance->get_cm_id());
        $joinurlv2 = $this->get_join_url($instance->get_cm_id());

        foreach ([$joinurl, $joinurlv2] as $join) {
            $this->assertIsArray($join);
            $this->assertArrayHasKey('join_url', $join);
            $this->assertEmpty($join['warnings']);
            $this->assertNotNull($join['join_url']);
        }
        $this->assertEquals($joinurl['join_url'], $joinurlv2['join_url']);
    }

    /**
     * Check that we return the same URL once meeting is started.
     */
    public function test_user_limit() {
        $this->resetAfterTest();
        set_config('bigbluebuttonbn_userlimit_editable', true);
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $record = $generator->create_module('bigbluebuttonbn', ['course' => $course->id, 'userlimit' => 2]);

        $user1 = $generator->create_and_enrol($course, 'student');
        $user2 = $generator->create_and_enrol($course, 'student');
        $instance = instance::get_from_instanceid($record->id);

        $bbbgenerator = $this->getDataGenerator()->get_plugin_generator('mod_bigbluebuttonbn');
        // Make sure the meeting is running (this is not the default with the mock server).
        $bbbgenerator->create_meeting([
            'instanceid' => $instance->get_instance_id(),
            'groupid' => $instance->get_group_id(),
            'participants' => 2
        ]);
        $this->setUser($user1);
        $joinurl = $this->get_join_url($instance->get_cm_id());
        $this->assertNotNull($joinurl['warnings']);
        $this->assertEquals('userlimitreached', $joinurl['warnings'][0]['warningcode']);
    }
}

