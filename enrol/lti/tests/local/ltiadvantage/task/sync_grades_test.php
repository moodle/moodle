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

namespace enrol_lti\local\ltiadvantage\task;

use enrol_lti\helper;
use Packback\Lti1p3\LtiAssignmentsGradesService;
use Packback\Lti1p3\LtiGrade;
use Packback\Lti1p3\LtiLineitem;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lti_advantage_testcase.php');

/**
 * Tests for the enrol_lti\local\ltiadvantage\task\sync_grades scheduled task.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \enrol_lti\local\ltiadvantage\task\sync_grades
 */
class sync_grades_test extends \lti_advantage_testcase {

    /**
     * Test confirming task name.
     *
     * @covers ::get_name
     */
    public function test_get_name(): void {
        $this->assertEquals(get_string('tasksyncgrades', 'enrol_lti'), (new sync_grades())->get_name());
    }

    /**
     * Test grade sync when the resource has syncgrades disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_gradesync_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, true, true, helper::MEMBER_SYNC_ENROL_AND_UNENROL,
            false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = new \enrol_lti\local\ltiadvantage\task\sync_grades();
        $this->expectOutputRegex('/Skipping task - There are no resources with grade sync enabled./');
        $task->execute();
    }

    /**
     * Test the grade sync task when the auth_lti plugin is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_auth_plugin_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = new \enrol_lti\local\ltiadvantage\task\sync_grades();
        $this->expectOutputRegex('/Skipping task - ' .
            get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti')) . '/');
        $task->execute();
    }

    /**
     * Test the grade sync task when the enrol_lti plugin is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_enrol_plugin_disabled(): void {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = new \enrol_lti\local\ltiadvantage\task\sync_grades();
        $this->expectOutputRegex('/Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti') . '/');
        $task->execute();
    }
}
