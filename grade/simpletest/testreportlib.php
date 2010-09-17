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
 * Unit tests for grade/report/lib.php.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/grade/report/lib.php');

/**
 * @TODO create a set of mock objects to simulate the database operations. We don't want to connect to any real sql server.
 */
class gradereportlib_test extends UnitTestCaseUsingDatabase {
    var $courseid = 1;
    var $context = null;
    var $report = null;
    public  static $includecoverage = array('grade/report/lib.php');

    function setUp() {
        //$this->report = new grade_report($this->courseid, $this->context);
    }

}


