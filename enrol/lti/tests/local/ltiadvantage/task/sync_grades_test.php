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
     * Get a task which has a mocked ags instance injected, meaning no real calls will be made to the platform.
     *
     * This allows us to test the behaviour of the task (in terms of which users are in scope and which grades are sent)
     * without needing to deal with any auth.
     *
     * @param string $statuscode the HTTP status code to simulate.
     * @param bool $mockexception whether to simulate an exception during the service call or not.
     * @return sync_grades instance of the task with a mocked ags instance inside.
     */
    protected function get_task_with_mocked_grade_service($statuscode = '200', $mockexception = false): sync_grades {
        $mockgradeservice = $this->getMockBuilder(LtiAssignmentsGradesService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['putGrade'])
            ->getMock();
        $mockgradeservice->method('putGrade')->willReturnCallback(function() use ($statuscode, $mockexception) {
            if ($mockexception) {
                throw new \Exception();
            }
            return ['headers' => ['httpstatus' => "HTTP/2 $statuscode OK"], 'body' => '', 'status' => $statuscode];
        });
        // Get a mock task, with the method 'get_ags()' mocked to return the mocked AGS instance.
        $mocktask = $this->getMockBuilder(sync_grades::class)
            ->onlyMethods(['get_ags'])
            ->getMock();
        $mocktask->method('get_ags')->willReturn($mockgradeservice);
        return $mocktask;
    }

    /**
     * Helper function to set a grade for a user.
     *
     * @param int $userid the id of the user being graded.
     * @param float $grade the grade value, out of 100, to set for the user.
     * @param \stdClass $resource the published resource object.
     * @return float the fractional grade value expected to be used during sync.
     */
    protected function set_user_grade_for_resource(int $userid, float $grade, \stdClass $resource): float {

        global $CFG;
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/gradelib.php');
        $context = \context::instance_by_id($resource->contextid);

        if ($context->contextlevel == CONTEXT_COURSE) {
            $gi = \grade_item::fetch_course_item($resource->courseid);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id('assign', $context->instanceid);

            $gi = \grade_item::fetch([
                'itemtype' => 'mod',
                'itemmodule' => 'assign',
                'iteminstance' => $cm->instance,
                'courseid' => $resource->courseid
            ]);
        }

        if ($ggrade = \grade_grade::fetch(['itemid' => $gi->id, 'userid' => $userid])) {
            $ggrade->finalgrade = $grade;
            $ggrade->rawgrade = $grade;
            $ggrade->update();
        } else {
            $ggrade = new \grade_grade();
            $ggrade->itemid = $gi->id;
            $ggrade->userid = $userid;
            $ggrade->rawgrade = $grade;
            $ggrade->finalgrade = $grade;
            $ggrade->rawgrademax = 100;
            $ggrade->rawgrademin = 0;
            $ggrade->timecreated = time();
            $ggrade->timemodified = time();
            $ggrade->insert();
        }
        return floatval($ggrade->finalgrade / $gi->grademax);
    }

    /**
     * Helper to set the completion status for published course or module.
     *
     * @param \stdClass $resource the resource - either a course or module.
     * @param int $userid the id of the user to override the completion status for.
     * @param bool $complete whether the resource is deemed complete or not.
     */
    protected function override_resource_completion_status_for_user(\stdClass $resource, int $userid,
            bool $complete): void {

        global $CFG;
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->libdir . '/datalib.php');
        $this->setAdminUser();
        $context = \context::instance_by_id($resource->contextid);
        $completion = new \completion_info(get_course($resource->courseid));
        if ($context->contextlevel == CONTEXT_COURSE) {
            $ccompletion = new \completion_completion(['userid' => $userid, 'course' => $resource->courseid]);
            if ($complete) {
                $ccompletion->mark_complete();
            } else {
                $completion->clear_criteria();
            }
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $completion->update_state(
                get_coursemodule_from_id('assign', $context->instanceid),
                $complete ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE,
                $userid,
                true
            );
        }
    }

    /**
     * Test confirming task name.
     *
     * @covers ::get_name
     */
    public function test_get_name() {
        $this->assertEquals(get_string('tasksyncgrades', 'enrol_lti'), (new sync_grades())->get_name());
    }

    /**
     * Test the sync grades task during several runs and for a series of grade changes.
     *
     * @covers ::execute
     */
    public function test_grade_sync_chronological_syncs() {
        $this->resetAfterTest();

        [$course, $resource] = $this->create_test_environment();
        $launchservice = $this->get_tool_launch_service();
        $task = $this->get_task_with_mocked_grade_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$teacherid, $resource] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        // Launch the resource for a few more users, creating those enrolments and allowing grading to take place.
        $studentusers = $this->get_mock_launch_users_with_ids(['2', '3'], false,
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner');

        $student1mocklaunch = $this->get_mock_launch($resource, $studentusers[0]);
        $student2mocklaunch = $this->get_mock_launch($resource, $studentusers[1]);
        $student1user = $this->getDataGenerator()->create_user();
        $student2user = $this->getDataGenerator()->create_user();
        [$student1id] = $launchservice->user_launches_tool($student1user, $student1mocklaunch);
        [$student2id] = $launchservice->user_launches_tool($student2user, $student2mocklaunch);

        // Grade student1 only.
        $expectedstudent1grade = $this->set_user_grade_for_resource($student1id, 65, $resource);

        // Sync and verify that only student1's grade is sent.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Skipping - Invalid grade for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
                "'$resource->id' and the course '$course->id' was sent.",
            "Skipping - Invalid grade for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 1 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Sync again, verifying no grades are sent because nothing has changed.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Skipping - Invalid grade for the user '$teacherid', for the resource '$resource->id' and the course ".
            "'$course->id'.",
            "Not sent - The grade for the user '$student1id', for the resource '$resource->id' and the course ".
                "'$course->id' was not sent as the grades are the same.",
            "Skipping - Invalid grade for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
            "Processed 3 users; sent 0 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Change student1's grade and add a grade for student2.
        $expectedstudent1grade = $this->set_user_grade_for_resource($student1id, 68.5, $resource);
        $expectedstudent2grade = $this->set_user_grade_for_resource($student2id, 44.5, $resource);

        // Sync again, verifying both grade changes are sent.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();

        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Skipping - Invalid grade for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
                "'$resource->id' and the course '$course->id' was sent.",
            "Success - The grade '$expectedstudent2grade' for the user '$student2id', for the resource ".
                "'$resource->id' and the course '$course->id' was sent.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 2 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }
    }

    /**
     * Test a grade sync when there are more than one resource link for the resource.
     *
     * @covers ::execute
     */
    public function test_grade_sync_multiple_resource_links() {
        $this->resetAfterTest();

        [$course, $resource] = $this->create_test_environment();
        $launchservice = $this->get_tool_launch_service();
        $task = $this->get_task_with_mocked_grade_service();

        // Launch the resource first for an instructor using the default resource link in the platform.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$teacherid, $resource] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        // Launch again as the instructor, this time from a different resource link in the platform.
        $teachermocklaunch2 = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0], 'RLID-2');
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch2);

        // Launch the resource for a few more users, creating those enrolments and allowing grading to take place.
        $studentusers = $this->get_mock_launch_users_with_ids(['2', '3'], false,
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner');

        $student1reslink1launch = $this->get_mock_launch($resource, $studentusers[0]);
        $student2reslink1launch = $this->get_mock_launch($resource, $studentusers[1]);
        $student1reslink2launch = $this->get_mock_launch($resource, $studentusers[1], 'RLID-2');
        $student1user = $this->getDataGenerator()->create_user();
        $student2user = $this->getDataGenerator()->create_user();
        [$student1id] = $launchservice->user_launches_tool($student1user, $student1reslink1launch);
        [$student2id] = $launchservice->user_launches_tool($student2user, $student2reslink1launch);
        $launchservice->user_launches_tool($student1user, $student1reslink2launch);

        // Grade student1 only.
        $expectedstudent1grade = $this->set_user_grade_for_resource($student1id, 65, $resource);

        // Sync and verify that only student1's grade is sent but that it's sent for BOTH resource links.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();

        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Skipping - Invalid grade for the user '$teacherid', for the resource '$resource->id' and the course ".
            "'$course->id'.",
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id' was sent.",
            "Found 2 resource link(s) for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id'. Attempting to sync grades for all.",
            "Skipping - Invalid grade for the user '$student2id', for the resource '$resource->id' and the course ".
            "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
            "Processed 3 users; sent 1 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Verify that the grade was reported as being synced twice - once for each resource link.
        $expected = "/Found 2 resource link\(s\) for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id'. Attempting to sync grades for all.\n".
            "Processing resource link '.*'.\n".
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id' was sent.\n".
            "Processing resource link '.*'.\n".
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id' was sent./";
        $this->assertMatchesRegularExpression($expected, $ob);
    }

    /**
     * Test grade sync when the resource has syncgrades disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_gradesync_disabled() {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, true, true, helper::MEMBER_SYNC_ENROL_AND_UNENROL,
            false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex('/Skipping task - There are no resources with grade sync enabled./');
        $task->execute();
    }

    /**
     * Test the grade sync task when the auth_lti plugin is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_auth_plugin_disabled() {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex('/Skipping task - ' .
            get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti')) . '/');
        $task->execute();
    }

    /**
     * Test the grade sync task when the enrol_lti plugin is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_enrol_plugin_disabled() {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment(true, false);
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex('/Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti') . '/');
        $task->execute();
    }

    /**
     * Test the grade sync task when the launch data doesn't include the AGS support.
     *
     * @covers ::execute
     */
    public function test_sync_grades_no_service_endpoint() {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0],
            null, null);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$userid] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex(
            "/Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.\n".
            "Found 1 resource link\(s\) for the user '$userid', for the resource '$resource->id' and the ".
            "course '$course->id'. Attempting to sync grades for all.\n".
            "Processing resource link '.*'.\n".
            "Skipping - No grade service found for the user '$userid', for the resource '$resource->id' and the ".
            "course '$course->id'.\n".
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. Processed 1 users; ".
            "sent 0 grades./"
        );
        $task->execute();
    }

    /**
     * Test syncing grades when the enrolment instance is disabled.
     *
     * @covers ::execute
     */
    public function test_sync_grades_disabled_instance() {
        $this->resetAfterTest();
        global $DB;

        [$course, $resource, $resource2, $resource3] = $this->create_test_environment();

        // Disable resource 1.
        $enrol = (object) ['id' => $resource->enrolid, 'status' => ENROL_INSTANCE_DISABLED];
        $DB->update_record('enrol', $enrol);

        // Delete the activity being shared by resource 2, leaving resource 2 disabled as a result.
        $modcontext = \context::instance_by_id($resource2->contextid);
        course_delete_module($modcontext->instanceid);

        // Only the enabled resource 3 should sync grades.
        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex(
            "/^Starting - LTI Advantage grade sync for shared resource '$resource3->id' in course '$course->id'.\n".
            "Completed - Synced grades for tool '$resource3->id' in the course '$course->id'. Processed 0 users; ".
            "sent 0 grades.\n$/"
        );
        $task->execute();
    }

    /**
     * Test the grade sync when the context has been deleted in between launch and when the grade sync task is run.
     *
     * @covers ::execute
     */
    public function test_sync_grades_deleted_context() {
        $this->resetAfterTest();
        global $DB;

        [$course, $resource] = $this->create_test_environment();
        $launchservice = $this->get_tool_launch_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$userid] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        // Delete the activity, then enable the enrolment method (it is disabled during activity deletion).
        $modcontext = \context::instance_by_id($resource->contextid);
        course_delete_module($modcontext->instanceid);
        $enrol = (object) ['id' => $resource->enrolid, 'status' => ENROL_INSTANCE_ENABLED];
        $DB->update_record('enrol', $enrol);

        $task = $this->get_task_with_mocked_grade_service();
        $this->expectOutputRegex(
            "/Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.\n".
            "Found 1 resource link\(s\) for the user '$userid', for the resource '$resource->id' and the ".
            "course '$course->id'. Attempting to sync grades for all.\n".
            "Processing resource link '.*'.\n".
            "Failed - Invalid contextid '$resource->contextid' for the resource '$resource->id'.\n".
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. Processed 1 users; ".
            "sent 0 grades./"
        );
        $task->execute();
    }

    /**
     * Test grade sync when completion is required for the activity before sync takes place.
     *
     * @covers ::execute
     */
    public function test_sync_grades_completion_required() {
        $this->resetAfterTest();
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');

        [
            $course,
            $resource,
            $resource2,
            $publishedcourse
        ] = $this->create_test_environment(true, true, false, helper::MEMBER_SYNC_ENROL_AND_UNENROL, true, true);
        $launchservice = $this->get_tool_launch_service();
        $task = $this->get_task_with_mocked_grade_service();

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$teacherid] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        // Launch the resource for a few more users, creating those enrolments and allowing grading to take place.
        $studentusers = $this->get_mock_launch_users_with_ids(['2', '3'], false,
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner');
        $student1mocklaunch = $this->get_mock_launch($resource, $studentusers[0]);
        $student2mocklaunch = $this->get_mock_launch($resource, $studentusers[1]);
        $student1user = $this->getDataGenerator()->create_user();
        $student2user = $this->getDataGenerator()->create_user();
        [$student1id] = $launchservice->user_launches_tool($student1user, $student1mocklaunch);
        [$student2id] = $launchservice->user_launches_tool($student2user, $student2mocklaunch);

        // Launch the published course as student2.
        $student2mockcourselaunch = $this->get_mock_launch($publishedcourse, $studentusers[1], '23456');
        $launchservice->user_launches_tool($student2user, $student2mockcourselaunch);

        // Grade student1 in the assign resource.
        $expectedstudent1grade = $this->set_user_grade_for_resource($student1id, 65, $resource);

        // And student2 in the course resource.
        $expectedstudent2grade = $this->set_user_grade_for_resource($student2id, 55.5, $publishedcourse);

        // Sync and verify that no grades are sent because resource and published course are both not yet complete.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Activity not completed for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student1id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Skipping - Course not completed for the user '$student2id', for the resource '$publishedcourse->id' and ".
                "the course '$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 0 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Complete the resource for student1.
        $this->override_resource_completion_status_for_user($resource, $student1id, true);

        // Run the sync again, this time confirming the grade for student1 is sent.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Activity not completed for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Success - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
                "'$resource->id' and the course '$course->id' was sent.",
            "Activity not completed for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 1 grades.",
            "Starting - LTI Advantage grade sync for shared resource '$publishedcourse->id' in course '$course->id'.",
            "Skipping - Course not completed for the user '$student2id', for the resource '$publishedcourse->id' and ".
                "the course '$course->id'.",
            "Completed - Synced grades for tool '$publishedcourse->id' in the course '$course->id'. ".
                "Processed 1 users; sent 0 grades.",
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Fail completion for student1 and confirm no grade is sent, even despite it being changed.
        $this->set_user_grade_for_resource($student1id, 33.3, $resource);
        $this->override_resource_completion_status_for_user($resource, $student1id, false);

        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Activity not completed for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student1id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 0 grades.",
            "Starting - LTI Advantage grade sync for shared resource '$publishedcourse->id' in course '$course->id'.",
            "Skipping - Course not completed for the user '$student2id', for the resource '$publishedcourse->id' and ".
                "the course '$course->id'.",
            "Completed - Synced grades for tool '$publishedcourse->id' in the course '$course->id'. ".
                "Processed 1 users; sent 0 grades.",
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Complete the course for student2 and verify the grade is now sent.
        $this->override_resource_completion_status_for_user($publishedcourse, $student2id, true);

        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Activity not completed for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student1id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 0 grades.",
            "Starting - LTI Advantage grade sync for shared resource '$publishedcourse->id' in course '$course->id'.",
            "Success - The grade '$expectedstudent2grade' for the user '$student2id', for the resource ".
                "'$publishedcourse->id' and the course '$course->id' was sent.",
            "Completed - Synced grades for tool '$publishedcourse->id' in the course '$course->id'. ".
                "Processed 1 users; sent 1 grades.",

        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Mark the course as in progress again for student2 and verify any new grade changes are not sent.
        $this->set_user_grade_for_resource($student2id, 78.8, $publishedcourse);
        $this->override_resource_completion_status_for_user($publishedcourse, $student2id, false);

        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Activity not completed for the user '$teacherid', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student1id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Activity not completed for the user '$student2id', for the resource '$resource->id' and the course ".
                "'$course->id'.",
            "Skipping - Course not completed for the user '$student2id', for the resource '$publishedcourse->id' and ".
                "the course '$course->id'.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 3 users; sent 0 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }
    }

    /**
     * Test grade sync when the attempt to call the service returns an exception or a bad HTTP response code.
     *
     * @covers ::execute
     */
    public function test_sync_grades_failed_service_call() {
        $this->resetAfterTest();
        [$course, $resource] = $this->create_test_environment();
        $launchservice = $this->get_tool_launch_service();
        $task = $this->get_task_with_mocked_grade_service('200', true);

        // Launch the resource for an instructor which will create the domain objects needed for service calls.
        $teachermocklaunch = $this->get_mock_launch($resource, $this->get_mock_launch_users_with_ids(['1'], false)[0]);
        $instructoruser = $this->getDataGenerator()->create_user();
        [$teacherid] = $launchservice->user_launches_tool($instructoruser, $teachermocklaunch);

        // Launch the resource for a student, creating the enrolment and allowing grading to take place.
        $studentusers = $this->get_mock_launch_users_with_ids(['2', '3'], false,
            'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner');
        $student1mocklaunch = $this->get_mock_launch($resource, $studentusers[0]);
        $student1user = $this->getDataGenerator()->create_user();
        [$student1id] = $launchservice->user_launches_tool($student1user, $student1mocklaunch);

        // Grade student1 in the assign resource.
        $expectedstudent1grade = $this->set_user_grade_for_resource($student1id, 65, $resource);

        // Run the sync, verifying that the response error causes a 'Failed' trace but that the task completes.
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Failed - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
                "'$resource->id' and the course '$course->id' failed to send.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
                "Processed 2 users; sent 0 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }

        // Now run the sync again, this time with a bad http response code.
        $task = $this->get_task_with_mocked_grade_service('400');
        ob_start();
        $task->execute();
        $ob = ob_get_contents();
        ob_end_clean();
        $expectedtraces = [
            "Starting - LTI Advantage grade sync for shared resource '$resource->id' in course '$course->id'.",
            "Failed - The grade '$expectedstudent1grade' for the user '$student1id', for the resource ".
            "'$resource->id' and the course '$course->id' failed to send.",
            "Completed - Synced grades for tool '$resource->id' in the course '$course->id'. ".
            "Processed 2 users; sent 0 grades."
        ];
        foreach ($expectedtraces as $expectedtrace) {
            $this->assertStringContainsString($expectedtrace, $ob);
        }
    }
}
