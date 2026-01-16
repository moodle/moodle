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

namespace core_completion;

use completion_completion;
use core_availability\tree;
use availability_date\condition;

/**
 * Test completion progress API.
 *
 * @package core_completion
 * @category test
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_completion\progress
 */
final class progress_test extends \advanced_testcase {

    /**
     * Test setup.
     */
    public function setUp(): void {
        global $CFG;
        parent::setUp();

        $CFG->enablecompletion = true;
        $this->resetAfterTest();
    }

    /**
     * Tests that the course progress percentage is returned correctly when we have only activity completion.
     */
    public function test_course_progress_percentage_with_just_activities(): void {
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

        $this->setUser($user);

        // Mark two of them as completed for a user.
        $cmassign = get_coursemodule_from_id('assign', $assign->cmid);
        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $completion = new \completion_info($course);
        $completion->update_state($cmassign, COMPLETION_COMPLETE, $user->id);
        $completion->update_state($cmdata, COMPLETION_COMPLETE, $user->id);

        // Check we have received valid data.
        // Note - only 4 out of the 5 activities support completion, and the user has completed 2 of those.
        $this->assertEquals('50', \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests that the course progress percentage is returned correctly when we have a course and activity completion.
     */
    public function test_course_progress_percentage_with_activities_and_course(): void {
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
        $completion = new \completion_info($course);
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
     * Tests that the course progress percentage is returned correctly for various grade to pass settings
     */
    public function test_course_progress_percentage_completion_state(): void {
        global $DB, $CFG;

        require_once("{$CFG->dirroot}/completion/criteria/completion_criteria_activity.php");

        // Add a course that supports completion.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);

        // Enrol a user in the course.
        $teacher = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        // Add three activities that use completion.
        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign['passgragepassed'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => 1,
            'gradepass' => 50,
            'completionpassgrade' => 1
        ]);

        $assign['passgragefailed'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => 1,
            'gradepass' => 50,
            'completionpassgrade' => 1
        ]);

        $assign['passgragenotused'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
            'completionusegrade' => 1,
            'gradepass' => 50,
        ]);

        $assign['nograde'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);

        $c = new \completion_info($course);

        foreach ($assign as $item) {
            $cmassing = get_coursemodule_from_id('assign', $item->cmid);

            // Add activity completion criteria.
            $criteriadata = new \stdClass();
            $criteriadata->id = $course->id;
            $criteriadata->criteria_activity = [];
            // Some activities.
            $criteriadata->criteria_activity[$cmassing->id] = 1;
            $criterion = new \completion_criteria_activity();
            $criterion->update_config($criteriadata);
        }

        $this->setUser($teacher);

        foreach ($assign as $key => $item) {
            $cm = get_coursemodule_from_instance('assign', $item->id);

            // Mark user completions.
            $completion = new \stdClass();
            $completion->coursemoduleid = $cm->id;
            $completion->timemodified = time();
            $completion->viewed = COMPLETION_NOT_VIEWED;
            $completion->overrideby = null;

            if ($key == 'passgragepassed') {
                $completion->id = 0;
                $completion->completionstate = COMPLETION_COMPLETE_PASS;
                $completion->userid = $user->id;
                $c->internal_set_data($cm, $completion, true);
            } else if ($key == 'passgragefailed') {
                $completion->id = 0;
                $completion->completionstate = COMPLETION_COMPLETE_FAIL;
                $completion->userid = $user->id;
                $c->internal_set_data($cm, $completion, true);
            } else if ($key == 'passgragenotused') {
                $completion->id = 0;
                $completion->completionstate = COMPLETION_COMPLETE;
                $completion->userid = $user->id;
                $c->internal_set_data($cm, $completion, true);
            } else if ($key == 'nograde') {
                $completion->id = 0;
                $completion->completionstate = COMPLETION_COMPLETE;
                $completion->userid = $user->id;
                $c->internal_set_data($cm, $completion, true);
            }
        }

        // Run course completions cron.
        \core_completion\api::mark_course_completions_activity_criteria();

        // Check we have received valid data.
        // Only assign2 is not completed.
        $this->assertEquals('75', \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests that the course progress returns null when the course does not support it.
     */
    public function test_course_progress_course_not_using_completion(): void {
        // Create a course that does not use completion.
        $course = $this->getDataGenerator()->create_course();

        // Check that the result was null.
        $this->assertNull(\core_completion\progress::get_course_progress_percentage($course));
    }

    /**
     * Tests that the course progress returns null when there are no activities that support it.
     */
    public function test_course_progress_no_activities_using_completion(): void {
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
    public function test_course_progress_not_tracked_user(): void {
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

    /**
     * Tests course progress with hidden section.
     */
    public function test_course_progress_percentage_with_hidden_section(): void {
        global $DB;

        // Create a course with completion enabled and two sections.
        $course = $this->getDataGenerator()->create_course([
            'enablecompletion' => 1,
            'numsections' => 2,
            'format' => 'topics',
        ]);

        // Enrol a student.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');

        // Add visible activities to section 0 and 1.
        $activity1 = $assigngenerator->create_instance([
            'course' => $course->id,
            'section' => 0,
            'completion' => COMPLETION_ENABLED,
        ]);
        $activity2 = $assigngenerator->create_instance([
            'course' => $course->id,
            'section' => 1,
            'completion' => COMPLETION_ENABLED,
        ]);
        $this->setUser($user);

        // Hide section 1.
        set_section_visible($course->id, 1, 0);
        $completion = new \completion_info($course);

        // Complete the visible activity: activity1.
        $cm = get_coursemodule_from_id('assign', $activity1->cmid);
        $completion->update_state($cm, COMPLETION_COMPLETE, $user->id);

        // Only the visible activity (activity1) counts toward course completion.
        // Activities in hidden sections are not included in the calculation.
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Now unhide section 1.
        set_section_visible($course->id, 1, 1);

        // Course completion: 1 of 2 visible activities complete; previously hidden activity now counted.
        $this->assertEquals(50, \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests course progress with hidden activity.
     */
    public function test_course_progress_percentage_with_hidden_activity(): void {
        global $DB;

        // Create a course with completion enabled and two sections.
        $course = $this->getDataGenerator()->create_course([
            'enablecompletion' => 1,
            'numsections' => 3,
            'format' => 'topics',
        ]);

        // Enrol a student.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');

        // Section 0: visible activity.
        $activity1 = $assigngenerator->create_instance([
            'course' => $course->id,
            'section' => 0,
            'completion' => COMPLETION_ENABLED,
        ]);

        // Section 1: hidden activity.
        $activity2 = $assigngenerator->create_instance([
            'course' => $course->id,
            'section' => 1,
            'completion' => COMPLETION_ENABLED,
            'visible' => 0,
        ]);

        $completion = new \completion_info($course);
        $this->setUser($user);

        // Complete activity1 (visible).
        $cm1 = get_coursemodule_from_id('assign', $activity1->cmid);
        $completion->update_state($cm1, COMPLETION_COMPLETE, $user->id);

        // Only 1 visible activity: course completion = 100%.
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user->id));

        $cm2 = get_coursemodule_from_id('assign', $activity2->cmid);
        // Show activity2.
        $DB->set_field('course_modules', 'visible', 1, ['id' => $cm2->id]);
        $completion->update_state($cm2, COMPLETION_COMPLETE, $user->id);
        rebuild_course_cache($course->id, true);

        // Course completion: both activities are complete.
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Hide activity1 so it is excluded from course progress.
        $DB->set_field('course_modules', 'visible', 0, ['id' => $cm1->id]);
        rebuild_course_cache($course->id, true);

        // Course completion: activity1 is hidden and excluded from calculation.
        // Only activity2 remains visible and complete, so progress stays at 100%.
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user->id));
    }

    /**
     * Tests course progress percentage with availability restrictions.
     */
    public function test_course_progress_percentage_with_availability_restrictions(): void {
        global $DB;

        set_config('enableavailability', 1);

        // Create a course with completion enabled.
        $course = $this->getDataGenerator()->create_course([
            'enablecompletion' => 1,
            'format' => 'topics',
        ]);

        // Create and enrol a student.
        $user = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');

        // Activity 1: no restrictions (always visible).
        $assign['activity1'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);

        // Activity 2: date restriction from past (visible).
        $assign['activity2'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);
        $availabilityjson = json_encode(tree::get_root_json(
            [
                condition::get_json(condition::DIRECTION_FROM, time() - 3600),
            ],
            tree::OP_AND,
            false,
        ));
        $DB->set_field('course_modules', 'availability', $availabilityjson, ['id' => $assign['activity2']->cmid]);

        // Activity 3: date restriction from future, shows info (open eye).
        $assign['activity3'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);
        $availabilityjson = json_encode(tree::get_root_json(
            [
                condition::get_json(condition::DIRECTION_FROM, time() + 3600),
            ],
            tree::OP_AND,
            true,
        ));
        $DB->set_field('course_modules', 'availability', $availabilityjson, ['id' => $assign['activity3']->cmid]);

        // Activity 4: date restriction from future, hidden (closed eye).
        $assign['activity4'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);
        $availabilityjson = json_encode(tree::get_root_json(
            [
                condition::get_json(condition::DIRECTION_FROM, time() + 7200),
            ],
            tree::OP_AND,
            false,
        ));
        $DB->set_field('course_modules', 'availability', $availabilityjson, ['id' => $assign['activity4']->cmid]);

        rebuild_course_cache($course->id, true);

        // Set user context and get completion info.
        $this->setUser($user);
        $completion = new \completion_info($course);

        // Initial completion: 0%.
        $this->assertEquals(0, \core_completion\progress::get_course_progress_percentage($course, $user->id));

        // Complete activity 1.
        $cm1 = get_coursemodule_from_id('assign', $assign['activity1']->cmid);
        $completion->update_state($cm1, COMPLETION_COMPLETE, $user->id);
        // 1 of 3 user-visible activities is complete; activity4 is hidden and ignored.
        $this->assertEquals(33.33, round(\core_completion\progress::get_course_progress_percentage($course, $user->id), 2));

        // Complete activity 2 (available from past).
        $cm2 = get_coursemodule_from_id('assign', $assign['activity2']->cmid);
        $completion->update_state($cm2, COMPLETION_COMPLETE, $user->id);
        // Now 2 of the 3 user-visible activities are complete.
        $this->assertEquals(66.67, round(\core_completion\progress::get_course_progress_percentage($course, $user->id), 2));

        // Mark the restricted activity (with open eye) as complete.
        $cm3 = get_coursemodule_from_id('assign', $assign['activity3']->cmid);
        $completion->update_state($cm3, COMPLETION_COMPLETE, $user->id);
        // All 3 user-visible activities are now complete; activity4 remains hidden.
        $this->assertEquals(100, round(\core_completion\progress::get_course_progress_percentage($course, $user->id), 2));
    }

    /**
     * Tests course progress percentage with group restrictions.
     */
    public function test_course_progress_percentage_with_group_restrictions(): void {
        global $DB;

        set_config('enableavailability', 1);

        // Create a course with completion enabled.
        $course = $this->getDataGenerator()->create_course([
            'enablecompletion' => 1,
            'format' => 'topics',
        ]);

        // Create and enrol two students in the course.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, $studentrole->id);

        // Create a group and add user1.
        $group1 = $this->getDataGenerator()->create_group([
            'courseid' => $course->id,
            'name' => 'Group 1',
        ]);
        $this->getDataGenerator()->create_group_member([
            'groupid' => $group1->id,
            'userid' => $user1->id,
        ]);

        /** @var \mod_assign_generator $assigngenerator */
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');

        // Activity 1: restricted to group1.
        $assign['activity1'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);
        $availabilityjson = json_encode(tree::get_root_json(
            [
                \availability_group\condition::get_json($group1->id),
            ],
            tree::OP_AND,
            false,
        ));
        $DB->set_field('course_modules', 'availability', $availabilityjson, ['id' => $assign['activity1']->cmid]);

        // Activity 2: Visible to all users.
        $assign['activity2'] = $assigngenerator->create_instance([
            'course' => $course->id,
            'completion' => COMPLETION_ENABLED,
        ]);
        rebuild_course_cache($course->id, true);

        $cm1 = get_coursemodule_from_id('assign', $assign['activity1']->cmid);
        $cm2 = get_coursemodule_from_id('assign', $assign['activity2']->cmid);

        // Test user1 (in group): complete both the activities.
        $this->setUser($user1);
        $completion = new \completion_info($course);
        $completion->update_state($cm1, COMPLETION_COMPLETE, $user1->id);
        $this->assertEquals(50, \core_completion\progress::get_course_progress_percentage($course, $user1->id));
        $completion->update_state($cm2, COMPLETION_COMPLETE, $user1->id);
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user1->id));

        // Test user2 (not in group1): completes only the visible activity.
        $this->setUser($user2);
        $completion = new \completion_info($course);
        $completion->update_state($cm2, COMPLETION_COMPLETE, $user2->id);
        $this->assertEquals(100, \core_completion\progress::get_course_progress_percentage($course, $user2->id));
    }
}
