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
 * Tests for deprecated conditional activities classes.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/conditionlib.php');


/**
 * Tests for deprecated conditional activities classes.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
class core_conditionlib_testcase extends advanced_testcase {

    protected function setUp() {
        global $CFG;
        parent::setUp();

        $this->resetAfterTest();

        $CFG->enableavailability = 1;
        $CFG->enablecompletion = 1;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
    }

    public function test_constructor() {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $page = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course));
        $modinfo = get_fast_modinfo($course);

        // No ID.
        try {
            $test = new condition_info((object)array());
            $this->fail();
        } catch (coding_exception $e) {
            // Do nothing.
            $this->assertDebuggingCalled();
        }

        // Get actual cm_info for comparison.
        $realcm = $modinfo->get_cm($page->cmid);

        // No other data.
        $test = new condition_info((object)array('id' => $page->cmid));
        $this->assertDebuggingCalled();
        $this->assertEquals($realcm, $test->get_full_course_module());
        $this->assertDebuggingCalled();

        // Course id.
        $test = new condition_info((object)array('id' => $page->cmid, 'course' => $course->id));
        $this->assertDebuggingCalled();
        $this->assertEquals($realcm, $test->get_full_course_module());
        $this->assertDebuggingCalled();

        // Full cm.
        $test = new condition_info($realcm);
        $this->assertDebuggingCalled();
        $this->assertEquals($realcm, $test->get_full_course_module());
        $this->assertDebuggingCalled();
    }

    /**
     * Same as above test but for course_sections instead of course_modules.
     */
    public function test_section_constructor() {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 1), array('createsections' => true));
        $modinfo = get_fast_modinfo($course);

        // No ID.
        try {
            $test = new condition_info_section(((object)array()));
            $this->fail();
        } catch (coding_exception $e) {
            // Do nothing.
            $this->assertDebuggingCalled();
        }

        // Get actual cm_info for comparison.
        $realsection = $modinfo->get_section_info(1);

        // No other data.
        $test = new condition_info_section((object)array('id' => $realsection->id));
        $this->assertDebuggingCalled();
        $this->assertEquals($realsection, $test->get_full_section());
        $this->assertDebuggingCalled();

        // Course id.
        $test = new condition_info_section((object)array('id' => $realsection->id,
                'course' => $course->id));
        $this->assertDebuggingCalled();
        $this->assertEquals($realsection, $test->get_full_section());
        $this->assertDebuggingCalled();

        // Full object.
        $test = new condition_info_section($realsection);
        $this->assertDebuggingCalled();
        $this->assertEquals($realsection, $test->get_full_section());
        $this->assertDebuggingCalled();
    }

    /**
     * Tests the is_available function for modules. This does not test all the
     * conditions and stuff, because it only needs to check that the system
     * connects through to the real availability API. Also tests
     * get_full_information function.
     */
    public function test_is_available() {
        // Create course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();

        // Create activity with no restrictions and one with date restriction.
        $page1 = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course));
        $time = time() + 100;
        $avail = '{"op":"|","show":true,"c":[{"type":"date","d":">=","t":' . $time . '}]}';
        $page2 = $generator->get_plugin_generator('mod_page')->create_instance(
                array('course' => $course, 'availability' => $avail));

        // No conditions.
        $ci = new condition_info((object)array('id' => $page1->cmid),
                CONDITION_MISSING_EVERYTHING);
        $this->assertDebuggingCalled();
        $this->assertTrue($ci->is_available($text, false, 0));
        $this->assertDebuggingCalled();
        $this->assertEquals('', $text);

        // Date condition.
        $ci = new condition_info((object)array('id' => $page2->cmid),
            CONDITION_MISSING_EVERYTHING);
        $this->assertDebuggingCalled();
        $this->assertFalse($ci->is_available($text));
        $this->assertDebuggingCalled();
        $expectedtime = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertContains($expectedtime, $text);

        // Full information display.
        $text = $ci->get_full_information();
        $this->assertDebuggingCalled();
        $expectedtime = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertContains($expectedtime, $text);
    }

    /**
     * Tests the is_available function for sections.
     */
    public function test_section_is_available() {
        global $DB;

        // Create course.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(
                array('numsections' => 2), array('createsections' => true));

        // Set one of the sections unavailable.
        $time = time() + 100;
        $avail = '{"op":"|","show":true,"c":[{"type":"date","d":">=","t":' . $time . '}]}';
        $DB->set_field('course_sections', 'availability', $avail, array(
                'course' => $course->id, 'section' => 2));

        $modinfo = get_fast_modinfo($course);

        // No conditions.
        $ci = new condition_info_section($modinfo->get_section_info(1));
        $this->assertDebuggingCalled();
        $this->assertTrue($ci->is_available($text, false, 0));
        $this->assertDebuggingCalled();
        $this->assertEquals('', $text);

        // Date condition.
        $ci = new condition_info_section($modinfo->get_section_info(2));
        $this->assertDebuggingCalled();
        $this->assertFalse($ci->is_available($text));
        $this->assertDebuggingCalled();
        $expectedtime = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertContains($expectedtime, $text);

        // Full information display.
        $text = $ci->get_full_information();
        $this->assertDebuggingCalled();
        $expectedtime = userdate($time, get_string('strftimedate', 'langconfig'));
        $this->assertContains($expectedtime, $text);
    }
}
