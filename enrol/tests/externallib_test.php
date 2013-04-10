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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/externallib.php');

/**
 * Enrol external PHPunit tests
 *
 * @package    core_enrol
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
class core_enrol_external_testcase extends externallib_advanced_testcase {

    /**
     * Test get_enrolled_users
     */
    public function test_get_enrolled_users() {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id);
        $this->assignUserCapability('moodle/user:viewdetails', $context->id, $roleid);

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $roleid, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $roleid, 'manual');
        $this->getDataGenerator()->enrol_user($USER->id, $course->id, $roleid, 'manual');

        // Call the external function.
        $enrolledusers = core_enrol_external::get_enrolled_users($course->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check we retrieve the good total number of enrolled users.
        $this->assertEquals(3, count($enrolledusers));

        // Call without required capability.
        $this->unassignUserCapability('moodle/course:viewparticipants', $context->id, $roleid);
        $this->setExpectedException('moodle_exception');
        $categories = core_enrol_external::get_enrolled_users($course->id);
    }

    /**
     * Test get_users_courses
     */
    public function test_get_users_courses() {
        global $USER;

        $this->resetAfterTest(true);

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();
        $courses = array($course1, $course2);

        // Enrol $USER in the courses.
        // We use the manual plugin.
        $roleid = null;
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $roleid = $this->assignUserCapability('moodle/course:viewparticipants',
                    $context->id, $roleid);

            $this->getDataGenerator()->enrol_user($USER->id, $course->id, $roleid, 'manual');
        }

        // Call the external function.
        $enrolledincourses = core_enrol_external::get_users_courses($USER->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);

        // Check we retrieve the good total number of enrolled users.
        $this->assertEquals(2, count($enrolledincourses));
    }

    /**
     * Test get_enrolled_users_with_capability
     */
    public function test_get_enrolled_users_with_capability () {
        global $DB, $USER;

        $this->resetAfterTest(true);

        $coursedata['idnumber'] = 'idnumbercourse1';
        $coursedata['fullname'] = 'Lightwork Course 1';
        $coursedata['summary'] = 'Lightwork Course 1 description';
        $coursedata['summaryformat'] = FORMAT_MOODLE;
        $course1  = self::getDataGenerator()->create_course($coursedata);

        // Create a manual enrolment record.
        $manual_enrol_data['enrol'] = 'manual';
        $manual_enrol_data['status'] = 0;
        $manual_enrol_data['courseid'] = $course1->id;
        $enrolid = $DB->insert_record('enrol', $manual_enrol_data);

        // Create the user and give them capabilities in the course context.
        $context = context_course::instance($course1->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id, 3);

        // Create a student.
        $student1  = self::getDataGenerator()->create_user();

        // Enrol both the user and the student in the course.
        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $USER->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $student1->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        $params = array("coursecapabilities" =>array
        ('courseid' => $course1->id, 'capabilities' => array('moodle/course:viewparticipants')));
        $options = array();
        $result = core_enrol_external::get_enrolled_users_with_capability($params, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0];
        $this->assertEquals($course1->id, $expecteduserlist['courseid']);
        $this->assertEquals('moodle/course:viewparticipants', $expecteduserlist['capability']);
        $this->assertEquals(1, count($expecteduserlist['users']));

    }
}

/**
 * Role external PHPunit tests
 *
 * @package    core_enrol
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
class core_role_external_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/externallib.php');
    }

    /**
     * Test assign_roles
     */
    public function test_assign_roles() {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/role:assign', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Add manager role to $USER.
        // So $USER is allowed to assign 'manager', 'editingteacher', 'teacher' and 'student'.
        role_assign(1, $USER->id, context_system::instance()->id);

        // Check the teacher role has not been assigned to $USER.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Call the external function. Assign teacher role to $USER.
        core_role_external::assign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id)));

        // Check the role has been assigned.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Call without required capability.
        $this->unassignUserCapability('moodle/role:assign', $context->id, $roleid);
        $this->setExpectedException('moodle_exception');
        $categories = core_role_external::assign_roles(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id));
    }

    /**
     * Test unassign_roles
     */
    public function test_unassign_roles() {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/role:assign', $context->id);
        $this->assignUserCapability('moodle/course:view', $context->id, $roleid);

        // Add manager role to $USER.
        // So $USER is allowed to assign 'manager', 'editingteacher', 'teacher' and 'student'.
        role_assign(1, $USER->id, context_system::instance()->id);

        // Add teacher role to $USER on course context.
        role_assign(3, $USER->id, $context->id);

        // Check the teacher role has been assigned to $USER on course context.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 1);

        // Call the external function. Assign teacher role to $USER.
        core_role_external::unassign_roles(array(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id)));

        // Check the role has been unassigned on course context.
        $users = get_role_users(3, $context);
        $this->assertEquals(count($users), 0);

        // Call without required capability.
        $this->unassignUserCapability('moodle/role:assign', $context->id, $roleid);
        $this->setExpectedException('moodle_exception');
        $categories = core_role_external::unassign_roles(
            array('roleid' => 3, 'userid' => $USER->id, 'contextid' => $context->id));
    }
}
