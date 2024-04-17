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

use Generator;

/**
 * SMS manager.
 *
 * @package    core_sms
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var int The maximum length of a message */
    const MESSAGE_LENGTH_LIMIT = 160 * 3;

    /**
     * Create a new SMS manager.
     *
     * @param \moodle_database $db
     */
    public function __construct(
        /** @var \moodle_database The database instance */
        protected readonly \moodle_database $db,
    ) {
    }

    /**
     * Send an SMS to the given recipient.
     *
     * @param string $recipientnumber The phone number to send the SMS to
     * @param string $content The SMS Content
     * @param string $component The owning component
     * @param string $messagetype The message type within the component
     * @param null|int $recipientuserid The user id of the recipient if one exists
     * @param bool $issensitive Whether this SMS contains sensitive information
     * @param bool $async Whether this SMS should be sent asynchronously. Note: sensitive messages cannot be sent async
     * @return message
     * @throws \coding_exception If a sensitive message is sent asynchronously
     */
    public function send(
        string $recipientnumber,
        string $content,
        string $component,
        string $messagetype,
        ?int $recipientuserid,
        bool $issensitive = false,
        bool $async = true,
    ): message {
        $message = new message(
            recipientnumber: $recipientnumber,
            content: $content,
            component: $component,
            messagetype: $messagetype,
            recipientuserid: $recipientuserid,
            issensitive: $issensitive,
        );

        if ($issensitive && $async) {
            throw new \coding_exception('Sensitive messages cannot be sent asynchronously');
        }

        if ($async) {
            // TODO See MDL-81015 for further information.
            throw new \coding_exception('Asynchronous sending is not yet implemented');
        }

        if (\core_text::strlen($content) > self::MESSAGE_LENGTH_LIMIT) {
            $message = $message->with(status: message_status::MESSAGE_OVER_SIZE);
        } else if ($gateway = $this->get_gateway_for_message($message)) {
            $message = $message->with(gatewayid: $gateway->id);
            $message = $gateway->send(
                message: $message,
            );
        } else {
            $message = $message->with(status: message_status::GATEWAY_NOT_AVAILABLE);
        }

        return $this->save_message($message);
    }

    /**
     * Get the gateways that can send the given message.
     *
     * @param message $message
     * @return gateway[]
     */
    public function get_possible_gateways_for_message(
        message $message,
    ): array {
        $gateways = [];
        foreach ($this->get_enabled_gateway_instances() as $gateway) {
            if ($gateway->get_send_priority($message)) {
                $gateways[] = $gateway;
            }
        }

        return $gateways;
    }

    /**
     * Get the gateway that can send the given message.
     *
     * @param message $message
     * @return null|gateway
     */
    public function get_gateway_for_message(
        message $message,
    ): ?gateway {
        $gateways = $this->get_possible_gateways_for_message($message);
        if (!count($gateways)) {
            return null;
        }

        // Sort the gateways by their send priority for this message.
        usort($gateways, fn($a, $b) => $a->get_send_priority($message) <=> $b->get_send_priority($message));

        return array_pop($gateways);
    }

    /**
     * Get a list of all gateway instances.
     *
     * @param null|array $filter The database filter to apply
     * @return array
     */
    public function get_gateway_instances(?array $filter = null): array {
        return array_filter(
            array_map(
                function ($record): ?gateway {
                    if (!class_exists($record->gateway)) {
                        debugging(
                            "Unable to find a gateway class for {$record->gateway}",
                            DEBUG_DEVELOPER,
                        );
                        return null;
                    }

                    return new $record->gateway(
                        id: $record->id,
                        enabled: $record->enabled,
                        config: $record->config,
                    );
                },
                $this->get_gateway_records($filter),
            )
        );
    }

    /**
     * Get a list of all enabled gateway instances.
     *
     * @return array
     */
    public function get_enabled_gateway_instances(): array {
        return $this->get_gateway_instances(['enabled' => 1]);
    }

    /**
     * Save the message to the database.
     *
     * @param message $message
     * @return message
     */
    public function save_message(
        message $message,
    ): message {
        if ($message->issensitive) {
            // Sensitive messages should not store content.
            $message = $message->with(content: null);
        }
        if ($message->id) {
            $this->db->update_record('sms_messages', $message->to_record());
            return $message;
        }
        $id = $this->db->insert_record('sms_messages', $message->to_record());

        return $message->with(id: $id);
    }

    /**
     * Enable a gateway.
     *
     * @param gateway $gateway
     * @return gateway
     */
    public function enable_gateway(gateway $gateway): gateway {
        if (!$gateway->enabled) {
            $gateway = $gateway->with(enabled: true);
            $this->db->update_record('sms_gateways', $gateway->to_record());
        }

        return $gateway;
    }

    /**
     * Disable a gateway.
     *
     * @param gateway $gateway
     * @return gateway
     */
    public function disable_gateway(gateway $gateway): gateway {
        if ($gateway->enabled) {
            $gateway = $gateway->with(enabled: false);
            $this->db->update_record('sms_gateways', $gateway->to_record());
        }

        return $gateway;
    }

    /**
     * Create a new gateway instance.
     *
     * @param string $classname
     * @param bool $enabled
     * @param ?\stdClass $config
     * @return gateway
     */
    public function create_gateway_instance(
        string $classname,
        bool $enabled = false,
        ?\stdClass $config = null,
    ): gateway {
        if (!class_exists($classname) || !is_a($classname, gateway::class, true)) {
            throw new \coding_exception("Gateway class not valid: {$classname}");
        }
        $gateway = new $classname(
            enabled: $enabled,
            config: $config ? json_encode($config) : '',
        );

        $id = $this->db->insert_record('sms_gateways', $gateway->to_record());

        return $gateway->with(id: $id);
    }

    /**
     * Get all messages.
     *
     * @param string $sort
     * @param null|array $filter
     * @param int $pagesize
     * @param int $page
     * @return Generator
     */
    public function get_messages(
        string $sort = 'timecreated ASC',
        ?array $filter = null,
        int $pagesize = 0,
        int $page = 0,
    ): Generator {
        $rows = $this->db->get_records(
            table: 'sms_messages',
            conditions: $filter,
            limitfrom: $pagesize * $page,
            limitnum: $pagesize,
            sort: $sort,
        );

        foreach ($rows as $record) {
            yield new message(
                id: $record->id,
                recipientnumber: $record->recipientnumber,
                content: $record->content,
                component: $record->component,
                messagetype: $record->messagetype,
                recipientuserid: $record->recipientuserid,
                issensitive: $record->issensitive,
                status: message_status::from($record->status),
                gatewayid: $record->gatewayid,
                timecreated: $record->timecreated,
            );
        }
    }

    /**
     * Get a message
     *
     * @param array $filter
     * @return message
     */
    public function get_message(
        array $filter,
    ): message {
        $record = $this->db->get_record(
            table: 'sms_messages',
            conditions: $filter,
        );

        return new message(
            id: $record->id,
            recipientnumber: $record->recipientnumber,
            content: $record->content,
            component: $record->component,
            messagetype: $record->messagetype,
            recipientuserid: $record->recipientuserid,
            issensitive: $record->issensitive,
            status: message_status::from($record->status),
            gatewayid: $record->gatewayid,
            timecreated: $record->timecreated,
        );
    }

    /**
     * This function internationalises a number to E.164 standard.
     * https://46elks.com/kb/e164
     *
     * @param string $phonenumber the phone number to format.
     * @param ?string $countrycode The country code of the phone number.
     * @return string the formatted phone number.
     */
    public static function format_number(
        string $phonenumber,
        ?string $countrycode = null,
    ): string {
        // Remove all whitespace, dashes, and brackets in one step.
        $phonenumber = preg_replace('/[ ()-]/', '', $phonenumber);

        // Check if the number is already in international format or if it starts with a 0.
        if (!str_starts_with($phonenumber, '+')) {
            // Strip leading 0.
            if (str_starts_with($phonenumber, '0')) {
                $phonenumber = substr($phonenumber, 1);
            }

            // Prepend country code if not already in international format.
            $phonenumber = !empty($countrycode) ? '+' . $countrycode . $phonenumber : $phonenumber;
        }

        return $phonenumber;
    }
}
