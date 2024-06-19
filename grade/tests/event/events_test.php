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
 * Unit tests for events found in /grade/letter and /grade/scale.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2017 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace core_grades\event;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/lib.php');

/**
 * Unit tests for grade events.
 *
 * @package   core_grades
 * @category  test
 * @copyright 2017 Stephen Bourget
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events_test extends \advanced_testcase {

    /** @var stdClass the course used for testing */
    private $course;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Test the grade letter created event.
     *
     * There is no external API for triggering this event, so the unit test will simply
     * create and trigger the event and ensure the data is returned as expected.
     */
    public function test_grade_letter_created(): void {
        // Create a grade letter created event.
        $event = \core\event\grade_letter_created::create(array(
            'objectid' => 10,
            'context' => \context_course::instance($this->course->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\grade_letter_created', $event);
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }

    /**
     * Test the grade letter deleted event.
     *
     * There is no external API for triggering this event, so the unit test will simply
     * create and trigger the event and ensure the data is returned as expected.
     */
    public function test_grade_letter_deleted(): void {
        // Create a grade letter deleted event.
        $event = \core\event\grade_letter_deleted::create(array(
            'objectid' => 10,
            'context' => \context_course::instance($this->course->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\grade_letter_deleted', $event);
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }

    /**
     * Test the grade letter updated event.
     *
     * There is no external API for triggering this event, so the unit test will simply
     * create and trigger the event and ensure the data is returned as expected.
     */
    public function test_grade_letter_updated(): void {
        // Create a grade letter updated event.
        $event = \core\event\grade_letter_updated::create(array(
            'objectid' => 10,
            'context' => \context_course::instance($this->course->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\grade_letter_updated', $event);
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }

    /**
     * Test the scale created event.
     */
    public function test_scale_created(): void {
        $gradescale = new \grade_scale();
        $gradescale->name        = 'unittestscale3';
        $gradescale->courseid    = $this->course->id;
        $gradescale->userid      = 317;
        $gradescale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $gradescale->description = 'This scale is used to mark standard assignments.';

        $url = new \moodle_url('/grade/edit/scale/index.php', array('id' => $this->course->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $id = $gradescale->insert();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\scale_created', $event);
        $this->assertEquals($id, $event->objectid);
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }

    /**
     * Test the scale deleted event.
     */
    public function test_scale_deleted(): void {
        $gradescale = new \grade_scale();
        $gradescale->name        = 'unittestscale3';
        $gradescale->courseid    = $this->course->id;
        $gradescale->userid      = 317;
        $gradescale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $gradescale->description = 'This scale is used to mark standard assignments.';
        $gradescale->insert();

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $gradescale->delete();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\scale_deleted', $event);
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }

    /**
     * Test the scale updated event.
     */
    public function test_scale_updated(): void {
        $gradescale = new \grade_scale();
        $gradescale->name        = 'unittestscale3';
        $gradescale->courseid    = $this->course->id;
        $gradescale->userid      = 317;
        $gradescale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $gradescale->description = 'This scale is used to mark standard assignments.';
        $id = $gradescale->insert();

        $gradescale->name = 'Updated info for this unittest grade_scale';
        $url = new \moodle_url('/grade/edit/scale/index.php', array('id' => $this->course->id));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $gradescale->update();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\scale_updated', $event);
        $this->assertEquals($id, $event->objectid);
        $this->assertEquals($url, $event->get_url());
        $this->assertEquals(\context_course::instance($this->course->id), $event->get_context());
    }
}
