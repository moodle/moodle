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
 * Unit tests for (some of) mod/workshop/lib.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Make sure the code being tested is accessible.
require_once($CFG->dirroot . '/mod/workshop/lib.php'); // Include the code to test

/**
 * Test cases for the functions in lib.php
 */
class workshop_lib_test extends UnitTestCase {

    function test_workshop_get_maxgrades() {
        $this->assertIsA(workshop_get_maxgrades(), 'Array');
        $this->assertTrue(workshop_get_maxgrades());
        $values_are_integers = True;
        foreach(workshop_get_maxgrades() as $key => $val) {
            if (!is_int($val)) {
                $values_are_integers = false;
                break;
            }
        }
        $this->assertTrue($values_are_integers, 'Array values must be integers');
    }

}
