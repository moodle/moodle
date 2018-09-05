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
 * Restore date tests.
 *
 * @package    mod_feedback
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/phpunit/classes/restore_date_testcase.php");

/**
 * Restore date tests.
 *
 * @package    mod_feedback
 * @copyright  2017 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_restore_date_testcase extends restore_date_testcase {

    public function test_restore_dates() {
        global $DB, $USER;

        $time = 10000;
        list($course, $feedback) = $this->create_course_and_module('feedback', ['timeopen' => $time, 'timeclose' => $time]);

        // Create response.
        $response = new stdClass();
        $response->feedback = $feedback->id;
        $response->userid = $USER->id;
        $response->anonymous_response = FEEDBACK_ANONYMOUS_NO;
        $response->timemodified = $time;
        $completedid = $DB->insert_record('feedback_completed', $response);
        $response = $DB->get_record('feedback_completed', array('id' => $completedid), '*', MUST_EXIST);

        // Do backup and restore.
        $newcourseid = $this->backup_and_restore($course);
        $newfeedback = $DB->get_record('feedback', ['course' => $newcourseid]);
        $newresponse = $DB->get_record('feedback_completed', ['feedback' => $newfeedback->id]);

        $this->assertFieldsNotRolledForward($feedback, $newfeedback, ['timemodified']);
        $props = ['timeopen', 'timeclose'];
        $this->assertFieldsRolledForward($feedback, $newfeedback, $props);
        $this->assertEquals($response->timemodified, $newresponse->timemodified);
    }
}
