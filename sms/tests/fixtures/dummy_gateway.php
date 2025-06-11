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

namespace smsgateway_dummy;

use core_sms\message;

/**
 * Dummy SMS gateway.
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gateway extends \core_sms\gateway {
    #[\Override]
    public function send(message $message): message {
        return $message->with(
            status: \core_sms\message_status::GATEWAY_SENT,
        );
    }

    #[\Override]
    public function get_send_priority(message $message): int {
        if (!$this->config) {
            return 50;
        }

        // Check if the recipient number starts with a specific prefix.
        if (property_exists($this->config, 'startswith')) {
            $startswith = substr($message->recipientnumber, 0, 3);
            if (property_exists($this->config->startswith, $startswith)) {
                return (int) $this->config->startswith->$startswith;
            }
        }

        if (property_exists($this->config, 'priority')) {
            return (int) $this->config->priority;
        }

        return 0;
    }
}
