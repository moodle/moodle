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
     * dataProvider for test_get_enrolled_users_visibility().
     */
    public function get_enrolled_users_visibility_provider() {
        return array(
            'Course without groups, default behavior (not filtering by cap, group, active)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => NOGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array(),
                ),
                'results' => array( // Everybody can view everybody.
                    'user0' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user1' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user2' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user31' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'userall' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                ),
            ),

            'Course with visible groups, default behavior (not filtering by cap, group, active)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => VISIBLEGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array(),
                ),
                'results' => array( // Everybody can view everybody.
                    'user0' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user1' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user2' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user31' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'userall' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                ),
            ),

            'Course with separate groups, default behavior (not filtering by cap, group, active)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array(),
                ),
                'results' => array( // Only users from own groups are visible.
                    'user0' => array('canview' => array()), // Poor guy, cannot see anybody, himself included.
                    'user1' => array('canview' => array('user1', 'userall')),
                    'user2' => array('canview' => array('user2', 'user2su', 'userall')),
                    'user31' => array('canview' => array('user31', 'user32', 'userall')),
                    'userall' => array('canview' => array('user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                ),
            ),

            'Course with separate groups, default behavior (not filtering but having moodle/site:accessallgroups)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => VISIBLEGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array('moodle/site:accessallgroups'),
                ),
                'results' => array( // Everybody can view everybody.
                    'user0' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user1' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user2' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'user31' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                    'userall' => array('canview' => array('user0', 'user1', 'user2', 'user2su', 'user31', 'user32', 'userall')),
                ),
            ),

            'Course with separate groups, filtering onlyactive (missing moodle/course:enrolreview)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => true,
                    'allowedcaps' => array(),
                ),
                'results' => array( // returns exception, cannot view anybody without the cap.
                    'user2' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review course enrolments')),
                    'userall' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review course enrolments')),
                ),
            ),

            'Course with separate groups, filtering onlyactive (having moodle/course:enrolreview)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => null,
                    'groupid' => null,
                    'onlyactive' => true,
                    'allowedcaps' => array('moodle/course:enrolreview'),
                ),
                'results' => array( // Suspended are not returned.
                    'user2' => array('canview' => array('user2', 'userall')),
                    'user31' => array('canview' => array('user31', 'user32', 'userall')),
                    'userall' => array('canview' => array('user1', 'user2', 'user31', 'user32', 'userall')),
                ),
            ),

            'Course with separate groups, filtering by groupid (not having moodle/site:accessallgroups)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => null,
                    'groupid' => 'group2',
                    'onlyactive' => false,
                    'allowedcaps' => array(),
                ),
                'results' => array( // Only group 2 members and only for members. Exception for non-members.
                    'user0' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Access all groups')),
                    'user1' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Access all groups')),
                    'user2' => array('canview' => array('user2', 'user2su', 'userall')),
                    'userall' => array('canview' => array('user2', 'user2su', 'userall')),
                ),
            ),

            'Course with separate groups, filtering by groupid (having moodle/site:accessallgroups)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => null,
                    'groupid' => 'group2',
                    'onlyactive' => false,
                    'allowedcaps' => array('moodle/site:accessallgroups'),
                ),
                'results' => array( // All users with 'moodle/site:accessallgroups' can view group 2
                    'user0' => array('canview' => array('user2', 'user2su', 'userall')),
                    'user1' => array('canview' => array('user2', 'user2su', 'userall')),
                    'user2' => array('canview' => array('user2', 'user2su', 'userall')),
                    'userall' => array('canview' => array('user2', 'user2su', 'userall')),
                ),
            ),

            'Course with separate groups, filtering by withcapability (not having moodle/role:review)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => 'moodle/course:bulkmessaging',
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array(),
                ),
                'results' => array( // No user has 'moodle/role:review' so exception.
                    'user0' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review permissions for others')),
                    'user1' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review permissions for others')),
                    'user2' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review permissions for others')),
                    'userall' => array('exception' => array(
                        'type' => 'required_capability_exception',
                        'message' => 'Review permissions for others')),
                ),
            ),

            'Course with separate groups, filtering by withcapability (having moodle/role:review)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => 'moodle/course:bulkmessaging',
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array('moodle/role:review'),
                ),
                'results' => array( // No user has withcapability, but all have 'moodle/role:review'. Empties.
                    'user0' => array('canview' => array()),
                    'user1' => array('canview' => array()),
                    'user2' => array('canview' => array()),
                    'userall' => array('canview' => array()),
                ),
            ),

            'Course with separate groups, filtering by withcapability (having moodle/role:review)' =>
            array(
                'settings' => array(
                    'coursegroupmode' => SEPARATEGROUPS,
                    'withcapability' => 'moodle/course:bulkmessaging',
                    'groupid' => null,
                    'onlyactive' => false,
                    'allowedcaps' => array('moodle/role:review', 'moodle/course:bulkmessaging'),
                ),
                'results' => array( // Users (previous) have withcapability, and all have 'moodle/role:review'.
                    'user0' => array('canview' => array()),
                    'user1' => array('canview' => array('user1')),
                    'user2' => array('canview' => array('user2')),
                    'userall' => array('canview' => array('user1', 'user2', 'userall')),
                ),
            ),
        );
    }

    /**
     * Verify get_enrolled_users() returned users are the expected in every situation.
     *
     * @dataProvider get_enrolled_users_visibility_provider
     */
    public function test_get_enrolled_users_visibility($settings, $results) {

        global $USER;

        $this->resetAfterTest();

        // Create the course and the users.
        $course = $this->getDataGenerator()->create_course(array('groupmode' => $settings['coursegroupmode']));
        $coursecontext = context_course::instance($course->id);
        $user0 = $this->getDataGenerator()->create_user(array('username' => 'user0'));     // A user without group.
        $user1 = $this->getDataGenerator()->create_user(array('username' => 'user1'));     // User for group 1.
        $user2 = $this->getDataGenerator()->create_user(array('username' => 'user2'));     // Two users for group 2.
        $user2su = $this->getDataGenerator()->create_user(array('username' => 'user2su')); // (one suspended).
        $user31 = $this->getDataGenerator()->create_user(array('username' => 'user31'));   // Two users for group 3.
        $user32 = $this->getDataGenerator()->create_user(array('username' => 'user32'));   // (both enabled).
        $userall = $this->getDataGenerator()->create_user(array('username' => 'userall')); // A user in all groups.

        // Create utility array of created users, to produce better assertion messages.
        $createdusers = array();
        foreach (array($user0, $user1, $user2, $user2su, $user31, $user32, $userall) as $createduser) {
            $createdusers[$createduser->id] = $createduser->username;
        }

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($user0->id, $course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2su->id, $course->id, null, 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($user31->id, $course->id);
        $this->getDataGenerator()->enrol_user($user32->id, $course->id);
        $this->getDataGenerator()->enrol_user($userall->id, $course->id);

        // Create 3 groups.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Add the users to the groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user2su->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user31->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user32->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $userall->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $userall->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $userall->id));

        // Create a role to add the allowedcaps. Users will have this role assigned.
        $roleid = $this->getDataGenerator()->create_role();
        // Allow the specified capabilities.
        if (!empty($settings['allowedcaps'])) {
            foreach ($settings['allowedcaps'] as $capability) {
                assign_capability($capability, CAP_ALLOW, $roleid, $coursecontext);
            }
        }

        // For each of the users, configure everything, perform the call, and assert results.
        foreach ($results as $user => $expectations) {
            // Convert canview expectations into a nice array of ids for easier handling.
            $canview = array();
            $exception = null;
            // Analyse the expectations.
            if (isset($expectations['canview'])) {
                foreach ($expectations['canview'] as $canviewuser) {
                    $canview[] = $createdusers[${$canviewuser}->id];
                }
            } else if (isset($expectations['exception'])) {
                $exception = $expectations['exception'];
                $this->expectException($exception['type']);
                $this->expectExceptionMessage($exception['message']);
            } else {
                // Failed, only canview and exception are supported.
                $this->markTestIncomplete('Incomplete, only canview and exception are supported');
            }
            // Switch to the user and assign the role.
            $this->setUser(${$user});
            role_assign($roleid, $USER->id, $coursecontext);

            // Convert groupid to proper id.
            $groupid = 0;
            if (isset($settings['groupid'])) {
                $groupid = ${$settings['groupid']}->id;
            }

            // Call to the function.
            $options = array(
                array('name' => 'withcapability', 'value' => $settings['withcapability']),
                array('name' => 'groupid', 'value' => $groupid),
                array('name' => 'onlyactive', 'value' => $settings['onlyactive']),
                array('name' => 'userfields', 'value' => 'id')
            );
            $enrolledusers = core_enrol_external::get_enrolled_users($course->id, $options);

            // We need to execute the return values cleaning process to simulate the web service server.
            $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

            // We are only interested in ids to check visibility.
            $viewed = array();
            // Verify the user canview the expected users.
            foreach ($enrolledusers as $enrolleduser) {
                $viewed[] = $createdusers[$enrolleduser['id']];
            }
            // Verify viewed matches canview expectation (using canonicalize to ignore ordering).
            $this->assertEquals($canview, $viewed, "Problem checking visible users for '{$createdusers[$USER->id]}'", 0, 1, true);
        }
    }

    /**
     * Test get_users_courses
     */
    public function test_get_users_courses() {
        global $USER;

        $this->resetAfterTest(true);

        $timenow = time();
        $coursedata1 = array(
            'fullname'         => '<b>Course 1</b>',                // Adding tags here to check that external_format_string works.
            'shortname'         => '<b>Course 1</b>',               // Adding tags here to check that external_format_string works.
            'summary'          => 'Lightwork Course 1 description',
            'summaryformat'    => FORMAT_MOODLE,
            'lang'             => 'en',
            'enablecompletion' => true,
            'showgrades'       => true,
            'startdate'        => $timenow,
            'enddate'          => $timenow + WEEKSECS
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
        $course1->fullname = external_format_string($course1->fullname, $contexts[$course1->id]->id);
        $course1->shortname = external_format_string($course1->shortname, $contexts[$course1->id]->id);
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
        $coursedata = new stdClass();
        $coursedata->visible = 0;
        $course2 = self::getDataGenerator()->create_course($coursedata);

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

        $this->setAdminUser();

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

        // Try to retrieve information using a normal user for a hidden course.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            core_enrol_external::get_course_enrolment_methods($course2->id);
        } catch (moodle_exception $e) {
            $this->assertEquals('coursehidden', $e->errorcode);
        }
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
     *
     * @expectedException moodle_exception
     */
    public function test_get_enrolled_users_without_capability() {
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_setup($capability);

        // Call without required capability.
        $this->unassignUserCapability($capability, $data->context->id, $data->roleid);
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
