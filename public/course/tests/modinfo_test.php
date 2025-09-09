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

namespace core_course;

use core\exception\coding_exception;
use core\exception\moodle_exception;
use core_cache\cache;
use core_courseformat\formatactions;
use dml_exception;

/**
 * Tests for course
 *
 * @package    core_course
 * @category   test
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(modinfo::class)]
final class modinfo_test extends \advanced_testcase {
    public function test_course_modinfo_properties(): void {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('allowstealth', true);

        // Generate the course and some modules. Make one section hidden.
        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 3],
            ['createsections' => true],
        );
        $DB->execute(
            'UPDATE {course_sections} SET visible = 0 WHERE course = ? and section = ?',
            [$course->id, 3],
        );
        $forum0 = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id, 'section' => 0],
        );
        $assign0 = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 0, 'visible' => 0],
        );
        $page0 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'section' => 0, 'visibleoncoursepage' => 0],
        );
        $forum1 = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id, 'section' => 1],
        );
        $assign1 = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'section' => 1],
        );
        $page1 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'section' => 1],
        );
        $page3 = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id, 'section' => 3],
        );

        $modinfo = get_fast_modinfo($course->id);

        $this->assertEquals(
            [$forum0->cmid, $assign0->cmid, $page0->cmid, $forum1->cmid, $assign1->cmid, $page1->cmid, $page3->cmid],
            array_keys($modinfo->cms),
        );
        $this->assertEquals($course->id, $modinfo->courseid);
        $this->assertEquals($USER->id, $modinfo->userid);
        $this->assertEquals(
            [
                0 => [$forum0->cmid, $assign0->cmid, $page0->cmid],
                1 => [$forum1->cmid, $assign1->cmid, $page1->cmid],
                3 => [$page3->cmid],
            ],
            $modinfo->sections,
        );
        $this->assertEquals(['forum', 'assign', 'page'], array_keys($modinfo->instances));
        $this->assertEquals([$assign0->id, $assign1->id], array_keys($modinfo->instances['assign']));
        $this->assertEquals([$forum0->id, $forum1->id], array_keys($modinfo->instances['forum']));
        $this->assertEquals([$page0->id, $page1->id, $page3->id], array_keys($modinfo->instances['page']));
        $this->assertEquals(groups_get_user_groups($course->id), $modinfo->groups);
        $this->assertEquals(
            [
                0 => [$forum0->cmid, $assign0->cmid, $page0->cmid],
                1 => [$forum1->cmid, $assign1->cmid, $page1->cmid],
                3 => [$page3->cmid],
            ],
            $modinfo->get_sections(),
        );
        $this->assertEquals([0, 1, 2, 3], array_keys($modinfo->get_section_info_all()));
        $this->assertEquals($forum0->cmid . ',' . $assign0->cmid . ',' . $page0->cmid, $modinfo->get_section_info(0)->sequence);
        $this->assertEquals($forum1->cmid . ',' . $assign1->cmid . ',' . $page1->cmid, $modinfo->get_section_info(1)->sequence);
        $this->assertEquals('', $modinfo->get_section_info(2)->sequence);
        $this->assertEquals($page3->cmid, $modinfo->get_section_info(3)->sequence);
        $this->assertEquals($course->id, $modinfo->get_course()->id);

        $names = array_keys($modinfo->get_used_module_names());
        sort($names);
        $this->assertEquals(['assign', 'forum', 'page'], $names);

        $names = array_keys($modinfo->get_used_module_names(true));
        sort($names);
        $this->assertEquals(['assign', 'forum', 'page'], $names);

        // Admin can see hidden modules/sections.
        $this->assertTrue($modinfo->cms[$assign0->cmid]->uservisible);
        $this->assertTrue($modinfo->cms[$assign0->cmid]->is_visible_on_course_page());
        $this->assertTrue($modinfo->cms[$page0->cmid]->uservisible);
        $this->assertTrue($modinfo->cms[$page0->cmid]->is_visible_on_course_page());
        $this->assertTrue($modinfo->get_section_info(3)->uservisible);

        $this->assertFalse($modinfo->cms[$assign0->cmid]->is_stealth());
        $this->assertFalse($modinfo->cms[$assign0->cmid]->is_stealth());
        $this->assertTrue($modinfo->cms[$page0->cmid]->is_stealth());
        $this->assertTrue($modinfo->cms[$page3->cmid]->is_stealth());

        // Get modinfo for user with student role (without capability to view hidden activities/sections).
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $studentmodinfo = get_fast_modinfo($course->id, $student->id);
        $this->assertEquals($student->id, $studentmodinfo->userid);
        $this->assertTrue($studentmodinfo->cms[$forum0->cmid]->uservisible);
        $this->assertTrue($studentmodinfo->cms[$forum0->cmid]->is_visible_on_course_page());
        $this->assertFalse($studentmodinfo->cms[$assign0->cmid]->uservisible);
        $this->assertFalse($studentmodinfo->cms[$assign0->cmid]->is_visible_on_course_page());
        $this->assertTrue($studentmodinfo->cms[$page0->cmid]->uservisible);
        $this->assertFalse($studentmodinfo->cms[$page0->cmid]->is_visible_on_course_page());
        $this->assertFalse($studentmodinfo->get_section_info(3)->uservisible);
        $this->assertTrue($studentmodinfo->cms[$page3->cmid]->uservisible);
        $this->assertTrue($studentmodinfo->cms[$page3->cmid]->is_visible_on_course_page());

        // Get modinfo for user with teacher role (with capability to view hidden activities but not sections).
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'teacher');
        $teachermodinfo = get_fast_modinfo($course->id, $teacher->id);
        $this->assertEquals($teacher->id, $teachermodinfo->userid);
        $this->assertTrue($teachermodinfo->cms[$forum0->cmid]->uservisible);
        $this->assertTrue($teachermodinfo->cms[$forum0->cmid]->is_visible_on_course_page());
        $this->assertTrue($teachermodinfo->cms[$assign0->cmid]->uservisible);
        $this->assertTrue($teachermodinfo->cms[$assign0->cmid]->is_visible_on_course_page());
        $this->assertTrue($teachermodinfo->cms[$page0->cmid]->uservisible);
        $this->assertTrue($teachermodinfo->cms[$page0->cmid]->is_visible_on_course_page());
        $this->assertFalse($teachermodinfo->get_section_info(3)->uservisible);
        $this->assertTrue($teachermodinfo->cms[$page3->cmid]->uservisible);
        $this->assertTrue($teachermodinfo->cms[$page3->cmid]->is_visible_on_course_page());

        // Get modinfo for user with editingteacher role (with capability to view hidden activities/sections).
        $editingteacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($editingteacher->id, $course->id, 'editingteacher');
        $editingteachermodinfo = get_fast_modinfo($course->id, $editingteacher->id);
        $this->assertEquals($editingteacher->id, $editingteachermodinfo->userid);
        $this->assertTrue($editingteachermodinfo->cms[$forum0->cmid]->uservisible);
        $this->assertTrue($editingteachermodinfo->cms[$forum0->cmid]->is_visible_on_course_page());
        $this->assertTrue($editingteachermodinfo->cms[$assign0->cmid]->uservisible);
        $this->assertTrue($editingteachermodinfo->cms[$assign0->cmid]->is_visible_on_course_page());
        $this->assertTrue($editingteachermodinfo->cms[$page0->cmid]->uservisible);
        $this->assertTrue($editingteachermodinfo->cms[$page0->cmid]->is_visible_on_course_page());
        $this->assertTrue($editingteachermodinfo->get_section_info(3)->uservisible);
        $this->assertTrue($editingteachermodinfo->cms[$page3->cmid]->uservisible);
        $this->assertTrue($editingteachermodinfo->cms[$page3->cmid]->is_visible_on_course_page());

        // Attempt to access and set non-existing field.
        $this->assertTrue(empty($modinfo->somefield));
        $this->assertFalse(isset($modinfo->somefield));
        $modinfo->somefield;
        $this->assertDebuggingCalled();
        $modinfo->somefield = 'Some value';
        $this->assertDebuggingCalled();
        $this->assertEmpty($modinfo->somefield);
        $this->assertDebuggingCalled();

        // Attempt to overwrite existing field.
        $this->assertFalse(empty($modinfo->cms));
        $this->assertTrue(isset($modinfo->cms));
        $modinfo->cms = 'Illegal overwriting';
        $this->assertDebuggingCalled();
        $this->assertNotEquals('Illegal overwriting', $modinfo->cms);
    }

    /**
     * Tests the availability property that has been added to course modules
     * and sections (just to see that it is correctly saved and accessed).
     */
    public function test_availability_property(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course with two modules and three sections.
        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 3],
            ['createsections' => true],
        );
        $forum = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id],
        );
        $forum2 = $this->getDataGenerator()->create_module(
            'forum',
            ['course' => $course->id],
        );

        // Get modinfo. Check that availability is null for both cm and sections.
        $modinfo = get_fast_modinfo($course->id);
        $cm = $modinfo->get_cm($forum->cmid);
        $this->assertNull($cm->availability);
        $section = $modinfo->get_section_info(1, MUST_EXIST);
        $this->assertNull($section->availability);

        // Update availability for cm and section in database.
        $DB->set_field('course_modules', 'availability', '{}', ['id' => $cm->id]);
        $DB->set_field('course_sections', 'availability', '{}', ['id' => $section->id]);

        // Clear cache and get modinfo again.
        rebuild_course_cache($course->id, true);
        get_fast_modinfo(0, 0, true);
        $modinfo = get_fast_modinfo($course->id);

        // Check values that were changed.
        $cm = $modinfo->get_cm($forum->cmid);
        $this->assertEquals('{}', $cm->availability);
        $section = $modinfo->get_section_info(1, MUST_EXIST);
        $this->assertEquals('{}', $section->availability);

        // Check other values are still null.
        $cm = $modinfo->get_cm($forum2->cmid);
        $this->assertNull($cm->availability);
        $section = $modinfo->get_section_info(2, MUST_EXIST);
        $this->assertNull($section->availability);
    }

    /**
     * Test for get_listed_section_info_all method.
     */
    public function test_get_listed_section_info_all(): void {
        $this->resetAfterTest();
        $this->load_fixture('core', 'sectiondelegatetest.php');

        // Create a course with 4 sections.
        $course = $this->getDataGenerator()->create_course(['numsections' => 3]);

        $listed = get_fast_modinfo($course)->get_section_info_all();
        $this->assertCount(4, $listed);

        // Generate some delegated sections (not listed).
        formatactions::section($course)->create_delegated('test_component', 0);
        formatactions::section($course)->create_delegated('test_component', 1);

        $this->assertCount(6, get_fast_modinfo($course)->get_section_info_all());

        $result = get_fast_modinfo($course)->get_listed_section_info_all();

        $this->assertCount(4, $result);
        $this->assertEquals($listed[0]->id, $result[0]->id);
        $this->assertEquals($listed[1]->id, $result[1]->id);
        $this->assertEquals($listed[2]->id, $result[2]->id);
        $this->assertEquals($listed[3]->id, $result[3]->id);
    }

    /**
     * Tests for get_groups() method.
     */
    public function test_get_groups(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create courses.
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();

        // Create users.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();

        // Enrol users on courses.
        $generator->enrol_user($user1->id, $course1->id);
        $generator->enrol_user($user2->id, $course2->id);
        $generator->enrol_user($user3->id, $course2->id);
        $generator->enrol_user($user3->id, $course3->id);

        // Create groups.
        $group1 = $generator->create_group(['courseid' => $course1->id]);
        $group2 = $generator->create_group(['courseid' => $course2->id]);
        $group3 = $generator->create_group(['courseid' => $course2->id]);

        // Assign users to groups and assert the result.
        $this->assertTrue($generator->create_group_member(['groupid' => $group1->id, 'userid' => $user1->id]));
        $this->assertTrue($generator->create_group_member(['groupid' => $group2->id, 'userid' => $user2->id]));
        $this->assertTrue($generator->create_group_member(['groupid' => $group3->id, 'userid' => $user2->id]));
        $this->assertTrue($generator->create_group_member(['groupid' => $group2->id, 'userid' => $user3->id]));

        // Create groupings.
        $grouping1 = $generator->create_grouping(['courseid' => $course1->id]);
        $grouping2 = $generator->create_grouping(['courseid' => $course2->id]);

        // Assign and assert group to groupings.
        groups_assign_grouping($grouping1->id, $group1->id);
        groups_assign_grouping($grouping2->id, $group2->id);
        groups_assign_grouping($grouping2->id, $group3->id);

        // Test with one single group.
        $modinfo = get_fast_modinfo($course1, $user1->id);
        $groups = $modinfo->get_groups($grouping1->id);
        $this->assertCount(1, $groups);
        $this->assertArrayHasKey($group1->id, $groups);

        // Test with two groups.
        $modinfo = get_fast_modinfo($course2, $user2->id);
        $groups = $modinfo->get_groups();
        $this->assertCount(2, $groups);
        $this->assertTrue(in_array($group2->id, $groups));
        $this->assertTrue(in_array($group3->id, $groups));

        // Test with no groups.
        $modinfo = get_fast_modinfo($course3, $user3->id);
        $groups = $modinfo->get_groups();
        $this->assertCount(0, $groups);
        $this->assertArrayNotHasKey($group1->id, $groups);
    }

    /**
     * Tests the function for constructing a cm_info from mixed data.
     */

    public function test_create(): void {
        global $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->create_module('page', ['course' => $course->id, 'name' => 'Annie']);

        // Null is passed through.
        $this->assertNull(cm_info::create(null));

        // Stdclass object turns into cm_info.
        $cm = cm_info::create(
            (object)['id' => $page->cmid, 'course' => $course->id],
        );
        $this->assertInstanceOf(cm_info::class, $cm);
        $this->assertEquals('Annie', $cm->name);

        // A cm_info object stays as cm_info.
        $this->assertSame($cm, cm_info::create($cm));

        // Invalid object (missing fields) causes error.
        try {
            cm_info::create((object)['id' => $page->cmid]);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertInstanceOf(coding_exception::class, $e);
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module('page', ['course' => $course->id, 'name' => 'Annie', 'visible' => 0]);

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user(
            $manager->id,
            $course->id,
            $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST),
        );

        // User can see the normal page but not the hidden one.
        $cm = cm_info::create(
            (object)['id' => $page->cmid, 'course' => $course->id],
            $user->id,
        );
        $this->assertTrue($cm->uservisible);
        $cm = cm_info::create(
            (object)['id' => $hiddenpage->cmid, 'course' => $course->id],
            $user->id,
        );
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        $cm = cm_info::create(
            (object)['id' => $hiddenpage->cmid, 'course' => $course->id],
            $manager->id,
        );
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Tests function for getting $course and $cm at once quickly from modinfo
     * based on cmid or cm record.
     */
    public function test_get_course_and_cm_from_cmid(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['shortname' => 'Halls']);
        $page = $generator->create_module(
            'page',
            ['course' => $course->id, 'name' => 'Annie'],
        );

        // Successful usage.
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertInstanceOf(cm_info::class, $cm);
        $this->assertEquals('Annie', $cm->name);

        // Specified module type.
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid, 'page');
        $this->assertEquals('Annie', $cm->name);

        // With id in object.
        $fakecm = (object)['id' => $page->cmid];
        [$course, $cm] = get_course_and_cm_from_cmid($fakecm);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With both id and course in object.
        $fakecm->course = $course->id;
        [$course, $cm] = get_course_and_cm_from_cmid($fakecm);
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course id.
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid, 'page', $course->id);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course object (modified just so we can check it is
        // indeed reusing the supplied object).
        $course->silly = true;
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid, 'page', $course);
        $this->assertEquals('Annie', $cm->name);
        $this->assertTrue($course->silly);

        // Incorrect module type.
        try {
            get_course_and_cm_from_cmid($page->cmid, 'forum');
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertEquals('invalidcoursemoduleid', $e->errorcode);
        }

        // Invalid module name.
        try {
            get_course_and_cm_from_cmid($page->cmid, 'pigs can fly');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertStringContainsString('Invalid modulename parameter', $e->getMessage());
        }

        // Doesn't exist.
        try {
            get_course_and_cm_from_cmid($page->cmid + 1);
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf(dml_exception::class, $e);
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module(
            'page',
            ['course' => $course->id, 'name' => 'Annie', 'visible' => 0],
        );

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user(
            $manager->id,
            $course->id,
            $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST),
        );

        // User can see the normal page but not the hidden one.
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid, 'page', 0, $user->id);
        $this->assertTrue($cm->uservisible);
        [$course, $cm] = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $user->id);
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        [$course, $cm] = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $manager->id);
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Tests function for getting $course and $cm at once quickly from modinfo
     * based on instance id or record.
     */
    public function test_get_course_and_cm_from_instance(): void {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Create a course and an activity.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['shortname' => 'Halls']);
        $page = $generator->create_module(
            'page',
            ['course' => $course->id, 'name' => 'Annie'],
        );

        // Successful usage.
        [$course, $cm] = get_course_and_cm_from_instance($page->id, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertInstanceOf(cm_info::class, $cm);
        $this->assertEquals('Annie', $cm->name);

        // With id in object.
        $fakeinstance = (object)['id' => $page->id];
        [$course, $cm] = get_course_and_cm_from_instance($fakeinstance, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With both id and course in object.
        $fakeinstance->course = $course->id;
        [$course, $cm] = get_course_and_cm_from_instance($fakeinstance, 'page');
        $this->assertEquals('Halls', $course->shortname);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course id.
        [$course, $cm] = get_course_and_cm_from_instance($page->id, 'page', $course->id);
        $this->assertEquals('Annie', $cm->name);

        // With supplied course object (modified just so we can check it is
        // indeed reusing the supplied object).
        $course->silly = true;
        [$course, $cm] = get_course_and_cm_from_instance($page->id, 'page', $course);
        $this->assertEquals('Annie', $cm->name);
        $this->assertTrue($course->silly);

        // Doesn't exist (or is wrong type).
        try {
            get_course_and_cm_from_instance($page->id, 'forum');
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertInstanceOf(dml_exception::class, $e);
        }

        // Invalid module ID.
        try {
            get_course_and_cm_from_instance(-1, 'page', $course);
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertStringContainsString('Invalid module ID: -1', $e->getMessage());
        }

        // Invalid module name.
        try {
            get_course_and_cm_from_cmid($page->cmid, '1337 h4x0ring');
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertStringContainsString('Invalid modulename parameter', $e->getMessage());
        }

        // Create a second hidden activity.
        $hiddenpage = $generator->create_module(
            'page',
            ['course' => $course->id, 'name' => 'Annie', 'visible' => 0],
        );

        // Create 2 user accounts, one is a manager who can see everything.
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);
        $manager = $generator->create_user();
        $generator->enrol_user(
            $manager->id,
            $course->id,
            $DB->get_field('role', 'id', ['shortname' => 'manager'], MUST_EXIST),
        );

        // User can see the normal page but not the hidden one.
        [$course, $cm] = get_course_and_cm_from_cmid($page->cmid, 'page', 0, $user->id);
        $this->assertTrue($cm->uservisible);
        [$course, $cm] = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $user->id);
        $this->assertFalse($cm->uservisible);

        // Manager can see the hidden one too.
        [$course, $cm] = get_course_and_cm_from_cmid($hiddenpage->cmid, 'page', 0, $manager->id);
        $this->assertTrue($cm->uservisible);
    }

    /**
     * Test test_get_section_info_by_id method
     *
     * @param int $sectionnum the section number
     * @param int $strictness the search strict mode
     * @param bool $expectnull if the function will return a null
     * @param bool $expectexception if the function will throw an exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_section_info_by_id_provider')]
    public function test_get_section_info_by_id(
        int $sectionnum,
        int $strictness = IGNORE_MISSING,
        bool $expectnull = false,
        bool $expectexception = false
    ): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course with 4 sections.
        $course = $this->getDataGenerator()->create_course(['numsections' => 4]);

        // Index sections.
        $sectionindex = [];
        $modinfo = get_fast_modinfo($course);
        $allsections = $modinfo->get_section_info_all();
        foreach ($allsections as $section) {
            $sectionindex[$section->sectionnum] = $section->id;
        }

        if ($expectexception) {
            $this->expectException(moodle_exception::class);
        }

        $sectionid = $sectionindex[$sectionnum] ?? -1;

        $section = $modinfo->get_section_info_by_id($sectionid, $strictness);

        if ($expectnull) {
            $this->assertNull($section);
        } else {
            $this->assertEquals($sectionid, $section->id);
            $this->assertEquals($sectionnum, $section->sectionnum);
        }
    }

    /**
     * Data provider for test_get_section_info_by_id().
     *
     * @return array
     */
    public static function get_section_info_by_id_provider(): array {
        return [
            'Valid section id' => [
                'sectionnum' => 1,
                'strictness' => IGNORE_MISSING,
                'expectnull' => false,
                'expectexception' => false,
            ],
            'Section zero' => [
                'sectionnum' => 0,
                'strictness' => IGNORE_MISSING,
                'expectnull' => false,
                'expectexception' => false,
            ],
            'invalid section ignore missing' => [
                'sectionnum' => -1,
                'strictness' => IGNORE_MISSING,
                'expectnull' => true,
                'expectexception' => false,
            ],
            'invalid section must exists' => [
                'sectionnum' => -1,
                'strictness' => MUST_EXIST,
                'expectnull' => false,
                'expectexception' => true,
            ],
        ];
    }

    /**
     * Test get_section_info_by_component method
     *
     * @param string $component the component name
     * @param int $itemid the section number
     * @param int $strictness the search strict mode
     * @param bool $expectnull if the function will return a null
     * @param bool $expectexception if the function will throw an exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('get_section_info_by_component_provider')]
    public function test_get_section_info_by_component(
        string $component,
        int $itemid,
        int $strictness,
        bool $expectnull,
        bool $expectexception
    ): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        formatactions::section($course)->create_delegated('mod_forum', 42);

        $modinfo = get_fast_modinfo($course);

        if ($expectexception) {
            $this->expectException(moodle_exception::class);
        }

        $section = $modinfo->get_section_info_by_component($component, $itemid, $strictness);

        if ($expectnull) {
            $this->assertNull($section);
        } else {
            $this->assertEquals($component, $section->component);
            $this->assertEquals($itemid, $section->itemid);
        }
    }

    /**
     * Data provider for test_get_section_info_by_component().
     *
     * @return array
     */
    public static function get_section_info_by_component_provider(): array {
        return [
            'Valid component and itemid' => [
                'component' => 'mod_forum',
                'itemid' => 42,
                'strictness' => IGNORE_MISSING,
                'expectnull' => false,
                'expectexception' => false,
            ],
            'Invalid component' => [
                'component' => 'mod_nonexisting',
                'itemid' => 42,
                'strictness' => IGNORE_MISSING,
                'expectnull' => true,
                'expectexception' => false,
            ],
            'Invalid itemid' => [
                'component' => 'mod_forum',
                'itemid' => 0,
                'strictness' => IGNORE_MISSING,
                'expectnull' => true,
                'expectexception' => false,
            ],
            'Invalid component and itemid' => [
                'component' => 'mod_nonexisting',
                'itemid' => 0,
                'strictness' => IGNORE_MISSING,
                'expectnull' => true,
                'expectexception' => false,
            ],
            'Invalid component must exists' => [
                'component' => 'mod_nonexisting',
                'itemid' => 42,
                'strictness' => MUST_EXIST,
                'expectnull' => true,
                'expectexception' => true,
            ],
            'Invalid itemid must exists' => [
                'component' => 'mod_forum',
                'itemid' => 0,
                'strictness' => MUST_EXIST,
                'expectnull' => true,
                'expectexception' => true,
            ],
            'Invalid component and itemid must exists' => [
                'component' => 'mod_nonexisting',
                'itemid' => 0,
                'strictness' => MUST_EXIST,
                'expectnull' => false,
                'expectexception' => true,
            ],
        ];
    }

    /**
     * Test has_delegated_sections method
     */
    public function test_has_delegated_sections(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        $modinfo = get_fast_modinfo($course);
        $this->assertFalse($modinfo->has_delegated_sections());

        formatactions::section($course)->create_delegated('mod_forum', 42);

        $modinfo = get_fast_modinfo($course);
        $this->assertTrue($modinfo->has_delegated_sections());
    }

    /**
     * Test purge_section_cache_by_id method
     */
    public function test_purge_section_cache_by_id(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 3],
            ['createsections' => true],
        );
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        $modinfo = get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        $numberedsections = $modinfo->get_section_info_all();

        // Make sure that we will have 4 section caches here.
        $this->assertCount(4, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[3]->id, $sectioncaches);

        // Purge cache for the section by id.
        modinfo::purge_course_section_cache_by_id(
            $course->id,
            $numberedsections[1]->id
        );
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 3 section caches left.
        $this->assertCount(3, $sectioncaches);
        $this->assertArrayNotHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[3]->id, $sectioncaches);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }

    /**
     * Test purge_section_cache_by_number method
     */
    public function test_section_cache_by_number(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course(
            ['format' => 'topics', 'numsections' => 3],
            ['createsections' => true]
        );
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        $modinfo = get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        $numberedsections = $modinfo->get_section_info_all();

        // Make sure that we will have 4 section caches here.
        $this->assertCount(4, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[3]->id, $sectioncaches);

        // Purge cache for the section with section number is 1.
        modinfo::purge_course_section_cache_by_number($course->id, 1);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        // Get the section cache.
        $sectioncaches = $coursemodinfo->sectioncache;

        // Make sure that we will have 3 section caches left.
        $this->assertCount(3, $sectioncaches);
        $this->assertArrayNotHasKey($numberedsections[1]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[0]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[2]->id, $sectioncaches);
        $this->assertArrayHasKey($numberedsections[3]->id, $sectioncaches);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }

    /**
     * Purge a single course module from the cache.
     */
    public function test_purge_course_module(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm2 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm3 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm4 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        $this->assertCount(4, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm1->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm2->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm3->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm4->cmid, $coursemodinfo->modinfo);

        modinfo::purge_course_module_cache($course->id, $cm1->cmid);

        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        $this->assertCount(3, $coursemodinfo->modinfo);
        $this->assertArrayNotHasKey($cm1->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm2->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm3->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm4->cmid, $coursemodinfo->modinfo);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }

    /**
     * Purge a multiple course modules from the cache.
     */
    public function test_purge_multiple_course_modules(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate the course and pre-requisite section.
        $course = $this->getDataGenerator()->create_course();
        $cm1 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm2 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm3 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        $cm4 = $this->getDataGenerator()->create_module('page', ['course' => $course]);
        // Reset course cache.
        rebuild_course_cache($course->id, true);
        // Build course cache.
        get_fast_modinfo($course->id);
        // Get the course modinfo cache.
        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        $this->assertCount(4, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm1->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm2->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm3->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm4->cmid, $coursemodinfo->modinfo);

        modinfo::purge_course_modules_cache($course->id, [$cm2->cmid, $cm3->cmid]);

        $coursemodinfo = $cache->get_versioned($course->id, $course->cacherev);
        $this->assertCount(2, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm1->cmid, $coursemodinfo->modinfo);
        $this->assertArrayNotHasKey($cm2->cmid, $coursemodinfo->modinfo);
        $this->assertArrayNotHasKey($cm3->cmid, $coursemodinfo->modinfo);
        $this->assertArrayHasKey($cm4->cmid, $coursemodinfo->modinfo);
        // Make sure that the cacherev will be reset.
        $this->assertEquals(-1, $coursemodinfo->cacherev);
    }

    /**
     * Test get_cm() method to output course module id in the exception text.
     */
    public function test_invalid_course_module_id(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $forum0 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id], ['section' => 0]);
        $forum1 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id], ['section' => 0]);
        $forum2 = $this->getDataGenerator()->create_module('assign', ['course' => $course->id], ['section' => 0]);

        // Break section sequence.
        $modinfo = get_fast_modinfo($course->id);
        $sectionid = $modinfo->get_section_info(0)->id;
        $section = $DB->get_record('course_sections', ['id' => $sectionid]);
        $sequence = explode(',', $section->sequence);
        $sequence = array_diff($sequence, [$forum1->cmid]);
        $section->sequence = implode(',', $sequence);
        $DB->update_record('course_sections', $section);

        // Assert exception text.
        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid course module ID: ' . $forum1->cmid);
        delete_course($course, false);
    }

    /**
     * Tests that if the modinfo cache returns a newer-than-expected version, Moodle won't rebuild
     * it.
     *
     * This is important to avoid wasted time/effort and poor performance, for example in cases
     * where multiple requests are accessing the course.
     *
     * Certain cases could be particularly bad if this test fails. For example, if using clustered
     * databases where there is a 100ms delay between updates to the course table being available
     * to all users (but no such delay on the cache infrastructure), then during that 100ms, every
     * request that calls get_fast_modinfo and uses the read-only database will rebuild the course
     * cache. Since these will then create a still-newer version, future requests for the next
     * 100ms will also rebuild it again... etc.
     */
    public function test_get_modinfo_with_newer_version(): void {
        global $DB;

        $this->resetAfterTest();

        // Get info about a course and build the initial cache, then drop it from memory.
        $course = $this->getDataGenerator()->create_course();
        get_fast_modinfo($course);
        get_fast_modinfo(0, 0, true);

        // User A starts a request, which takes some time...
        $useracourse = $DB->get_record('course', ['id' => $course->id]);

        // User B also starts a request and makes a change to the course.
        $userbcourse = $DB->get_record('course', ['id' => $course->id]);
        $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        rebuild_course_cache($userbcourse->id, false);

        // Finally, user A's request now gets modinfo. It should accept the version from B even
        // though the course version (of cache) is newer than the one expected by A.
        $before = $DB->perf_get_queries();
        $modinfo = get_fast_modinfo($useracourse);
        $after = $DB->perf_get_queries();
        $this->assertEquals($after, $before, 'Should use cached version, making no DB queries');

        // Obviously, modinfo should include the Page now.
        $this->assertCount(1, $modinfo->get_instances_of('page'));
    }

    /**
     * Test the modinfo::purge_course_caches() function with a
     * one-course array, a two-course array, and an empty array, and ensure
     * that only the courses specified have their course cache version
     * incremented (or all course caches if none specified).
     */
    public function test_multiple_modinfo_cache_purge(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $cache = cache::make('core', 'coursemodinfo');

        // Generate two courses and pre-requisite modules for targeted course
        // cache tests.
        $courseone = $this->getDataGenerator()->create_course(
            [
                'format' => 'topics',
                'numsections' => 3,
            ],
            [
                'createsections' => true,
            ],
        );
        $coursetwo = $this->getDataGenerator()->create_course(
            [
                'format' => 'topics',
                'numsections' => 3,
            ],
            [
                'createsections' => true,
            ],
        );
        $coursethree = $this->getDataGenerator()->create_course(
            [
                'format' => 'topics',
                'numsections' => 3,
            ],
            [
                'createsections' => true,
            ],
        );

        // Make sure the cacherev is set for all three.
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertGreaterThan(0, $cacherevone);
        $prevcacherevone = $cacherevone;

        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertGreaterThan(0, $cacherevtwo);
        $prevcacherevtwo = $cacherevtwo;

        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertGreaterThan(0, $cacherevthree);
        $prevcacherevthree = $cacherevthree;

        // Reset course caches and make sure cacherev is bumped up but cache is empty.
        rebuild_course_cache($courseone->id, true);
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertGreaterThan($prevcacherevone, $cacherevone);
        $this->assertEmpty($cache->get_versioned($courseone->id, $prevcacherevone));
        $prevcacherevone = $cacherevone;

        rebuild_course_cache($coursetwo->id, true);
        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertGreaterThan($prevcacherevtwo, $cacherevtwo);
        $this->assertEmpty($cache->get_versioned($coursetwo->id, $prevcacherevtwo));
        $prevcacherevtwo = $cacherevtwo;

        rebuild_course_cache($coursethree->id, true);
        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertGreaterThan($prevcacherevthree, $cacherevthree);
        $this->assertEmpty($cache->get_versioned($coursethree->id, $prevcacherevthree));
        $prevcacherevthree = $cacherevthree;

        // Build course caches. Cacherev should not change but caches are now not empty. Make sure cacherev is the same everywhere.
        $modinfoone = get_fast_modinfo($courseone->id);
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertEquals($prevcacherevone, $cacherevone);
        $cachedvalueone = $cache->get_versioned($courseone->id, $cacherevone);
        $this->assertNotEmpty($cachedvalueone);
        $this->assertEquals($cacherevone, $cachedvalueone->cacherev);
        $this->assertEquals($cacherevone, $modinfoone->get_course()->cacherev);
        $prevcacherevone = $cacherevone;

        $modinfotwo = get_fast_modinfo($coursetwo->id);
        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertEquals($prevcacherevtwo, $cacherevtwo);
        $cachedvaluetwo = $cache->get_versioned($coursetwo->id, $cacherevtwo);
        $this->assertNotEmpty($cachedvaluetwo);
        $this->assertEquals($cacherevtwo, $cachedvaluetwo->cacherev);
        $this->assertEquals($cacherevtwo, $modinfotwo->get_course()->cacherev);
        $prevcacherevtwo = $cacherevtwo;

        $modinfothree = get_fast_modinfo($coursethree->id);
        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertEquals($prevcacherevthree, $cacherevthree);
        $cachedvaluethree = $cache->get_versioned($coursethree->id, $cacherevthree);
        $this->assertNotEmpty($cachedvaluethree);
        $this->assertEquals($cacherevthree, $cachedvaluethree->cacherev);
        $this->assertEquals($cacherevthree, $modinfothree->get_course()->cacherev);
        $prevcacherevthree = $cacherevthree;

        // Purge course one's cache. Cacherev must be incremented (but only for
        // course one, check course two and three in next step).
        modinfo::purge_course_caches([$courseone->id]);

        get_fast_modinfo($courseone->id);
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertGreaterThan($prevcacherevone, $cacherevone);
        $prevcacherevone = $cacherevone;

        // Confirm course two and three's cache shouldn't have been affected.
        get_fast_modinfo($coursetwo->id);
        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertEquals($prevcacherevtwo, $cacherevtwo);
        $prevcacherevtwo = $cacherevtwo;

        get_fast_modinfo($coursethree->id);
        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertEquals($prevcacherevthree, $cacherevthree);
        $prevcacherevthree = $cacherevthree;

        // Purge course two and three's cache. Cacherev must be incremented (but only for
        // course two and three, then check course one hasn't changed in next step).
        modinfo::purge_course_caches([$coursetwo->id, $coursethree->id]);

        get_fast_modinfo($coursetwo->id);
        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertGreaterThan($prevcacherevtwo, $cacherevtwo);
        $prevcacherevtwo = $cacherevtwo;

        get_fast_modinfo($coursethree->id);
        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertGreaterThan($prevcacherevthree, $cacherevthree);
        $prevcacherevthree = $cacherevthree;

        // Confirm course one's cache shouldn't have been affected.
        get_fast_modinfo($courseone->id);
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertEquals($prevcacherevone, $cacherevone);
        $prevcacherevone = $cacherevone;

        // Purge all course caches. Cacherev must be incremented for all three courses.
        modinfo::purge_course_caches();
        get_fast_modinfo($courseone->id);
        $cacherevone = $DB->get_field('course', 'cacherev', ['id' => $courseone->id]);
        $this->assertGreaterThan($prevcacherevone, $cacherevone);

        get_fast_modinfo($coursetwo->id);
        $cacherevtwo = $DB->get_field('course', 'cacherev', ['id' => $coursetwo->id]);
        $this->assertGreaterThan($prevcacherevtwo, $cacherevtwo);

        get_fast_modinfo($coursethree->id);
        $cacherevthree = $DB->get_field('course', 'cacherev', ['id' => $coursethree->id]);
        $this->assertGreaterThan($prevcacherevthree, $cacherevthree);
    }

    /**
     * Test get_sections_delegated_by_cm method
     */
    public function test_get_sections_delegated_by_cm(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['numsections' => 1]);

        $modinfo = get_fast_modinfo($course);
        $delegatedsections = $modinfo->get_sections_delegated_by_cm();
        $this->assertEmpty($delegatedsections);

        // Add a section delegated by a course module.
        $subsection = $this->getDataGenerator()->create_module('subsection', ['course' => $course]);
        $modinfo = get_fast_modinfo($course);
        $delegatedsections = $modinfo->get_sections_delegated_by_cm();
        $this->assertCount(1, $delegatedsections);
        $this->assertArrayHasKey($subsection->cmid, $delegatedsections);

        // Add a section delegated by a block.
        formatactions::section($course)->create_delegated('block_site_main_menu', 1);
        $modinfo = get_fast_modinfo($course);
        $delegatedsections = $modinfo->get_sections_delegated_by_cm();
        // Sections delegated by a block shouldn't be returned.
        $this->assertCount(1, $delegatedsections);
    }


    /**
     * Test for sort_cm_array method.
     */
    public function test_sort_cm_array(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        // Create a course with 4 sections.
        $course = $generator->create_course(['numsections' => 3]);
        $generator->create_module('page', ['name' => 'Page s1', 'course' => $course->id, 'section' => 0]);
        $generator->create_module('page', ['name' => 'Page s2', 'course' => $course->id, 'section' => 1]);
        $generator->create_module('assign', ['name' => 'Assign s3', 'course' => $course->id, 'section' => 2]);
        $generator->create_module('page', ['name' => 'Page s3', 'course' => $course->id, 'section' => 3]);
        // Check we return all cms in order.
        $cms = get_fast_modinfo($course)->get_instances_of('page');
        get_fast_modinfo($course)->sort_cm_array($cms);
        $this->assertCount(3, $cms);
        $this->assertEquals(['Page s1', 'Page s2', 'Page s3'], array_column($cms, 'name'));

        // Generate some delegated sections (not listed).
        $module = $this->getDataGenerator()->create_module('subsection', (object) ['course' => $course->id, 'section' => 1]);
        $sub1 = get_fast_modinfo($course)->get_section_info_by_component('mod_subsection', $module->id);
        $generator->create_module('page', ['name' => 'Page sub1', 'course' => $course->id, 'section' => $sub1->sectionnum]);
        $generator->create_module('page', ['name' => 'Page sub2', 'course' => $course->id, 'section' => $sub1->sectionnum]);
        $generator->create_module('assign', ['name' => 'Assign sub1', 'course' => $course->id, 'section' => $sub1->sectionnum]);

        $cms = get_fast_modinfo($course)->get_instances_of('page');
        get_fast_modinfo($course)->sort_cm_array($cms);
        $this->assertCount(5, $cms);
        $this->assertEquals(['Page s1', 'Page s2', 'Page sub1', 'Page sub2', 'Page s3'], array_column($cms, 'name'));

        $cms = get_fast_modinfo($course)->get_instances_of('assign');
        get_fast_modinfo($course)->sort_cm_array($cms);
        $this->assertCount(2, $cms);
        $this->assertEquals(['Assign sub1', 'Assign s3'], array_column($cms, 'name'));
    }

    /**
     * Test for get_instance_of method.
     */
    public function test_get_instance_of(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        // Create a course with 4 sections.
        $course = $generator->create_course(['numsections' => 3]);
        $generator->create_module('page', ['name' => 'Page s1', 'course' => $course->id, 'section' => 0]);
        $generator->create_module('page', ['name' => 'Page s2', 'course' => $course->id, 'section' => 1]);
        $generator->create_module('assign', ['name' => 'Assign s3', 'course' => $course->id, 'section' => 2]);

        $modinfo = get_fast_modinfo($course);
        $pagecms = array_values($modinfo->get_instances_of('page'));
        $assigncms = array_values($modinfo->get_instances_of('assign'));
        $this->assertCount(2, $pagecms);
        $this->assertCount(1, $assigncms);

        $this->assertEquals('Page s1', $modinfo->get_instance_of('page', $pagecms[0]->instance)->name);
        $this->assertEquals('Page s2', $modinfo->get_instance_of('page', $pagecms[1]->instance)->name);
        $this->assertEquals('Assign s3', $modinfo->get_instance_of('assign', $assigncms[0]->instance)->name);

        $this->assertNull($modinfo->get_instance_of('page', 99999));
        $this->assertNull($modinfo->get_instance_of('assign', 99999));
        $this->assertNull($modinfo->get_instance_of('nonexisting', 99999));
    }

    /**
     * Test for get_instance_of method when asking for a non existing module with MUST_EXIST.
     */
    public function test_get_instance_of_exception(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        // Create a course with 4 sections.
        $course = $generator->create_course(['numsections' => 3]);
        $generator->create_module('page', ['name' => 'Page s1', 'course' => $course->id, 'section' => 0]);
        $generator->create_module('page', ['name' => 'Page s2', 'course' => $course->id, 'section' => 1]);
        $generator->create_module('assign', ['name' => 'Assign s3', 'course' => $course->id, 'section' => 2]);

        $modinfo = get_fast_modinfo($course);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid module ID: 99999');
        $modinfo->get_instance_of('page', 99999, MUST_EXIST);
    }
}
