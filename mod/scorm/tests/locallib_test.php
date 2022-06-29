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
 * File containing the SCORM module local library function tests.
 *
 * @package mod_scorm
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_scorm;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/mod/scorm/lib.php');

/**
 * Class containing the SCORM module local library function tests.
 *
 * @package mod_scorm
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class locallib_test extends \advanced_testcase {

    public function setUp(): void {
        $this->resetAfterTest();
    }

    public function test_scorm_update_calendar() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $time = time();
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeopen' => $time
            )
        );

        // Check that there is now an event in the database.
        $events = $DB->get_records('event');
        $this->assertCount(1, $events);

        // Get the event.
        $event = reset($events);

        // Confirm the event is correct.
        $this->assertEquals('scorm', $event->modulename);
        $this->assertEquals($scorm->id, $event->instance);
        $this->assertEquals(CALENDAR_EVENT_TYPE_ACTION, $event->type);
        $this->assertEquals(DATA_EVENT_TYPE_OPEN, $event->eventtype);
        $this->assertEquals($time, $event->timestart);
        $this->assertEquals($time, $event->timesort);
    }

    public function test_scorm_update_calendar_time_open_update() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $time = time();
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeopen' => $time
            )
        );

        // Set the time open and update the event.
        $scorm->timeopen = $time + DAYSECS;
        scorm_update_calendar($scorm, $scorm->cmid);

        // Check that there is an event in the database.
        $events = $DB->get_records('event');
        $this->assertCount(1, $events);

        // Get the event.
        $event = reset($events);

        // Confirm the event time was updated.
        $this->assertEquals('scorm', $event->modulename);
        $this->assertEquals($scorm->id, $event->instance);
        $this->assertEquals(CALENDAR_EVENT_TYPE_ACTION, $event->type);
        $this->assertEquals(DATA_EVENT_TYPE_OPEN, $event->eventtype);
        $this->assertEquals($time + DAYSECS, $event->timestart);
        $this->assertEquals($time + DAYSECS, $event->timesort);
    }

    public function test_scorm_update_calendar_time_open_delete() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm', array('course' => $course->id));

        // Create a scorm activity.
        $time = time();
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeopen' => $time
            )
        );

        // Set the time open to 0 and update the event.
        $scorm->timeopen = 0;
        scorm_update_calendar($scorm, $scorm->cmid);

        // Confirm the event was deleted.
        $this->assertEquals(0, $DB->count_records('event'));
    }

    public function test_scorm_update_calendar_time_close() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $time = time();
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeclose' => $time
            )
        );

        // Check that there is now an event in the database.
        $events = $DB->get_records('event');
        $this->assertCount(1, $events);

        // Get the event.
        $event = reset($events);

        // Confirm the event is correct.
        $this->assertEquals('scorm', $event->modulename);
        $this->assertEquals($scorm->id, $event->instance);
        $this->assertEquals(CALENDAR_EVENT_TYPE_ACTION, $event->type);
        $this->assertEquals(DATA_EVENT_TYPE_CLOSE, $event->eventtype);
        $this->assertEquals($time, $event->timestart);
        $this->assertEquals($time, $event->timesort);
    }

    public function test_scorm_update_calendar_time_close_update() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $time = time();
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeclose' => $time
            )
        );

        // Set the time close and update the event.
        $scorm->timeclose = $time + DAYSECS;
        scorm_update_calendar($scorm, $scorm->cmid);

        // Check that there is an event in the database.
        $events = $DB->get_records('event');
        $this->assertCount(1, $events);

        // Get the event.
        $event = reset($events);

        // Confirm the event time was updated.
        $this->assertEquals('scorm', $event->modulename);
        $this->assertEquals($scorm->id, $event->instance);
        $this->assertEquals(CALENDAR_EVENT_TYPE_ACTION, $event->type);
        $this->assertEquals(DATA_EVENT_TYPE_CLOSE, $event->eventtype);
        $this->assertEquals($time + DAYSECS, $event->timestart);
        $this->assertEquals($time + DAYSECS, $event->timesort);
    }

    public function test_scorm_update_calendar_time_close_delete() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a scorm activity.
        $scorm = $this->getDataGenerator()->create_module('scorm',
            array(
                'course' => $course->id,
                'timeclose' => time()
            )
        );

        // Set the time close to 0 and update the event.
        $scorm->timeclose = 0;
        scorm_update_calendar($scorm, $scorm->cmid);

        // Confirm the event time was deleted.
        $this->assertEquals(0, $DB->count_records('event'));
    }
}
