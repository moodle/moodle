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
 * This file contains tests for scorm events.
 *
 * @package    mod_scorm
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot . '/mod/scorm/locallib.php');
require_once($CFG->dirroot . '/mod/scorm/lib.php');

/**
 * Test class for various events related to Scorm.
 *
 * @package    mod_scorm
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_scorm_event_testcase extends advanced_testcase {

    /** @var stdClass store course object */
    protected $eventcourse;

    /** @var stdClass store user object */
    protected $eventuser;

    /** @var stdClass store scorm object */
    protected $eventscorm;

    /** @var stdClass store course module object */
    protected $eventcm;

    protected function setUp() {
        $this->setAdminUser();
        $this->eventcourse = $this->getDataGenerator()->create_course();
        $this->eventuser = $this->getDataGenerator()->create_user();
        $record = new stdClass();
        $record->course = $this->eventcourse->id;
        $this->eventscorm = $this->getDataGenerator()->create_module('scorm', $record);
        $this->eventcm = get_coursemodule_from_instance('scorm', $this->eventscorm->id);
    }

    /** Tests for attempt deleted event */
    public function test_attempt_deleted_event() {

        global $USER;

        $this->resetAfterTest();
        scorm_insert_track(2, $this->eventscorm->id, 1, 4, 'cmi.core.score.raw', 10);
        $sink = $this->redirectEvents();
        scorm_delete_attempt(2, $this->eventscorm, 4);
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);

        // Verify data.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_scorm\event\attempt_deleted', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals(context_module::instance($this->eventcm->id), $event->get_context());
        $this->assertEquals(4, $event->other['attemptid']);
        $this->assertEquals(2, $event->relateduserid);
        $expected = array($this->eventcourse->id, 'scorm', 'delete attempts', 'report.php?id=' . $this->eventcm->id,
                4, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $events[0]);
        $this->assertEventContextNotUsed($event);

        // Test event validations.
        $this->setExpectedException('coding_exception');
        \mod_scorm\event\attempt_deleted::create(array(
            'contextid' => 5,
            'relateduserid' => 2
        ));
        $this->fail('event \\mod_scorm\\event\\attempt_deleted is not validating events properly');
    }

    /**
     * Tests for course module viewed event.
     *
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_course_module_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\course_module_viewed::create(array(
            'objectid' => $this->eventscorm->id,
            'context' => context_module::instance($this->eventcm->id),
            'courseid' => $this->eventcourse->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'pre-view', 'view.php?id=' . $this->eventcm->id,
                $this->eventscorm->id, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for instance list viewed event.
     *
     * There is no api involved so the best we can do is test legacy data by triggering event manually.
     */
    public function test_course_module_instance_list_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\course_module_instance_list_viewed::create(array(
            'context' => context_course::instance($this->eventcourse->id),
            'courseid' => $this->eventcourse->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'view all', 'index.php?id=' . $this->eventcourse->id, '');
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for interactions viewed.
     *
     * There is no api involved so the best we can do is test legacy data by triggering event manually and test validations.
     */
    public function test_interactions_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\interactions_viewed::create(array(
            'relateduserid' => 5,
            'context' => context_module::instance($this->eventcm->id),
            'courseid' => $this->eventcourse->id,
            'other' => array('attemptid' => 2, 'instanceid' => $this->eventscorm->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'userreportinteractions', 'report/userreportinteractions.php?id=' .
                $this->eventcm->id . '&user=5&attempt=' . 2, $this->eventscorm->id, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for interactions viewed validations.
     */
    public function test_interactions_viewed_event_validations() {
        $this->resetAfterTest();
        try {
            \mod_scorm\event\interactions_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\interactions_viewed to be triggered without
                    other['instanceid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\interactions_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\interactions_viewed to be triggered without
                    other['attemptid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /** Tests for report viewed.
     *
     * There is no api involved so the best we can do is test legacy data and validations by triggering event manually.
     */
    public function test_report_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\report_viewed::create(array(
             'context' => context_module::instance($this->eventcm->id),
             'courseid' => $this->eventcourse->id,
             'other' => array(
                 'scormid' => $this->eventscorm->id,
                 'mode' => 'basic'
             )
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'report', 'report.php?id=' . $this->eventcm->id . '&mode=basic',
                $this->eventscorm->id, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /** Tests for sco launched event.
     *
     * There is no api involved so the best we can do is test legacy data and validations by triggering event manually.
     */
    public function test_sco_launched_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\sco_launched::create(array(
             'objectid' => 2,
             'context' => context_module::instance($this->eventcm->id),
             'courseid' => $this->eventcourse->id,
             'other' => array('loadedcontent' => 'url_to_content_that_was_laoded.php')
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'launch', 'view.php?id=' . $this->eventcm->id,
                          'url_to_content_that_was_laoded.php', $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);

        // Test validations.
        $this->setExpectedException('coding_exception');
        \mod_scorm\event\sco_launched::create(array(
             'objectid' => $this->eventscorm->id,
             'context' => context_module::instance($this->eventcm->id),
             'courseid' => $this->eventcourse->id,
        ));
        $this->fail('Event \\mod_scorm\\event\\sco_launched is not validating "loadedcontent" properly');
    }

    /**
     * Tests for tracks viewed event.
     *
     * There is no api involved so the best we can do is test validations by triggering event manually.
     */
    public function test_tracks_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\tracks_viewed::create(array(
            'relateduserid' => 5,
            'context' => context_module::instance($this->eventcm->id),
            'courseid' => $this->eventcourse->id,
            'other' => array('attemptid' => 2, 'instanceid' => $this->eventscorm->id, 'scoid' => 3)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'userreporttracks', 'report/userreporttracks.php?id=' .
                $this->eventcm->id . '&user=5&attempt=' . 2 . '&scoid=3', $this->eventscorm->id, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for tracks viewed event validations.
     */
    public function test_tracks_viewed_event_validations() {
        $this->resetAfterTest();
        try {
            \mod_scorm\event\tracks_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2, 'scoid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['instanceid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\tracks_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2, 'scoid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['attemptid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            \mod_scorm\event\tracks_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2, 'instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['scoid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * Tests for userreport viewed event.
     *
     * There is no api involved so the best we can do is test validations and legacy log by triggering event manually.
     */
    public function test_user_report_viewed_event() {
        $this->resetAfterTest();
        $event = \mod_scorm\event\user_report_viewed::create(array(
            'relateduserid' => 5,
            'context' => context_module::instance($this->eventcm->id),
            'courseid' => $this->eventcourse->id,
            'other' => array('attemptid' => 2, 'instanceid' => $this->eventscorm->id)
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'userreport', 'report/userreport.php?id=' .
                $this->eventcm->id . '&user=5&attempt=' . 2, $this->eventscorm->id, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Tests for userreport viewed event validations.
     */
    public function test_user_report_viewed_event_validations() {
        $this->resetAfterTest();
        try {
            \mod_scorm\event\user_report_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\user_report_viewed to be triggered without
                    other['instanceid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\user_report_viewed::create(array(
                'context' => context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\user_report_viewed to be triggered without
                    other['attemptid']");
        } catch (Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }
}

