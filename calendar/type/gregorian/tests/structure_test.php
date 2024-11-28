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

namespace calendartype_gregorian;

/**
 * Tests for Gregorian calendar type
 *
 * @package    calendartype_gregorian
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \calendartype_gregorian\structure
 */
final class structure_test extends \advanced_testcase {
    public function tearDown(): void {
        parent::tearDown();

        get_string_manager(true);
    }

    /**
     * Test the timestamp_to_date_string method with different input values.
     *
     * @dataProvider timestamp_to_date_string_provider
     * @param string $locale
     * @param int $timestamp
     * @param string $format
     * @param string $timezone
     * @param bool $fixday
     * @param bool $fixhour
     * @param string $expected
     */
    public function test_timestamp_to_date_string(
        string $locale,
        int $timestamp,
        string $format,
        string $timezone,
        bool $fixday,
        bool $fixhour,
        string $expected,
    ): void {
        $this->resetAfterTest();

        $stringmanager = $this->get_mocked_string_manager();
        $stringmanager->mock_string('locale', 'langconfig', $locale);

        $structure = new structure();
        $this->assertEquals(
            $expected,
            $structure->timestamp_to_date_string(
                $timestamp,
                $format,
                $timezone,
                $fixday,
                $fixhour,
            ),
        );
    }

    /**
     * Data provider for timestamp_to_date_string tests.
     *
     * @return array
     */
    public static function timestamp_to_date_string_provider(): array {
        return [
            'English with UTC timezone' => [
                'en',
                0,
                '%Y-%m-%d %H:%M:%S',
                'UTC',
                false,
                false,
                '1970-01-01 00:00:00',
            ],
            'English with London timezone' => [
                'en',
                1728487003,
                "%d %B %Y",
                'Europe/London',
                false,
                false,
                "09 October 2024",
            ],
            'English with Sydney (+11) timezone' => [
                'en',
                1728487003,
                "%d %B %Y",
                'Australia/Sydney',
                false,
                false,
                "10 October 2024",
            ],
            'Russian with Sydney (+11) timezone' => [
                'ru',
                1728487003,
                "%d %B %Y %H:%M:%S",
                'Australia/Sydney',
                false,
                false,
                '10 октября 2024 02:16:43',
            ],
            'Russian %B %Y (Genitive) with Sydney (+11) timezone' => [
                'ru',
                1728487003,
                "%B %Y",
                'Australia/Sydney',
                false,
                false,
                "октябрь 2024",
            ],
            'Russian %d %B %Y (Nominative) with London timezone' => [
                'ru',
                1728487003,
                "%d %B %Y",
                'Europe/London',
                false,
                false,
                "09 октября 2024",
            ],
            'Russian %d %B %Y (Nominative) with London timezone fixing leading zero' => [
                'ru',
                1728487003,
                "%d %B %Y",
                'Europe/London',
                true,
                false,
                "9 октября 2024",
            ],
            'Russian %e %B %Y (Nominative) with London timezone' => [
                'ru',
                1728487003,
                "%e %B %Y",
                'Europe/London',
                false,
                false,
                " 9 октября 2024",
            ],
            'Time %I without fixing leading zero' => [
                'ru',
                1728487003,
                "%I:%M:%S",
                'Australia/Sydney',
                false,
                false
                ,
                "02:16:43",
            ],
            'Time %I fixing leading zero' => [
                'ru',
                1728487003,
                "%I:%M:%S",
                'Australia/Sydney',
                false,
                true
                ,
                "2:16:43",
            ],
            'Time %l without fixing leading zero' => [
                'ru',
                1728487003,
                "%l:%M:%S",
                'Australia/Sydney',
                false,
                false,
                " 2:16:43",
            ],
            'Time %l fixing leading zero' => [
                'ru',
                1728487003,
                "%l:%M:%S",
                'Australia/Sydney',
                false,
                true,
                " 2:16:43",
            ],
        ];
    }
}
