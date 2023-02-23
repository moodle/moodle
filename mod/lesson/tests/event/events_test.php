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

namespace mod_lesson\event;

use lesson;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lesson/locallib.php');

class events_test extends \advanced_testcase {

    /** @var stdClass the course used for testing */
    private $course;

    /** @var lesson the lesson used for testing */
    private $lesson;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp(): void {
        $this->resetAfterTest();

        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', array('course' => $this->course->id));

        // Convert to a lesson object.
        $this->lesson = new lesson($lesson);
    }

    /**
     * Test the page created event.
     *
     */
    public function test_page_created() {

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $pagerecord = $generator->create_content($this->lesson);
        $page = $this->lesson->load_page($pagerecord->id);

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\page_created', $event);
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the page created event.
     *
     */
    public function test_page_moved() {

        // Set up a generator to create content.
        // paga3 is the first one and page1 the last one.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $pagerecord1 = $generator->create_content($this->lesson);
        $page1 = $this->lesson->load_page($pagerecord1->id);
        $pagerecord2 = $generator->create_content($this->lesson);
        $page2 = $this->lesson->load_page($pagerecord2->id);
        $pagerecord3 = $generator->create_content($this->lesson);
        $page3 = $this->lesson->load_page($pagerecord3->id);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->resort_pages($page3->id, $pagerecord2->id);
        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertCount(1, $events);
        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\page_moved', $event);
        $this->assertEquals($page3->id, $event->objectid);
        $this->assertEquals($pagerecord1->id, $event->other['nextpageid']);
        $this->assertEquals($pagerecord2->id, $event->other['prevpageid']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the page deleted event.
     *
     */
    public function test_page_deleted() {

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        // Create a content page.
        $pagerecord = $generator->create_content($this->lesson);
        // Get the lesson page information.
        $page = $this->lesson->load_page($pagerecord->id);
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $page->delete();

        // Get our event event.
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\page_deleted', $event);
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the page updated event.
     *
     * There is no external API for updateing a page, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_page_updated() {

        // Trigger an event: page updated.
        $eventparams = array(
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'objectid' => 25,
            'other' => array(
                'pagetype' => 'True/false'
                )
        );

        $event = \mod_lesson\event\page_updated::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\page_updated', $event);
        $this->assertEquals(25, $event->objectid);
        $this->assertEquals('True/false', $event->other['pagetype']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
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
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'courseid' => $this->course->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\essay_attempt_viewed', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'view grade', 'essay.php?id=' . $this->lesson->properties()->cmid .
            '&mode=grade&attemptid='.$this->lesson->id, get_string('manualgrading', 'lesson'), $this->lesson->properties()->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the lesson started event.
     */
    public function test_lesson_started() {
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->start_timer();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\lesson_started', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'start', 'view.php?id=' . $this->lesson->properties()->cmid,
            $this->lesson->properties()->id, $this->lesson->properties()->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the lesson restarted event.
     */
    public function test_lesson_restarted() {

        // Initialize timer.
        $this->lesson->start_timer();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->update_timer(true);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\lesson_restarted', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'start', 'view.php?id=' . $this->lesson->properties()->cmid,
            $this->lesson->properties()->id, $this->lesson->properties()->cmid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

    }

    /**
     * Test the lesson restarted event.
     */
    public function test_lesson_resumed() {

        // Initialize timer.
        $this->lesson->start_timer();
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->update_timer(true, true);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\lesson_resumed', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'start', 'view.php?id=' . $this->lesson->properties()->cmid,
            $this->lesson->properties()->id, $this->lesson->properties()->cmid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();

    }
    /**
     * Test the lesson ended event.
     */
    public function test_lesson_ended() {
        global $DB, $USER;

        // Add a lesson timer so that stop_timer() does not complain.
        $lessontimer = new \stdClass();
        $lessontimer->lessonid = $this->lesson->properties()->id;
        $lessontimer->userid = $USER->id;
        $lessontimer->startime = time();
        $lessontimer->lessontime = time();
        $DB->insert_record('lesson_timer', $lessontimer);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->stop_timer();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\lesson_ended', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'end', 'view.php?id=' . $this->lesson->properties()->cmid,
            $this->lesson->properties()->id, $this->lesson->properties()->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the essay assessed event.
     *
     * There is no external API for assessing an essay, so the unit test will simply
     * create and trigger the event and ensure the legacy log data is returned as expected.
     */
    public function test_essay_assessed() {
        // Create an essay assessed event
        $gradeid = 5;
        $attemptid = 7;
        $event = \mod_lesson\event\essay_assessed::create(array(
            'objectid' => $gradeid,
            'relateduserid' => 3,
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'courseid' => $this->course->id,
            'other' => array(
                'lessonid' => $this->lesson->id,
                'attemptid' => $attemptid
            )
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\essay_assessed', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $expected = array($this->course->id, 'lesson', 'update grade', 'essay.php?id=' . $this->lesson->properties()->cmid,
                $this->lesson->name, $this->lesson->properties()->cmid);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the content page viewed event.
     *
     */
    public function test_content_page_viewed() {
        global $DB, $PAGE;

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        // Create a content page.
        $pagerecord = $generator->create_content($this->lesson);
        // Get the lesson page information.
        $page = $this->lesson->load_page($pagerecord->id);
        // Get the coursemodule record to setup the $PAGE->cm.
        $coursemodule = $DB->get_record('course_modules', array('id' => $this->lesson->properties()->cmid));
        // Set the $PAGE->cm.
        $PAGE->set_cm($coursemodule);
        // Get the appropriate renderer.
        $lessonoutput = $PAGE->get_renderer('mod_lesson');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Fire the function that leads to the triggering of our event.
        $lessonoutput->display_page($this->lesson, $page, false);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\content_page_viewed', $event);
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the question viewed event.
     *
     */
    public function test_question_viewed() {
        global $DB, $PAGE;

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        // Create a question page.
        $pagerecord = $generator->create_question_truefalse($this->lesson);
        // Get the lesson page information.
        $page = $this->lesson->load_page($pagerecord->id);
        // Get the coursemodule record to setup the $PAGE->cm.
        $coursemodule = $DB->get_record('course_modules', array('id' => $this->lesson->properties()->cmid));
        // Set the $PAGE->cm.
        $PAGE->set_cm($coursemodule);
        // Get the appropriate renderer.
        $lessonoutput = $PAGE->get_renderer('mod_lesson');

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        // Fire the function that leads to the triggering of our event.
        $lessonoutput->display_page($this->lesson, $page, false);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\question_viewed', $event);
        $this->assertEquals($page->id, $event->objectid);
        $this->assertEquals('True/false', $event->other['pagetype']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the question answered event.
     *
     * There is no external API for answering an truefalse question, so the unit test will simply
     * create and trigger the event and ensure data is returned as expected.
     */
    public function test_question_answered() {

        // Trigger an event: truefalse question answered.
        $eventparams = array(
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'objectid' => 25,
            'other' => array(
                'pagetype' => 'True/false'
                )
        );

        $event = \mod_lesson\event\question_answered::create($eventparams);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\question_answered', $event);
        $this->assertEquals(25, $event->objectid);
        $this->assertEquals('True/false', $event->other['pagetype']);
        $this->assertEventContextNotUsed($event);
        $this->assertDebuggingNotCalled();
    }

    /**
     * Test the user override created event.
     *
     * There is no external API for creating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_created() {

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'other' => array(
                'lessonid' => $this->lesson->id
            )
        );
        $event = \mod_lesson\event\user_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\user_override_created', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override created event.
     *
     * There is no external API for creating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_created() {

        $params = array(
            'objectid' => 1,
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'other' => array(
                'lessonid' => $this->lesson->id,
                'groupid' => 2
            )
        );
        $event = \mod_lesson\event\group_override_created::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\group_override_created', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override updated event.
     *
     * There is no external API for updating a user override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_user_override_updated() {

        $params = array(
            'objectid' => 1,
            'relateduserid' => 2,
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'other' => array(
                'lessonid' => $this->lesson->id
            )
        );
        $event = \mod_lesson\event\user_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\user_override_updated', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override updated event.
     *
     * There is no external API for updating a group override, so the unit test will simply
     * create and trigger the event and ensure the event data is returned as expected.
     */
    public function test_group_override_updated() {

        $params = array(
            'objectid' => 1,
            'context' => \context_module::instance($this->lesson->properties()->cmid),
            'other' => array(
                'lessonid' => $this->lesson->id,
                'groupid' => 2
            )
        );
        $event = \mod_lesson\event\group_override_updated::create($params);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\group_override_updated', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the user override deleted event.
     */
    public function test_user_override_deleted() {
        global $DB;

        // Create an override.
        $override = new \stdClass();
        $override->lesson = $this->lesson->id;
        $override->userid = 2;
        $override->id = $DB->insert_record('lesson_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\user_override_deleted', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the group override deleted event.
     */
    public function test_group_override_deleted() {
        global $DB;

        // Create an override.
        $override = new \stdClass();
        $override->lesson = $this->lesson->id;
        $override->groupid = 2;
        $override->id = $DB->insert_record('lesson_overrides', $override);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $this->lesson->delete_override($override->id);
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\mod_lesson\event\group_override_deleted', $event);
        $this->assertEquals(\context_module::instance($this->lesson->properties()->cmid), $event->get_context());
        $this->assertEventContextNotUsed($event);
    }
}
