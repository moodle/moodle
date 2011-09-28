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
 * Unit tests for Moodle language manipulation library defined in mlanglib.php
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB, $CFG;

if (empty($CFG->unittestprefix)) {
    die('You must define $CFG->unittestprefix to run these unit tests.');
}

require_once($CFG->dirroot . '/grade/grading/lib.php'); // Include the code to test

/**
 * Makes protected method accessible for testing purposes
 */
class testable_grading_manager extends grading_manager {
}

/**
 * Test cases for the grading manager API
 */
class grading_manager_test extends UnitTestCase {

    public function setUp() {
    }

    public function tearDown() {
    }

    public function test_basic_instantiation() {

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
}
