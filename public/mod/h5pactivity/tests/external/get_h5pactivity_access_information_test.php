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
 * External function test for get_h5pactivity_access_information.
 *
 * @package    mod_h5pactivity
 * @category   external
 * @since      Moodle 3.9
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

use dml_missing_record_exception;
use core_external\external_api;
use externallib_advanced_testcase;

/**
 * External function test for get_h5pactivity_access_information.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class get_h5pactivity_access_information_test extends externallib_advanced_testcase {

    /**
     * Test the behaviour of get_h5pactivity_access_information().
     *
     * @dataProvider get_h5pactivity_access_information_data
     * @param string $role user role in course
     * @param int $enabletracking if tracking is enabled
     * @param array $enabledcaps capabilities enabled
     */
    public function test_get_h5pactivity_access_information(string $role, int $enabletracking, array $enabledcaps): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            [
                'course' => $course,
                'enabletracking' => $enabletracking
            ]
        );

        if ($role) {
            $user = $this->getDataGenerator()->create_and_enrol($course, $role);
            $this->setUser($user);
        }

        // Check the access information.
        $result = get_h5pactivity_access_information::execute($activity->id);
        $result = external_api::clean_returnvalue(get_h5pactivity_access_information::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        unset($result['warnings']);

        // Check the values for capabilities.
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }
    }

    /**
     * Data provider for get_h5pactivity_access_information.
     *
     * @return array
     */
    public static function get_h5pactivity_access_information_data(): array {
        return [
            'Admin, tracking enabled' => [
                '', 1, ['canview', 'canreviewattempts', 'canaddinstance']
            ],
            'Admin, tracking disabled' => [
                '', 0, ['canview', 'canreviewattempts', 'canaddinstance']
            ],
            'Student, tracking enabled' => [
                'student', 1, ['canview', 'cansubmit']
            ],
            'Student, tracking disabled' => [
                'student', 0, ['canview']
            ],
            'Teacher, tracking enabled' => [
                'editingteacher', 1, [
                    'canview',
                    'canreviewattempts',
                    'canaddinstance'
                ]
            ],
            'Teacher, tracking disabled' => [
                'editingteacher', 0, [
                    'canview',
                    'canreviewattempts',
                    'canaddinstance'
                ]
            ],
        ];
    }

    /**
     * Test dml_missing_record_exception in get_h5pactivity_access_information.
     */
    public function test_dml_missing_record_exception(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Call the WS using an unexisting h5pactivityid.
        $this->expectException(dml_missing_record_exception::class);
        $result = get_h5pactivity_access_information::execute(1);
    }
}