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
 * Contains the event tests for the module assign.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');
require_once($CFG->dirroot . '/mod/assign/tests/fixtures/event_mod_assign_fixtures.php');

/**
 * Contains the event tests for the module assign.
 *
 * @package   mod_assign
 * @copyright 2014 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_events_testcase extends advanced_testcase {

    /** @var stdClass $user A user to submit an assignment. */
    protected $user;

    /** @var stdClass $course New course created to hold the assignment activity. */
    protected $course;

    /** @var stdClass $cm A context module object. */
    protected $cm;

    /** @var stdClass $context Context of the assignment activity. */
    protected $context;

    /** @var array $params Standard event parameters. */
    protected $params;

    /**
     * Setup all the various parts of an assignment activity.
     */
    protected function setUp() {
        $this->user = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance(array('course' => $this->course->id));
        $this->cm = get_coursemodule_from_instance('assign', $instance->id);
        $this->context = context_module::instance($this->cm->id);

        // Standard Event parameters.
        $this->params = array(
            'context' => $this->context,
            'courseid' => $this->course->id
        );
    }

    /**
     * Basic tests for the submission_created() abstract class.
     */
    public function test_submission_created() {
        $this->resetAfterTest();

        $eventinfo = $this->params;
        $eventinfo['other'] = array(
            'submissionid' => '17',
            'submissionattempt' => 0,
            'submissionstatus' => 'submitted'
        );

        $sink = $this->redirectEvents();
        $event = \mod_assign_unittests\event\submission_created::create($eventinfo);
        $event->trigger();
        $result = $sink->get_events();
        $event = reset($result);
        $sink->close();

        $this->assertEquals($this->context->id, $event->contextid);
        $this->assertEquals($this->course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($this->params);
            $this->fail('Other must contain the key submissionid.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $this->params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Basic tests for the submission_updated() abstract class.
     */
    public function test_submission_updated() {
        $this->resetAfterTest();

        $eventinfo = $this->params;
        $eventinfo['other'] = array(
            'submissionid' => '17',
            'submissionattempt' => 0,
            'submissionstatus' => 'submitted'
        );

        $sink = $this->redirectEvents();
        $event = \mod_assign_unittests\event\submission_updated::create($eventinfo);
        $event->trigger();
        $result = $sink->get_events();
        $event = reset($result);
        $sink->close();

        $this->assertEquals($this->context->id, $event->contextid);
        $this->assertEquals($this->course->id, $event->courseid);

        // Check that an error occurs when teamsubmission is not set.
        try {
            \mod_assign_unittests\event\submission_created::create($this->params);
            $this->fail('Other must contain the key submissionid.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        // Check that the submission status debugging is fired.
        $subinfo = $this->params;
        $subinfo['other'] = array('submissionid' => '23');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionattempt.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        $subinfo['other'] = array('submissionattempt' => '0');
        try {
            \mod_assign_unittests\event\submission_created::create($subinfo);
            $this->fail('Other must contain the key submissionstatus.');
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }
}

