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
 * Events test.
 *
 * @package    mod_resource
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_resource\event;

/**
 * Resource events test cases.
 *
 * @package    mod_resource
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class events_test extends \advanced_testcase {

    /**
     * Setup is called before calling test case.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        // Must be a non-guest user to create resources.
        $this->setAdminUser();
    }

    /**
     * Test course_module_instance_list_viewed event.
     */
    public function test_course_module_instance_list_viewed(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $params = array(
            'context' => \context_course::instance($course->id)
        );
        $event = \mod_resource\event\course_module_instance_list_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_resource\event\course_module_instance_list_viewed', $event);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test course_module_viewed event.
     */
    public function test_course_module_viewed(): void {
        // There is no proper API to call to trigger this event, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $resource = $this->getDataGenerator()->create_module('resource', array('course' => $course->id));

        $params = array(
            'context' => \context_module::instance($resource->cmid),
            'objectid' => $resource->id
        );
        $event = \mod_resource\event\course_module_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_resource\event\course_module_viewed', $event);
        $this->assertEquals(\context_module::instance($resource->cmid), $event->get_context());
        $this->assertEquals($resource->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }
}
