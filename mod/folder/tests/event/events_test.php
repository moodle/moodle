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
 * @package    mod_folder
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_folder\event;

final class events_test extends \advanced_testcase {

    /**
     * Tests set up.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test the folder updated event.
     *
     * There is no external API for updating a folder, so the unit test will simply create
     * and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_folder_updated(): void {
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));

        $params = array(
            'context' => \context_module::instance($folder->cmid),
            'objectid' => $folder->id,
            'courseid' => $course->id
        );
        $event = \mod_folder\event\folder_updated::create($params);
        $event->add_record_snapshot('folder', $folder);

        // Trigger and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_folder\event\folder_updated', $event);
        $this->assertEquals(\context_module::instance($folder->cmid), $event->get_context());
        $this->assertEquals($folder->id, $event->objectid);
    }

    /**
     * Test the folder updated event.
     *
     * There is no external API for updating a folder, so the unit test will simply create
     * and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_all_files_downloaded(): void {
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));
        $context = \context_module::instance($folder->cmid);
        $cm = get_coursemodule_from_id('folder', $folder->cmid, $course->id, true, MUST_EXIST);

        $sink = $this->redirectEvents();
        folder_downloaded($folder, $course, $cm, $context);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_folder\event\all_files_downloaded', $event);
        $this->assertEquals(\context_module::instance($folder->cmid), $event->get_context());
        $this->assertEquals($folder->id, $event->objectid);
        $expected = array($course->id, 'folder', 'edit', 'edit.php?id=' . $folder->cmid, $folder->id, $folder->cmid);
        $this->assertEventContextNotUsed($event);
    }
}
