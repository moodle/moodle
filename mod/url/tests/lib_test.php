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
        $this->assertTrue(url_appears_valid_url('http://www.exa-mple2.com'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/~nobody/index.html'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com#hmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/#hmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/žlutý koníček/lala.txt#hmmmm'));
        $this->assertTrue(url_appears_valid_url('http://www.example.com/index.php?xx=yy&zz=aa'));
        $this->assertTrue(url_appears_valid_url('https://user:password@www.example.com/žlutý koníček/lala.txt'));
        $this->assertTrue(url_appears_valid_url('ftp://user:password@www.example.com/žlutý koníček/lala.txt'));

        $this->assertFalse(url_appears_valid_url('http:example.com'));
        $this->assertFalse(url_appears_valid_url('http:/example.com'));
        $this->assertFalse(url_appears_valid_url('http://'));
        $this->assertFalse(url_appears_valid_url('http://www.exa mple.com'));
        $this->assertFalse(url_appears_valid_url('http://www.examplé.com'));
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
}