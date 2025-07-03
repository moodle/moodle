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
 * Frozen clock for testing purposes.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \DateTimeImmutable $time The current time of the clock
 */
class frozen_clock implements \core\clock {
    /** @var DateTimeImmutable The next time of the clock */
    public DateTimeImmutable $time;

    /**
     * Create a new instance of the frozen clock.
     *
     * @param null|int $time The initial time to use. If not specified, the current time is used.
     */
    public function __construct(
        ?int $time = null,
    ) {
        if ($time) {
            // Note that the constructor with time zone does not work when specifying a timestamp,
            // so we have to set timezone separately afterward.
            $this->time = (new \DateTimeImmutable("@{$time}"))
                ->setTimezone(\core_date::get_server_timezone_object());
        } else {
            $this->time = (new \DateTimeImmutable())->setTimezone(\core_date::get_server_timezone_object());
        }
    }

    public function now(): \DateTimeImmutable {
        return $this->time;
    }

    public function time(): int {
        return $this->time->getTimestamp();
    }

    /**
     * Set the time of the clock.
     *
     * @param int $time
     */
    public function set_to(int $time): void {
        $this->time = (new \DateTimeImmutable("@{$time}"))
            ->setTimezone(\core_date::get_server_timezone_object());
    }

    /**
     * Bump the time by a number of seconds.
     *
     * @param int $seconds
     */
    public function bump(int $seconds = 1): void {
        $this->time = $this->time->modify("+{$seconds} seconds");
    }
}
