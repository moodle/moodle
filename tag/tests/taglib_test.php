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
 * Tag related unit tests.
 *
 * @package core_tag
 * @category test
 * @copyright 2014 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/tag/lib.php');

class core_tag_taglib_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test the tag_set function.
     */
    public function test_tag_set() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set('course', $course->id, array('A random tag'), 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Now call the tag_set function without specifying the component or contextid and
        // ensure the function debugging is called.
        tag_set('course', $course->id, array('Another tag'));
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_set_add function.
     */
    public function test_tag_set_add() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Remove the tag we just created.
        $tag = $DB->get_record('tag', array('rawname' => 'A random tag'));
        tag_delete($tag->id);

        // Now call the tag_set_add function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_set_add('course', $course->id, 'Another tag');
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_set_delete function.
     */
    public function test_tag_set_delete() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag and tag instance we are going to delete.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Call the tag_set_delete function.
        tag_set_delete('course', $course->id, 'a random tag', 'core', context_course::instance($course->id)->id);

        // Now check that there are no tags or tag instances.
        $this->assertEquals(0, $DB->count_records('tag'));
        $this->assertEquals(0, $DB->count_records('tag_instance'));

        // Recreate the tag and tag instance.
        tag_set_add('course', $course->id, 'A random tag', 'core', context_course::instance($course->id)->id);

        // Now call the tag_set_delete function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_set_delete('course', $course->id, 'A random tag');
        $this->assertDebuggingCalled();
    }

    /**
     * Test the tag_assign function.
     */
    public function test_tag_assign() {
        global $DB;

        // Create a course to tag.
        $course = $this->getDataGenerator()->create_course();

        // Create the tag.
        $tag = $this->getDataGenerator()->create_tag();

        // Tag the course with the tag we created.
        tag_assign('course', $course->id, $tag->id, 0, 0, 'core', context_course::instance($course->id)->id);

        // Get the tag instance that should have been created.
        $taginstance = $DB->get_record('tag_instance', array('itemtype' => 'course', 'itemid' => $course->id), '*', MUST_EXIST);
        $this->assertEquals('core', $taginstance->component);
        $this->assertEquals(context_course::instance($course->id)->id, $taginstance->contextid);

        // Now call the tag_assign function without specifying the component or
        // contextid and ensure the function debugging is called.
        tag_assign('course', $course->id, $tag->id, 0, 0);
        $this->assertDebuggingCalled();
    }
}
