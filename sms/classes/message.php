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

use libphonenumber\NumberParseException;
use Spatie\Cloneable\Cloneable;
use ValueError;

/**
 * A Message used in an SMS.
 *
 * Note: This class is immutable. All properties are readonly.
 *       The class itself will likely become readonly when PHP 8.2 is the minimum requirement.
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read int $timecreated The time that the message was created
 * @property-read string $recipientnumber The recipient of the message
 * @property-read null|string $content The content of the message
 * @property-read string $component The component that owns the message
 * @property-read string $messagetype The type of message within the component
 * @property-read int|null $recipientuserid The user id of the recipient if one exists
 * @property-read bool $issensitive Whether this message contains sensitive information
 * @property-read int|null $id The id of the message in the database
 * @property-read message_status $status The status of the message
 * @property-read int|null $gatewayid The id of the gateway that sent the message
 */
class message {
    use Cloneable {
        with as private _with;
    }

    /** @var int The time that the message was created */
    public readonly int $timecreated;

    /**
     * Create a new message.
     *
     * @param string $recipientnumber The phone number of the message recipient
     * @param null|string $content The content of the message
     * @param string $component The component that owns the message
     * @param string $messagetype The type of message within the component
     * @param int|null $recipientuserid The user id of the recipient if one exists
     * @param bool $issensitive Whether this message contains sensitive information
     * @param int|null $id The id of the message in the database
     * @param message_status $status The status of the message
     * @param int|null $gatewayid The id of the gateway that sent the message
     * @param int|null $timecreated The time that the message was created
     */
    public function __construct(
        /** @var string The phone number of the message recipient */
        public readonly string $recipientnumber,
        /** @var null|string The content of the message */
        public readonly ?string $content,
        /** @var string The component that owns the message */
        public readonly string $component,
        /** @var string The type of message within the component */
        public readonly string $messagetype,
        /** @var int|null The user id of the recipient if one exists */
        public readonly ?int $recipientuserid,
        /** @var bool Whether this message contains sensitive information */
        public readonly bool $issensitive,
        /** @var null|int The id of the message in the database */
        public readonly ?int $id = null,
        /** @var message_status The status of the message */
        public readonly message_status $status = message_status::UNKNOWN,
        /** @var null|int The id of the gateway that sent the message */
        public readonly ?int $gatewayid = null,
        ?int $timecreated = null,
    ) {
        if ($timecreated === null) {
            $this->timecreated = \core\di::get(\core\clock::class)->now()->getTimestamp();
        } else {
            $this->timecreated = $timecreated;
        }
    }

    /**
     * Convert the message to a record.
     *
     * @return \stdClass
     */
    public function to_record(): \stdClass {
        $record = (object) [
            'recipientnumber' => $this->recipientnumber,
            'content' => $this->content,
            'component' => $this->component,
            'messagetype' => $this->messagetype,
            'recipientuserid' => $this->recipientuserid,
            'issensitive' => $this->issensitive,
            'status' => $this->status->value,
            'gatewayid' => $this->gatewayid,
            'timecreated' => $this->timecreated,
        ];

        if ($this->id !== null) {
            $record->id = $this->id;
        }

        return $record;
    }

    /**
     * Update the message properties.
     *
     * Note: The message is immutable.
     * When setting the status a new object will be returned.
     *
     * @param mixed ...$args
     * @return message
     */
    public function with(...$args): self {
        if (isset($this->id) && array_key_exists('id', $args)) {
            throw new \coding_exception('Message already has an id');
        }

        return $this->_with(...$args);
    }

    /**
     * Get the region code for this messages.
     *
     * @return null|string
     */
    public function get_region(): ?string {
        $pnu = \core\di::get(\libphonenumber\PhoneNumberUtil::class);
        try {
            $recipient = $pnu->parse($this->recipientnumber);
        } catch (NumberParseException $e) {
            // Note: Throw errors which are not specific to libphonenumber here.
            // This is to avoid hard-trying use of this library.
            throw new ValueError(
                new \lang_string('phonenumbernotvalid', 'sms', ['message' => $e->getMessage()]),
                $e->getCode(),
                $e,
            );
        }

        return $pnu->getRegionCodeForNumber($recipient);
    }

    /**
     * Check if the message has been sent.
     *
     * @return bool
     */
    public function is_sent(): bool {
        return $this->status->is_sent();
    }
}
