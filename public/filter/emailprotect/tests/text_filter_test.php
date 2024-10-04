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

namespace filter_emailprotect;

/**
 * Tests for the filter_emailprotect text filter.
 *
 * @package    filter_emailprotect
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_emailprotect\text_filter
 */
final class text_filter_test extends \advanced_testcase {
    /**
     * Test the filter method.
     *
     * @dataProvider filter_provider
     * @param string $expression The regexp to check.
     * @param string $text The text to filter.
     */
    public function test_filter(
        string $expression,
        string $text,
        bool $exactmatch,
    ): void {
        $filter = new text_filter(\core\context\system::instance(), []);
        $result = $filter->filter($text);

        $this->assertMatchesRegularExpression($expression, $result);

        if ($exactmatch) {
            $this->assertEquals($text, $result);
        } else {
            $this->assertNotEquals($text, $result);
        }
    }

    /**
     * Data provider for the filter test.
     *
     * @return array
     */
    public static function filter_provider(): array {
        $email = 'chaise@example.com';
        return [
            // No email address found.
            [
                '/Hello, world!/',
                'Hello, world!',
                true,
            ],
            // Email addresses present.
            // Note: The obfuscation randomly choose which chars to obfuscate.
            [
                '/.*(@|&#64;).*/',
                $email,
                false,
            ],
            [
                "~<a href=\".*:.*(@|&#64;).*\">.*(@|&#64;).*</a>~",
                "<a href='mailto:$email'>$email</a>",
                false,
            ],
        ];
    }
}
