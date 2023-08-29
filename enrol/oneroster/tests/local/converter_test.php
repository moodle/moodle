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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use advanced_testcase;

/**
 * One Roster tests for the `converter` class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\converter
 */
class converter_testcase extends advanced_testcase {

    /**
     * Ensure that the `from_date_to_unix` function.
     *
     * @dataProvider from_date_to_unix_provider
     * @param   string $date
     * @param   int $expected
     */
    public function test_from_date_to_unix(string $date, int $expected): void {
        $this->assertEquals($expected, converter::from_date_to_unix($date));
    }

    /**
     * Data provider for the `from_date_to_unix` function.
     */
    public function from_date_to_unix_provider(): array {
        return [
            ['1970-01-01', 0],
            ['2020-12-31', 1609372800],
            ['2040-06-15', 2223331200],
        ];
    }

    /**
     * Ensure that the `from_datetime_to_unix` function.
     *
     * @dataProvider from_datetime_to_unix_provider
     * @param   string $date
     * @param   int $expected
     */
    public function test_from_datetime_to_unix(string $date, int $expected): void {
        $this->assertEquals($expected, converter::from_datetime_to_unix($date));
    }

    /**
     * Data provider for the `from_date_to_unix` function.
     */
    public function from_datetime_to_unix_provider(): array {
        return [
            ['0', 0],
            ['1970-01-01T09:00:03.511Z', 32403],
            ['2020-12-31T13:51:04.992Z', 1609422664],
        ];
    }
}
