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

namespace mod_workshop\courseformat;

use core_courseformat\local\overview\overviewfactory;

/**
 * Tests for Workshop overview integration.
 *
 * @package    mod_workshop
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;

        parent::setUpBeforeClass();

        require_once($CFG->dirroot . '/mod/workshop/locallib.php');
    }

    /**
     * Test get_grade_item_names method.
     *
     * @param string $user
     * @param bool $expectempty
     * @param bool $hassubmission
     * @param bool $hasassesment
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_grade_item_names')]
    public function test_get_grade_item_names(
        string $user,
        bool $expectempty,
        bool $hassubmission,
        bool $hasassesment,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Set up a generator to create content.
        /** @var \mod_workshop_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        // Add grading.
        $workshopitems = array_values(
            \grade_item::fetch_all([
                'itemtype' => 'mod',
                'itemmodule' => 'workshop',
                'iteminstance' => (int) $activity->id,
                'courseid' => $course->id,
                ['grade' => 100.0],
            ])
        );

        // Workshop stores in number 1 the assessment grade and in number 0 the submission grade.
        $gradeitems = [];
        foreach ($workshopitems as $workshopitem) {
            if ($workshopitem->itemnumber == 0) {
                $gradeitems['submission'] = $workshopitem;
            } else {
                $gradeitems['assessment'] = $workshopitem;
            }
        }

        $expectedsubmissions = '-';
        if ($hassubmission) {
            $submissionid = $generator->create_submission(
                $activity->id,
                $student->id,
                ['title' => 'My custom title', 'grade' => 85.00000],
            );
            $gradegrade = new \grade_grade();
            $gradegrade->itemid = $gradeitems['submission']->id;
            $gradegrade->userid = (int) $student->id;
            $gradegrade->rawgrade = 77;
            $gradegrade->finalgrade = 77;
            $gradegrade->insert();
            $expectedsubmissions = '77.00000';
        }

        $expectedassessments = '-';
        if ($hasassesment) {
            $generator->create_assessment(
                $submissionid,
                $student->id,
                ['weight' => 3, 'grade' => 95.00000],
            );
            $gradegrade = new \grade_grade();
            $gradegrade->itemid = $gradeitems['assessment']->id;
            $gradegrade->userid = (int) $student->id;
            $gradegrade->rawgrade = 88;
            $gradegrade->finalgrade = 88;
            $gradegrade->insert();
            $expectedassessments = '88.00000';
        }

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $items = overviewfactory::create($cm)->get_grades_overviews();

        // Students should not see item.
        if ($expectempty) {
            $this->assertEmpty($items);
            return;
        }

        $this->assertEquals(get_string('overview_submission_grade', 'mod_workshop'), $items[0]->get_name());
        $this->assertEquals($expectedsubmissions, $items[0]->get_value());

        $this->assertEquals(get_string('overview_assessment_grade', 'mod_workshop'), $items[1]->get_name());
        $this->assertEquals($expectedassessments, $items[1]->get_value());
    }

    /**
     * Data provider for test_get_grade_item_names.
     *
     * @return \Generator
     */
    public static function data_provider_get_grade_item_names(): \Generator {
        yield 'student with submissions' => [
            'user' => 'student',
            'expectempty' => false,
            'hassubmission' => true,
            'hasassesment' => false,
        ];
        yield 'student with assessments' => [
            'user' => 'student',
            'expectempty' => false,
            'hassubmission' => true,
            'hasassesment' => true,
        ];
        yield 'teacher' => [
            'user' => 'teacher',
            'expectempty' => true,
            'hassubmission' => false,
            'hasassesment' => false,
        ];
    }

    /**
     * Test get_extra_phase_overview method.
     *
     * @param string $user
     * @param int $currentphase
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_extra_phase_overview')]
    public function test_get_extra_phase_overview(string $user, int $currentphase): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $manager = new \workshop($activity, $cm, $course, $cm->context);
        $manager->switch_phase($currentphase);

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_phase_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        $this->assertEquals(get_string('phase', 'mod_workshop'), $item->get_name());
        $this->assertEquals($currentphase, $item->get_value());
    }

    /**
     * Data provider for test_get_extra_phase_overview.
     *
     * @return \Generator
     */
    public static function data_provider_get_extra_phase_overview(): \Generator {
        yield 'teacher setup phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_SETUP,
        ];
        yield 'student setup phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_SETUP,
        ];
        yield 'teacher submission phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_SUBMISSION,
        ];
        yield 'student submission phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_SUBMISSION,
        ];
        yield 'teacher assessment phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
        ];
        yield 'student assessment phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
        ];
        yield 'teacher evaluation phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_EVALUATION,
        ];
        yield 'student evaluation phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_EVALUATION,
        ];
        yield 'teacher closed phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_CLOSED,
        ];
        yield 'student closed phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
        ];
    }

    /**
     * Test get_extra_deadline_overview method.
     *
     * @param string $user
     * @param int $currentphase
     * @param int $submissionend
     * @param int $assessmentend
     * @param int|null $expectedincrement null if the item should be null.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_extra_deadline_overview')]
    public function test_get_extra_deadline_overview(
        string $user,
        int $currentphase,
        int $submissionend,
        int $assessmentend,
        ?int $expectedincrement,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $current = $this->mock_clock_with_frozen()->time();

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            [
                'course' => $course->id,
                'submissionend' => $current + $submissionend,
                'assessmentend' => $current + $assessmentend,
            ],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        $manager = new \workshop($activity, $cm, $course, $cm->context);
        $manager->switch_phase($currentphase);

        $currentuser = ($user == 'teacher') ? $teacher : $student;
        $this->setUser($currentuser);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);
        $method = $reflection->getMethod('get_extra_deadline_overview');
        $method->setAccessible(true);
        $item = $method->invoke($overview);

        $this->assertEquals(get_string('deadline', 'mod_workshop'), $item->get_name());

        if ($expectedincrement === null) {
            $this->assertNull($item->get_value());
            return;
        }

        $this->assertEquals($current + $expectedincrement, $item->get_value());
    }

    /**
     * Data provider for test_get_extra_phase_overview.
     *
     * @return \Generator
     */
    public static function data_provider_get_extra_deadline_overview(): \Generator {
        $submissionend = 3600;
        $assessmentend = 7200;
        yield 'teacher setup phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_SETUP,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
        yield 'student setup phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_SETUP,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
        yield 'teacher submission phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => $submissionend,
        ];
        yield 'student submission phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => $submissionend,
        ];
        yield 'teacher assessment phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => $assessmentend,
        ];
        yield 'student assessment phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => $assessmentend,
        ];
        yield 'teacher evaluation phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
        yield 'student evaluation phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
        yield 'teacher closed phase' => [
            'user' => 'teacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
        yield 'student closed phase' => [
            'user' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
            'submissionend' => $submissionend,
            'assessmentend' => $assessmentend,
            'expectedincrement' => null,
        ];
    }

    /**
     * Test get_extra_submissions_overview and get_extra_assessments_overview methods.
     *
     * @param string $role
     * @param int $currentphase
     * @param int $groupmode
     * @param bool $hasstudentactivity
     * @param bool $expectnull
     * @param array $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('data_provider_get_extra_submissions_overview')]
    public function test_get_extra_submissions_overview(
        string $role,
        int $currentphase,
        int $groupmode,
        bool $hasstudentactivity,
        bool $expectnull,
        array $expected = [],
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);

        if ($groupmode != NOGROUPS) {
            $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $currentuser->id, 'groupid' => $group1->id]);
            $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        }

        $activity = $this->getDataGenerator()->create_module(
            'workshop',
            ['course' => $course->id, 'groupmode' => $groupmode],
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);

        // Set up a generator to create content.
        /** @var \mod_workshop_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_workshop');

        // Add grading.
        $workshopitems = array_values(
            \grade_item::fetch_all([
                'itemtype' => 'mod',
                'itemmodule' => 'workshop',
                'iteminstance' => (int) $activity->id,
                'courseid' => $course->id,
                ['grade' => 100.0],
            ])
        );

        // Worksop stores un number 1 the assessment grade and in number 0 the submission grade.
        foreach ($workshopitems as $workshopitem) {
            if ($workshopitem->itemnumber == 0) {
                $gradeitems['submission'] = $workshopitem;
            } else {
                $gradeitems['assessment'] = $workshopitem;
            }
        }

        if ($hasstudentactivity) {
            // Create some submissions.
            $student1submissionid = $generator->create_submission(
                $activity->id,
                $student1->id,
                ['title' => 'My custom title', 'grade' => 85.00000],
            );
            $student2submissionid = $generator->create_submission(
                $activity->id,
                $student2->id,
                ['title' => 'My custom title', 'grade' => 65.00000],
            );
            // Assess some submissions.
            $generator->create_assessment(
                $student1submissionid,
                $currentuser->id,
                ['weight' => 3, 'grade' => 45.00000],
            );
            $generator->create_assessment(
                $student2submissionid,
                $currentuser->id,
                ['weight' => 3, 'grade' => 55.00000],
            );
            $generator->create_assessment(
                $student2submissionid,
                $currentuser->id,
                ['weight' => 3, 'grade' => 65.00000],
            );
        }

        $manager = new \workshop($activity, $cm, $course, $cm->context);
        $manager->switch_phase($currentphase);

        $this->setUser($currentuser);

        $overview = overviewfactory::create($cm);
        $reflection = new \ReflectionClass($overview);

        $method = $reflection->getMethod('get_extra_submissions_overview');
        $method->setAccessible(true);
        $itemsubmissions = $method->invoke($overview);

        $method = $reflection->getMethod('get_extra_assessments_overview');
        $method->setAccessible(true);
        $itemassessment = $method->invoke($overview);

        if ($expectnull) {
            $this->assertNull($itemsubmissions);
            $this->assertNull($itemassessment);
            return;
        }

        $this->assertEquals(get_string('submissions', 'mod_workshop'), $itemsubmissions->get_name());
        $this->assertEquals($expected['submissions'], $itemsubmissions->get_value());

        $this->assertEquals(get_string('assessments', 'mod_workshop'), $itemassessment->get_name());
        $this->assertEquals($expected['assessments'], $itemassessment->get_value());
    }

    /**
     * Data provider for test_get_extra_submissions_overview.
     *
     * @return \Generator
     */
    public static function data_provider_get_extra_submissions_overview(): \Generator {
        yield 'teacher setup phase without activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_SETUP,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => false,
            'expected' => ['submissions' => 0, 'assessments' => 0],
        ];
        yield 'student setup phase without activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_SETUP,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => true,
        ];
        yield 'teacher submission phase without activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => false,
            'expected' => ['submissions' => 0, 'assessments' => 0],
        ];
        yield 'student submission phase without activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => true,
        ];
        yield 'teacher assessment phase without activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => false,
            'expected' => ['submissions' => 0, 'assessments' => 0],
        ];
        yield 'student assessment phase without activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => true,
        ];
        yield 'teacher evaluation phase without activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => false,
            'expected' => ['submissions' => 0, 'assessments' => 0],
        ];
        yield 'student evaluation phase without activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => true,
        ];
        yield 'teacher closed phase without activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => false,
            'expected' => ['submissions' => 0, 'assessments' => 0],
        ];
        yield 'student closed phase without activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => false,
            'expectnull' => true,
        ];
        // Tests with assessments.
        yield 'teacher setup phase with activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_SETUP,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student setup phase with activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_SETUP,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        yield 'teacher submission phase with activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student submission phase with activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_SUBMISSION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        yield 'teacher assessment phase with activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student assessment phase with activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_ASSESSMENT,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        yield 'teacher evaluation phase with activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student evaluation phase with activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_EVALUATION,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        yield 'teacher closed phase with activity' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student closed phase with activity' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => NOGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        // Group mode tests.
        yield 'teacher closed phase with group activity (separate groups)' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => SEPARATEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'non-editing teacher closed phase with group activity (separate groups)' => [
            'role' => 'teacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => SEPARATEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 1, 'assessments' => 1],
        ];
        yield 'student closed phase with group activity (separate groups)' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => SEPARATEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
        yield 'teacher closed phase with group activity (visible groups)' => [
            'role' => 'editingteacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => VISIBLEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'non-editing teacher closed phase with group activity (visible groups)' => [
            'role' => 'teacher',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => VISIBLEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => false,
            'expected' => ['submissions' => 2, 'assessments' => 3],
        ];
        yield 'student closed phase with group activity (visible groups)' => [
            'role' => 'student',
            'currentphase' => \workshop::PHASE_CLOSED,
            'groupmode' => VISIBLEGROUPS,
            'hasstudentactivity' => true,
            'expectnull' => true,
        ];
    }

    /**
     * Test get_actions_overview.
     *
     * @param string $role
     * @param array|null $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_get_actions_overview')]
    public function test_get_actions_overview(
        string $role,
        ?array $expected
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $currentuser = $this->getDataGenerator()->create_and_enrol($course, $role);
        $activity = $this->getDataGenerator()->create_module('workshop', ['course' => $course->id]);

        $this->setUser($currentuser);

        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $item = overviewfactory::create($cm)->get_actions_overview();

        if ($expected === null) {
            $this->assertNull($item);
            return;
        }

        $this->assertEquals(
            $expected,
            ['name' => $item->get_name(), 'value' => $item->get_value()]
        );
    }

    /**
     * Data provider for test_get_actions_overview.
     *
     * @return \Generator
     */
    public static function provider_test_get_actions_overview(): \Generator {
        yield 'Student' => [
            'role' => 'student',
            'expected' => null,
        ];
        yield 'Editing teacher' => [
            'role' => 'editingteacher',
            'expected' => [
            'name' => get_string('actions'),
            'value' => get_string('view'),
            ],
        ];
        yield 'Teacher' => [
            'role' => 'teacher',
            'expected' => [
            'name' => get_string('actions'),
            'value' => get_string('view'),
            ],
        ];
    }
}
