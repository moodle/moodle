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

namespace core_courseformat;

/**
 * Tests for course
 *
 * @package    core_courseformat
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activityoverviewbase::class)]
final class activityoverviewbase_test extends \advanced_testcase {
    #[\Override()]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/fake_activityoverview.php');
        require_once($CFG->libdir . '/gradelib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test get_name_overview method.
     */
    public function test_get_name_overview(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Test!']);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $result = $overview->get_name_overview();
        $this->assertEquals(get_string('name'), $result->get_name());
        $this->assertEquals('Test!', $result->get_value());
        $this->assertInstanceOf(\core_courseformat\output\local\overview\activityname::class, $result->get_content());
    }

    /**
     * Test get_completion_overview method.
     *
     * @param int $setcompletion the completion status
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_get_completion_overview')]
    public function test_get_completion_overview(
        int $setcompletion,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'completion' => \COMPLETION_TRACKING_AUTOMATIC]
        );

        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $completion = (object) [
            'coursemoduleid' => $cm->id,
            'timemodified' => time(),
            'viewed' => \COMPLETION_NOT_VIEWED,
            'overrideby' => null,
            'id' => 0,
            'completionstate' => $setcompletion,
            'userid' => $user->id,
        ];
        $comletioninfo = new \completion_info($course);
        $comletioninfo->internal_set_data($cm, $completion, true);

        $this->setUser($user);

        $result = $overview->get_completion_overview();
        $this->assertEquals(get_string('completion_status', 'completion'), $result->get_name());
        $this->assertEquals($setcompletion, $result->get_value());
        $this->assertInstanceOf(\core_courseformat\output\local\content\cm\completion::class, $result->get_content());
    }

    /**
     * Data provider for test_get_completion_overview.
     *
     * @return \Generator the testing scenarios
     */
    public static function provider_get_completion_overview(): \Generator {
        yield 'complet' => [
            'setcompletion' => \COMPLETION_COMPLETE,
        ];
        yield 'incomplete' => [
            'setcompletion' => \COMPLETION_INCOMPLETE,
        ];
        yield 'complete pass' => [
            'setcompletion' => \COMPLETION_COMPLETE_PASS,
        ];
        yield 'complete fail' => [
            'setcompletion' => \COMPLETION_COMPLETE_FAIL,
        ];
    }

    /**
     * Test get_completion_overview method on an activity with no completion.
     */
    public function test_get_completion_overview_no_completion(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['enablecompletion' => 1]);

        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id);

        $this->setAdminUser();

        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );

        rebuild_course_cache($course->id, true);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);

        $overview = new \core_courseformat\fake_activityoverview($cm);

        $this->setUser($user);

        $result = $overview->get_completion_overview();
        $this->assertEquals(get_string('completion_status', 'completion'), $result->get_name());
        $this->assertEquals(null, $result->get_value());
        $this->assertEquals('-', $result->get_content());
    }

    /**
     * Test get_grades_overviews method.
     */
    public function test_get_grades_overviews(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create some modules.
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );
        $workshop = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id],
            ['grade' => 100.0],
        );
        $page = $this->getDataGenerator()->create_module(
            'page',
            ['course' => $course->id]
        );

        // Assignments have one grade item.
        $assignitems = \grade_item::fetch_all([
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => (int) $assign->id,
            'courseid' => $course->id,
        ]);
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = reset($assignitems)->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 88;
        $gradegrade->finalgrade = 88;
        $gradegrade->insert();

        // Workshops have two grade items.
        $workshopitems = array_values(
            \grade_item::fetch_all([
                'itemtype' => 'mod',
                'itemmodule' => 'workshop',
                'iteminstance' => (int) $workshop->id,
                'courseid' => $course->id,
                ['grade' => 100.0],
            ])
        );
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = reset($workshopitems)->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 77;
        $gradegrade->finalgrade = 77;
        $gradegrade->insert();

        // Validate student grades.
        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertCount(1, $result);
        $this->assertEquals(get_string('gradenoun'), $result[0]->get_name());
        $this->assertEquals(88, $result[0]->get_value());
        $this->assertEquals('88.00', $result[0]->get_content());

        // Validate workshop gradeitems (having two grade, they should return an empty array).
        $cm = $modinfo->get_cm($workshop->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate page has no gradeitems.
        $cm = $modinfo->get_cm($page->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate teacher does not has grade overiviews items.
        $this->setUser($teacher);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate workshop gradeitems (having two grade, they should return an empty array).
        $cm = $modinfo->get_cm($workshop->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);

        // Validate page has no gradeitems.
        $cm = $modinfo->get_cm($page->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertEmpty($result);
    }

    /**
     * Test get_grades_overviews method.
     */
    public function test_get_grades_overviews_hidden(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $course = $this->getDataGenerator()->create_course();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create some modules.
        $assign = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id]
        );

        // Assignments have one grade item.
        $assignitems = \grade_item::fetch_all([
            'itemtype' => 'mod',
            'itemmodule' => 'assign',
            'iteminstance' => (int) $assign->id,
            'courseid' => $course->id,
        ]);
        $gradeitem = reset($assignitems);
        $gradegrade = new \grade_grade();
        $gradegrade->itemid = $gradeitem->id;
        $gradegrade->userid = (int) $student->id;
        $gradegrade->rawgrade = 88;
        $gradegrade->finalgrade = 88;
        $gradegrade->insert();

        // Hide some grades.
        $gradeitem->set_hidden(1, true);

        // Validate student grades.
        $this->setUser($student);
        $modinfo = get_fast_modinfo($course);

        // Validate assign gradeitems.
        $cm = $modinfo->get_cm($assign->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_grades_overviews();
        $this->assertCount(1, $result);
        $this->assertEquals(get_string('gradenoun'), $result[0]->get_name());
        $this->assertEquals('-', $result[0]->get_value());
        $this->assertEquals('-', $result[0]->get_content());
    }

    /**
     * Test needs_filtering_by_groups method.
     *
     *
     * @param string $role of the user to test
     * @param int $groupmode of the activity to test
     * @param bool $expected result.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_needs_filtering_by_groups')]
    public function test_needs_filtering_by_groups(string $role, int $groupmode, bool $expected): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'groupmode' => $groupmode]
        );
        $user = $this->getDataGenerator()->create_and_enrol($course, $role);
        $this->setUser($user);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $this->assertEquals($expected, $overview->needs_filtering_by_groups());
    }

    /**
     * Data provider for test_needs_filtering_by_groups.
     *
     * @return \Generator the testing scenarios
     */
    public static function provider_needs_filtering_by_groups(): \Generator {
        yield 'Editing teacher with no groups' => [
            'role' => 'editingteacher',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Editing teacher with visible groups' => [
            'role' => 'editingteacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Editing teacher with separate groups' => [
            'role' => 'editingteacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with no groups' => [
            'role' => 'teacher',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with visible groups' => [
            'role' => 'teacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with separate groups' => [
            'role' => 'teacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => true,
        ];
        yield 'Student with no groups' => [
            'role' => 'student',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Student with visible groups' => [
            'role' => 'student',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Student with separate groups' => [
            'role' => 'student',
            'groupmode' => SEPARATEGROUPS,
            'expected' => true,
        ];
    }

    /**
     * Test needs_filtering_by_groups method.
     *
     * @param string $role of the user to test
     * @param int $groupmode of the activity to test
     * @param array $expected result
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_get_groups_for_filtering')]
    public function test_get_groups_for_filtering(string $role, int $groupmode, array $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $generator->create_module(
            'assign',
            ['course' => $course->id, 'groupmode' => $groupmode]
        );
        $user = $generator->create_and_enrol($course, $role);
        $g1 = $generator->create_group(['courseid' => $course->id, 'name' => 'g1']);
        $g2 = $generator->create_group(['courseid' => $course->id, 'name' => 'g2']);
        $g3 = $generator->create_group(['courseid' => $course->id, 'name' => 'g3']);

        // We add user to g1 and g2 only.
        groups_add_member($g1, $user);
        groups_add_member($g2, $user);

        $this->setUser($user);
        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $result = $overview->get_groups_for_filtering();
        if (!$expected) {
            $this->assertEquals($expected, $result);
        } else {
            foreach ($result as $group) {
                $this->assertContains($group->name, $expected);
            }
        }
    }

    /**
     * Data provider for test_get_groups_for_filtering.
     *
     * @return \Generator the testing scenarios
     */
    public static function provider_get_groups_for_filtering(): \Generator {
        yield 'Editing teacher with no groups' => [
            'role' => 'editingteacher',
            'groupmode' => NOGROUPS,
            'expected' => [],
        ];
        yield 'Editing teacher with visible groups' => [
            'role' => 'editingteacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => [],
        ];
        yield 'Editing teacher with separate groups' => [
            'role' => 'editingteacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => [],
        ];
        yield 'Non-editing teacher with no groups' => [
            'role' => 'teacher',
            'groupmode' => NOGROUPS,
            'expected' => [],
        ];
        yield 'Non-editing teacher with visible groups' => [
            'role' => 'teacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => ['g1', 'g2', 'g3'],
        ];
        yield 'Non-editing teacher with separate groups' => [
            'role' => 'teacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => ['g1', 'g2'],
        ];
        yield 'Student with no groups' => [
            'role' => 'student',
            'groupmode' => NOGROUPS,
            'expected' => [],
        ];
        yield 'Student with visible groups' => [
            'role' => 'student',
            'groupmode' => VISIBLEGROUPS,
            'expected' => ['g1', 'g2', 'g3'],
        ];
        yield 'Student with separate groups' => [
            'role' => 'student',
            'groupmode' => SEPARATEGROUPS,
            'expected' => ['g1', 'g2'],
        ];
    }

    /**
     * Test has_error method.
     *
     *
     * @param string $role of the user to test
     * @param int $groupmode of the activity to test
     * @param bool $expected result
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_has_error')]
    public function test_has_error(string $role, int $groupmode, bool $expected): void {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $activity = $generator->create_module(
            'assign',
            ['course' => $course->id, 'groupmode' => $groupmode]
        );
        $user = $generator->create_and_enrol($course, $role);
        $g1 = $generator->create_group(['courseid' => $course->id, 'name' => 'g1']);
        $g2 = $generator->create_group(['courseid' => $course->id, 'name' => 'g2']);

        $this->setUser($user);

        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);
        $this->assertEquals($expected, $overview->has_error());

        // We add user to g1.
        groups_add_member($g1, $user);

        $modinfo = get_fast_modinfo($course);
        $cm = $modinfo->get_cm($activity->cmid);
        $overview = new \core_courseformat\fake_activityoverview($cm);

        $this->assertfalse($overview->has_error());
    }

    /**
     * Data provider for test_has_error.
     *
     * @return \Generator the testing scenarios
     */
    public static function provider_has_error(): \Generator {
        yield 'Editing teacher with no groups' => [
            'role' => 'editingteacher',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Editing teacher with visible groups' => [
            'role' => 'editingteacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Editing teacher with separate groups' => [
            'role' => 'editingteacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with no groups' => [
            'role' => 'teacher',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with visible groups' => [
            'role' => 'teacher',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Non-editing teacher with separate groups' => [
            'role' => 'teacher',
            'groupmode' => SEPARATEGROUPS,
            'expected' => true,
        ];
        yield 'Student with no groups' => [
            'role' => 'student',
            'groupmode' => NOGROUPS,
            'expected' => false,
        ];
        yield 'Student with visible groups' => [
            'role' => 'student',
            'groupmode' => VISIBLEGROUPS,
            'expected' => false,
        ];
        yield 'Student with separate groups' => [
            'role' => 'student',
            'groupmode' => SEPARATEGROUPS,
            'expected' => true,
        ];
    }
}
