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
class core_enrol_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test get_users_courses
     */
    public function test_get_users_courses() {
        global $USER;

        $this->resetAfterTest(true);

        $coursedata1 = array(
            'summary'          => 'Lightwork Course 1 description',
            'summaryformat'    => FORMAT_MOODLE,
            'lang'             => 'en',
            'enablecompletion' => true,
            'showgrades'       => true
        );

        $course1 = self::getDataGenerator()->create_course($coursedata1);
        $course2 = self::getDataGenerator()->create_course();
        $courses = array($course1, $course2);

        // Enrol $USER in the courses.
        // We use the manual plugin.
        $roleid = null;
        $contexts = array();
        foreach ($courses as $course) {
            $contexts[$course->id] = context_course::instance($course->id);
            $roleid = $this->assignUserCapability('moodle/course:viewparticipants',
                    $contexts[$course->id]->id, $roleid);

            $this->getDataGenerator()->enrol_user($USER->id, $course->id, $roleid, 'manual');
        }

        // Call the external function.
        $enrolledincourses = core_enrol_external::get_users_courses($USER->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);

        // Check we retrieve the good total number of enrolled users.
        $this->assertEquals(2, count($enrolledincourses));

        // We need to format summary and summaryformat before to compare them with those values returned by the webservice.
        list($course1->summary, $course1->summaryformat) =
             external_format_text($course1->summary, $course1->summaryformat, $contexts[$course1->id]->id, 'course', 'summary', 0);

        // Check there are no differences between $course1 properties and course values returned by the webservice
        // only for those fields listed in the $coursedata1 array.
        foreach ($enrolledincourses as $courseenrol) {
            if ($courseenrol['id'] == $course1->id) {
                foreach ($coursedata1 as $fieldname => $value) {
                    $this->assertEquals($courseenrol[$fieldname], $course1->$fieldname);
                }
            }
        }
    }

    /**
     * Test get_course_enrolment_methods
     */
    public function test_get_course_enrolment_methods() {
        global $DB;

        $this->resetAfterTest(true);

        // Get enrolment plugins.
        $selfplugin = enrol_get_plugin('self');
        $this->assertNotEmpty($selfplugin);
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotEmpty($manualplugin);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        // Add enrolment methods for course.
        $instanceid1 = $selfplugin->add_instance($course1, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 1',
                                                                'customint6' => 1,
                                                                'roleid' => $studentrole->id));
        $instanceid2 = $selfplugin->add_instance($course1, array('status' => ENROL_INSTANCE_DISABLED,
                                                                'name' => 'Test instance 2',
                                                                'roleid' => $studentrole->id));

        $instanceid3 = $manualplugin->add_instance($course1, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 3'));

        $enrolmentmethods = $DB->get_records('enrol', array('courseid' => $course1->id, 'status' => ENROL_INSTANCE_ENABLED));
        $this->assertCount(2, $enrolmentmethods);

        // Check if information is returned.
        $enrolmentmethods = core_enrol_external::get_course_enrolment_methods($course1->id);
        $enrolmentmethods = external_api::clean_returnvalue(core_enrol_external::get_course_enrolment_methods_returns(),
                                                            $enrolmentmethods);
        // Enrolment information is currently returned by self enrolment plugin, so count == 1.
        // This should be changed as we implement get_enrol_info() for other enrolment plugins.
        $this->assertCount(1, $enrolmentmethods);

        $enrolmentmethod = $enrolmentmethods[0];
        $this->assertEquals($course1->id, $enrolmentmethod['courseid']);
        $this->assertEquals('self', $enrolmentmethod['type']);
        $this->assertTrue($enrolmentmethod['status']);
        $this->assertFalse(isset($enrolmentmethod['wsfunction']));

        $instanceid4 = $selfplugin->add_instance($course2, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 4',
                                                                'roleid' => $studentrole->id,
                                                                'customint6' => 1,
                                                                'password' => 'test'));
        $enrolmentmethods = core_enrol_external::get_course_enrolment_methods($course2->id);
        $enrolmentmethods = external_api::clean_returnvalue(core_enrol_external::get_course_enrolment_methods_returns(),
                                                            $enrolmentmethods);
        $this->assertCount(1, $enrolmentmethods);

        $enrolmentmethod = $enrolmentmethods[0];
        $this->assertEquals($course2->id, $enrolmentmethod['courseid']);
        $this->assertEquals('self', $enrolmentmethod['type']);
        $this->assertTrue($enrolmentmethod['status']);
        $this->assertEquals('enrol_self_get_instance_info', $enrolmentmethod['wsfunction']);
    }

    public function get_enrolled_users_setup($capability) {
        global $USER;

        $this->resetAfterTest(true);

        $return = new stdClass();

        $return->course = self::getDataGenerator()->create_course();
        $return->user1 = self::getDataGenerator()->create_user();
        $return->user2 = self::getDataGenerator()->create_user();
        $return->user3 = self::getDataGenerator()->create_user();
        $this->setUser($return->user3);

        // Set the required capabilities by the external function.
        $return->context = context_course::instance($return->course->id);
        $return->roleid = $this->assignUserCapability($capability, $return->context->id);
        $this->assignUserCapability('moodle/user:viewdetails', $return->context->id, $return->roleid);

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($return->user1->id, $return->course->id, $return->roleid, 'manual');
        $this->getDataGenerator()->enrol_user($return->user2->id, $return->course->id, $return->roleid, 'manual');
        $this->getDataGenerator()->enrol_user($return->user3->id, $return->course->id, $return->roleid, 'manual');

        return $return;
    }

    /**
     * Test get_enrolled_users from core_enrol_external without additional
     * parameters.
     */
    public function test_get_enrolled_users_without_parameters() {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_setup($capability);

        // Call the external function.
        $enrolledusers = core_enrol_external::get_enrolled_users($data->course->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set.
        $this->assertEquals(3, count($enrolledusers));
        $this->assertArrayHasKey('email', $enrolledusers[0]);
    }

    /**
     * Test get_enrolled_users from core_enrol_external with some parameters set.
     */
    public function test_get_enrolled_users_with_parameters() {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_setup($capability);

        // Call the function with some parameters set.
        $enrolledusers = core_enrol_external::get_enrolled_users($data->course->id, array(
            array('name' => 'limitfrom', 'value' => 2),
            array('name' => 'limitnumber', 'value' => 1),
            array('name' => 'userfields', 'value' => 'id')
        ));

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set, we should only get the 3rd result, which is $user3.
        $this->assertCount(1, $enrolledusers);
        $this->assertEquals($data->user3->id, $enrolledusers[0]['id']);
        $this->assertArrayHasKey('id', $enrolledusers[0]);
        $this->assertArrayNotHasKey('email', $enrolledusers[0]);
    }

    /**
     * Test get_enrolled_users from core_enrol_external with capability to
     * viewparticipants removed.
     */
    public function test_get_enrolled_users_without_capability() {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_setup($capability);

        // Call without required capability.
        $this->unassignUserCapability($capability, $data->context->id, $data->roleid);
        $this->setExpectedException('moodle_exception');
        $categories = core_enrol_external::get_enrolled_users($data->course->id);
    }

    public function get_enrolled_users_with_capability_setup($capability) {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $return = new stdClass();

        // Create the course and fetch its context.
        $return->course = self::getDataGenerator()->create_course();
        $context = context_course::instance($return->course->id);

        // Create one teacher, and two students.
        $return->teacher = self::getDataGenerator()->create_user();
        $return->student1 = self::getDataGenerator()->create_user();
        $return->student2 = self::getDataGenerator()->create_user();

        // Create a new student role based on the student archetype but with the capability prohibitted.
        $fakestudentroleid = create_role('Fake student role', 'fakestudent', 'Fake student role', 'student');
        assign_capability($capability, CAP_PROHIBIT, $fakestudentroleid, $context->id);

        // Enrol all of the users in the course.
        // * 'teacher'  is an editing teacher.
        // * 'student1' is a standard student.
        // * 'student2' is a student with the capability prohibitted.
        $editingteacherroleid = $DB->get_field('role', 'id', array('shortname' => 'editingteacher'));
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($return->teacher->id, $return->course->id, $editingteacherroleid);
        $this->getDataGenerator()->enrol_user($return->student1->id, $return->course->id, $studentroleid);
        $this->getDataGenerator()->enrol_user($return->student2->id, $return->course->id, $fakestudentroleid);

        // Log in as the teacher.
        $this->setUser($return->teacher);

        // Clear caches.
        accesslib_clear_all_caches_for_unit_testing();

        return $return;
    }

    /**
     * Test get_enrolled_users_with_capability without additional paramaters.
     */
    public function test_get_enrolled_users_with_capability_without_parameters() {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_with_capability_setup($capability);

        $result = core_enrol_external::get_enrolled_users_with_capability(
            array(
                'coursecapabilities' => array(
                    'courseid' => $data->course->id,
                    'capabilities' => array(
                        $capability,
                    ),
                ),
            ),
            array()
        );

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0];
        $this->assertEquals($data->course->id, $expecteduserlist['courseid']);
        $this->assertEquals($capability, $expecteduserlist['capability']);
        $this->assertEquals(2, count($expecteduserlist['users']));
    }

    /**
     * Test get_enrolled_users_with_capability
     */
    public function test_get_enrolled_users_with_capability_with_parameters () {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_with_capability_setup($capability);

        $result = core_enrol_external::get_enrolled_users_with_capability(
            array(
                'coursecapabilities' => array(
                    'courseid' => $data->course->id,
                    'capabilities' => array(
                        $capability,
                    ),
                ),
            ),
            array(
                array('name' => 'limitfrom', 'value' => 1),
                array('name' => 'limitnumber', 'value' => 1),
                array('name' => 'userfields', 'value' => 'id')
            )
        );

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0]['users'];
        $expecteduser = reset($expecteduserlist);
        $this->assertEquals(1, count($expecteduserlist));
        $this->assertEquals($data->student1->id, $expecteduser['id']);
    }

}
