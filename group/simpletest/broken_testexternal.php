<?php
/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *         http://moodle.com
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details:
 *
 *         http://www.gnu.org/copyleft/gpl.html
 *
 * @category  Moodle
 * @package   group
 * @copyright Copyright (c) 1999 onwards Martin Dougiamas     http://dougiamas.com
 * @license   http://www.gnu.org/copyleft/gpl.html     GNU GPL License
 */

/**
 * Unit tests for (some of) user/external.php.
 * WARNING: DO NOT RUN THIS TEST ON A PRODUCTION SITE
 * => DO NOT UNCOMMENT THESE TEST FUNCTIONS EXCEPT IF YOU ARE DEVELOPER
 * => NONE OF THESE TEST FUNCTIONS SHOULD BE UNCOMMENT BY DEFAULT
 * => THESE TEST FUNCTIONS ARE DEPENDENT BETWEEEN EACH OTHER
 * => THE FUNCTION ORDER MUST NOT BE CHANGED
 *
 *
 * THIS TEST NEEDS TO BE RUN AS ADMIN!!!
 * @author Jerome Mouneyrac
 */

require_once($CFG->dirroot . '/group/external.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/user/lib.php');

class group_external_test extends UnitTestCase {
/*
    var $realDB;
    var $group;
    var $group2;
    var $userid1;
    var $userid2;
    var $userid3;
    var $userid4;
    var $userid5;
    var $course;
    var $categoryid;
    var $roleid;
    var $context;
    public  static $includecoverage = array('user/lib.php');

    function setUp() {
        global $DB;

        /// create a category
        $tempcat = new object();
        $tempcat->name = 'categoryForTestGroup';
        $this->categoryid = $DB->insert_record('course_categories', $tempcat);

        /// create a course
        $course->category =  $this->categoryid;
        $course->summary = 'Test course for Group';
        $course->format = 'weeks';
        $course->numsections = '10';
        $course->startdate = mktime();
        $course->name = "Test course for Group";
        $course->fullname = "Test course for Group";
        $course->shortname = "TestCourseForGroup";
        $course = create_course($course);
        $this->course = $course;


        /// create two students
        $user = new stdClass();
        $user->username = 'mockuserfortestingXX';
        $user->firstname = 'mockuserfortestingX_firstname';
        $user->lastname = 'mockuserfortestingX_lastname';
        $user->email = 'mockuserfortestingX@moodle.com';
        $user->password = 'mockuserfortestingX_password';
        $this->userid1 = create_user($user);
        $user->username = 'mockuserfortestingXY';
        $user->firstname = 'mockuserfortestingY_firstname';
        $user->lastname = 'mockuserfortestingY_lastname';
        $user->email = 'mockuserfortestingY@moodle.com';
        $user->password = 'mockuserfortestingY_password';
        $this->userid2 = create_user($user);

        //create some more test users (not add yet to any group)
        $user = new stdClass();
        $user->username = 'mockuserfortestingZ';
        $user->firstname = 'mockuserfortestingZ_firstname';
        $user->lastname = 'mockuserfortestingZ_lastname';
        $user->email = 'mockuserfortestingZ@moodle.com';
        $user->password = 'mockuserfortestingZ_password';
        $this->userid3 = create_user($user);
        $user = new stdClass();
        $user->username = 'mockuserfortestingZ2';
        $user->firstname = 'mockuserfortestingZ2_firstname';
        $user->lastname = 'mockuserfortestingZ2_lastname';
        $user->email = 'mockuserfortestingZ2@moodle.com';
        $user->password = 'mockuserfortestingZ2_password';
        $this->userid4 = create_user($user);

        //create a user, don't add it to a role or group
        $user = new stdClass();
        $user->username = 'mockuserfortestingZ23';
        $user->firstname = 'mockuserfortestingZ23_firstname';
        $user->lastname = 'mockuserfortestingZ23_lastname';
        $user->email = 'mockuserfortestingZ23@moodle.com';
        $user->password = 'mockuserfortestingZ23_password';
        $this->userid5 = create_user($user);

        //we're creating a new test role with viewcourse capabilyt
        $this->context = $DB->get_record('context',array('contextlevel' => 50, 'instanceid' => $this->course->id));
        $this->roleid = create_role('testrole', 'testrole', 'testrole');
        assign_capability('moodle/course:view', CAP_ALLOW, $this->roleid, $this->context->id);

        //assign the students to this role
        role_assign($this->roleid, $this->userid1, null, $this->context->id);
        role_assign($this->roleid, $this->userid2, null, $this->context->id);
        role_assign($this->roleid, $this->userid3, null, $this->context->id);
        role_assign($this->roleid, $this->userid4, null, $this->context->id);

        /// create a group with these two students
        $this->group = new stdClass();
        $this->group->courseid = $this->course->id;
        $this->group->name = "Unit Test group";
        $this->group->id = groups_create_group( $this->group, false);

        /// create a group with one of these students
        $this->group2 = new stdClass();
        $this->group2->courseid = $this->course->id;
        $this->group2->name = "Unit Test group 2";
        $this->group2->id = groups_create_group( $this->group2, false);


        //add the two students as member of the group
        groups_add_member($this->group->id, $this->userid1);
        groups_add_member($this->group->id, $this->userid2);
        groups_add_member($this->group2->id, $this->userid1);

    }

    function tearDown() {
        global $DB;

        /// delete the course
        delete_course($this->course, false);

        /// delete the category
        $DB->delete_records('course_categories',array('id' =>  $this->categoryid));

        /// delete the two students
        $user = $DB->get_record('user', array('username'=>'mockuserfortestingXX', 'mnethostid'=>1));
        delete_user($user);
        $user = $DB->get_record('user', array('username'=>'mockuserfortestingXY', 'mnethostid'=>1));
        delete_user($user);

        /// delete other test users
        $user = $DB->get_record('user', array('username'=>'mockuserfortestingZ', 'mnethostid'=>1));
        delete_user($user);
        $user = $DB->get_record('user', array('username'=>'mockuserfortestingZ2', 'mnethostid'=>1));
        delete_user($user);

        //delete the user without group
        $user = $DB->get_record('user', array('username'=>'mockuserfortestingZ23', 'mnethostid'=>1));
        delete_user($user);

        //delete role
        delete_role($this->roleid);
    }

    function test_create_groups() {
        /// create two different groups
        $params = array();
        $group = array('groupname' => 'Create Unit Test Group 1', 'courseid' => $this->course->id);
        $params[] = $group;
        $group = array('groupname' => 'Create Unit Test Group 2', 'courseid' => $this->course->id);
        $params[] = $group;
        $groupids = group_external::create_groups($params);
        $this->assertEqual(sizeof($groupids), 2);
        $this->assertIsA($groupids[key($groupids)], "integer");
        $this->assertNotNull($groupids[key($groupids)]);

        /// create a course with a not existing course id
        $params = array();
        $group = array('groupname' => 'Create Unit Test Group 3', 'courseid' => 6544656);
        $params[] = $group;
        $this->expectException(new moodle_exception('coursedoesntexistcannotcreategroup'));
        $groupids = group_external::create_groups($params);
    }

    function test_get_groups() {
        /// retrieve the two groups
        $params = array($this->group->id, $this->group2->id);
        $groups = group_external::get_groups($params);
        $this->assertEqual(sizeof($groups), 2);
        $group = $groups[key($groups)];
        next($groups);
        $group2 = $groups[key($groups)];
        $this->assertEqual($group->id, $this->group->id);
        $this->assertEqual($group->courseid, $this->group->courseid);
        $this->assertEqual($group->name, $this->group->name);
        $this->assertEqual($group2->id, $this->group2->id);
        $this->assertEqual($group2->courseid, $this->group2->courseid);
        $this->assertEqual($group2->name, $this->group2->name);
    }

    function test_add_group_members() {
        //add the two members without group
        $params = array(array("groupid" => $this->group->id, "userid" => $this->userid3), array("groupid" => $this->group->id, "userid" => $this->userid4));
        $result = group_external::add_groupmembers($params);
        $this->assertEqual($result, true);

        //add them a new time
        $params = array(array("groupid" => $this->group->id, "userid" => $this->userid3), array("groupid" => $this->group->id, "userid" => $this->userid4));
        $result = group_external::add_groupmembers($params);
        $this->assertEqual($result, true);

        //One of the userid doesn't exist
        $params = array(array("groupid" => $this->group->id, "userid" => 654685), array("groupid" => $this->group->id, "userid" => $this->userid4));
        $this->expectException(new moodle_exception('useriddoesntexist'));
        $result = group_external::add_groupmembers($params);
    }

    function test_add_group_members2() {
        //the group id doesn't exist
        $params = array(array("groupid" => 6465465, "userid" => $this->userid3), array("groupid" => $this->group->id, "userid" => $this->userid4));
        $this->expectException(new moodle_exception('cannotaddmembergroupiddoesntexist'));
        $result = group_external::add_groupmembers($params);
    }

    function test_add_group_members3() {
        //the user is not a participant
        $params = array(array("groupid" => $this->group->id, "userid" => $this->userid5));
        $this->expectException(new moodle_exception('userisnotaparticipant'));
        $result = group_external::add_groupmembers($params);

    }

    function test_get_groupmembers() {
        $params = array($this->group->id, $this->group2->id);
        $groups = group_external::get_groupmembers($params);
        $this->assertEqual(sizeof($groups), 2);
        $this->assertEqual(sizeof($groups[0]['members']), 2);
        $this->assertEqual(sizeof($groups[1]['members']), 1);
    }

    function test_delete_group_members() {
        //One of the userid doesn't exist
        $params = array(array("groupid" => $this->group->id, "userid" => 654685), array("groupid" => $this->group->id, "userid" => $this->userid2));
        $this->expectException(new moodle_exception('useriddoesntexist'));
        $result = group_external::delete_groupmembers($params);
    }

    function test_delete_group_members2() {
        //the group id doesn't exist
        $params = array(array("groupid" => 6465465, "userid" => $this->userid1), array("groupid" => $this->group->id, "userid" => $this->userid2));
        $this->expectException(new moodle_exception('cannotaddmembergroupiddoesntexist'));
        $result = group_external::delete_groupmembers($params);
    }

    function test_delete_group_members3() {
        //delete members from group
        $params = array(array("groupid" => $this->group->id, "userid" => $this->userid1), array("groupid" => $this->group->id, "userid" => $this->userid2));
        $result = group_external::delete_groupmembers($params);
        $this->assertEqual($result, true);
    }

    function test_delete_groups() {
        $params = array($this->group->id, $this->group2->id);
        $result = group_external::delete_groups($params);
        $this->assertEqual($result, true);

        //Exception: delete same groups
        $params = array($this->group->id, $this->group2->id);
        $this->expectException(new moodle_exception('groupiddoesntexistcannotdelete'));
        $result = group_external::delete_groups($params);
    }
*/
}
