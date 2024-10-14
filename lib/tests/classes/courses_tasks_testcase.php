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
                'lastweek' => 0,
                'yesterday' => 0,
                'tomorrow' => 0,
            ],
            'No hidden courses (without visible courses)' => [
                'lastweek' => 0,
                'yesterday' => 0,
                'tomorrow' => 0,
                'createvisible' => false,
            ],
            'Hidden courses with last week or tomorrow dates' => [
                'lastweek' => 2,
                'yesterday' => 0,
                'tomorrow' => 2,
            ],
            'One hidden course of each type (last week, yesterday and tomorrow)' => [
                'lastweek' => 1,
                'yesterday' => 1,
                'tomorrow' => 1,
            ],
            'Different hidden courses of each type' => [
                'lastweek' => 2,
                'yesterday' => 3,
                'tomorrow' => 4,
            ],
            'A couple of hidden courses of each type (without visible courses)' => [
                'lastweek' => 2,
                'yesterday' => 2,
                'tomorrow' => 2,
                'createvisible' => false,
            ],
            'Only a few hidden courses for yesterday' => [
                'lastweek' => 0,
                'yesterday' => 5,
                'tomorrow' => 0,
            ],
            'Only a few hidden courses for yesterday (without visible courses)' => [
                'lastweek' => 0,
                'yesterday' => 5,
                'tomorrow' => 0,
                'createvisible' => false,
            ],
        ];
    }
}
