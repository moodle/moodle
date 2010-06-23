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
 * Unit tests for grade/edit/tree/lib.php.
 *
 * @author Andrew Davis
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/grade/edit/tree/lib.php');

/**
 * Tests grade_edit_tree (deals with the data on the categories and items page in the gradebook)
 */
class gradeedittreelib_test extends UnitTestCase {
    var $courseid = 1;
    var $context = null;
    var $grade_edit_tree = null;
    public  static $includecoverage = array('grade/edit/tree/lib.php');

    function setUp() {
    }

    public function test_format_number() {
        $numinput = array( 0,   1,   1.01, '1.010', 1.2345);
        $numoutput = array(0.0, 1.0, 1.01,  1.01,   1.2345);

        for ($i=0; $i<sizeof($numinput); $i++) {
            $msg = 'format_number() testing '.$numinput[$i].' %s';
            $this->assertEqual(grade_edit_tree::format_number($numinput[$i]),$numoutput[$i],$msg);
        }
    }

}


