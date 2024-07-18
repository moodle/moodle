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

namespace filter_tidy;

/**
 * Tests for HTML tidy.
 *
 * @package    filter_tidy
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \filter_tidy
 */
final class filter_tidy_test extends \advanced_testcase {
    /** @var string Locale */
    protected string $locale;

    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        require_once(__DIR__ . '/../filter.php');
    }

    #[\Override]
    public function setUp(): void {
        parent::setUp();
        $this->locale = \core\locale::get_locale();
    }

    #[\Override]
    public function tearDown(): void {
        parent::tearDown();
        \core\locale::set_locale(LC_ALL, $this->locale);
    }

    /**
     * Test the filter method.
     *
     * @requires extension tidy
     * @dataProvider filter_provider
     * @param string $text The text to filter.
     * @param string $expected The expected value
     */
    public function test_filter(
        string $text,
        string $expected,
    ): void {
        $filter = new \filter_tidy(\core\context\system::instance(), []);
        $this->assertEquals($expected, $filter->filter($text));
        $this->assertEquals(
            \core\locale::standardise_locale($this->locale),
            \core\locale::standardise_locale(\core\locale::get_locale()),
        );
    }

    /**
     * Data provider for the filter test.
     *
     * @return array
     */
    public static function filter_provider(): array {
        return [
            // No HTML tags.
            [
                'The cat is in the hat',
                'The cat is in the hat',
            ],
            // Partial HTML.
            [
                '<p>The cat is in the hat',
                <<<EOF
                <p>
                  The cat is in the hat
                </p>
                EOF,
            ],
            // Return only the body, repairing the closing tag.
            [
                <<<EOF
                <html>
                    <head>
                        <title>test</title>
                    </head>
                    <body>
                        <p>error</i>
                    </body>
                </html>
                EOF,
                <<<EOF
                <p>
                  error
                </p>
                EOF,
            ],
        ];
    }

}
