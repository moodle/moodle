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

namespace mod_assign;

use assign;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/accesslib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Unit tests for (some of) mod/assign/markerallocaion_test.php.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2017 Andrés Melo <andres.torres@blackboard.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \assign
 */
final class markerallocation_test extends \advanced_testcase {

    /** @var \stdClass course record. */
    private $course;

    /**
     * @var array Generated users
     */
    private array $users = [];

    /**
     * @var array Generated groups
     */
    private array $groups = [];

    /**
     * Create the assignment object for testing.
     *
     * @param array $args Array of options that can be overwritten.
     * @return assign
     */
    private function create_assignment(array $args = []): assign {
        $modulesettings = [
            'course'                            => $this->course->id,
            'alwaysshowdescription'             => 1,
            'submissiondrafts'                  => 1,
            'requiresubmissionstatement'        => 0,
            'sendnotifications'                 => 0,
            'sendstudentnotifications'          => 1,
            'sendlatenotifications'             => 0,
            'duedate'                           => 0,
            'allowsubmissionsfromdate'          => 0,
            'grade'                             => (!isset($args['scale'])) ? 100 : null,
            'cutoffdate'                        => 0,
            'teamsubmission'                    => ($args['teamsubmission']) ?? 0,
            'requireallteammemberssubmit'       => 0,
            'blindmarking'                      => 0,
            'attemptreopenmethod'               => 'untilpass',
            'maxattempts'                       => 1,
            'markingworkflow'                   => 1,
            'markingallocation'                 => 1,
            'markercount'                       => ($args['markercount']) ?? ASSIGN_MULTIMARKING_DEFAULT_MARKERS,
            'multimarkmethod'                   => ($args['multimarkmethod']) ?? ASSIGN_MULTIMARKING_METHOD_MANUAL,
            'multimarkrounding'                 => ($args['multimarkrounding']) ?? null,
        ];

        if (isset($args['scale'])) {
            $scale = $this->getDataGenerator()->create_scale();
            $modulesettings['gradetype'] = GRADE_TYPE_SCALE;
            $modulesettings['gradescale'] = $scale->id;
        }

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $generator->create_instance($modulesettings);
        [$course, $cm] = get_course_and_cm_from_instance($instance->id, 'assign');
        $context = \core\context\module::instance($cm->id);
        $assignment = new assign($context, $cm, $course);
        return $assignment;
    }

    /**
     * Setup all required test data.
     */
    private function setup_data(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a course, by default it is created with 5 sections.
        $this->course = $this->getDataGenerator()->create_course();

        // Adding users to the course.
        $userdata = array();
        $userdata['firstname'] = 'teacher1';
        $userdata['lasttname'] = 'lastname_teacher1';
        $this->users[0] = $this->getDataGenerator()->create_user($userdata);
        $this->getDataGenerator()->enrol_user($this->users[0]->id, $this->course->id, 'editingteacher');

        $userdata = array();
        $userdata['firstname'] = 'teacher2';
        $userdata['lasttname'] = 'lastname_teacher2';
        $this->users[1] = $this->getDataGenerator()->create_user($userdata);
        $this->getDataGenerator()->enrol_user($this->users[1]->id, $this->course->id, 'editingteacher');

        $userdata = array();
        $userdata['firstname'] = 'student';
        $userdata['lasttname'] = 'lastname_student';
        $this->users[2] = $this->getDataGenerator()->create_user($userdata);
        $this->getDataGenerator()->enrol_user($this->users[2]->id, $this->course->id, 'student');

        // Adding manager to the system.
        $userdata = array();
        $userdata['firstname'] = 'Manager';
        $userdata['lasttname'] = 'lastname_Manager';
        $this->users[3] = $this->getDataGenerator()->create_user($userdata);
        $managerrole = $DB->get_record('role', array('shortname' => 'manager'));
        if (!empty($managerrole)) {
            // By default the context of the system is assigned.
            $this->getDataGenerator()->role_assign($managerrole->id, $this->users[3]->id);
        }
    }

