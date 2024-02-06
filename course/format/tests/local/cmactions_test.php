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

namespace core_courseformat\local;

use core_courseformat\hook\after_cm_name_edited;

/**
 * Course module format actions class tests.
 *
 * @package    core_courseformat
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_courseformat\cmactions
 */
final class cmactions_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
    }

    /**
     * Test renaming a course module.
     *
     * @dataProvider provider_test_rename
     * @covers ::rename
     * @param string $newname The new name for the course module.
     * @param bool $expected Whether the course module was renamed.
     * @param bool $expectexception Whether an exception is expected.
     */
    public function test_rename(string $newname, bool $expected, bool $expectexception): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics']);
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $cmactions = new cmactions($course);

        if ($expectexception) {
            $this->expectException(\moodle_exception::class);
        }
        $result = $cmactions->rename($activity->cmid, $newname);
        $this->assertEquals($expected, $result);

        $cminfo = get_fast_modinfo($course)->get_cm($activity->cmid);
        if ($result) {
            $this->assertEquals('New name', $cminfo->name);
        } else {
            $this->assertEquals('Old name', $cminfo->name);
        }
    }

    /**
     * Data provider for test_rename.
     *
     * @return array
     */
    public static function provider_test_rename(): array {
        return [
            'Empty name' => [
                'newname' => '',
                'expected' => false,
                'expectexception' => false,
            ],
            'Maximum length' => [
                'newname' => str_repeat('a', 256),
                'expected' => false,
                'expectexception' => true,
            ],
            'Valid name' => [
                'newname' => 'New name',
                'expected' => true,
                'expectexception' => false,
            ],
        ];
    }

    /**
     * Test rename an activity also rename the calendar events.
     *
     * @covers ::rename
     */
    public function test_rename_calendar_events(): void {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();
        set_config('enablecompletion', 1);

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'name' => 'Old name',
                'course' => $course,
                'completionexpected' => time(),
                'duedate' => time(),
            ]
        );
        $cm = get_coursemodule_from_instance('assign', $activity->id, $course->id);

        // Validate course events naming.
        $this->assertEquals(2, $DB->count_records('event'));

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'due']
        );
        $this->assertEquals(
            get_string('calendardue', 'assign', 'Old name'),
            $event->name
        );

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'expectcompletionon']
        );
        $this->assertEquals(
            get_string('completionexpectedfor', 'completion', (object) ['instancename' => 'Old name']),
            $event->name
        );

        // Rename activity.
        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        // Validate event renaming.
        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'due']
        );
        $this->assertEquals(
            get_string('calendardue', 'assign', 'New name'),
            $event->name
        );

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'expectcompletionon']
        );
        $this->assertEquals(
            get_string('completionexpectedfor', 'completion', (object) ['instancename' => 'New name']),
            $event->name
        );
    }

    /**
     * Test renaming an activity trigger a course update log event.
     *
     * @covers ::rename
     */
    public function test_rename_course_module_updated_event(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $sink = $this->redirectEvents();

        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_module_updated', $event);
        $this->assertEquals(\context_module::instance($activity->cmid), $event->get_context());
    }

    /**
     * Test renaming an activity triggers the after_cm_name_edited hook.
     * @covers ::rename
     */
    public function test_rename_after_cm_name_edited_hook(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $executedhook = null;

        $testcallback = function(after_cm_name_edited $hook) use (&$executedhook): void {
            $executedhook = $hook;
        };
        $this->redirectHook(after_cm_name_edited::class, $testcallback);

        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        $this->assertEquals($activity->cmid, $executedhook->get_cm()->id);
        $this->assertEquals('New name', $executedhook->get_newname());
    }
}
