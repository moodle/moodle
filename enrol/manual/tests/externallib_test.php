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

namespace enrol_manual;

use enrol_manual_external;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/manual/externallib.php');

/**
 * Enrol manual external PHPunit tests
 *
 * @package    enrol_manual
 * @category   phpunit
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
class externallib_test extends externallib_advanced_testcase {

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

        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $instance1 = $DB->get_record('enrol', array('courseid' => $course1->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', array('courseid' => $course2->id, 'enrol' => 'manual'), '*', MUST_EXIST);

        // Set the required capabilities by the external function.
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $context1->id);
        $this->assignUserCapability('moodle/course:view', $context1->id, $roleid);
        $this->assignUserCapability('moodle/role:assign', $context1->id, $roleid);
        $this->assignUserCapability('enrol/manual:enrol', $context2->id, $roleid);
        $this->assignUserCapability('moodle/course:view', $context2->id, $roleid);
        $this->assignUserCapability('moodle/role:assign', $context2->id, $roleid);

        core_role_set_assign_allowed($roleid, 3);

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
        } catch (\moodle_exception $e) {
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
        } catch (\moodle_exception $e) {
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
        } catch (\moodle_exception $e) {
            $this->assertSame('wsnoinstance', $e->errorcode);
            $this->assertSame(
                "Manual enrolment plugin instance doesn't exist or is disabled for the course (id = {$course2->id})",
                $e->getMessage()
            );
        }
    }

    /**
     * Test for unerolling a single user.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public function test_unenrol_user_single() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/enrollib.php');
        $this->resetAfterTest(true);
        // The user who perform the action.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user); // Log this user in.
        $enrol = enrol_get_plugin('manual');
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        // Set the capability for the user.
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $coursecontext);
        $this->assignUserCapability('enrol/manual:unenrol', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/course:view', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/role:assign', $coursecontext, $roleid);
        // Create a student and enrol them into the course.
        $student = $this->getDataGenerator()->create_user();
        $enrol->enrol_user($enrolinstance, $student->id);
        $this->assertTrue(is_enrolled($coursecontext, $student));
        // Call the web service to unenrol.
        enrol_manual_external::unenrol_users(array(
            array('userid' => $student->id, 'courseid' => $course->id),
        ));
        $this->assertFalse(is_enrolled($coursecontext, $student));
    }

    /**
     * Test for unenrolling multiple users.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public function test_unenrol_user_multiple() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/enrollib.php');
        $this->resetAfterTest(true);
        // The user who perform the action.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user); // Log this user in.
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        // Set the capability for the user.
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $coursecontext);
        $this->assignUserCapability('enrol/manual:unenrol', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/course:view', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/role:assign', $coursecontext, $roleid);
        $enrol = enrol_get_plugin('manual');
        // Create a student and enrol them into the course.
        $student1 = $this->getDataGenerator()->create_user();
        $enrol->enrol_user($enrolinstance, $student1->id);
        $this->assertTrue(is_enrolled($coursecontext, $student1));
        $student2 = $this->getDataGenerator()->create_user();
        $enrol->enrol_user($enrolinstance, $student2->id);
        $this->assertTrue(is_enrolled($coursecontext, $student2));
        // Call the web service to unenrol.
        enrol_manual_external::unenrol_users(array(
            array('userid' => $student1->id, 'courseid' => $course->id),
            array('userid' => $student2->id, 'courseid' => $course->id),
        ));
        $this->assertFalse(is_enrolled($coursecontext, $student1));
        $this->assertFalse(is_enrolled($coursecontext, $student2));
    }

    /**
     * Test for unenrol capability.
     * @throws coding_exception
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     */
    public function test_unenrol_user_error_no_capability() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/enrollib.php');
        $this->resetAfterTest(true);
        // The user who perform the action.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user); // Log this user in.
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        $enrol = enrol_get_plugin('manual');
        // Create a student and enrol them into the course.
        $student = $this->getDataGenerator()->create_user();
        $enrol->enrol_user($enrolinstance, $student->id);
        $this->assertTrue(is_enrolled($coursecontext, $student));
        // Call the web service to unenrol.
        try {
            enrol_manual_external::unenrol_users(array(
                array('userid' => $student->id, 'courseid' => $course->id),
            ));
            $this->fail('Exception expected: User cannot log in to the course');
        } catch (\Exception $ex) {
            $this->assertTrue($ex instanceof \require_login_exception);
        }
        // Set the capability for the course, then try again.
        $roleid = $this->assignUserCapability('moodle/course:view', $coursecontext);
        try {
            enrol_manual_external::unenrol_users(array(
                array('userid' => $student->id, 'courseid' => $course->id),
            ));
            $this->fail('Exception expected: User cannot log in to the course');
        } catch (\Exception $ex) {
            $this->assertTrue($ex instanceof \required_capability_exception);
        }
        // Assign unenrol capability.
        $this->assignUserCapability('enrol/manual:unenrol', $coursecontext, $roleid);
        enrol_manual_external::unenrol_users(array(
            array('userid' => $student->id, 'courseid' => $course->id),
        ));
        $this->assertFalse(is_enrolled($coursecontext, $student));
    }

    /**
     * Test for unenrol if user does not exist.
     * @throws coding_exception
     */
    public function test_unenrol_user_error_not_exist() {
        global $CFG, $DB;
        require_once($CFG->libdir . '/enrollib.php');
        $this->resetAfterTest(true);
        // The user who perform the action.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user); // Log this user in.
        $enrol = enrol_get_plugin('manual');
        // Create a course.
        $course = self::getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id, 'enrol' => 'manual'), '*', MUST_EXIST);
        // Set the capability for the user.
        $roleid = $this->assignUserCapability('enrol/manual:enrol', $coursecontext);
        $this->assignUserCapability('enrol/manual:unenrol', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/course:view', $coursecontext, $roleid);
        $this->assignUserCapability('moodle/role:assign', $coursecontext, $roleid);
        // Create a student and enrol them into the course.
        $student = $this->getDataGenerator()->create_user();
        $enrol->enrol_user($enrolinstance, $student->id);
        $this->assertTrue(is_enrolled($coursecontext, $student));
        try {
            enrol_manual_external::unenrol_users(array(
                array('userid' => $student->id + 1, 'courseid' => $course->id),
            ));
            $this->fail('Exception expected: invalid student id');
        } catch (\Exception $ex) {
            $this->assertTrue($ex instanceof \invalid_parameter_exception);
        }

        // Call for course without manual instance.
        $DB->delete_records('user_enrolments');
        $DB->delete_records('enrol', ['courseid' => $course->id]);
        try {
            enrol_manual_external::unenrol_users(array(
                array('userid' => $student->id + 1, 'courseid' => $course->id),
            ));
            $this->fail('Exception expected if course does not have manual instance');
        } catch (\moodle_exception $e) {
            $this->assertSame('wsnoinstance', $e->errorcode);
            $this->assertSame(
                "Manual enrolment plugin instance doesn't exist or is disabled for the course (id = {$course->id})",
                $e->getMessage()
            );
        }
    }
}
