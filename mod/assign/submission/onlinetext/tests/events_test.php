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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

class assignsubmission_onlinetext_events_testcase extends advanced_testcase {

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
        $data = new stdClass();
        $data->onlinetext_editor = array(
            'itemid' => file_get_unused_draft_itemid(),
            'text' => 'Submission text',
            'format' => FORMAT_PLAIN
        );
        $plugin = $assign->get_submission_plugin_by_type('onlinetext');
        $sink = $this->redirectEvents();
        $plugin->save($submission, $data);
        $events = $sink->get_events();

        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertInstanceOf('\assignsubmission_onlinetext\event\assessable_uploaded', $event);
        $this->assertEquals($context->id, $event->contextid);
        $this->assertEquals($submission->id, $event->objectid);
        $this->assertEquals(array(), $event->other['pathnamehashes']);
        $this->assertEquals('Submission text', $event->other['content']);
        $expected = new stdClass();
        $expected->modulename = 'assign';
        $expected->cmid = $cm->id;
        $expected->itemid = $submission->id;
        $expected->courseid = $course->id;
        $expected->userid = $user->id;
        $expected->content = 'Submission text';
        $this->assertEventLegacyData($expected, $event);
    }

}
