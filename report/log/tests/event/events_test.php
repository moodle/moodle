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
 * Tests for report log events.
 *
 * @package    report_log
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace report_log\event;

/**
 * Class report_log_events_testcase
 *
 * Class for tests related to log events.
 *
 * @package    report_log
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

        // Trigger event for log report viewed.
        $event = \report_log\event\report_viewed::create(array('context' => $context,
                'relateduserid' => 0, 'other' => array('groupid' => 0, 'date' => 0, 'modid' => 0, 'modaction' => '',
                'logformat' => 'showashtml')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_log\event\report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $expected = array($course->id, "course", "report log", "report/log/index.php?id=$course->id", $course->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/report/log/index.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the user report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of user log report, so here we
     * simply create the event and trigger it.
     */
    public function test_user_report_viewed() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course->id);

        // Trigger event for user report viewed.
        $event = \report_log\event\user_report_viewed::create(array('context' => $context,
                'relateduserid' => $user->id, 'other' => array('mode' => 'today')));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_log\event\user_report_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $url = 'report/log/user.php?id=' . $user->id . '&course=' . $course->id . '&mode=today';
        $expected = array($course->id, "course", "report log", $url, $course->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/report/log/user.php', array('course' => $course->id, 'id' => $user->id, 'mode' => 'today'));
        $this->assertEquals($url, $event->get_url());
    }
}
