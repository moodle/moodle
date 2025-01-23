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

namespace core_calendar\output;

use DateTime;

/**
 * Tests for humantimeperiod_test class.
 *
 * @covers     \core_calendar\output\humantimeperiod
 * @package    core_calendar
 * @category   test
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class humantimeperiod_test extends \advanced_testcase {

    /**
     * Test format_period() method.
     *
     * @dataProvider provider_format_period
     * @param int|null $addsecondsend The number of seconds to add to the current time for the end date.
     * @param bool $expectedendnull Whether the end date is null.
     */
    public function test_format_period(
        ?int $addsecondsend,
        bool $expectedendnull,
    ): void {
        $this->resetAfterTest();

        // 26 February 2025 15:30:00 (GMT).
        $clock = $this->mock_clock_with_frozen(1740583800);

        $timestampstart = $clock->time();
        $timestampend = is_null($addsecondsend) ? null : $clock->time() + $addsecondsend;
        $humandateend = null;
        if (!$expectedendnull) {
            $humandateend = humandate::create_from_timestamp(
                timestamp: $timestampend,
                timeonly: (abs($addsecondsend) < 1800),
            );
        }
        $expected = [
            'startdate' => humandate::create_from_timestamp($timestampstart),
            'enddate' => $humandateend,
        ];
        $humantimeperiod = humantimeperiod::create_from_timestamp($timestampstart, $timestampend);

        $reflection = new \ReflectionClass($humantimeperiod);
        $method = $reflection->getMethod('format_period');
        $method->setAccessible(true);
        $result = $method->invoke($humantimeperiod);
        $this->compare_output($expected, $result);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public static function provider_format_period(): array {
        return [
            'Same start and end' => [
                'addsecondsend' => 0,
                'expectedendnull' => true,
            ],
            'Null end date' => [
                'addsecondsend' => null,
                'expectedendnull' => true,
            ],
            '1 day ahead' => [
                'addsecondsend' => 87000,
                'expectedendnull' => false,
            ],
            '1 day behind' => [
                'addsecondsend' => -87000,
                'expectedendnull' => false,
            ],
            '29 minutes ahead' => [
                'addsecondsend' => 1799,
                'expectedendnull' => false,
            ],
            '30 minutes ahead (Midnight)' => [
                'addsecondsend' => 1800,
                'expectedendnull' => false,
            ],
            '31 minutes ahead (Midnight)' => [
                'addsecondsend' => 1801,
                'expectedendnull' => false,
            ],
        ];
    }

    /**
     * Test create_from_timestamp.
     */
    public function test_create_from_timestamp(): void {
        $this->resetAfterTest();

        $clock = \core\di::get(\core\clock::class);
        $timestamp = $clock->time();
        $humantimeperiod = humantimeperiod::create_from_timestamp($timestamp, $timestamp + 3600);
        $this->assertInstanceOf(humantimeperiod::class, $humantimeperiod);
    }

    /**
     * Test create_from_timestamp with enddatetime null.
     */
    public function test_create_from_timestamp_endnull(): void {
        $this->resetAfterTest();

        $clock = $this->mock_clock_with_frozen();
        $timestamp = $clock->time();
        $humantimeperiod = humantimeperiod::create_from_timestamp($timestamp, null);
        $this->assertInstanceOf(humantimeperiod::class, $humantimeperiod);
    }

    /**
     * Test create_from_datetime.
     */
    public function test_create_from_datetime(): void {
        $this->resetAfterTest();

        $humantimeperiod = humantimeperiod::create_from_datetime(new DateTime(), new DateTime('+1 hour'));
        $this->assertInstanceOf(humantimeperiod::class, $humantimeperiod);
    }

    /**
     * Test create_from_datetime with enddatetime null.
     */
    public function test_create_from_datetime_endnull(): void {
        $this->resetAfterTest();

        $humantimeperiod = humantimeperiod::create_from_datetime(new DateTime(), null);
        $this->assertInstanceOf(humantimeperiod::class, $humantimeperiod);
    }
    /**
     * Compare humantimeperiod output.
     *
     * @param array $expected The expected output.
     * @param array $actual The actual output.
     */
    protected function compare_output(
        array $expected,
        array $actual,
    ): void {
        $fields = ['startdate', 'enddate'];
        foreach ($fields as $field) {
            $this->assertEquals($expected[$field], $actual[$field], "Field $field does not match");
        }
    }
}
