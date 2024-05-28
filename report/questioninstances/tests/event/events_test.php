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
 * Tests for question instances events.
 *
 * @package    report_questioninstances
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace report_questioninstances\event;

/**
 * Class for question instances events.
 *
 * @package    report_questioninstances
 * @copyright  2014 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class events_test extends \advanced_testcase {

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
     */
    public function test_report_viewed(): void {
        $requestedqtype = 'all';
        $event = \report_questioninstances\event\report_viewed::create(array('other' => array('requestedqtype' => $requestedqtype)));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_questioninstances\event\report_viewed', $event);
        $this->assertEquals(\context_system::instance(), $event->get_context());
        $this->assertEventContextNotUsed($event);
        $url = new \moodle_url('/report/questioninstances/index.php', array('qtype' => $requestedqtype));
        $this->assertEquals($url, $event->get_url());
        $event->get_name();
    }
}
