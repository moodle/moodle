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


class courselib_testcase extends advanced_testcase {

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
        $result = moveto_module($cm, $newsection);
        $this->assertTrue($result);

        // get_fast_modinfo(reset) is usually called the code calling moveto_module so call it here
        $reset = 'reset';
        get_fast_modinfo($reset);
        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

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

        // get_fast_modinfo(reset) is usually called the code calling moveto_module so call it here
        $reset = 'reset';
        get_fast_modinfo($reset);
        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

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
        $reset = 'reset';
        get_fast_modinfo($reset);

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
        $reset = 'reset';
        get_fast_modinfo($reset);
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
        $reset = 'reset';
        get_fast_modinfo($reset);

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
        $reset = 'reset';
        get_fast_modinfo($reset);

        // Verify that the the page is still hidden
        $modinfo = get_fast_modinfo($course);
        $pagecm = $modinfo->cms[$page->cmid];
        $this->assertEquals($pagecm->visible, 0);
    }
}
