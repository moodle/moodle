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
 * Test completion progress API.
 *
 * @package core_completion
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test completion progress API.
 *
 * @package core_completion
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_completion_progress_testcase extends advanced_testcase {

    /**
     * Test setup.
     */
    public function setUp(): void {
        global $CFG;

        $CFG->enablecompletion = true;
        $this->resetAfterTest();
    }

    /**
     * Tests that the course progress percentage is returned correctly when we have only activity completion.
     */
    public function test_course_progress_percentage_with_just_activities() {
        global $DB;

        // Add a course that supports completion.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Enrol a user in the course.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Add four activities that use completion.
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id),
            array('completion' => 1));
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
            array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('completion' => 1));

        // Add an activity that does *not* use completion.
        $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Mark two of them as completed for a user.
        $cmassign = get_coursemodule_from_id('assign', $assign->cmid);
        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $completion = new completion_info($course);
        $completion->update_state($cmassign, COMPLETION_COMPLETE, $user->id);
        $completion->update_state($cmdata, COMPLETION_COMPLETE, $user->id);

        // Check we have received valid data.
        // Note - only 4 out of the 5 activities support completion, and the user has completed 2 of those.
        $this->assertEquals('50', \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests that the course progress percentage is returned correctly when we have a course and activity completion.
     */
    public function test_course_progress_percentage_with_activities_and_course() {
        global $DB;

        // Add a course that supports completion.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Enrol a user in the course.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Add four activities that use completion.
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id),
            array('completion' => 1));
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
            array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('completion' => 1));
        $this->getDataGenerator()->create_module('forum', array('course' => $course->id),
            array('completion' => 1));

        // Add an activity that does *not* use completion.
        $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Mark two of them as completed for a user.
        $cmassign = get_coursemodule_from_id('assign', $assign->cmid);
        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $completion = new completion_info($course);
        $completion->update_state($cmassign, COMPLETION_COMPLETE, $user->id);
        $completion->update_state($cmdata, COMPLETION_COMPLETE, $user->id);

        // Now, mark the course as completed.
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user->id));
        $ccompletion->mark_complete();

        // Check we have received valid data.
        // The course completion takes priority, so should return 100.
        $this->assertEquals('100', \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests that the course progress returns null when the course does not support it.
     */
    public function test_course_progress_course_not_using_completion() {
        // Create a course that does not use completion.
        $course = $this->getDataGenerator()->create_course();

        // Check that the result was null.
        $this->assertNull(\core_completion\progress::get_course_progress_percentage($course));
    }

    /**
     * Tests that the course progress returns null when there are no activities that support it.
     */
    public function test_course_progress_no_activities_using_completion() {
        // Create a course that does support completion.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Add an activity that does *not* support completion.
        $this->getDataGenerator()->create_module('assign', array('course' => $course->id));

        // Check that the result was null.
        $this->assertNull(\core_completion\progress::get_course_progress_percentage($course));
    }

    /**
     * Tests that the course progress returns null for a not tracked for completion user in a course.
     */
    public function test_course_progress_not_tracked_user() {
        global $DB;

        // Add a course that supports completion.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Enrol a user in the course.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        // Now, mark the course as completed.
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user->id));
        $ccompletion->mark_complete();

        // The course completion should return 100.
        $this->assertEquals('100', \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Now make the user's role to be not tracked for completion.
        unassign_capability('moodle/course:isincompletionreports', $studentrole->id);

        // Check that the result is null now.
        $this->assertNull(\core_completion\progress::get_course_progress_percentage($course, $user->id));
    }
}
