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
 * Unit tests for workshop events.
 *
 * @package    mod_workshop
 * @category   phpunit
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_workshop\event;

use testable_workshop;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workshop/lib.php'); // Include the code to test.
require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test.
require_once(__DIR__ . '/../fixtures/testable.php');


/**
 * Test cases for the internal workshop api
 */
class events_test extends \advanced_testcase {

    /** @var \stdClass $workshop Basic workshop data stored in an object. */
    protected $workshop;
    /** @var \stdClass $course Generated Random Course. */
    protected $course;
    /** @var stdClass mod info */
    protected $cm;
    /** @var context $context Course module context. */
    protected $context;

    /**
     * Set up the testing environment.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->setAdminUser();

        // Create a workshop activity.
        $this->course = $this->getDataGenerator()->create_course();
        $this->workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $this->course));
        $this->cm = get_coursemodule_from_instance('workshop', $this->workshop->id);
        $this->context = \context_module::instance($this->cm->id);
    }

    protected function tearDown(): void {
        $this->workshop = null;
        $this->course = null;
        $this->cm = null;
        $this->context = null;
        parent::tearDown();
    }

    /**
     * This event is triggered in view.php and workshop/lib.php through the function workshop_cron().
     */
    public function test_phase_switched_event() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Add additional workshop information.
        $this->workshop->phase = 20;
        $this->workshop->phaseswitchassessment = 1;
        $this->workshop->submissionend = time() - 1;

        $cm = get_coursemodule_from_instance('workshop', $this->workshop->id, $this->course->id, false, MUST_EXIST);
        $workshop = new testable_workshop($this->workshop, $cm, $this->course);

        // The phase that we are switching to.
        $newphase = 30;
        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $workshop->switch_phase($newphase);
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertEventContextNotUsed($event);

        $sink->close();
    }

    public function test_assessment_evaluated() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $cm = get_coursemodule_from_instance('workshop', $this->workshop->id, $this->course->id, false, MUST_EXIST);

        $workshop = new testable_workshop($this->workshop, $cm, $this->course);

        $assessments = array();
        $assessments[] = (object)array('reviewerid' => 2, 'gradinggrade' => null,
            'gradinggradeover' => null, 'aggregationid' => null, 'aggregatedgrade' => 12);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $workshop->aggregate_grading_grades_process($assessments);
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\mod_workshop\event\assessment_evaluated', $event);
        $this->assertEquals('workshop_aggregations', $event->objecttable);
        $this->assertEquals(\context_module::instance($cm->id), $event->get_context());
        $this->assertEventContextNotUsed($event);

        $sink->close();
    }

    public function test_assessment_reevaluated() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $cm = get_coursemodule_from_instance('workshop', $this->workshop->id, $this->course->id, false, MUST_EXIST);

        $workshop = new testable_workshop($this->workshop, $cm, $this->course);

        $assessments = array();
        $assessments[] = (object)array('reviewerid' => 2, 'gradinggrade' => null, 'gradinggradeover' => null,
            'aggregationid' => 2, 'aggregatedgrade' => 12);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $workshop->aggregate_grading_grades_process($assessments);
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\mod_workshop\event\assessment_reevaluated', $event);
        $this->assertEquals('workshop_aggregations', $event->objecttable);
        $this->assertEquals(\context_module::instance($cm->id), $event->get_context());
        $this->assertEventContextNotUsed($event);

        $sink->close();
    }

    /**
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_aggregate_grades_reset_event() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $event = \mod_workshop\event\assessment_evaluations_reset::create(array(
            'context'  => $this->context,
            'courseid' => $this->course->id,
            'other' => array('workshopid' => $this->workshop->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $sink->close();
    }

    /**
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_instances_list_viewed_event() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $context = \context_course::instance($this->course->id);

        $event = \mod_workshop\event\course_module_instance_list_viewed::create(array('context' => $context));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertEventContextNotUsed($event);

        $sink->close();
    }

    /**
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_submission_created_event() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user();
        $submissionid = 48;

        $event = \mod_workshop\event\submission_created::create(array(
                'objectid'      => $submissionid,
                'context'       => $this->context,
                'courseid'      => $this->course->id,
                'relateduserid' => $user->id,
                'other'         => array(
                    'submissiontitle' => 'The submission title'
                )
            )
        );

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertEventContextNotUsed($event);

        $sink->close();
    }
}
