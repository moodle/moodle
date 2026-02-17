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

namespace core\event;

use advanced_testcase;
use context_module;
use stdClass;
use moodle_url;

/**
 * Tests for base course module viewed event.
 *
 * @package    core
 * @covers     \core\event\course_restricted_module_viewed
 * @copyright  2026 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_restricted_module_viewed_test extends advanced_testcase {
    /**
     * Test event properties and methods.
     */
    public function test_event_attributes(): void {

        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->course = $course->id;
        $feed = $this->getDataGenerator()->create_module('feedback', $record);
        $cm = get_coursemodule_from_instance('feedback', $feed->id);
        $context = context_module::instance($cm->id);

        // Trigger the page view event.
        $sink = $this->redirectEvents();
        $pageevent = course_restricted_module_viewed::create([
            'context' => $context,
            'courseid' => $course->id,
            'objectid' => $cm->id,
        ]);
        $pageevent->trigger();
        $result = $sink->get_events();
        $event = reset($result);
        $sink->close();

        $this->assertSame('course_modules', $event->objecttable);
        $url = new moodle_url('/mod/feedback/view.php', ['id' => $cm->id]);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }
}
