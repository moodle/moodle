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
 * External completion functions unit tests
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External completion functions unit tests
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_completion_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test update_activity_completion_status_manually
     */
    public function test_update_activity_completion_status_manually() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
                                                             array('completion' => 1));
        $cm = get_coursemodule_from_id('data', $data->cmid);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $this->setUser($user);

        $result = core_completion_external::update_activity_completion_status_manually($data->cmid, true);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::update_activity_completion_status_manually_returns(), $result);

        // Check in DB.
        $this->assertEquals(1, $DB->get_field('course_modules_completion', 'completionstate',
                            array('coursemoduleid' => $data->cmid)));

        // Check using the API.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
        $this->assertTrue($result['status']);

        $result = core_completion_external::update_activity_completion_status_manually($data->cmid, false);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::update_activity_completion_status_manually_returns(), $result);

        $this->assertEquals(0, $DB->get_field('course_modules_completion', 'completionstate',
                            array('coursemoduleid' => $data->cmid)));
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(0, $completiondata->completionstate);
        $this->assertTrue($result['status']);
    }
}
