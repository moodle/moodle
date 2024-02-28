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

namespace core_enrol;

use core_enrol_external;
use core_external\external_api;
use enrol_user_enrolment_form;
use externallib_advanced_testcase;
use stdClass;

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
class externallib_test extends externallib_advanced_testcase {

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
        $coursecontext = \context_course::instance($course->id);
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
            $this->assertEqualsCanonicalizing($canview, $viewed, "Problem checking visible users for '{$createdusers[$USER->id]}'");
        }
    }

    /**
     * Verify get_enrolled_users() returned users according to their status.
     */
    public function test_get_enrolled_users_active_suspended() {
        global $USER;

        $this->resetAfterTest();

        // Create the course and the users.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = \context_course::instance($course->id);
        $user0 = $this->getDataGenerator()->create_user(['username' => 'user0active']);
        $user1 = $this->getDataGenerator()->create_user(['username' => 'user1active']);
        $user2 = $this->getDataGenerator()->create_user(['username' => 'user2active']);
        $user2su = $this->getDataGenerator()->create_user(['username' => 'user2suspended']); // Suspended user.
        $user3 = $this->getDataGenerator()->create_user(['username' => 'user3active']);
        $user3su = $this->getDataGenerator()->create_user(['username' => 'user3suspended']); // Suspended user.

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($user0->id, $course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2su->id, $course->id, null, 'manual', 0, 0, ENROL_USER_SUSPENDED);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id);
        $this->getDataGenerator()->enrol_user($user3su->id, $course->id, null, 'manual', 0, 0, ENROL_USER_SUSPENDED);

        // Create a role to add the allowedcaps. Users will have this role assigned.
        $roleid = $this->getDataGenerator()->create_role();
        // Allow the specified capabilities.
        assign_capability('moodle/course:enrolreview', CAP_ALLOW, $roleid, $coursecontext);
        assign_capability('moodle/user:viewalldetails', CAP_ALLOW, $roleid, $coursecontext);

        // Switch to the user and assign the role.
        $this->setUser($user0);
        role_assign($roleid, $USER->id, $coursecontext);

        // Suspended users.
        $options = [
            ['name' => 'onlysuspended', 'value' => true],
            ['name' => 'userfields', 'value' => 'id,username']
        ];
        $suspendedusers = core_enrol_external::get_enrolled_users($course->id, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $suspendedusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $suspendedusers);
        $this->assertCount(2, $suspendedusers);

        foreach ($suspendedusers as $suspendeduser) {
            $this->assertStringContainsString('suspended', $suspendeduser['username']);
        }

        // Active users.
        $options = [
            ['name' => 'onlyactive', 'value' => true],
            ['name' => 'userfields', 'value' => 'id,username']
        ];
        $activeusers = core_enrol_external::get_enrolled_users($course->id, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $activeusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $activeusers);
        $this->assertCount(4, $activeusers);

        foreach ($activeusers as $activeuser) {
            $this->assertStringContainsString('active', $activeuser['username']);
        }

        // All enrolled users.
        $options = [
            ['name' => 'userfields', 'value' => 'id,username']
        ];
        $allusers = core_enrol_external::get_enrolled_users($course->id, $options);

        // We need to execute the return values cleaning process to simulate the web service server.
        $allusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $allusers);
        $this->assertCount(6, $allusers);

        // Active and suspended. Test exception is thrown.
        $options = [
            ['name' => 'onlyactive', 'value' => true],
            ['name' => 'onlysuspended', 'value' => true],
            ['name' => 'userfields', 'value' => 'id,username']
        ];
        $this->expectException('coding_exception');
        $message = 'Coding error detected, it must be fixed by a programmer: Both onlyactive ' .
                        'and onlysuspended are set, this is probably not what you want!';
        $this->expectExceptionMessage($message);
        core_enrol_external::get_enrolled_users($course->id, $options);
    }

    /**
     * Test get_users_courses
     */
    public function test_get_users_courses() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/completion/criteria/completion_criteria_self.php');

        $this->resetAfterTest(true);
        $CFG->enablecompletion = 1;

        $timenow = time();
        $coursedata1 = array(
            // Adding tags here to check that \core_external\util::format_string works.
            'fullname'         => '<b>Course 1</b>',
            // Adding tags here to check that \core_external\util::format_string works.
            'shortname'         => '<b>Course 1</b>',
            'summary'          => 'Lightwork Course 1 description',
            'summaryformat'    => FORMAT_MOODLE,
            'lang'             => 'en',
            'enablecompletion' => true,
            'showgrades'       => true,
            'startdate'        => $timenow,
            'enddate'          => $timenow + WEEKSECS,
            'marker'           => 1
        );

        $coursedata2 = array(
            'lang'             => 'kk', // Check invalid language pack.
        );

        $course1 = self::getDataGenerator()->create_course($coursedata1);
        $course2 = self::getDataGenerator()->create_course($coursedata2);
        $courses = array($course1, $course2);
        $contexts = array ($course1->id => \context_course::instance($course1->id),
            $course2->id => \context_course::instance($course2->id));

        $student = $this->getDataGenerator()->create_user();
        $otherstudent = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course1->id, $studentroleid);
        $this->getDataGenerator()->enrol_user($otherstudent->id, $course1->id, $studentroleid);
        $this->getDataGenerator()->enrol_user($student->id, $course2->id, $studentroleid);

        // Force last access.
        $timenow = time();
        $lastaccess = array(
            'userid' => $student->id,
            'courseid' => $course1->id,
            'timeaccess' => $timenow
        );
        $DB->insert_record('user_lastaccess', $lastaccess);

        // Force completion, setting at least one criteria.
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_self.php');
        $criteriadata = new \stdClass();
        $criteriadata->id = $course1->id;
        // Self completion.
        $criteriadata->criteria_self = 1;

        $criterion = new \completion_criteria_self();
        $criterion->update_config($criteriadata);

        $ccompletion = new \completion_completion(array('course' => $course1->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        // Set course hidden and favourited.
        set_user_preference('block_myoverview_hidden_course_' . $course1->id, 1, $student);
        $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($student->id));
        $ufservice->create_favourite('core_course', 'courses', $course1->id, \context_system::instance());

        $this->setUser($student);
        // Call the external function.
        $enrolledincourses = core_enrol_external::get_users_courses($student->id, true);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);

        // Check we retrieve the good total number of enrolled users.
        $this->assertEquals(2, count($enrolledincourses));

        // We need to format summary and summaryformat before to compare them with those values returned by the webservice.
        [$course1->summary, $course1->summaryformat] = \core_external\util::format_text(
            $course1->summary,
            $course1->summaryformat,
            $contexts[$course1->id],
            'course',
            'summary',
            0
        );

        // Check there are no differences between $course1 properties and course values returned by the webservice
        // only for those fields listed in the $coursedata1 array.
        $course1->fullname = \core_external\util::format_string($course1->fullname, $contexts[$course1->id]->id);
        $course1->shortname = \core_external\util::format_string($course1->shortname, $contexts[$course1->id]->id);
        foreach ($enrolledincourses as $courseenrol) {
            if ($courseenrol['id'] == $course1->id) {
                foreach ($coursedata1 as $fieldname => $value) {
                    $this->assertEquals($courseenrol[$fieldname], $course1->$fieldname);
                }
                // Text extra fields.
                $this->assertEquals($course1->fullname, $courseenrol['displayname']);
                $this->assertEquals([], $courseenrol['overviewfiles']);
                $this->assertEquals($timenow, $courseenrol['lastaccess']);
                $this->assertEquals(100.0, $courseenrol['progress']);
                $this->assertEquals(true, $courseenrol['completed']);
                $this->assertTrue($courseenrol['completionhascriteria']);
                $this->assertTrue($courseenrol['completionusertracked']);
                $this->assertTrue($courseenrol['hidden']);
                $this->assertTrue($courseenrol['isfavourite']);
                $this->assertEquals(2, $courseenrol['enrolledusercount']);
                $this->assertEquals($course1->timemodified, $courseenrol['timemodified']);
                $url = "https://www.example.com/moodle/pluginfile.php/{$contexts[$course1->id]->id}/course/generated/course.svg";
                $this->assertEquals($url, $courseenrol['courseimage']);
            } else {
                // Check language pack. Should be empty since an incorrect one was used when creating the course.
                $this->assertEmpty($courseenrol['lang']);
                $this->assertEquals($course2->fullname, $courseenrol['displayname']);
                $this->assertEquals([], $courseenrol['overviewfiles']);
                $this->assertEquals(0, $courseenrol['lastaccess']);
                $this->assertEquals(0, $courseenrol['progress']);
                $this->assertEquals(false, $courseenrol['completed']);
                $this->assertFalse($courseenrol['completionhascriteria']);
                $this->assertFalse($courseenrol['completionusertracked']);
                $this->assertFalse($courseenrol['hidden']);
                $this->assertFalse($courseenrol['isfavourite']);
                $this->assertEquals(1, $courseenrol['enrolledusercount']);
                $this->assertEquals($course2->timemodified, $courseenrol['timemodified']);
                $url = "https://www.example.com/moodle/pluginfile.php/{$contexts[$course2->id]->id}/course/generated/course.svg";
                $this->assertEquals($url, $courseenrol['courseimage']);
            }
        }

        // Check that returnusercount works correctly.
        $enrolledincourses = core_enrol_external::get_users_courses($student->id, false);
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);
        foreach ($enrolledincourses as $courseenrol) {
            $this->assertFalse(isset($courseenrol['enrolledusercount']));
        }

        // Now check that admin users can see all the info.
        $this->setAdminUser();

        $enrolledincourses = core_enrol_external::get_users_courses($student->id, true);
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);
        $this->assertEquals(2, count($enrolledincourses));
        foreach ($enrolledincourses as $courseenrol) {
            if ($courseenrol['id'] == $course1->id) {
                $this->assertEquals($timenow, $courseenrol['lastaccess']);
                $this->assertEquals(100.0, $courseenrol['progress']);
                $this->assertTrue($courseenrol['completionhascriteria']);
                $this->assertTrue($courseenrol['completionusertracked']);
                $this->assertFalse($courseenrol['isfavourite']);    // This always false.
                $this->assertFalse($courseenrol['hidden']); // This always false.
            } else {
                $this->assertEquals(0, $courseenrol['progress']);
                $this->assertFalse($courseenrol['completionhascriteria']);
                $this->assertFalse($courseenrol['completionusertracked']);
                $this->assertFalse($courseenrol['isfavourite']);    // This always false.
                $this->assertFalse($courseenrol['hidden']); // This always false.
            }
        }

        // Check other users can't see private info.
        $this->setUser($otherstudent);

        $enrolledincourses = core_enrol_external::get_users_courses($student->id, true);
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);
        $this->assertEquals(1, count($enrolledincourses));

        $this->assertEquals($timenow, $enrolledincourses[0]['lastaccess']); // I can see this, not hidden.
        $this->assertEquals(null, $enrolledincourses[0]['progress']);   // I can't see this, private.

        // Change some global profile visibility fields.
        $CFG->hiddenuserfields = 'lastaccess';
        $enrolledincourses = core_enrol_external::get_users_courses($student->id, true);
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);

        $this->assertEquals(0, $enrolledincourses[0]['lastaccess']); // I can't see this, hidden by global setting.
    }

    /**
     * Test that get_users_courses respects the capability to view participants when viewing courses of other user
     */
    public function test_get_users_courses_can_view_participants(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($user1);

        $courses = core_enrol_external::clean_returnvalue(
            core_enrol_external::get_users_courses_returns(),
            core_enrol_external::get_users_courses($user2->id, false)
        );

        $this->assertCount(1, $courses);
        $this->assertEquals($course->id, reset($courses)['id']);

        // Prohibit the capability for viewing course participants.
        $studentrole = $DB->get_field('role', 'id', ['shortname' => 'student']);
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $studentrole, $context->id);

        $courses = core_enrol_external::clean_returnvalue(
            core_enrol_external::get_users_courses_returns(),
            core_enrol_external::get_users_courses($user2->id, false)
        );
        $this->assertEmpty($courses);
    }

    /*
     * Test that get_users_courses respects the capability to view a users profile when viewing courses of other user
     */
    public function test_get_users_courses_can_view_profile(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([
            'groupmode' => VISIBLEGROUPS,
        ]);

        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create separate groups for each of our students.
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group1, $user1);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        groups_add_member($group2, $user2);

        $this->setUser($user1);

        $courses = core_enrol_external::clean_returnvalue(
            core_enrol_external::get_users_courses_returns(),
            core_enrol_external::get_users_courses($user2->id, false)
        );

        $this->assertCount(1, $courses);
        $this->assertEquals($course->id, reset($courses)['id']);

        // Change to separate groups mode, so students can't view information about each other in different groups.
        $course->groupmode = SEPARATEGROUPS;
        update_course($course);

        $courses = core_enrol_external::clean_returnvalue(
            core_enrol_external::get_users_courses_returns(),
            core_enrol_external::get_users_courses($user2->id, false)
        );
        $this->assertEmpty($courses);
    }

    /**
     * Test get_users_courses with mathjax in the name.
     */
    public function test_get_users_courses_with_mathjax() {
        global $DB;

        $this->resetAfterTest(true);

        // Enable MathJax filter in content and headings.
        $this->configure_filters([
            ['name' => 'mathjaxloader', 'state' => TEXTFILTER_ON, 'move' => -1, 'applytostrings' => true],
        ]);

        // Create a course with MathJax in the name and summary.
        $coursedata = [
            'fullname'         => 'Course 1 $$(a+b)=2$$',
            'shortname'         => 'Course 1 $$(a+b)=2$$',
            'summary'          => 'Lightwork Course 1 description $$(a+b)=2$$',
            'summaryformat'    => FORMAT_HTML,
        ];

        $course = self::getDataGenerator()->create_course($coursedata);
        $context = \context_course::instance($course->id);

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_user();
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentroleid);

        $this->setUser($student);

        // Call the external function.
        $enrolledincourses = core_enrol_external::get_users_courses($student->id, true);

        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledincourses = external_api::clean_returnvalue(core_enrol_external::get_users_courses_returns(), $enrolledincourses);

        // Check that the amount of courses is the right one.
        $this->assertCount(1, $enrolledincourses);

        // Filter the values to compare them with the returned ones.
        $course->fullname = \core_external\util::format_string($course->fullname, $context->id);
        $course->shortname = \core_external\util::format_string($course->shortname, $context->id);
        [$course->summary, $course->summaryformat] = \core_external\util::format_text(
            $course->summary,
            $course->summaryformat,
            $context,
            'course',
            'summary',
            0
        );

        // Compare the values.
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">', $enrolledincourses[0]['fullname']);
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">', $enrolledincourses[0]['shortname']);
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">', $enrolledincourses[0]['summary']);
        $this->assertEquals($course->fullname, $enrolledincourses[0]['fullname']);
        $this->assertEquals($course->shortname, $enrolledincourses[0]['shortname']);
        $this->assertEquals($course->summary, $enrolledincourses[0]['summary']);
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
        $coursedata = new \stdClass();
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
        } catch (\moodle_exception $e) {
            $this->assertEquals('coursehidden', $e->errorcode);
        }
    }

    public function get_enrolled_users_setup($capability) {
        global $USER;

        $this->resetAfterTest(true);

        $return = new \stdClass();

        $return->course = self::getDataGenerator()->create_course();
        $return->user1 = self::getDataGenerator()->create_user();
        $return->user2 = self::getDataGenerator()->create_user();
        $return->user3 = self::getDataGenerator()->create_user();
        $this->setUser($return->user3);

        // Set the required capabilities by the external function.
        $return->context = \context_course::instance($return->course->id);
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
     * Test get_enrolled_users last course access.
     */
    public function test_get_enrolled_users_including_lastcourseaccess() {
        global $DB;
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_setup($capability);

        // Call the external function.
        $enrolledusers = core_enrol_external::get_enrolled_users($data->course->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set.
        $this->assertEquals(3, count($enrolledusers));
        $this->assertArrayHasKey('email', $enrolledusers[0]);
        $this->assertEquals(0, $enrolledusers[0]['lastcourseaccess']);
        $this->assertEquals(0, $enrolledusers[1]['lastcourseaccess']);
        $this->assertNotEquals(0, $enrolledusers[2]['lastcourseaccess']);   // We forced an access to the course via setUser.

        // Force last access.
        $timenow = time();
        $lastaccess = array(
            'userid' => $enrolledusers[0]['id'],
            'courseid' => $data->course->id,
            'timeaccess' => $timenow
        );
        $DB->insert_record('user_lastaccess', $lastaccess);

        $enrolledusers = core_enrol_external::get_enrolled_users($data->course->id);
        $enrolledusers = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_returns(), $enrolledusers);

        // Check the result set.
        $this->assertEquals(3, count($enrolledusers));
        $this->assertEquals($timenow, $enrolledusers[0]['lastcourseaccess']);
        $this->assertEquals(0, $enrolledusers[1]['lastcourseaccess']);
        $this->assertNotEquals(0, $enrolledusers[2]['lastcourseaccess']);
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
        $this->expectException(\moodle_exception::class);
        $categories = core_enrol_external::get_enrolled_users($data->course->id);
    }

    public function get_enrolled_users_with_capability_setup($capability) {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $return = new \stdClass();

        // Create the course and fetch its context.
        $return->course = self::getDataGenerator()->create_course();
        $context = \context_course::instance($return->course->id);

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
    public function test_get_enrolled_users_with_capability_with_parameters() {
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

    /**
     * Test get_enrolled_users last course access.
     */
    public function test_get_enrolled_users_with_capability_including_lastcourseaccess() {
        global $DB;
        $capability = 'moodle/course:viewparticipants';
        $data = $this->get_enrolled_users_with_capability_setup($capability);

        $parameters = array(
            'coursecapabilities' => array(
                'courseid' => $data->course->id,
                'capabilities' => array(
                    $capability,
                ),
            ),
        );

        $result = core_enrol_external::get_enrolled_users_with_capability($parameters, array());
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check an array containing the expected user for the course capability is returned.
        $expecteduserlist = $result[0];
        $this->assertEquals($data->course->id, $expecteduserlist['courseid']);
        $this->assertEquals($capability, $expecteduserlist['capability']);
        $this->assertEquals(2, count($expecteduserlist['users']));
        // We forced an access to the course via setUser.
        $this->assertNotEquals(0, $expecteduserlist['users'][0]['lastcourseaccess']);
        $this->assertEquals(0, $expecteduserlist['users'][1]['lastcourseaccess']);

        // Force last access.
        $timenow = time();
        $lastaccess = array(
            'userid' => $expecteduserlist['users'][1]['id'],
            'courseid' => $data->course->id,
            'timeaccess' => $timenow
        );
        $DB->insert_record('user_lastaccess', $lastaccess);

        $result = core_enrol_external::get_enrolled_users_with_capability($parameters, array());
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_enrol_external::get_enrolled_users_with_capability_returns(), $result);

        // Check the result set.
        $expecteduserlist = $result[0];
        $this->assertEquals(2, count($expecteduserlist['users']));
        $this->assertNotEquals(0, $expecteduserlist['users'][0]['lastcourseaccess']);
        $this->assertEquals($timenow, $expecteduserlist['users'][1]['lastcourseaccess']);
    }

    /**
     * dataProvider for test_submit_user_enrolment_form().
     */
    public function submit_user_enrolment_form_provider() {
        $now = new \DateTime();

        $nextmonth = clone($now);
        $nextmonth->add(new \DateInterval('P1M'));

        return [
            'Invalid data' => [
                'customdata' => [
                    'status' => ENROL_USER_ACTIVE,
                    'timestart' => [
                        'day' => $now->format('j'),
                        'month' => $now->format('n'),
                        'year' => $now->format('Y'),
                        'hour' => $now->format('G'),
                        'minute' => 0,
                        'enabled' => 1,
                    ],
                    'timeend' => [
                        'day' => $now->format('j'),
                        'month' => $now->format('n'),
                        'year' => $now->format('Y'),
                        'hour' => $now->format('G'),
                        'minute' => 0,
                        'enabled' => 1,
                    ],
                ],
                'expectedresult' => false,
                'validationerror' => true,
            ],
            'Valid data' => [
                'customdata' => [
                    'status' => ENROL_USER_ACTIVE,
                    'timestart' => [
                        'day' => $now->format('j'),
                        'month' => $now->format('n'),
                        'year' => $now->format('Y'),
                        'hour' => $now->format('G'),
                        'minute' => 0,
                        'enabled' => 1,
                    ],
                    'timeend' => [
                        'day' => $nextmonth->format('j'),
                        'month' => $nextmonth->format('n'),
                        'year' => $nextmonth->format('Y'),
                        'hour' => $nextmonth->format('G'),
                        'minute' => 0,
                        'enabled' => 1,
                    ],
                ],
                'expectedresult' => true,
                'validationerror' => false
            ],
            'Suspend user' => [
                'customdata' => [
                    'status' => ENROL_USER_SUSPENDED,
                ],
                'expectedresult' => true,
                'validationerror' => false
            ],
        ];
    }

    /**
     * @param array $customdata The data we are providing to the webservice.
     * @param bool $expectedresult The result we are expecting to receive from the webservice.
     * @param bool $validationerror The validationerror we are expecting to receive from the webservice.
     * @dataProvider submit_user_enrolment_form_provider
     */
    public function test_submit_user_enrolment_form($customdata, $expectedresult, $validationerror) {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        $datagen = $this->getDataGenerator();

        /** @var \enrol_manual_plugin $manualplugin */
        $manualplugin = enrol_get_plugin('manual');

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        $course = $datagen->create_course();
        $user = $datagen->create_user();
        $teacher = $datagen->create_user();

        $instanceid = null;
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $inst) {
            if ($inst->enrol == 'manual') {
                $instanceid = (int)$inst->id;
                break;
            }
        }
        if (empty($instanceid)) {
            $instanceid = $manualplugin->add_default_instance($course);
            if (empty($instanceid)) {
                $instanceid = $manualplugin->add_instance($course);
            }
        }
        $this->assertNotNull($instanceid);

        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $manualplugin->enrol_user($instance, $user->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $manualplugin->enrol_user($instance, $teacher->id, $teacherroleid, 0, 0, ENROL_USER_ACTIVE);
        $ueid = (int) $DB->get_field(
                'user_enrolments',
                'id',
                ['enrolid' => $instance->id, 'userid' => $user->id],
                MUST_EXIST
        );

        // Login as teacher.
        $teacher->ignoresesskey = true;
        $this->setUser($teacher);

        $formdata = [
            'ue'        => $ueid,
            'ifilter'   => 0,
            'status'    => null,
            'timestart' => null,
            'duration'  => null,
            'timeend'   => null,
        ];

        $formdata = array_merge($formdata, $customdata);

        require_once("$CFG->dirroot/enrol/editenrolment_form.php");
        $formdata = enrol_user_enrolment_form::mock_generate_submit_keys($formdata);

        $querystring = http_build_query($formdata, '', '&');

        $result = external_api::clean_returnvalue(
                core_enrol_external::submit_user_enrolment_form_returns(),
                core_enrol_external::submit_user_enrolment_form($querystring)
        );

        $this->assertEqualsCanonicalizing(
                ['result' => $expectedresult, 'validationerror' => $validationerror],
                $result);

        if ($result['result']) {
            $ue = $DB->get_record('user_enrolments', ['id' => $ueid], '*', MUST_EXIST);
            $this->assertEquals($formdata['status'], $ue->status);
        }
    }

    /**
     * Test for core_enrol_external::unenrol_user_enrolment().
     */
    public function test_unenerol_user_enrolment() {
        global $DB;

        $this->resetAfterTest(true);
        $datagen = $this->getDataGenerator();

        /** @var \enrol_manual_plugin $manualplugin */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotNull($manualplugin);

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        $course = $datagen->create_course();
        $user = $datagen->create_user();
        $teacher = $datagen->create_user();

        $instanceid = null;
        $instances = enrol_get_instances($course->id, true);
        foreach ($instances as $inst) {
            if ($inst->enrol == 'manual') {
                $instanceid = (int)$inst->id;
                break;
            }
        }
        if (empty($instanceid)) {
            $instanceid = $manualplugin->add_default_instance($course);
            if (empty($instanceid)) {
                $instanceid = $manualplugin->add_instance($course);
            }
        }
        $this->assertNotNull($instanceid);

        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $manualplugin->enrol_user($instance, $user->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $manualplugin->enrol_user($instance, $teacher->id, $teacherroleid, 0, 0, ENROL_USER_ACTIVE);
        $ueid = (int)$DB->get_field(
            'user_enrolments',
            'id',
            ['enrolid' => $instance->id, 'userid' => $user->id],
            MUST_EXIST
        );

        // Login as teacher.
        $this->setUser($teacher);

        // Invalid data by passing invalid ueid.
        $data = core_enrol_external::unenrol_user_enrolment(101010);
        $data = external_api::clean_returnvalue(core_enrol_external::unenrol_user_enrolment_returns(), $data);
        $this->assertFalse($data['result']);
        $this->assertNotEmpty($data['errors']);

        // Valid data.
        $data = core_enrol_external::unenrol_user_enrolment($ueid);
        $data = external_api::clean_returnvalue(core_enrol_external::unenrol_user_enrolment_returns(), $data);
        $this->assertTrue($data['result']);
        $this->assertEmpty($data['errors']);

        // Check unenrol user enrolment.
        $ue = $DB->count_records('user_enrolments', ['id' => $ueid]);
        $this->assertEquals(0, $ue);
    }

    /**
     * Test for core_enrol_external::test_search_users().
     */
    public function test_search_users() {
        global $DB;

        $this->resetAfterTest(true);
        $datagen = $this->getDataGenerator();

        /** @var \enrol_manual_plugin $manualplugin */
        $manualplugin = enrol_get_plugin('manual');
        $this->assertNotNull($manualplugin);

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);

        $course1 = $datagen->create_course();
        $course2 = $datagen->create_course();

        $user1 = $datagen->create_user(['firstname' => 'user 1']);
        $user2 = $datagen->create_user(['firstname' => 'user 2']);
        $user3 = $datagen->create_user(['firstname' => 'user 3']);
        $teacher = $datagen->create_user(['firstname' => 'user 4']);

        $instanceid = null;
        $instances = enrol_get_instances($course1->id, true);
        foreach ($instances as $inst) {
            if ($inst->enrol == 'manual') {
                $instanceid = (int)$inst->id;
                break;
            }
        }
        if (empty($instanceid)) {
            $instanceid = $manualplugin->add_default_instance($course1);
            if (empty($instanceid)) {
                $instanceid = $manualplugin->add_instance($course1);
            }
        }
        $this->assertNotNull($instanceid);

        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $manualplugin->enrol_user($instance, $user1->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $manualplugin->enrol_user($instance, $user2->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $manualplugin->enrol_user($instance, $user3->id, $studentroleid, 0, 0, ENROL_USER_ACTIVE);
        $manualplugin->enrol_user($instance, $teacher->id, $teacherroleid, 0, 0, ENROL_USER_ACTIVE);

        $this->setUser($teacher);

        // Search for users in a course with enrolled users.
        $result = core_enrol_external::search_users($course1->id, 'user', true, 0, 30);
        $this->assertCount(4, $result);

        $this->expectException('moodle_exception');
        // Search for users in a course without any enrolled users, shouldn't return anything.
        $result = core_enrol_external::search_users($course2->id, 'user', true, 0, 30);
        $this->assertCount(0, $result);

        // Search for invalid first name.
        $result = core_enrol_external::search_users($course1->id, 'yada yada', true, 0, 30);
        $this->assertCount(0, $result);

        // Test pagination, it should return only 3 users.
        $result = core_enrol_external::search_users($course1->id, 'user', true, 0, 3);
        $this->assertCount(3, $result);

        // Test pagination, it should return only 3 users.
        $result = core_enrol_external::search_users($course1->id, 'user 1', true, 0, 1);
        $result = $result[0];
        $this->assertEquals($user1->id, $result['id']);
        $this->assertEquals($user1->email, $result['email']);
        $this->assertEquals(fullname($user1), $result['fullname']);

        $this->setUser($user1);

        // Search for users in a course with enrolled users.
        $result = core_enrol_external::search_users($course1->id, 'user', true, 0, 30);
        $this->assertCount(4, $result);

        $this->expectException('moodle_exception');
        // Search for users in a course without any enrolled users, shouldn't return anything.
        $result = core_enrol_external::search_users($course2->id, 'user', true, 0, 30);
        $this->assertCount(0, $result);

        // Search for invalid first name.
        $result = core_enrol_external::search_users($course1->id, 'yada yada', true, 0, 30);
        $this->assertCount(0, $result);
    }

    /**
     * Test for core_enrol_external::search_users() when group mode is active.
     * @covers ::search_users
     */
    public function test_search_users_groupmode() {
        global $DB;

        $this->resetAfterTest();
        $datagen = $this->getDataGenerator();

        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'teacher'], MUST_EXIST);

        $course = $datagen->create_course();

        $student1 = $datagen->create_and_enrol($course);
        $student2 = $datagen->create_and_enrol($course);
        $student3 = $datagen->create_and_enrol($course);
        $teacher1 = $datagen->create_and_enrol($course, 'teacher');
        $teacher2 = $datagen->create_and_enrol($course, 'teacher');
        $teacher3 = $datagen->create_and_enrol($course, 'teacher');
        $teacher4 = $datagen->create_and_enrol($course, 'editingteacher');

        // Create 2 groups.
        $group1 = $datagen->create_group(['courseid' => $course->id]);
        $group2 = $datagen->create_group(['courseid' => $course->id]);

        // Add the users to the groups.
        $datagen->create_group_member(['groupid' => $group1->id, 'userid' => $student1->id]);
        $datagen->create_group_member(['groupid' => $group2->id, 'userid' => $student2->id]);
        $datagen->create_group_member(['groupid' => $group2->id, 'userid' => $student3->id]);
        $datagen->create_group_member(['groupid' => $group1->id, 'userid' => $teacher1->id]);
        $datagen->create_group_member(['groupid' => $group2->id, 'userid' => $teacher1->id]);
        $datagen->create_group_member(['groupid' => $group1->id, 'userid' => $teacher2->id]);

        // Create the forum.
        $record = new stdClass();
        $record->introformat = FORMAT_HTML;
        $record->course = $course->id;
        $forum = self::getDataGenerator()->create_module('forum', $record, ['groupmode' => SEPARATEGROUPS]);
        $contextid = $DB->get_field('context', 'id', ['instanceid' => $forum->cmid, 'contextlevel' => CONTEXT_MODULE]);

        $this->setUser($teacher1);
        $result = core_enrol_external::search_users($course->id, 'user', true, 0, 30, $contextid);
        $this->assertCount(5, $result);

        $this->setUser($teacher2);
        $result = core_enrol_external::search_users($course->id, 'user', true, 0, 30, $contextid);
        $this->assertCount(3, $result);

        $this->setUser($teacher3);
        $result = core_enrol_external::search_users($course->id, 'user', true, 0, 30, $contextid);
        $this->assertCount(0, $result);

        $this->setUser($teacher4);
        $result = core_enrol_external::search_users($course->id, 'user', true, 0, 30, $contextid);
        $this->assertCount(7, $result);

        // Now change the group mode to no groups.
        set_coursemodule_groupmode($forum->cmid, NOGROUPS);
        $this->setUser($teacher1);
        $result = core_enrol_external::search_users($course->id, 'user', true, 0, 30, $contextid);
        $this->assertCount(7, $result);
    }

    /**
     * Tests the get_potential_users external function (not too much detail because the back-end
     * is covered in another test).
     */
    public function test_get_potential_users(): void {
        $this->resetAfterTest();

        // Create a couple of custom profile fields, one of which is in user identity.
        $generator = $this->getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text',
                'shortname' => 'researchtopic', 'name' => 'Research topic']);
        $generator->create_custom_profile_field(['datatype' => 'text',
                'shortname' => 'specialid', 'name' => 'Special id']);
        set_config('showuseridentity', 'department,profile_field_specialid');

        // Create a course.
        $course = $generator->create_course();

        // Get enrol id for manual enrol plugin.
        foreach (enrol_get_instances($course->id, true) as $instance) {
            if ($instance->enrol === 'manual') {
                $enrolid = $instance->id;
            }
        }

        // Create a couple of test users.
        $user1 = $generator->create_user(['firstname' => 'Eigh', 'lastname' => 'User',
                'department' => 'Amphibians', 'profile_field_specialid' => 'Q123',
                'profile_field_researchtopic' => 'Frogs']);
        $user2 = $generator->create_user(['firstname' => 'Anne', 'lastname' => 'Other',
                'department' => 'Amphibians', 'profile_field_specialid' => 'Q456',
                'profile_field_researchtopic' => 'Toads']);

        // Do this as admin user.
        $this->setAdminUser();

        // Get potential users and extract the 2 we care about.
        $result = core_enrol_external::get_potential_users($course->id, $enrolid, '', false, 0, 10);
        $result1 = $this->extract_user_from_result($result, $user1->id);
        $result2 = $this->extract_user_from_result($result, $user2->id);

        // Check the fields are the expected ones.
        $this->assertEquals(['id', 'fullname', 'customfields',
                'profileimageurl', 'profileimageurlsmall', 'department'], array_keys($result1));
        $this->assertEquals('Eigh User', $result1['fullname']);
        $this->assertEquals('Amphibians', $result1['department']);

        // Check the custom fields ONLY include the user identity one.
        $fieldvalues = [];
        foreach ($result1['customfields'] as $customfield) {
            $fieldvalues[$customfield['shortname']] = $customfield['value'];
        }
        $this->assertEquals(['specialid'], array_keys($fieldvalues));
        $this->AssertEquals('Q123', $fieldvalues['specialid']);

        // Just check user 2 is the right user.
        $this->assertEquals('Anne Other', $result2['fullname']);
    }

    /**
     * Utility function to get one user out of the get_potential_users result.
     *
     * @param array $result Result array
     * @param int $userid User id
     * @return array Data for that user
     */
    protected function extract_user_from_result(array $result, int $userid): array {
        foreach ($result as $item) {
            if ($item['id'] == $userid) {
                return $item;
            }
        }
        $this->fail('User not in result: ' . $userid);
    }
}