    /**
     * Setup group data for teamsubmission tests.
     */
    private function setup_group_data(): void {
        $this->resetAfterTest(false);

        // Create a course, by default it is created with 5 sections.
        $this->course = $this->getDataGenerator()->create_course();

        // Split users into seaprate arrays for easier use here.
        $teachers = [];
        $students = [];

        // Adding teachers to the course.
        for ($i = 1; $i <= 2; $i++) {
            $userdata = [];
            $userdata['firstname'] = 'teacher' . $i;
            $userdata['lasttname'] = 'lastname_teacher' . $i;
            $teachers[$i] = $this->getDataGenerator()->create_user($userdata);
            $this->getDataGenerator()->enrol_user($teachers[$i]->id, $this->course->id, 'teacher');
        }

        // Adding students to the course.
        for ($i = 1; $i <= 6; $i++) {
            $userdata = [];
            $userdata['firstname'] = 'student' . $i;
            $userdata['lasttname'] = 'lastname_student' . $i;
            $students[$i] = $this->getDataGenerator()->create_user($userdata);
            $this->getDataGenerator()->enrol_user($students[$i]->id, $this->course->id, 'student');
        }

        // Adding students to groups.
        $this->groups['A'] = $this->getDataGenerator()->create_group(['courseid' => $this->course->id, 'name' => 'A']);
        $this->groups['B'] = $this->getDataGenerator()->create_group(['courseid' => $this->course->id, 'name' => 'B']);
        foreach ($students as $studentnumber => $user) {
            if ($studentnumber <= 3) {
                groups_add_member($this->groups['A'], $user);
            } else {
                groups_add_member($this->groups['B'], $user);
            }
        }

        $this->users = ['students' => $students, 'teachers' => $teachers];
    }

