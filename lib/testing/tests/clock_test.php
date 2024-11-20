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

use frozen_clock;
use incrementing_clock;

/**
 * Tests for testing clocks.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class clock_test extends \advanced_testcase {
    /**
     * Test the incrementing mock clock.
     *
     * @covers \incrementing_clock
     */
    public function test_clock_with_incrementing(): void {
        require_once(__DIR__ . '/../classes/incrementing_clock.php');

        $clock = new incrementing_clock();
        $this->assertInstanceOf(\incrementing_clock::class, $clock);

        $initialtime = $clock->now()->getTimestamp();

        // Test the functionality.
        $this->assertEquals($initialtime + 1, $clock->now()->getTimestamp());
        $this->assertEquals($initialtime + 2, $clock->time());
        $this->assertEquals($initialtime + 3, $clock->now()->getTimestamp());

        // Specify a specific start time.
        $clock = new incrementing_clock(12345);

        $this->assertEquals(12345, $clock->now()->getTimestamp());
        $this->assertEquals(12346, $clock->time());
        $this->assertEquals(12347, $clock->now()->getTimestamp());

        $clock->set_to(12345);
        $this->assertEquals(12345, $clock->time());
        $this->assertEquals(12346, $clock->time());

        $clock->bump();
        $this->assertEquals(12348, $clock->time());
        $clock->bump();
        $this->assertEquals(12350, $clock->time());
        $clock->bump(5);
        $this->assertEquals(12356, $clock->time());
    }

    /**
     * Test the incrementing mock clock.
     *
     * @covers \frozen_clock
     */
    public function test_mock_clock_with_frozen(): void {
        require_once(__DIR__ . '/../classes/frozen_clock.php');

        $clock = new frozen_clock();

        // Test the functionality.
        $initialtime = $clock->now()->getTimestamp();
        $this->assertEquals($initialtime, $clock->now()->getTimestamp());
        $this->assertEquals($initialtime, $clock->now()->getTimestamp());
        $this->assertEquals($initialtime, $clock->now()->getTimestamp());
        $this->assertEquals($initialtime, $clock->time());

        // Specify a specific start time.
        $clock = new frozen_clock(12345);

        $initialtime = $clock->now();
        $this->assertEquals($initialtime, $clock->now());
        $this->assertEquals($initialtime, $clock->now());
        $this->assertEquals($initialtime, $clock->now());

        $clock->set_to(12345);
        $this->assertEquals(12345, $clock->now()->getTimestamp());
        $this->assertEquals(12345, $clock->now()->getTimestamp());
        $this->assertEquals(12345, $clock->now()->getTimestamp());

        $this->assertEquals(12345, $clock->time());

        $clock->bump();
        $this->assertEquals(12346, $clock->time());
        $clock->bump();
        $this->assertEquals(12347, $clock->time());
        $clock->bump(5);
        $this->assertEquals(12352, $clock->time());
    }
}
