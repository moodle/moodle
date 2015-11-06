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
                $this->setExpectedException($exception['type'], $exception['message']);
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
     * Test get_enrolled_users
     */
    public function test_get_enrolled_users() {
        global $USER;

        $this->resetAfterTest(true);

        $course = self::getDataGenerator()->create_course();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $this->setUser($user3);

        // Set the required capabilities by the external function.
        $context = context_course::instance($course->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id);
        $this->assignUserCapability('moodle/user:viewdetails', $context->id, $roleid);

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $roleid, 'manual');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $roleid, 'manual');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, $roleid, 'manual');

        // Call the external function.
        $enrolledusers = core_enrol_external::get_enrolled_users($course->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set.
        $this->assertEquals(3, count($enrolledusers));
        $this->assertArrayHasKey('email', $enrolledusers[0]);

        // Call the function with some parameters set.
        $enrolledusers = core_enrol_external::get_enrolled_users($course->id, array(
            array('name' => 'limitfrom', 'value' => 2),
            array('name' => 'limitnumber', 'value' => 1),
            array('name' => 'userfields', 'value' => 'id')
        ));

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set, we should only get the 3rd result, which is $user3.
        $this->assertCount(1, $enrolledusers);
        $this->assertEquals($user3->id, $enrolledusers[0]['id']);
        $this->assertArrayHasKey('id', $enrolledusers[0]);
        $this->assertArrayNotHasKey('email', $enrolledusers[0]);

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

        $user1 = $this->getDataGenerator()->create_user();

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

        // Create the users and give them capabilities in the course context.
        $context = context_course::instance($course1->id);
        $roleid = $this->assignUserCapability('moodle/course:viewparticipants', $context->id, 3);

        // Create 2 students.
        $student1 = self::getDataGenerator()->create_user();
        $student2 = self::getDataGenerator()->create_user();

        // Give the capability to student2.
        assign_capability('moodle/course:viewparticipants', CAP_ALLOW, 3, $context->id);
        role_assign(3, $student2->id, $context->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Enrol both the user and the students in the course.
        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $USER->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $student1->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        $user_enrolment_data['status'] = 0;
        $user_enrolment_data['enrolid'] = $enrolid;
        $user_enrolment_data['userid'] = $student2->id;
        $DB->insert_record('user_enrolments', $user_enrolment_data);

        $params = array("coursecapabilities" => array('courseid' => $course1->id,
            'capabilities' => array('moodle/course:viewparticipants')));
        $options = array();
        $result = core_enrol_external::get_enrolled_users_with_capability($params, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0];
        $this->assertEquals($course1->id, $expecteduserlist['courseid']);
        $this->assertEquals('moodle/course:viewparticipants', $expecteduserlist['capability']);
        $this->assertEquals(2, count($expecteduserlist['users']));

        // Now doing the query again with options.
        $params = array(
            "coursecapabilities" => array(
                'courseid' => $course1->id,
                'capabilities' => array('moodle/course:viewparticipants')
            )
        );
        $options = array(
            array('name' => 'limitfrom', 'value' => 1),
            array('name' => 'limitnumber', 'value' => 1),
            array('name' => 'userfields', 'value' => 'id')
        );

        $result = core_enrol_external::get_enrolled_users_with_capability($params, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0]['users'];
        $expecteduser = reset($expecteduserlist);
        $this->assertEquals(1, count($expecteduserlist));
        $this->assertEquals($student2->id, $expecteduser['id']);
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
}
