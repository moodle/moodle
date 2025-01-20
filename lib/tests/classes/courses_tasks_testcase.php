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

namespace core\tests;

/**
 * TODO describe file courses_tasks_testcase
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class courses_tasks_testcase extends \advanced_testcase {
    /**
     * Data provider for test_show_started_courses.
     *
     * @return array
     */
    public static function get_courses_provider(): array {
        return [
            'No hidden courses' => [
                'lastweekcount' => 0,
                'yesterdaycount' => 0,
                'tomorrowcount' => 0,
            ],
            'No hidden courses (without visible courses)' => [
                'lastweekcount' => 0,
                'yesterdaycount' => 0,
                'tomorrowcount' => 0,
                'createvisible' => false,
            ],
            'Hidden courses with last week or tomorrow dates' => [
                'lastweekcount' => 2,
                'yesterdaycount' => 0,
                'tomorrowcount' => 2,
            ],
            'One hidden course of each type (last week, yesterday and tomorrow)' => [
                'lastweekcount' => 1,
                'yesterdaycount' => 1,
                'tomorrowcount' => 1,
            ],
            'Different hidden courses of each type' => [
                'lastweekcount' => 2,
                'yesterdaycount' => 3,
                'tomorrowcount' => 4,
            ],
            'A couple of hidden courses of each type (without visible courses)' => [
                'lastweekcount' => 2,
                'yesterdaycount' => 2,
                'tomorrowcount' => 2,
                'createvisible' => false,
            ],
            'Only a few hidden courses for yesterdaycount' => [
                'lastweekcount' => 0,
                'yesterdaycount' => 5,
                'tomorrowcount' => 0,
            ],
            'Only a few hidden courses for yesterday (without visible courses)' => [
                'lastweekcount' => 0,
                'yesterdaycount' => 5,
                'tomorrowcount' => 0,
                'createvisible' => false,
            ],
        ];
    }
}
