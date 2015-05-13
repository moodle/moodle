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
 * Unit tests for the date condition.
 *
 * @package availability_date
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use availability_date\condition;

/**
 * Unit tests for the date condition.
 *
 * @package availability_date
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_date_condition_testcase extends advanced_testcase {
    /**
     * Load required classes.
     */
    public function setUp() {
        // Load the mock info class so that it can be used.
        global $CFG;
        require_once($CFG->dirroot . '/availability/tests/fixtures/mock_info.php');
    }

    /**
     * Tests constructing and using date condition as part of tree.
     */
    public function test_in_tree() {
        global $SITE, $USER, $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Set server timezone for test. (Important as otherwise the timezone
        // could be anything - this is modified by other unit tests, too.)
        $this->setTimezone('UTC');

        // SEt user to GMT+5.
        $USER->timezone = 5;

        // Construct tree with date condition.
        $time = strtotime('2014-02-18 14:20:00 GMT');
        $structure = (object)array('op' => '|', 'show' => true, 'c' => array(
                (object)array('type' => 'date', 'd' => '>=', 't' => $time)));
        $tree = new \core_availability\tree($structure);
        $info = new \core_availability\mock_info();

        // Check if available (when not available).
        condition::set_current_time_for_test($time - 1);
        $information = '';
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertFalse($result->is_available());
        $information = $tree->get_result_information($info, $result);

        // Note: PM is normally upper-case, but an issue with PHP on Mac means
        // that on that platform, it is reported lower-case.
        $this->assertRegExp('~from.*18 February 2014, 7:20 (PM|pm)~', $information);

        // Check if available (when available).
        condition::set_current_time_for_test($time);
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertTrue($result->is_available());
        $information = $tree->get_result_information($info, $result);
        $this->assertEquals('', $information);
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function test_constructor() {
        // No parameters.
        $structure = (object)array();
        try {
            $date = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->d', $e->getMessage());
        }

        // Invalid ->d.
        $structure->d = 'woo hah!!';
        try {
            $date = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->d', $e->getMessage());
        }

        // Missing ->t.
        $structure->d = '>=';
        try {
            $date = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->t', $e->getMessage());
        }

        // Invalid ->t.
        $structure->t = 'got you all in check';
        try {
            $date = new condition($structure);
            $this->fail();
        } catch (coding_exception $e) {
            $this->assertContains('Missing or invalid ->t', $e->getMessage());
        }

        // Valid conditions of both types.
        $structure = (object)array('d' => '>=', 't' => strtotime('2014-02-18 14:43:17 GMT'));
        $date = new condition($structure);
        $this->assertEquals('{date:>= 2014-02-18 14:43:17}', (string)$date);
        $structure->d = '<';
        $date = new condition($structure);
        $this->assertEquals('{date:< 2014-02-18 14:43:17}', (string)$date);
    }

    /**
     * Tests the save() function.
     */
    public function test_save() {
        $structure = (object)array('d' => '>=', 't' => 12345);
        $cond = new condition($structure);
        $structure->type = 'date';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the is_available() and is_available_to_all() functions.
     */
    public function test_is_available() {
        global $SITE, $USER;

        $time = strtotime('2014-02-18 14:50:10 GMT');
        $info = new \core_availability\mock_info();

        // Test with >=.
        $date = new condition((object)array('d' => '>=', 't' => $time));
        condition::set_current_time_for_test($time - 1);
        $this->assertFalse($date->is_available(false, $info, true, $USER->id));
        condition::set_current_time_for_test($time);
        $this->assertTrue($date->is_available(false, $info, true, $USER->id));

        // Test with <.
        $date = new condition((object)array('d' => '<', 't' => $time));
        condition::set_current_time_for_test($time);
        $this->assertFalse($date->is_available(false, $info, true, $USER->id));
        condition::set_current_time_for_test($time - 1);
        $this->assertTrue($date->is_available(false, $info, true, $USER->id));

        // Repeat this test with is_available_to_all() - it should be the same.
        $date = new condition((object)array('d' => '<', 't' => $time));
        condition::set_current_time_for_test($time);
        $this->assertFalse($date->is_available_for_all(false));
        condition::set_current_time_for_test($time - 1);
        $this->assertTrue($date->is_available_for_all(false));
    }

    /**
     * Tests the get_description and get_standalone_description functions.
     */
    public function test_get_description() {
        global $SITE, $CFG;

        $this->resetAfterTest();
        $this->setTimezone('UTC');

        $modinfo = get_fast_modinfo($SITE);
        $info = new \core_availability\mock_info();
        $time = strtotime('2014-02-18 14:55:01 GMT');

        // Test with >=.
        $date = new condition((object)array('d' => '>=', 't' => $time));
        $information = $date->get_description(true, false, $info);
        $this->assertRegExp('~after.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_description(true, true, $info);
        $this->assertRegExp('~before.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_standalone_description(true, false, $info);
        $this->assertRegExp('~from.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_standalone_description(true, true, $info);
        $this->assertRegExp('~until.*18 February 2014, 2:55 (PM|pm)~', $information);

        // Test with <.
        $date = new condition((object)array('d' => '<', 't' => $time));
        $information = $date->get_description(true, false, $info);
        $this->assertRegExp('~before.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_description(true, true, $info);
        $this->assertRegExp('~after.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_standalone_description(true, false, $info);
        $this->assertRegExp('~until.*18 February 2014, 2:55 (PM|pm)~', $information);
        $information = $date->get_standalone_description(true, true, $info);
        $this->assertRegExp('~from.*18 February 2014, 2:55 (PM|pm)~', $information);

        // Test special case for dates that are midnight.
        $date = new condition((object)array('d' => '>=',
                't' => strtotime('2014-03-05 00:00 GMT')));
        $information = $date->get_description(true, false, $info);
        $this->assertRegExp('~on or after.*5 March 2014([^0-9]*)$~', $information);
        $information = $date->get_description(true, true, $info);
        $this->assertRegExp('~before.*end of.*4 March 2014([^0-9]*)$~', $information);
        $information = $date->get_standalone_description(true, false, $info);
        $this->assertRegExp('~from.*5 March 2014([^0-9]*)$~', $information);
        $information = $date->get_standalone_description(true, true, $info);
        $this->assertRegExp('~until end of.*4 March 2014([^0-9]*)$~', $information);

        // In the 'until' case for midnight, it shows the previous day. (I.e.
        // if the date is 5 March 00:00, then we show it as available until 4
        // March, implying 'the end of'.)
        $date = new condition((object)array('d' => '<',
                't' => strtotime('2014-03-05 00:00 GMT')));
        $information = $date->get_description(true, false, $info);
        $this->assertRegExp('~before end of.*4 March 2014([^0-9]*)$~', $information);
        $information = $date->get_description(true, true, $info);
        $this->assertRegExp('~on or after.*5 March 2014([^0-9]*)$~', $information);
        $information = $date->get_standalone_description(true, false, $info);
        $this->assertRegExp('~until end of.*4 March 2014([^0-9]*)$~', $information);
        $information = $date->get_standalone_description(true, true, $info);
        $this->assertRegExp('~from.*5 March 2014([^0-9]*)$~', $information);
    }
}
