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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

use block_quickmail\persistents\schedule;

class block_quickmail_schedule_persistent_testcase extends advanced_testcase {

    use has_general_helpers;

    public function test_creates_a_schedule() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $beginattimestamp = $this->get_soon_time();
        $endattimestamp = $this->get_future_time();

        // Create.
        $schedule = schedule::create_from_params([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $beginattimestamp,
            'end_at' => $endattimestamp,
        ]);

        $this->assertInstanceOf(schedule::class, $schedule);
        $this->assertEquals('week', $schedule->get('unit'));
        $this->assertEquals(1, $schedule->get('amount'));
        $this->assertEquals($beginattimestamp, $schedule->get('begin_at'));
        $this->assertEquals($beginattimestamp, $schedule->get_begin_time());
        $this->assertEquals($endattimestamp, $schedule->get('end_at'));
        $this->assertEquals($endattimestamp, $schedule->get_end_time());
    }

    public function test_gets_an_increment_string() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $beginattimestamp = $this->get_soon_time();
        $endattimestamp = $this->get_future_time();

        // Create.
        $schedule = schedule::create_from_params([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $beginattimestamp,
            'end_at' => $endattimestamp,
        ]);

        $this->assertEquals('+1 week', $schedule->get_increment_string());

        // Create.
        $schedule = schedule::create_from_params([
            'unit' => 'month',
            'amount' => 2,
            'begin_at' => $beginattimestamp,
            'end_at' => $endattimestamp,
        ]);

        $this->assertEquals('+2 month', $schedule->get_increment_string());

        // Create.
        $schedule = schedule::create_from_params([
            'unit' => 'day',
            'amount' => 21,
            'begin_at' => $beginattimestamp,
            'end_at' => $endattimestamp,
        ]);

        $this->assertEquals('+21 day', $schedule->get_increment_string());
    }

    public function test_calculates_next_time_from_timestamp_for_active_schedule() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $now = $this->get_now_time();

        // Create a schedule that has not begun (or expired yet).
        $schedule = schedule::create_from_params([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $now,
            'end_at' => $this->get_future_time(),
        ]);

        $nextruntime = $schedule->calculate_next_time_from($now);

        // Calculate expected next run time.
        $expectednext = \DateTime::createFromFormat('U', $now, \core_date::get_server_timezone_object());
        $expectednext->modify('+1 week');

        $expectednexttimestamp = $expectednext->getTimestamp();

        $this->assertEquals($nextruntime, $expectednexttimestamp);
    }

    public function test_calculates_next_time_from_timestamp_for_expired_schedule() {
        // Reset all changes automatically after this test.
        $this->resetAfterTest(true);

        $now = $this->get_now_time();

        // Create a schedule that has expired.
        $schedule = schedule::create_from_params([
            'unit' => 'week',
            'amount' => 1,
            'begin_at' => $this->get_past_time(),
            'end_at' => $this->get_recent_time(),
        ]);

        $nextruntime = $schedule->calculate_next_time_from($now);

        $this->assertNull($nextruntime);
    }

}
