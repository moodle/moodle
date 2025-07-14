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

namespace core_sms;

/**
 * Message status categorisation.
 *
 * Statuses in the message_status class provide a human-readable machine description of the status.
 *
 * Each message status can further be categorised with attributes such as:
 * - sent
 * - failed
 * - inprogress
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\Attribute]
class status {
    /**
     * Message status categorisation.
     *
     * @param bool $sent
     * @param bool $failed
     * @param bool $inprogress
     */
    public function __construct(
        /** @var bool Whether the message was sent successfully */
        public readonly bool $sent = false,
        /** @var bool Whether the message is in a failed state */
        public readonly bool $failed = false,
        /** @var bool Whether the message is in-progress */
        public readonly bool $inprogress = false,
    ) {
    }
}
