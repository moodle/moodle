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
 * Unit tests for the condition.
 *
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use availability_grouping\condition;

/**
 * Unit tests for the condition.
 *
 * @package availability_grouping
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_grouping_condition_testcase extends advanced_testcase {
    /**
     * Load required classes.
     */
    public function setUp() {
        // Load the mock info class so that it can be used.
        global $CFG;
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
    }

    /**
     * Tests constructing and using condition.
     */
    public function test_usage() {
        global $CFG, $USER;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Erase static cache before test.
        condition::wipe_static_cache();

        // Make a test course and user.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $info = new \core_availability\mock_info($course, $user->id);

        // Make a test grouping and group.
        $grouping = $generator->create_grouping(array('courseid' => $course->id,
                'name' => 'Grouping!'));
        $group = $generator->create_group(array('courseid' => $course->id));
        groups_assign_grouping($grouping->id, $group->id);

        // Do test (not in grouping).
        $structure = (object)array('type' => 'grouping', 'id' => (int)$grouping->id);
        $cond = new condition($structure);

        // Check if available (when not available).
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $this->assertRegExp('~belong to a group in.*Grouping!~', $information);
        $this->assertTrue($cond->is_available(true, $info, true, $user->id));

        // Add user to grouping and refresh cache.
        groups_add_member($group, $user);
        get_fast_modinfo($course->id, 0, true);

        // Recheck.
        $this->assertTrue($cond->is_available(false, $info, true, $user->id));
        $this->assertFalse($cond->is_available(true, $info, true, $user->id));
        $information = $cond->get_description(false, true, $info);
        $this->assertRegExp('~do not belong to a group in.*Grouping!~', $information);

        // Admin user doesn't belong to the grouping, but they can access it
        // either way (positive or NOT) because of accessallgroups.
        $this->setAdminUser();
        $infoadmin = new \core_availability\mock_info($course, $USER->id);
        $this->assertTrue($cond->is_available(false, $infoadmin, true, $USER->id));
        $this->assertTrue($cond->is_available(true, $infoadmin, true, $USER->id));

        // Grouping that doesn't exist uses 'missing' text.
        $cond = new condition((object)array('id' => $grouping->id + 1000));
        $this->assertFalse($cond->is_available(false, $info, true, $user->id));
        $information = $cond->get_description(false, false, $info);
        $this->assertRegExp('~belong to a group in.*(Missing grouping)~', $information);

        // We need an actual cm object to test the 'grouping from cm' option.
        $pagegen = $generator->get_plugin_generator('mod_page');
        $page = $pagegen->create_instance(array('course' => $course->id,
                'groupingid' => $grouping->id, 'availability' =>
                '{"op":"|","show":true,"c":[{"type":"grouping","activity":true}]}'));
        rebuild_course_cache($course->id, true);

        // Check if available using the 'from course-module' grouping option.
        $modinfo = get_fast_modinfo($course, $user->id);
        $cm = $modinfo->get_cm($page->cmid);
        $info = new \core_availability\info_module($cm);
        $information = '';
        $this->assertTrue($info->is_available($information, false, $user->id));

        // Remove user from grouping again and recheck.
        groups_remove_member($group, $user);
        get_fast_modinfo($course->id, 0, true);
        $this->assertFalse($info->is_available($information, false, $user->id));
        $this->assertRegExp('~belong to a group in.*Grouping!~', $information);
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor() {
        // No parameters.
        $structure = new stdClass();
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing ->id / ->activity', $e->getMessage());
        }

        // Invalid id (not int).
        $structure->id = 'bourne';
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid ->id', $e->getMessage());
        }

        // Invalid activity option (not bool).
        unset($structure->id);
        $structure->activity = 42;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid ->activity', $e->getMessage());
        }

        // Invalid activity option (false).
        $structure->activity = false;
        try {
            $cond = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Invalid ->activity', $e->getMessage());
        }

        // Valid with id.
        $structure->id = 123;
        $cond = new condition($structure);
        $this->assertEquals('{grouping:#123}', (string)$cond);

        // Valid with activity.
        unset($structure->id);
        $structure->activity = true;
        $cond = new condition($structure);
        $this->assertEquals('{grouping:CM}', (string)$cond);
    }

    /**
     * Tests the save() function.
     */
    public function test_save() {
        $structure = (object)array('id' => 123);
        $cond = new condition($structure);
        $structure->type = 'grouping';
        $this->assertEquals($structure, $cond->save());

        $structure = (object)array('activity' => true);
        $cond = new condition($structure);
        $structure->type = 'grouping';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function test_update_dependency_id() {
        $cond = new condition((object)array('id' => 123));
        $this->assertFalse($cond->update_dependency_id('frogs', 123, 456));
        $this->assertFalse($cond->update_dependency_id('groupings', 12, 34));
        $this->assertTrue($cond->update_dependency_id('groupings', 123, 456));
        $after = $cond->save();
        $this->assertEquals(456, $after->id);

        $cond = new condition((object)array('activity' => true));
        $this->assertFalse($cond->update_dependency_id('frogs', 123, 456));
    }

    /**
     * Tests the filter_users (bulk checking) function. Also tests the SQL
     * variant get_user_list_sql.
     */
    public function test_filter_users() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Erase static cache before test.
        condition::wipe_static_cache();

        // Make a test course and some users.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $course->id, $roleids['teacher']);
        $allusers = array($teacher->id => $teacher);
        $students = array();
        for ($i = 0; $i < 3; $i++) {
            $student = $generator->create_user();
            $students[$i] = $student;
            $generator->enrol_user($student->id, $course->id, $roleids['student']);
            $allusers[$student->id] = $student;
        }
        $info = new \core_availability\mock_info($course);
        $checker = new \core_availability\capability_checker($info->get_context());

        // Make test groups.
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $grouping1 = $generator->create_grouping(array('courseid' => $course->id));
        $grouping2 = $generator->create_grouping(array('courseid' => $course->id));
        groups_assign_grouping($grouping1->id, $group1->id);
        groups_assign_grouping($grouping2->id, $group2->id);

        // Make page in grouping 2.
        $pagegen = $generator->get_plugin_generator('mod_page');
        $page = $pagegen->create_instance(array('course' => $course->id,
                'groupingid' => $grouping2->id, 'availability' =>
                '{"op":"|","show":true,"c":[{"type":"grouping","activity":true}]}'));

        // Assign students to groups as follows (teacher is not in a group):
        // 0: no groups.
        // 1: in group 1/grouping 1.
        // 2: in group 2/grouping 2.
        groups_add_member($group1, $students[1]);
        groups_add_member($group2, $students[2]);

        // Test specific grouping.
        $cond = new condition((object)array('id' => (int)$grouping1->id));
        $result = array_keys($cond->filter_user_list($allusers, false, $info, $checker));
        ksort($result);
        $expected = array($teacher->id, $students[1]->id);
        $this->assertEquals($expected, $result);

        // Test it with get_user_list_sql.
        list ($sql, $params) = $cond->get_user_list_sql(false, $info, true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);

        // NOT test.
        $result = array_keys($cond->filter_user_list($allusers, true, $info, $checker));
        ksort($result);
        $expected = array($teacher->id, $students[0]->id, $students[2]->id);
        $this->assertEquals($expected, $result);

        // NOT with get_user_list_sql.
        list ($sql, $params) = $cond->get_user_list_sql(true, $info, true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);

        // Test course-module grouping.
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($page->cmid);
        $info = new \core_availability\info_module($cm);
        $result = array_keys($info->filter_user_list($allusers, $course));
        $expected = array($teacher->id, $students[2]->id);
        $this->assertEquals($expected, $result);

        // With get_user_list_sql.
        list ($sql, $params) = $info->get_user_list_sql(true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);
    }
}
