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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\output;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the userdate Mustache helper.
 *
 * @package core
 * @copyright 2026 Cameron Ball <cameron@cameron1729.xyz>
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(mustache_user_date_helper::class)]
final class mustache_user_date_helper_test extends \advanced_testcase {
    /**
     * Date formats are strings, including those containing literal JSON.
     *
     * @param string $format Format argument
     * @param string $expected Expected date
     */
    #[DataProvider('format_provider')]
    public function test_transform(string $format, string $expected): void {
        global $USER;

        $this->resetAfterTest();
        $USER->timezone = 'UTC';
        $engine = new mustache_engine([
            'helpers' => ['userdate' => (new mustache_user_date_helper())->transform(...)],
        ]);

        $this->assertSame($expected, $engine->render(
            '{{#userdate}} {{timestamp}}, ' . $format . ' {{/userdate}}',
            ['timestamp' => 946782306, 'format' => '%d %b %Y'],
        ));
    }

    /**
     * Date format arguments.
     *
     * @return array
     */
    public static function format_provider(): array {
        return [
            'string' => ['%d %b %Y', '2 Jan 2000'],
            'comma in format' => ['%d %b, %Y', '2 Jan, 2000'],
            'nested variable' => ['{{format}}', '2 Jan 2000'],
            'literal braces' => ['{%Y-%m-%d}', '{2000-01-2}'],
            'literal JSON string' => ['{"year": "%Y"}', '{"year": "2000"}'],
            'literal JSON number' => ['{"year": %Y}', '{"year": 2000}'],
        ];
    }
}
