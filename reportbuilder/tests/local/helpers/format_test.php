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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use stdClass;

/**
 * Unit tests for the format helper
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\format
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class format_test extends advanced_testcase {

    /**
     * Test userdate method
     */
    public function test_userdate(): void {
        $now = time();

        $userdate = format::userdate($now, new stdClass());
        $this->assertEquals(userdate($now), $userdate);
    }

    /**
     * Data provider for {@see test_format_time}
     *
     * @return array[]
     */
    public static function format_time_provider(): array {
        return [
            [null, 0, ''],
            [0, 0, '0 secs'],
            [2.456, 1, '2.5 secs'],
            [3.2, null, '3 secs'],
        ];
    }

    /**
     * Test format time
     *
     * @param float|null $value
     * @param int|null $precision
     * @param string $expected
     *
     * @dataProvider format_time_provider
     */
    public function test_format_time(?float $value, ?int $precision, string $expected): void {
        $this->assertEquals($expected, format::format_time($value, (object) [], $precision));
    }

    /**
     * Data provider for {@see test_boolean_as_text}
     *
     * @return array[]
     */
    public static function boolean_as_text_provider(): array {
        return [
            [false, get_string('no')],
            [true, get_string('yes')],
        ];
    }

    /**
     * Test boolean as text
     *
     * @param bool $value
     * @param string $expected
     *
     * @dataProvider boolean_as_text_provider
     */
    public function test_boolean_as_text(bool $value, string $expected): void {
        $this->assertEquals($expected, format::boolean_as_text($value));
    }

    /**
     * Test percentage formatting of a float
     */
    public function test_percent(): void {
        $this->assertEquals('33.3%', format::percent(1 / 3 * 100));
    }
}
