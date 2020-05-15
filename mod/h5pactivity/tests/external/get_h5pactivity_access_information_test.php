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
use external_api;
use externallib_advanced_testcase;

/**
 * External function test for get_h5pactivity_access_information.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_h5pactivity_access_information_testcase extends externallib_advanced_testcase {

    /**
     * Test the behaviour of get_h5pactivity_access_information().
     */
    public function test_get_h5pactivity_access_information() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Check the access information for a student.
        $this->setUser($student);
        $result = get_h5pactivity_access_information::execute($activity->id);
        $result = external_api::clean_returnvalue(get_h5pactivity_access_information::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        unset($result['warnings']);

        // Check default values for capabilities for student.
        $enabledcaps = ['canview', 'cansubmit'];
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }

        // Check the access information for a teacher.
        $this->setUser($teacher);
        $result = get_h5pactivity_access_information::execute($activity->id);
        $result = external_api::clean_returnvalue(get_h5pactivity_access_information::execute_returns(), $result);
        $this->assertCount(0, $result['warnings']);
        unset($result['warnings']);

        // Check default values for capabilities for teacher.
        $enabledcaps = ['canview', 'canaddinstance', 'canreviewattempts'];
        foreach ($result as $capname => $capvalue) {
            if (in_array($capname, $enabledcaps)) {
                $this->assertTrue($capvalue);
            } else {
                $this->assertFalse($capvalue);
            }
        }

        // Call the WS using an unexisting h5pactivityid.
        $this->expectException(dml_missing_record_exception::class);
        $result = get_h5pactivity_access_information::execute($activity->id + 1);
    }
}