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
 * Unit tests for (some of) mod/imscp/lib.php.
 *
 * @package    mod_imscp
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/imscp/lib.php');

/**
 * Unit tests for (some of) mod/imscp/lib.php.
 *
 * @package    mod_imscp
 * @category   test
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_imscp_lib_testcase extends advanced_testcase {

    public function test_export_contents() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $this->setAdminUser();
        $imscp = $this->getDataGenerator()->create_module('imscp', array('course' => $course->id));
        $cm = get_coursemodule_from_id('imscp', $imscp->cmid);

        $this->setUser($user);
        $contents = imscp_export_contents($cm, '');

        // The test package contains 47 files.
        $this->assertCount(47, $contents);
        // The structure is present.
        $this->assertEquals('structure', $contents[0]['filename']);
        // The structure is returned and it maches the expected one.
        $this->assertEquals(json_encode(unserialize($imscp->structure)), $contents[0]['content']);

    }

    /**
     * Test imscp_view
     * @return void
     */
    public function test_imscp_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $imscp = $this->getDataGenerator()->create_module('imscp', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($imscp->cmid);
        $cm = get_coursemodule_from_instance('imscp', $imscp->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        imscp_view($imscp, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_imscp\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/imscp/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }
}
