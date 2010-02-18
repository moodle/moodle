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
 * Unit tests for (some of) ../outputlib.php.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}
require_once($CFG->libdir . '/outputlib.php');


/**
 * Unit tests for the xhtml_container_stack class.
 *
 * These tests assume that developer debug mode is on, which, at the time of
 * writing, is true. admin/report/unittest/index.php forces it on.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhtml_container_stack_test extends UnitTestCase {

    public static $includecoverage = array('lib/outputlib.php');
    protected function start_capture() {
        ob_start();
    }

    protected function end_capture() {
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

    public function test_push_then_pop() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $stack->push('testtype', '</div>');
        $html = $stack->pop('testtype');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</div>', $html);
        $this->assertEqual('', $errors);
    }

    public function test_mismatched_pop_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('testtype', '</div>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop('mismatch');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</div>', $html);
        $this->assertNotEqual('', $errors);
    }

    public function test_pop_when_empty_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop('testtype');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertNotEqual('', $errors);
    }

    public function test_correct_nesting() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $stack->push('testdiv', '</div>');
        $stack->push('testp', '</p>');
        $html2 = $stack->pop('testp');
        $html1 = $stack->pop('testdiv');
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</p>', $html2);
        $this->assertEqual('</div>', $html1);
        $this->assertEqual('', $errors);
    }

    public function test_pop_all_but_last() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        $stack->push('test2', '</h2>');
        $stack->push('test3', '</h3>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('</h3></h2>', $html);
        $this->assertEqual('', $errors);
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_only_one() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertEqual('', $errors);
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_empty() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $this->start_capture();
        $html = $stack->pop_all_but_last();
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $html);
        $this->assertEqual('', $errors);
    }

    public function test_discard() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</somethingdistinctive>');
        $stack->discard();
        // Exercise SUT.
        $this->start_capture();
        $stack = null;
        $errors = $this->end_capture();
        // Verify outcome
        $this->assertEqual('', $errors);
    }
}
