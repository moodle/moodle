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

namespace tool_usertours;

use tool_usertours\local\filter\course;

/**
 * Tests for course filter.
 *
 * @package    tool_usertours
 * @copyright  2025 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \tool_usertours\local\filter\course
 */
final class course_filter_test extends \advanced_testcase {
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Data Provider for filter_matches method.
     *
     * @return array
     */
    public static function filter_matches_provider(): array {
        return [
            'No filter set; Matches' => [
                'all',
                true,
            ],
            'Select specific courses; Match' => [
                'select',
                true,
            ],
            'Select specific courses; No match' => [
                'select',
                false,
            ],
            'Except specific courses; Match' => [
                'except',
                true,
            ],
            'Except specific courses; No match' => [
                'except',
                false,
            ],
        ];
    }

    /**
     * Test filter matches.
     *
     * @dataProvider filter_matches_provider
     *
     * @param string $operator the filter operator.
     * @param bool $expected result expected.
     */
    public function test_filter_matches(string $operator, bool $expected): void {
        global $COURSE;

        // Create courses for testing.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        // Set global $COURSE variable to the first course created.
        $COURSE = $course1;

        $tour = new tour();
        if ($operator === course::OPERATOR_SELECT) {
            // Test case for selecting specific courses.
            $tour->set_filter_values('course', $expected ? [$course1->id, $course2->id] : [$course2->id, $course3->id]);
        } else if ($operator === course::OPERATOR_EXCEPT) {
            // Test case for excluding specific courses.
            $tour->set_filter_values('course', $expected ? [$course2->id, $course3->id] : [$course1->id, $course2->id]);
        }

        $tour->set_filter_values('course_operator', [$operator]);

        $context = \context_course::instance($COURSE->id);

        $this->assertEquals($expected, course::filter_matches($tour, $context));
    }

    /**
     * Test validating course selection.
     */
    public function test_validate_form(): void {
        $fields = [
            'filter_course_operator' => course::OPERATOR_SELECT,
            'filter_course' => [],
        ];
        $errors = [];

        $errors = course::validate_form($fields, $errors);

        $this->assertArrayHasKey('filter_course', $errors);
        $this->assertEquals(
            get_string('filter_course_error_course_selection', 'tool_usertours'),
            $errors['filter_course']
        );
    }
}
