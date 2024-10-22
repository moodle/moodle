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

namespace report_progress;

use advanced_testcase;
use completion_info;
use testing_data_generator;

/**
 * Class for testing report progress helper.
 *
 * @package   report_progress
 * @covers    \report_progress\local\helper
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
final class report_progress_helper_test extends advanced_testcase {

    /** @var testing_data_generator data generator.*/
    protected $generator;

    /**
     * Set up testcase.
     */
    public function setUp(): void {
        global $CFG;

        parent::setUp();

        $CFG->enablecompletion = true;
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator();
    }

    /**
     * Test process_activities_by_filter_options function.
     */
    public function test_sort_activities(): void {
        $expectedactivitytypes = ['all' => 'All activities and resources', 'assign' => 'Assignments', 'quiz' => 'Quizzes'];

        // Generate test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $quiz2 = $this->generator->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz 2'],
                ['completion' => 1]);
        $quiz1 = $this->generator->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz 1'],
                ['completion' => 1]);
        $assign1 = $this->generator->create_module('assign', ['course' => $course->id, 'name' => 'Assignment'],
                ['completion' => 1]);
        $completion = new completion_info($course);
        // Sort the activities by name.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, 'quiz',
                'alphabetical');

        // Check weather the result is filtered and sorted.
        $this->assertEquals(2, count($activities));
        $this->assertEquals('Quiz 1', $activities[0]->name);
        $this->assertEquals('Quiz 2', $activities[1]->name);
        $this->assertEquals($activitytypes, $expectedactivitytypes);
    }

    /**
     * Test filtering by section.
     */
    public function test_filter_activities_by_section(): void {
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $this->generator->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz 2', 'section' => 1],
                ['completion' => 1]);
        $this->generator->create_module('quiz', ['course' => $course->id, 'name' => 'Quiz 1', 'section' => 2],
                ['completion' => 1]);
        $this->generator->create_module('assign', ['course' => $course->id, 'name' => 'Assignment', 'section' => 2],
                ['completion' => 1]);
        $completion = new completion_info($course);

        // Get activities of section 0.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, '', '', 0);
        $this->assertEquals(0, count($activities));
        $this->assertEquals($activitytypes, ['all' => 'All activities and resources']);

        // Get activities of section 1.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, '', '', 1);
        $this->assertEquals(1, count($activities));
        $this->assertEquals('Quiz 2', array_shift($activities)->name);
        $this->assertEquals($activitytypes, ['all' => 'All activities and resources', 'quiz' => 'Quizzes']);

        // Get activities of section 2.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, '', '', 2);
        $this->assertEquals(2, count($activities));
        $this->assertEquals('Quiz 1', array_shift($activities)->name);
        $this->assertEquals('Assignment', array_shift($activities)->name);
        $this->assertEquals(
                $activitytypes,
                ['all' => 'All activities and resources', 'assign' => 'Assignments', 'quiz' => 'Quizzes']
        );

        // Get assignments of section 2.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, 'assign', '', 2);
        $this->assertEquals(1, count($activities));
        $this->assertEquals('Assignment', array_shift($activities)->name);
        $this->assertEquals(
                $activitytypes,
                ['all' => 'All activities and resources', 'assign' => 'Assignments', 'quiz' => 'Quizzes']
        );

        // Get assignments of section 1.
        list($activitytypes, $activities) = \report_progress\local\helper::get_activities_to_show($completion, 'assign', '', 1);
        $this->assertEquals(0, count($activities));
        $this->assertEquals(
                $activitytypes,
                ['all' => 'All activities and resources', 'assign' => 'Assignments', 'quiz' => 'Quizzes']
        );
    }
}
