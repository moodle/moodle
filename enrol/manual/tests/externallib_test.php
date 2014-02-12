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
 * Enrol manual external PHPunit tests
 *
 * @package    enrol_manual
 * @category   phpunit
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/manual/externallib.php');

class enrol_manual_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test get_enrolled_users
     */
    public function test_enrol_users() {
        global $DB;

        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $context1 = context_course::instance($course1->id);
        $context2 = context_course::instance($course2->id);
        $instance1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        // Set the required capabilities by the external function.
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $context1->id);
        $this->assignUserCapability('moodle/course:view', $context1->id, $roleid);
        $this->assignUserCapability('moodle/role:assign', $context1->id, $roleid);
        $this->assignUserCapability('enrol/manual:enrol', $context2->id, $roleid);
        $this->assignUserCapability('moodle/course:view', $context2->id, $roleid);
        $this->assignUserCapability('moodle/role:assign', $context2->id, $roleid);

        allow_assign($roleid, 3);

        // Call the external function.
        enrol_manual_external::enrol_users(array(
            array('roleid' => 3, 'userid' => $user1->id, 'courseid' => $course1->id),
            array('roleid' => 3, 'userid' => $user2->id, 'courseid' => $course1->id),
        ));

        $this->assertEquals(2, $DB->count_records('user_enrolments', array('enrolid' => $instance1->id)));
        $this->assertEquals(0, $DB->count_records('user_enrolments', array('enrolid' => $instance2->id)));
        $this->assertTrue(is_enrolled($context1, $user1));
        $this->assertTrue(is_enrolled($context1, $user2));

        // Call without required capability.
        $DB->delete_records('user_enrolments');
        $this->unassignUserCapability('enrol/manual:enrol', $context1->id, $roleid);
        try {
            enrol_manual_external::enrol_users(array(
                array('roleid' => 3, 'userid' => $user1->id, 'courseid' => $course1->id),
            ));
            $this->fail('Exception expected if not having capability to enrol');
        } catch (moodle_exception $e) {
            $this->assertInstanceOf('required_capability_exception', $e);
            $this->assertSame('nopermissions', $e->errorcode);
        }
        $this->assignUserCapability('enrol/manual:enrol', $context1->id, $roleid);
        $this->assertEquals(0, $DB->count_records('user_enrolments'));

        // Call with forbidden role.
        try {
            enrol_manual_external::enrol_users(array(
                array('roleid' => 1, 'userid' => $user1->id, 'courseid' => $course1->id),
            ));
            $this->fail('Exception expected if not allowed to assign role.');
        } catch (moodle_exception $e) {
            $this->assertSame('wsusercannotassign', $e->errorcode);
        }
        $this->assertEquals(0, $DB->count_records('user_enrolments'));

        // Call for course without manual instance.
        $DB->delete_records('user_enrolments');
        $DB->delete_records('enrol', array('courseid' => $course2->id));
        try {
            enrol_manual_external::enrol_users(array(
                array('roleid' => 3, 'userid' => $user1->id, 'courseid' => $course1->id),
                array('roleid' => 3, 'userid' => $user1->id, 'courseid' => $course2->id),
            ));
            $this->fail('Exception expected if course does not have manual instance');
        } catch (moodle_exception $e) {
            $this->assertSame('wsnoinstance', $e->errorcode);
        }
    }
}
