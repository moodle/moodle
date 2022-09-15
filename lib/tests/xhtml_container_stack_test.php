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

namespace core;

use xhtml_container_stack;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputlib.php');

/**
 * Unit tests for the xhtml_container_stack class.
 *
 * These tests assume that developer debug mode is on which is enforced by our phpunit integration.
 *
 * @package   core
 * @category  test
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xhtml_container_stack_test extends \advanced_testcase {
    public function test_push_then_pop() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $stack->push('testtype', '</div>');
        $html = $stack->pop('testtype');
        // Verify outcome.
        $this->assertEquals('</div>', $html);
        $this->assertDebuggingNotCalled();
    }

    public function test_mismatched_pop_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('testtype', '</div>');
        // Exercise SUT.
        $html = $stack->pop('mismatch');
        // Verify outcome.
        $this->assertEquals('</div>', $html);
        $this->assertDebuggingCalled();
    }

    public function test_pop_when_empty_prints_warning() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $html = $stack->pop('testtype');
        // Verify outcome.
        $this->assertEquals('', $html);
        $this->assertDebuggingCalled();
    }

    public function test_correct_nesting() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $stack->push('testdiv', '</div>');
        $stack->push('testp', '</p>');
        $html2 = $stack->pop('testp');
        $html1 = $stack->pop('testdiv');
        // Verify outcome.
        $this->assertEquals('</p>', $html2);
        $this->assertEquals('</div>', $html1);
        $this->assertDebuggingNotCalled();
    }

    public function test_pop_all_but_last() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        $stack->push('test2', '</h2>');
        $stack->push('test3', '</h3>');
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome.
        $this->assertEquals('</h3></h2>', $html);
        $this->assertDebuggingNotCalled();
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_only_one() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</h1>');
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome.
        $this->assertEquals('', $html);
        $this->assertDebuggingNotCalled();
        // Tear down.
        $stack->discard();
    }

    public function test_pop_all_but_last_empty() {
        // Set up.
        $stack = new xhtml_container_stack();
        // Exercise SUT.
        $html = $stack->pop_all_but_last();
        // Verify outcome.
        $this->assertEquals('', $html);
        $this->assertDebuggingNotCalled();
    }

    public function test_discard() {
        // Set up.
        $stack = new xhtml_container_stack();
        $stack->push('test1', '</somethingdistinctive>');
        $stack->discard();
        // Exercise SUT.
        $stack = null;
        // Verify outcome.
        $this->assertDebuggingNotCalled();
    }
}
