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
 * Contains the event tests for the plugin.
 *
 * @package   assignsubmission_onlinetext
 * @copyright 2013 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_onlinetext\event;

use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

final class events_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Test that the assessable_uploaded event is fired when an online text submission is saved.
     */
    public function test_assessable_uploaded(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();
        $cm = $assign->get_course_module();

        $this->setUser($student->id);

        $submission = $assign->get_user_submission($student->id, true);
        $data = (object) [
            'onlinetext_editor' => [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => 'Submission text',
                'format' => FORMAT_PLAIN,
            ],
        ];

        $sink = $this->redirectEvents();
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        $event = reset($events);
        $this->assertInstanceOf('\assignsubmission_onlinetext\event\assessable_uploaded', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals(array(), $event->other['pathnamehashes']);
        $this->assertEquals(FORMAT_PLAIN, $event->other['format']);
        $this->assertEquals('Submission text', $event->other['content']);
        $expected = new \stdClass();
        $expected->modulename = 'assign';
        $expected->cmid = $cm->id;
        $expected->itemid = $submission->id;
        $expected->courseid = $course->id;
        $expected->userid = $student->id;
        $expected->content = 'Submission text';
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that the submission_created event is fired when an onlinetext submission is saved.
     */
    public function test_submission_created(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();

        $this->setUser($student->id);

        $submission = $assign->get_user_submission($student->id, true);
        $data = (object) [
            'onlinetext_editor' => [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => 'Submission text',
                'format' => FORMAT_PLAIN,
            ],
        ];

        $sink = $this->redirectEvents();
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        $event = $events[1];
        $this->assertInstanceOf('\assignsubmission_onlinetext\event\submission_created', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals($submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($submission->status, $event->other['submissionstatus']);
        $this->assertEquals($submission->userid, $event->relateduserid);
    }

    /**
     * Test that the submission_updated event is fired when an onlinetext
     * submission is saved and an existing submission already exists.
     */
    public function test_submission_updated(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();

        $this->setUser($student->id);

        $submission = $assign->get_user_submission($student->id, true);
        $data = (object) [
            'onlinetext_editor' => [
                'itemid' => file_get_unused_draft_itemid(),
                'text' => 'Submission text',
                'format' => FORMAT_PLAIN,
            ],
        ];

        $sink = $this->redirectEvents();
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $plugin->save($submission, $data);
        $sink->clear();

        // Update a submission.
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        $event = $events[1];
        $this->assertInstanceOf('\assignsubmission_onlinetext\event\submission_updated', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals($submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($submission->status, $event->other['submissionstatus']);
        $this->assertEquals($submission->userid, $event->relateduserid);
    }
}
