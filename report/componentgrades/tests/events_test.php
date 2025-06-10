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
 * Tests for componentgrades report events.
 *
 * @package    report_componentgrades
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Class report_componentgrades_events_testcase
 *
 * Class for tests related to componentgrades report events.
 *
 * @package    report_componentgrades
 * @copyright  2018 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class report_componentgrades_events_testcase extends \advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
    }

    /**
     * Test the componentgrades report viewed event.
     *
     * It's not possible to use the moodle API to simulate the viewing of componentgrades report, so here we
     * simply create the event and trigger it.
     */
    public function test_report_viewed() {
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        $modcontext = \context_module::instance($assign->cmid);
        $event = \report_componentgrades\event\report_viewed::create(array(
                    'context' => $modcontext,
                    'other' => array(
                        'gradingmethod' => 'rubric'
                    )
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\report_componentgrades\event\report_viewed', $event);
        $this->assertEquals($modcontext, $event->get_context());
    }

}
