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
 * Tests for the progress report sorting.
 *
 * @package   report_progress
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class for testing report progress helper.
 *
 * @copyright 2021 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class report_progress_helper_testcase extends advanced_testcase {

    /**
     * Set up testcase.
     */
    public function setUp(): void {
        global $CFG;

        $CFG->enablecompletion = true;
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->generator = $this->getDataGenerator();
    }

    /**
     * Test process_activities_by_filter_options function.
     */
    public function test_sort_activities() {
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
}
