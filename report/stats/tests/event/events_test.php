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
 * Tests for stats report events.
 *
 * @package    report_stats
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace report_stats\event;

/**
 * Class report_stats_events_testcase
 *
 * Class for tests related to stats report events.
 *
 * @package    report_stats
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
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
     * Test the stats report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of stats report, so here we
     * simply create the event and trigger it.
     */
    public function test_report_viewed(): void {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Trigger event for stats report viewed.
        $event = \report_stats\event\report_viewed::create(array('context' => $context, 'relateduserid' => $user->id,
                'other' => array('report' => 0, 'time' => 0, 'mode' => 1)));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_stats\event\report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user stats report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of user stats report, so here we
     * simply create the event and trigger it.
     */
    public function test_user_report_viewed(): void {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Trigger event for user stats report viewed.
        $event = \report_stats\event\user_report_viewed::create(array('context' => $context, 'relateduserid' => $user->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_stats\event\user_report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}
