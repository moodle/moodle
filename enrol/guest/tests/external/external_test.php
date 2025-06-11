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
 * Self enrol external PHPunit tests
 *
 * @package   enrol_guest
 * @copyright 2015 Juan Leyva <juan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 3.1
 */

namespace enrol_guest\external;

use core_external\external_api;
use enrol_guest_external;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Guest enrolment external functions tests
 *
 * @package    enrol_guest
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
final class external_test extends externallib_advanced_testcase {

    /**
     * Test get_instance_info
     */
    public function test_get_instance_info(): void {
        global $DB;

        $this->resetAfterTest(true);

        // Check if guest enrolment plugin is enabled.
        $guestplugin = enrol_get_plugin('guest');
        $this->assertNotEmpty($guestplugin);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $coursedata = new \stdClass();
        $coursedata->visible = 0;
        $course = self::getDataGenerator()->create_course($coursedata);

        $student = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        // Add enrolment methods for course.
        $instance = $guestplugin->add_instance($course, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance',
                                                                'customint6' => 1,
                                                                'roleid' => $studentrole->id));

        $this->setAdminUser();
        $result = enrol_guest_external::get_instance_info($instance);
        $result = external_api::clean_returnvalue(enrol_guest_external::get_instance_info_returns(), $result);

        $this->assertEquals($instance, $result['instanceinfo']['id']);
        $this->assertEquals($course->id, $result['instanceinfo']['courseid']);
        $this->assertEquals('guest', $result['instanceinfo']['type']);
        $this->assertEquals('Test instance', $result['instanceinfo']['name']);
        $this->assertTrue($result['instanceinfo']['status']);
        $this->assertFalse($result['instanceinfo']['passwordrequired']);

        $DB->set_field('enrol', 'status', ENROL_INSTANCE_DISABLED, array('id' => $instance));

        $result = enrol_guest_external::get_instance_info($instance);
        $result = external_api::clean_returnvalue(enrol_guest_external::get_instance_info_returns(), $result);
        $this->assertEquals($instance, $result['instanceinfo']['id']);
        $this->assertEquals($course->id, $result['instanceinfo']['courseid']);
        $this->assertEquals('guest', $result['instanceinfo']['type']);
        $this->assertEquals('Test instance', $result['instanceinfo']['name']);
        $this->assertFalse($result['instanceinfo']['status']);
        $this->assertFalse($result['instanceinfo']['passwordrequired']);

        $DB->set_field('enrol', 'status', ENROL_INSTANCE_ENABLED, array('id' => $instance));

        // Try to retrieve information using a normal user for a hidden course.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            enrol_guest_external::get_instance_info($instance);
        } catch (\moodle_exception $e) {
            $this->assertEquals('coursehidden', $e->errorcode);
        }

        // Student user.
        $DB->set_field('course', 'visible', 1, array('id' => $course->id));
        $this->setUser($student);
        $result = enrol_guest_external::get_instance_info($instance);
        $result = external_api::clean_returnvalue(enrol_guest_external::get_instance_info_returns(), $result);

        $this->assertEquals($instance, $result['instanceinfo']['id']);
        $this->assertEquals($course->id, $result['instanceinfo']['courseid']);
        $this->assertEquals('guest', $result['instanceinfo']['type']);
        $this->assertEquals('Test instance', $result['instanceinfo']['name']);
        $this->assertTrue($result['instanceinfo']['status']);
        $this->assertFalse($result['instanceinfo']['passwordrequired']);
    }
}
