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
 * Events tests.
 *
 * @package tool_recyclebin
 * @category test
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Events tests class.
 *
 * @package tool_recyclebin
 * @category test
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_recyclebin_events_testcase extends advanced_testcase {

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();

        // We want the category and course bin to be enabled.
        set_config('categorybinenable', 1, 'tool_recyclebin');
        set_config('coursebinenable', 1, 'tool_recyclebin');
    }

    /**
     * Test the category bin item created event.
     */
    public function test_category_bin_item_created() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        delete_course($course, false);
        $events = $sink->get_events();
        $event = reset($events);
        // Need the second event here, the first is backup created.
        $event = next($events);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\category_bin($course->category);
        $items = $rb->get_items();
        $item = reset($items);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\category_bin_item_created', $event);
        $this->assertEquals(context_coursecat::instance($course->category), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the category bin item deleted event.
     */
    public function test_category_bin_item_deleted() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Delete the course.
        delete_course($course, false);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\category_bin($course->category);
        $items = $rb->get_items();
        $item = reset($items);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $rb->delete_item($item);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\category_bin_item_deleted', $event);
        $this->assertEquals(context_coursecat::instance($course->category), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the category bin item restored event.
     */
    public function test_category_bin_item_restored() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Delete the course.
        delete_course($course, false);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\category_bin($course->category);
        $items = $rb->get_items();
        $item = reset($items);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $rb->restore_item($item);
        $events = $sink->get_events();
        $event = $events[count($events) - 2];

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\category_bin_item_restored', $event);
        $this->assertEquals(context_coursecat::instance($course->category), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course bin item created event.
     */
    public function test_course_bin_item_created() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create the assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        course_delete_module($instance->cmid);
        $events = $sink->get_events();
        $event = reset($events);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\course_bin($course->id);
        $items = $rb->get_items();
        $item = reset($items);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\course_bin_item_created', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course bin item deleted event.
     */
    public function test_course_bin_item_deleted() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create the assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));

        // Delete the module.
        course_delete_module($instance->cmid);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\course_bin($course->id);
        $items = $rb->get_items();
        $item = reset($items);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $rb->delete_item($item);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\course_bin_item_deleted', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the course bin item restored event.
     */
    public function test_course_bin_item_restored() {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create the assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $course->id));

        course_delete_module($instance->cmid);

        // Get the item from the recycle bin.
        $rb = new \tool_recyclebin\course_bin($course->id);
        $items = $rb->get_items();
        $item = reset($items);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $rb->restore_item($item);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event contains the expected values.
        $this->assertInstanceOf('\tooL_recyclebin\event\course_bin_item_restored', $event);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($item->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }
}
