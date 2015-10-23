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
 * Unit tests for mod_wiki lib
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <dani@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/wiki/lib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Unit tests for mod_wiki lib
 *
 * @package    mod_wiki
 * @category   external
 * @copyright  2015 Dani Palou <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class mod_wiki_lib_testcase extends advanced_testcase {

    /**
     * Test wiki_view.
     *
     * @return void
     */
    public function test_wiki_view() {
        global $CFG;

        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => COMPLETION_ENABLED));
        $options = array('completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => COMPLETION_VIEW_REQUIRED);
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id), $options);
        $context = context_module::instance($wiki->cmid);
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        wiki_view($wiki, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/wiki/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test wiki_page_view.
     *
     * @return void
     */
    public function test_wiki_page_view() {
        global $CFG;

        $CFG->enablecompletion = COMPLETION_ENABLED;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => COMPLETION_ENABLED));
        $options = array('completion' => COMPLETION_TRACKING_AUTOMATIC, 'completionview' => COMPLETION_VIEW_REQUIRED);
        $wiki = $this->getDataGenerator()->create_module('wiki', array('course' => $course->id), $options);
        $context = context_module::instance($wiki->cmid);
        $cm = get_coursemodule_from_instance('wiki', $wiki->id);
        $firstpage = $this->getDataGenerator()->get_plugin_generator('mod_wiki')->create_first_page($wiki);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        wiki_page_view($wiki, $firstpage, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_wiki\event\page_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $pageurl = new \moodle_url('/mod/wiki/view.php', array('pageid' => $firstpage->id));
        $this->assertEquals($pageurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }
}