    /**
     * Test marker allocation and marking with group submissions.
     *
     * @covers ::update_allocated_markers, ::save_grade
     */
    public function test_allocated_markers_with_group_submissions(): void {
        $this->setup_group_data();
        $assignment = $this->create_assignment([
            'teamsubmission' => 1,
        ]);

        // To test the logic that a marker should not be able to update anyone not in their group
        // we will use the "public" method `save_grade` instead of the internal `update_mark`.
        // Firstly, allocate teacher1 to every student in group A.
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber <= 3) {
                $assignment->update_allocated_markers($student->id, [$this->users['teachers'][1]->id]);
            }
        }

        // Allocate a mark to the first student in the group.
        // This should spread out to the other students in the group as well.
        $this->setUser($this->users['teachers'][1]);

        // Before we save it, we need to create the submission record, which won't happen from just saving it.
        // We are passing -1 as userid because it's a required argument, but if the groupid is present, then
        // the `get_group_submission` function ignores it, so it just needs any value really.
        $assignment->get_group_submission(-1, $this->groups['A']->id, true);

        // Then save it.
        $assignment->save_grade($this->users['students'][1]->id, (object)[
            'mark' => 50,
            'applytoall' => 1,
            'attemptnumber' => -1,
        ]);

        // All 3 students in the group should now have the same mark from this allocated marker.
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber <= 3) {
                $gradeobject = $assignment->get_user_grade($student->id, true);
                $mark = $assignment->get_mark($gradeobject->id, $this->users['teachers'][1]->id);
                $this->assertEquals(50, $mark->mark);
            }
        }

        // Now allocate teacher2 to 2 out of 3 students in group B.
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber > 3 && $studentnumber < 6) {
                $assignment->update_allocated_markers($student->id, [$this->users['teachers'][2]->id]);
            }
        }

        // Allocate a mark to the first student in the group.
        $this->setUser($this->users['teachers'][2]);
        $assignment->get_group_submission(-1, $this->groups['B']->id, true);
        $assignment->save_grade($this->users['students'][4]->id, (object)[
            'mark' => 99,
            'applytoall' => 1,
            'attemptnumber' => -1,
        ]);

        // Only 2 out of 3 students should have the grade applied.
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber > 3) {
                $gradeobject = $assignment->get_user_grade($student->id, true);
                $mark = $assignment->get_mark($gradeobject->id, $this->users['teachers'][2]->id);
                if ($studentnumber < 6) {
                    $this->assertEquals(99, $mark->mark);
                } else {
                    $this->assertNull($mark);
                }
            }
        }
    }

    /**
     * Create all the needed elements to test the difference between both functions.
     *
     * @coversNothing
     */
    public function test_markerusers(): void {
        $this->setup_data();

        $oldusers = [$this->users[0], $this->users[1], $this->users[3]];
        $newusers = [$this->users[0], $this->users[1]];

        list($sort, $params) = users_order_by_sql('u');

        // Old code, it must return 3 users: teacher1, teacher2 and Manger.
        $oldmarkers = get_users_by_capability(\context_course::instance($this->course->id), 'mod/assign:grade', '', $sort);
        // New code, it must return 2 users: teacher1 and teacher2.
        $newmarkers = get_enrolled_users(\context_course::instance($this->course->id), 'mod/assign:grade', 0, 'u.*', $sort);

        // Test result quantity.
        $this->assertEquals(count($oldusers), count($oldmarkers));
        $this->assertEquals(count($newusers), count($newmarkers));
        $this->assertEquals(count($oldmarkers) > count($newmarkers), true);

        // Elements expected with new code.
        foreach ($newmarkers as $key => $nm) {
            $this->assertEquals($nm, $newusers[array_search($nm, $newusers)]);
        }

        // Elements expected with old code.
        foreach ($oldusers as $key => $os) {
            $this->assertEquals($os->id, $oldmarkers[$os->id]->id);
            unset($oldmarkers[$os->id]);
        }

        $this->assertEquals(count($oldmarkers), 0);
    }

    /**
     * Test functionality around having multiple allocated markers.
     *
     * @covers ::update_allocated_markers, ::update_mark
     */
    public function test_multiple_marker_allocation(): void {

        $this->setup_data();
        $assignment = $this->create_assignment();

        // To start with, confirm that no markers are allocated to the student submission.
        $markers = $assignment->get_allocated_markers($this->users[2]->id);
        $this->assertCount(0, $markers);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);
        $markers = $assignment->get_allocated_markers($this->users[2]->id);
        $this->assertCount(2, $markers);

        // Now test that we can add a mark to the submission.
        // Firstly, there should be no mark currently for either marker.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $mark = $assignment->get_mark($gradeobject->id, $this->users[0]->id);
        $this->assertNull($mark);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 99);

        // Now check that we can find the mark.
        $mark = $assignment->get_mark($gradeobject->id, $this->users[0]->id);
        $this->assertEquals("99.00000", $mark->mark);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 11);

        // Now check that we can find the mark.
        $mark = $assignment->get_mark($gradeobject->id, $this->users[1]->id);
        $this->assertEquals("11.00000", $mark->mark);
    }

    /**
     * Test manual calculation of final grade.
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_manual(): void {
        $this->setup_data();
        $assignment = $this->create_assignment();

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 99);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 11);

        // With manual calculation, there should be no grade set yet.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);
    }

    /**
     * Test "maximum" calculation of final grade when using scale grading.
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_maximum(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_MAX,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 11);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 99);

        // With max calculation, the grade should be the highest one.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(99, $gradeobject->grade);
    }

    /**
     * Test "average" calculation of final grade when using rounding of "none".
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_average_round_none(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25);

        // With avg calculation and no rounding, the grade should be 57.5.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(57.5, $gradeobject->grade);
    }

    /**
     * Test "average" calculation of final grade when using rounding of "down".
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_average_rounding_down(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_DOWN,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25);

        // With avg calculation and down rounding, the grade should be 57.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(57, $gradeobject->grade);
    }

    /**
     * Test that the grade calculation from marks using method "average" with up rounding, sets the correct grade.
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_average_round_up(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_UP,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25);

        // With avg calculation and up rounding, the grade should be 58.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(58, $gradeobject->grade);
    }

    /**
     * Test that the grade calculation from marks using method "average" with natural rounding, sets the correct grade.
     *
     * @covers ::update_mark
     */
    public function test_calculated_marker_grade_average_round_natural(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NATURAL,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25);

        // With avg calculation and natural rounding, the grade should be 58.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(58, $gradeobject->grade);
    }

    /**
     * Test that the workflow state changes on the overall grade based on marker states.
     *
     * @covers ::update_mark, ::calculate_and_save_overall_workflow_state
     */
    public function test_calculated_marker_workflow(): void {
        $this->setup_data();
        $assignment = $this->create_assignment();

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        // First confirm that the overall grade workflow state is not set.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEmpty($flags->workflowstate);

        // One marker then sets their mark to be in the state "In Marking".
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, null, ASSIGN_MARKING_WORKFLOW_STATE_INMARKING);
        $assignment->calculate_and_save_overall_workflow_state($gradeobject, $flags, $flags->workflowstate);

        // Re-check the overall workflow. This should now be "In Marking" as well.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $flags->workflowstate);

        // Now this teacher marks theirs as "Marking Complete".
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
        $assignment->calculate_and_save_overall_workflow_state($gradeobject, $flags, $flags->workflowstate);

        // Nothing should change on the overall state, that should still be In Marking.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $flags->workflowstate);

        // Now the second marker sets theirs as "Marking Complete".
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 70, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
        $assignment->calculate_and_save_overall_workflow_state($gradeobject, $flags, $flags->workflowstate);

        // Now that both are complete, the overall state should be the same.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW, $flags->workflowstate);
    }

    /**
     * Test that when we remove a marker their marks are not counted towards anything.
     *
     * @covers ::update_mark
     */
    public function test_unallocated_marker_not_included_in_mark_calculations(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NATURAL,
        ]);

        // Allocate both teachers to the student assignment.
        $assignment->update_allocated_markers($this->users[2]->id, [
            $this->users[0]->id,
            $this->users[1]->id,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90);

        // Now we remove teacher1 and add manager instead. So we have manager and teacher2 as the markers.
        $assignment->update_allocated_markers($this->users[2]->id, [$this->users[3]->id, $this->users[1]->id]);

        // Now add a marker from teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10);

        // At this point, though we've had 2 marks, only 1 of the allocated markers has marked.
        // So the grade should not be set.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);
    }
}
