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
 * @package    mod_lesson
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lesson/locallib.php');

class mod_lesson_events_testcase extends advanced_testcase {

    /** @var stdClass the course used for testing */
    private $course;

    /** @var lesson the lesson used for testing */
    private $lesson;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $this->course->id));

        // Convert to a lesson object.
        $this->lesson = new lesson($lesson);
    }

    /**
     * Test the essay attempt viewed event.
     *
     * There is no external API for viewing an essay attempt, so the unit test will simply
     * create and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_essay_attempt_viewed() {
        // Create a essays list viewed event
        $event = \mod_lesson\event\essay_attempt_viewed::create(array(
            'objectid' => $this->lesson->id,
            'relateduserid' => 3,
            'context' => context_module::instance($this->lesson->properties()->cmid),
            'courseid' => $this->course->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\essay_attempt_viewed', $event);
        $this->assertEquals(context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'view grade', 'essay.php?id=' . $this->lesson->properties()->cmid .
            '&mode=grade&attemptid=1', get_string('manualgrading', 'lesson'), $this->lesson->properties()->cmid);
        $this->assertEventLegacyLogData($expected, $event);
    }
}
