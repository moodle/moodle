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

use coding_exception;
use Spatie\Cloneable\Cloneable;
use stdClass;

/**
 * Class gateway.
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read int $id The id of the gateway in the database
 * @property-read bool $enabled Whether the gateway is enabled
 * @property-read stdClass $config The configuration for this instance
 * @property string $name The name of the gateway config
 */
abstract class gateway {
    use Cloneable;

    /** @var int The maximum length of a message. */
    protected const MESSAGE_LENGTH_LIMIT = 160 * 3;

    /** @var stdClass The configuration for this instance */
    public readonly stdClass $config;

    /**
     * Create a new gateway.
     *
     * @param bool $enabled Whether the gateway is enabled
     * @param string $name The name of the gateway config
     * @param string $config The configuration for this instance
     * @param int|null $id The id of the gateway in the database
     */
    public function __construct(
        /** @var bool Whether the gateway is enabled */
        public readonly bool $enabled,
        /** @var string The name of the gateway config */
        public string $name,
        string $config,
        /** @var null|int The ID of the gateway in the database, or null if it has not been persisted yet */
        public readonly ?int $id = null,
    ) {
        $this->config = json_decode($config);
    }

    /**
     * Convert this object to a stdClass.
     *
     * @return stdClass
     */
    public function to_record(): stdClass {
        return (object) [
            'id' => $this->id,
            'name' => $this->name,
            'gateway' => get_class($this),
            'enabled' => $this->enabled,
            'config' => json_encode($this->config),
        ];
    }

    /**
     * Send the given message.
     *
     * @param message $message
     * @return message
     */
    abstract public function send(message $message): message;

    /**
     * Confirm whether this gateway can send the given message.
     *
     * @param message $message
     * @return bool
     */
    public function can_send(message $message): bool {
        return $this->get_send_priority($message) > 0;
    }

    /**
     * Get the priority of this gateway for sending the given message.
     *
     * A priority of 0 means that the gateway cannot send the message.
     * Higher values are higher priority.
     *
     * This method is called frequently, so should be fast.
     * If calculation is expensive the value should be cached.
     *
     * @param message $message
     * @return int
     */
    abstract public function get_send_priority(message $message): int;

    /**
     * Update the status of the given message from the gateway.
     *
     * @param message $message
     * @return message
     * @throws coding_exception
     */
    public function update_message_status(message $message): message {
        if ($message->gatewayid !== $this->id) {
            throw new \coding_exception('This gateway cannot update the status of this message');
        }

        return $message;
    }

    /**
     * Update the statuses of the given messages from the gateway.
     *
     * @param message[] $messages
     * @return message[]
     */
    public function update_message_statuses(array $messages): array {
        return array_map([$this, 'update_message_status'], $messages);
    }

    /**
     * Truncates the given message to fit the constraints.
     *
     * @param string $message The message to be truncated.
     * @return string The truncated message.
     */
    public function truncate_message(string $message): string {
        if (strlen($message) > static::MESSAGE_LENGTH_LIMIT) {
            $message = self::remove_urls_from_message($message);
        }
        return \core_text::substr($message, 0, static::MESSAGE_LENGTH_LIMIT);
    }

    /**
     * Remove URLs from the message.
     *
     * @param string $message The message to check.
     * @return string The updated message.
     */
    public static function remove_urls_from_message(string $message): string {
        return trim(preg_replace('/https?:\/\/\S+/', '', $message));
    }
}
