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
 * Unit tests for (some of) mod/feedback/lib.php.
 *
 * @package    mod_feedback
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/feedback/lib.php');

/**
 * Unit tests for (some of) mod/feedback/lib.php.
 *
 * @copyright  2016 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_lib_testcase extends advanced_testcase {

    /**
     * Tests for mod_feedback_refresh_events.
     */
    public function test_feedback_refresh_events() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $timeopen = time();
        $timeclose = time() + 86400;

        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_feedback');
        $params['course'] = $course->id;
        $params['timeopen'] = $timeopen;
        $params['timeclose'] = $timeclose;
        $feedback = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id);
        $context = context_module::instance($cm->id);

        // Normal case, with existing course.
        $this->assertTrue(feedback_refresh_events($course->id));
        $eventparams = array('modulename' => 'feedback', 'instance' => $feedback->id, 'eventtype' => 'open');
        $openevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($openevent->timestart, $timeopen);

        $eventparams = array('modulename' => 'feedback', 'instance' => $feedback->id, 'eventtype' => 'close');
        $closeevent = $DB->get_record('event', $eventparams, '*', MUST_EXIST);
        $this->assertEquals($closeevent->timestart, $timeclose);
        // In case the course ID is passed as a numeric string.
        $this->assertTrue(feedback_refresh_events('' . $course->id));
        // Course ID not provided.
        $this->assertTrue(feedback_refresh_events());
        $eventparams = array('modulename' => 'feedback');
        $events = $DB->get_records('event', $eventparams);
        foreach ($events as $event) {
            if ($event->modulename === 'feedback' && $event->instance === $feedback->id && $event->eventtype === 'open') {
                $this->assertEquals($event->timestart, $timeopen);
            }
            if ($event->modulename === 'feedback' && $event->instance === $feedback->id && $event->eventtype === 'close') {
                $this->assertEquals($event->timestart, $timeclose);
            }
        }
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

        // User enrolment.
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id, 'manual');

        $this->setCurrentTimeStart();
        $record = array(
            'course' => $course->id,
            'custom' => 0,
            'feedback' => 1,
        );
        $feedback = $this->getDataGenerator()->create_module('feedback', $record);
        $cm = get_coursemodule_from_instance('feedback', $feedback->id, $course->id);
        $cm = cm_info::create($cm);

        $this->setUser($student);
        // Check that upon creation, the updates are only about the new configuration created.
        $onehourago = time() - HOURSECS;
        $updates = feedback_check_updates_since($cm, $onehourago);
        foreach ($updates as $el => $val) {
            if ($el == 'configuration') {
                $this->assertTrue($val->updated);
                $this->assertTimeCurrent($val->timeupdated);
            } else {
                $this->assertFalse($val->updated);
            }
        }

        $record = [
            'feedback' => $feedback->id,
            'userid' => $student->id,
            'timemodified' => time(),
            'random_response' => 0,
            'anonymous_response' => FEEDBACK_ANONYMOUS_NO,
            'courseid' => $course->id,
        ];
        $DB->insert_record('feedback_completed', (object) $record);
        $DB->insert_record('feedback_completedtmp', (object) $record);

        // Check now for finished and unfinished attempts.
        $updates = feedback_check_updates_since($cm, $onehourago);
        $this->assertTrue($updates->attemptsunfinished->updated);
        $this->assertCount(1, $updates->attemptsunfinished->itemids);

        $this->assertTrue($updates->attemptsfinished->updated);
        $this->assertCount(1, $updates->attemptsfinished->itemids);
    }
}
