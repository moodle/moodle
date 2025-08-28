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

/**
 * Tests for the get_bigbluebuttons_by_courses class.
 *
 * @package    mod_bigbluebuttonbn
 * @category   test
 * @copyright  2021 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\external\get_bigbluebuttonbns_by_courses
 */
final class get_bigbluebuttons_by_courses_test extends \core_external\tests\externallib_testcase {
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
    protected function get_bigbluebuttons_by_courses(...$params) {
        $returnvalue = get_bigbluebuttonbns_by_courses::execute(...$params);

        return external_api::clean_returnvalue(get_bigbluebuttonbns_by_courses::execute_returns(), $returnvalue);
    }

    /**
     * Test execute API CALL with no instance
     */
    public function test_execute_no_instance(): void {
        $this->resetAfterTest();
        $bbbactivities = $this->get_bigbluebuttons_by_courses([1234, 5678]);

        $this->assertIsArray($bbbactivities);
        $this->assertArrayHasKey('bigbluebuttonbns', $bbbactivities);
        $this->assertArrayHasKey('warnings', $bbbactivities);
        $this->assertEmpty($bbbactivities['bigbluebuttonbns']);
    }

    /**
     * Test execute API CALL without login
     */
    public function test_execute_without_login(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $record = $this->getDataGenerator()->create_module('bigbluebuttonbn', ['course' => $course->id]);
        $instance = instance::get_from_instanceid($record->id);

        $this->expectException(moodle_exception::class);
        $this->get_bigbluebuttons_by_courses($instance->get_cm_id());
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

        $this->expectException(moodle_exception::class);
        $this->get_bigbluebuttons_by_courses($instance->get_cm_id());
    }

    /**
     * Test get bbbactivities
     */
    public function test_execute_with_valid_login(): void {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create a user.
        $user = $generator->create_user();

        // Create courses to add the modules.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        $bbbs = [];
        // First bbb activity.
        $bbbs[] = $generator->create_module('bigbluebuttonbn', ['course' => $course1->id]);

        // Second bbb activity.
        $bbbs[] = $generator->create_module('bigbluebuttonbn', ['course' => $course2->id]);

        $generator->enrol_user($user->id, $course1->id, null, 'manual');
        $generator->enrol_user($user->id, $course2->id, null, 'manual');
        // Set to the user.
        self::setUser($user);

        // Call the external function passing course ids.
        $bbbactivities = $this->get_bigbluebuttons_by_courses([$course1->id, $course2->id]);
        $this->assert_same_bbb_activities($bbbs, $bbbactivities['bigbluebuttonbns']);

        // Call the external function without passing course id.
        $bbbactivities = $this->get_bigbluebuttons_by_courses();
        $this->assert_same_bbb_activities($bbbs, $bbbactivities['bigbluebuttonbns']);

        // Unenrol user from second course and alter expected bbb activity.
        $enrol = enrol_get_plugin('manual');
        $enrolinstances = enrol_get_instances($course2->id, true);
        foreach ($enrolinstances as $courseenrolinstance) {
            if ($courseenrolinstance->enrol == "manual") {
                $instance2 = $courseenrolinstance;
                break;
            }
        }
        $enrol->unenrol_user($instance2, $user->id);

        // Call the external function without passing course id.
        $bbbactivities = $this->get_bigbluebuttons_by_courses();
        $this->assertCount(1, $bbbactivities['bigbluebuttonbns']);
        $this->assertEquals($bbbs[0]->id, $bbbactivities['bigbluebuttonbns'][0]['id']);

        // Call for the second course we unenrolled the user from.
        $bbbactivities = $this->get_bigbluebuttons_by_courses([$course2->id]);
        $this->assertCount(0, $bbbactivities['bigbluebuttonbns']);
    }

    /**
     * Check if the two arrays containing the activities are the same.
     *
     * @param mixed $expected
     * @param mixed $actual
     */
    protected function assert_same_bbb_activities($expected, $actual) {
        $this->assertCount(count($expected), $actual);
        $getids = function($bbb) {
            return is_array($bbb) ? $bbb['id'] : $bbb->id;
        };
        $expectedids = array_map($getids, $expected);
        $actualid = array_map($getids, $actual);
        sort($expectedids);
        sort($actualid);
        $this->assertEquals($expectedids, $actualid);
    }
}
