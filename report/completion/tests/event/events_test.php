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
 * Tests for report completion events.
 *
 * @package    report_completion
 * @copyright  2014 onwards Ankit Agarwal<ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace report_completion\event;

/**
 * Class report_completion_events_testcase
 *
 * Class for tests related to completion report events.
 *
 * @package    report_completion
 * @copyright  2014 onwards Ankit Agarwal<ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
final class events_test extends \advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        parent::setUp();
        $this->setAdminUser();
        $this->resetAfterTest();
    }

    /**
     * Test the report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of log report, so here we
     * simply create the event and trigger it.
     */
    public function test_report_viewed(): void {
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        // Trigger event for completion report viewed.
        $event = \report_completion\event\report_viewed::create(array('context' => $context));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_completion\event\report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/report/completion/index.php', array('course' => $course->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of log report, so here we
     * simply create the event and trigger it.
     */
    public function test_user_report_viewed(): void {
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        // Trigger event for completion report viewed.
        $event = \report_completion\event\user_report_viewed::create(array('context' => $context, 'relateduserid' => 3));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_completion\event\user_report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals(3, $event->relateduserid);
        $this->assertEquals(new \moodle_url('/report/completion/user.php', array('id' => 3, 'course' => $course->id)),
                $event->get_url());
        $this->assertEventContextNotUsed($event);
    }
}
