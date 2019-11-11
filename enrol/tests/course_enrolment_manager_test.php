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
 * Test course_enrolment_manager parts.
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2016 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test course_enrolment_manager parts.
 *
 * @package    core
 * @category   test
 * @copyright  2016 Ruslan Kabalin, Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_course_enrolment_manager_testcase extends advanced_testcase {
    /**
     * The course context used in tests.
     * @var stdClass
     */
    private $course = null;
    /**
     * List of users used in tests.
     * @var array
     */
    private $users = array();
    /**
     * List of groups used in tests.
     * @var array
     */
    private $groups = array();

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/locallib.php');
        $this->setAdminUser();

        $users = array();
        $groups = array();
        // Create the course and the users.
        $course = $this->getDataGenerator()->create_course();
        $users['user0'] = $this->getDataGenerator()->create_user(
                array('username' => 'user0', 'firstname' => 'user0')); // A user without group.
        $users['user1'] = $this->getDataGenerator()->create_user(
                array('username' => 'user1', 'firstname' => 'user1')); // User for group 1.
        $users['user21'] = $this->getDataGenerator()->create_user(
                array('username' => 'user21', 'firstname' => 'user21')); // Two users for group 2.
        $users['user22'] = $this->getDataGenerator()->create_user(
                array('username' => 'user22', 'firstname' => 'user22'));
        $users['userall'] = $this->getDataGenerator()->create_user(
                array('username' => 'userall', 'firstname' => 'userall')); // A user in all groups.
        $users['usertch'] = $this->getDataGenerator()->create_user(
                array('username' => 'usertch', 'firstname' => 'usertch')); // A user with teacher role.

        // Enrol the users in the course.
        $this->getDataGenerator()->enrol_user($users['user0']->id, $course->id, 'student'); // Student.
        $this->getDataGenerator()->enrol_user($users['user1']->id, $course->id, 'student'); // Student.
        $this->getDataGenerator()->enrol_user($users['user21']->id, $course->id, 'student'); // Student.
        $this->getDataGenerator()->enrol_user($users['user22']->id, $course->id, 'student', 'manual', 0, 0, ENROL_USER_SUSPENDED); // Suspended student.
        $this->getDataGenerator()->enrol_user($users['userall']->id, $course->id, 'student'); // Student.
        $this->getDataGenerator()->enrol_user($users['usertch']->id, $course->id, 'editingteacher'); // Teacher.

        // Create 2 groups.
        $groups['group1'] = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $groups['group2'] = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Add the users to the groups.
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $groups['group1']->id, 'userid' => $users['user1']->id));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $groups['group2']->id, 'userid' => $users['user21']->id));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $groups['group2']->id, 'userid' => $users['user22']->id));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $groups['group1']->id, 'userid' => $users['userall']->id));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $groups['group2']->id, 'userid' => $users['userall']->id));

        // Make setup data accessible from test methods.
        $this->course = $course;
        $this->users = $users;
        $this->groups = $groups;

        // Make sample users and not enroll to any course.
        $this->getDataGenerator()->create_user([
                'username' => 'testapiuser1',
                'firstname' => 'testapiuser 1'
        ]);
        $this->getDataGenerator()->create_user([
                'username' => 'testapiuser2',
                'firstname' => 'testapiuser 2'
        ]);
        $this->getDataGenerator()->create_user([
                'username' => 'testapiuser3',
                'firstname' => 'testapiuser 3'
        ]);
    }

    /**
     * Verify get_total_users() returned number of users expected in every situation.
     */
    public function test_get_total_users() {
        global $PAGE;

        $this->resetAfterTest();

        // All users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(6, $totalusers, 'All users must be returned when no filtering is applied.');

        // Student role filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 5);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(5, $totalusers, 'Only students must be returned when student role filtering is applied.');

        // Teacher role filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 3);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(1, $totalusers, 'Only teacher must be returned when teacher role filtering is applied.');

        // Search user filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, 'userall');
        $totalusers = $manager->get_total_users();
        $this->assertEquals(1, $totalusers, 'Only searchable user must be returned when search filtering is applied.');

        // Group 1 filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', $this->groups['group1']->id);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(2, $totalusers, 'Only group members must be returned when group filtering is applied.');

        // Group 2 filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', $this->groups['group2']->id);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(3, $totalusers, 'Only group members must be returned when group filtering is applied.');

        // 'No groups' filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', -1);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(2, $totalusers, 'Only non-group members must be returned when \'no groups\' filtering is applied.');

        // Active users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', 0, ENROL_USER_ACTIVE);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(5, $totalusers, 'Only active users must be returned when active users filtering is applied.');

        // Suspended users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', 0, ENROL_USER_SUSPENDED);
        $totalusers = $manager->get_total_users();
        $this->assertEquals(1, $totalusers, 'Only suspended users must be returned when suspended users filtering is applied.');
    }

    /**
     * Verify get_users() returned number of users expected in every situation.
     */
    public function test_get_users() {
        global $PAGE;

        $this->resetAfterTest();

        // All users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course);
        $users = $manager->get_users('id');
        $this->assertCount(6, $users,  'All users must be returned when no filtering is applied.');
        $this->assertArrayHasKey($this->users['user0']->id, $users);
        $this->assertArrayHasKey($this->users['user1']->id, $users);
        $this->assertArrayHasKey($this->users['user21']->id, $users);
        $this->assertArrayHasKey($this->users['user22']->id, $users);
        $this->assertArrayHasKey($this->users['userall']->id, $users);
        $this->assertArrayHasKey($this->users['usertch']->id, $users);

        // Student role filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 5);
        $users = $manager->get_users('id');
        $this->assertCount(5, $users, 'Only students must be returned when student role filtering is applied.');
        $this->assertArrayHasKey($this->users['user0']->id, $users);
        $this->assertArrayHasKey($this->users['user1']->id, $users);
        $this->assertArrayHasKey($this->users['user21']->id, $users);
        $this->assertArrayHasKey($this->users['user22']->id, $users);
        $this->assertArrayHasKey($this->users['userall']->id, $users);

        // Teacher role filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 3);
        $users = $manager->get_users('id');
        $this->assertCount(1, $users, 'Only teacher must be returned when teacher role filtering is applied.');
        $this->assertArrayHasKey($this->users['usertch']->id, $users);

        // Search user filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, 'userall');
        $users = $manager->get_users('id');
        $this->assertCount(1, $users, 'Only searchable user must be returned when search filtering is applied.');
        $this->assertArrayHasKey($this->users['userall']->id, $users);

        // Group 1 filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', $this->groups['group1']->id);
        $users = $manager->get_users('id');
        $this->assertCount(2, $users, 'Only group members must be returned when group filtering is applied.');
        $this->assertArrayHasKey($this->users['user1']->id, $users);
        $this->assertArrayHasKey($this->users['userall']->id, $users);

        // Group 2 filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', $this->groups['group2']->id);
        $users = $manager->get_users('id');
        $this->assertCount(3, $users, 'Only group members must be returned when group filtering is applied.');
        $this->assertArrayHasKey($this->users['user21']->id, $users);
        $this->assertArrayHasKey($this->users['user22']->id, $users);
        $this->assertArrayHasKey($this->users['userall']->id, $users);

        // 'No groups' filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', -1);
        $users = $manager->get_users('id');
        $this->assertCount(2, $users, 'Only non-group members must be returned when \'no groups\' filtering is applied.');
        $this->assertArrayHasKey($this->users['user0']->id, $users);
        $this->assertArrayHasKey($this->users['usertch']->id, $users);

        // Active users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', 0, ENROL_USER_ACTIVE);
        $users = $manager->get_users('id');
        $this->assertCount(5, $users, 'Only active users must be returned when active users filtering is applied.');
        $this->assertArrayHasKey($this->users['user0']->id, $users);
        $this->assertArrayHasKey($this->users['user1']->id, $users);
        $this->assertArrayHasKey($this->users['user21']->id, $users);
        $this->assertArrayHasKey($this->users['userall']->id, $users);
        $this->assertArrayHasKey($this->users['usertch']->id, $users);

        // Suspended users filtering.
        $manager = new course_enrolment_manager($PAGE, $this->course, null, 0, '', 0, ENROL_USER_SUSPENDED);
        $users = $manager->get_users('id');
        $this->assertCount(1, $users, 'Only suspended users must be returned when suspended users filtering is applied.');
        $this->assertArrayHasKey($this->users['user22']->id, $users);
    }

    /**
     * Test get_potential_users without returnexactcount param.
     *
     * @dataProvider search_users_provider
     *
     * @param int $perpage Number of users per page.
     * @param bool $returnexactcount Return the exact count or not.
     * @param int $expectedusers Expected number of users return.
     * @param int $expectedtotalusers Expected total of users in database.
     * @param bool $expectedmoreusers Expected for more users return or not.
     */
    public function test_get_potential_users($perpage, $returnexactcount, $expectedusers, $expectedtotalusers, $expectedmoreusers) {
        global $DB, $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        $enrol = $DB->get_record('enrol', array('courseid' => $this->course->id, 'enrol' => 'manual'));
        $manager = new course_enrolment_manager($PAGE, $this->course);
        $users = $manager->get_potential_users($enrol->id,
                'testapiuser',
                true,
                0,
                $perpage,
                0,
                $returnexactcount);

        $this->assertCount($expectedusers, $users['users']);
        $this->assertEquals($expectedmoreusers, $users['moreusers']);
        if ($returnexactcount) {
            $this->assertArrayHasKey('totalusers', $users);
            $this->assertEquals($expectedtotalusers, $users['totalusers']);
        } else {
            $this->assertArrayNotHasKey('totalusers', $users);
        }
    }

    /**
     * Test search_other_users with returnexactcount param.
     *
     * @dataProvider search_users_provider
     *
     * @param int $perpage Number of users per page.
     * @param bool $returnexactcount Return the exact count or not.
     * @param int $expectedusers Expected number of users return.
     * @param int $expectedtotalusers Expected total of users in database.
     * @param bool $expectedmoreusers Expected for more users return or not.
     */
    public function test_search_other_users($perpage, $returnexactcount, $expectedusers, $expectedtotalusers, $expectedmoreusers) {
        global $PAGE;
        $this->resetAfterTest();
        $this->setAdminUser();

        $manager = new course_enrolment_manager($PAGE, $this->course);
        $users = $manager->search_other_users(
                'testapiuser',
                true,
                0,
                $perpage,
                $returnexactcount);

        $this->assertCount($expectedusers, $users['users']);
        $this->assertEquals($expectedmoreusers, $users['moreusers']);
        if ($returnexactcount) {
            $this->assertArrayHasKey('totalusers', $users);
            $this->assertEquals($expectedtotalusers, $users['totalusers']);
        } else {
            $this->assertArrayNotHasKey('totalusers', $users);
        }
    }

    /**
     * Test case for test_get_potential_users, test_search_other_users and test_search_users tests.
     *
     * @return array Dataset
     */
    public function search_users_provider() {
        return [
                [2, false, 2, 3, true],
                [5, false, 3, 3, false],
                [2, true, 2, 3, true],
                [5, true, 3, 3, false]
        ];
    }

    /**
     * Test search_users function.
     *
     * @dataProvider search_users_provider
     *
     * @param int $perpage Number of users per page.
     * @param bool $returnexactcount Return the exact count or not.
     * @param int $expectedusers Expected number of users return.
     * @param int $expectedtotalusers Expected total of users in database.
     * @param bool $expectedmoreusers Expected for more users return or not.
     */
    public function test_search_users($perpage, $returnexactcount, $expectedusers, $expectedtotalusers, $expectedmoreusers) {
        global $PAGE;
        $this->resetAfterTest();

        $this->getDataGenerator()->create_and_enrol($this->course, 'student', ['firstname' => 'sutest 1']);
        $this->getDataGenerator()->create_and_enrol($this->course, 'student', ['firstname' => 'sutest 2']);
        $this->getDataGenerator()->create_and_enrol($this->course, 'student', ['firstname' => 'sutest 3']);

        $manager = new course_enrolment_manager($PAGE, $this->course);
        $users = $manager->search_users(
            'sutest',
            true,
            0,
            $perpage,
            $returnexactcount
        );

        $this->assertCount($expectedusers, $users['users']);
        $this->assertEquals($expectedmoreusers, $users['moreusers']);
        if ($returnexactcount) {
            $this->assertArrayHasKey('totalusers', $users);
            $this->assertEquals($expectedtotalusers, $users['totalusers']);
        } else {
            $this->assertArrayNotHasKey('totalusers', $users);
        }
    }
}
