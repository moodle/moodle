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

namespace mod_scorm\event;

defined('MOODLE_INTERNAL') || die();

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
class events_test extends \advanced_testcase {

    /** @var stdClass store course object */
    protected $eventcourse;

    /** @var stdClass store user object */
    protected $eventuser;

    /** @var stdClass store scorm object */
    protected $eventscorm;

    /** @var stdClass store course module object */
    protected $eventcm;

    protected function setUp(): void {
        $this->setAdminUser();
        $this->eventcourse = $this->getDataGenerator()->create_course();
        $this->eventuser = $this->getDataGenerator()->create_user();
        $record = new \stdClass();
        $record->course = $this->eventcourse->id;
        $this->eventscorm = $this->getDataGenerator()->create_module('scorm', $record);
        $this->eventcm = get_coursemodule_from_instance('scorm', $this->eventscorm->id);
    }

    /**
     * Tests for attempt deleted event
     */
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
        $this->assertCount(3, $events);
        $this->assertInstanceOf('\mod_scorm\event\attempt_deleted', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals(\context_module::instance($this->eventcm->id), $event->get_context());
        $this->assertEquals(4, $event->other['attemptid']);
        $this->assertEquals(2, $event->relateduserid);
        $expected = array($this->eventcourse->id, 'scorm', 'delete attempts', 'report.php?id=' . $this->eventcm->id,
                4, $this->eventcm->id);
        $this->assertEventLegacyLogData($expected, $events[0]);
        $this->assertEventContextNotUsed($event);

        // Test event validations.
        $this->expectException(\coding_exception::class);
        \mod_scorm\event\attempt_deleted::create(array(
            'contextid' => 5,
            'relateduserid' => 2
        ));
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
            'context' => \context_module::instance($this->eventcm->id),
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
            'context' => \context_course::instance($this->eventcourse->id),
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
            'context' => \context_module::instance($this->eventcm->id),
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
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\interactions_viewed to be triggered without
                    other['instanceid']");
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\interactions_viewed::create(array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\interactions_viewed to be triggered without
                    other['attemptid']");
        } catch (\Exception $e) {
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
             'context' => \context_module::instance($this->eventcm->id),
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
             'context' => \context_module::instance($this->eventcm->id),
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
        $this->expectException(\coding_exception::class);
        \mod_scorm\event\sco_launched::create(array(
             'objectid' => $this->eventscorm->id,
             'context' => \context_module::instance($this->eventcm->id),
             'courseid' => $this->eventcourse->id,
        ));
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
            'context' => \context_module::instance($this->eventcm->id),
            'courseid' => $this->eventcourse->id,
            'other' => array('attemptid' => 2, 'instanceid' => $this->eventscorm->id, 'scoid' => 3, 'mode' => 'interactions')
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        // Check that the legacy log data is valid.
        $expected = array($this->eventcourse->id, 'scorm', 'userreporttracks', 'report/userreporttracks.php?id=' .
                $this->eventcm->id . '&user=5&attempt=' . 2 . '&scoid=3' . '&mode=interactions',
                $this->eventscorm->id, $this->eventcm->id);
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
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2, 'scoid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['instanceid']");
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\tracks_viewed::create(array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2, 'scoid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['attemptid']");
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }

        try {
            \mod_scorm\event\tracks_viewed::create(array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2, 'instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\tracks_viewed to be triggered without
                    other['scoid']");
        } catch (\Exception $e) {
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
            'context' => \context_module::instance($this->eventcm->id),
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
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\user_report_viewed to be triggered without
                    other['instanceid']");
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
        try {
            \mod_scorm\event\user_report_viewed::create(array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('instanceid' => 2)
            ));
            $this->fail("Event validation should not allow \\mod_scorm\\event\\user_report_viewed to be triggered without
                    other['attemptid']");
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
        }
    }

    /**
     * dataProvider for test_scoreraw_submitted_event().
     */
    public function get_scoreraw_submitted_event_provider() {
        return array(
            // SCORM 1.2.
            // - cmi.core.score.raw.
            'cmi.core.score.raw => 100' => array('cmi.core.score.raw', '100'),
            'cmi.core.score.raw => 90' => array('cmi.core.score.raw', '90'),
            'cmi.core.score.raw => 50' => array('cmi.core.score.raw', '50'),
            'cmi.core.score.raw => 10' => array('cmi.core.score.raw', '10'),
            // Check an edge case (PHP empty() vs isset()): score value equals to '0'.
            'cmi.core.score.raw => 0' => array('cmi.core.score.raw', '0'),
            // SCORM 1.3 AKA 2004.
            // - cmi.score.raw.
            'cmi.score.raw => 100' => array('cmi.score.raw', '100'),
            'cmi.score.raw => 90' => array('cmi.score.raw', '90'),
            'cmi.score.raw => 50' => array('cmi.score.raw', '50'),
            'cmi.score.raw => 10' => array('cmi.score.raw', '10'),
            // Check an edge case (PHP empty() vs isset()): score value equals to '0'.
            'cmi.score.raw => 0' => array('cmi.score.raw', '0'),
        );
    }

    /**
     * Tests for score submitted event.
     *
     * There is no api involved so the best we can do is test data by triggering event manually.
     *
     * @dataProvider get_scoreraw_submitted_event_provider
     *
     * @param string $cmielement a valid CMI raw score element
     * @param string $cmivalue a valid CMI raw score value
     */
    public function test_scoreraw_submitted_event($cmielement, $cmivalue) {
        $this->resetAfterTest();
        $event = \mod_scorm\event\scoreraw_submitted::create(array(
            'other' => array('attemptid' => '2', 'cmielement' => $cmielement, 'cmivalue' => $cmivalue),
            'objectid' => $this->eventscorm->id,
            'context' => \context_module::instance($this->eventcm->id),
            'relateduserid' => $this->eventuser->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);
        $this->assertEquals(2, $event->other['attemptid']);
        $this->assertEquals($cmielement, $event->other['cmielement']);
        $this->assertEquals($cmivalue, $event->other['cmivalue']);

        // Check that no legacy log data is provided.
        $this->assertEventLegacyLogData(null, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * dataProvider for test_scoreraw_submitted_event_validations().
     */
    public function get_scoreraw_submitted_event_validations() {
        return array(
            'scoreraw_submitted => missing cmielement' => array(
                null, '50',
                "Event validation should not allow \\mod_scorm\\event\\scoreraw_submitted " .
                    "to be triggered without other['cmielement']",
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmielement' must be set in other."
            ),
            'scoreraw_submitted => missing cmivalue' => array(
                'cmi.core.score.raw', null,
                "Event validation should not allow \\mod_scorm\\event\\scoreraw_submitted " .
                    "to be triggered without other['cmivalue']",
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmivalue' must be set in other."
            ),
            'scoreraw_submitted => wrong CMI element' => array(
                'cmi.core.lesson_status', '50',
                "Event validation should not allow \\mod_scorm\\event\\scoreraw_submitted " .
                    'to be triggered with a CMI element not representing a raw score',
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmielement' must represents a valid CMI raw score (cmi.core.lesson_status)."
            ),
        );
    }

    /**
     * Tests for score submitted event validations.
     *
     * @dataProvider get_scoreraw_submitted_event_validations
     *
     * @param string $cmielement a valid CMI raw score element
     * @param string $cmivalue a valid CMI raw score value
     * @param string $failmessage the message used to fail the test in case of missing to violate a validation rule
     * @param string $excmessage the exception message when violating the validations rules
     */
    public function test_scoreraw_submitted_event_validations($cmielement, $cmivalue, $failmessage, $excmessage) {
        $this->resetAfterTest();
        try {
            $data = array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            );
            if ($cmielement != null) {
                $data['other']['cmielement'] = $cmielement;
            }
            if ($cmivalue != null) {
                $data['other']['cmivalue'] = $cmivalue;
            }
            \mod_scorm\event\scoreraw_submitted::create($data);
            $this->fail($failmessage);
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
            $this->assertEquals($excmessage, $e->getMessage());
        }
    }

    /**
     * dataProvider for test_status_submitted_event().
     */
    public function get_status_submitted_event_provider() {
        return array(
            // SCORM 1.2.
            // 1. Status: cmi.core.lesson_status.
            'cmi.core.lesson_status => passed' => array('cmi.core.lesson_status', 'passed'),
            'cmi.core.lesson_status => completed' => array('cmi.core.lesson_status', 'completed'),
            'cmi.core.lesson_status => failed' => array('cmi.core.lesson_status', 'failed'),
            'cmi.core.lesson_status => incomplete' => array('cmi.core.lesson_status', 'incomplete'),
            'cmi.core.lesson_status => browsed' => array('cmi.core.lesson_status', 'browsed'),
            'cmi.core.lesson_status => not attempted' => array('cmi.core.lesson_status', 'not attempted'),
            // SCORM 1.3 AKA 2004.
            // 1. Completion status: cmi.completion_status.
            'cmi.completion_status => completed' => array('cmi.completion_status', 'completed'),
            'cmi.completion_status => incomplete' => array('cmi.completion_status', 'incomplete'),
            'cmi.completion_status => not attempted' => array('cmi.completion_status', 'not attempted'),
            'cmi.completion_status => unknown' => array('cmi.completion_status', 'unknown'),
            // 2. Success status: cmi.success_status.
            'cmi.success_status => passed' => array('cmi.success_status', 'passed'),
            'cmi.success_status => failed' => array('cmi.success_status', 'failed'),
            'cmi.success_status => unknown' => array('cmi.success_status', 'unknown')
        );
    }

    /**
     * Tests for status submitted event.
     *
     * There is no api involved so the best we can do is test data by triggering event manually.
     *
     * @dataProvider get_status_submitted_event_provider
     *
     * @param string $cmielement a valid CMI status element
     * @param string $cmivalue a valid CMI status value
     */
    public function test_status_submitted_event($cmielement, $cmivalue) {
        $this->resetAfterTest();
        $event = \mod_scorm\event\status_submitted::create(array(
            'other' => array('attemptid' => '2', 'cmielement' => $cmielement, 'cmivalue' => $cmivalue),
            'objectid' => $this->eventscorm->id,
            'context' => \context_module::instance($this->eventcm->id),
            'relateduserid' => $this->eventuser->id
        ));

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $sink->close();
        $event = reset($events);
        $this->assertEquals(2, $event->other['attemptid']);
        $this->assertEquals($cmielement, $event->other['cmielement']);
        $this->assertEquals($cmivalue, $event->other['cmivalue']);

        // Check that no legacy log data is provided.
        $this->assertEventLegacyLogData(null, $event);
        $this->assertEventContextNotUsed($event);
    }

    /**
     * dataProvider for test_status_submitted_event_validations().
     */
    public function get_status_submitted_event_validations() {
        return array(
            'status_submitted => missing cmielement' => array(
                null, 'passed',
                "Event validation should not allow \\mod_scorm\\event\\status_submitted " .
                    "to be triggered without other['cmielement']",
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmielement' must be set in other."
            ),
            'status_submitted => missing cmivalue' => array(
                'cmi.core.lesson_status', null,
                "Event validation should not allow \\mod_scorm\\event\\status_submitted " .
                    "to be triggered without other['cmivalue']",
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmivalue' must be set in other."
            ),
            'status_submitted => wrong CMI element' => array(
                'cmi.core.score.raw', 'passed',
                "Event validation should not allow \\mod_scorm\\event\\status_submitted " .
                    'to be triggered with a CMI element not representing a valid CMI status element',
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmielement' must represents a valid CMI status element (cmi.core.score.raw)."
            ),
            'status_submitted => wrong CMI value' => array(
                'cmi.core.lesson_status', 'blahblahblah',
                "Event validation should not allow \\mod_scorm\\event\\status_submitted " .
                    'to be triggered with a CMI element not representing a valid CMI status',
                'Coding error detected, it must be fixed by a programmer: ' .
                    "The 'cmivalue' must represents a valid CMI status value (blahblahblah)."
            ),
        );
    }

    /**
     * Tests for status submitted event validations.
     *
     * @dataProvider get_status_submitted_event_validations
     *
     * @param string $cmielement a valid CMI status element
     * @param string $cmivalue a valid CMI status value
     * @param string $failmessage the message used to fail the test in case of missing to violate a validation rule
     * @param string $excmessage the exception message when violating the validations rules
     */
    public function test_status_submitted_event_validations($cmielement, $cmivalue, $failmessage, $excmessage) {
        $this->resetAfterTest();
        try {
            $data = array(
                'context' => \context_module::instance($this->eventcm->id),
                'courseid' => $this->eventcourse->id,
                'other' => array('attemptid' => 2)
            );
            if ($cmielement != null) {
                $data['other']['cmielement'] = $cmielement;
            }
            if ($cmivalue != null) {
                $data['other']['cmivalue'] = $cmivalue;
            }
            \mod_scorm\event\status_submitted::create($data);
            $this->fail($failmessage);
        } catch (\Exception $e) {
            $this->assertInstanceOf('coding_exception', $e);
            $this->assertEquals($excmessage, $e->getMessage());
        }
    }
}
