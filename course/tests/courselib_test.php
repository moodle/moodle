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
 * Course related unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/course/lib.php');

class courselib_testcase extends advanced_testcase {

    public function test_create_course() {
        global $DB;
        $this->resetAfterTest(true);
        $defaultcategory = $DB->get_field_select('course_categories', "MIN(id)", "parent=0");

        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'Spread the lŭve';
        $course->idnumber = '123';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'topics';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $defaultcategory;

        $created = create_course($course);
        $context = context_course::instance($created->id);

        // Compare original and created.
        $original = (array) $course;
        $this->assertEquals($original, array_intersect_key((array) $created, $original));

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $created->id, 'section' => 0));
        $this->assertTrue($sectioncreated);

        // Ensure blocks have been associated to the course.
        $blockcount = $DB->count_records('block_instances', array('parentcontextid' => $context->id));
        $this->assertGreaterThan(0, $blockcount);
    }

    public function test_create_course_with_generator() {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course();

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $course->id, 'section' => 0));
        $this->assertTrue($sectioncreated);
    }

    public function test_create_course_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'GrowingCourse',
                    'fullname' => 'Growing Course',
                    'numsections' => 5),
                array('createsections' => true));

        // Ensure all 6 (0-5) sections were created and modinfo/sectioninfo cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $course->numsections), $sectionscreated);

        // this will do nothing, section already exists
        $this->assertFalse(course_create_sections_if_missing($course, $course->numsections));

        // this will create new section
        $this->assertTrue(course_create_sections_if_missing($course, $course->numsections + 1));

        // Ensure all 7 (0-6) sections were created and modinfo/sectioninfo cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $course->numsections + 1), $sectionscreated);
    }

    public function test_reorder_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id), 'id') as $section) {
            $oldsections[$section->section] = $section->id;
            $sections[$section->id] = $section->section;
        }
        ksort($oldsections);

        $neworder = reorder_sections($sections, 2, 4);
        $neworder = array_keys($neworder);
        $this->assertEquals($oldsections[0], $neworder[0]);
        $this->assertEquals($oldsections[1], $neworder[1]);
        $this->assertEquals($oldsections[2], $neworder[4]);
        $this->assertEquals($oldsections[3], $neworder[2]);
        $this->assertEquals($oldsections[4], $neworder[3]);
        $this->assertEquals($oldsections[5], $neworder[5]);
        $this->assertEquals($oldsections[6], $neworder[6]);

        $neworder = reorder_sections($sections, 4, 2);
        $neworder = array_keys($neworder);
        $this->assertEquals($oldsections[0], $neworder[0]);
        $this->assertEquals($oldsections[1], $neworder[1]);
        $this->assertEquals($oldsections[2], $neworder[3]);
        $this->assertEquals($oldsections[3], $neworder[4]);
        $this->assertEquals($oldsections[4], $neworder[2]);
        $this->assertEquals($oldsections[5], $neworder[5]);
        $this->assertEquals($oldsections[6], $neworder[6]);

        $neworder = reorder_sections(1, 2, 4);
        $this->assertFalse($neworder);
    }

    public function test_move_section_down() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section down..
        move_section_to($course, 2, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $sections[$section->section] = $section->id;
        }
        ksort($sections);

        $this->assertEquals($oldsections[0], $sections[0]);
        $this->assertEquals($oldsections[1], $sections[1]);
        $this->assertEquals($oldsections[2], $sections[4]);
        $this->assertEquals($oldsections[3], $sections[2]);
        $this->assertEquals($oldsections[4], $sections[3]);
        $this->assertEquals($oldsections[5], $sections[5]);
        $this->assertEquals($oldsections[6], $sections[6]);
    }

    public function test_move_section_up() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section up..
        move_section_to($course, 6, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course'=>$course->id)) as $section) {
            $sections[$section->section] = $section->id;
        }
        ksort($sections);

        $this->assertEquals($oldsections[0], $sections[0]);
        $this->assertEquals($oldsections[1], $sections[1]);
        $this->assertEquals($oldsections[2], $sections[2]);
        $this->assertEquals($oldsections[3], $sections[3]);
        $this->assertEquals($oldsections[4], $sections[5]);
        $this->assertEquals($oldsections[5], $sections[6]);
        $this->assertEquals($oldsections[6], $sections[4]);
    }

    public function test_move_section_marker() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections'=>true));
        $course = $this->getDataGenerator()->create_course(array('numsections'=>10), array('createsections'=>true));

        // Set course marker to the section we are going to move..
        course_set_marker($course->id, 2);
        // Verify that the course marker is set correctly.
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(2, $course->marker);

        // Test move the marked section down..
        move_section_to($course, 2, 4);

        // Verify that the coruse marker has been moved along with the section..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(4, $course->marker);

        // Test move the marked section up..
        move_section_to($course, 4, 3);

        // Verify that the course marker has been moved along with the section..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(3, $course->marker);

        // Test moving a non-marked section above the marked section..
        move_section_to($course, 4, 2);

        // Verify that the course marker has been moved down to accomodate..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(4, $course->marker);

        // Test moving a non-marked section below the marked section..
        move_section_to($course, 3, 6);

        // Verify that the course marker has been up to accomodate..
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(3, $course->marker);
    }

    public function test_get_course_display_name_for_list() {
        global $CFG;
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('shortname' => 'FROG101', 'fullname' => 'Introduction to pond life'));

        $CFG->courselistshortnames = 0;
        $this->assertEquals('Introduction to pond life', get_course_display_name_for_list($course));

        $CFG->courselistshortnames = 1;
        $this->assertEquals('FROG101 Introduction to pond life', get_course_display_name_for_list($course));
    }

    public function test_create_course_category() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        // Create the category
        $data = new stdClass();
        $data->name = 'aaa';
        $data->description = 'aaa';
        $data->idnumber = '';

        $category1 = create_course_category($data);

        // Initially confirm that base data was inserted correctly
        $this->assertEquals($data->name, $category1->name);
        $this->assertEquals($data->description, $category1->description);
        $this->assertEquals($data->idnumber, $category1->idnumber);

        // sortorder should be blank initially
        $this->assertEmpty($category1->sortorder);

        // Calling fix_course_sortorder() should provide a new sortorder
        fix_course_sortorder();
        $category1 = $DB->get_record('course_categories', array('id' => $category1->id));

        $this->assertGreaterThanOrEqual(1, $category1->sortorder);

        // Create two more categories and test the sortorder worked correctly
        $data->name = 'ccc';
        $category2 = create_course_category($data);
        $this->assertEmpty($category2->sortorder);

        $data->name = 'bbb';
        $category3 = create_course_category($data);
        $this->assertEmpty($category3->sortorder);

        // Calling fix_course_sortorder() should provide a new sortorder to give category1,
        // category2, category3. New course categories are ordered by id not name
        fix_course_sortorder();

        $category1 = $DB->get_record('course_categories', array('id' => $category1->id));
        $category2 = $DB->get_record('course_categories', array('id' => $category2->id));
        $category3 = $DB->get_record('course_categories', array('id' => $category3->id));

        $this->assertGreaterThanOrEqual($category1->sortorder, $category2->sortorder);
        $this->assertGreaterThanOrEqual($category2->sortorder, $category3->sortorder);
        $this->assertGreaterThanOrEqual($category1->sortorder, $category3->sortorder);
    }

    public function test_move_module_in_course() {
        $this->resetAfterTest(true);
        // Setup fixture
        $course = $this->getDataGenerator()->create_course(array('numsections'=>5));
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        course_create_sections_if_missing($course, 3);
        $section3 = get_fast_modinfo($course)->get_section_info(3);

        moveto_module($cm, $section3);

        $modinfo = get_fast_modinfo($course);
        $this->assertTrue(empty($modinfo->sections[0]));
        $this->assertFalse(empty($modinfo->sections[3]));
    }

    public function test_module_visibility() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(), 'course' => $course->id));
        $modules = compact('forum', 'assign');

        // Hiding the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 0);
            $this->check_module_visibility($mod, 0, 0);
        }

        // Showing the modules.
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 1);
            $this->check_module_visibility($mod, 1, 1);
        }
    }

    public function test_section_visibility() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        // Create course.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 3), array('createsections' => true));

        // Testing an empty section.
        $sectionnumber = 1;
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);

        // Testing a section with visible modules.
        $sectionnumber = 2;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
                array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
                'course' => $course->id), array('section' => $sectionnumber));
        $modules = compact('forum', 'assign');
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 1);
        }
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 1, 1);
        }

        // Testing a section with hidden modules, which should stay hidden.
        $sectionnumber = 3;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
                array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
                'course' => $course->id), array('section' => $sectionnumber));
        $modules = compact('forum', 'assign');
        foreach ($modules as $mod) {
            set_coursemodule_visible($mod->cmid, 0);
            $this->check_module_visibility($mod, 0, 0);
        }
        set_section_visible($course->id, $sectionnumber, 0);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 0);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 0);
        }
        set_section_visible($course->id, $sectionnumber, 1);
        $section_info = get_fast_modinfo($course->id)->get_section_info($sectionnumber);
        $this->assertEquals($section_info->visible, 1);
        foreach ($modules as $mod) {
            $this->check_module_visibility($mod, 0, 0);
        }
    }

    /**
     * Helper function to assert that a module has correctly been made visible, or hidden.
     *
     * @param stdClass $mod module information
     * @param int $visibility the current state of the module
     * @param int $visibleold the current state of the visibleold property
     * @return void
     */
    public function check_module_visibility($mod, $visibility, $visibleold) {
        global $DB;
        $cm = get_fast_modinfo($mod->course)->get_cm($mod->cmid);
        $this->assertEquals($visibility, $cm->visible);
        $this->assertEquals($visibleold, $cm->visibleold);

        // Check the module grade items.
        $grade_items = grade_item::fetch_all(array('itemtype' => 'mod', 'itemmodule' => $cm->modname,
                'iteminstance' => $cm->instance, 'courseid' => $cm->course));
        if ($grade_items) {
            foreach ($grade_items as $grade_item) {
                if ($visibility) {
                    $this->assertFalse($grade_item->is_hidden(), "$cm->modname grade_item not visible");
                } else {
                    $this->assertTrue($grade_item->is_hidden(), "$cm->modname grade_item not hidden");
                }
            }
        }

        // Check the events visibility.
        if ($events = $DB->get_records('event', array('instance' => $cm->instance, 'modulename' => $cm->modname))) {
            foreach ($events as $event) {
                $calevent = new calendar_event($event);
                $this->assertEquals($visibility, $calevent->visible, "$cm->modname calendar_event visibility");
            }
        }
    }

}
