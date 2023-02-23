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
 * @package   assignsubmission_file
 * @copyright 2013 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_file\event;

use mod_assign_test_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/generator.php');

class events_test extends \advanced_testcase {

    // Use the generator helper.
    use mod_assign_test_generator;

    /**
     * Test that the assessable_uploaded event is fired when a file submission has been made.
     */
    public function test_assessable_uploaded() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();
        $cm = $assign->get_course_module();

        $this->setUser($student->id);
        $submission = $assign->get_user_submission($student->id, true);

        $fs = get_file_storage();
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $fi = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.png'
        );
        $fi2 = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $files = $fs->get_area_files($context->id, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA,
            $submission->id, 'id', false);

        $data = new \stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        $event = reset($events);
        $this->assertInstanceOf('\assignsubmission_file\event\assessable_uploaded', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertCount(2, $event->other['pathnamehashes']);
        $this->assertEquals($fi->get_pathnamehash(), $event->other['pathnamehashes'][0]);
        $this->assertEquals($fi2->get_pathnamehash(), $event->other['pathnamehashes'][1]);
        $expected = new \stdClass();
        $expected->modulename = 'assign';
        $expected->cmid = $cm->id;
        $expected->itemid = $submission->id;
        $expected->courseid = $course->id;
        $expected->userid = $student->id;
        $expected->file = $files;
        $expected->files = $files;
        $expected->pathnamehashes = array($fi->get_pathnamehash(), $fi2->get_pathnamehash());
        $this->assertEventLegacyData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that the submission_created event is fired when a file submission is saved.
     */
    public function test_submission_created() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();

        $this->setUser($student->id);
        $submission = $assign->get_user_submission($student->id, true);

        $fs = get_file_storage();
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $fi = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.png'
        );
        $fi2 = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $files = $fs->get_area_files($context->id, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA,
            $submission->id, 'id', false);

        $data = new \stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        // We want to test the last event fired.
        $event = $events[1];
        $this->assertInstanceOf('\assignsubmission_file\event\submission_created', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals($submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($submission->status, $event->other['submissionstatus']);
        $this->assertEquals($submission->userid, $event->relateduserid);
    }

    /**
     * Test that the submission_updated event is fired when a file submission is saved when an existing submission already exists.
     */
    public function test_submission_updated() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $assign = $this->create_instance($course);
        $context = $assign->get_context();

        $this->setUser($student->id);
        $submission = $assign->get_user_submission($student->id, true);

        $fs = get_file_storage();
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $fi = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $dummy = (object) array(
            'contextid' => $context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.png'
        );
        $fi2 = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $files = $fs->get_area_files($context->id, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA,
            $submission->id, 'id', false);

        $data = new \stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        // Create a submission.
        $plugin->save($submission, $data);
        // Update a submission.
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(4, $events);
        // We want to test the last event fired.
        $event = $events[3];
        $this->assertInstanceOf('\assignsubmission_file\event\submission_updated', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($course->id, $event->courseid);
        $this->assertEquals($submission->id, $event->other['submissionid']);
        $this->assertEquals($submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($submission->status, $event->other['submissionstatus']);
        $this->assertEquals($submission->userid, $event->relateduserid);
    }

}
