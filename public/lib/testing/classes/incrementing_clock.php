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
 * Incrementing clock for testing purposes.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read int $time The current time of the clock
 */
class incrementing_clock implements \core\clock {
    /** @var int The next time of the clock */
    public int $time;

    /** @var DateTimeZone The system timezone. */
    protected DateTimeZone $timezone;

    /**
     * Create a new instance of the incrementing clock.
     *
     * @param null|int $starttime The initial time to use. If not specified, the current time is used.
     */
    public function __construct(
        ?int $starttime = null,
    ) {
        $this->time = $starttime ?? time();
        $this->timezone = \core_date::get_server_timezone_object();
    }

    public function now(): \DateTimeImmutable {
        return (new \DateTimeImmutable('@' . $this->time++))->setTimezone($this->timezone);
    }

    public function time(): int {
        return $this->now()->getTimestamp();
    }

    /**
     * Set the time of the clock.
     *
     * @param int $time
     */
    public function set_to(int $time): void {
        $this->time = $time;
    }

    /**
     * Bump the time by a number of seconds.
     *
     * Note: The act of fetching the time will also bump the time by one second.
     *
     * @param int $seconds
     */
    public function bump(int $seconds = 1): void {
        $this->time += $seconds;
    }
}
