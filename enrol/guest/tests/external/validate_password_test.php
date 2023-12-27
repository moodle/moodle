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

namespace enrol_guest\external;

use core_external\external_api;

/**
 * Tests for validate_password class.
 *
 * @package enrol_guest
 * @covers \enrol_guest\external\validate_password
 */
class validate_password_test extends \advanced_testcase {

    public function test_execute(): void {
        global $DB;

        $this->resetAfterTest();

        $course = self::getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $student = self::getDataGenerator()->create_user();
        $pass = 'abc';

        // Add enrolment methods for course.
        $guestplugin = enrol_get_plugin('guest');
        $instanceid = $guestplugin->add_instance($course, [
            'status' => ENROL_INSTANCE_ENABLED,
            'name' => 'Test instance',
            'customint6' => 1,
            'password' => $pass,
            'roleid' => $studentrole->id,
        ]);

        $this->setUser($student);

        // Invalid password.
        $result = validate_password::execute($instanceid, 'z');
        $result = external_api::clean_returnvalue(validate_password::execute_returns(), $result);
        $this->assertFalse($result['validated']);
        $this->assertEmpty($result['hint']);

        // Set invalid password preference.
        set_user_preference('enrol_guest_ws_password_' . $instanceid, 'y');

        // Enable hint for invalid password.
        set_config('showhint', 1, 'enrol_guest');
        $result = validate_password::execute($instanceid, 'z');
        $result = external_api::clean_returnvalue(validate_password::execute_returns(), $result);
        $this->assertFalse($result['validated']);
        $this->assertNotEmpty($result['hint']); // Check hint.
        $this->assertNull(get_user_preferences('enrol_guest_ws_password_'. $instanceid));   // Check preference was reset.

        // Try valid password.
        $result = validate_password::execute($instanceid, $pass);
        $result = external_api::clean_returnvalue(validate_password::execute_returns(), $result);
        $this->assertTrue($result['validated']);

        // Check correct user preference.
        $this->assertEquals($pass, get_user_preferences('enrol_guest_ws_password_'. $instanceid));

        // Course hidden, expect exception.
        $DB->set_field('course', 'visible', 0, ['id' => $course->id]);
        $this->expectException(\moodle_exception::class);
        $result = validate_password::execute($instanceid, '');
    }
}
