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
 * Events tests.
 *
 * @package    mod_choice
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/choice/lib.php');

/**
 * Events tests class.
 *
 * @package    mod_choice
 * @copyright  2013 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_choice_events_testcase extends advanced_testcase {
    /** @var choice_object */
    protected $choice;

    /** @var course_object */
    protected $course;

    /** @var cm_object Course module object. */
    protected $cm;

    /** @var context_object */
    protected $context;

    /**
     * Setup often used objects for the following tests.
     */
    protected function setup() {
        global $DB;

        $this->resetAfterTest();

        $this->course = $this->getDataGenerator()->create_course();
        $this->choice = $this->getDataGenerator()->create_module('choice', array('course' => $this->course->id));
        $this->cm = $DB->get_record('course_modules', array('id' => $this->choice->cmid));
        $this->context = context_module::instance($this->choice->cmid);
    }

    /**
     * Test to ensure that event data is being stored correctly.
     */
    public function test_answer_submitted() {
        global $DB;
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();

        $optionids = array_keys($DB->get_records('choice_options', array('choiceid' => $this->choice->id)));
        // Redirect event.
        $sink = $this->redirectEvents();
        choice_user_submit_response($optionids[3], $this->choice, $user->id, $this->course, $this->cm);
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_choice\event\answer_submitted', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals(context_module::instance($this->choice->cmid), $events[0]->get_context());
        $this->assertEquals($this->choice->id, $events[0]->other['choiceid']);
        $this->assertEquals(array($optionids[3]), $events[0]->other['optionid']);
        $expected = array($this->course->id, "choice", "choose", 'view.php?id=' . $this->cm->id, $this->choice->id, $this->cm->id);
        $this->assertEventLegacyLogData($expected, $events[0]);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }

    /**
     * Test to ensure that multiple choice data is being stored correctly.
     */
    public function test_answer_submitted_multiple() {
        global $DB;

        // Generate user data.
        $user = $this->getDataGenerator()->create_user();

        // Create multiple choice.
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $this->course->id,
            'allowmultiple' => 1));
        $cm = $DB->get_record('course_modules', array('id' => $choice->cmid));
        $context = context_module::instance($choice->cmid);

        $optionids = array_keys($DB->get_records('choice_options', array('choiceid' => $choice->id)));
        $submittedoptionids = array($optionids[1], $optionids[3]);

        // Redirect event.
        $sink = $this->redirectEvents();
        choice_user_submit_response($submittedoptionids, $choice, $user->id, $this->course, $cm);
        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_choice\event\answer_submitted', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals(context_module::instance($choice->cmid), $events[0]->get_context());
        $this->assertEquals($choice->id, $events[0]->other['choiceid']);
        $this->assertEquals($submittedoptionids, $events[0]->other['optionid']);
        $expected = array($this->course->id, "choice", "choose", 'view.php?id=' . $cm->id, $choice->id, $cm->id);
        $this->assertEventLegacyLogData($expected, $events[0]);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }

    /**
     * Test custom validations.
     */
    public function test_answer_submitted_other_exception() {
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();

        $eventdata = array();
        $eventdata['context'] = $this->context;
        $eventdata['objectid'] = 2;
        $eventdata['userid'] = $user->id;
        $eventdata['courseid'] = $this->course->id;
        $eventdata['other'] = array();

        // Make sure content identifier is always set.
        $this->setExpectedException('coding_exception');
        $event = \mod_choice\event\answer_submitted::create($eventdata);
        $event->trigger();
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test to ensure that event data is being stored correctly.
     */
    public function test_answer_updated() {
        global $DB;
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();

        $optionids = array_keys($DB->get_records('choice_options', array('choiceid' => $this->choice->id)));

        // Create the first answer.
        choice_user_submit_response($optionids[2], $this->choice, $user->id, $this->course, $this->cm);

        // Redirect event.
        $sink = $this->redirectEvents();
        // Now choose a different answer.
        choice_user_submit_response($optionids[3], $this->choice, $user->id, $this->course, $this->cm);

        $events = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\mod_choice\event\answer_updated', $events[0]);
        $this->assertEquals($user->id, $events[0]->userid);
        $this->assertEquals(context_module::instance($this->choice->cmid), $events[0]->get_context());
        $this->assertEquals($this->choice->id, $events[0]->other['choiceid']);
        $this->assertEquals($optionids[3], $events[0]->other['optionid']);
        $expected = array($this->course->id, "choice", "choose again", 'view.php?id=' . $this->cm->id,
                $this->choice->id, $this->cm->id);
        $this->assertEventLegacyLogData($expected, $events[0]);
        $this->assertEventContextNotUsed($events[0]);
        $sink->close();
    }

    /**
     * Test custom validations
     * for answer_updated event.
     */
    public function test_answer_updated_other_exception() {
        // Generate user data.
        $user = $this->getDataGenerator()->create_user();

        $eventdata = array();
        $eventdata['context'] = $this->context;
        $eventdata['objectid'] = 2;
        $eventdata['userid'] = $user->id;
        $eventdata['courseid'] = $this->course->id;
        $eventdata['other'] = array();

        // Make sure content identifier is always set.
        $this->setExpectedException('coding_exception');
        $event = \mod_choice\event\answer_updated::create($eventdata);
        $event->trigger();
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test to ensure that event data is being stored correctly.
     */
    public function test_report_viewed() {
        global $USER;

        $this->resetAfterTest();

        // Generate user data.
        $this->setAdminUser();

        $eventdata = array();
        $eventdata['objectid'] = $this->choice->id;
        $eventdata['context'] = $this->context;
        $eventdata['courseid'] = $this->course->id;
        $eventdata['other']['content'] = 'choicereportcontentviewed';

        // This is fired in a page view so we can't run this through a function.
        $event = \mod_choice\event\report_viewed::create($eventdata);

        // Redirect event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $event = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $event);
        $this->assertInstanceOf('\mod_choice\event\report_viewed', $event[0]);
        $this->assertEquals($USER->id, $event[0]->userid);
        $this->assertEquals(context_module::instance($this->choice->cmid), $event[0]->get_context());
        $expected = array($this->course->id, "choice", "report", 'report.php?id=' . $this->context->instanceid,
                $this->choice->id, $this->context->instanceid);
        $this->assertEventLegacyLogData($expected, $event[0]);
        $this->assertEventContextNotUsed($event[0]);
        $sink->close();
    }

    /**
     * Test to ensure that event data is being stored correctly.
     */
    public function test_course_module_viewed() {
        global $USER;

        // Generate user data.
        $this->setAdminUser();

        $eventdata = array();
        $eventdata['objectid'] = $this->choice->id;
        $eventdata['context'] = $this->context;
        $eventdata['courseid'] = $this->course->id;
        $eventdata['other']['content'] = 'pageresourceview';

        // This is fired in a page view so we can't run this through a function.
        $event = \mod_choice\event\course_module_viewed::create($eventdata);

        // Redirect event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $event = $sink->get_events();

        // Data checking.
        $this->assertCount(1, $event);
        $this->assertInstanceOf('\mod_choice\event\course_module_viewed', $event[0]);
        $this->assertEquals($USER->id, $event[0]->userid);
        $this->assertEquals(context_module::instance($this->choice->cmid), $event[0]->get_context());
        $expected = array($this->course->id, "choice", "view", 'view.php?id=' . $this->context->instanceid,
                $this->choice->id, $this->context->instanceid);
        $this->assertEventLegacyLogData($expected, $event[0]);
        $this->assertEventContextNotUsed($event[0]);
        $sink->close();
    }

    /**
     * Test to ensure that event data is being stored correctly.
     */
    public function test_course_module_instance_list_viewed_viewed() {
        global $USER;

        // Not much can be tested here as the event is only triggered on a page load,
        // let's just check that the event contains the expected basic information.
        $this->setAdminUser();

        $params = array('context' => context_course::instance($this->course->id));
        $event = \mod_choice\event\course_module_instance_list_viewed::create($params);
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);
        $this->assertInstanceOf('\mod_choice\event\course_module_instance_list_viewed', $event);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals(context_course::instance($this->course->id), $event->get_context());
        $expected = array($this->course->id, 'choice', 'view all', 'index.php?id=' . $this->course->id, '');
        $this->assertEventLegacyLogData($expected, $event);
        $this->assertEventContextNotUsed($event);
    }
}
