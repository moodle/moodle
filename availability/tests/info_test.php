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
 * Unit tests for info and subclasses.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_availability\info;
use core_availability\info_module;
use core_availability\info_section;

/**
 * Unit tests for info and subclasses.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class info_testcase extends advanced_testcase {
    public function setUp(): void {
        // Load the mock condition so that it can be used.
        require_once(__DIR__ . '/fixtures/mock_condition.php');
    }

    /**
     * Tests the info_module class (is_available, get_full_information).
     */
    public function test_info_module() {
        global $DB, $CFG;

        // Create a course and pages.
        $CFG->enableavailability = 0;
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $rec = array('course' => $course);
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance($rec);
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance($rec);
        $page3 = $generator->get_plugin_generator('mod_page')->create_instance($rec);
        $page4 = $generator->get_plugin_generator('mod_page')->create_instance($rec);

        // Set up the availability option for the pages to mock options.
        $DB->set_field('course_modules', 'availability', '{"op":"|","show":true,"c":[' .
                '{"type":"mock","a":false,"m":"grandmaster flash"}]}', array('id' => $page1->cmid));
        $DB->set_field('course_modules', 'availability', '{"op":"|","show":true,"c":[' .
                '{"type":"mock","a":true,"m":"the furious five"}]}', array('id' => $page2->cmid));

        // Third page is invalid. (Fourth has no availability settings.)
        $DB->set_field('course_modules', 'availability', '{{{', array('id' => $page3->cmid));

        $modinfo = get_fast_modinfo($course);
        $cm1 = $modinfo->get_cm($page1->cmid);
        $cm2 = $modinfo->get_cm($page2->cmid);
        $cm3 = $modinfo->get_cm($page3->cmid);
        $cm4 = $modinfo->get_cm($page4->cmid);

        // Do availability and full information checks.
        $info = new info_module($cm1);
        $information = '';
        $this->assertFalse($info->is_available($information));
        $this->assertEquals('SA: grandmaster flash', $information);
        $this->assertEquals('SA: [FULL]grandmaster flash', $info->get_full_information());
        $info = new info_module($cm2);
        $this->assertTrue($info->is_available($information));
        $this->assertEquals('', $information);
        $this->assertEquals('SA: [FULL]the furious five', $info->get_full_information());

        // Check invalid one.
        $info = new info_module($cm3);
        $this->assertFalse($info->is_available($information));
        $debugging = $this->getDebuggingMessages();
        $this->resetDebugging();
        $this->assertEquals(1, count($debugging));
        $this->assertStringContainsString('Invalid availability', $debugging[0]->message);

        // Check empty one.
        $info = new info_module($cm4);
        $this->assertTrue($info->is_available($information));
        $this->assertEquals('', $information);
        $this->assertEquals('', $info->get_full_information());
    }

    /**
     * Tests the info_section class (is_available, get_full_information).
     */
    public function test_info_section() {
        global $DB;

        // Create a course.
        $this->setAdminUser();
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 4), array('createsections' => true));

        // Set up the availability option for the sections to mock options.
        $DB->set_field('course_sections', 'availability', '{"op":"|","show":true,"c":[' .
                '{"type":"mock","a":false,"m":"public"}]}',
                array('course' => $course->id, 'section' => 1));
        $DB->set_field('course_sections', 'availability', '{"op":"|","show":true,"c":[' .
                '{"type":"mock","a":true,"m":"enemy"}]}',
                array('course' => $course->id, 'section' => 2));

        // Third section is invalid. (Fourth has no availability setting.)
        $DB->set_field('course_sections', 'availability', '{{{',
                array('course' => $course->id, 'section' => 3));

        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();

        // Do availability and full information checks.
        $info = new info_section($sections[1]);
        $information = '';
        $this->assertFalse($info->is_available($information));
        $this->assertEquals('SA: public', $information);
        $this->assertEquals('SA: [FULL]public', $info->get_full_information());
        $info = new info_section($sections[2]);
        $this->assertTrue($info->is_available($information));
        $this->assertEquals('', $information);
        $this->assertEquals('SA: [FULL]enemy', $info->get_full_information());

        // Check invalid one.
        $info = new info_section($sections[3]);
        $this->assertFalse($info->is_available($information));
        $debugging = $this->getDebuggingMessages();
        $this->resetDebugging();
        $this->assertEquals(1, count($debugging));
        $this->assertStringContainsString('Invalid availability', $debugging[0]->message);

        // Check empty one.
        $info = new info_section($sections[4]);
        $this->assertTrue($info->is_available($information));
        $this->assertEquals('', $information);
        $this->assertEquals('', $info->get_full_information());
    }

    /**
     * Tests the is_user_visible() static function in info_module.
     */
    public function test_is_user_visible() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        $this->resetAfterTest();
        $CFG->enableavailability = 0;

        // Create a course and some pages:
        // 0. Invisible due to visible=0.
        // 1. Availability restriction (mock, set to fail).
        // 2. Availability restriction on section (mock, set to fail).
        // 3. Actually visible.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 1), array('createsections' => true));
        $rec = array('course' => $course, );
        $pages = array();
        $pagegen = $generator->get_plugin_generator('mod_page');
        $pages[0] = $pagegen->create_instance($rec, array('visible' => 0));
        $pages[1] = $pagegen->create_instance($rec);
        $pages[2] = $pagegen->create_instance($rec);
        $pages[3] = $pagegen->create_instance($rec);
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(1);
        $cm = $modinfo->get_cm($pages[2]->cmid);
        moveto_module($cm, $section);

        // Set the availability restrictions in database. The enableavailability
        // setting is off so these do not take effect yet.
        $notavailable = '{"op":"|","show":true,"c":[{"type":"mock","a":false}]}';
        $DB->set_field('course_sections', 'availability',
                $notavailable, array('id' => $section->id));
        $DB->set_field('course_modules', 'availability',
                $notavailable, array('id' => $pages[1]->cmid));
        get_fast_modinfo($course, 0, true);

        // Set up 4 users - a teacher and student plus somebody who isn't even
        // on the course. Also going to use admin user and a spare student to
        // avoid cache problems.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $teacher = $generator->create_user();
        $student = $generator->create_user();
        $student2 = $generator->create_user();
        $other = $generator->create_user();
        $admin = $DB->get_record('user', array('username' => 'admin'));
        $generator->enrol_user($teacher->id, $course->id, $roleids['teacher']);
        $generator->enrol_user($student->id, $course->id, $roleids['student']);
        $generator->enrol_user($student2->id, $course->id, $roleids['student']);

        // Basic case when availability disabled, for visible item.
        $this->assertTrue(info_module::is_user_visible($pages[3]->cmid, $student->id, false));

        // Specifying as an object should not make any queries.
        $cm = $DB->get_record('course_modules', array('id' => $pages[3]->cmid));
        $beforequeries = $DB->perf_get_queries();
        $this->assertTrue(info_module::is_user_visible($cm, $student->id, false));
        $this->assertEquals($beforequeries, $DB->perf_get_queries());

        // Specifying as cm_info for correct user should not make any more queries
        // if we have already obtained dynamic data.
        $modinfo = get_fast_modinfo($course, $student->id);
        $cminfo = $modinfo->get_cm($cm->id);
        // This will obtain dynamic data.
        $name = $cminfo->name;
        $beforequeries = $DB->perf_get_queries();
        $this->assertTrue(info_module::is_user_visible($cminfo, $student->id, false));
        $this->assertEquals($beforequeries, $DB->perf_get_queries());

        // Function does not care if you are in the course (unless $checkcourse).
        $this->assertTrue(info_module::is_user_visible($cm, $other->id, false));

        // With $checkcourse, check for enrolled, not enrolled, and admin user.
        $this->assertTrue(info_module::is_user_visible($cm, $student->id, true));
        $this->assertFalse(info_module::is_user_visible($cm, $other->id, true));
        $this->assertTrue(info_module::is_user_visible($cm, $admin->id, true));

        // With availability off, the student can access all except the
        // visible=0 one.
        $this->assertFalse(info_module::is_user_visible($pages[0]->cmid, $student->id, false));
        $this->assertTrue(info_module::is_user_visible($pages[1]->cmid, $student->id, false));
        $this->assertTrue(info_module::is_user_visible($pages[2]->cmid, $student->id, false));

        // Teacher and admin can even access the visible=0 one.
        $this->assertTrue(info_module::is_user_visible($pages[0]->cmid, $teacher->id, false));
        $this->assertTrue(info_module::is_user_visible($pages[0]->cmid, $admin->id, false));

        // Now enable availability (and clear cache).
        $CFG->enableavailability = true;
        get_fast_modinfo($course, 0, true);

        // Student cannot access the activity restricted by its own or by the
        // section's availability.
        $this->assertFalse(info_module::is_user_visible($pages[1]->cmid, $student->id, false));
        $this->assertFalse(info_module::is_user_visible($pages[2]->cmid, $student->id, false));
    }

    /**
     * Tests the convert_legacy_fields function used in restore.
     */
    public function test_convert_legacy_fields() {
        // Check with no availability conditions first.
        $rec = (object)array('availablefrom' => 0, 'availableuntil' => 0,
                'groupingid' => 7, 'showavailability' => 1);
        $this->assertNull(info::convert_legacy_fields($rec, false));

        // Check same list for a section.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"grouping","id":7}]}',
                info::convert_legacy_fields($rec, true));

        // Check groupmembersonly with grouping.
        $rec->groupmembersonly = 1;
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"grouping","id":7}]}',
                info::convert_legacy_fields($rec, false));

        // Check groupmembersonly without grouping.
        $rec->groupingid = 0;
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"group"}]}',
                info::convert_legacy_fields($rec, false));

        // Check start date.
        $rec->groupmembersonly = 0;
        $rec->availablefrom = 123;
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"date","d":">=","t":123}]}',
                info::convert_legacy_fields($rec, false));

        // Start date with show = false.
        $rec->showavailability = 0;
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"date","d":">=","t":123}]}',
                info::convert_legacy_fields($rec, false));

        // End date.
        $rec->showavailability = 1;
        $rec->availablefrom = 0;
        $rec->availableuntil = 456;
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"date","d":"<","t":456}]}',
                info::convert_legacy_fields($rec, false));

        // All together now.
        $rec->groupingid = 7;
        $rec->groupmembersonly = 1;
        $rec->availablefrom = 123;
        $this->assertEquals(
                '{"op":"&","showc":[false,true,false],"c":[' .
                '{"type":"grouping","id":7},' .
                '{"type":"date","d":">=","t":123},' .
                '{"type":"date","d":"<","t":456}' .
                ']}',
                info::convert_legacy_fields($rec, false));
        $this->assertEquals(
                '{"op":"&","showc":[false,true,false],"c":[' .
                '{"type":"grouping","id":7},' .
                '{"type":"date","d":">=","t":123},' .
                '{"type":"date","d":"<","t":456}' .
                ']}',
                info::convert_legacy_fields($rec, false, true));
    }

    /**
     * Tests the add_legacy_availability_condition function used in restore.
     */
    public function test_add_legacy_availability_condition() {
        // Completion condition tests.
        $rec = (object)array('sourcecmid' => 7, 'requiredcompletion' => 1);
        // No previous availability, show = true.
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"completion","cm":7,"e":1}]}',
                info::add_legacy_availability_condition(null, $rec, true));
        // No previous availability, show = false.
        $this->assertEquals(
                '{"op":"&","showc":[false],"c":[{"type":"completion","cm":7,"e":1}]}',
                info::add_legacy_availability_condition(null, $rec, false));

        // Existing availability.
        $before = '{"op":"&","showc":[true],"c":[{"type":"date","d":">=","t":70}]}';
        $this->assertEquals(
                '{"op":"&","showc":[true,true],"c":['.
                '{"type":"date","d":">=","t":70},' .
                '{"type":"completion","cm":7,"e":1}' .
                ']}',
                info::add_legacy_availability_condition($before, $rec, true));

        // Grade condition tests.
        $rec = (object)array('gradeitemid' => 3, 'grademin' => 7, 'grademax' => null);
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":3,"min":7.00000}]}',
                info::add_legacy_availability_condition(null, $rec, true));
        $rec->grademax = 8;
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":3,"min":7.00000,"max":8.00000}]}',
                info::add_legacy_availability_condition(null, $rec, true));
        unset($rec->grademax);
        unset($rec->grademin);
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[{"type":"grade","id":3}]}',
                info::add_legacy_availability_condition(null, $rec, true));

        // Note: There is no need to test the grade condition with show
        // true/false and existing availability, because this uses the same
        // function.
    }

    /**
     * Tests the add_legacy_availability_field_condition function used in restore.
     */
    public function test_add_legacy_availability_field_condition() {
        // User field, normal operator.
        $rec = (object)array('userfield' => 'email', 'shortname' => null,
                'operator' => 'contains', 'value' => '@');
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[' .
                '{"type":"profile","op":"contains","sf":"email","v":"@"}]}',
                info::add_legacy_availability_field_condition(null, $rec, true));

        // User field, non-value operator.
        $rec = (object)array('userfield' => 'email', 'shortname' => null,
                'operator' => 'isempty', 'value' => '');
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[' .
                '{"type":"profile","op":"isempty","sf":"email"}]}',
                info::add_legacy_availability_field_condition(null, $rec, true));

        // Custom field.
        $rec = (object)array('userfield' => null, 'shortname' => 'frogtype',
                'operator' => 'isempty', 'value' => '');
        $this->assertEquals(
                '{"op":"&","showc":[true],"c":[' .
                '{"type":"profile","op":"isempty","cf":"frogtype"}]}',
                info::add_legacy_availability_field_condition(null, $rec, true));
    }

    /**
     * Tests the filter_user_list() and get_user_list_sql() functions.
     */
    public function test_filter_user_list() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        $this->resetAfterTest();
        $CFG->enableavailability = true;

        // Create a course with 2 sections and 2 pages and 3 users.
        // Availability is set up initially on the 'page/section 2' items.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 2), array('createsections' => true));
        $u1 = $generator->create_user();
        $u2 = $generator->create_user();
        $u3 = $generator->create_user();
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $allusers = array($u1->id => $u1, $u2->id => $u2, $u3->id => $u3);
        $generator->enrol_user($u1->id, $course->id, $studentroleid);
        $generator->enrol_user($u2->id, $course->id, $studentroleid);
        $generator->enrol_user($u3->id, $course->id, $studentroleid);

        // Page 2 allows access to users 2 and 3, while section 2 allows access
        // to users 1 and 2.
        $pagegen = $generator->get_plugin_generator('mod_page');
        $page = $pagegen->create_instance(array('course' => $course));
        $page2 = $pagegen->create_instance(array('course' => $course,
                'availability' => '{"op":"|","show":true,"c":[{"type":"mock","filter":[' .
                $u2->id . ',' . $u3->id . ']}]}'));
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info(1);
        $section2 = $modinfo->get_section_info(2);
        $DB->set_field('course_sections', 'availability',
                '{"op":"|","show":true,"c":[{"type":"mock","filter":[' . $u1->id . ',' . $u2->id .']}]}',
                array('id' => $section2->id));
        moveto_module($modinfo->get_cm($page2->cmid), $section2);

        // With no restrictions, returns full list.
        $info = new info_module($modinfo->get_cm($page->cmid));
        $this->assertEquals(array($u1->id, $u2->id, $u3->id),
                array_keys($info->filter_user_list($allusers)));
        $this->assertEquals(array('', array()), $info->get_user_list_sql(true));

        // Set an availability restriction in database for section 1.
        // For the section we set it so it doesn't support filters; for the
        // module we have a filter.
        $DB->set_field('course_sections', 'availability',
                '{"op":"|","show":true,"c":[{"type":"mock","a":false}]}',
                array('id' => $section->id));
        $DB->set_field('course_modules', 'availability',
                '{"op":"|","show":true,"c":[{"type":"mock","filter":[' . $u3->id .']}]}',
                array('id' => $page->cmid));
        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);

        // Now it should work (for the module).
        $info = new info_module($modinfo->get_cm($page->cmid));
        $expected = array($u3->id);
        $this->assertEquals($expected,
                array_keys($info->filter_user_list($allusers)));
        list ($sql, $params) = $info->get_user_list_sql();
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);
        $info = new info_section($modinfo->get_section_info(1));
        $this->assertEquals(array($u1->id, $u2->id, $u3->id),
                array_keys($info->filter_user_list($allusers)));
        $this->assertEquals(array('', array()), $info->get_user_list_sql(true));

        // With availability disabled, module returns full list too.
        $CFG->enableavailability = false;
        $info = new info_module($modinfo->get_cm($page->cmid));
        $this->assertEquals(array($u1->id, $u2->id, $u3->id),
                array_keys($info->filter_user_list($allusers)));
        $this->assertEquals(array('', array()), $info->get_user_list_sql(true));

        // Check the other section...
        $CFG->enableavailability = true;
        $info = new info_section($modinfo->get_section_info(2));
        $expected = array($u1->id, $u2->id);
        $this->assertEquals($expected, array_keys($info->filter_user_list($allusers)));
        list ($sql, $params) = $info->get_user_list_sql(true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);

        // And the module in that section - which has combined the section and
        // module restrictions.
        $info = new info_module($modinfo->get_cm($page2->cmid));
        $expected = array($u2->id);
        $this->assertEquals($expected, array_keys($info->filter_user_list($allusers)));
        list ($sql, $params) = $info->get_user_list_sql(true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);

        // If the students have viewhiddenactivities, they get past the module
        // restriction.
        role_change_permission($studentroleid, context_module::instance($page2->cmid),
                'moodle/course:ignoreavailabilityrestrictions', CAP_ALLOW);
        $expected = array($u1->id, $u2->id);
        $this->assertEquals($expected, array_keys($info->filter_user_list($allusers)));
        list ($sql, $params) = $info->get_user_list_sql(true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);

        // If they have viewhiddensections, they also get past the section
        // restriction.
        role_change_permission($studentroleid, context_course::instance($course->id),
                'moodle/course:ignoreavailabilityrestrictions', CAP_ALLOW);
        $expected = array($u1->id, $u2->id, $u3->id);
        $this->assertEquals($expected, array_keys($info->filter_user_list($allusers)));
        list ($sql, $params) = $info->get_user_list_sql(true);
        $result = $DB->get_fieldset_sql($sql, $params);
        sort($result);
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests the info_module class when involved in a recursive call to $cm->name.
     */
    public function test_info_recursive_name_call() {
        global $DB;

        $this->resetAfterTest();

        // Create a course and page.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page1 = $generator->create_module('page', ['course' => $course->id, 'name' => 'Page1']);

        // Set invalid availability.
        $DB->set_field('course_modules', 'availability', 'not valid', ['id' => $page1->cmid]);

        // Get the cm_info object.
        $this->setAdminUser();
        $modinfo = get_fast_modinfo($course);
        $cm1 = $modinfo->get_cm($page1->cmid);

        // At this point we will generate dynamic data for $cm1, which will cause the debugging
        // call below.
        $this->assertEquals('Page1', $cm1->name);

        $this->assertDebuggingCalled('Error processing availability data for ' .
                '&lsquo;Page1&rsquo;: Invalid availability text');
    }

    /**
     * Test for the is_available_for_all() method of the info base class.
     * @covers \core_availability\info_module::is_available_for_all
     */
    public function test_is_available_for_all() {
        global $CFG, $DB;
        $this->resetAfterTest();
        $CFG->enableavailability = 0;

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->get_plugin_generator('mod_page')->create_instance(['course' => $course]);

        // Set an availability restriction and reset the modinfo cache.
        // The enableavailability setting is disabled so this does not take effect yet.
        $notavailable = '{"op":"|","show":true,"c":[{"type":"mock","a":false}]}';
        $DB->set_field('course_modules', 'availability', $notavailable, ['id' => $page->cmid]);
        get_fast_modinfo($course, 0, true);

        // Availability is disabled, so we expect this module to be available for everyone.
        $modinfo = get_fast_modinfo($course);
        $info = new info_module($modinfo->get_cm($page->cmid));
        $this->assertTrue($info->is_available_for_all());

        // Now, enable availability restrictions, and check again.
        // This time, we expect it to return false, because of the access restriction.
        $CFG->enableavailability = 1;
        $this->assertFalse($info->is_available_for_all());
    }
}
