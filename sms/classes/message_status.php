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

use core\attribute_helper;

/**
 * The general status of a message. Gateways are able to provide more specific statuses to supplement these.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum message_status: string {
    #[description('status:unknown', 'core_sms')]
    #[status()]
    case UNKNOWN = 'unknown';

    #[description('status:message_over_size', 'core_sms')]
    #[status(failed: true)]
    case MESSAGE_OVER_SIZE = 'message_over_size';

    #[description('status:gateway_not_available', 'core_sms')]
    #[status(failed: true)]
    case GATEWAY_NOT_AVAILABLE = 'gateway_not_available';

    #[description('status:gateway_queued', 'core_sms')]
    #[status(inprogress: true)]
    case GATEWAY_QUEUED = 'gateway_queued';

    #[description('status:gateway_sent', 'core_sms')]
    #[status(sent: true)]
    case GATEWAY_SENT = 'gateway_sent';

    #[description('status:gateway_rejected', 'core_sms')]
    #[status(failed: true)]
    case GATEWAY_REJECTED = 'gateway_rejected';

    #[description('status:gateway_failed', 'core_sms')]
    #[status(failed: true)]
    case GATEWAY_FAILED = 'gateway_failed';

    /**
     * Whether the message is in a state marked as sent.
     *
     * @return bool
     */
    public function is_sent(): bool {
        $status = attribute_helper::instance($this, status::class);
        return $status?->sent ?? false;
    }

    /**
     * Whether the message is in a state marked as failed.
     *
     * @return bool
     */
    public function is_failed(): bool {
        $status = attribute_helper::instance($this, status::class);
        return $status?->failed ?? false;
    }

    /**
     * Whether the message is in an in-progress state.
     *
     * @return bool
     */
    public function is_in_progress(): bool {
        $status = attribute_helper::instance($this, status::class);
        return $status?->inprogress ?? false;
    }

    /**
     * Get the human-readable status of the message.
     *
     * @return null|\lang_string
     */
    public function description(): ?\lang_string {
        return attribute_helper::instance($this, description::class);
    }
}
