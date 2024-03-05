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
 * @package    mod_survey
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_survey\event;

/**
 * Events tests class.
 *
 * @package    mod_survey
 * @copyright  2014 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    /**
     * Setup.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        // Survey module is disabled by default, enable it for testing.
        $manager = \core_plugin_manager::resolve_plugininfo_class('mod');
        $manager::enable_plugin('survey', 1);
    }

    /**
     * Test report downloaded event.
     */
    public function test_report_downloaded() {
        // There is no proper API to call to generate chapters for a book, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        $params = array(
            'objectid' => $survey->id,
            'context' => \context_module::instance($survey->cmid),
            'courseid' => $course->id,
            'other' => array('type' => 'xls')
        );
        $event = \mod_survey\event\report_downloaded::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\report_downloaded', $event);
        $this->assertEquals(\context_module::instance($survey->cmid), $event->get_context());
        $this->assertEquals($survey->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test report viewed event.
     */
    public function test_report_viewed() {
        // There is no proper API to call to generate chapters for a book, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        $params = array(
            'objectid' => $survey->id,
            'context' => \context_module::instance($survey->cmid),
            'courseid' => $course->id
        );
        $event = \mod_survey\event\report_viewed::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\report_viewed', $event);
        $this->assertEquals(\context_module::instance($survey->cmid), $event->get_context());
        $this->assertEquals($survey->id, $event->objectid);
    }

    /**
     * Test response submitted event.
     */
    public function test_response_submitted() {
        // There is no proper API to call to generate chapters for a book, so what we are
        // doing here is simply making sure that the events returns the right information.

        $course = $this->getDataGenerator()->create_course();
        $survey = $this->getDataGenerator()->create_module('survey', array('course' => $course->id));

        $params = array(
            'context' => \context_module::instance($survey->cmid),
            'courseid' => $course->id,
            'other' => array('surveyid' => $survey->id)
        );
        $event = \mod_survey\event\response_submitted::create($params);

        // Triggering and capturing the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_survey\event\response_submitted', $event);
        $this->assertEquals(\context_module::instance($survey->cmid), $event->get_context());
        $this->assertEquals($survey->id, $event->other['surveyid']);
        $this->assertEventContextNotUsed($event);
    }
}
