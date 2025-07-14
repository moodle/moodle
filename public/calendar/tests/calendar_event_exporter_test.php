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

namespace core_calendar;

use core_calendar\external\calendar_event_exporter;
use core_calendar\local\event\container;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/helpers.php');

/**
 * Calendar event exporter testcase.
 *
 * @package core_calendar
 * @copyright 2017 Ryan Wyllie <ryan@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class calendar_event_exporter_test extends \advanced_testcase {
    /**
     * Data provider for the timestamp min limit test case to confirm
     * that the minimum time limit is set correctly on the boundary cases.
     */
    public static function get_timestamp_min_limit_test_cases(): array {
        $now = time();
        $todaymidnight = usergetmidnight($now);
        $tomorrowmidnight = $todaymidnight + DAYSECS;
        $eightam = $todaymidnight + (60 * 60 * 8);
        $starttime = (new \DateTime())->setTimestamp($eightam);

        return [
            'before min' => [
                $starttime,
                [
                    ($starttime->getTimestamp() + 1),
                    'some error'
                ],
                $tomorrowmidnight
            ],
            'equal min' => [
                $starttime,
                [
                    $starttime->getTimestamp(),
                    'some error'
                ],
                $todaymidnight
            ],
            'after min' => [
                $starttime,
                [
                    ($starttime->getTimestamp() - 1),
                    'some error'
                ],
                $todaymidnight
            ]
        ];
    }

    /**
     * @dataProvider get_timestamp_min_limit_test_cases
     */
    public function test_get_timestamp_min_limit($starttime, $min, $expected): void {
        $class = calendar_event_exporter::class;
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $reflector = new \ReflectionClass($class);
        $method = $reflector->getMethod('get_timestamp_min_limit');

        $result = $method->invoke($mock, $starttime, $min);
        $this->assertEquals($expected, $result['mindaytimestamp']);
        $this->assertEquals($min[1], $result['mindayerror']);
    }

    /**
     * Data provider for the timestamp max limit test case to confirm
     * that the maximum time limit is set correctly on the boundary cases.
     */
    public static function get_timestamp_max_limit_test_cases(): array {
        $now = time();
        $todaymidnight = usergetmidnight($now);
        $yesterdaymidnight = $todaymidnight - DAYSECS;
        $eightam = $todaymidnight + (60 * 60 * 8);
        $starttime = (new \DateTime())->setTimestamp($eightam);

        return [
            'before max' => [
                $starttime,
                [
                    ($starttime->getTimestamp() + 1),
                    'some error'
                ],
                $todaymidnight
            ],
            'equal max' => [
                $starttime,
                [
                    $starttime->getTimestamp(),
                    'some error'
                ],
                $todaymidnight
            ],
            'after max' => [
                $starttime,
                [
                    ($starttime->getTimestamp() - 1),
                    'some error'
                ],
                $yesterdaymidnight
            ]
        ];
    }

    /**
     * @dataProvider get_timestamp_max_limit_test_cases
     */
    public function test_get_timestamp_max_limit($starttime, $max, $expected): void {
        $class = calendar_event_exporter::class;
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $reflector = new \ReflectionClass($class);
        $method = $reflector->getMethod('get_timestamp_max_limit');

        $result = $method->invoke($mock, $starttime, $max);
        $this->assertEquals($expected, $result['maxdaytimestamp']);
        $this->assertEquals($max[1], $result['maxdayerror']);
    }

    /**
     * Exporting a course event should generate the course URL.
     */
    public function test_calendar_event_exporter_course_url_course_event(): void {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/course/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $now = time();
        $mapper = container::get_event_mapper();
        $legacyevent = create_event([
            'courseid' => $course->id,
            'userid' => 1,
            'eventtype' => 'course',
            'timestart' => $now
        ]);
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $exporter = new calendar_event_exporter($event, [
            'context' => $context,
            'course' => $course,
            'moduleinstance' => null,
            'daylink' => new \moodle_url(''),
            'type' => type_factory::get_calendar_instance(),
            'today' => $now
        ]);

        $courseurl = course_get_url($course->id);
        $expected = $courseurl->out(false);
        $renderer = $PAGE->get_renderer('core_calendar');
        $exportedevent = $exporter->export($renderer);

        // The exported URL should be for the course.
        $this->assertEquals($expected, $exportedevent->url);
    }

    /**
     * Exporting a user event should generate the site course URL.
     */
    public function test_calendar_event_exporter_course_url_user_event(): void {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/course/lib.php');

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $context = \context_user::instance($user->id);
        $now = time();
        $mapper = container::get_event_mapper();
        $legacyevent = create_event([
            'courseid' => 0,
            'userid' => $user->id,
            'eventtype' => 'user',
            'timestart' => $now
        ]);
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $exporter = new calendar_event_exporter($event, [
            'context' => $context,
            'course' => null,
            'moduleinstance' => null,
            'daylink' => new \moodle_url(''),
            'type' => type_factory::get_calendar_instance(),
            'today' => $now
        ]);

        $courseurl = course_get_url(SITEID);
        $expected = $courseurl->out(false);
        $renderer = $PAGE->get_renderer('core_calendar');
        $exportedevent = $exporter->export($renderer);

        // The exported URL should be for the site course.
        $this->assertEquals($expected, $exportedevent->url);
    }

    /**
     * Popup name respects filters for course shortname.
     */
    public function test_calendar_event_exporter_popupname_course_shortname_strips_links(): void {
        global $CFG, $PAGE;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $rawshortname = 'Shortname <a href="#">link</a>';
        $nolinkshortname = strip_links($rawshortname);
        $course = $generator->create_course(['shortname' => $rawshortname]);
        $coursecontext = \context_course::instance($course->id);
        $now = time();
        $mapper = container::get_event_mapper();
        $renderer = $PAGE->get_renderer('core_calendar');
        $legacyevent = create_event([
            'courseid' => $course->id,
            'userid' => 1,
            'eventtype' => 'course',
            'timestart' => $now
        ]);
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $exporter = new calendar_event_exporter($event, [
            'context' => $coursecontext,
            'course' => $course,
            'moduleinstance' => null,
            'daylink' => new \moodle_url(''),
            'type' => type_factory::get_calendar_instance(),
            'today' => $now
        ]);

        $exportedevent = $exporter->export($renderer);
        // Links should always be stripped from the course short name.
        $this->assertMatchesRegularExpression("/$nolinkshortname/", $exportedevent->popupname);
    }

    /**
     * Exported event contains the exported course.
     */
    public function test_calendar_event_exporter_exports_course(): void {
        global $CFG, $PAGE;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $rawshortname = 'Shortname <a href="#">link</a>';
        $nolinkshortname = strip_links($rawshortname);
        $course = $generator->create_course(['shortname' => $rawshortname]);
        $coursecontext = \context_course::instance($course->id);
        $now = time();
        $mapper = container::get_event_mapper();
        $renderer = $PAGE->get_renderer('core_calendar');
        $legacyevent = create_event([
            'courseid' => $course->id,
            'userid' => 1,
            'eventtype' => 'course',
            'timestart' => $now
        ]);
        $event = $mapper->from_legacy_event_to_event($legacyevent);
        $exporter = new calendar_event_exporter($event, [
            'context' => $coursecontext,
            'course' => $course,
            'moduleinstance' => null,
            'daylink' => new \moodle_url(''),
            'type' => type_factory::get_calendar_instance(),
            'today' => $now
        ]);

        $exportedevent = $exporter->export($renderer);
        $courseexporter = new \core_course\external\course_summary_exporter($course, [
            'context' => $coursecontext
        ]);
        $exportedcourse = $courseexporter->export($renderer);
        $this->assertEquals($exportedevent->course, $exportedcourse);
    }
}
