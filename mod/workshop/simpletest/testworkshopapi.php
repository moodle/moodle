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
 * Unit tests for workshop_api class defined in mod/workshop/locallib.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
 
// Make sure the code being tested is accessible.
require_once($CFG->dirroot . '/mod/workshop/locallib.php'); // Include the code to test
 

/**
 * Test subclass that makes all the protected methods we want to test public.
 * Also re-implements bridging methods so we can test more easily.
 */
class testable_workshop_api extends workshop_api {

}


/** 
 * Test cases for the internal workshop api
 */
class workshop_api_test extends UnitTestCase {

    /** workshop instance emulation */
    protected $workshop;

    /** setup testing environment */
    public function setUp() {
        $workshoprecord         = new stdClass;
        $workshoprecord->id     = 42;

        $cm                     = new stdClass;
        $this->workshop = new testable_workshop_api($workshoprecord, $cm);
    }

    public function tearDown() {
        $this->workshop = null;
    }


 
}
