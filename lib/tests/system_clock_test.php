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
 * Tests for the standard ClockInterface implementation.
 *
 * @package    core
 * @category   test
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\system_clock
 */
final class system_clock_test extends \advanced_testcase {
    /**
     * Test that the now method returns a DateTimeImmutable object.
     */
    public function test_now(): void {
        $starttime = time();

        $clock = new system_clock();
        $now = $clock->now();
        $this->assertInstanceOf(\DateTimeImmutable::class, $now);
        $this->assertGreaterThanOrEqual($starttime, $now->getTimestamp());
    }

    /**
     * Test that the time method returns a timestamp.
     */
    public function test_time(): void {
        $starttime = time();

        $clock = new system_clock();
        $time = $clock->time();
        $this->assertGreaterThanOrEqual($starttime, $time);
    }

    /**
     * Test that the now method returns a DateTimeImmutable object in the server timezone.
     *
     * @dataProvider timezone_provider
     * @param string $timezone
     */
    public function test_now_timezone(string $timezone): void {
        global $CFG;
        $this->resetAfterTest();

        $CFG->timezone = $timezone;

        $clock = new system_clock();
        $now = $clock->now();
        $this->assertEquals(\core_date::normalise_timezone($CFG->timezone), $now->getTimezone()->getName());
    }

    /**
     * Data provider for the test_now_timezone method.
     *
     * @return array
     */
    public static function timezone_provider(): array {
        return [
            ['UTC'],
            ['Europe/London'],
            ['America/New_York'],
        ];
    }
}
