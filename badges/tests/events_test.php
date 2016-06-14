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
 * Badge events tests.
 *
 * @package    core_badges
 * @copyright  2015 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/badges/tests/badgeslib_test.php');

/**
 * Badge events tests class.
 *
 * @package    core_badges
 * @copyright  2015 onwards Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_badges_events_testcase extends core_badges_badgeslib_testcase {

    /**
     * Test badge awarded event.
     */
    public function test_badge_awarded() {

        $systemcontext = context_system::instance();

        $sink = $this->redirectEvents();

        $badge = new badge($this->badgeid);
        $badge->issue($this->user->id, true);
        $badge->is_issued($this->user->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\core\event\badge_awarded', $event);
        $this->assertEquals($this->badgeid, $event->objectid);
        $this->assertEquals($this->user->id, $event->relateduserid);
        $this->assertEquals($systemcontext, $event->get_context());

        $sink->close();
    }
}