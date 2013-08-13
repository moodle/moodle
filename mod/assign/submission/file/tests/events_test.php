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

    public function test_assessable_uploaded() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course->id;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $context = context_module::instance($cm->id);
        $assign = new testable_assign($context, $cm, $course);

        $this->setUser($user->id);
        $submission = $assign->get_user_submission($user->id, true);

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

        $data = new stdClass();
        $plugin = $assign->get_submission_plugin_by_type('file');
        $sink = $this->redirectEvents();
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\assignsubmission_file\event\assessable_uploaded', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertCount(2, $event->other['pathnamehashes']);
        $this->assertEquals($fi->get_pathnamehash(), $event->other['pathnamehashes'][0]);
        $this->assertEquals($fi2->get_pathnamehash(), $event->other['pathnamehashes'][1]);
        $expected = new stdClass();
        $expected->modulename = 'assign';
        $expected->cmid = $cm->id;
        $expected->itemid = $submission->id;
        $expected->courseid = $course->id;
        $expected->userid = $user->id;
        $expected->file = $files;
        $expected->files = $files;
        $expected->pathnamehashes = array($fi->get_pathnamehash(), $fi2->get_pathnamehash());
        $this->assertEventLegacyData($expected, $event);
    }

}
