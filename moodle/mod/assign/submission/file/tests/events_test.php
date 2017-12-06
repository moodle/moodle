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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

class assignsubmission_file_events_testcase extends advanced_testcase {

    /** @var stdClass $user A user to submit an assignment. */
    protected $user;

    /** @var stdClass $course New course created to hold the assignment activity. */
    protected $course;

    /** @var stdClass $cm A context module object. */
    protected $cm;

    /** @var stdClass $context Context of the assignment activity. */
    protected $context;

    /** @var stdClass $assign The assignment object. */
    protected $assign;

    /** @var stdClass $files Files that are being submitted for the assignment. */
    protected $files;

    /** @var stdClass $submission Submission information. */
    protected $submission;

    /** @var stdClass $fi File information - First file*/
    protected $fi;

    /** @var stdClass $fi2 File information - Second file*/
    protected $fi2;

    /**
     * Setup all the various parts of an assignment activity including creating a file submission.
     */
    protected function setUp() {
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $this->course->id;
        $instance = $generator->create_instance($params);
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);
        $this->assign = new testable_assign($this->context, $this->cm, $this->course);

        $this->setUser($this->user->id);
        $this->submission = $this->assign->get_user_submission($this->user->id, true);

        $fs = get_file_storage();
        $dummy = (object) array(
            'contextid' => $this->context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $this->submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.pdf'
        );
        $this->fi = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $dummy = (object) array(
            'contextid' => $this->context->id,
            'component' => 'assignsubmission_file',
            'filearea' => ASSIGNSUBMISSION_FILE_FILEAREA,
            'itemid' => $this->submission->id,
            'filepath' => '/',
            'filename' => 'myassignmnent.png'
        );
        $this->fi2 = $fs->create_file_from_string($dummy, 'Content of ' . $dummy->filename);
        $this->files = $fs->get_area_files($this->context->id, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA,
            $this->submission->id, 'id', false);

    }

    /**
     * Test that the assessable_uploaded event is fired when a file submission has been made.
     */
    public function test_assessable_uploaded() {
        $this->resetAfterTest();

        $data = new stdClass();
        $plugin = $this->assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        $plugin->save($this->submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        $event = reset($events);
        $this->assertInstanceOf('\assignsubmission_file\event\assessable_uploaded', $event);
        $this->assertEquals($this->context->id, $event->contextid);
        $this->assertEquals($this->submission->id, $event->objectid);
        $this->assertCount(2, $event->other['pathnamehashes']);
        $this->assertEquals($this->fi->get_pathnamehash(), $event->other['pathnamehashes'][0]);
        $this->assertEquals($this->fi2->get_pathnamehash(), $event->other['pathnamehashes'][1]);
        $expected = new stdClass();
        $expected->modulename = 'assign';
        $expected->cmid = $this->cm->id;
        $expected->itemid = $this->submission->id;
        $expected->courseid = $this->course->id;
        $expected->userid = $this->user->id;
        $expected->file = $this->files;
        $expected->files = $this->files;
        $expected->pathnamehashes = array($this->fi->get_pathnamehash(), $this->fi2->get_pathnamehash());
        $this->assertEventLegacyData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test that the submission_created event is fired when a file submission is saved.
     */
    public function test_submission_created() {
        $this->resetAfterTest();

        $data = new stdClass();
        $plugin = $this->assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        $plugin->save($this->submission, $data);
        $events = $sink->get_events();

        $this->assertCount(2, $events);
        // We want to test the last event fired.
        $event = $events[1];
        $this->assertInstanceOf('\assignsubmission_file\event\submission_created', $event);
        $this->assertEquals($this->context->id, $event->contextid);
        $this->assertEquals($this->course->id, $event->courseid);
        $this->assertEquals($this->submission->id, $event->other['submissionid']);
        $this->assertEquals($this->submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($this->submission->status, $event->other['submissionstatus']);
        $this->assertEquals($this->submission->userid, $event->relateduserid);
    }

    /**
     * Test that the submission_updated event is fired when a file submission is saved when an existing submission already exists.
     */
    public function test_submission_updated() {
        $this->resetAfterTest();

        $data = new stdClass();
        $plugin = $this->assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        // Create a submission.
        $plugin->save($this->submission, $data);
        // Update a submission.
        $plugin->save($this->submission, $data);
        $events = $sink->get_events();

        $this->assertCount(4, $events);
        // We want to test the last event fired.
        $event = $events[3];
        $this->assertInstanceOf('\assignsubmission_file\event\submission_updated', $event);
        $this->assertEquals($this->context->id, $event->contextid);
        $this->assertEquals($this->course->id, $event->courseid);
        $this->assertEquals($this->submission->id, $event->other['submissionid']);
        $this->assertEquals($this->submission->attemptnumber, $event->other['submissionattempt']);
        $this->assertEquals($this->submission->status, $event->other['submissionstatus']);
        $this->assertEquals($this->submission->userid, $event->relateduserid);
    }

}
