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
 * This file contains tests for the question_engine class.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../lib.php');


/**
 *Unit tests for the question_engine class.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_test extends UnitTestCase {

    public function setUp() {

    }

    public function tearDown() {

    }

    public function test_load_behaviour_class() {
        // Exercise SUT
        question_engine::load_behaviour_class('deferredfeedback');
        // Verify
        $this->assertTrue(class_exists('qbehaviour_deferredfeedback'));
    }

    public function test_load_behaviour_class_missing() {
        // Set expectation.
        $this->expectException();
        // Exercise SUT
        question_engine::load_behaviour_class('nonexistantbehaviour');
    }

    public function test_get_behaviour_unused_display_options() {
        $this->assertEqual(array(), question_engine::get_behaviour_unused_display_options('interactive'));
        $this->assertEqual(array('correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'),
                question_engine::get_behaviour_unused_display_options('deferredfeedback'));
        $this->assertEqual(array('correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'),
                question_engine::get_behaviour_unused_display_options('deferredcbm'));
        $this->assertEqual(array('correctness', 'marks', 'specificfeedback', 'generalfeedback', 'rightanswer'),
                question_engine::get_behaviour_unused_display_options('manualgraded'));
    }

    public function test_sort_behaviours() {
        $in = array('b1' => 'Behave 1', 'b2' => 'Behave 2', 'b3' => 'Behave 3', 'b4' => 'Behave 4', 'b5' => 'Behave 5', 'b6' => 'Behave 6');

        $out = array('b1' => 'Behave 1', 'b2' => 'Behave 2', 'b3' => 'Behave 3', 'b4' => 'Behave 4', 'b5' => 'Behave 5', 'b6' => 'Behave 6');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, '', '', ''));

        $this->assertIdentical($out, question_engine::sort_behaviours($in, '', 'b4', 'b4'));

        $out = array('b4' => 'Behave 4', 'b5' => 'Behave 5', 'b6' => 'Behave 6');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, '', 'b1,b2,b3,b4', 'b4'));

        $out = array('b6' => 'Behave 6', 'b1' => 'Behave 1', 'b4' => 'Behave 4');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, 'b6,b1,b4', 'b2,b3,b4,b5', 'b4'));

        $out = array('b6' => 'Behave 6', 'b5' => 'Behave 5', 'b4' => 'Behave 4');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, 'b6,b5,b4', 'b1,b2,b3', 'b4'));

        $out = array('b6' => 'Behave 6', 'b5' => 'Behave 5', 'b4' => 'Behave 4');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, 'b1,b6,b5', 'b1,b2,b3,b4', 'b4'));

        $out = array('b2' => 'Behave 2', 'b4' => 'Behave 4', 'b6' => 'Behave 6');
        $this->assertIdentical($out, question_engine::sort_behaviours($in, 'b2,b4,b6', 'b1,b3,b5', 'b2'));

        // Ignore unknown input in the order argument.
        $this->assertIdentical($in, question_engine::sort_behaviours($in, 'unknown', '', ''));

        // Ignore unknown input in the disabled argument.
        $this->assertIdentical($in, question_engine::sort_behaviours($in, '', 'unknown', ''));
    }
}