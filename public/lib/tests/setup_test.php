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

namespace core;

/**
 * Tests for the \core\setup class.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core\setup::class)]
final class setup_test extends \advanced_testcase {
    #[\PHPUnit\Framework\Attributes\DataProvider('wwwroot_validity_provider')]
    public function test_wwwroot_ends_in_slash(
        string $wwwroot,
        bool $valid,
        ?string $exceptionstring = null,
    ): void {
        global $CFG;

        $this->resetAfterTest(true);
        $CFG->wwwroot = $wwwroot;

        if (!$valid) {
            $this->expectException(\core\exception\moodle_exception::class);
            $this->expectExceptionMessage($exceptionstring);
        }

        $this->assertTrue(di::get(setup::class)->validate_wwwroot());
    }

    /**
     * Data provider for test_wwwroot_ends_in_slash.
     *
     * @return \Generator
     */
    public static function wwwroot_validity_provider(): \Generator {
        foreach (['https', 'http'] as $protocol) {
            yield "Valid {$protocol} wwwroot on root domain" => [
                "{$protocol}://example.com",
                true,
            ];

            yield "Valid {$protocol} wwwroot on sub domain" => [
                "{$protocol}://moodle.example.com",
                true,
            ];

            yield "Valid {$protocol} wwwroot on directory" => [
                "{$protocol}://example.com/moodle",
                true,
            ];
        }

        yield "Root domain ends in slash" => [
            "https://example.com/",
            false,
            get_string('wwwrootslash', 'error'),
        ];

        yield "Sub domain ends in slash" => [
            "https://moodle.example.com/",
            false,
            get_string('wwwrootslash', 'error'),
        ];

        yield "Directory ends in slash" => [
            "https://example.com/moodle/",
            false,
            get_string('wwwrootslash', 'error'),
        ];

        yield "Directory ends in public and should not" => [
            "https://example.com/public",
            false,
            get_string('wwwrootpublic', 'error'),
        ];
    }
}
