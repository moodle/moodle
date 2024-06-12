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

namespace core_grading;

use grading_manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/grading/lib.php'); // Include the code to test

/**
 * Test cases for the grading manager API
 *
 * @package    core_grading
 * @category   test
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grading_manager_test extends \advanced_testcase {
    public function test_basic_instantiation(): void {
        $manager1 = get_grading_manager();

        $fakecontext = (object)array(
            'id'            => 42,
            'contextlevel'  => CONTEXT_MODULE,
            'instanceid'    => 22,
            'path'          => '/1/3/15/42',
            'depth'         => 4);

        $manager2 = get_grading_manager($fakecontext);
        $manager3 = get_grading_manager($fakecontext, 'assignment_upload');
        $manager4 = get_grading_manager($fakecontext, 'assignment_upload', 'submission');
    }

    /**
     * Unit test to set and get grading areas
     */
    public function test_set_and_get_grading_area(): void {
        global $DB;

        $this->resetAfterTest(true);

        //sleep(2); // to make sure the microtime will always return unique values // No sleeping in tests!!! --skodak
        $areaname1 = 'area1-' . (string)microtime(true);
        $areaname2 = 'area2-' . (string)microtime(true);
        $fakecontext = (object)array(
            'id'            => 42,
            'contextlevel'  => CONTEXT_MODULE,
            'instanceid'    => 22,
            'path'          => '/1/3/15/42',
            'depth'         => 4);

        // non-existing area
        $gradingman = get_grading_manager($fakecontext, 'mod_foobar', $areaname1);
        $this->assertNull($gradingman->get_active_method());

        // creates area implicitly and sets active method
        $this->assertTrue($gradingman->set_active_method('rubric'));
        $this->assertEquals('rubric', $gradingman->get_active_method());

        // repeat setting of already set active method
        $this->assertFalse($gradingman->set_active_method('rubric'));

        // switch the manager to another area
        $gradingman->set_area($areaname2);
        $this->assertNull($gradingman->get_active_method());

        // switch back and ask again
        $gradingman->set_area($areaname1);
        $this->assertEquals('rubric', $gradingman->get_active_method());

        // attempting to set an invalid method
        $this->expectException(\moodle_exception::class);
        $gradingman->set_active_method('no_one_should_ever_try_to_implement_a_method_with_this_silly_name');
    }

    /**
     * Unit test to check the tokenize method
     */
    public function test_tokenize(): void {

        $UTFfailuremessage = 'A test using UTF-8 characters has failed. Consider updating PHP and PHP\'s PCRE or INTL extensions (MDL-30494)';

        $needle = "    šašek, \n\n   \r    a král;  \t";
        $tokens = grading_manager::tokenize($needle);
        $this->assertEquals(2, count($tokens), $UTFfailuremessage);
        $this->assertTrue(in_array('šašek', $tokens), $UTFfailuremessage);
        $this->assertTrue(in_array('král', $tokens), $UTFfailuremessage);

        $needle = ' "   šašek a král "    ';
        $tokens = grading_manager::tokenize($needle);
        $this->assertEquals(1, count($tokens));
        $this->assertTrue(in_array('šašek a král', $tokens));

        $needle = '""';
        $tokens = grading_manager::tokenize($needle);
        $this->assertTrue(empty($tokens));

        $needle = '"0"';
        $tokens = grading_manager::tokenize($needle);
        $this->assertEquals(1, count($tokens));
        $this->assertTrue(in_array('0', $tokens));

        $needle = '<span>Aha</span>, then who\'s a bad guy here he?';
        $tokens = grading_manager::tokenize($needle);
        $this->assertEquals(8, count($tokens));
        $this->assertTrue(in_array('span', $tokens)); // Extracted the tag name
        $this->assertTrue(in_array('Aha', $tokens));
        $this->assertTrue(in_array('who', $tokens)); // Removed the trailing 's
        $this->assertTrue(!in_array('a', $tokens)); //Single letter token was dropped
        $this->assertTrue(in_array('he', $tokens)); // Removed the trailing ?

        $needle = 'grammar, "english language"';
        $tokens = grading_manager::tokenize($needle);
        $this->assertTrue(in_array('grammar', $tokens));
        $this->assertTrue(in_array('english', $tokens));
        $this->assertTrue(in_array('language', $tokens));
        $this->assertTrue(!in_array('english language', $tokens)); // Quoting part of the string is not supported
    }
}
