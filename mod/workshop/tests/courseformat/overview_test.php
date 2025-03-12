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
 * @covers \mod_workshop\course\overview
 * @package    mod_workshop
 * @category   test
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class overview_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/workshop/locallib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test get_grade_item_names method.
     *
     * @dataProvider data_provider_get_grade_item_names
     * @covers ::get_grade_item_names
     * @param string $user
     * @param bool $expectempty
     * @param bool $hassubmission
     * @param bool $hasassesment
     */
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
     * @return array
     */
    public static function data_provider_get_grade_item_names(): array {
        return [
            'student with submissions' => [
                'user' => 'student',
                'expectempty' => false,
                'hassubmission' => true,
                'hasassesment' => false,
            ],
            'student with assessments' => [
                'user' => 'student',
                'expectempty' => false,
                'hassubmission' => true,
                'hasassesment' => true,
            ],
            'teacher' => [
                'user' => 'teacher',
                'expectempty' => true,
                'hassubmission' => false,
                'hasassesment' => false,
            ],
        ];
    }

    /**
     * Test get_extra_phase_overview method.
     *
     * @covers ::get_extra_phase_overview
     * @dataProvider data_provider_get_extra_phase_overview
     * @param string $user
     * @param int $currentphase
     */
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
     * @return array
     */
    public static function data_provider_get_extra_phase_overview(): array {
        return [
            'teacher setup phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SETUP,
            ],
            'student setup phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SETUP,
            ],
            'teacher submission phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SUBMISSION,
            ],
            'student submission phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SUBMISSION,
            ],
            'teacher assessment phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
            ],
            'student assessment phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
            ],
            'teacher evaluation phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_EVALUATION,
            ],
            'student evaluation phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_EVALUATION,
            ],
            'teacher closed phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_CLOSED,
            ],
            'student closed phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_CLOSED,
            ],
        ];
    }

    /**
     * Test get_extra_deadline_overview method.
     *
     * @covers ::get_extra_deadline_overview
     * @dataProvider data_provider_get_extra_deadline_overview
     * @param string $user
     * @param int $currentphase
     * @param int $submissionend
     * @param int $assessmentend
     * @param int|null $expectedincrement null if the item should be null.
     */
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
     * @return array
     */
    public static function data_provider_get_extra_deadline_overview(): array {
        $submissionend = 3600;
        $assessmentend = 7200;
        return [
            'teacher setup phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SETUP,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
            'student setup phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SETUP,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
            'teacher submission phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => $submissionend,
            ],
            'student submission phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => $submissionend,
            ],
            'teacher assessment phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => $assessmentend,
            ],
            'student assessment phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => $assessmentend,
            ],
            'teacher evaluation phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
            'student evaluation phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
            'teacher closed phase' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_CLOSED,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
            'student closed phase' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_CLOSED,
                'submissionend' => $submissionend,
                'assessmentend' => $assessmentend,
                'expectedincrement' => null,
            ],
        ];
    }

    /**
     * Test get_extra_submissions_overview and get_extra_assessments_overview methods.
     *
     * @covers ::get_extra_submissions_overview
     * @covers ::get_extra_assessments_overview
     * @dataProvider data_provider_get_extra_submissions_overview
     * @param string $user
     * @param int $currentphase
     * @param bool $hasstudentactivity
     * @param bool $expectnull
     */
    public function test_get_extra_submissions_overview(
        string $user,
        int $currentphase,
        bool $hasstudentactivity,
        bool $expectnull,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

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
            $submissionid = $generator->create_submission(
                $activity->id,
                $student->id,
                ['title' => 'My custom title', 'grade' => 85.00000],
            );
            $generator->create_submission(
                $activity->id,
                $student2->id,
                ['title' => 'My custom title', 'grade' => 65.00000],
            );
            // Assess one submission.
            $generator->create_assessment(
                $submissionid,
                $student->id,
                ['weight' => 3, 'grade' => 95.00000],
            );
        }

        $manager = new \workshop($activity, $cm, $course, $cm->context);
        $manager->switch_phase($currentphase);

        $currentuser = ($user == 'teacher') ? $teacher : $student;
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
        $expected = ($hasstudentactivity) ? 2 : 0;
        $this->assertEquals($expected, $itemsubmissions->get_value());

        $this->assertEquals(get_string('assessments', 'mod_workshop'), $itemassessment->get_name());
        $expected = ($hasstudentactivity) ? 1 : 0;
        $this->assertEquals($expected, $itemassessment->get_value());
    }

    /**
     * Data provider for test_get_extra_submissions_overview.
     *
     * @return array
     */
    public static function data_provider_get_extra_submissions_overview(): array {
        return [
            'teacher setup phase without activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SETUP,
                'hasstudentactivity' => false,
                'expectnull' => false,
            ],
            'student setup phase without activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SETUP,
                'hasstudentactivity' => false,
                'expectnull' => true,
            ],
            'teacher submission phase without activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'hasstudentactivity' => false,
                'expectnull' => false,
            ],
            'student submission phase without activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'hasstudentactivity' => false,
                'expectnull' => true,
            ],
            'teacher assessment phase without activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'hasstudentactivity' => false,
                'expectnull' => false,
            ],
            'student assessment phase without activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'hasstudentactivity' => false,
                'expectnull' => true,
            ],
            'teacher evaluation phase without activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'hasstudentactivity' => false,
                'expectnull' => false,
            ],
            'student evaluation phase without activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'hasstudentactivity' => false,
                'expectnull' => true,
            ],
            'teacher closed phase without activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_CLOSED,
                'hasstudentactivity' => false,
                'expectnull' => false,
            ],
            'student closed phase without activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_CLOSED,
                'hasstudentactivity' => false,
                'expectnull' => true,
            ],
            // Tests with assessments.
            'teacher setup phase with activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SETUP,
                'hasstudentactivity' => true,
                'expectnull' => false,
            ],
            'student setup phase with activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SETUP,
                'hasstudentactivity' => true,
                'expectnull' => true,
            ],
            'teacher submission phase with activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'hasstudentactivity' => true,
                'expectnull' => false,
            ],
            'student submission phase with activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_SUBMISSION,
                'hasstudentactivity' => true,
                'expectnull' => true,
            ],
            'teacher assessment phase with activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'hasstudentactivity' => true,
                'expectnull' => false,
            ],
            'student assessment phase with activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_ASSESSMENT,
                'hasstudentactivity' => true,
                'expectnull' => true,
            ],
            'teacher evaluation phase with activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'hasstudentactivity' => true,
                'expectnull' => false,
            ],
            'student evaluation phase with activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_EVALUATION,
                'hasstudentactivity' => true,
                'expectnull' => true,
            ],
            'teacher closed phase with activity' => [
                'user' => 'teacher',
                'currentphase' => \workshop::PHASE_CLOSED,
                'hasstudentactivity' => true,
                'expectnull' => false,
            ],
            'student closed phase with activity' => [
                'user' => 'student',
                'currentphase' => \workshop::PHASE_CLOSED,
                'hasstudentactivity' => true,
                'expectnull' => true,
            ],
        ];
    }
}
