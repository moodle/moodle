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
 * Unit tests for some mod URL lib stuff.
 *
 * @package    mod_url
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * mod_url tests
 *
 * @package    mod_url
 * @category   phpunit
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_url_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/url/lib.php');
        require_once($CFG->dirroot . '/mod/url/locallib.php');
    }

    /**
     * Tests the url_appears_valid_url function
     * @return void
     */
    public function test_url_appears_valid_url() {
        $this->assertTrue(url_appears_valid_url('http://example'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com'));
        $this->assertTrue(url_appears_valid_url('http://www.examplé.com'));
        $this->assertTrue(url_appears_valid_url('http://💩.la'));
        $this->assertTrue(url_appears_valid_url('http://香港大學.香港'));
        $this->assertTrue(url_appears_valid_url('http://وزارة-الأتصالات.مصر'));
        $this->assertTrue(url_appears_valid_url('http://www.теннис-алт.рф'));
        $this->assertTrue(url_appears_valid_url('http://имена.бг'));
        $this->assertTrue(url_appears_valid_url('http://straße.de'));
        $this->assertTrue(url_appears_valid_url('http://キース.コム'));
        $this->assertTrue(url_appears_valid_url('http://太亞.中国'));
        $this->assertTrue(url_appears_valid_url('http://www.რეგისტრაცია.გე'));
        $this->assertTrue(url_appears_valid_url('http://уміц.укр'));
        $this->assertTrue(url_appears_valid_url('http://현대.한국'));
        $this->assertTrue(url_appears_valid_url('http://мон.мон'));
        $this->assertTrue(url_appears_valid_url('http://тест.қаз'));
        $this->assertTrue(url_appears_valid_url('http://рнидс.срб'));
        $this->assertTrue(url_appears_valid_url('http://اسماء.شبكة'));
        $this->assertTrue(url_appears_valid_url('http://www.informationssäkerhet.se'));
        $this->assertTrue(url_appears_valid_url('http://москва.рф/services'));
        $this->assertTrue(url_appears_valid_url('http://detdumærker.dk'));
        $this->assertTrue(url_appears_valid_url('http://www.exa-mple2.com'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/~nobody/index.html'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com#hmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/#hmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt#hmmmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/index.php?xx=yy&zz=aa'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com:80/index.php?xx=yy&zz=aa'));
        $this->assertTrue(url_appears_valid_url('https://user:password@www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(url_appears_valid_url('ftp://user:password@www.example.com/žlutý koníček/lala.txt'));

        $this->assertFalse(url_appears_valid_url('http:example.com'));
        $this->assertFalse(url_appears_valid_url('http:/example.com'));
        $this->assertFalse(url_appears_valid_url('http://'));
        $this->assertFalse(url_appears_valid_url('http://www.exa mple.com'));
        $this->assertFalse(url_appears_valid_url('http://@www.example.com'));
        $this->assertFalse(url_appears_valid_url('http://user:@www.example.com'));

        $this->assertTrue(url_appears_valid_url('lalala://@:@/'));
    }

    /**
     * Test url_view
     * @return void
     */
    public function test_url_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $url = $this->getDataGenerator()->create_module('url', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($url->cmid);
        $cm = get_coursemodule_from_instance('url', $url->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        $this->setAdminUser();
        url_view($url, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_url\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/url/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    /**
     * Test mod_url_core_calendar_provide_event_action with user override
     */
    public function test_url_core_calendar_provide_event_action_user_override() {
        global $CFG, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();
        $user = $this->getDataGenerator()->create_user();
        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $url = $this->getDataGenerator()->create_module('url', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('url', $url->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $url->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_url_core_calendar_provide_event_action($event, $factory, $USER->id);

        // Decorate action with a userid override.
        $actionevent2 = mod_url_core_calendar_provide_event_action($event, $factory, $user->id);

        // Ensure result was null because it has been marked as completed for the associated user.
        // Logic was brought across from the "_already_completed" function.
        $this->assertNull($actionevent);

        // Confirm the event was decorated.
        $this->assertNotNull($actionevent2);
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent2);
        $this->assertEquals(get_string('view'), $actionevent2->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent2->get_url());
        $this->assertEquals(1, $actionevent2->get_item_count());
        $this->assertTrue($actionevent2->is_actionable());
    }

    public function test_url_core_calendar_provide_event_action() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $url = $this->getDataGenerator()->create_module('url', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $url->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_url_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_url_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $url = $this->getDataGenerator()->create_module('url', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('url', $url->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $url->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_url_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'url';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }
}