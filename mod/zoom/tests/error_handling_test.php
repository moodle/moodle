<?php
// This file is part of the Zoom plugin for Moodle - http://moodle.org/
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
 * Unit tests for error handling for zoom exceptions.
 *
 * @package    mod_zoom
 * @copyright  2019 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/zoom/locallib.php');

/**
 * PHPunit testcase class.
 *
 * @copyright  2020 UC Regents
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class error_handling_test extends basic_testcase {

    /**
     * Exception for when the meeting isn't found on Zoom.
     * @var mod_zoom\zoom_not_found_exception
     */
    private $meetingnotfoundexception;

    /**
     * Exception for when the user isn't found on Zoom.
     * @var mod_zoom\zoom_not_found_exception
     */
    private $usernotfoundexception;

    /**
     * Exception for when the user is found in the system but they haven't
     * accepted their invite, so they don't have permissions to do what was
     * requested.
     * @var mod_zoom\zoom_not_found_exception
     */
    private $invaliduserexception;

    /**
     * Exception for when the meeting isn't found on Zoom.
     * @var mod_zoom\zoom_not_found_exception
     */
    private $othererrorcodeexception;

    /**
     * Setup before every test.
     */
    public function setUp(): void {
        $this->meetingnotfoundexception = new zoom_not_found_exception('meeting not found', 3001);
        $this->usernotfoundexception = new zoom_not_found_exception('user not found', 1001);
        $this->invaliduserexception = new zoom_not_found_exception('invalid user found', 1120);
        $this->othererrorcodeexception = new zoom_not_found_exception('other exception', -1);
    }

    /**
     * Tests that uuid are encoded properly for use in web service calls.
     */
    public function test_correct_error_recognition() {
        // Check meeting not found behavior.
        $this->assertTrue(zoom_is_meeting_gone_error($this->meetingnotfoundexception));
        $this->assertTrue(zoom_is_meeting_gone_error($this->usernotfoundexception));
        $this->assertTrue(zoom_is_meeting_gone_error($this->invaliduserexception));
        $this->assertFalse(zoom_is_meeting_gone_error($this->othererrorcodeexception));

        // Check user not found behavior.
        $this->assertTrue(zoom_is_user_not_found_error($this->usernotfoundexception));
        $this->assertTrue(zoom_is_user_not_found_error($this->invaliduserexception));
        $this->assertFalse(zoom_is_user_not_found_error($this->meetingnotfoundexception));
        $this->assertFalse(zoom_is_user_not_found_error($this->othererrorcodeexception));
    }
}
