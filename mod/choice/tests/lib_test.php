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
 * Choice module library functions tests
 *
 * @package    mod_choice
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/mod/choice/lib.php');

/**
 * Choice module library functions tests
 *
 * @package    mod_choice
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_choice_lib_testcase extends externallib_advanced_testcase {

    /**
     * Test choice_view
     * @return void
     */
    public function test_choice_view() {
        global $CFG;

        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        $context = context_module::instance($choice->cmid);
        $cm = get_coursemodule_from_instance('choice', $choice->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        choice_view($choice, $course, $cm, $context);

        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_choice\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $url = new \moodle_url('/mod/choice/view.php', array('id' => $cm->id));
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());
    }

    /**
     * Test choice_can_view_results
     * @return void
     */
    public function test_choice_can_view_results() {
        global $DB, $USER;

        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        $context = context_module::instance($choice->cmid);
        $cm = get_coursemodule_from_instance('choice', $choice->id);

        // Default values are false, user cannot view results.
        $canview = choice_can_view_results($choice);
        $this->assertFalse($canview);

        // Show results forced.
        $choice->showresults = CHOICE_SHOWRESULTS_ALWAYS;
        $DB->update_record('choice', $choice);
        $canview = choice_can_view_results($choice);
        $this->assertTrue($canview);

        // Show results after closing.
        $choice->showresults = CHOICE_SHOWRESULTS_AFTER_CLOSE;
        $DB->update_record('choice', $choice);
        $canview = choice_can_view_results($choice);
        $this->assertFalse($canview);

        $choice->timeclose = time() - HOURSECS;
        $DB->update_record('choice', $choice);
        $canview = choice_can_view_results($choice);
        $this->assertTrue($canview);

        // Show results after answering.
        $choice->timeclose = 0;
        $choice->showresults = CHOICE_SHOWRESULTS_AFTER_ANSWER;
        $DB->update_record('choice', $choice);
        $canview = choice_can_view_results($choice);
        $this->assertFalse($canview);

        // Get the first option.
        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[0], $choice, $USER->id, $course, $cm);

        $canview = choice_can_view_results($choice);
        $this->assertTrue($canview);

    }

    /**
     * @expectedException moodle_exception
     */
    public function test_choice_user_submit_response_validation() {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $choice1 = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        $choice2 = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('choice', $choice1->id);

        $choicewithoptions1 = choice_get_choice($choice1->id);
        $choicewithoptions2 = choice_get_choice($choice2->id);
        $optionids1 = array_keys($choicewithoptions1->option);
        $optionids2 = array_keys($choicewithoptions2->option);

        // Make sure we cannot submit options from a different choice instance.
        choice_user_submit_response($optionids2[0], $choice1, $USER->id, $course, $cm);
    }

    /**
     * Test choice_get_my_response
     * @return void
     */
    public function test_choice_get_my_response() {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));
        $context = context_module::instance($choice->cmid);
        $cm = get_coursemodule_from_instance('choice', $choice->id);

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids[0], $choice, $USER->id, $course, $cm);
        $responses = choice_get_my_response($choice, $course, $cm, $context);
        $this->assertCount(1, $responses);
        $response = array_shift($responses);
        $this->assertEquals($optionids[0], $response->optionid);

        // Multiple responses.
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id, 'allowmultiple' => 1));
        $context = context_module::instance($choice->cmid);
        $cm = get_coursemodule_from_instance('choice', $choice->id);

        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);

        choice_user_submit_response($optionids, $choice, $USER->id, $course, $cm);
        $responses = choice_get_my_response($choice, $course, $cm, $context);
        $this->assertCount(count($optionids), $responses);
        foreach ($responses as $resp) {
            $this->assertContains($resp->optionid, $optionids);
        }
    }

    /**
     * Test choice_get_availability_status
     * @return void
     */
    public function test_choice_get_availability_status() {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();
        $choice = $this->getDataGenerator()->create_module('choice', array('course' => $course->id));

        // No time restrictions and updates allowed.
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // No updates allowed, but haven't answered yet.
        $choice->allowupdate = false;
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // No updates allowed and have answered.
        $cm = get_coursemodule_from_instance('choice', $choice->id);
        $choicewithoptions = choice_get_choice($choice->id);
        $optionids = array_keys($choicewithoptions->option);
        choice_user_submit_response($optionids[0], $choice, $USER->id, $course, $cm);
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);
        $this->assertEquals('choicesaved', array_keys($warnings)[0]);

        $choice->allowupdate = true;

        // With time restrictions, still open.
        $choice->timeopen = time() - DAYSECS;
        $choice->timeclose = time() + DAYSECS;
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(true, $status);
        $this->assertCount(0, $warnings);

        // Choice not open yet.
        $choice->timeopen = time() + DAYSECS;
        $choice->timeclose = $choice->timeopen + DAYSECS;
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);
        $this->assertEquals('notopenyet', array_keys($warnings)[0]);

        // Choice closed.
        $choice->timeopen = time() - DAYSECS;
        $choice->timeclose = time() - 1;
        list($status, $warnings) = choice_get_availability_status($choice, false);
        $this->assertEquals(false, $status);
        $this->assertCount(1, $warnings);
        $this->assertEquals('expired', array_keys($warnings)[0]);

    }

}
