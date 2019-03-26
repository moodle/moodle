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
 * Unit tests for mod/workshop/lib.php.
 *
 * @package    mod_workshop
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/workshop/lib.php');

/**
 * Unit tests for mod/workshop/lib.php.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_workshop_lib_testcase extends advanced_testcase {

    /**
     * Test calendar event provide action open.
     */
    public function test_workshop_core_calendar_provide_event_action_open() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => $now - DAYSECS, 'submissionend' => $now + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event provide action open for a non user.
     */
    public function test_workshop_core_calendar_provide_event_action_open_for_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => $now - DAYSECS, 'submissionend' => $now + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    /**
     * Test calendar event provide action open when user id is provided.
     */
    public function test_workshop_core_calendar_provide_event_action_open_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $now = time();
        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => $now - DAYSECS, 'submissionend' => $now + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory, $student->id);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event provide action closed.
     */
    public function test_workshop_core_calendar_provide_event_action_closed() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course->id,
            'submissionend' => time() - DAYSECS));
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event provide action closed for a non user.
     */
    public function test_workshop_core_calendar_provide_event_action_closed_for_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course->id,
            'submissionend' => time() - DAYSECS));
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    /**
     * Test calendar event provide action closed when user id is provided.
     */
    public function test_workshop_core_calendar_provide_event_action_closed_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', array('course' => $course->id,
            'submissionend' => time() - DAYSECS));
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory, $student->id);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event action open in future.
     */
    public function test_workshop_core_calendar_provide_event_action_open_in_future() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => time() + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event action open in future for a non user.
     */
    public function test_workshop_core_calendar_provide_event_action_open_in_future_for_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => time() + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    /**
     * Test calendar event action open in future when user id is provided.
     */
    public function test_workshop_core_calendar_provide_event_action_open_in_future_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id,
            'submissionstart' => time() + DAYSECS]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Now log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory, $student->id);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event with no time specified.
     */
    public function test_workshop_core_calendar_provide_event_action_no_time_specified() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('viewworkshopsummary', 'workshop'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    /**
     * Test calendar event with no time specified for a non user.
     */
    public function test_workshop_core_calendar_provide_event_action_no_time_specified_for_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $workshop = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id]);
        $event = $this->create_action_event($course->id, $workshop->id, WORKSHOP_EVENT_TYPE_SUBMISSION_OPEN);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        $factory = new \core_calendar\action_factory();
        $actionevent = mod_workshop_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The workshop id.
     * @param string $eventtype The event type. eg. WORKSHOP_EVENT_TYPE_OPEN.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename = 'workshop';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    /**
     * Test check_updates_since callback.
     */
    public function test_check_updates_since() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        // Create user.
        $student = self::getDataGenerator()->create_user();
        $teacher = self::getDataGenerator()->create_user();

        // User enrolment.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id, 'manual');

        $this->setCurrentTimeStart();
        $record = array(
            'course' => $course->id,
            'custom' => 0,
            'feedback' => 1,
        );
        $workshop = $this->getDataGenerator()->create_module('workshop', $record);
        $cm = get_coursemodule_from_instance('workshop', $workshop->id, $course->id);
        $context = context_module::instance($cm->id);
        $cm = cm_info::create($cm);

        $this->setUser($student);
        // Check that upon creation, the updates are only about the new configuration created.
        $onehourago = time() - HOURSECS;
        $updates = workshop_check_updates_since($cm, $onehourago);
        foreach ($updates as $el => $val) {
            if ($el == 'configuration') {
                $this->assertTrue($val->updated);
                $this->assertTimeCurrent($val->timeupdated);
            } else {
                $this->assertFalse($val->updated);
            }
        }

        // Set up a generator to create content.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');
        // Submission.
        $submissionid = $generator->create_submission($workshop->id, $student->id, array(
            'title' => 'My custom title',
        ));
        // Now assessment.
        $assessmentid = $generator->create_assessment($submissionid, $student->id, array(
            'weight' => 3,
            'grade' => 95.00000,
        ));
        // Add files to one editor file area.
        $fs = get_file_storage();
        $filerecordinline = array(
            'contextid' => $context->id,
            'component' => 'mod_workshop',
            'filearea'  => 'instructauthors',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'image.png',
        );
        $instructauthorsfile = $fs->create_file_from_string($filerecordinline, 'image contents (not really)');

        $updates = workshop_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->submissions->updated);
        $this->assertCount(1, $updates->submissions->itemids);
        $this->assertEquals($submissionid, $updates->submissions->itemids[0]);
        $this->assertTrue($updates->assessments->updated);
        $this->assertCount(1, $updates->assessments->itemids);
        $this->assertEquals($assessmentid, $updates->assessments->itemids[0]);
        $this->assertTrue($updates->instructauthorsfiles->updated);
        $this->assertCount(1, $updates->instructauthorsfiles->itemids);
        $this->assertEquals($instructauthorsfile->get_id(), $updates->instructauthorsfiles->itemids[0]);

        // Check I see the user updates as teacher.
        $this->setUser($teacher);
        $updates = workshop_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->usersubmissions->updated);
        $this->assertCount(1, $updates->usersubmissions->itemids);
        $this->assertEquals($submissionid, $updates->usersubmissions->itemids[0]);
        $this->assertTrue($updates->userassessments->updated);
        $this->assertCount(1, $updates->userassessments->itemids);
        $this->assertEquals($assessmentid, $updates->userassessments->itemids[0]);
        $this->assertTrue($updates->instructauthorsfiles->updated);
        $this->assertCount(1, $updates->instructauthorsfiles->itemids);
        $this->assertEquals($instructauthorsfile->get_id(), $updates->instructauthorsfiles->itemids[0]);

        // The teacher didn't do anything.
        $this->assertFalse($updates->submissions->updated);
        $this->assertFalse($updates->assessments->updated);
    }
}
