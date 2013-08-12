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

    public function test_course_add_cm_to_section() {
        global $DB;
        $this->resetAfterTest(true);

        // Create course with 1 section.
        $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'GrowingCourse',
                    'fullname' => 'Growing Course',
                    'numsections' => 1),
                array('createsections' => true));

        // Trash modinfo.
        rebuild_course_cache($course->id, true);

        // Create some cms for testing.
        $cmids = array();
        for ($i=0; $i<4; $i++) {
            $cmids[$i] = $DB->insert_record('course_modules', array('course' => $course->id));
        }

        // Add it to section that exists.
        course_add_cm_to_section($course, $cmids[0], 1);

        // Check it got added to sequence.
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0], $sequence);

        // Add a second, this time using courseid variant of parameters.
        course_add_cm_to_section($course->id, $cmids[1], 1);
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0] . ',' . $cmids[1], $sequence);

        // Check modinfo was not rebuilt (important for performance if calling
        // repeatedly).
        $this->assertNull($DB->get_field('course', 'modinfo', array('id' => $course->id)));

        // Add one to section that doesn't exist (this might rebuild modinfo).
        course_add_cm_to_section($course, $cmids[2], 2);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[2], $sequence);

        // Add using the 'before' option.
        course_add_cm_to_section($course, $cmids[3], 2, $cmids[2]);
        $this->assertEquals(3, $DB->count_records('course_sections', array('course' => $course->id)));
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 2));
        $this->assertEquals($cmids[3] . ',' . $cmids[2], $sequence);
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
        global $DB;

        $this->resetAfterTest(true);
        // Setup fixture
        $course = $this->getDataGenerator()->create_course(array('numsections'=>5), array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course'=>$course->id));

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        $newsection = get_fast_modinfo($course)->get_section_info(3);
        $oldsectionid = $cm->section;

        // Perform the move
        moveto_module($cm, $newsection);

        // reset of get_fast_modinfo is usually called the code calling moveto_module so call it here
        get_fast_modinfo(0, 0, true);
        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        // Check that the cached modinfo contains the correct section info
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue(empty($modinfo->sections[0]));
        $this->assertFalse(empty($modinfo->sections[3]));

        // Check that the old section's sequence no longer contains this ID
        $oldsection = $DB->get_record('course_sections', array('id' => $oldsectionid));
        $oldsequences = explode(',', $newsection->sequence);
        $this->assertFalse(in_array($cm->id, $oldsequences));

        // Check that the new section's sequence now contains this ID
        $newsection = $DB->get_record('course_sections', array('id' => $newsection->id));
        $newsequences = explode(',', $newsection->sequence);
        $this->assertTrue(in_array($cm->id, $newsequences));

        // Check that the section number has been changed in the cm
        $this->assertEquals($newsection->id, $cm->section);


        // Perform a second move as some issues were only seen on the second move
        $newsection = get_fast_modinfo($course)->get_section_info(2);
        $oldsectionid = $cm->section;
        $result = moveto_module($cm, $newsection);
        $this->assertTrue($result);

        // reset of get_fast_modinfo is usually called the code calling moveto_module so call it here
        get_fast_modinfo(0, 0, true);
        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        // Check that the cached modinfo contains the correct section info
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue(empty($modinfo->sections[0]));
        $this->assertFalse(empty($modinfo->sections[2]));

        // Check that the old section's sequence no longer contains this ID
        $oldsection = $DB->get_record('course_sections', array('id' => $oldsectionid));
        $oldsequences = explode(',', $newsection->sequence);
        $this->assertFalse(in_array($cm->id, $oldsequences));

        // Check that the new section's sequence now contains this ID
        $newsection = $DB->get_record('course_sections', array('id' => $newsection->id));
        $newsequences = explode(',', $newsection->sequence);
        $this->assertTrue(in_array($cm->id, $newsequences));
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

    public function test_course_page_type_list() {
        global $DB;
        $this->resetAfterTest(true);

        // Create a category.
        $category = new stdClass();
        $category->name = 'Test Category';

        $testcategory = $this->getDataGenerator()->create_category($category);

        // Create a course.
        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'Spread the lŭve';
        $course->idnumber = '123';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'topics';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $testcategory->id;

        $testcourse = $this->getDataGenerator()->create_course($course);

        // Create contexts.
        $coursecontext = context_course::instance($testcourse->id);
        $parentcontext = $coursecontext->get_parent_context(); // Not actually used.
        $pagetype = 'page-course-x'; // Not used either.
        $pagetypelist = course_page_type_list($pagetype, $parentcontext, $coursecontext);

        // Page type lists for normal courses.
        $testpagetypelist1 = array();
        $testpagetypelist1['*'] = 'Any page';
        $testpagetypelist1['course-*'] = 'Any course page';
        $testpagetypelist1['course-view-*'] = 'Any type of course main page';

        $this->assertEquals($testpagetypelist1, $pagetypelist);

        // Get the context for the front page course.
        $sitecoursecontext = context_course::instance(SITEID);
        $pagetypelist = course_page_type_list($pagetype, $parentcontext, $sitecoursecontext);

        // Page type list for the front page course.
        $testpagetypelist2 = array('*' => 'Any page');
        $this->assertEquals($testpagetypelist2, $pagetypelist);

        // Make sure that providing no current context to the function doesn't result in an error.
        // Calls made from generate_page_type_patterns() may provide null values.
        $pagetypelist = course_page_type_list($pagetype, null, null);
        $this->assertEquals($pagetypelist, $testpagetypelist1);
    }

    /**
     * Tests moving a module between hidden/visible sections and
     * verifies that the course/module visiblity seettings are
     * retained.
     */
    public function test_moveto_module_between_hidden_sections() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 4), array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $quiz= $this->getDataGenerator()->create_module('quiz', array('course' => $course->id));

        // Set the page as hidden
        set_coursemodule_visible($page->cmid, 0);

        // Set sections 3 as hidden.
        set_section_visible($course->id, 3, 0);

        $modinfo = get_fast_modinfo($course);

        $hiddensection = $modinfo->get_section_info(3);
        // New section is definitely not visible:
        $this->assertEquals($hiddensection->visible, 0);

        $forumcm = $modinfo->cms[$forum->cmid];
        $pagecm = $modinfo->cms[$page->cmid];

        // Move the forum and the page to a hidden section.
        moveto_module($forumcm, $hiddensection);
        moveto_module($pagecm, $hiddensection);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

        $modinfo = get_fast_modinfo($course);

        // Verify that forum and page have been moved to the hidden section and quiz has not.
        $this->assertContains($forum->cmid, $modinfo->sections[3]);
        $this->assertContains($page->cmid, $modinfo->sections[3]);
        $this->assertNotContains($quiz->cmid, $modinfo->sections[3]);

        // Verify that forum has been made invisible.
        $forumcm = $modinfo->cms[$forum->cmid];
        $this->assertEquals($forumcm->visible, 0);
        // Verify that old state has been retained.
        $this->assertEquals($forumcm->visibleold, 1);

        // Verify that page has stayed invisible.
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
        // Verify that old state has been retained.
        $this->assertEquals($pagecm->visibleold, 0);

        // Verify that quiz has been unaffected.
        $quizcm = $modinfo->cms[$quiz->cmid];
        $this->assertEquals($quizcm->visible, 1);

        // Move forum and page back to visible section.
        $visiblesection = $modinfo->get_section_info(2);
        moveto_module($forumcm, $visiblesection);
        moveto_module($pagecm, $visiblesection);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);
        $modinfo = get_fast_modinfo($course);

        // Verify that forum has been made visible.
        $forumcm = $modinfo->cms[$forum->cmid];
        $this->assertEquals($forumcm->visible, 1);

        // Verify that page has stayed invisible.
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);

        // Move the page in the same section (this is what mod duplicate does_
        moveto_module($pagecm, $visiblesection, $forumcm);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

        // Verify that the the page is still hidden
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }

    /**
     * Tests moving a module around in the same section. moveto_module()
     * is called this way in modduplicate.
     */
    public function test_moveto_module_in_same_section() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 3), array('createsections' => true));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        // Simulate inconsistent visible/visibleold values (MDL-38713).
        $cm = $DB->get_record('course_modules', array('id' => $page->cmid), '*', MUST_EXIST);
        $cm->visible = 0;
        $cm->visibleold = 1;
        $DB->update_record('course_modules', $cm);

        $modinfo = get_fast_modinfo($course);
        $forumcm = $modinfo->cms[$forum->cmid];
        $pagecm = $modinfo->cms[$page->cmid];

        // Verify that page is hidden.
        $this->assertEquals($pagecm->visible, 0);

        // Verify section 0 is where all mods added.
        $section = $modinfo->get_section_info(0);
        $this->assertEquals($section->id, $forumcm->section);
        $this->assertEquals($section->id, $pagecm->section);


        // Move the forum and the page to a hidden section.
        moveto_module($pagecm, $section, $forumcm);

        // Reset modinfo cache.
        get_fast_modinfo(0, 0, true);

        // Verify that the the page is still hidden
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }
}
