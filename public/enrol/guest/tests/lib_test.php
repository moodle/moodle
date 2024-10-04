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
 * Guest enrolment tests.
 *
 * @package    enrol_guest
 * @category   phpunit
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_guest;

final class lib_test extends \advanced_testcase {

    /**
     * Test the behaviour of validate_enrol_plugin_data().
     *
     * @covers ::validate_enrol_plugin_data
     */
    public function test_validate_enrol_plugin_data(): void {
        global $CFG;

        $this->resetAfterTest();

        $guestplugin = enrol_get_plugin('guest');

        $guestplugin->set_config('usepasswordpolicy', false);
        $enrolmentdata = [];
        $errors = $guestplugin->validate_enrol_plugin_data($enrolmentdata);
        $this->assertEmpty($errors);

        // Now enable some controls, and check that the policy responds with policy text.
        $guestplugin->set_config('usepasswordpolicy', true);
        $CFG->minpasswordlength = 8;
        $CFG->minpassworddigits = 1;
        $CFG->minpasswordlower = 1;
        $CFG->minpasswordupper = 1;
        $CFG->minpasswordnonalphanum = 1;
        $CFG->maxconsecutiveidentchars = 1;
        $errors = $guestplugin->validate_enrol_plugin_data($enrolmentdata);
        // If password is omitted it will be autocreated so nothing to validate.
        $this->assertEmpty($errors);

        $enrolmentdata = ['password' => 'test'];
        $errors = $guestplugin->validate_enrol_plugin_data($enrolmentdata);
        $this->assertCount(4, $errors);
        $this->assertEquals(get_string('errorminpasswordlength', 'auth', $CFG->minpasswordlength), $errors['enrol_guest0']);
        $this->assertEquals(get_string('errorminpassworddigits', 'auth', $CFG->minpassworddigits), $errors['enrol_guest1']);
        $this->assertEquals(get_string('errorminpasswordupper', 'auth', $CFG->minpasswordupper), $errors['enrol_guest2']);
        $this->assertEquals(get_string('errorminpasswordnonalphanum', 'auth', $CFG->minpasswordnonalphanum), $errors['enrol_guest3']);

        $enrolmentdata = ['password' => 'Testingtest123@'];
        $errors = $guestplugin->validate_enrol_plugin_data($enrolmentdata);
        $this->assertEmpty($errors);
    }

    /**
     * Test the behaviour of update_enrol_plugin_data().
     *
     * @covers ::update_enrol_plugin_data
     */
    public function test_update_enrol_plugin_data(): void {
        global $DB;
        $this->resetAfterTest();
        $manualplugin = enrol_get_plugin('guest');

        $admin = get_admin();
        $this->setUser($admin);

        $enrolmentdata = [];

        $cat = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $cat->id, 'shortname' => 'ANON']);
        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'guest'], '*', MUST_EXIST);

        $expectedinstance = $instance;
        $modifiedinstance = $manualplugin->update_enrol_plugin_data($course->id, $enrolmentdata, $instance);
        $this->assertEquals($expectedinstance, $modifiedinstance);

        $enrolmentdata['password'] = 'test';
        $expectedinstance->password = 'test';
        $modifiedinstance = $manualplugin->update_enrol_plugin_data($course->id, $enrolmentdata, $instance);
        $this->assertEquals($expectedinstance, $modifiedinstance);
    }

    /**
     * Test the behaviour of find_instance().
     *
     * @covers ::find_instance
     */
    public function test_find_instance(): void {
        global $DB;
        $this->resetAfterTest();

        $cat = $this->getDataGenerator()->create_category();
        // When we create a course, a guest enrolment instance is also created.
        $course = $this->getDataGenerator()->create_course(['category' => $cat->id, 'shortname' => 'ANON']);

        $guestplugin = enrol_get_plugin('guest');

        $expected = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'guest']);

        // Let's try to add second instance - only 1 guest instance is possible.
        $instanceid2 = null;
        // Have to do this check since add_instance doesn't block adding second instance for guest plugin.
        if ($guestplugin->can_add_instance($course->id)) {
            $instanceid2 = $guestplugin->add_instance($course, []);
        }
        $this->assertNull($instanceid2);

        $enrolmentdata = [];
        $actual = $guestplugin->find_instance($enrolmentdata, $course->id);
        $this->assertEquals($expected->id, $actual->id);
    }

}
