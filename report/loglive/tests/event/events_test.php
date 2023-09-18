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
 * Tests for report log live events.
 *
 * @package    report_loglive
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace report_loglive\event;

/**
 * Class report_loglive_events_testcase
 *
 * Class for tests related to log live events.
 *
 * @package    report_loglive
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class events_test extends \advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
    }

    /**
     * Test the report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of log report, so here we
     * simply create the event and trigger it.
     */
    public function test_report_viewed() {
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);
        // Trigger event for loglive report viewed.
        $event = \report_loglive\event\report_viewed::create(array('context' => $context));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_loglive\event\report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/report/loglive/index.php', array('id' => $course->id));
        $this->assertEquals($url, $event->get_url());
    }
}
