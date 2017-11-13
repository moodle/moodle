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
 * Unit tests for file browser
 *
 * @package    core_files
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for file browser
 *
 * @package    core_files
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_browser_testcase extends advanced_testcase {

    /** @var stdClass */
    protected $course1;
    /** @var stdClass */
    protected $course2;
    /** @var stdClass */
    protected $module1;
    /** @var stdClass */
    protected $module2;
    /** @var stdClass */
    protected $course1filerecord;
    /** @var stdClass */
    protected $teacher;
    /** @var stdClass */
    protected $teacherrole;

    /**
     * Set up
     */
    public function setUp() {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $this->getDataGenerator()->create_category(); // Empty category.
        $this->course1 = $this->getDataGenerator()->create_course(); // Empty course.

        $this->course2 = $this->getDataGenerator()->create_course();

        // Add a file to course1 summary.
        $coursecontext1 = context_course::instance($this->course1->id);
        $this->course1filerecord = array('contextid' => $coursecontext1->id,
            'component' => 'course',
            'filearea' => 'summary',
            'itemid' => '0',
            'filepath' => '/',
            'filename' => 'summaryfile.jpg');
        $fs = get_file_storage();
        $fs->create_file_from_string($this->course1filerecord, 'IMG');

        $this->module1 = $this->getDataGenerator()->create_module('resource', ['course' => $this->course2->id]); // Contains 1 file.
        $this->module2 = $this->getDataGenerator()->create_module('assign', ['course' => $this->course2->id]); // Contains no files.

        $this->teacher = $this->getDataGenerator()->create_user();
        $this->teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));

        // Make sure we're testing what should be the default capabilities.
        assign_capability('moodle/restore:viewautomatedfilearea', CAP_ALLOW, $this->teacherrole->id, $coursecontext1);

        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course1->id, $this->teacherrole->id);
        $this->getDataGenerator()->enrol_user($this->teacher->id, $this->course2->id, $this->teacherrole->id);

        $this->setUser($this->teacher);
    }

    /**
     * Test "Server files" from the system context
     */
    public function test_file_info_context_system() {

        // There is one non-empty category child and two category children.

        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_system::instance());
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $this->assertEquals(1, count($fileinfo->get_non_empty_children()));
        $categorychildren = array_filter($fileinfo->get_children(), function($a) {
            return $a instanceof file_info_context_coursecat;
        });
        $this->assertEquals(2, count($categorychildren));
    }

    /**
     * Test "Server files" from the system context, hide Misc category
     */
    public function test_file_info_context_system_hidden() {

        // Hide the course category that contains our two courses. Teacher does not have cap to view hidden categories.
        coursecat::get($this->course1->category)->update(['visible' => 0]);

        // We should have two non-empty children in system context (courses).
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_system::instance());
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $this->assertEquals(2, count($fileinfo->get_non_empty_children()));

        // Should be 1 category children (empty category).
        $categorychildren = array_filter($fileinfo->get_children(), function($a) {
            return $a instanceof file_info_context_coursecat;
        });
        $this->assertEquals(1, count($categorychildren));

        // Should be 2 course children - courses that belonged to hidden subcategory are now direct children of "System".
        $coursechildren = array_filter($fileinfo->get_children(), function($a) {
            return $a instanceof file_info_context_course;
        });
        $this->assertEquals(2, count($coursechildren));
    }

    /**
     * Test "Server files" from the course category context
     */
    public function test_file_info_context_coursecat() {

        // There are two non-empty courses.

        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_coursecat::instance($this->course2->category));
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $this->assertEquals(2, count($fileinfo->get_non_empty_children()));
        $coursechildren = array_filter($fileinfo->get_children(), function($a) {
            return $a instanceof file_info_context_course;
        });
        $this->assertEquals(2, count($coursechildren));
    }

    /**
     * Test "Server files" from the course category context, only look for .jpg
     */
    public function test_file_info_context_coursecat_jpg() {

        // There is one non-empty category child and two category children.

        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_system::instance());
        $this->assertNotEmpty($fileinfo->count_non_empty_children(['.jpg']));
        $this->assertEquals(1, count($fileinfo->get_non_empty_children(['.jpg'])));
    }

    /**
     * Test "Server files" from the course context (course1)
     */
    public function test_file_info_context_course_1() {

        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_course::instance($this->course1->id));
        // Fileinfo element has only one non-empty child - "Course summary" file area.
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $nonemptychildren = $fileinfo->get_non_empty_children();
        $this->assertEquals(1, count($nonemptychildren));
        $child = reset($nonemptychildren);
        $this->assertTrue($child instanceof file_info_stored);
        $this->assertEquals(['filename' => '.'] + $this->course1filerecord, $child->get_params());
        // Filearea "Course summary" has a child that is the actual image file.
        $this->assertEquals($this->course1filerecord, $child->get_children()[0]->get_params());

        // There are seven course-level file areas available to teachers with default caps and no modules in this course.
        $allchildren = $fileinfo->get_children();
        $this->assertEquals(7, count($allchildren));
        $modulechildren = array_filter($allchildren, function($a) {
            return $a instanceof file_info_context_module;
        });
        $this->assertEquals(0, count($modulechildren));

        // Admin can see seven course-level file areas.
        $this->setAdminUser();
        $fileinfo = $browser->get_file_info(context_course::instance($this->course1->id));
        $this->assertEquals(7, count($fileinfo->get_children()));
    }

    /**
     * Test "Server files" from the course context (course1)
     */
    public function test_file_info_context_course_2() {

        // 2. Start from the course level.
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info(context_course::instance($this->course2->id));
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $nonemptychildren = $fileinfo->get_non_empty_children();
        $this->assertEquals(1, count($nonemptychildren));
        $child = reset($nonemptychildren);
        $this->assertTrue($child instanceof file_info_context_module);
        $this->assertEquals($this->module1->name.' (File)', $child->get_visible_name());
        $this->assertEquals(1, count($child->get_non_empty_children()));
        $this->assertEquals(1, $child->count_non_empty_children());
        $modulechildren = array_filter($fileinfo->get_children(), function($a) {
            return $a instanceof file_info_context_module;
        });
        $this->assertEquals(2, count($modulechildren));
    }

    /**
     * Test "Server files" from the course context (module1)
     */
    public function test_file_info_context_module_1() {

        $module1context = context_module::instance($this->module1->cmid);
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info($module1context);
        $this->assertEquals($this->module1->name . ' (File)', $fileinfo->get_visible_name());
        $this->assertNotEmpty($fileinfo->count_non_empty_children());
        $nonemptychildren = $fileinfo->get_non_empty_children();
        $this->assertEquals(1, count($nonemptychildren));
        $child = reset($nonemptychildren);
        $this->assertTrue($child instanceof file_info_stored);
    }

    /**
     * Test "Server files" from the course context (module1)
     */
    public function test_file_info_context_module_2() {

        $module2context = context_module::instance($this->module2->cmid);
        $browser = get_file_browser();
        $fileinfo = $browser->get_file_info($module2context);
        $this->assertEquals($this->module2->name.' (Assignment)', $fileinfo->get_visible_name());
        $this->assertEmpty($fileinfo->count_non_empty_children());
        $nonemptychildren = $fileinfo->get_non_empty_children();
        $this->assertEquals(0, count($nonemptychildren));

    }
}
