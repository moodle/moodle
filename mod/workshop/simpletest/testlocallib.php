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
 * Unit tests for workshop api class defined in mod/workshop/locallib.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test

/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_workshop extends workshop {

    public function __construct() {
        $this->cm       = new stdClass();
        $this->course   = new stdClass();
        $this->context  = new stdClass();
    }

    public function aggregate_submission_grades_process(array $assessments) {
        parent::aggregate_submission_grades_process($assessments);
    }
}

/**
 * Test cases for the internal workshop api
 */
class workshop_internal_api_test extends UnitTestCase {

    /** workshop instance emulation */
    protected $workshop;

    /** setup testing environment */
    public function setUp() {
        $this->workshop = new testable_workshop();
    }

    public function tearDown() {
        $this->workshop = null;
    }

    public function test_aggregate_submission_grades_process_empty_param() {

    }

    public function test_percent_to_value() {
        // fixture setup
        $total = 185;
        $percent = 56.6543;
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
        // verify
        $this->assertEqual($part, $total * $percent / 100);
    }

    public function test_percent_to_value_negative() {
        // fixture setup
        $total = 185;
        $percent = -7.098;
        // set expectation
        $this->expectException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

    public function test_percent_to_value_over_hundred() {
        // fixture setup
        $total = 185;
        $percent = 121.08;
        // set expectation
        $this->expectException('coding_exception');
        // exercise SUT
        $part = workshop::percent_to_value($percent, $total);
    }

}
