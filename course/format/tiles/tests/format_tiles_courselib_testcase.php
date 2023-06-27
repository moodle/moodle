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

// @codingStandardsIgnoreStart - ignore whole file this since it is copied from an old core file.

/**
 * Course related tests for format tiles (copied core courselib_test with format changed to tiles).
 *
 * @package    format_tiles
 * @copyright  2018 David Watson {@link http://evolutioncode.uk} based on core version 2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/tests/fixtures/course_capability_assignment.php');
require_once($CFG->dirroot . '/enrol/imsenterprise/tests/imsenterprise_test.php');

/**
 * Class format_tiles_course_courselib_testcase
 *
 * @copyright 2018 David Watson {@link http://evolutioncode.uk} based on core version 2012 Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_tiles_courselib_testcase extends advanced_testcase {

    /**
     * Test the create_course function
     */
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
        $course->format = 'tiles';
        $course->newsitems = 0;
        $course->category = $defaultcategory;
        $original = (array) $course;

        $created = create_course($course);
        $context = context_course::instance($created->id);

        // Compare original and created.
        $this->assertEquals($original, array_intersect_key((array) $created, $original));

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $created->id, 'section' => 0));
        $this->assertTrue($sectioncreated);

        // Ensure that the shortname isn't duplicated.
        try {
            $created = create_course($course);
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertSame(get_string('shortnametaken', 'error', $course->shortname), $e->getMessage());
        }

        // Ensure that the idnumber isn't duplicated.
        $course->shortname .= '1';
        try {
            $created = create_course($course);
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertSame(get_string('courseidnumbertaken', 'error', $course->idnumber), $e->getMessage());
        }
    }

    /**
     * Test_create_course_with_generator.
     * @throws dml_exception
     */
    public function test_create_course_with_generator() {
        global $DB;
        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));

        // Ensure default section is created.
        $sectioncreated = $DB->record_exists('course_sections', array('course' => $course->id, 'section' => 0));
        $this->assertTrue($sectioncreated);
    }

    /**
     * Test_create_course_sections.
     * @throws moodle_exception
     */
    public function test_create_course_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $numsections = 5;
        $course = $this->getDataGenerator()->create_course(
            array('shortname' => 'GrowingCourse',
                'fullname' => 'Growing Course',
                'numsections' => $numsections,
                'format' => 'tiles'),
            array('createsections' => true));

        // Ensure all 6 (0-5) sections were created and course content cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $numsections), $sectionscreated);

        // this will do nothing, section already exists
        $this->assertFalse(course_create_sections_if_missing($course, $numsections));

        // this will create new section
        $this->assertTrue(course_create_sections_if_missing($course, $numsections + 1));

        // Ensure all 7 (0-6) sections were created and modinfo/sectioninfo cache works properly
        $sectionscreated = array_keys(get_fast_modinfo($course)->get_section_info_all());
        $this->assertEquals(range(0, $numsections + 1), $sectionscreated);
    }

    /**
     * Test_update_course.
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_update_course() {
        global $DB;

        $this->resetAfterTest();

        $defaultcategory = $DB->get_field_select('course_categories', 'MIN(id)', 'parent = 0');

        $course = new stdClass();
        $course->fullname = 'Apu loves Unit Təsts';
        $course->shortname = 'test1';
        $course->idnumber = '1';
        $course->summary = 'Awesome!';
        $course->summaryformat = FORMAT_PLAIN;
        $course->format = 'tiles';
        $course->newsitems = 0;
        $course->numsections = 5;
        $course->category = $defaultcategory;

        $created = create_course($course);
        // Ensure the checks only work on idnumber/shortname that are not already ours.
        update_course($created);

        $course->shortname = 'test2';
        $course->idnumber = '2';

        $created2 = create_course($course);

        // Test duplicate idnumber.
        $created2->idnumber = '1';
        try {
            update_course($created2);
            $this->fail('Expected exception when trying to update a course with duplicate idnumber');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('courseidnumbertaken', 'error', $created2->idnumber), $e->getMessage());
        }

        // Test duplicate shortname.
        $created2->idnumber = '2';
        $created2->shortname = 'test1';
        try {
            update_course($created2);
            $this->fail('Expected exception when trying to update a course with a duplicate shortname');
        } catch (moodle_exception $e) {
            $this->assertEquals(get_string('shortnametaken', 'error', $created2->shortname), $e->getMessage());
        }
    }

    /**
     * Test_update_course_section_time_modified.
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_update_course_section_time_modified() {
        global $DB;

        $this->resetAfterTest();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10, 'format' => 'tiles'), array('createsections' => true));
        $sections = $DB->get_records('course_sections', array('course' => $course->id));

        // Get the last section's time modified value.
        $section = array_pop($sections);
        $oldtimemodified = $section->timemodified;

        // Update the section.
        $this->waitForSecond(); // Ensuring that the section update occurs at a different timestamp.
        course_update_section($course, $section, array());

        // Check that the time has changed.
        $section = $DB->get_record('course_sections', array('id' => $section->id));
        $newtimemodified = $section->timemodified;
        $this->assertGreaterThan($oldtimemodified, $newtimemodified);
    }

    /**
     * Test_course_add_cm_to_section.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_course_add_cm_to_section() {
        global $DB;
        $this->resetAfterTest(true);

        // Create course with 1 section.
        $course = $this->getDataGenerator()->create_course(
            array('shortname' => 'GrowingCourse',
                'fullname' => 'Growing Course',
                'numsections' => 1,
                'format' => 'tiles'),
            array('createsections' => true));

        // Trash modinfo.
        rebuild_course_cache($course->id, true);

        // Create some cms for testing.
        $cmids = array();
        for ($i = 0; $i < 4; $i++) {
            $cmids[$i] = $DB->insert_record('course_modules', array('course' => $course->id));
        }

        // Add it to section that exists.
        course_add_cm_to_section($course, $cmids[0], 1);

        // Check it got added to sequence.
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0], $sequence);

        // Add a second, this time using courseid variant of parameters.
        $coursecacherev = $DB->get_field('course', 'cacherev', array('id' => $course->id));
        course_add_cm_to_section($course->id, $cmids[1], 1);
        $sequence = $DB->get_field('course_sections', 'sequence', array('course' => $course->id, 'section' => 1));
        $this->assertEquals($cmids[0] . ',' . $cmids[1], $sequence);

        // Check that modinfo cache was reset but not rebuilt (important for performance if calling repeatedly).
        $this->assertGreaterThan($coursecacherev, $DB->get_field('course', 'cacherev', array('id' => $course->id)));
        $this->assertEmpty(cache::make('core', 'coursemodinfo')->get($course->id));

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

    /**
     * Test_reorder_sections.
     * @throws dml_exception
     */
    public function test_reorder_sections() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'), array('createsections' => true));
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10), array('createsections' => true));
        $oldsections = array();
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course' => $course->id), 'id') as $section) {
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

    /**
     * Test_move_section_down.
     * @throws dml_exception
     */
    public function test_move_section_down() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'), array('createsections' => true));
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10), array('createsections' => true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course' => $course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section down..
        move_section_to($course, 2, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course' => $course->id)) as $section) {
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

    /**
     * Test_move_section_up.
     * @throws dml_exception
     */
    public function test_move_section_up() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'), array('createsections' => true));
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10), array('createsections' => true));
        $oldsections = array();
        foreach ($DB->get_records('course_sections', array('course' => $course->id)) as $section) {
            $oldsections[$section->section] = $section->id;
        }
        ksort($oldsections);

        // Test move section up..
        move_section_to($course, 6, 4);
        $sections = array();
        foreach ($DB->get_records('course_sections', array('course' => $course->id)) as $section) {
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

    /**
     * Test_move_section_marker.
     * @throws dml_exception
     */
    public function test_move_section_marker() {
        global $DB;
        $this->resetAfterTest(true);

        $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'), array('createsections' => true));
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10, 'format' => 'tiles'), array('createsections' => true));

        // Set course marker to the section we are going to move..
        course_set_marker($course->id, 2);
        // Verify that the course marker is set correctly.
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals(2, $course->marker);

        // Test move the marked section down..
        move_section_to($course, 2, 4);

        // Verify that the course marker has been moved along with the section..
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

    /**
     * Test_course_can_delete_section.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_course_can_delete_section() {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $coursetiles = $generator->create_course(
            array('numsections' => 5, 'format' => 'tiles'),
            array('createsections' => true));

        $assign1 = $generator->create_module('assign', array('course' => $coursetiles, 'section' => 1));
        $assign2 = $generator->create_module('assign', array('course' => $coursetiles, 'section' => 2));

        // Enrol student and teacher.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $student = $generator->create_user();
        $teacher = $generator->create_user();

        $generator->enrol_user($student->id, $coursetiles->id, $roleids['student']);
        $generator->enrol_user($teacher->id, $coursetiles->id, $roleids['editingteacher']);

        // Teacher should be able to delete sections (except for 0) in tiles format.
        $this->setUser($teacher);

        // For tiles format will return false for section 0 and true for any other section.
        $this->assertFalse(course_can_delete_section($coursetiles, 0));
        $this->assertTrue(course_can_delete_section($coursetiles, 1));

        // Now let's revoke a capability from teacher to manage activity in section 1.
        $modulecontext = context_module::instance($assign1->cmid);
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $roleids['editingteacher'],
            $modulecontext);
        $modulecontext->mark_dirty();
        $this->assertFalse(course_can_delete_section($coursetiles, 1));
        $this->assertTrue(course_can_delete_section($coursetiles, 2));

        // Student does not have permissions to delete sections.
        $this->setUser($student);
        $this->assertFalse(course_can_delete_section($coursetiles, 1));
    }

    /**
     * Test_course_delete_section.
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_course_delete_section() {
        global $DB;
        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        $course = $generator->create_course(array('numsections' => 6, 'format' => 'tiles'),
            array('createsections' => true));
        $assign0 = $generator->create_module('assign', array('course' => $course, 'section' => 0));
        $assign1 = $generator->create_module('assign', array('course' => $course, 'section' => 1));
        $assign21 = $generator->create_module('assign', array('course' => $course, 'section' => 2));
        $assign22 = $generator->create_module('assign', array('course' => $course, 'section' => 2));
        $assign3 = $generator->create_module('assign', array('course' => $course, 'section' => 3));
        $assign5 = $generator->create_module('assign', array('course' => $course, 'section' => 5));
        $assign6 = $generator->create_module('assign', array('course' => $course, 'section' => 6));

        $this->setAdminUser();

        // Attempt to delete non-existing section.
        $this->assertFalse(course_delete_section($course, 10, false));
        $this->assertFalse(course_delete_section($course, 9, true));

        // Attempt to delete 0-section.
        $this->assertFalse(course_delete_section($course, 0, true));
        $this->assertTrue($DB->record_exists('course_modules', array('id' => $assign0->cmid)));

        // Delete last section.
        $this->assertTrue(course_delete_section($course, 6, true));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign6->cmid)));
        $this->assertEquals(5, course_get_format($course)->get_last_section_number());

        // Delete empty section.
        $this->assertTrue(course_delete_section($course, 4, false));
        $this->assertEquals(4, course_get_format($course)->get_last_section_number());

        // Delete section in the middle (2).
        $this->assertFalse(course_delete_section($course, 2, false));
        $this->assertTrue(course_delete_section($course, 2, true));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign21->cmid)));
        $this->assertFalse($DB->record_exists('course_modules', array('id' => $assign22->cmid)));
        $this->assertEquals(3, course_get_format($course)->get_last_section_number());
        $this->assertEquals(array(0 => array($assign0->cmid),
            1 => array($assign1->cmid),
            2 => array($assign3->cmid),
            3 => array($assign5->cmid)), get_fast_modinfo($course)->sections);

        // Remove marked section.
        course_set_marker($course->id, 1);
        $this->assertTrue(course_get_format($course)->is_section_current(1));
        $this->assertTrue(course_delete_section($course, 1, true));
        $this->assertFalse(course_get_format($course)->is_section_current(1));
    }

    /**
     * Test_move_module_in_course.
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_move_module_in_course() {
        global $DB;

        $this->resetAfterTest(true);
        // Setup fixture
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'), array('createsections' => true));
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id));

        $cms = get_fast_modinfo($course)->get_cms();
        $cm = reset($cms);

        $newsection = get_fast_modinfo($course)->get_section_info(3);
        $oldsectionid = $cm->section;

        // Perform the move
        moveto_module($cm, $newsection);

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
        moveto_module($cm, $newsection);

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

    /**
     * Test_section_visibility_events.
     */
    public function test_section_visibility_events() {
        $this->setAdminUser();
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course(array('numsections' => 1, 'format' => 'tiles'), array('createsections' => true));
        $sectionnumber = 1;
        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => $sectionnumber));
        $assign = $this->getDataGenerator()->create_module('assign', array('duedate' => time(),
            'course' => $course->id), array('section' => $sectionnumber));
        $sink = $this->redirectEvents();
        set_section_visible($course->id, $sectionnumber, 0);
        $events = $sink->get_events();

        // Extract the number of events related to what we are testing, other events
        // such as course_section_updated could have been triggered.
        $count = 0;
        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_updated) {
                $count++;
            }
        }
        $this->assertSame(2, $count);
        $sink->close();
    }

    /**
     * Test that triggering a course_updated event works as expected.
     */
    public function test_course_updated_event() {
        global $DB;

        $this->resetAfterTest();

        // Create a course.
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));

        // Create a category we are going to move this course to.
        $category = $this->getDataGenerator()->create_category();

        // Create a hidden category we are going to move this course to.
        $categoryhidden = $this->getDataGenerator()->create_category(array('visible' => 0));

        // Update course and catch course_updated event.
        $sink = $this->redirectEvents();
        update_course($course);
        $events = $sink->get_events();
        $sink->close();

        // Get updated course information from the DB.
        $updatedcourse = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($updatedcourse->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $url = new moodle_url('/course/edit.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($updatedcourse, $event->get_record_snapshot('course', $event->objectid));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($updatedcourse, $event);
        $expectedlog = array($updatedcourse->id, 'course', 'update', 'edit.php?id=' . $course->id, $course->id);
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Move course and catch course_updated event.
        $sink = $this->redirectEvents();
        move_courses(array($course->id), $category->id);
        $events = $sink->get_events();
        $sink->close();

        // Return the moved course information from the DB.
        $movedcourse = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($movedcourse->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($movedcourse, $event->get_record_snapshot('course', $movedcourse->id));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($movedcourse, $event);
        $expectedlog = array($movedcourse->id, 'course', 'move', 'edit.php?id=' . $movedcourse->id, $movedcourse->id);
        $this->assertEventLegacyLogData($expectedlog, $event);

        // Move course to hidden category and catch course_updated event.
        $sink = $this->redirectEvents();
        move_courses(array($course->id), $categoryhidden->id);
        $events = $sink->get_events();
        $sink->close();

        // Return the moved course information from the DB.
        $movedcoursehidden = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);
        // Validate event.
        $event = array_shift($events);
        $this->assertInstanceOf('\core\event\course_updated', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($movedcoursehidden->id, $event->objectid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($movedcoursehidden, $event->get_record_snapshot('course', $movedcoursehidden->id));
        $this->assertEquals('course_updated', $event->get_legacy_eventname());
        $this->assertEventLegacyData($movedcoursehidden, $event);
        $expectedlog = array($movedcoursehidden->id, 'course', 'move', 'edit.php?id=' . $movedcoursehidden->id, $movedcoursehidden->id);
        $this->assertEventLegacyLogData($expectedlog, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_content_deleted event works as expected.
     */
    public function test_course_content_deleted_event() {
        global $DB;

        $this->resetAfterTest();

        // Create the course.
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));

        // Get the course from the DB. The data generator adds some extra properties, such as
        // numsections, to the course object which will fail the assertions later on.
        $course = $DB->get_record('course', array('id' => $course->id), '*', MUST_EXIST);

        // Save the course context before we delete the course.
        $coursecontext = context_course::instance($course->id);

        // Catch the update event.
        $sink = $this->redirectEvents();

        remove_course_contents($course->id, false);

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_content_deleted', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($course->id, $event->objectid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($course, $event->get_record_snapshot('course', $course->id));
        $this->assertEquals('course_content_removed', $event->get_legacy_eventname());
        // The legacy data also passed the context and options in the course object.
        $course->context = $coursecontext;
        $course->options = array();
        $this->assertEventLegacyData($course, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_backup_created event works as expected.
     */
    public function test_course_backup_created_event() {
        global $CFG;

        // Get the necessary files to perform backup and restore.
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();

        // Set to admin user.
        $this->setAdminUser();

        // The user id is going to be 2 since we are the admin user.
        $userid = 2;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));

        // Create backup file and save it to the backup location.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userid);
        $sink = $this->redirectEvents();
        $bc->execute_plan();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_backup_created', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($bc->get_courseid(), $event->objectid);
        $this->assertEquals(context_course::instance($bc->get_courseid())->id, $event->contextid);

        $url = new moodle_url('/course/view.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);

        // Destroy the resource controller since we are done using it.
        $bc->destroy();
    }

    /**
     * Test that triggering a course_restored event works as expected.
     */
    public function test_course_restored_event() {
        global $CFG;

        // Get the necessary files to perform backup and restore.
        require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
        require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

        $this->resetAfterTest();

        // Set to admin user.
        $this->setAdminUser();

        // The user id is going to be 2 since we are the admin user.
        $userid = 2;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));

        // Create backup file and save it to the backup location.
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userid);
        $bc->execute_plan();
        $results = $bc->get_results();
        $file = $results['backup_destination'];
        $fp = get_file_packer('application/vnd.moodle.backup');
        $filepath = $CFG->dataroot . '/temp/backup/test-restore-course-event';
        $file->extract_to_pathname($fp, $filepath);
        $bc->destroy();

        // Now we want to catch the restore course event.
        $sink = $this->redirectEvents();

        // Now restore the course to trigger the event.
        $rc = new restore_controller('test-restore-course-event', $course->id, backup::INTERACTIVE_NO,
            backup::MODE_GENERAL, $userid, backup::TARGET_NEW_COURSE);
        $rc->execute_precheck();
        $rc->execute_plan();

        // Capture the event.
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = array_pop($events);
        $this->assertInstanceOf('\core\event\course_restored', $event);
        $this->assertEquals('course', $event->objecttable);
        $this->assertEquals($rc->get_courseid(), $event->objectid);
        $this->assertEquals(context_course::instance($rc->get_courseid())->id, $event->contextid);
        $this->assertEquals('course_restored', $event->get_legacy_eventname());
        $legacydata = (object) array(
            'courseid' => $rc->get_courseid(),
            'userid' => $rc->get_userid(),
            'type' => $rc->get_type(),
            'target' => $rc->get_target(),
            'mode' => $rc->get_mode(),
            'operation' => $rc->get_operation(),
            'samesite' => $rc->is_samesite()
        );
        $url = new moodle_url('/course/view.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventLegacyData($legacydata, $event);
        $this->assertEventContextNotUsed($event);

        // Destroy the resource controller since we are done using it.
        $rc->destroy();
    }

    /**
     * Test that triggering a course_section_updated event works as expected.
     */
    public function test_course_section_updated_event() {
        global $DB;

        $this->resetAfterTest();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10, 'format' => 'tiles'), array('createsections' => true));
        $sections = $DB->get_records('course_sections', array('course' => $course->id));

        $coursecontext = context_course::instance($course->id);

        $section = array_pop($sections);
        $section->name = 'Test section';
        $section->summary = 'Test section summary';
        $DB->update_record('course_sections', $section);

        // Trigger an event for course section update.
        $event = \core\event\course_section_updated::create(
            array(
                'objectid' => $section->id,
                'courseid' => $course->id,
                'context' => context_course::instance($course->id),
                'other' => array(
                    'sectionnum' => $section->section
                )
            )
        );
        $event->add_record_snapshot('course_sections', $section);
        // Trigger and catch event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();

        // Validate the event.
        $event = $events[0];
        $this->assertInstanceOf('\core\event\course_section_updated', $event);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($section->section, $event->other['sectionnum']);
        $expecteddesc = "The user with id '{$event->userid}' updated section number '{$event->other['sectionnum']}' for the course with id '{$event->courseid}'";
        $this->assertEquals($expecteddesc, $event->get_description());
        $url = new moodle_url('/course/editsection.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $event->objectid));
        $id = $section->id;
        $sectionnum = $section->section;
        $expectedlegacydata = array($course->id, "course", "editsection", 'editsection.php?id=' . $id, $sectionnum);
        $this->assertEventLegacyLogData($expectedlegacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that triggering a course_section_deleted event works as expected.
     */
    public function test_course_section_deleted_event() {
        global $USER, $DB;
        $this->resetAfterTest();
        $sink = $this->redirectEvents();

        // Create the course with sections.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 10, 'format' => 'tiles'), array('createsections' => true));
        $sections = $DB->get_records('course_sections', array('course' => $course->id), 'section');
        $coursecontext = context_course::instance($course->id);
        $section = array_pop($sections);
        course_delete_section($course, $section);
        $events = $sink->get_events();
        $event = array_pop($events); // Delete section event.
        $sink->close();

        // Validate event data.
        $this->assertInstanceOf('\core\event\course_section_deleted', $event);
        $this->assertEquals('course_sections', $event->objecttable);
        $this->assertEquals($section->id, $event->objectid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($coursecontext->id, $event->contextid);
        $this->assertEquals($section->section, $event->other['sectionnum']);
        $expecteddesc = "The user with id '{$event->userid}' deleted section number '{$event->other['sectionnum']}' " .
            "(section name '{$event->other['sectionname']}') for the course with id '{$event->courseid}'";
        $this->assertEquals($expecteddesc, $event->get_description());
        $this->assertEquals($section, $event->get_record_snapshot('course_sections', $event->objectid));
        $this->assertNull($event->get_url());

        // Test legacy data.
        $sectionnum = $section->section;
        $expectedlegacydata = array($course->id, "course", "delete section", 'view.php?id=' . $course->id, $sectionnum);
        $this->assertEventLegacyLogData($expectedlegacydata, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test_course_integrity_check.
     * @throws dml_exception
     */
    public function test_course_integrity_check() {
        global $DB;

        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course(array('numsections' => 1, 'format' => 'tiles'),
            array('createsections' => true));

        $forum = $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('section' => 0));
        $page = $this->getDataGenerator()->create_module('page', array('course' => $course->id),
            array('section' => 0));
        $quiz = $this->getDataGenerator()->create_module('quiz', array('course' => $course->id),
            array('section' => 0));
        $correctseq = join(',', array($forum->cmid, $page->cmid, $quiz->cmid));

        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($correctseq, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);
        $this->assertEmpty(course_integrity_check($course->id));

        // Now let's make manual change in DB and let course_integrity_check() fix it:

        // 1. Module appears twice in one section.
        $DB->update_record('course_sections', array('id' => $section0->id, 'sequence' => $section0->sequence. ','. $page->cmid));
        $this->assertEquals(
            array('Failed integrity check for course ['. $course->id.
                ']. Sequence for course section ['. $section0->id. '] is "'.
                $section0->sequence. ','. $page->cmid. '", must be "'.
                $section0->sequence. '"'),
            course_integrity_check($course->id));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($correctseq, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 2. Module appears in two sections (last section wins).
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''. $page->cmid));
        // First message about double mentioning in sequence, second message about wrong section field for $page.
        $this->assertEquals(array(
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] must be removed from sequence of section ['. $section0->id.
            '] because it is also present in sequence of section ['. $section1->id. ']',
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] points to section ['. $section0->id. '] instead of ['. $section1->id. ']'),
            course_integrity_check($course->id));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEquals(''. $page->cmid, $section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 3. Module id is not present in course_section.sequence (integrity check with $fullcheck = false).
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''));
        $this->assertEmpty(course_integrity_check($course->id)); // Not an error!
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section); // Not changed.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 4. Module id is not present in course_section.sequence (integrity check with $fullcheck = true).
        $this->assertEquals(array('Failed integrity check for course ['. $course->id. ']. Course module ['.
            $page->cmid. '] is missing from sequence of section ['. $section1->id. ']'),
            course_integrity_check($course->id, null, null, true)); // Error!
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEquals(''. $page->cmid, $section1->sequence);  // Yay, module added to section.
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section1->id, $cms[$page->cmid]->section); // Not changed.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 5. Module id is not present in course_section.sequence and it's section is invalid (integrity check with $fullcheck = true).
        $DB->update_record('course_modules', array('id' => $page->cmid, 'section' => 8765));
        $DB->update_record('course_sections', array('id' => $section1->id, 'sequence' => ''));
        $this->assertEquals(array(
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] is missing from sequence of section ['. $section0->id. ']',
            'Failed integrity check for course ['. $course->id. ']. Course module ['. $page->cmid.
            '] points to section [8765] instead of ['. $section0->id. ']'),
            course_integrity_check($course->id, null, null, true));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid. ','. $page->cmid, $section0->sequence); // Module added to section.
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$page->cmid]->section); // Section changed to section0.
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);

        // 6. Module is deleted from course_modules but not deleted in sequence (integrity check with $fullcheck = true).
        $DB->delete_records('course_modules', array('id' => $page->cmid));
        $this->assertEquals(array('Failed integrity check for course ['. $course->id. ']. Course module ['.
            $page->cmid. '] does not exist but is present in the sequence of section ['. $section0->id. ']'),
            course_integrity_check($course->id, null, null, true));
        $section0 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 0));
        $section1 = $DB->get_record('course_sections', array('course' => $course->id, 'section' => 1));
        $cms = $DB->get_records('course_modules', array('course' => $course->id), 'id', 'id,section');
        $this->assertEquals($forum->cmid. ','. $quiz->cmid, $section0->sequence);
        $this->assertEmpty($section1->sequence);
        $this->assertEquals($section0->id, $cms[$forum->cmid]->section);
        $this->assertEquals($section0->id, $cms[$quiz->cmid]->section);
        $this->assertEquals(2, count($cms));
    }

    /**
     * Tests for event related to course module creation.
     */
    public function test_course_module_created_event() {
        global $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create an assign module.
        $sink = $this->redirectEvents();
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $events = $sink->get_events();
        $eventscount = 0;

        // Validate event data.
        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_created) {
                $eventscount++;

                $this->assertEquals($module->cmid, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals('course_modules', $event->objecttable);
                $url = new moodle_url('/mod/assign/view.php', array('id' => $module->cmid));
                $this->assertEquals($url, $event->get_url());

                // Test legacy data.
                $this->assertSame('mod_created', $event->get_legacy_eventname());
                $eventdata = new stdClass();
                $eventdata->modulename = 'assign';
                $eventdata->name       = $module->name;
                $eventdata->cmid       = $module->cmid;
                $eventdata->courseid   = $module->course;
                $eventdata->userid     = $USER->id;
                $this->assertEventLegacyData($eventdata, $event);

                $arr = array(
                    array($module->course, "course", "add mod", "../mod/assign/view.php?id=$module->cmid", "assign $module->id"),
                    array($module->course, "assign", "add", "view.php?id=$module->cmid", $module->id, $module->cmid)
                );
                $this->assertEventLegacyLogData($arr, $event);
                $this->assertEventContextNotUsed($event);
            }
        }
        // Only one \core\event\course_module_created event should be triggered.
        $this->assertEquals(1, $eventscount);

        // Let us see if duplicating an activity results in a nice course module created event.
        $sink->clear();
        $course = get_course($module->course);
        $cm = get_coursemodule_from_id('assign', $module->cmid, 0, false, MUST_EXIST);
        $newcm = duplicate_module($course, $cm);
        $events = $sink->get_events();
        $eventscount = 0;
        $sink->close();

        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_created) {
                $eventscount++;
                // Validate event data.
                $this->assertInstanceOf('\core\event\course_module_created', $event);
                $this->assertEquals($newcm->id, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals($course->id, $event->courseid);
                $url = new moodle_url('/mod/assign/view.php', array('id' => $newcm->id));
                $this->assertEquals($url, $event->get_url());
            }
        }

        // Only one \core\event\course_module_created event should be triggered.
        $this->assertEquals(1, $eventscount);
    }

    /**
     * Tests for create_from_cm method.
     */
    public function test_course_module_create_from_cm() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and modules.
        $course = $this->getDataGenerator()->create_course(array('numsections' => 5, 'format' => 'tiles'));

        // Generate an assignment.
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Get the module context.
        $modcontext = context_module::instance($assign->cmid);

        // Get course module.
        $cm = get_coursemodule_from_id(null, $assign->cmid, $course->id, false, MUST_EXIST);

        // Create an event from course module.
        $event = \core\event\course_module_updated::create_from_cm($cm, $modcontext);

        // Trigger the events.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event2 = array_pop($events);

        // Test event data.
        $this->assertInstanceOf('\core\event\course_module_updated', $event);
        $this->assertEquals($cm->id, $event2->objectid);
        $this->assertEquals($modcontext, $event2->get_context());
        $this->assertEquals($cm->modname, $event2->other['modulename']);
        $this->assertEquals($cm->instance, $event2->other['instanceid']);
        $this->assertEquals($cm->name, $event2->other['name']);
        $this->assertEventContextNotUsed($event2);
        $this->assertSame('mod_updated', $event2->get_legacy_eventname());
        $arr = array(
            array($cm->course, "course", "update mod", "../mod/assign/view.php?id=$cm->id", "assign $cm->instance"),
            array($cm->course, "assign", "update", "view.php?id=$cm->id", $cm->instance, $cm->id)
        );
        $this->assertEventLegacyLogData($arr, $event);
    }

    /**
     * Tests changing the visibility of a course.
     */
    public function test_course_change_visibility() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course = $generator->create_course(array('category' => $category->id));

        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);

        $this->assertTrue(course_change_visibility($course->id, false));
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals('0', $course->visible);
        $this->assertEquals('0', $course->visibleold);

        $this->assertTrue(course_change_visibility($course->id, true));
        $course = $DB->get_record('course', array('id' => $course->id));
        $this->assertEquals('1', $course->visible);
        $this->assertEquals('1', $course->visibleold);
    }

    /**
     * Tests moving the course up and down by one.
     */
    public function test_course_change_sortorder_by_one() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $category = $generator->create_category();
        $course3 = $generator->create_course(array('category' => $category->id));
        $course2 = $generator->create_course(array('category' => $category->id));
        $course1 = $generator->create_course(array('category' => $category->id));

        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving down.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, false));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course2->id, $course1->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving up.
        $course1 = get_course($course1->id);
        $this->assertTrue(course_change_sortorder_by_one($course1, true));
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the top course up one.
        $course1 = get_course($course1->id);
        $this->assertFalse(course_change_sortorder_by_one($course1, true));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));

        // Test moving the bottom course up down.
        $course3 = get_course($course3->id);
        $this->assertFalse(course_change_sortorder_by_one($course3, false));
        // Check nothing changed.
        $courses = $category->get_courses();
        $this->assertIsArray($courses);
        $this->assertEquals(array($course1->id, $course2->id, $course3->id), array_keys($courses));
        $dbcourses = $DB->get_records('course', array('category' => $category->id), 'sortorder', 'id');
        $this->assertEquals(array_keys($dbcourses), array_keys($courses));
    }

    /**
     * Test duplicate_module()
     */
    public function test_duplicate_module() {
        $this->setAdminUser();
        $this->resetAfterTest();
        $course = self::getDataGenerator()->create_course(array('format' => 'tiles'));
        $res = self::getDataGenerator()->create_module('resource', array('course' => $course));
        $cm = get_coursemodule_from_id('resource', $res->cmid, 0, false, MUST_EXIST);

        $newcm = duplicate_module($course, $cm);

        // Make sure they are the same, except obvious id changes.
        foreach ($cm as $prop => $value) {
            if ($prop == 'id' || $prop == 'url' || $prop == 'instance' || $prop == 'added') {
                // Ignore obviously different properties.
                continue;
            }
            if ($prop == 'name') {
                // We expect ' (copy)' to be added to the original name since MDL-59227.
                $value = get_string('duplicatedmodule', 'moodle', $value);
            }
            $this->assertEquals($value, $newcm->$prop);
        }
    }

    /**
     * Tests that when creating or updating a module, if the availability settings
     * are present but set to an empty tree, availability is set to null in
     * database.
     */
    public function test_empty_availability_settings() {
        global $DB;
        $this->setAdminUser();
        $this->resetAfterTest();

        // Enable availability.
        set_config('enableavailability', 1);

        // Test add.
        $emptyavailability = json_encode(\core_availability\tree::get_root_json(array()));
        $course = self::getDataGenerator()->create_course(array('format' => 'tiles'));
        $label = self::getDataGenerator()->create_module('label', array(
            'course' => $course, 'availability' => $emptyavailability));
        $this->assertNull($DB->get_field('course_modules', 'availability',
            array('id' => $label->cmid)));

        // Test update.
        $formdata = $DB->get_record('course_modules', array('id' => $label->cmid));
        unset($formdata->availability);
        $formdata->availabilityconditionsjson = $emptyavailability;
        $formdata->modulename = 'label';
        $formdata->coursemodule = $label->cmid;
        $draftid = 0;
        file_prepare_draft_area($draftid, context_module::instance($label->cmid)->id,
            'mod_label', 'intro', 0);
        $formdata->introeditor = array(
            'itemid' => $draftid,
            'text' => '<p>Yo</p>',
            'format' => FORMAT_HTML);
        update_module($formdata);
        $this->assertNull($DB->get_field('course_modules', 'availability',
            array('id' => $label->cmid)));
    }

    /**
     * Test update_inplace_editable()
     */
    public function test_update_module_name_inplace() {
        global $CFG, $DB, $PAGE;
        require_once($CFG->dirroot . '/lib/external/externallib.php');

        $this->setUser($this->getDataGenerator()->create_user());

        $this->resetAfterTest(true);
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $forum = self::getDataGenerator()->create_module('forum', array('course' => $course->id, 'name' => 'forum name'));

        // Call service for core_course component without necessary permissions.
        try {
            core_external::update_inplace_editable('core_course', 'activityname', $forum->cmid, 'New forum name');
            $this->fail('Exception expected');
        } catch (moodle_exception $e) {
            $this->assertEquals('Course or activity not accessible. (Not enrolled)',
                $e->getMessage());
        }

        // Change to admin user and make sure that cm name can be updated using web service update_inplace_editable().
        $this->setAdminUser();
        $res = core_external::update_inplace_editable('core_course', 'activityname', $forum->cmid, 'New forum name');
        $res = external_api::clean_returnvalue(core_external::update_inplace_editable_returns(), $res);
        $this->assertEquals('New forum name', $res['value']);
        $this->assertEquals('New forum name', $DB->get_field('forum', 'name', array('id' => $forum->id)));
    }

    /**
     * Test course_get_user_navigation_options for managers in a normal course.
     */
    public function test_course_get_user_navigation_options_for_managers() {
        global $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $context = context_course::instance($course->id);
        $this->setAdminUser();

        $navoptions = course_get_user_navigation_options($context);
        $this->assertTrue($navoptions->blogs);
        $this->assertTrue($navoptions->notes);
        $this->assertTrue($navoptions->participants);
        $this->assertTrue($navoptions->badges);
    }

    /**
     * Test course_get_user_navigation_options for students in a normal course.
     */
    public function test_course_get_user_navigation_options_for_students() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $context = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);

        $navoptions = course_get_user_navigation_options($context);
        $this->assertTrue($navoptions->blogs);
        $this->assertFalse($navoptions->notes);
        $this->assertTrue($navoptions->participants);
        $this->assertTrue($navoptions->badges);

        // Disable some options.
        $CFG->badges_allowcoursebadges = 0;
        $CFG->enableblogs = 0;
        // Disable view participants capability.
        assign_capability('moodle/course:viewparticipants', CAP_PROHIBIT, $roleid, $context);

        $navoptions = course_get_user_navigation_options($context);
        $this->assertFalse($navoptions->blogs);
        $this->assertFalse($navoptions->notes);
        $this->assertFalse($navoptions->participants);
        $this->assertFalse($navoptions->badges);
    }

    /**
     * Test course_get_user_administration_options for managers in a normal course.
     */
    public function test_course_get_user_administration_options_for_managers() {
        global $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $context = context_course::instance($course->id);
        $this->setAdminUser();

        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertTrue($adminoptions->update);
        $this->assertTrue($adminoptions->filters);
        $this->assertTrue($adminoptions->reports);
        $this->assertTrue($adminoptions->backup);
        $this->assertTrue($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertTrue($adminoptions->tags);
        $this->assertTrue($adminoptions->gradebook);
        $this->assertFalse($adminoptions->outcomes);
        $this->assertTrue($adminoptions->badges);
        $this->assertTrue($adminoptions->import);
        $this->assertTrue($adminoptions->reset);
        $this->assertTrue($adminoptions->roles);
    }

    /**
     * Test course_get_user_administration_options for students in a normal course.
     */
    public function test_course_get_user_administration_options_for_students() {
        global $DB, $CFG;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(array('format' => 'tiles'));
        $context = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        $this->setUser($user);
        $adminoptions = course_get_user_administration_options($course, $context);

        $this->assertFalse($adminoptions->update);
        $this->assertFalse($adminoptions->filters);
        $this->assertFalse($adminoptions->reports);
        $this->assertFalse($adminoptions->backup);
        $this->assertFalse($adminoptions->restore);
        $this->assertFalse($adminoptions->files);
        $this->assertFalse($adminoptions->tags);
        $this->assertFalse($adminoptions->gradebook);
        $this->assertFalse($adminoptions->outcomes);
        $this->assertTrue($adminoptions->badges);
        $this->assertFalse($adminoptions->import);
        $this->assertFalse($adminoptions->reset);
        $this->assertFalse($adminoptions->roles);

        $CFG->enablebadges = false;
        $adminoptions = course_get_user_administration_options($course, $context);
        $this->assertFalse($adminoptions->badges);
    }

    /**
     * Test_course_enddate.
     *
     * @dataProvider course_enddate_provider
     * @param int $startdate
     * @param int $enddate
     * @param string $errorcode
     */
    public function test_course_enddate($startdate, $enddate, $errorcode) {

        $this->resetAfterTest(true);

        $record = array('startdate' => $startdate, 'enddate' => $enddate);
        try {
            $course1 = $this->getDataGenerator()->create_course($record);
            if ($errorcode !== false) {
                $this->fail('Expected exception with "' . $errorcode . '" error code in create_create');
            }
        } catch (moodle_exception $e) {
            if ($errorcode === false) {
                $this->fail('Got "' . $errorcode . '" exception error code and no exception was expected');
            }
            if ($e->errorcode != $errorcode) {
                $this->fail('Got "' . $e->errorcode. '" exception error code and "' . $errorcode . '" was expected');
            }
            return;
        }

        $this->assertEquals($startdate, $course1->startdate);
        $this->assertEquals($enddate, $course1->enddate);
    }

    /**
     * Provider for test_course_enddate.
     *
     * @return array
     */
    public function course_enddate_provider() {
        // Each provided example contains startdate, enddate and the expected exception error code if there is any.
        return [
            [
                111,
                222,
                false
            ], [
                222,
                111,
                'enddatebeforestartdate'
            ], [
                111,
                0,
                false
            ], [
                0,
                222,
                'nostartdatenoenddate'
            ]
        ];
    }


    /**
     * Test_course_dates_reset.
     *
     * @dataProvider course_dates_reset_provider
     * @param int $startdate
     * @param int $enddate
     * @param int $resetstartdate
     * @param int $resetenddate
     * @param int $resultingstartdate
     * @param int $resultingenddate
     */
    public function test_course_dates_reset($startdate, $enddate, $resetstartdate, $resetenddate, $resultingstartdate, $resultingenddate) {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_date.php');

        $this->resetAfterTest(true);

        $this->setAdminUser();

        $CFG->enablecompletion = true;

        $this->setTimezone('UTC');

        $record = array('startdate' => $startdate, 'enddate' => $enddate, 'enablecompletion' => 1);
        $originalcourse = $this->getDataGenerator()->create_course($record);
        $coursecriteria = new completion_criteria_date(array('course' => $originalcourse->id, 'timeend' => $startdate + DAYSECS));
        $coursecriteria->insert();

        $activitycompletiondate = $startdate + DAYSECS;
        $data = $this->getDataGenerator()->create_module('data', array('course' => $originalcourse->id),
            array('completion' => 1, 'completionexpected' => $activitycompletiondate));

        $resetdata = new stdClass();
        $resetdata->id = $originalcourse->id;
        $resetdata->reset_start_date_old = $originalcourse->startdate;
        $resetdata->reset_start_date = $resetstartdate;
        $resetdata->reset_end_date = $resetenddate;
        $resetdata->reset_end_date_old = $record['enddate'];
        reset_course_userdata($resetdata);

        $course = $DB->get_record('course', array('id' => $originalcourse->id));

        $this->assertEquals($resultingstartdate, $course->startdate);
        $this->assertEquals($resultingenddate, $course->enddate);

        $coursecompletioncriteria = completion_criteria_date::fetch(array('course' => $originalcourse->id));
        $this->assertEquals($resultingstartdate + DAYSECS, $coursecompletioncriteria->timeend);

        $this->assertEquals($resultingstartdate + DAYSECS, $DB->get_field('course_modules', 'completionexpected',
            array('id' => $data->cmid)));
    }

    /**
     * Provider for test_course_dates_reset.
     *
     * @return array
     */
    public function course_dates_reset_provider() {

        // Each example contains the following:
        // - course startdate
        // - course enddate
        // - startdate to reset to (false if not reset)
        // - enddate to reset to (false if not reset)
        // - resulting startdate
        // - resulting enddate
        $time = 1445644800;
        return [
            // No date changes.
            [
                $time,
                $time + DAYSECS,
                false,
                false,
                $time,
                $time + DAYSECS
            ],
            // End date changes to a valid value.
            [
                $time,
                $time + DAYSECS,
                false,
                $time + DAYSECS + 111,
                $time,
                $time + DAYSECS + 111
            ],
            // Start date changes to a valid value. End date does not get updated because it does not have value.
            [
                $time,
                0,
                $time + DAYSECS,
                false,
                $time + DAYSECS,
                0
            ],
            // Start date changes to a valid value. End date gets updated accordingly.
            [
                $time,
                $time + DAYSECS,
                $time + WEEKSECS,
                false,
                $time + WEEKSECS,
                $time + WEEKSECS + DAYSECS
            ],
            // Start date and end date change to a valid value.
            [
                $time,
                $time + DAYSECS,
                $time + WEEKSECS,
                $time + YEARSECS,
                $time + WEEKSECS,
                $time + YEARSECS
            ]
        ];
    }

    /**
     * Test reset_course_userdata()
     *    - with reset_roles_overrides enabled
     *    - with selective role unenrolments
     */
    public function test_course_roles_reset() {
        global $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();

        // Create test course and user, enrol one in the other.
        $course = $generator->create_course(array('format' => 'tiles'));
        $user = $generator->create_user();
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $generator->enrol_user($user->id, $course->id, $roleid);

        // Test case with reset_roles_overrides enabled.
        // Override course so it does NOT allow students 'mod/forum:viewdiscussion'.
        $coursecontext = context_course::instance($course->id);
        assign_capability('mod/forum:viewdiscussion', CAP_PREVENT, $roleid, $coursecontext->id);

        // Check expected capabilities so far.
        $this->assertFalse(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));

        // Oops, preventing student from viewing forums was a mistake, let's reset the course.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->reset_roles_overrides = true;
        reset_course_userdata($resetdata);

        // Check new expected capabilities - override at the course level should be reset.
        $this->assertTrue(has_capability('mod/forum:viewdiscussion', $coursecontext, $user));

        // Test case with selective role unenrolments.
        $roles = array();
        $roles['student'] = $DB->get_field('role', 'id', array('shortname' => 'student'), MUST_EXIST);
        $roles['teacher'] = $DB->get_field('role', 'id', array('shortname' => 'teacher'), MUST_EXIST);

        // We enrol a user with student and teacher roles.
        $generator->enrol_user($user->id, $course->id, $roles['student']);
        $generator->enrol_user($user->id, $course->id, $roles['teacher']);

        // When we reset only student role, we expect to keep teacher role.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->unenrol_users = array($roles['student']);
        reset_course_userdata($resetdata);

        $usersroles = enrol_get_course_users_roles($course->id);
        $this->assertArrayHasKey($user->id, $usersroles);
        $this->assertArrayHasKey($roles['teacher'], $usersroles[$user->id]);
        $this->assertArrayNotHasKey($roles['student'], $usersroles[$user->id]);
        $this->assertCount(1, $usersroles[$user->id]);

        // We reenrol user as student.
        $generator->enrol_user($user->id, $course->id, $roles['student']);

        // When we reset student and teacher roles, we expect no roles left.
        $resetdata = new stdClass();
        $resetdata->id = $course->id;
        $resetdata->unenrol_users = array($roles['student'], $roles['teacher']);
        reset_course_userdata($resetdata);

        $usersroles = enrol_get_course_users_roles($course->id);
        $this->assertEmpty($usersroles);
    }

    /**
     * Test_course_check_module_updates_since.
     * @throws coding_exception
     * @throws comment_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function test_course_check_module_updates_since() {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/mod/glossary/lib.php');
        require_once($CFG->dirroot . '/rating/lib.php');
        require_once($CFG->dirroot . '/comment/lib.php');

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $glossary = $this->getDataGenerator()->create_module('glossary', array(
            'course' => $course->id,
            'completion' => COMPLETION_TRACKING_AUTOMATIC,
            'completionview' => 1,
            'allowcomments' => 1,
            'assessed' => RATING_AGGREGATE_AVERAGE,
            'scale' => 100
        ));
        $glossarygenerator = $this->getDataGenerator()->get_plugin_generator('mod_glossary');
        $context = context_module::instance($glossary->cmid);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($glossary->cmid);
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $from = time();

        $teacher = $this->getDataGenerator()->create_user();
        $teacherrole = $DB->get_record('role', array('shortname' => 'teacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        assign_capability('mod/glossary:viewanyrating', CAP_ALLOW, $studentrole->id, $context->id, true);

        // Check nothing changed right now.
        $updates = course_check_module_updates_since($cm, $from);
        $this->assertFalse($updates->configuration->updated);
        $this->assertFalse($updates->completion->updated);
        $this->assertFalse($updates->gradeitems->updated);
        $this->assertFalse($updates->comments->updated);
        $this->assertFalse($updates->ratings->updated);
        $this->assertFalse($updates->introfiles->updated);
        $this->assertFalse($updates->outcomes->updated);

        $this->waitForSecond();

        // Do some changes.
        $this->setUser($user);
        $entry = $glossarygenerator->create_content($glossary);

        $this->setUser($teacher);
        // Name.
        set_coursemodule_name($glossary->cmid, 'New name');

        // Add some ratings.
        $rm = new rating_manager();
        $result = $rm->add_rating($cm, $context, 'mod_glossary', 'entry', $entry->id, 100, 50, $user->id, RATING_AGGREGATE_AVERAGE);

        // Change grades.
        $glossary->cmidnumber = $glossary->cmid;
        glossary_update_grades($glossary, $user->id);

        $this->setUser($user);
        // Completion status.
        glossary_view($glossary, $course, $cm, $context, 'letter');

        // Add one comment.
        $args = new stdClass;
        $args->context   = $context;
        $args->course    = $course;
        $args->cm        = $cm;
        $args->area      = 'glossary_entry';
        $args->itemid    = $entry->id;
        $args->client_id = 1;
        $args->component = 'mod_glossary';
        $manager = new comment($args);
        $manager->add('blah blah blah');

        // Check upgrade status.
        $updates = course_check_module_updates_since($cm, $from);
        $this->assertTrue($updates->configuration->updated);
        $this->assertTrue($updates->completion->updated);
        $this->assertTrue($updates->gradeitems->updated);
        $this->assertTrue($updates->comments->updated);
        $this->assertTrue($updates->ratings->updated);
        $this->assertFalse($updates->introfiles->updated);
        $this->assertFalse($updates->outcomes->updated);
    }

    /**
     * Test cases for the course_classify_courses_for_timeline test.
     */
    public function get_course_classify_courses_for_timeline_test_cases() {
        $now = time();
        $day = 86400;

        return [
            'no courses' => [
                'coursesdata' => [],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'only past' => [
                'coursesdata' => [
                    [
                        'shortname' => 'past1',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'past2',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => ['past1', 'past2'],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'only in progress' => [
                'coursesdata' => [
                    [
                        'shortname' => 'inprogress1',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'inprogress2',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => [],
                    COURSE_TIMELINE_INPROGRESS => ['inprogress1', 'inprogress2']
                ]
            ],
            'only future' => [
                'coursesdata' => [
                    [
                        'shortname' => 'future1',
                        'startdate' => $now + $day
                    ],
                    [
                        'shortname' => 'future2',
                        'startdate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => [],
                    COURSE_TIMELINE_FUTURE => ['future1', 'future2'],
                    COURSE_TIMELINE_INPROGRESS => []
                ]
            ],
            'combination' => [
                'coursesdata' => [
                    [
                        'shortname' => 'past1',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'past2',
                        'startdate' => $now - ($day * 2),
                        'enddate' => $now - $day
                    ],
                    [
                        'shortname' => 'inprogress1',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'inprogress2',
                        'startdate' => $now - $day,
                        'enddate' => $now + $day
                    ],
                    [
                        'shortname' => 'future1',
                        'startdate' => $now + $day
                    ],
                    [
                        'shortname' => 'future2',
                        'startdate' => $now + $day
                    ]
                ],
                'expected' => [
                    COURSE_TIMELINE_PAST => ['past1', 'past2'],
                    COURSE_TIMELINE_FUTURE => ['future1', 'future2'],
                    COURSE_TIMELINE_INPROGRESS => ['inprogress1', 'inprogress2']
                ]
            ],
        ];
    }

    /**
     * Test the course_classify_courses_for_timeline function.
     *
     * @dataProvider get_course_classify_courses_for_timeline_test_cases()
     * @param array $coursesdata Courses to create
     * @param array $expected Expected test results.
     */
    public function test_course_classify_courses_for_timeline($coursesdata, $expected) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursesdata);

        sort($expected[COURSE_TIMELINE_PAST]);
        sort($expected[COURSE_TIMELINE_FUTURE]);
        sort($expected[COURSE_TIMELINE_INPROGRESS]);

        $results = course_classify_courses_for_timeline($courses);

        $actualpast = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_PAST]);

        $actualfuture = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_FUTURE]);

        $actualinprogress = array_map(function($result) {
            return $result->shortname;
        }, $results[COURSE_TIMELINE_INPROGRESS]);

        sort($actualpast);
        sort($actualfuture);
        sort($actualinprogress);

        $this->assertEquals($expected[COURSE_TIMELINE_PAST], $actualpast);
        $this->assertEquals($expected[COURSE_TIMELINE_FUTURE], $actualfuture);
        $this->assertEquals($expected[COURSE_TIMELINE_INPROGRESS], $actualinprogress);
    }

    /**
     * Test cases for the course_filter_courses_by_timeline_classification tests.
     */
    public function get_course_filter_courses_by_timeline_classification_test_cases() {
        $now = time();
        $day = 86400;

        $coursedata = [
            [
                'shortname' => 'apast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'bpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'cpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'dpast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'epast',
                'startdate' => $now - ($day * 2),
                'enddate' => $now - $day
            ],
            [
                'shortname' => 'ainprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'binprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'cinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'dinprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'einprogress',
                'startdate' => $now - $day,
                'enddate' => $now + $day
            ],
            [
                'shortname' => 'afuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'bfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'cfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'dfuture',
                'startdate' => $now + $day
            ],
            [
                'shortname' => 'efuture',
                'startdate' => $now + $day
            ]
        ];

        // Raw enrolled courses result set should be returned in this order:
        // afuture, ainprogress, apast, bfuture, binprogress, bpast, cfuture, cinprogress, cpast,
        // dfuture, dinprogress, dpast, efuture, einprogress, epast
        //
        // By classification the offset values for each record should be:
        // COURSE_TIMELINE_FUTURE
        // 0 (afuture), 3 (bfuture), 6 (cfuture), 9 (dfuture), 12 (efuture)
        // COURSE_TIMELINE_INPROGRESS
        // 1 (ainprogress), 4 (binprogress), 7 (cinprogress), 10 (dinprogress), 13 (einprogress)
        // COURSE_TIMELINE_PAST
        // 2 (apast), 5 (bpast), 8 (cpast), 11 (dpast), 14 (epast).
        return [
            'empty set' => [
                'coursedata' => [],
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => [],
                'expectedprocessedcount' => 0
            ],
            // COURSE_TIMELINE_FUTURE.
            'future not limit no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 0,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15
            ],
            'future no offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture'],
                'expectedprocessedcount' => 4
            ],
            'future offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 2,
                'offset' => 2,
                'expectedcourses' => ['bfuture', 'cfuture'],
                'expectedprocessedcount' => 5
            ],
            'future exact limit' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 5,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 13
            ],
            'future limit less results' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['afuture', 'bfuture', 'cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 15
            ],
            'future limit less results with offset' => [
                'coursedata' => $coursedata,
                'classification' => COURSE_TIMELINE_FUTURE,
                'limit' => 10,
                'offset' => 5,
                'expectedcourses' => ['cfuture', 'dfuture', 'efuture'],
                'expectedprocessedcount' => 10
            ],
        ];
    }

    /**
     * Test the course_filter_courses_by_timeline_classification function.
     *
     * @dataProvider get_course_filter_courses_by_timeline_classification_test_cases()
     * @param array $coursedata Course test data to create.
     * @param string $classification Timeline classification.
     * @param int $limit Maximum number of results to return.
     * @param int $offset Results to skip at the start of the result set.
     * @param string[] $expectedcourses Expected courses in results.
     * @param int $expectedprocessedcount Expected number of course records to be processed.
     */
    public function test_course_filter_courses_by_timeline_classification(
        $coursedata,
        $classification,
        $limit,
        $offset,
        $expectedcourses,
        $expectedprocessedcount
    ) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursedata);

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $coursesgenerator = course_get_enrolled_courses_for_logged_in_user(0, $offset, 'shortname ASC', 'shortname');
        list($result, $processedcount) = course_filter_courses_by_timeline_classification(
            $coursesgenerator,
            $classification,
            $limit
        );

        $actual = array_map(function($course) {
            return $course->shortname;
        }, $result);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectedprocessedcount, $processedcount);
    }

    /**
     * Test cases for the course_filter_courses_by_timeline_classification tests.
     */
    public function get_course_filter_courses_by_customfield_test_cases() {
        global $CFG;
        require_once($CFG->dirroot.'/blocks/myoverview/lib.php');
        $coursedata = [
            [
                'shortname' => 'C1',
                'customfield_checkboxfield' => 1,
                'customfield_datefield' => strtotime('2001-02-01T12:00:00Z'),
                'customfield_selectfield' => 1,
                'customfield_textfield' => 'fish',
            ],
            [
                'shortname' => 'C2',
                'customfield_checkboxfield' => 0,
                'customfield_datefield' => strtotime('1980-08-05T13:00:00Z'),
            ],
            [
                'shortname' => 'C3',
                'customfield_checkboxfield' => 0,
                'customfield_datefield' => strtotime('2001-02-01T12:00:00Z'),
                'customfield_selectfield' => 2,
                'customfield_textfield' => 'dog',
            ],
            [
                'shortname' => 'C4',
                'customfield_checkboxfield' => 1,
                'customfield_selectfield' => 3,
                'customfield_textfield' => 'cat',
            ],
            [
                'shortname' => 'C5',
                'customfield_datefield' => strtotime('1980-08-06T13:00:00Z'),
                'customfield_selectfield' => 2,
                'customfield_textfield' => 'fish',
            ],
        ];

        return [
            'empty set' => [
                'coursedata' => [],
                'customfield' => 'checkboxfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => [],
                'expectedprocessedcount' => 0
            ],
            'checkbox yes' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C4'],
                'expectedprocessedcount' => 5
            ],
            'checkbox no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2', 'C3', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'date 1 Feb 2001' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => strtotime('2001-02-01T12:00:00Z'),
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C3'],
                'expectedprocessedcount' => 5
            ],
            'date 6 Aug 1980' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => strtotime('1980-08-06T13:00:00Z'),
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C5'],
                'expectedprocessedcount' => 5
            ],
            'date no date' => [
                'coursedata' => $coursedata,
                'customfield' => 'datefield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C4'],
                'expectedprocessedcount' => 5
            ],
            'select Option 1' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => 1,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1'],
                'expectedprocessedcount' => 5
            ],
            'select Option 2' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => 2,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C3', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'select no select' => [
                'coursedata' => $coursedata,
                'customfield' => 'selectfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2'],
                'expectedprocessedcount' => 5
            ],
            'text fish' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => 'fish',
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C1', 'C5'],
                'expectedprocessedcount' => 5
            ],
            'text dog' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => 'dog',
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C3'],
                'expectedprocessedcount' => 5
            ],
            'text no text' => [
                'coursedata' => $coursedata,
                'customfield' => 'textfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 10,
                'offset' => 0,
                'expectedcourses' => ['C2'],
                'expectedprocessedcount' => 5
            ],
            'checkbox limit no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 2,
                'offset' => 0,
                'expectedcourses' => ['C2', 'C3'],
                'expectedprocessedcount' => 3
            ],
            'checkbox limit offset no' => [
                'coursedata' => $coursedata,
                'customfield' => 'checkboxfield',
                'customfieldvalue' => BLOCK_MYOVERVIEW_CUSTOMFIELD_EMPTY,
                'limit' => 2,
                'offset' => 3,
                'expectedcourses' => ['C5'],
                'expectedprocessedcount' => 2
            ],
        ];
    }

    /**
     * Test the course_filter_courses_by_customfield function.
     *
     * @dataProvider get_course_filter_courses_by_customfield_test_cases()
     * @param array $coursedata Course test data to create.
     * @param string $customfield Shortname of the customfield.
     * @param string $customfieldvalue the value to filter by.
     * @param int $limit Maximum number of results to return.
     * @param int $offset Results to skip at the start of the result set.
     * @param string[] $expectedcourses Expected courses in results.
     * @param int $expectedprocessedcount Expected number of course records to be processed.
     */
    public function test_course_filter_courses_by_customfield(
        $coursedata,
        $customfield,
        $customfieldvalue,
        $limit,
        $offset,
        $expectedcourses,
        $expectedprocessedcount
    ) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create the custom fields.
        $generator->create_custom_field_category([
            'name' => 'Course fields',
            'component' => 'core_course',
            'area' => 'course',
            'itemid' => 0,
        ]);
        $generator->create_custom_field([
            'name' => 'Checkbox field',
            'category' => 'Course fields',
            'type' => 'checkbox',
            'shortname' => 'checkboxfield',
        ]);
        $generator->create_custom_field([
            'name' => 'Date field',
            'category' => 'Course fields',
            'type' => 'date',
            'shortname' => 'datefield',
            'configdata' => '{"mindate":0, "maxdate":0}',
        ]);
        $generator->create_custom_field([
            'name' => 'Select field',
            'category' => 'Course fields',
            'type' => 'select',
            'shortname' => 'selectfield',
            'configdata' => '{"options":"Option 1\nOption 2\nOption 3\nOption 4"}',
        ]);
        $generator->create_custom_field([
            'name' => 'Text field',
            'category' => 'Course fields',
            'type' => 'text',
            'shortname' => 'textfield',
        ]);

        $courses = array_map(function($coursedata) use ($generator) {
            return $generator->create_course($coursedata);
        }, $coursedata);

        $student = $generator->create_user();

        foreach ($courses as $course) {
            $generator->enrol_user($student->id, $course->id, 'student');
        }

        $this->setUser($student);

        $coursesgenerator = course_get_enrolled_courses_for_logged_in_user(0, $offset, 'shortname ASC', 'shortname');
        list($result, $processedcount) = course_filter_courses_by_customfield(
            $coursesgenerator,
            $customfield,
            $customfieldvalue,
            $limit
        );

        $actual = array_map(function($course) {
            return $course->shortname;
        }, $result);

        $this->assertEquals($expectedcourses, $actual);
        $this->assertEquals($expectedprocessedcount, $processedcount);
    }
}
// @codingStandardsIgnoreEnd