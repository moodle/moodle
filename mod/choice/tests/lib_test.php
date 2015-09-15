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

}
