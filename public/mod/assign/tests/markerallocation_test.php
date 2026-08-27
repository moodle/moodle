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
            'optionalmarkercount'               => ($args['optionalmarkercount']) ?? ASSIGN_MULTIMARKING_DEFAULT_OPTIONAL_MARKERS,
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
     * Updates an assignment instance.
     * @param assign $assignment
     * @param array $updatefields
     */
    private function update_assignment_instance(assign $assignment, array $updatefields): void {
        // Need to clone so the update can detect the differences.
        $instance = clone $assignment->get_instance();
        $instance->instance = $instance->id;
        $instance->advancedgradingmethod_submissions = '';
        foreach ($updatefields as $key => $value) {
            $instance->$key = $value;
        }
        $assignment->update_instance($instance);
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

        // Start as admin to allocate users.
        $this->setAdminUser();
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
     * @covers ::update_marker_allocations, ::save_grade
     */
    public function test_allocated_markers_with_group_submissions(): void {
        $this->setup_group_data();
        $assignment = $this->create_assignment([
            'teamsubmission' => 1,
        ]);

        // To test the logic that a marker should not be able to update anyone not in their group
        // we will use the "public" method `save_grade` instead of the internal `update_mark`.
        // Firstly, allocate teacher1 to every student in group A.
        $this->setAdminUser();
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber <= 3) {
                $assignment->update_marker_allocations($student->id, [
                    1 => [$this->users['teachers'][1]->id],
                ]);
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
        $this->setAdminUser();
        foreach ($this->users['students'] as $studentnumber => $student) {
            if ($studentnumber > 3 && $studentnumber < 6) {
                $assignment->update_marker_allocations($student->id, [
                    1 => [$this->users['teachers'][2]->id],
                ]);
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
     * @covers ::update_marker_allocations, ::update_mark
     */
    public function test_multiple_marker_allocation(): void {

        $this->setup_data();
        $assignment = $this->create_assignment();

        // To start with, confirm that no markers are allocated to the student submission.
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertCount(0, $markers);

        // Wait a small amount of time so we can test whether grade timemodified is updated.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $timemodified = $gradeobject->timemodified;
        sleep(1);

        // Allocate both teachers to the student assignment.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertCount(2, $markers);

        // Changing allocated markers should update grade timemodified as it's used to prevent stale form submissions.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertNotEquals($timemodified, $gradeobject->timemodified);

        // Now test that we can add a mark to the submission.
        // Firstly, there should be no mark currently for either marker.
        $mark = $assignment->get_mark($gradeobject->id, $this->users[0]->id);
        $this->assertNull($mark);

        // Wait a small amount of time so we can test whether grade timemodified is updated.
        $timemodified = $gradeobject->timemodified;
        sleep(1);

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

        // Updating marks should also update grade timemodified.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertNotEquals($timemodified, $gradeobject->timemodified);
    }

    /**
     * Test functionality around having duplicate allocated markers.
     *
     * @covers ::update_marker_allocations
     */
    public function test_duplicate_marker_allocation(): void {
        global $DB;

        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 2,
            'optionalmarkercount' => 1,
        ]);

        // Test duplicate markers in the modified params.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[0]->id],
            3 => [$this->users[0]->id, 1],
        ]);

        $markers = $assignment->get_marker_allocations($this->users[2]->id);

        // Marker 1 should be allocated, 2 unallocated, 3 unallocated but enabled.
        $this->assertEquals($this->users[0]->id, $markers[1]->marker);
        $this->assertFalse(array_key_exists(2, $markers));
        $this->assertEquals(0, $markers[3]->marker);
        $this->assertEquals(1, $markers[3]->enabled);

        // Now we want to test markers when they aren't in the modified params.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
            3 => [$this->users[3]->id, 1],
        ]);

        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertCount(3, $markers);

        // Test allocating a duplicate required marker in a earlier position.
        // The marker in the foremost position should always be kept.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[1]->id],
        ]);

        // Marker 1 should be updated and marker 2 should no longer be allocated.
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertEquals($this->users[1]->id, $markers[1]->marker);
        $this->assertFalse(array_key_exists(2, $markers));

        // Test allocating a duplicate required marker in a later position.
        $assignment->update_marker_allocations($this->users[2]->id, [
            2 => [$this->users[1]->id],
        ]);

        // Marker 1 should not be changed, marker 2 should not be allocated.
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertEquals($this->users[1]->id, $markers[1]->marker);
        $this->assertFalse(array_key_exists(2, $markers));

        // Test allocating a duplicate marker that is already an optional marker.
        // Required markers are always ahead of optional markers so should be kept.
        $assignment->update_marker_allocations($this->users[2]->id, [
            2 => [$this->users[3]->id],
        ]);

        // Marker 3 should no longer be allocated but should still be enabled.
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertEquals($this->users[3]->id, $markers[2]->marker);
        $this->assertEquals(0, $markers[3]->marker);
        $this->assertEquals(1, $markers[3]->enabled);

        // Test that an optional marker cannot be cleared as a duplicate without the capability.
        // Reset the marker allocation.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
            3 => [$this->users[3]->id, 1],
        ]);
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertEquals($this->users[0]->id, $markers[1]->marker);
        $this->assertEquals($this->users[1]->id, $markers[2]->marker);
        $this->assertEquals($this->users[3]->id, $markers[3]->marker);
        $this->assertEquals(1, $markers[3]->enabled);

        // Remove the manage optional allocations capability from editing teachers.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        unassign_capability('mod/assign:manageoptionalallocations', $roleid);
        accesslib_clear_all_caches_for_unit_testing();

        $this->setUser($this->users[0]->id);
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[1]->id],
            2 => [$this->users[3]->id],
        ]);

        // Marker allocation should be unchanged.
        $markers = $assignment->get_marker_allocations($this->users[2]->id);
        $this->assertEquals($this->users[0]->id, $markers[1]->marker);
        $this->assertEquals($this->users[1]->id, $markers[2]->marker);
        $this->assertEquals($this->users[3]->id, $markers[3]->marker);
        $this->assertEquals(1, $markers[3]->enabled);
    }

    /**
     * Test whether marker positions are valid for protecting against stale form submissions.
     *
     * @dataProvider validate_marker_positions_provider
     * @covers ::validate_marker_positions
     *
     * @param array $config Assignment config.
     * @param array $allocated Whether a marker is allocated to a position.
     * @param array $enabled Whether an optional marker is enabled.
     * @param bool $expected
     */
    public function test_validate_marker_positions(array $config, array $allocated, array $enabled, bool $expected): void {
        $this->setup_data();
        $assignment = $this->create_assignment($config);

        // Convert allocated markers to the expected format. We don't need real markers to test the positions.
        $markers = array_map(fn($allocated) => $allocated ? '1' : '', $allocated);

        $this->assertSame($expected, $assignment->validate_marker_positions($markers, $enabled));
    }

    /**
     * Data provider for test_validate_marker_positions().
     *
     * @return array
     */
    public static function validate_marker_positions_provider(): array {
        return [
            'valid required markers' => [
                ['markercount' => 2],
                [1 => true, 2 => true],
                [],
                true,
            ],
            'extra allocated marker' => [
                ['markercount' => 2],
                [1 => true, 2 => true, 3 => true],
                [],
                false,
            ],
            'unused extra marker position' => [
                ['markercount' => 2],
                [1 => true, 2 => true, 3 => false],
                [],
                true,
            ],
            'missing required marker' => [
                ['markercount' => 2],
                [1 => true],
                [],
                true,
            ],
            'valid optional marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [1 => true, 2 => true],
                [2 => true],
                true,
            ],
            'missing enabled status' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [1 => true, 2 => true],
                [],
                false,
            ],
            'extra enabled optional marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [1 => true, 2 => true, 3 => false],
                [2 => true, 3 => true],
                false,
            ],
            'unused extra optional marker status' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [1 => true, 2 => true, 3 => false],
                [2 => true, 3 => false],
                true,
            ],
            'enabled status on required marker' => [
                ['markercount' => 2, 'optionalmarkercount' => 1],
                [1 => true, 2 => true],
                [2 => true],
                false,
            ],
            'enabled status on unallocated required marker' => [
                ['markercount' => 2, 'optionalmarkercount' => 1],
                [1 => true, 2 => false],
                [2 => true],
                false,
            ],
        ];
    }

    /**
     * Test get_modified_marker_allocation().
     *
     * @dataProvider get_modified_marker_allocation_provider
     * @covers ::get_modified_marker_allocation
     *
     * @param array $config Assignment config.
     * @param array $current Current allocated markers.
     * @param array $submittedmarkers Submitted marker IDs.
     * @param array $submittedenabled Submitted enabled status.
     * @param array $expected Expected modified allocations.
     */
    public function test_get_modified_marker_allocation(
        array $config,
        array $current,
        array $submittedmarkers,
        array $submittedenabled,
        array $expected
    ): void {
        $this->setup_data();

        $assignment = $this->create_assignment($config);

        // Map marker ids to test users.
        $mapmarker = fn($marker) => match ($marker) {
            'marker1' => (int)$this->users[0]->id,
            'marker2' => (int)$this->users[1]->id,
            'marker3' => (int)$this->users[3]->id,
            default => $marker,
        };
        $mapallocation = fn($allocation) => [$mapmarker($allocation[0]), ...array_slice($allocation, 1)];

        $current = array_map($mapallocation, $current);
        $submittedmarkers = array_map($mapmarker, $submittedmarkers);
        $expected = array_map($mapallocation, $expected);

        // Setup current allocations.
        $assignment->update_marker_allocations($this->users[2]->id, $current);
        $this->assertCount(count($current), $assignment->get_marker_allocations($this->users[2]->id));

        // Verify modified allocations.
        $modified = $assignment->get_modified_marker_allocation($this->users[2]->id, $submittedmarkers, $submittedenabled);
        $this->assertSame($expected, $modified);
    }

    /**
     * Data provider for test_get_modified_marker_allocation().
     *
     * @return array
     */
    public static function get_modified_marker_allocation_provider(): array {
        return [
            'no changes' => [
                ['markercount' => 2],
                [
                    1 => ['marker1'],
                ],
                [1 => 'marker1'],
                [],
                [],
            ],
            'marker added' => [
                ['markercount' => 2],
                [],
                [1 => 'marker1'],
                [],
                [
                    1 => ['marker1', null],
                ],
            ],
            'marker changed' => [
                ['markercount' => 2],
                [
                    1 => ['marker1'],
                ],
                [1 => 'marker2'],
                [],
                [
                    1 => ['marker2', null],
                ],
            ],
            'marker removed' => [
                ['markercount' => 2],
                [
                    1 => ['marker1'],
                ],
                [1 => 0],
                [],
                [
                    1 => [0, null],
                ],
            ],
            'marker removed with empty submission' => [
                ['markercount' => 2],
                [
                    1 => ['marker1'],
                ],
                [],
                [],
                [
                    1 => [0, null],
                ],
            ],
            'swap positions' => [
                ['markercount' => 2],
                [
                    1 => ['marker1'],
                    2 => ['marker2'],
                ],
                [1 => 'marker2', 2 => 'marker1'],
                [],
                [
                    1 => ['marker2', null],
                    2 => ['marker1', null],
                ],
            ],
            'optional enabled with marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [],
                [2 => 'marker1'],
                [2 => 1],
                [
                    2 => ['marker1', 1],
                ],
            ],
            'optional enabled with no marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [],
                [],
                [2 => 1],
                [
                    2 => [0, 1],
                ],
            ],
            'optional disabled with marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [
                    2 => ['marker1', 1],
                ],
                [2 => 'marker1'],
                [2 => 0],
                [
                    2 => ['marker1', 0],
                ],
            ],
            'optional disabled with no marker' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [
                    2 => ['marker1', 1],
                ],
                [],
                [2 => 0],
                [
                    2 => [0, 0],
                ],
            ],
            'optional disabled with empty submission' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [
                    2 => ['marker1', 1],
                ],
                [],
                [],
                [
                    2 => [0, 0],
                ],
            ],
            'optional with enabled as string' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [],
                [],
                [2 => '0'],
                [],
            ],
            'swap marker type' => [
                ['markercount' => 1, 'optionalmarkercount' => 1],
                [
                    2 => ['marker1', 1],
                ],
                [1 => 'marker1'],
                [],
                [
                    1 => ['marker1', null],
                    2 => [0, 0],
                ],
            ],
        ];
    }

    /**
     * Test whether a user can allocate markers.
     *
     * @covers ::can_allocate_marker
     */
    public function test_can_allocate_marker(): void {
        $this->setup_data();

        $assignment = $this->create_assignment([
            'markercount' => 1,
            'optionalmarkercount' => 1,
        ]);

        // Cannot allocate without capabilities.
        $this->setUser($this->users[2]->id);
        $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 1, '', null));
        $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 2, '', null));

        // Editing teachers have 'manageallocations' and 'manageoptionalallocations' capabilities.
        $this->setUser($this->users[0]->id);

        // Required and optional markers can both be allocated while not marked or in marking.
        foreach (['', ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED, ASSIGN_MARKING_WORKFLOW_STATE_INMARKING] as $workflowstate) {
            $this->assertTrue($assignment->can_allocate_marker($this->users[2]->id, 1, $workflowstate, null));
            $this->assertTrue($assignment->can_allocate_marker($this->users[2]->id, 2, $workflowstate, null));
        }

        // Only optional markers can be allocated while marking completed or in review.
        foreach ([ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW, ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW] as $workflowstate) {
            $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 1, $workflowstate, null));
            $this->assertTrue($assignment->can_allocate_marker($this->users[2]->id, 2, $workflowstate, null));
        }

        // No markers can be allocated while ready for release or released.
        foreach ([ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE, ASSIGN_MARKING_WORKFLOW_STATE_RELEASED] as $workflowstate) {
            $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 1, $workflowstate, null));
            $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 2, $workflowstate, null));
        }

        // Verify reallocating existing markers.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[1]->id],
        ]);
        $currentmarker = $assignment->get_marker_allocations($this->users[2]->id)[1];
        $this->assertEquals($this->users[1]->id, $currentmarker->marker);

        // Can reallocate a marker that hasn't marked.
        $this->assertTrue($assignment->can_allocate_marker($this->users[2]->id, 1, '', $currentmarker));

        // Cannot reallocate markers who are marking or have marked.
        foreach ([ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW] as $markworkflow) {
            $grade = $assignment->get_user_grade($this->users[2]->id, true);
            $grade->grader = $currentmarker->marker;
            $assignment->update_mark($grade, null, $markworkflow);
            $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 1, '', $currentmarker));
        }

        // The 'managemarkedallocations' capability allows reallocating markers who have marked.
        $this->setUser($this->users[3]->id);
        $this->assertTrue($assignment->can_allocate_marker($this->users[2]->id, 1, '', $currentmarker));

        // But this doesn't allow allocation when the workflow state is locked.
        $workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW;
        $this->assertFalse($assignment->can_allocate_marker($this->users[2]->id, 1, $workflowstate, $currentmarker));
    }


    /**
     * Verify direct grading is restricted when not all allocated markers have marked.
     *
     * @covers ::grading_restricted
     */
    public function test_grading_restricted(): void {
        $this->setup_data();
        $this->setUser($this->users[0]->id);

        // Grading is not restricted when multiple markers aren't used.
        $assignment = $this->create_assignment(['markercount' => 1]);
        $this->assertFalse($assignment->grading_restricted(null, $this->users[2]->id));

        // Grading is restricted when no grade record exists.
        $assignment = $this->create_assignment();
        $this->assertTrue($assignment->grading_restricted(null, $this->users[2]->id));

        // Grading is restricted when a grade exists but no markers have marked.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertTrue($assignment->grading_restricted($gradeobject->id, $this->users[2]->id));

        // Grading is restricted when one marker has marked.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 70, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
        $this->assertTrue($assignment->grading_restricted($gradeobject->id, $this->users[2]->id));

        // Grading is not restricted when all markers have marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 50, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
        $this->assertFalse($assignment->grading_restricted($gradeobject->id, $this->users[2]->id));

        // Grading is restricted after a mark is removed.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, null, ASSIGN_MARKING_WORKFLOW_STATE_INMARKING);
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertTrue($assignment->grading_restricted($gradeobject->id, $this->users[2]->id));

        // Users with managerestrictedgrades capability should not be restricted.
        $this->setUser($this->users[3]->id);
        $this->assertFalse($assignment->grading_restricted($gradeobject->id, $this->users[2]->id));
    }

    /**
     * Test validation of mark workflow state and mark combinations.
     *
     * @dataProvider validate_mark_workflow_state_provider
     * @param string|null $workflowstate The marking workflow state to validate.
     * @param float|null $mark Mark value.
     * @param bool $expected Expected validation result.
     * @covers ::validate_mark_workflow_state
     */
    public function test_validate_mark_workflow_state(?string $workflowstate, ?float $mark, bool $expected): void {
        $method = new \ReflectionMethod(assign::class, 'validate_mark_workflow_state');
        $this->assertSame($expected, $method->invoke(null, $workflowstate, $mark));
    }

    /**
     * Data provider for test_validate_mark_workflow_state().
     *
     * @return array
     */
    public static function validate_mark_workflow_state_provider(): array {
        return [
            'null with no mark' => [null, null, true],
            'notmarked with no mark' => ['notmarked', null, true],
            'inmarking with valid mark' => ['inmarking', 75.0, true],
            'inmarking with no mark' => ['inmarking', null, true],
            'readyforreview with valid mark' => ['readyforreview', 75.0, true],
            'readyforreview with no mark' => ['readyforreview', null, false],
            'readyforreview with -1 mark' => ['readyforreview', -1.0, false],
            'inreview with valid mark' => ['inreview', 75.0, false],
            'readyforrelease with valid mark' => ['readyforrelease', 75.0, false],
            'released with valid mark' => ['released', 75.0, false],
        ];
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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 99, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 11, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // With manual calculation, there should be no grade set yet.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);

        // The agreed grade should still meet the conditions to be determined.
        $this->assertTrue($assignment->can_determine_agreed_grade($gradeobject->id, $gradeobject->userid));

        // Set a manual grade.
        $gradeobject->grade = 55;
        $assignment->update_grade($gradeobject);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Confirm that updating a mark does not change a manual grade.
        $assignment->update_mark($gradeobject, 22, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Confirm that removing a mark does not change the manual grade.
        $assignment->update_mark($gradeobject, null, ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED);
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);
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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 11, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 99, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 25, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        // First confirm that the overall grade workflow state is not set.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEmpty($flags->workflowstate);

        // One marker then sets their mark to be in the state "In Marking".
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, null, ASSIGN_MARKING_WORKFLOW_STATE_INMARKING);

        // Re-check the overall workflow. This should now be "In Marking" as well.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $flags->workflowstate);

        // Now this teacher marks theirs as "Marking Complete".
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Nothing should change on the overall state, that should still be In Marking.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $flags->workflowstate);

        // Now the second marker sets theirs as "Marking Complete".
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 70, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Now that both are complete, the overall state should be the same.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW, $flags->workflowstate);

        // If the workflow state has been manually updated to a future state, updates should not override it.
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW;
        $assignment->update_user_flags($flags);

        // Manually update the workflow state to 'In review'.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW, $flags->workflowstate);

        // Trigger calculation.
        $assignment->update_mark($gradeobject, 80, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // The workflow state should not be updated.
        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW, $flags->workflowstate);
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
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Now we remove teacher1 and add manager instead. So we have manager and teacher2 as the markers.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[3]->id],
            2 => [$this->users[1]->id],
        ]);

        // Now add a marker from teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // At this point, though we've had 2 marks, only 1 of the allocated markers has marked.
        // So the grade should not be set.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);
    }

    /**
     * Verify that stale agreed grade calculations are cleared when a mark can no longer be calculated.
     * This should only be triggered when markers are allocated or marks are assigned.
     *
     * @covers ::update_mark, ::update_marker_allocations
     */
    public function test_clear_stale_agreed_grade_calculations(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 2,
            'optionalmarkercount' => 1,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Start with a manual grade that isn't calculated.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grade = 55;
        $assignment->update_grade($gradeobject);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Manual grades should be kept on allocation when a grade cannot be calculated.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Manual grades should be cleared on allocation when an optional marker is enabled.
        $assignment->update_marker_allocations($this->users[2]->id, [
            3 => [0, 1],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);

        // Restore the previous state.
        $gradeobject->grade = 55;
        $assignment->update_grade($gradeobject);

        $assignment->update_marker_allocations($this->users[2]->id, [
            3 => [0, 0],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Manual grades should be kept on marking when a grade cannot be calculated.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(55, $gradeobject->grade);

        // Manual grades should be replaced on marking when a grade can be calculated.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);

        // Calculated agreed grades should be cleared on marking when a grade can no longer be calculated.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, null, ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(-1, $gradeobject->grade);

        // Restore the previous state.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);

        $flags = $assignment->get_user_flags($this->users[2]->id, true);
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INMARKING;
        $assignment->update_user_flags($flags);

        $flags = $assignment->get_user_flags($this->users[2]->id, false);
        $this->assertEquals(ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, $flags->workflowstate);

        // Calculated agreed grades should be cleared on allocation when a grade can no longer be calculated.
        $assignment->update_marker_allocations($this->users[2]->id, [
            2 => [0],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(-1, $gradeobject->grade);
    }

    /**
     * Verify that a final grade is not calculated when an enabled optional marker
     * has not completed marking.
     *
     * @covers ::update_mark
     */
    public function test_enabled_optional_markers_included_in_mark_calculations(): void {
        global $DB;

        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 1,
            'optionalmarkercount' => 1,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Slot 2 is optional and enabled.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id, 1],
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Ensure it is not graded when the enabled optional marker has not marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(-1, $gradeobject->grade);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // All required markers have now marked, so the grade should be set.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(50, $gradeobject->grade);
    }

    /**
     * Verify that disabled optional markers are ignored during grade calculation.
     *
     * @covers ::update_mark
     */
    public function test_disabled_optional_markers_not_included_in_mark_calculations(): void {
        global $DB;

        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 1,
            'optionalmarkercount' => 1,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Teachers are allocated to both marking positions.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id, 0],
        ]);

        // Confirm that marker 2 isn't stored.
        $this->assertCount(1, $assignment->get_marker_allocations($this->users[2]->id));

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);

        // Assign a mark as teacher1.
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // All required markers have marked, so we should have a grade.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(90, $gradeobject->grade);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Ensure the mark is not used in the agreed grade calculation.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(90, $gradeobject->grade);

        // Test with invalid records with disabled optional markers.
        $record = [
            'student' => $this->users[2]->id,
            'assignment' => $assignment->get_instance()->id,
            'marker' => $this->users[1]->id,
            'optional' => true,
            'enabled' => false,
        ];
        $DB->insert_record('assign_allocated_marker', $record);
        $assignment->calculate_and_save_agreed_grade($gradeobject, true);

        // Ensure the mark is not used in the agreed grade calculation.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, false);
        $this->assertEquals(90, $gradeobject->grade);
    }

    /**
     * Verify behaviour when increasing required marker count.
     *
     * @covers ::can_change_marker_count, ::update_instance
     */
    public function test_increasing_marker_count(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 2,
            'optionalmarkercount' => 0,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Can increase when markers are not allocated.
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Can increase when all markers are allocated.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);
        $this->assertEquals(2, $assignment->required_marker_count());
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Assign a mark as teacher1.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // The grade should be calculated as all markers have marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);

        // Can increase when there is an agreed grade.
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Update the assignment settings.
        $this->update_assignment_instance($assignment, ['markercount' => 3]);
        $this->assertEquals(3, $assignment->required_marker_count());

        // The agreed grade should only be changed when the user explicitly selects an update action.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);
    }

    /**
     * Verify behaviour when decreasing required marker count.
     *
     * @covers ::can_change_marker_count, ::update_instance
     */
    public function test_decreasing_marker_count(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 3,
            'optionalmarkercount' => 0,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        // Can decrease when not all markers are allocated.
        $this->assertTrue($assignment->can_change_marker_count(2, false));

        // Assign a mark as teacher1.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // The grade shouldn't be calculated as not all markers have marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(-1, $gradeobject->grade);

        // Update the assignment settings.
        $this->update_assignment_instance($assignment, ['markercount' => 2]);
        $this->assertEquals(2, $assignment->total_marker_count());

        // The grade should now be calculated as all markers have marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);

        // Can't decrease when all markers are allocated.
        $this->assertFalse($assignment->can_change_marker_count(1, false));
    }

    /**
     * Verify behaviour when increasing optional marker count.
     *
     * @covers ::can_change_marker_count, ::update_instance
     */
    public function test_increasing_optional_marker_count(): void {
        global $DB;

        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 2,
            'optionalmarkercount' => 0,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Can increase when markers are not allocated.
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Can increase when all markers are allocated.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);
        $this->assertEquals(2, $assignment->total_marker_count());
        $this->assertEquals(2, $assignment->required_marker_count());
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Assign a mark as teacher1.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 10, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // The grade should be calculated as all markers have marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);

        // Can increase when there is an agreed grade.
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Update the assignment settings.
        $this->update_assignment_instance($assignment, ['markercount' => 2, 'optionalmarkercount' => 1]);
        $this->assertEquals(3, $assignment->total_marker_count());
        $this->assertEquals(2, $assignment->required_marker_count());

        // The optional marker should be unchecked by default.
        $this->assertEquals(2, $assignment->expected_marker_count($this->users[2]->id));

        // The grade should be the same.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(50, $gradeobject->grade);
    }

    /**
     * Verify behaviour when decreasing optional marker count.
     *
     * @covers ::can_change_marker_count, ::update_instance
     */
    public function test_decreasing_optional_marker_count(): void {
        $this->setup_data();
        $assignment = $this->create_assignment([
            'markercount' => 1,
            'optionalmarkercount' => 2,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Can't decrease when there's a user with all optional markers enabled.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id, 1],
            3 => [0, 1],
        ]);
        $this->assertEquals(3, $assignment->expected_marker_count($this->users[2]->id));
        $this->assertFalse($assignment->can_change_marker_count(1, true));

        // Can decrease when there's no users with all optional markers allocated or enabled.
        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id, 1],
            3 => [0, 0],
        ]);
        $this->assertEquals(2, $assignment->expected_marker_count($this->users[2]->id));
        $this->assertTrue($assignment->can_change_marker_count(1, true));

        // Update the assignment settings.
        $this->update_assignment_instance($assignment, ['markercount' => 1, 'optionalmarkercount' => 1]);
        $this->assertEquals(2, $assignment->total_marker_count());
        $this->assertEquals(1, $assignment->required_marker_count());
        $this->assertEquals(2, $assignment->expected_marker_count($this->users[2]->id));

        // Can't decrease when there's a user with all optional markers allocated.
        $this->assertFalse($assignment->can_change_marker_count(0, true));
    }

    /**
     * Verify enabling multi-marking keeps existing grades when no marker marks exist.
     *
     * @covers ::update_instance
     */
    public function test_enabling_multimarking_keeps_existing_grades(): void {
        $this->setup_data();

        $assignment = $this->create_assignment([
            'markercount' => 1,
        ]);

        // Create a standard grade.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grade = 75;
        $assignment->update_grade($gradeobject);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(75, $gradeobject->grade);

        // Enable multi-marking.
        $this->update_assignment_instance($assignment, [
            'markercount' => 2,
            'optionalmarkercount' => 0,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        // Existing grade should be kept until allocated or marked.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(75, $gradeobject->grade);
    }

    /**
     * Verify that grades are recalculated when the marking method changes.
     *
     * @dataProvider changing_grade_calculation_provider
     * @param string $method
     * @param int|null $rounding
     * @param string $action
     * @param float $expectedgrade
     * @covers ::update_instance
     */
    public function test_changing_grade_calculation(string $method, ?int $rounding, string $action, float $expectedgrade): void {
        $this->setup_data();

        $assignment = $this->create_assignment([
            'markercount' => 2,
            'optionalmarkercount' => 0,
            'multimarkmethod' => ASSIGN_MULTIMARKING_METHOD_AVERAGE,
            'multimarkrounding' => ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
        ]);

        $assignment->update_marker_allocations($this->users[2]->id, [
            1 => [$this->users[0]->id],
            2 => [$this->users[1]->id],
        ]);

        // Assign a mark as teacher1.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $gradeobject->grader = $this->users[0]->id;
        $assignment->update_mark($gradeobject, 90, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // Assign a mark as teacher2.
        $gradeobject->grader = $this->users[1]->id;
        $assignment->update_mark($gradeobject, 15, ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);

        // All required markers have marked, so grade is calculated.
        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals(52.5, $gradeobject->grade);

        // Manually update the grade to confirm no changes to the existing strategy.
        $gradeobject->grade = 55;
        $assignment->update_grade($gradeobject);

        // Update the assignment settings.
        $this->update_assignment_instance($assignment, [
            'multimarkmethod' => $method,
            'multimarkrounding' => $rounding,
            'multimarkupdate' => $action,
        ]);

        $gradeobject = $assignment->get_user_grade($this->users[2]->id, true);
        $this->assertEquals($expectedgrade, $gradeobject->grade);
    }

    /**
     * Data provider for test_changing_grade_calculation.
     *
     * @return array[]
     */
    public static function changing_grade_calculation_provider(): array {
        return [
            'manual' => [
                ASSIGN_MULTIMARKING_METHOD_MANUAL,
                null,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                -1,
            ],
            'maximum' => [
                ASSIGN_MULTIMARKING_METHOD_MAX,
                null,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                90,
            ],
            'average' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                ASSIGN_MULTIMARKING_AVERAGE_ROUND_NONE,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                52.5,
            ],
            'average_round_natural' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                ASSIGN_MULTIMARKING_AVERAGE_ROUND_NATURAL,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                53,
            ],
            'average_round_up' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                ASSIGN_MULTIMARKING_AVERAGE_ROUND_UP,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                53,
            ],
            'average_round_down' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                ASSIGN_MULTIMARKING_AVERAGE_ROUND_DOWN,
                ASSIGN_MULTIMARKING_ACTION_RECALCULATE,
                52,
            ],
            'clear' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                null,
                ASSIGN_MULTIMARKING_ACTION_CLEAR,
                -1,
            ],
            'noaction' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                null,
                ASSIGN_MULTIMARKING_ACTION_NONE,
                55,
            ],
            'unselected' => [
                ASSIGN_MULTIMARKING_METHOD_AVERAGE,
                null,
                '',
                55,
            ],
        ];
    }
}
