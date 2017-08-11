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
 * Tests groups subsystems.
 *
 * @package    core_group
 * @category   phpunit
 * @copyright  2007 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for lib/grouplib.php
 * @group core_group
 */
class core_grouplib_testcase extends advanced_testcase {

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
        $student = $generator->create_user();
        $plugin = enrol_get_plugin('manual');
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $group = $generator->create_group(array('courseid' => $course->id));
        $instance = $DB->get_record('enrol', array(
                'courseid' => $course->id,
                'enrol' => 'manual',
        ));

        $this->assertNotEquals($instance, false);

        // Enrol the user in the course.
        $plugin->enrol_user($instance, $student->id, $role->id);

        list($sql, $params) = groups_get_members_ids_sql($group->id, true);

        // Test an empty group.
        $users = $DB->get_records_sql($sql, $params);

        $this->assertFalse(array_key_exists($student->id, $users));
        groups_add_member($group->id, $student->id);

        // Test with a group member.
        $users = $DB->get_records_sql($sql, $params);
        $this->assertTrue(array_key_exists($student->id, $users));
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
        $this->assertContains($grouping1->id, array_keys($data->groupings));
        $this->assertContains($grouping2->id, array_keys($data->groupings));

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
                $this->assertContains($mapping->groupid, array($group1->id, $group2->id));
            } else if ($mapping->groupingid === $grouping2->id) {
                $grouping2maps++;
                $this->assertContains($mapping->groupid, array($group3->id, $group4->id));
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
        $groupkeys = array_keys($groups);
        $this->assertCount(4, $groups);
        $this->assertContains($group1->id, $groupkeys);
        $this->assertContains($group2->id, $groupkeys);
        $this->assertContains($group3->id, $groupkeys);
        $this->assertContains($group4->id, $groupkeys);

        $groups  = groups_get_all_groups($course->id, null, $grouping1->id);
        $groupkeys = array_keys($groups);
        $this->assertCount(2, $groups);
        $this->assertContains($group1->id, $groupkeys);
        $this->assertContains($group2->id, $groupkeys);
        $this->assertNotContains($group3->id, $groupkeys);
        $this->assertNotContains($group4->id, $groupkeys);

        $groups  = groups_get_all_groups($course->id, null, $grouping2->id);
        $groupkeys = array_keys($groups);
        $this->assertCount(2, $groups);
        $this->assertNotContains($group1->id, $groupkeys);
        $this->assertNotContains($group2->id, $groupkeys);
        $this->assertContains($group3->id, $groupkeys);
        $this->assertContains($group4->id, $groupkeys);

        // Test this function using an alternate column for the result index
        $groups  = groups_get_all_groups($course->id, null, $grouping2->id, 'g.name, g.id');
        $groupkeys = array_keys($groups);
        $this->assertCount(2, $groups);
        $this->assertNotContains($group3->id, $groupkeys);
        $this->assertContains($group3->name, $groupkeys);
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
        $coursecontext = context_course::instance($course->id);
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
        $record = new stdClass();
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
        $this->assertContains(format_string($group1->name), $html);
        $this->assertNotContains(format_string($group2->name), $html);

        $this->setAdminUser();

        // Now user can access everything.
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertContains(format_string($group1->name), $html);
        $this->assertContains(format_string($group2->name), $html);

        // Make sure separate groups mode, doesn't change anything.
        $course->groupmode = SEPARATEGROUPS;
        update_course($course);
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertContains(format_string($group1->name), $html);
        $this->assertContains(format_string($group2->name), $html);

        // Make sure Visible groups mode, doesn't change anything.
        $course->groupmode = VISIBLEGROUPS;
        update_course($course);
        $html = groups_allgroups_course_menu($course, 'someurl.php');
        $this->assertContains(format_string($group1->name), $html);
        $this->assertContains(format_string($group2->name), $html);

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
            $grp = new stdClass();
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
        global $CFG, $DB;

        $generator = $this->getDataGenerator();
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create a course category, course and groups.
        $cat = $generator->create_category(array('parent' => 0));
        $course = $generator->create_course(array('category' => $cat->id));
        $coursecontext = context_course::instance($course->id);
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

        $result = groups_user_groups_visible($course, $user2->id);
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

        $result = groups_user_groups_visible($course, $user2->id);
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
        $result = groups_user_groups_visible($course, $user2->id);
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

        $result = groups_user_groups_visible($course, $user4->id);
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

        $result = groups_user_groups_visible($course, $user4->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user4->id);
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
        $result = groups_user_groups_visible($course, $user4->id);
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

        $result = groups_user_groups_visible($course, $user1->id);
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

        $result = groups_user_groups_visible($course, $user1->id);
        $this->assertTrue($result); // Cm with no groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.
        $result = groups_user_groups_visible($course, $user2->id, $cm);
        $this->assertTrue($result); // Cm with separate groups.

        $cm->groupmode = SEPARATEGROUPS;
        $result = groups_user_groups_visible($course, $user1->id);
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
        $result = groups_user_groups_visible($course, $user1->id);
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
        $this->assertEquals([$user1->id, $user2->id], array_keys($members), '', 0.0, 10, true);

        // Retrieve users sharing groups with user2.
        $members = groups_get_activity_shared_group_members($cm, $user2->id);
        $this->assertCount(2, $members);
        $this->assertEquals([$user1->id, $user2->id], array_keys($members), '', 0.0, 10, true);

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
        $this->assertEquals([$user1->id, $user3->id], array_keys($members), '', 0.0, 10, true);
    }

    /**
     * Test groups_get_all_groups_for_courses() method.
     */
    public function test_groups_get_all_groups_for_courses_no_courses() {
        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $this->assertEquals([], groups_get_all_groups_for_courses([]));
    }

    /**
     * Test groups_get_all_groups_for_courses() method.
     */
    public function test_groups_get_all_groups_for_courses_with_courses() {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        // Create courses.
        $course1 = $generator->create_course(); // no groups.
        $course2 = $generator->create_course(); // one group, no members.
        $course3 = $generator->create_course(); // one group, one member.
        $course4 = $generator->create_course(); // one group, multiple members.
        $course5 = $generator->create_course(); // two groups, no members.
        $course6 = $generator->create_course(); // two groups, one member.
        $course7 = $generator->create_course(); // two groups, multiple members.

        $courses = [$course1, $course2, $course3, $course4, $course5, $course6, $course7];
        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $user4 = $generator->create_user();

        // Enrol users.
        foreach ($courses as $course) {
            $generator->enrol_user($user1->id, $course->id);
            $generator->enrol_user($user2->id, $course->id);
            $generator->enrol_user($user3->id, $course->id);
            $generator->enrol_user($user4->id, $course->id);
        }

        // Create groups.
        $group1 = $generator->create_group(array('courseid' => $course2->id)); // no members.
        $group2 = $generator->create_group(array('courseid' => $course3->id)); // one member.
        $group3 = $generator->create_group(array('courseid' => $course4->id)); // multiple members.
        $group4 = $generator->create_group(array('courseid' => $course5->id)); // no members.
        $group5 = $generator->create_group(array('courseid' => $course5->id)); // no members.
        $group6 = $generator->create_group(array('courseid' => $course6->id)); // one member.
        $group7 = $generator->create_group(array('courseid' => $course6->id)); // one member.
        $group8 = $generator->create_group(array('courseid' => $course7->id)); // multiple members.
        $group9 = $generator->create_group(array('courseid' => $course7->id)); // multiple members.

        // Assign users to groups.
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group3->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group3->id, 'userid' => $user2->id));
        $generator->create_group_member(array('groupid' => $group6->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group7->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group8->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group8->id, 'userid' => $user2->id));
        $generator->create_group_member(array('groupid' => $group9->id, 'userid' => $user1->id));
        $generator->create_group_member(array('groupid' => $group9->id, 'userid' => $user2->id));

        // The process of modifying group members changes the timemodified of the group.
        // Refresh the group records.
        $group1 = $DB->get_record('groups', ['id' => $group1->id]);
        $group2 = $DB->get_record('groups', ['id' => $group2->id]);
        $group3 = $DB->get_record('groups', ['id' => $group3->id]);
        $group4 = $DB->get_record('groups', ['id' => $group4->id]);
        $group5 = $DB->get_record('groups', ['id' => $group5->id]);
        $group6 = $DB->get_record('groups', ['id' => $group6->id]);
        $group7 = $DB->get_record('groups', ['id' => $group7->id]);
        $group8 = $DB->get_record('groups', ['id' => $group8->id]);
        $group9 = $DB->get_record('groups', ['id' => $group9->id]);

        $result = groups_get_all_groups_for_courses($courses);
        $assertpropertiesmatch = function($expected, $actual) {
            $props = get_object_vars($expected);

            foreach ($props as $name => $val) {
                $got = $actual->{$name};
                $this->assertEquals(
                    $val,
                    $actual->{$name},
                    "Failed asserting that {$got} equals {$val} for property {$name}"
                );
            }
        };

        // Course 1 has no groups.
        $this->assertEquals([], $result[$course1->id]);

        // Course 2 has one group with no members.
        $coursegroups = $result[$course2->id];
        $coursegroup = $coursegroups[$group1->id];
        $this->assertCount(1, $coursegroups);
        $this->assertEquals([], $coursegroup->members);
        $assertpropertiesmatch($group1, $coursegroup);

        // Course 3 has one group with one member.
        $coursegroups = $result[$course3->id];
        $coursegroup = $coursegroups[$group2->id];
        $groupmember1 = $coursegroup->members[$user1->id];
        $this->assertCount(1, $coursegroups);
        $this->assertCount(1, $coursegroup->members);
        $assertpropertiesmatch($group2, $coursegroup);
        $this->assertEquals($user1->id, $groupmember1->userid);

        // Course 4 has one group with multiple members.
        $coursegroups = $result[$course4->id];
        $coursegroup = $coursegroups[$group3->id];
        $groupmember1 = $coursegroup->members[$user1->id];
        $groupmember2 = $coursegroup->members[$user2->id];
        $this->assertCount(1, $coursegroups);
        $this->assertCount(2, $coursegroup->members);
        $assertpropertiesmatch($group3, $coursegroup);
        $this->assertEquals($user1->id, $groupmember1->userid);
        $this->assertEquals($user2->id, $groupmember2->userid);

        // Course 5 has multiple groups with no members.
        $coursegroups = $result[$course5->id];
        $coursegroup1 = $coursegroups[$group4->id];
        $coursegroup2 = $coursegroups[$group5->id];
        $this->assertCount(2, $coursegroups);
        $this->assertEquals([], $coursegroup1->members);
        $this->assertEquals([], $coursegroup2->members);
        $assertpropertiesmatch($group4, $coursegroup1);
        $assertpropertiesmatch($group5, $coursegroup2);

        // Course 6 has multiple groups with one member.
        $coursegroups = $result[$course6->id];
        $coursegroup1 = $coursegroups[$group6->id];
        $coursegroup2 = $coursegroups[$group7->id];
        $group1member1 = $coursegroup1->members[$user1->id];
        $group2member1 = $coursegroup2->members[$user1->id];
        $this->assertCount(2, $coursegroups);
        $this->assertCount(1, $coursegroup1->members);
        $this->assertCount(1, $coursegroup2->members);
        $assertpropertiesmatch($group6, $coursegroup1);
        $assertpropertiesmatch($group7, $coursegroup2);
        $this->assertEquals($user1->id, $group1member1->userid);
        $this->assertEquals($user1->id, $group2member1->userid);

        // Course 7 has multiple groups with multiple members.
        $coursegroups = $result[$course7->id];
        $coursegroup1 = $coursegroups[$group8->id];
        $coursegroup2 = $coursegroups[$group9->id];
        $group1member1 = $coursegroup1->members[$user1->id];
        $group1member2 = $coursegroup1->members[$user2->id];
        $group2member1 = $coursegroup2->members[$user1->id];
        $group2member2 = $coursegroup2->members[$user2->id];
        $this->assertCount(2, $coursegroups);
        $this->assertCount(2, $coursegroup1->members);
        $this->assertCount(2, $coursegroup2->members);
        $assertpropertiesmatch($group8, $coursegroup1);
        $assertpropertiesmatch($group9, $coursegroup2);
        $this->assertEquals($user1->id, $group1member1->userid);
        $this->assertEquals($user2->id, $group1member2->userid);
        $this->assertEquals($user1->id, $group2member1->userid);
        $this->assertEquals($user2->id, $group2member2->userid);
    }
}
