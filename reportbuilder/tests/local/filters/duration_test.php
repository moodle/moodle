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

declare(strict_types=1);

namespace core_reportbuilder\local\filters;

use advanced_testcase;
use lang_string;
use core_reportbuilder\local\report\filter;

/**
 * Unit tests for duration report filter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\filters\base
 * @covers      \core_reportbuilder\local\filters\duration
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class duration_test extends advanced_testcase {

    /**
     * Data provider for {@see test_get_sql_filter}
     *
     * @return array
     */
    public function get_sql_filter_provider(): array {
        return [
            'Any duration' =>
                [duration::DURATION_ANY, true],

            // Maximum operator.
            'Maximum seconds non-match' =>
                [duration::DURATION_MAXIMUM, false, HOURSECS, 1],
            'Maximum seconds match' =>
                [duration::DURATION_MAXIMUM, true, HOURSECS * 3, 1],
            'Maximum minutes non-match' =>
                [duration::DURATION_MAXIMUM, false, 60, MINSECS],
            'Maximum minutes match' =>
                [duration::DURATION_MAXIMUM, true, 150, MINSECS],
            'Maximum hours non-match (float)' =>
                [duration::DURATION_MAXIMUM, false, 0.5, HOURSECS],
            'Maximum hours non-match' =>
                [duration::DURATION_MAXIMUM, false, 1, HOURSECS],
            'Maximum hours match (float)' =>
                [duration::DURATION_MAXIMUM, true, 2.5, HOURSECS],
            'Maximum hours match' =>
                [duration::DURATION_MAXIMUM, true, 3, HOURSECS],

            // Minimum operator.
            'Minimum seconds match' =>
                [duration::DURATION_MINIMUM, true, HOURSECS, 1],
            'Minimum seconds non-match' =>
                [duration::DURATION_MINIMUM, false, HOURSECS * 3, 1],
            'Minimum minutes match' =>
                [duration::DURATION_MINIMUM, true, 60, MINSECS],
            'Minimum minutes non-match' =>
                [duration::DURATION_MINIMUM, false, 150, MINSECS],
            'Minimum hours match (float)' =>
                [duration::DURATION_MINIMUM, true, 0.5, HOURSECS],
            'Minimum hours match' =>
                [duration::DURATION_MINIMUM, true, 1, HOURSECS],
            'Minimum hours non-match (float)' =>
                [duration::DURATION_MINIMUM, false, 2.5, HOURSECS],
            'Minimum hours non-match' =>
                [duration::DURATION_MINIMUM, false, 3, HOURSECS],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param int $operator
     * @param bool $expectuser
     * @param float $value
     * @param int $unit
     *
     * @dataProvider get_sql_filter_provider
     */
    public function test_get_sql_filter(int $operator, bool $expectuser, float $value = 0, int $unit = MINSECS): void {
        global $DB;

        $this->resetAfterTest();

        // We are going to enrol our student from now, with a duration of two hours (timeend is two hours later).
        $timestart = time();
        $timeend = $timestart + (HOURSECS * 2);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student', null, 'manual', $timestart, $timeend);

        $filter = new filter(
            duration::class,
            'test',
            new lang_string('yes'),
            'testentity',
            'timeend - timestart'
        );

        // Create instance of our filter, passing given values.
        [$select, $params] = duration::create($filter)->get_sql_filter([
            $filter->get_unique_identifier() . '_operator' => $operator,
            $filter->get_unique_identifier() . '_value' => $value,
            $filter->get_unique_identifier() . '_unit' => $unit,
        ]);

        $useridfield = $DB->get_field_select('user_enrolments', 'userid', $select, $params);
        if ($expectuser) {
            $this->assertEquals($useridfield, $user->id);
        } else {
            $this->assertFalse($useridfield);
        }
    }
}
