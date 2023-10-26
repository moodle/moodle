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

namespace core;

/**
 * Unit tests for lib/grouplib.php
 *
 * @package    core
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grouplib_test extends \advanced_testcase {

    public function test_groups_get_group_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /*
         * Test with an empty and a null idnumber.
         */
        // An empty idnumber should always return a false value.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Even when a group exists which also has an empty idnumber.
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /*
         * Test with a valid idnumber.
         */
        // There is no matching idnumber at present.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search.
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals($group, groups_get_group_by_idnumber($course->id, $idnumber1));

        // An empty idnumber should still return false.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        /*
         * Test with another idnumber.
         */
        // There is no matching idnumber at present.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should now have a valid group returned by the idnumber search.
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals($group, groups_get_group_by_idnumber($course->id, $idnumber2));

        /*
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course.
         */

        // Create a second course.
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course.
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_group_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course.
        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals($group, groups_get_group_by_idnumber($course->id, $idnumber1));

        $group = $generator->create_group(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals($group, groups_get_group_by_idnumber($course->id, $idnumber2));
    }

    public function test_groups_get_grouping_by_idnumber() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $idnumber1 = 'idnumber1';
        $idnumber2 = 'idnumber2';

        /*
         * Test with an empty and a null idnumber.
         */
        // An empty idnumber should always return a false value.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Even when a grouping exists which also has an empty idnumber.
        $generator->create_grouping(array('courseid' => $course->id));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /*
         * Test with a valid idnumber
         */
        // There is no matching idnumber at present.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));

        // We should now have a valid group returned by the idnumber search.
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals($grouping, groups_get_grouping_by_idnumber($course->id, $idnumber1));

        // An empty idnumber should still return false.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        /*
         * Test with another idnumber.
         */
        // There is no matching idnumber at present.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should now have a valid grouping returned by the idnumber search.
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals($grouping, groups_get_grouping_by_idnumber($course->id, $idnumber2));

        /*
         * Grouping idnumbers are unique within a course so test that we don't
         * retrieve groupings for the first course.
         */

        // Create a second course.
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty idnumber should always return a false value.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, null));

        // Our existing idnumbers shouldn't be returned here as we're in a different course.
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber1));
        $this->assertFalse(groups_get_grouping_by_idnumber($course->id, $idnumber2));

        // We should be able to reuse the idnumbers again since this is a different course.
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber1));
        $this->assertEquals($grouping, groups_get_grouping_by_idnumber($course->id, $idnumber1));

        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'idnumber' => $idnumber2));
        $this->assertEquals($grouping, groups_get_grouping_by_idnumber($course->id, $idnumber2));
    }

    public function test_groups_get_members_ids_sql() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group = $generator->create_group(array('courseid' => $course->id));
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);

        list($sql, $params) = groups_get_members_ids_sql($group->id);

        // Test an empty group.
        $users = $DB->get_records_sql($sql, $params);
        $this->assertFalse(array_key_exists($student1->id, $users));

        // Test with a group member.
        groups_add_member($group->id, $student1->id);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertTrue(array_key_exists($student1->id, $users));
    }

    public function test_groups_get_members_ids_sql_multiple_groups() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $groupids = [
            $group1->id,
            $group2->id,
        ];
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);

        list($sql, $params) = groups_get_members_ids_sql($groupids);

        // Test an empty group.
        $users = $DB->get_records_sql($sql, $params);
        $this->assertFalse(array_key_exists($student1->id, $users));

        // Test with a member of one of the two group.
        groups_add_member($group1->id, $student1->id);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertTrue(array_key_exists($student1->id, $users));

        // Test with members of two groups.
        groups_add_member($group2->id, $student2->id);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertTrue(array_key_exists($student1->id, $users));
        $this->assertTrue(array_key_exists($student2->id, $users));
    }

    public function test_groups_get_members_ids_sql_multiple_groups_join_types() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $student3 = $generator->create_user();
        $student4 = $generator->create_user();
        $student5 = $generator->create_user();
        $student6 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $group3 = $generator->create_group(array('courseid' => $course->id));
        // Only groups 1 and 2 specified in SQL (group 3 helps cover the None case).
        $groupids = [
            $group1->id,
            $group2->id,
        ];
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);
        $plugin->enrol_user($instance, $student3->id, $role->id);
        $plugin->enrol_user($instance, $student4->id, $role->id);
        $plugin->enrol_user($instance, $student5->id, $role->id);
        $plugin->enrol_user($instance, $student6->id, $role->id);

        // Generate SQL with the different groups join types for members of group1 and group2.
        list($sqlany, $paramsany) = groups_get_members_ids_sql($groupids, null, GROUPS_JOIN_ANY);
        list($sqlall, $paramsall) = groups_get_members_ids_sql($groupids, null, GROUPS_JOIN_ALL);
        list($sqlnone, $paramsnone) = groups_get_members_ids_sql($groupids, null, GROUPS_JOIN_NONE);

        // Any - Test empty groups, no matches.
        $users = $DB->get_records_sql($sqlany, $paramsany);
        $this->assertFalse(array_key_exists($student1->id, $users));
        $this->assertFalse(array_key_exists($student2->id, $users));
        $this->assertFalse(array_key_exists($student3->id, $users));
        $this->assertFalse(array_key_exists($student4->id, $users));
        $this->assertFalse(array_key_exists($student5->id, $users));
        $this->assertFalse(array_key_exists($student6->id, $users));

        // All - Test empty groups, no matches.
        $users = $DB->get_records_sql($sqlall, $paramsall);
        $this->assertFalse(array_key_exists($student1->id, $users));
        $this->assertFalse(array_key_exists($student2->id, $users));
        $this->assertFalse(array_key_exists($student3->id, $users));
        $this->assertFalse(array_key_exists($student4->id, $users));
        $this->assertFalse(array_key_exists($student5->id, $users));
        $this->assertFalse(array_key_exists($student6->id, $users));

        // None - Test empty groups, all match.
        $users = $DB->get_records_sql($sqlnone, $paramsnone);
        $this->assertTrue(array_key_exists($student1->id, $users));
        $this->assertTrue(array_key_exists($student2->id, $users));
        $this->assertTrue(array_key_exists($student3->id, $users));
        $this->assertTrue(array_key_exists($student4->id, $users));
        $this->assertTrue(array_key_exists($student5->id, $users));
        $this->assertTrue(array_key_exists($student6->id, $users));

        // Assign various group member combinations.
        groups_add_member($group1->id, $student1->id);
        groups_add_member($group1->id, $student2->id);
        groups_add_member($group1->id, $student3->id);
        groups_add_member($group2->id, $student2->id);
        groups_add_member($group2->id, $student3->id);
        groups_add_member($group2->id, $student4->id);
        groups_add_member($group3->id, $student5->id);

        // Any - Test students in one or both of groups 1 and 2 matched.
        $users = $DB->get_records_sql($sqlany, $paramsany);
        $this->assertTrue(array_key_exists($student1->id, $users));
        $this->assertTrue(array_key_exists($student2->id, $users));
        $this->assertTrue(array_key_exists($student3->id, $users));
        $this->assertTrue(array_key_exists($student4->id, $users));
        $this->assertFalse(array_key_exists($student5->id, $users));
        $this->assertFalse(array_key_exists($student6->id, $users));

        // All - Test only students in both groups 1 and 2 matched.
        $users = $DB->get_records_sql($sqlall, $paramsall);
        $this->assertTrue(array_key_exists($student2->id, $users));
        $this->assertTrue(array_key_exists($student3->id, $users));
        $this->assertFalse(array_key_exists($student1->id, $users));
        $this->assertFalse(array_key_exists($student4->id, $users));
        $this->assertFalse(array_key_exists($student5->id, $users));
        $this->assertFalse(array_key_exists($student6->id, $users));

        // None - Test only students not in group 1 or 2 matched.
        $users = $DB->get_records_sql($sqlnone, $paramsnone);
        $this->assertTrue(array_key_exists($student5->id, $users));
        $this->assertTrue(array_key_exists($student6->id, $users));
        $this->assertFalse(array_key_exists($student1->id, $users));
        $this->assertFalse(array_key_exists($student2->id, $users));
        $this->assertFalse(array_key_exists($student3->id, $users));
        $this->assertFalse(array_key_exists($student4->id, $users));
    }

    public function test_groups_get_members_ids_sql_valid_context() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group = $generator->create_group(array('courseid' => $course->id));
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);

        // Add student1 to the group.
        groups_add_member($group->id, $student1->id);

        // Test with members at any group and with a valid $context.
        list($sql, $params) = groups_get_members_ids_sql(USERSWITHOUTGROUP, $coursecontext);
        $users = $DB->get_records_sql($sql, $params);
        $this->assertFalse(array_key_exists($student1->id, $users));
        $this->assertTrue(array_key_exists($student2->id, $users));
    }

    public function test_groups_get_members_ids_sql_empty_context() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group = $generator->create_group(array('courseid' => $course->id));
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);

        // Add student1 to the group.
        groups_add_member($group->id, $student1->id);

        // Test with members at any group and without the $context.
        $this->expectException('coding_exception');
        list($sql, $params) = groups_get_members_ids_sql(USERSWITHOUTGROUP);
    }

    public function test_groups_get_members_ids_sql_invalid_context() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group = $generator->create_group(array('courseid' => $course->id));
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol users in the course.
        $plugin->enrol_user($instance, $student1->id, $role->id);
        $plugin->enrol_user($instance, $student2->id, $role->id);

        // Add student1 to the group.
        groups_add_member($group->id, $student1->id);

        // Test with members at any group and with an invalid $context.
        $syscontext = \context_system::instance();
        $this->expectException('coding_exception');
        list($sql, $params) = groups_get_members_ids_sql(USERSWITHOUTGROUP, $syscontext);
    }

    /**
     * Test retrieving users with concatenated group names from a course
     */
    public function test_groups_get_names_concat_sql(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course containing two groups.
        $course = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        // Create first user, add them to group 1 and group 2.
        $user1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member(['userid' => $user1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $user1->id, 'groupid' => $group2->id]);

        // Create second user, add them to group 1 only.
        $user2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->getDataGenerator()->create_group_member(['userid' => $user2->id, 'groupid' => $group1->id]);

        // Call our method, and assertion.
        [$sql, $params] = groups_get_names_concat_sql($course->id);
        $records = $DB->get_records_sql($sql, $params);

        $this->assertEqualsCanonicalizing([
            (object) [
                'userid' => $user1->id,
                'groupnames' => "{$group1->name}, {$group2->name}",
            ],
            (object) [
                'userid' => $user2->id,
                'groupnames' => $group1->name,
            ],
        ], $records);
    }

    public function test_groups_get_group_by_name() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $name1 = 'Name 1';
        $name2 = 'Name 2';

        // Test with an empty and a null idnumber.
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Even when a group exists.
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Test with a valid name, but one that doesn't exist yet.
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should now have a valid group returned by the name search.
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals($group1->id, groups_get_group_by_name($course->id, $name1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should now have a two valid groups returned by the name search.
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals($group1->id, groups_get_group_by_name($course->id, $name1));
        $this->assertEquals($group2->id, groups_get_group_by_name($course->id, $name2));

        // Delete a group.
        $this->assertTrue(groups_delete_group($group1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertEquals($group2->id, groups_get_group_by_name($course->id, $name2));

        /*
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course.
         */

        // Create a second course.
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty name should always return a false value.
        $this->assertFalse(groups_get_group_by_name($course->id, ''));
        $this->assertFalse(groups_get_group_by_name($course->id, null));

        // Our existing names shouldn't be returned here as we're in a different course.
        $this->assertFalse(groups_get_group_by_name($course->id, $name1));
        $this->assertFalse(groups_get_group_by_name($course->id, $name2));

        // We should be able to reuse the idnumbers again since this is a different course.
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals($group1->id, groups_get_group_by_name($course->id, $name1));

        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals($group2->id, groups_get_group_by_name($course->id, $name2));
    }

    public function test_groups_get_grouping() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));

        $name1 = 'Grouping 1';
        $name2 = 'Grouping 2';

        // Test with an empty and a null idnumber.
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Even when a group exists.
        $generator->create_group(array('courseid' => $course->id));
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Test with a valid name, but one that doesn't exist yet.
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should now have a valid group returned by the name search.
        $group1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals($group1->id, groups_get_grouping_by_name($course->id, $name1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should now have a two valid groups returned by the name search.
        $group2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals($group1->id, groups_get_grouping_by_name($course->id, $name1));
        $this->assertEquals($group2->id, groups_get_grouping_by_name($course->id, $name2));

        // Delete a group.
        $this->assertTrue(groups_delete_grouping($group1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertEquals($group2->id, groups_get_grouping_by_name($course->id, $name2));

        /*
         * Group idnumbers are unique within a course so test that we don't
         * retrieve groups for the first course.
         */

        // Create a second course.
        $course = $generator->create_course(array('category' => $cat->id));

        // An empty name should always return a false value.
        $this->assertFalse(groups_get_grouping_by_name($course->id, ''));
        $this->assertFalse(groups_get_grouping_by_name($course->id, null));

        // Our existing names shouldn't be returned here as we're in a different course.
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name1));
        $this->assertFalse(groups_get_grouping_by_name($course->id, $name2));

        // We should be able to reuse the idnumbers again since this is a different course.
        $group1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name1));
        $this->assertEquals($group1->id, groups_get_grouping_by_name($course->id, $name1));

        $group2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => $name2));
        $this->assertEquals($group2->id, groups_get_grouping_by_name($course->id, $name2));
    }

    public function test_groups_get_course_data() {
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $grouping1 = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping 1'));
        $grouping2 = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping 2'));
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group3 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 3'));
        $group4 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 4'));

        // Assign the groups to groupings.
        $this->assertTrue(groups_assign_grouping($grouping1->id, $group1->id));
        $this->assertTrue(groups_assign_grouping($grouping1->id, $group2->id));
        $this->assertTrue(groups_assign_grouping($grouping2->id, $group3->id));
        $this->assertTrue(groups_assign_grouping($grouping2->id, $group4->id));

        // Get the data.
        $data = groups_get_course_data($course->id);
        $this->assertInstanceOf('stdClass', $data);
        $this->assertObjectHasAttribute('groups', $data);
        $this->assertObjectHasAttribute('groupings', $data);
        $this->assertObjectHasAttribute('mappings', $data);

        // Test we have the expected items returns.
        $this->assertCount(4, $data->groups);
        $this->assertCount(2, $data->groupings);
        $this->assertCount(4, $data->mappings);

        // Check we have the expected groups.
        $this->assertArrayHasKey($group1->id, $data->groups);
        $this->assertArrayHasKey($group2->id, $data->groups);
        $this->assertArrayHasKey($group3->id, $data->groups);
        $this->assertArrayHasKey($group4->id, $data->groups);

        // Test a group-id is mapped correctly.
        $this->assertSame($group3->name, $data->groups[$group3->id]->name);

        // Check we have the expected number of groupings.
        $this->assertArrayHasKey($grouping1->id, $data->groupings);
        $this->assertArrayHasKey($grouping2->id, $data->groupings);

        // Test a grouping-id is mapped correctly.
        $this->assertEquals($grouping2->name, $data->groupings[$grouping2->id]->name);

        // Test that all of the mappings are correct.
        $grouping1maps = 0;
        $grouping2maps = 0;
        $group1maps = 0;
        $group2maps = 0;
        $group3maps = 0;
        $group4maps = 0;
        foreach ($data->mappings as $mapping) {
            if ($mapping->groupingid === $grouping1->id) {
                $grouping1maps++;
                $this->assertContainsEquals($mapping->groupid, array($group1->id, $group2->id));
            } else if ($mapping->groupingid === $grouping2->id) {
                $grouping2maps++;
                $this->assertContainsEquals($mapping->groupid, array($group3->id, $group4->id));
            } else {
                $this->fail('Unexpected groupingid');
            }
            switch ($mapping->groupid) {
                case $group1->id : $group1maps++; break;
                case $group2->id : $group2maps++; break;
                case $group3->id : $group3maps++; break;
                case $group4->id : $group4maps++; break;
            }
        }
        $this->assertEquals(2, $grouping1maps);
        $this->assertEquals(2, $grouping2maps);
        $this->assertEquals(1, $group1maps);
        $this->assertEquals(1, $group2maps);
        $this->assertEquals(1, $group3maps);
        $this->assertEquals(1, $group4maps);

        // Test the groups_get_all_groups which uses this functionality.
        $groups  = groups_get_all_groups($course->id);
        $this->assertCount(4, $groups);
        $this->assertArrayHasKey($group1->id, $groups);
        $this->assertArrayHasKey($group2->id, $groups);
        $this->assertArrayHasKey($group3->id, $groups);
        $this->assertArrayHasKey($group4->id, $groups);

        $groups  = groups_get_all_groups($course->id, null, $grouping1->id);
        $this->assertCount(2, $groups);
        $this->assertArrayHasKey($group1->id, $groups);
        $this->assertArrayHasKey($group2->id, $groups);
        $this->assertArrayNotHasKey($group3->id, $groups);
        $this->assertArrayNotHasKey($group4->id, $groups);

        $groups  = groups_get_all_groups($course->id, null, $grouping2->id);
        $this->assertCount(2, $groups);
        $this->assertArrayNotHasKey($group1->id, $groups);
        $this->assertArrayNotHasKey($group2->id, $groups);
        $this->assertArrayHasKey($group3->id, $groups);
        $this->assertArrayHasKey($group4->id, $groups);

        // Test this function using an alternate column for the result index
        $groups  = groups_get_all_groups($course->id, null, $grouping2->id, 'g.name, g.id');
        $this->assertCount(2, $groups);
        $this->assertArrayNotHasKey($group3->id, $groups);
        $this->assertArrayHasKey($group3->name, $groups);
        $this->assertEquals($group3->id, $groups[$group3->name]->id);
    }

    /**
     * Tests for groups_group_visible.
     */
    public function test_groups_group_visible() {
        global $CFG, $DB;

        $generator = $this->getDataGenerator();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course category, course and groups.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $coursecontext = \context_course::instance($course->id);
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group3 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 3'));
        $group4 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 4'));

        // Create cm.
        $assign = $generator->create_module("assign", array('course' => $course->id));
        $cm = get_coursemodule_from_instance("assign", $assign->id);

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users into the course.
        $generator->enrol_user($user1->id, $course->id);
        $generator->enrol_user($user2->id, $course->id);

        // Assign groups.
        groups_add_member($group1, $user2);

        // Give capability at course level to the user to access all groups.
        $role = $DB->get_field("role", "id", array("shortname" => "manager"));
        $generator->enrol_user($user3->id, $course->id, $role);
        // Make sure the user has the capability.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $role, $coursecontext->id);

        // No groups , not forced.
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // No groups, forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, not forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertFalse($result);
        $result = groups_group_visible($group1->id, $course, null, $user2->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user2->id);
        $this->assertFalse($result); // Requesting all groups.
        $result = groups_group_visible(0, $course, null, $user3->id);
        $this->assertTrue($result); // Requesting all groups.
        $result = groups_group_visible($group1->id, $course, null, $user3->id);
        $this->assertTrue($result); // Make sure user with access to all groups can see any group.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user3->id);
        $this->assertTrue($result); // Make sure user with access to all groups can see any group.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with visible groups.

        // Separate groups, not forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_group_visible($group1->id, $course, null, $user1->id);
        $this->assertFalse($result);
        $result = groups_group_visible($group1->id, $course, null, $user2->id);
        $this->assertTrue($result);
        $result = groups_group_visible(0, $course, null, $user2->id);
        $this->assertFalse($result); // Requesting all groups.
        $result = groups_group_visible(0, $course, null, $user3->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertFalse($result); // Cm with separate groups.
        $result = groups_group_visible($group1->id, $course, $cm, $user2->id);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_group_visible($group1->id, $course, $cm, $user1->id);
        $this->assertTrue($result); // Cm with visible groups.
    }

    function test_groups_get_groupmode() {
        global $DB;
        $generator = $this->getDataGenerator();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course with no groups forcing.
        $course1 = $generator->create_course();

        // Create cm1 with no groups, cm1 with visible groups, cm2 with separate groups and cm3 with visible groups.
        $assign1 = $generator->create_module("assign", array('course' => $course1->id));
        $assign2 = $generator->create_module("assign", array('course' => $course1->id),
                array('groupmode' => SEPARATEGROUPS));
        $assign3 = $generator->create_module("assign", array('course' => $course1->id),
                array('groupmode' => VISIBLEGROUPS));

        // Request data for tests.
        $cm1 = get_coursemodule_from_instance("assign", $assign1->id);
        $cm2 = get_coursemodule_from_instance("assign", $assign2->id);
        $cm3 = get_coursemodule_from_instance("assign", $assign3->id);
        $modinfo = get_fast_modinfo($course1->id);

        // Assert that any method of getting activity groupmode returns the correct result.
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($cm1));
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($cm1, $course1));
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm1->id]));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2, $course1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm2->id]));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($cm3));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($cm3, $course1));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm3->id]));

        // Update the course set the groupmode SEPARATEGROUPS but not forced.
        update_course((object)array('id' => $course1->id, 'groupmode' => SEPARATEGROUPS));
        // Re-request the data from DB.
        $course1 = $DB->get_record('course', array('id' => $course1->id));
        $modinfo = get_fast_modinfo($course1->id);

        // Existing activities are not changed.
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($cm1));
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($cm1, $course1));
        $this->assertEquals(NOGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm1->id]));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2, $course1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm2->id]));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($cm3));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($cm3, $course1));
        $this->assertEquals(VISIBLEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm3->id]));

        // Update the course set the groupmode SEPARATEGROUPS and forced.
        update_course((object)array('id' => $course1->id, 'groupmode' => SEPARATEGROUPS, 'groupmodeforce' => true));
        // Re-request the data from DB.
        $course1 = $DB->get_record('course', array('id' => $course1->id));
        $modinfo = get_fast_modinfo($course1->id);

        // Make sure all activities have separate groups mode now.
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm1, $course1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm1->id]));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm2, $course1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm2->id]));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm3));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($cm3, $course1));
        $this->assertEquals(SEPARATEGROUPS, groups_get_activity_groupmode($modinfo->cms[$cm3->id]));
    }

    /**
     * Tests for groups_allgroups_course_menu() .
     */
    public function test_groups_allgroups_course_menu() {
        global $SESSION;

        $this->resetAfterTest();

        // Generate data.
        $course = $this->getDataGenerator()->create_course();
        $record = new \stdClass();
        $record->courseid = $course->id;
        $group1 = $this->getDataGenerator()->create_group($record);
        $group2 = $this->getDataGenerator()->create_group($record);
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->setUser($user);

        $html = groups_allgroups_course_menu($course, 'someurl.php');
        // Since user is not a part of this group and doesn't have accessallgroups permission,
        // the html should be empty.
        $this->assertEmpty($html);

        groups_add_member($group1->id, $user);
        // Now user can access one of the group. We can't assert an exact match here because of random ids generated by yui. So do
        // partial match to see if all groups are listed or not.
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertStringContainsString(format_string($group1->name), $html);
        $this->assertStringNotContainsString(format_string($group2->name), $html);

        $this->setAdminUser();

        // Now user can access everything.
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertStringContainsString(format_string($group1->name), $html);
        $this->assertStringContainsString(format_string($group2->name), $html);

        // Make sure separate groups mode, doesn't change anything.
        $course->groupmode = SEPARATEGROUPS;
        update_course($course);
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertStringContainsString(format_string($group1->name), $html);
        $this->assertStringContainsString(format_string($group2->name), $html);

        // Make sure Visible groups mode, doesn't change anything.
        $course->groupmode = VISIBLEGROUPS;
        update_course($course);
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertStringContainsString(format_string($group1->name), $html);
        $this->assertStringContainsString(format_string($group2->name), $html);

        // Let us test activegroup changes now.
        $this->setUser($user);
        $SESSION->activegroup[$course->id][VISIBLEGROUPS][$course->defaultgroupingid] = 5;
        groups_allgroups_course_menu($course, 'someurl.php', false); // Do not update session.
        $this->assertSame(5, $SESSION->activegroup[$course->id][VISIBLEGROUPS][$course->defaultgroupingid]);
        groups_allgroups_course_menu($course, 'someurl.php', true, $group1->id); // Update session.
        $this->assertSame($group1->id, $SESSION->activegroup[$course->id][VISIBLEGROUPS][$course->defaultgroupingid]);
        // Try to update session with an invalid groupid. It should not accept the invalid id.
        groups_allgroups_course_menu($course, 'someurl.php', true, 256);
        $this->assertEquals($group1->id, $SESSION->activegroup[$course->id][VISIBLEGROUPS][$course->defaultgroupingid]);
    }

    /**
     * This unit test checks that groups_get_all_groups returns groups in
     * alphabetical order even if they are in a grouping.
     */
    public function test_groups_ordering() {
        $generator = $this->getDataGenerator();
        $this->resetAfterTest();

        // Create a course category and course.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping'));

        // Create groups in reverse order.
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));

        // Assign the groups to the grouping in reverse order.
        $this->assertTrue(groups_assign_grouping($grouping->id, $group2->id));
        $this->assertTrue(groups_assign_grouping($grouping->id, $group1->id));

        // Get all groups and check they are alphabetical.
        $groups = array_values(groups_get_all_groups($course->id, 0));
        $this->assertEquals('Group 1', $groups[0]->name);
        $this->assertEquals('Group 2', $groups[1]->name);

        // Now check the same is true when accessed by grouping.
        $groups = array_values(groups_get_all_groups($course->id, 0, $grouping->id));
        $this->assertEquals('Group 1', $groups[0]->name);
        $this->assertEquals('Group 2', $groups[1]->name);
    }

    /**
     * Tests for groups_get_all_groups when grouping is set and we want members as well.
     */
    public function test_groups_get_all_groups_in_grouping_with_members() {
        $generator = $this->getDataGenerator();
        $this->resetAfterTest();

        // Create courses.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        // Create users.
        $c1user1 = $generator->create_user();
        $c12user1 = $generator->create_user();
        $c12user2 = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($c1user1->id, $course1->id);
        $generator->enrol_user($c12user1->id, $course1->id);
        $generator->enrol_user($c12user1->id, $course2->id);
        $generator->enrol_user($c12user2->id, $course1->id);
        $generator->enrol_user($c12user2->id, $course2->id);

        // Create groupings and groups for course1.
        $c1grouping1 = $generator->create_grouping(array('courseid' => $course1->id));
        $c1grouping2 = $generator->create_grouping(array('courseid' => $course1->id));
        $c1group1 = $generator->create_group(array('courseid' => $course1->id));
        $c1group2 = $generator->create_group(array('courseid' => $course1->id));
        $c1group3 = $generator->create_group(array('courseid' => $course1->id));
        groups_assign_grouping($c1grouping1->id, $c1group1->id);
        groups_assign_grouping($c1grouping1->id, $c1group2->id);
        groups_assign_grouping($c1grouping2->id, $c1group3->id);

        // Create groupings and groups for course2.
        $c2grouping1 = $generator->create_grouping(array('courseid' => $course2->id));
        $c2group1 = $generator->create_group(array('courseid' => $course1->id));
        groups_assign_grouping($c2grouping1->id, $c2group1->id);

        // Assign users to groups.
        $generator->create_group_member(array('groupid' => $c1group1->id, 'userid' => $c1user1->id));
        $generator->create_group_member(array('groupid' => $c1group1->id, 'userid' => $c12user1->id));
        $generator->create_group_member(array('groupid' => $c1group2->id, 'userid' => $c12user2->id));
        $generator->create_group_member(array('groupid' => $c2group1->id, 'userid' => $c12user2->id));

        // Test without userid.
        $groups = groups_get_all_groups($course1->id, null, $c1grouping1->id, 'g.*', true);

        $this->assertEqualsCanonicalizing(
                [$c1group1->id, $c1group2->id],
                array_keys($groups)
        );
        $this->assertEquals(
                [$c1user1->id => $c1user1->id, $c12user1->id => $c12user1->id],
                $groups[$c1group1->id]->members
        );
        $this->assertEquals(
                [$c12user2->id => $c12user2->id],
                $groups[$c1group2->id]->members
        );

        // Test with userid.
        $groups = groups_get_all_groups($course1->id, $c1user1->id, $c1grouping1->id, 'g.*', true);

        $this->assertEquals([$c1group1->id], array_keys($groups));
        $this->assertEqualsCanonicalizing(
                [$c1user1->id, $c12user1->id],
                $groups[$c1group1->id]->members
        );
    }

    /**
     * Tests for groups_get_user_groups() method.
     */
    public function test_groups_get_user_groups() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create courses.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($user1->id, $course1->id);
        $generator->enrol_user($user1->id, $course2->id);
        $generator->enrol_user($user2->id, $course2->id);
        $generator->enrol_user($user3->id, $course2->id);

        // Create groups.
        $group1 = $generator->create_group(array('courseid' => $course1->id));
        $group2 = $generator->create_group(array('courseid' => $course2->id));
        $group3 = $generator->create_group(array('courseid' => $course2->id));

        // Assign users to groups.
        $this->assertTrue($generator->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id)));

        // Get user groups.
        $usergroups1 = groups_get_user_groups($course1->id, $user1->id);
        $usergroups2 = groups_get_user_groups($course2->id, $user2->id);;

        // Assert return data.
        $this->assertEquals($group1->id, $usergroups1[0][0]);
        $this->assertEquals($group2->id, $usergroups2[0][0]);

        // Now, test with groupings.
        $grouping1 = $generator->create_grouping(array('courseid' => $course1->id));
        $grouping2 = $generator->create_grouping(array('courseid' => $course2->id));

        // Assign the groups to grouping.
        groups_assign_grouping($grouping1->id, $group1->id);
        groups_assign_grouping($grouping2->id, $group2->id);
        groups_assign_grouping($grouping2->id, $group3->id);

        // Test with grouping.
        $usergroups1 = groups_get_user_groups($course1->id, $user1->id);
        $usergroups2 = groups_get_user_groups($course2->id, $user2->id);
        $this->assertArrayHasKey($grouping1->id, $usergroups1);
        $this->assertArrayHasKey($grouping2->id, $usergroups2);

        // Test user without a group.
        $usergroups1 = groups_get_user_groups($course2->id, $user3->id);
        $this->assertCount(0, $usergroups1[0]);

        // Test with userid = 0.
        $usergroups1 = groups_get_user_groups($course1->id, 0);
        $usergroups2 = groups_get_user_groups($course2->id, 0);
        $this->assertCount(0, $usergroups1[0]);
        $this->assertCount(0, $usergroups2[0]);

        // Test with courseid = 0.
        $usergroups1 = groups_get_user_groups(0, $user1->id);
        $usergroups2 = groups_get_user_groups(0, $user2->id);
        $this->assertCount(0, $usergroups1[0]);
        $this->assertCount(0, $usergroups2[0]);
    }

    /**
     * Create dummy groups array for use in menu tests
     * @param int $number
     * @return array
     */
    protected function make_group_list($number) {
        $testgroups = array();
        for ($a = 0; $a < $number; $a++) {
            $grp = new \stdClass();
            $grp->id = 100 + $a;
            $grp->name = 'test group ' . $grp->id;
            $testgroups[$grp->id] = $grp;
        }
        return $testgroups;
    }

    public function test_groups_sort_menu_options_empty() {
        $this->assertEquals(array(), groups_sort_menu_options(array(), array()));
    }

    public function test_groups_sort_menu_options_allowed_goups_only() {
        $this->assertEquals(array(
            100 => 'test group 100',
            101 => 'test group 101',
        ), groups_sort_menu_options($this->make_group_list(2), array()));
    }

    public function test_groups_sort_menu_options_user_goups_only() {
        $this->assertEquals(array(
            100 => 'test group 100',
            101 => 'test group 101',
        ), groups_sort_menu_options(array(), $this->make_group_list(2)));
    }

    public function test_groups_sort_menu_options_user_both() {
        $this->assertEquals(array(
            1 => array(get_string('mygroups', 'group') => array(
                100 => 'test group 100',
                101 => 'test group 101',
            )),
            2 => array(get_string('othergroups', 'group') => array(
                102 => 'test group 102',
                103 => 'test group 103',
            )),
        ), groups_sort_menu_options($this->make_group_list(4), $this->make_group_list(2)));
    }

    public function test_groups_sort_menu_options_user_both_many_groups() {
        $this->assertEquals(array(
            1 => array(get_string('mygroups', 'group') => array(
                100 => 'test group 100',
                101 => 'test group 101',
            )),
            2 => array (get_string('othergroups', 'group') => array(
                102 => 'test group 102',
                103 => 'test group 103',
                104 => 'test group 104',
                105 => 'test group 105',
                106 => 'test group 106',
                107 => 'test group 107',
                108 => 'test group 108',
                109 => 'test group 109',
                110 => 'test group 110',
                111 => 'test group 111',
                112 => 'test group 112',
            )),
        ), groups_sort_menu_options($this->make_group_list(13), $this->make_group_list(2)));
    }

    /**
     * Tests for groups_user_groups_visible.
     */
    public function test_groups_user_groups_visible() {
        global $DB;

        $generator = $this->getDataGenerator();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course category, course and groups.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $coursecontext = \context_course::instance($course->id);
        $group1 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 1'));
        $group2 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 2'));
        $group3 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 3'));
        $group4 = $generator->create_group(array('courseid' => $course->id, 'name' => 'Group 4'));

        // Create cm.
        $assign = $generator->create_module("assign", array('course' => $course->id));
        $cm = get_coursemodule_from_instance("assign", $assign->id);

        // Create users.
        $user1 = $generator->create_user(); // Normal user.
        $user2 = $generator->create_user(); // Normal user.
        $user3 = $generator->create_user(); // Teacher, access all groups.
        $user4 = $generator->create_user(); // Normal user.

        // Enrol users into the course.
        $generator->enrol_user($user1->id, $course->id);
        $generator->enrol_user($user2->id, $course->id);
        $generator->enrol_user($user4->id, $course->id);

        // Assign groups.
        // User1 and User4 share groups.
        groups_add_member($group1, $user1);
        groups_add_member($group2, $user2);
        groups_add_member($group1, $user4);

        // Give capability at course level to the user to access all groups.
        $role = $DB->get_field("role", "id", array("shortname" => "manager"));
        $generator->enrol_user($user3->id, $course->id, $role);
        // Make sure the user has the capability.
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $role, $coursecontext->id);

        // Normal users in different groups.
        $this->setUser($user1);

        // No groups , not forced.
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // No groups, forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, not forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertFalse($result);

        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertFalse($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with separate groups.

        $result = groups_user_groups_visible($course, $user3->id, $cm);
        $this->assertTrue($result);

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with visible groups.

        // Separate groups, not forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertFalse($result);

        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertFalse($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertFalse($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Users sharing groups.

        // No groups , not forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = false;
        update_course($course);

        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // No groups, forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, not forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertFalse($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $result = groups_user_groups_visible($course, $user3->id, $cm);
        $this->assertTrue($result);

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, not forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result);

        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertFalse($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // For teacher with access all groups.

        // No groups , not forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = false;
        update_course($course);

        $this->setUser($user3);

        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // No groups, forced.
        $course->groupmode = NOGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Visible groups, not forced.
        $course->groupmode = VISIBLEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = true;
        update_course($course);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result); // Requesting all groups.
        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertTrue($result); // Requesting all groups.
        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertTrue($result);

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user3->id, $cm);
        $this->assertTrue($result);

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.

        // Separate groups, not forced.
        $course->groupmode = SEPARATEGROUPS;
        $course->groupmodeforce = false;
        update_course($course);
        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result);
        $result = groups_user_groups_visible($course, $user2->id);
        $this->assertTrue($result); // Requesting all groups.
        $result = groups_user_groups_visible($course, $user3->id);
        $this->assertTrue($result); // Requesting all groups.

        $cm->groupmode = NOGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = VISIBLEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with visible groups.
    }

    /**
     * Tests for groups_get_groups_members() method.
     */
    public function test_groups_get_groups_members() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create courses.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($user1->id, $course1->id);
        $generator->enrol_user($user1->id, $course2->id);
        $generator->enrol_user($user2->id, $course2->id);
        $generator->enrol_user($user3->id, $course2->id);

        // Create groups.
        $group1 = $generator->create_group(array('courseid' => $course1->id));
        $group2 = $generator->create_group(array('courseid' => $course2->id));
        $group3 = $generator->create_group(array('courseid' => $course2->id));

        // Assign users to groups.
        $this->assertTrue($generator->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertTrue($generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id)));

        // Test get_groups_members (with extra field and ordering).
        $members = groups_get_groups_members([$group1->id, $group2->id], ['lastaccess'], 'u.id ASC');
        $this->assertCount(2, $members);
        $this->assertEquals([$user1->id, $user2->id], array_keys($members));
        $this->assertTrue(isset($members[$user1->id]->lastaccess));
        $this->assertTrue(isset($members[$user2->id]->lastaccess));

        // Group with just one.
        $members = groups_get_groups_members([$group1->id]);
        $this->assertCount(1, $members);
        $this->assertEquals($user1->id, $members[$user1->id]->id);

        // Test the info matches group membership for the entire course.
        $groups  = groups_get_all_groups($course1->id, 0, 0, 'g.*', true);
        $group1withmembers = array_pop($groups);

        // Compare the sorted keys of both arrays (should be list of user ids).
        $members = array_keys($members);
        sort($members);
        $group1members = array_keys($group1withmembers->members);
        sort($group1members);
        $this->assertEquals($members, $group1members);

        // Group with just one plus empty group.
        $members = groups_get_groups_members([$group1->id, $group3->id]);
        $this->assertCount(1, $members);
        $this->assertEquals($user1->id, $members[$user1->id]->id);

        // Empty group.
        $members = groups_get_groups_members([$group3->id]);
        $this->assertCount(0, $members);

        // Test groups_get_members.
        $members = groups_get_members($group2->id, 'u.*', 'u.id ASC');
        $this->assertCount(2, $members);
        $this->assertEquals([$user1->id, $user2->id], array_keys($members));

        // Test the info matches group membership for the entire course.
        $groups  = groups_get_all_groups($course2->id, 0, 0, 'g.*', true);
        $group2withmembers = $groups[$group2->id];

        // Compare the sorted keys of both arrays (should be list of user ids).
        $members = array_keys($members);
        sort($members);
        $group2members = array_keys($group2withmembers->members);
        sort($group2members);
        $this->assertEquals($members, $group2members);

    }

    /**
     * Tests for groups_get_activity_shared_group_members() method.
     */
    public function test_groups_get_activity_shared_group_members() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create courses.
        $course = $generator->create_course();

        // Create cm.
        $assign = $generator->create_module("assign", array('course' => $course->id));
        $cm = get_coursemodule_from_instance("assign", $assign->id);

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($user1->id, $course->id);
        $generator->enrol_user($user2->id, $course->id);
        $generator->enrol_user($user3->id, $course->id);
        $generator->enrol_user($user4->id, $course->id);

        // Create groups.
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $group3 = $generator->create_group(array('courseid' => $course->id));

        // Assign users to groups.
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id));
        $generator->create_group_member(array('groupid' => $group3->id, 'userid' => $user3->id));

        // Retrieve users sharing groups with user1.
        $members = groups_get_activity_shared_group_members($cm, $user1->id);
        $this->assertCount(2, $members);
        $this->assertEqualsCanonicalizing([$user1->id, $user2->id], array_keys($members));

        // Retrieve users sharing groups with user2.
        $members = groups_get_activity_shared_group_members($cm, $user2->id);
        $this->assertCount(2, $members);
        $this->assertEqualsCanonicalizing([$user1->id, $user2->id], array_keys($members));

        // Retrieve users sharing groups with user3.
        $members = groups_get_activity_shared_group_members($cm, $user3->id);
        $this->assertCount(1, $members);
        $this->assertEquals($user3->id, $members[$user3->id]->id);

        // Retrieve users sharing groups with user without groups (user4).
        $members = groups_get_activity_shared_group_members($cm, $user4->id);
        $this->assertCount(0, $members);

        // Now, create a different activity using groupings.
        $grouping = $generator->create_grouping(array('courseid' => $course->id, 'name' => 'Grouping 1'));
        // Skip group 2.
        groups_assign_grouping($grouping->id, $group1->id);
        groups_assign_grouping($grouping->id, $group3->id);

        $assign = $generator->create_module("assign", array('course' => $course->id, 'groupingid' => $grouping->id));
        $cm = get_coursemodule_from_instance("assign", $assign->id);

        // Since the activity is forced to groupings (groups 1 and 3), I don't see members of group 2.
        $members = groups_get_activity_shared_group_members($cm, $user1->id);
        $this->assertCount(1, $members);
        $this->assertEquals($user1->id, $members[$user1->id]->id);

        // Add user1 to group 3 (in the grouping).
        $generator->create_group_member(array('groupid' => $group3->id, 'userid' => $user1->id));
        $members = groups_get_activity_shared_group_members($cm, $user1->id);
        $this->assertCount(2, $members);    // Now I see members of group 3.
        $this->assertEqualsCanonicalizing([$user1->id, $user3->id], array_keys($members));
    }
}
