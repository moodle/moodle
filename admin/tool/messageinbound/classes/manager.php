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
 * The Mail Pickup Manager.
 *
 * @package    tool_messageinbound
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_messageinbound;

/**
 * Mail Pickup Manager.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var string The main mailbox to check.
     */
    const MAILBOX = 'INBOX';

    /**
     * @var string The mailbox to store messages in when they are awaiting confirmation.
     */
    const CONFIRMATIONFOLDER = 'tobeconfirmed';

    /**
     * @var string The flag for seen/read messages.
     */
    const MESSAGE_SEEN = '\seen';

    /**
     * @var string The flag for flagged messages.
     */
    const MESSAGE_FLAGGED = '\flagged';

    /**
     * @var string The flag for deleted messages.
     */
    const MESSAGE_DELETED = '\deleted';

    /**
     * @var \string IMAP folder namespace.
     */
    protected $imapnamespace = null;

    /**
     * @var \rcube_imap_generic A reference to the IMAP client.
     */
    protected $client = null;

    /**
     * @var \core\message\inbound\address_manager A reference to the Inbound Message Address Manager instance.
     */
    protected $addressmanager = null;

    /**
     * @var \stdClass The data for the current message being processed.
     */
    protected $currentmessagedata = null;

    /**
     * Mail Pickup Manager.
     */
    public function __construct() {
        // Load dependencies.
        $this->load_dependencies();
    }

    /**
     * Retrieve the connection to the IMAP client.
     *
     * @param string $mailbox The mailbox to connect to.
     *
     * @return bool Whether a connection was successfully established.
     */
    protected function get_imap_client(
        string $mailbox = self::MAILBOX,
    ): bool {
        global $CFG;

        if (!\core\message\inbound\manager::is_enabled()) {
            // E-mail processing not set up.
            mtrace("Inbound Message not fully configured - exiting early.");
            return false;
        }

        mtrace("Connecting to {$CFG->messageinbound_host} as {$CFG->messageinbound_hostuser}...");

        $configuration = [
            'username' => $CFG->messageinbound_hostuser,
            'password' => $CFG->messageinbound_hostpass,
            'hostspec' => $CFG->messageinbound_host,
            'options' => [
                'ssl_mode' => strtolower($CFG->messageinbound_hostssl),
                'auth_type' => 'CHECK',
            ],
        ];

        if (strpos($configuration['hostspec'], ':')) {
            $hostdata = explode(':', $configuration['hostspec']);
            if (count($hostdata) === 2) {
                // A hostname in the format hostname:port has been provided.
                $configuration['hostspec'] = $hostdata[0];
                $configuration['options']['port'] = $hostdata[1];
            }
        }

        // XOAUTH2.
        if (isset($CFG->messageinbound_hostoauth) && $CFG->messageinbound_hostoauth != '') {
            // Get the issuer.
            $issuer = \core\oauth2\api::get_issuer($CFG->messageinbound_hostoauth);
            // Validate the issuer and check if it is enabled or not.
            if ($issuer && $issuer->get('enabled')) {
                // Get the OAuth Client.
                if ($oauthclient = \core\oauth2\api::get_system_oauth_client($issuer)) {
                    $configuration['password'] = 'Bearer ' . $oauthclient->get_accesstoken()->token;
                    $configuration['options']['auth_type'] = 'XOAUTH2';
                }
            }
        }

        $this->client = new \rcube_imap_generic();
        if (!empty($CFG->debugimap)) {
            $this->client->setDebug(debug: true);
        }
        $success = $this->client->connect(
            host: $configuration['hostspec'],
            user: $configuration['username'],
            password: $configuration['password'],
            options: $configuration['options'],
        );

        if ($success) {
            mtrace("Connection established.");

            // Ensure that mailboxes exist.
            $this->ensure_mailboxes_exist();
            // Select mailbox.
            $this->select_mailbox(mailbox: $mailbox);
            return true;
        } else {
            throw new \moodle_exception('imapconnectfailure', 'tool_messageinbound', '', null, 'Could not connect to IMAP server.');
        }
    }

    /**
     * Shutdown and close the connection to the IMAP client.
     */
    protected function close_connection(): void {
        if ($this->client) {
            // Close the connection and return to authenticated state.
            $isclosed = $this->client->close();
            if ($isclosed) {
                // Connection was closed unsuccessfully. Send the LOGOUT command and close the socket.
                $this->client->closeConnection();
            }
        }
        $this->client = null;
    }

    /**
     * Get the confirmation folder imap name
     *
     * @return string
     */
    protected function get_confirmation_folder(): string {
        if ($this->imapnamespace === null) {
            $namespaces = $this->client->getNamespace();
            if ($namespaces != $this->client::ERROR_BAD && is_array($namespaces)) {
                $nspersonal = reset($namespaces['personal']);
                if (is_array($nspersonal) && !empty($nspersonal[0])) {
                    // Personal namespace is an array, the first part is the name, the second part is the delimiter.
                    $this->imapnamespace = $nspersonal[0] . $nspersonal[1];
                } else {
                    $this->imapnamespace = '';
                }
            } else {
                $this->imapnamespace = '';
            }
        }

        return $this->imapnamespace . self::CONFIRMATIONFOLDER;
    }

    /**
     * Get the current mailbox name.
     *
     * @return string The current mailbox name.
     * @throws \core\message\inbound\processing_failed_exception if the mailbox could not be opened.
     */
    protected function get_mailbox(): string {
        // Get the current mailbox.
        if ($this->client->selected) {
            return $this->client->selected;
        } else {
            throw new \core\message\inbound\processing_failed_exception('couldnotopenmailbox', 'tool_messageinbound');
        }
    }

    /**
     * Execute the main Inbound Message pickup task.
     *
     * @return bool
     */
    public function pickup_messages(): bool {
        if (!$this->get_imap_client()) {
            return false;
        }

        // Restrict results to messages which are unseen, and have not been flagged.
        mtrace("Searching for Unseen, Unflagged email in the folder '" . self::MAILBOX . "'");
        $result = $this->client->search(
            mailbox: $this->get_mailbox(),
            criteria: 'UNSEEN UNFLAGGED',
            return_uid: true,
        );

        if (empty($result->count())) {
            return false;
        }

        mtrace("Found " . $result->count() . " messages to parse. Parsing...");
        // Retrieve the message id.
        $messages = $result->get();
        $this->addressmanager = new \core\message\inbound\address_manager();
        foreach ($messages as $messageuid) {
            $messageuid = is_numeric($messageuid) ? intval($messageuid) : $messageuid;
            $this->process_message(messageuid: $messageuid);
        }

        // Close the client connection.
        $this->close_connection();

        return true;
    }

    /**
     * Process a message received and validated by the Inbound Message processor.
     *
     * @param \stdClass $maildata The data retrieved from the database for the current record.
     * @return bool Whether the message was successfully processed.
     * @throws \core\message\inbound\processing_failed_exception if the message cannot be found.
     */
    public function process_existing_message(
        \stdClass $maildata,
    ): bool {
        // Grab the new IMAP client.
        if (!$this->get_imap_client(mailbox: $this->get_confirmation_folder())) {
            return false;
        }

        // When dealing with Inbound Message messages, we mark them as flagged and seen. Restrict the search to those criterion.
        mtrace("Searching for a Seen, Flagged message in the folder '" . $this->get_confirmation_folder() . "'");

        // Build the search.
        $result = $this->client->search(
            mailbox: $this->get_mailbox(),
            criteria: 'SEEN FLAGGED TO "' . $maildata->address . '"',
            return_uid: true,
        );

        $this->addressmanager = new \core\message\inbound\address_manager();
        if (!empty($result->count())) {
            $messages = $result->get();
            $targetsequence = 0;
            mtrace("Found " . $result->count() . " messages to parse. Parsing...");
            foreach ($messages as $messageuid) {
                $messageuid = is_numeric($messageuid) ? intval($messageuid) : $messageuid;
                $results = $this->client->fetch(
                    mailbox: $this->get_mailbox(),
                    message_set: $messageuid,
                    is_uid: true,
                    query_items: [
                        'BODY.PEEK[HEADER.FIELDS (Message-ID)]',
                    ],
                );
                $messagedata = reset($results);
                // Match the message id.
                if (htmlentities($messagedata->get('Message-ID', false)) == $maildata->messageid) {
                    // Found the message.
                    $targetsequence = $messageuid;
                    break;
                }
            }
            mtrace("--> Found the message. Passing back to the pickup system.");

            // Process the message.
            $this->process_message(
                messageuid: $targetsequence,
                viewreadmessages: true,
                skipsenderverification: true,
            );

            // Close the client connection.
            $this->close_connection();

            mtrace("============================================================================");
            return true;
        } else {
            // Close the client connection.
            $this->close_connection();

            mtrace("============================================================================");
            throw new \core\message\inbound\processing_failed_exception('oldmessagenotfound', 'tool_messageinbound');
        }
    }

    /**
     * Tidy up old messages in the confirmation folder.
     *
     * @return bool Whether tidying occurred successfully.
     */
    public function tidy_old_messages(): bool {
        // Grab the new IMAP client.
        if (!$this->get_imap_client()) {
            return false;
        }
        // Switch to the confirmation folder.
        $this->select_mailbox(mailbox: $this->get_confirmation_folder());

        // Open the mailbox.
        mtrace("Searching for messages older than 24 hours in the '" .
                $this->get_confirmation_folder() . "' folder.");

        // Delete messages older than 24 hours old.
        $date = date(
            format: 'Y-m-d',
            timestamp: time() - DAYSECS
        );

        // Retrieve the messages and mark them for removal.
        $result = $this->client->search(
            mailbox: $this->get_mailbox(),
            criteria: 'BEFORE "' . $date . '"',
            return_uid: true,
        );

        if (empty($result->count())) {
            $this->close_connection();
            return false;
        }

        mtrace("Found " . $result->count() . " messages for removal.");
        $messages = $result->get();
        foreach ($messages as $messageuid) {
            $messageuid = is_numeric($messageuid) ? intval($messageuid) : $messageuid;
            $this->add_flag_to_message(
                messageuid: $messageuid,
                flag: self::MESSAGE_DELETED
            );
        }

        mtrace("Finished removing messages.");

        $this->close_connection();

        return true;
    }

    /**
     * Remove older verification failures.
     *
     * @return void
     */
    public function tidy_old_verification_failures() {
        global $DB;
        $DB->delete_records_select('messageinbound_messagelist', 'timecreated < :time', ['time' => time() - DAYSECS]);
    }

    /**
     * Process a message and pass it through the Inbound Message handling systems.
     *
     * @param int $messageuid The message uid to process
     * @param bool $viewreadmessages Whether to also look at messages which have been marked as read
     * @param bool $skipsenderverification Whether to skip the sender verification stage
     */
    public function process_message(
        int $messageuid,
        bool $viewreadmessages = false,
        bool $skipsenderverification = false,
    ): void {
        global $USER;

        mtrace("- Parsing message " . $messageuid);

        // First flag this message to prevent another running hitting this message while we look at the headers.
        $this->add_flag_to_message(
            messageuid: $messageuid,
            flag: self::MESSAGE_FLAGGED,
        );

        if ($this->is_bulk_message(messageuid: $messageuid)) {
            mtrace("- The message " . $messageuid . " has a bulk header set. This is likely an auto-generated reply - discarding.");
            return;
        }

        // Record the user that this script is currently being run as.  This is important when re-processing existing
        // messages, as \core\cron::setup_user is called multiple times.
        $originaluser = $USER;

        $envelope = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'BODY.PEEK[HEADER.FIELDS (SUBJECT FROM TO)]',
                'ENVELOPE',
            ],
        );
        $envelope = array_shift($envelope);
        $recipients = $this->get_address_from_envelope(addresslist: $envelope->envelope[5]);
        foreach ($recipients as $recipient) {
            if (!\core\message\inbound\address_manager::is_correct_format($recipient)) {
                // Message did not contain a subaddress.
                mtrace("- Recipient '{$recipient}' did not match Inbound Message headers.");
                continue;
            }

            // Message contained a match.
            $senders = $this->get_address_from_envelope(addresslist: $envelope->envelope[2]);
            if (count($senders) !== 1) {
                mtrace("- Received multiple senders. Only the first sender will be used.");
            }
            $sender = array_shift($senders);

            mtrace("-- Subject:\t"      . $envelope->subject);
            mtrace("-- From:\t"         . $sender);
            mtrace("-- Recipient:\t"    . $recipient);

            // Check whether this message has already been processed.
            if (
                !$viewreadmessages &&
                $this->message_has_flag(
                    messageuid: $messageuid,
                    flag: self::MESSAGE_SEEN,
                )
            ) {
                // Something else has already seen this message. Skip it now.
                mtrace("-- Skipping the message - it has been marked as seen - perhaps by another process.");
                continue;
            }

            // Mark it as read to lock the message.
            $this->add_flag_to_message(
                messageuid: $messageuid,
                flag: self::MESSAGE_SEEN,
            );

            // Now pass it through the Inbound Message processor.
            $status = $this->addressmanager->process_envelope($recipient, $sender);

            if (($status & ~ \core\message\inbound\address_manager::VALIDATION_DISABLED_HANDLER) !== $status) {
                // The handler is disabled.
                mtrace("-- Skipped message - Handler is disabled. Fail code {$status}");
                // In order to handle the user error, we need more information about the message being failed.
                $this->process_message_data(
                    envelope: $envelope,
                    messageuid: $messageuid,
                );
                $this->inform_user_of_error(get_string('handlerdisabled', 'tool_messageinbound', $this->currentmessagedata));
                return;
            }

            // Check the validation status early. No point processing garbage messages, but we do need to process it
            // for some validation failure types.
            if (!$this->passes_key_validation(status: $status)) {
                // None of the above validation failures were found. Skip this message.
                mtrace("-- Skipped message - it does not appear to relate to a Inbound Message pickup. Fail code {$status}");

                // Remove the seen flag from the message as there may be multiple recipients.
                $this->remove_flag_from_message(
                    messageuid: $messageuid,
                    flag: self::MESSAGE_SEEN,
                );

                // Skip further processing for this recipient.
                continue;
            }

            // Process the message as the user.
            $user = $this->addressmanager->get_data()->user;
            mtrace("-- Processing the message as user {$user->id} ({$user->username}).");
            \core\cron::setup_user($user);

            // Process and retrieve the message data for this message.
            // This includes fetching the full content, as well as all headers, and attachments.
            if (
                !$this->process_message_data(
                    envelope: $envelope,
                    messageuid: $messageuid,
                )
            ) {
                mtrace("--- Message could not be found on the server. Is another process removing messages?");
                return;
            }

            // When processing validation replies, we need to skip the sender verification phase as this has been
            // manually completed.
            if (!$skipsenderverification && $status !== 0) {
                // Check the validation status for failure types which require confirmation.
                // The validation result is tested in a bitwise operation.
                mtrace("-- Message did not meet validation but is possibly recoverable. Fail code {$status}");
                // This is a recoverable error, but requires user input.

                if (
                    $this->handle_verification_failure(
                        messageuid: $messageuid,
                        recipient: $recipient,
                    )
                ) {
                    mtrace("--- Original message retained on mail server and confirmation message sent to user.");
                } else {
                    mtrace("--- Invalid Recipient Handler - unable to save. Informing the user of the failure.");
                    $this->inform_user_of_error(get_string('invalidrecipientfinal', 'tool_messageinbound', $this->currentmessagedata));
                }

                // Returning to normal cron user.
                mtrace("-- Returning to the original user.");
                \core\cron::setup_user($originaluser);
                return;
            }

            // Add the content and attachment data.
            mtrace("-- Validation completed. Fetching rest of message content.");
            $this->process_message_data_body(messageuid: $messageuid);

            // The message processor throws exceptions upon failure. These must be caught and notifications sent to
            // the user here.
            try {
                $result = $this->send_to_handler();
            } catch (\core\message\inbound\processing_failed_exception $e) {
                // We know about these kinds of errors and they should result in the user being notified of the
                // failure. Send the user a notification here.
                $this->inform_user_of_error($e->getMessage());

                // Returning to normal cron user.
                mtrace("-- Returning to the original user.");
                \core\cron::setup_user($originaluser);
                return;
            } catch (\Exception $e) {
                // An unknown error occurred. The user is not informed, but the administrator is.
                mtrace("-- Message processing failed. An unexpected exception was thrown. Details follow.");
                mtrace($e->getMessage());

                // Returning to normal cron user.
                mtrace("-- Returning to the original user.");
                \core\cron::setup_user($originaluser);
                return;
            }

            if ($result) {
                // Handle message cleanup. Messages are deleted once fully processed.
                mtrace("-- Marking the message for removal.");
                $this->add_flag_to_message(
                    messageuid: $messageuid,
                    flag: self::MESSAGE_DELETED
                );
            } else {
                mtrace("-- The Inbound Message processor did not return a success status. Skipping message removal.");
            }

            // Returning to normal cron user.
            mtrace("-- Returning to the original user.");
            \core\cron::setup_user($originaluser);

            mtrace("-- Finished processing " . $messageuid);

            // Skip the outer loop too. The message has already been processed and it could be possible for there to
            // be two recipients in the envelope which match somehow.
            return;
        }
    }

    /**
     * Process a message to retrieve it's header data without body.
     *
     * @param \rcube_message_header $envelope The Envelope of the message
     * @param int $messageuid The message Uid to process
     * @return \stdClass|null The current value of the messagedata
     */
    private function process_message_data(
        \rcube_message_header $envelope,
        int $messageuid,
    ): ?\stdClass {
        // Retrieve the message with necessary information.
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'BODY.PEEK[HEADER.FIELDS (Message-ID SUBJECT DATE)]',
            ],
        );
        $messagedata = reset($messages);

        if (!$messagedata) {
            // Message was not found! Somehow it has been removed or is no longer returned.
            return null;
        }

        // The message ID should always be in the first part.
        $data = new \stdClass();
        $data->messageid = htmlentities($messagedata->get('Message-ID', false));
        $data->subject = $messagedata->get('SUBJECT', false);
        $data->timestamp = strtotime($messagedata->get('DATE', false));
        $data->envelope = $envelope;
        $data->data = $this->addressmanager->get_data();

        $this->currentmessagedata = $data;

        return $this->currentmessagedata;
    }

    /**
     * Process a message again to add body and attachment data.
     *
     * @param int $messageuid The message Uid
     * @return \stdClass|null The current value of the messagedata
     */
    private function process_message_data_body(
        int $messageuid,
    ): ?\stdClass {
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'BODYSTRUCTURE',
            ],
        );
        $messagedata = reset($messages);
        $structure = $messagedata->bodystructure;

        // Store the data for this message.
        $contentplain = '';
        $contenthtml = '';
        $attachments = [
            'inline' => [],
            'attachment' => [],
        ];
        $parameters = [];
        foreach ($structure as $partno => $part) {
            if (!is_array($part)) {
                continue;
            }
            $section = $partno + 1;

            // Subpart recursion.
            if (is_array($part[0])) {
                foreach ($part as $subpartno => $subpart) {
                    if (!is_array($subpart)) {
                        continue;
                    }
                    $subsection = $subpartno + 1;
                    $this->process_message_data_body_part(
                        messageuid: $messageuid,
                        partstructure: $subpart,
                        section: $section . '.' . $subsection,
                        contentplain: $contentplain,
                        contenthtml: $contenthtml,
                        attachments: $attachments,
                        parameters: $parameters,
                    );
                }
            } else {
                $this->process_message_data_body_part(
                    messageuid: $messageuid,
                    partstructure: $part,
                    section: $section,
                    contentplain: $contentplain,
                    contenthtml: $contenthtml,
                    attachments: $attachments,
                    parameters: $parameters,
                );
            }
        }

        // The message ID should always be in the first part.
        $this->currentmessagedata->plain = $contentplain;
        $this->currentmessagedata->html = $contenthtml;
        $this->currentmessagedata->attachments = $attachments;

        return $this->currentmessagedata;
    }

    /**
     * Process message data body part.
     *
     * @param int $messageuid Message uid to process.
     * @param array $partstructure Body part structure.
     * @param string $section Section number.
     * @param string $contentplain Plain text content.
     * @param string $contenthtml HTML content.
     * @param array $attachments Attachments.
     * @param array $parameters Parameters.
     */
    private function process_message_data_body_part(
        int $messageuid,
        array $partstructure,
        string $section,
        string &$contentplain,
        string &$contenthtml,
        array &$attachments,
        array &$parameters,
    ): void {
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'BODY[' . $section . ']',
            ],
        );
        if ($messages) {
            $messagedata = reset($messages);

            // Parse encoding.
            $encoding = array_search(
                needle: strtoupper($partstructure[5]),
                haystack: utils::get_body_encoding(),
            );

            // Parse subtype.
            $subtype = strtoupper($partstructure[1]);

            // Section part may be encoded, even plain text messages, so check everything.
            if ($encoding == utils::ENCQUOTEDPRINTABLE) {
                $data = quoted_printable_decode($messagedata->bodypart[$section]);
            } else if ($encoding == utils::ENCBASE64) {
                $data = base64_decode($messagedata->bodypart[$section]);
            } else {
                $data = $messagedata->bodypart[$section];
            }

            // Parse parameters.
            $parameters = $this->process_message_body_structure_parameters(
                attributes: $partstructure[2],
                parameters: $parameters,
            );

            // Parse content id.
            $contentid = '';
            if (!empty($partstructure[3])) {
                $contentid = htmlentities($partstructure[3]);
            }

            // Parse description.
            $description = '';
            if (!empty($partstructure[4])) {
                $description = $partstructure[4];
            }

            // Parse size of contents in bytes.
            $bytes = intval($partstructure[6]);

            // PLAIN text.
            if ($subtype == 'PLAIN') {
                $contentplain = $this->process_message_part_body(
                    bodycontent: $data,
                    charset: $parameters['CHARSET'],
                );
            }
            // HTML.
            if ($subtype == 'HTML') {
                $contenthtml = $this->process_message_part_body(
                    bodycontent: $data,
                    charset: $parameters['CHARSET'],
                );
            }
            // ATTACHMENT.
            if (isset($parameters['NAME']) || isset($parameters['FILENAME'])) {
                $filename = $parameters['NAME'] ?? $parameters['FILENAME'];
                if (
                    $attachment = $this->process_message_part_attachment(
                        filename: $filename,
                        filecontent: $data,
                        contentid: $contentid,
                        filesize: $bytes,
                        description: $description,
                    )
                ) {
                    // Parse disposition.
                    $disposition = null;
                    if (is_array($partstructure[8])) {
                        $disposition = strtolower($partstructure[8][0]);
                    }
                    $disposition = $disposition == 'inline' ? 'inline' : 'attachment';
                    $attachments[$disposition][] = $attachment;
                }
            }
        }
    }

    /**
     * Process message data body parameters.
     *
     * @param array $attributes List of attributes.
     * @param array $parameters List of parameters.
     * @return array
     */
    private function process_message_body_structure_parameters(
        array $attributes,
        array $parameters,
    ): array {
        if (empty($attributes)) {
            return [];
        }

        $attribute = null;

        foreach ($attributes as $value) {
            if (empty($attribute)) {
                $attribute = [
                    'attribute' => $value,
                    'value' => null,
                ];
            } else {
                $attribute['value'] = $value;
                $parameters[] = (object) $attribute;
                $attribute = null;
            }
        }

        $params = [];
        foreach ($parameters as $parameter) {
            if (isset($parameter->attribute)) {
                $params[$parameter->attribute] = $parameter->value;
            }
        }

        return $params;
    }

    /**
     * Process the message body content.
     *
     * @param string $bodycontent The message body.
     * @param string $charset The charset of the message body.
     * @return string Processed content.
     */
    private function process_message_part_body(
        string $bodycontent,
        string $charset,
    ): string {
        // This is a content section for the main body.
        // Convert the text from the current encoding to UTF8.
        $content = \core_text::convert($bodycontent, $charset);

        // Fix any invalid UTF8 characters.
        // Note: XSS cleaning is not the responsibility of this code. It occurs immediately before display when
        // format_text is called.
        $content = clean_param($content, PARAM_RAW);

        return $content;
    }

    /**
     * Process a message again to add body and attachment data.
     *
     * @param string $filename The filename of the attachment.
     * @param string $filecontent The content of the attachment.
     * @param string $contentid The content id of the attachment.
     * @param int $filesize The size of the attachment.
     * @param string $description The description of the attachment.
     * @return \stdClass
     */
    private function process_message_part_attachment(
        string $filename,
        string $filecontent,
        string $contentid,
        int $filesize,
        string $description = '',
    ): \stdClass {
        global $CFG;

        // If a filename is present, assume that this part is an attachment.
        $attachment = new \stdClass();
        $attachment->filename = $filename;
        $attachment->content = $filecontent;
        $attachment->description = $description;
        $attachment->contentid = $contentid;
        $attachment->filesize = $filesize;

        if (!empty($CFG->antiviruses)) {
            // Virus scanning is removed and will be brought back by MDL-50434.
        }

        return $attachment;
    }

    /**
     * Check whether the key provided is valid.
     *
     * @param int $status The status to validate.
     * @return bool
     */
    private function passes_key_validation(
        int $status,
    ): bool {
        // The validation result is tested in a bitwise operation.
        if ((
            $status & ~ \core\message\inbound\address_manager::VALIDATION_SUCCESS
                    & ~ \core\message\inbound\address_manager::VALIDATION_UNKNOWN_DATAKEY
                    & ~ \core\message\inbound\address_manager::VALIDATION_EXPIRED_DATAKEY
                    & ~ \core\message\inbound\address_manager::VALIDATION_INVALID_HASH
                    & ~ \core\message\inbound\address_manager::VALIDATION_ADDRESS_MISMATCH) !== 0) {

            // One of the above bits was found in the status - fail the validation.
            return false;
        }
        return true;
    }

    /**
     * Add the specified flag to the message.
     *
     * @param int $messageuid Message uid to process
     * @param string $flag The flag to add
     */
    private function add_flag_to_message(
        int $messageuid,
        string $flag,
    ): void {
        // Add flag to the message.
        $this->client->flag(
            mailbox: $this->get_mailbox(),
            messages: $messageuid,
            flag: strtoupper(substr($flag, 1)),
        );
    }

    /**
     * Remove the specified flag from the message.
     *
     * @param int $messageuid Message uid to process
     * @param string $flag The flag to remove
     */
    private function remove_flag_from_message(
        int $messageuid,
        string $flag,
    ): void {
        // Remove the flag from the message.
        $this->client->unflag(
            mailbox: $this->get_mailbox(),
            messages: $messageuid,
            flag: strtoupper(substr($flag, 1)),
        );
    }

    /**
     * Check whether the message has the specified flag
     *
     * @param int $messageuid Message uid to check.
     * @param string $flag The flag to check.
     * @return bool True if the message has the flag, false otherwise.
     */
    private function message_has_flag(
        int $messageuid,
        string $flag,
    ): bool {
        // Grab the message data with flags.
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'FLAGS',
            ],
        );
        $messagedata = reset($messages);
        $flags = $messagedata->flags;
        return array_key_exists(
            key: strtoupper(substr($flag, 1)),
            array: $flags,
        );
    }

    /**
     * Ensure that all mailboxes exist.
     */
    private function ensure_mailboxes_exist(): void {
        $requiredmailboxes = [
            self::MAILBOX,
            $this->get_confirmation_folder(),
        ];

        $existingmailboxes = $this->client->listMailboxes(
            ref: '',
            mailbox: '*',
        );
        foreach ($requiredmailboxes as $mailbox) {
            if (in_array($mailbox, $existingmailboxes)) {
                // This mailbox was found.
                continue;
            }

            mtrace("Unable to find the '{$mailbox}' mailbox - creating it.");
            $this->client->createFolder(
                mailbox: $mailbox,
            );
        }
    }

    /**
     * Attempt to determine whether this message is a bulk message (e.g. automated reply).
     *
     * @param int $messageuid The message uid to check
     * @return boolean
     */
    private function is_bulk_message(
        int $messageuid,
    ): bool {
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'BODY.PEEK[HEADER.FIELDS (Precedence X-Autoreply X-Autorespond Auto-Submitted)]',
            ],
        );
        $headerinfo = reset($messages);
        // Assume that this message is not bulk to begin with.
        $isbulk = false;

        // An auto-reply may itself include the Bulk Precedence.
        $precedence = $headerinfo->get('Precedence', false);
        $isbulk = $isbulk || strtolower($precedence ?? '') == 'bulk';

        // If the X-Autoreply header is set, and not 'no', then this is an automatic reply.
        $autoreply = $headerinfo->get('X-Autoreply', false);
        $isbulk = $isbulk || ($autoreply && $autoreply != 'no');

        // If the X-Autorespond header is set, and not 'no', then this is an automatic response.
        $autorespond = $headerinfo->get('X-Autorespond', false);
        $isbulk = $isbulk || ($autorespond && $autorespond != 'no');

        // If the Auto-Submitted header is set, and not 'no', then this is a non-human response.
        $autosubmitted = $headerinfo->get('Auto-Submitted', false);
        $isbulk = $isbulk || ($autosubmitted && $autosubmitted != 'no');

        return $isbulk;
    }

    /**
     * Send the message to the appropriate handler.
     *
     * @return bool
     * @throws \core\message\inbound\processing_failed_exception if anything goes wrong.
     */
    private function send_to_handler() {
        try {
            mtrace("--> Passing to Inbound Message handler {$this->addressmanager->get_handler()->classname}");
            if ($result = $this->addressmanager->handle_message($this->currentmessagedata)) {
                $this->inform_user_of_success($this->currentmessagedata, $result);
                // Request that this message be marked for deletion.
                return true;
            }

        } catch (\core\message\inbound\processing_failed_exception $e) {
            mtrace("-> The Inbound Message handler threw an exception. Unable to process this message. The user has been informed.");
            mtrace("--> " . $e->getMessage());
            // Throw the exception again, with additional data.
            $error = new \stdClass();
            $error->subject     = $this->currentmessagedata->envelope->subject;
            $error->message     = $e->getMessage();
            throw new \core\message\inbound\processing_failed_exception('messageprocessingfailed', 'tool_messageinbound', $error);

        } catch (\Exception $e) {
            mtrace("-> The Inbound Message handler threw an exception. Unable to process this message. User informed.");
            mtrace("--> " . $e->getMessage());
            // An unknown error occurred. Still inform the user but, this time do not include the specific
            // message information.
            $error = new \stdClass();
            $error->subject     = $this->currentmessagedata->envelope->subject;
            throw new \core\message\inbound\processing_failed_exception('messageprocessingfailedunknown',
                    'tool_messageinbound', $error);

        }

        // Something went wrong and the message was not handled well in the Inbound Message handler.
        mtrace("-> The Inbound Message handler reported an error. The message may not have been processed.");

        // It is the responsiblity of the handler to throw an appropriate exception if the message was not processed.
        // Do not inform the user at this point.
        return false;
    }

    /**
     * Handle failure of sender verification.
     *
     * This will send a notification to the user identified in the Inbound Message address informing them that a message has been
     * stored. The message includes a verification link and reply-to address which is handled by the
     * invalid_recipient_handler.
     *
     * @param int $messageuid The message uid to process.
     * @param string $recipient The message recipient
     * @return bool
     */
    private function handle_verification_failure(
        int $messageuid,
        string $recipient,
    ): bool {
        global $DB, $USER;

        $messageid = $this->get_message_sequence_from_uid($messageuid);
        if ($messageid == $this->currentmessagedata->messageid) {
            mtrace("---> Warning: Unable to determine the Message-ID of the message.");
            return false;
        }

        // Move the message into a new mailbox.
        $this->client->move(
            messages: $messageuid,
            from: $this->get_mailbox(),
            to: $this->get_confirmation_folder(),
        );

        // Store the data from the failed message in the associated table.
        $record = new \stdClass();
        $record->messageid = $messageuid;
        $record->userid = $USER->id;
        $record->address = $recipient;
        $record->timecreated = time();
        $record->id = $DB->insert_record('messageinbound_messagelist', $record);

        // Setup the Inbound Message generator for the invalid recipient handler.
        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data($record->id);

        $eventdata = new \core\message\message();
        $eventdata->component           = 'tool_messageinbound';
        $eventdata->name                = 'invalidrecipienthandler';

        $userfrom = clone $USER;
        $userfrom->customheaders = array();
        // Adding the In-Reply-To header ensures that it is seen as a reply.
        $userfrom->customheaders[] = 'In-Reply-To: ' . $messageuid;

        // The message will be sent from the intended user.
        $eventdata->courseid            = SITEID;
        $eventdata->userfrom            = \core_user::get_noreply_user();
        $eventdata->userto              = $USER;
        $eventdata->subject             = $this->get_reply_subject($this->currentmessagedata->envelope->subject);
        $eventdata->fullmessage         = get_string('invalidrecipientdescription', 'tool_messageinbound', $this->currentmessagedata);
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = get_string('invalidrecipientdescriptionhtml', 'tool_messageinbound', $this->currentmessagedata);
        $eventdata->smallmessage        = $eventdata->fullmessage;
        $eventdata->notification        = 1;
        $eventdata->replyto             = $addressmanager->generate($USER->id);

        mtrace("--> Sending a message to the user to report an verification failure.");
        if (!message_send($eventdata)) {
            mtrace("---> Warning: Message could not be sent.");
            return false;
        }

        return true;
    }

    /**
     * Inform the identified sender of a processing error.
     *
     * @param string $error The error message
     */
    private function inform_user_of_error($error) {
        global $USER;

        // The message will be sent from the intended user.
        $userfrom = clone $USER;
        $userfrom->customheaders = array();

        if ($messageid = $this->currentmessagedata->messageid) {
            // Adding the In-Reply-To header ensures that it is seen as a reply and threading is maintained.
            $userfrom->customheaders[] = 'In-Reply-To: ' . $messageid;
        }

        $messagedata = new \stdClass();
        $messagedata->subject = $this->currentmessagedata->envelope->subject;
        $messagedata->error = $error;

        $eventdata = new \core\message\message();
        $eventdata->courseid            = SITEID;
        $eventdata->component           = 'tool_messageinbound';
        $eventdata->name                = 'messageprocessingerror';
        $eventdata->userfrom            = $userfrom;
        $eventdata->userto              = $USER;
        $eventdata->subject             = self::get_reply_subject($this->currentmessagedata->envelope->subject);
        $eventdata->fullmessage         = get_string('messageprocessingerror', 'tool_messageinbound', $messagedata);
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = get_string('messageprocessingerrorhtml', 'tool_messageinbound', $messagedata);
        $eventdata->smallmessage        = $eventdata->fullmessage;
        $eventdata->notification        = 1;

        if (message_send($eventdata)) {
            mtrace("---> Notification sent to {$USER->email}.");
        } else {
            mtrace("---> Unable to send notification.");
        }
    }

    /**
     * Inform the identified sender that message processing was successful.
     *
     * @param \stdClass $messagedata The data for the current message being processed.
     * @param mixed $handlerresult The result returned by the handler.
     * @return bool
     */
    private function inform_user_of_success(\stdClass $messagedata, $handlerresult) {
        global $USER;

        // Check whether the handler has a success notification.
        $handler = $this->addressmanager->get_handler();
        $message = $handler->get_success_message($messagedata, $handlerresult);

        if (!$message) {
            mtrace("---> Handler has not defined a success notification e-mail.");
            return false;
        }

        // Wrap the message in the notification wrapper.
        $messageparams = new \stdClass();
        $messageparams->html    = $message->html;
        $messageparams->plain   = $message->plain;
        $messagepreferencesurl = new \moodle_url("/message/notificationpreferences.php", array('id' => $USER->id));
        $messageparams->messagepreferencesurl = $messagepreferencesurl->out();
        $htmlmessage = get_string('messageprocessingsuccesshtml', 'tool_messageinbound', $messageparams);
        $plainmessage = get_string('messageprocessingsuccess', 'tool_messageinbound', $messageparams);

        // The message will be sent from the intended user.
        $userfrom = clone $USER;
        $userfrom->customheaders = array();

        if ($messageid = $this->currentmessagedata->messageid) {
            // Adding the In-Reply-To header ensures that it is seen as a reply and threading is maintained.
            $userfrom->customheaders[] = 'In-Reply-To: ' . $messageid;
        }

        $messagedata = new \stdClass();
        $messagedata->subject = $this->currentmessagedata->envelope->subject;

        $eventdata = new \core\message\message();
        $eventdata->courseid            = SITEID;
        $eventdata->component           = 'tool_messageinbound';
        $eventdata->name                = 'messageprocessingsuccess';
        $eventdata->userfrom            = $userfrom;
        $eventdata->userto              = $USER;
        $eventdata->subject             = self::get_reply_subject($this->currentmessagedata->envelope->subject);
        $eventdata->fullmessage         = $plainmessage;
        $eventdata->fullmessageformat   = FORMAT_PLAIN;
        $eventdata->fullmessagehtml     = $htmlmessage;
        $eventdata->smallmessage        = $eventdata->fullmessage;
        $eventdata->notification        = 1;

        if (message_send($eventdata)) {
            mtrace("---> Success notification sent to {$USER->email}.");
        } else {
            mtrace("---> Unable to send success notification.");
        }
        return true;
    }

    /**
     * Return a formatted subject line for replies.
     *
     * @param string $subject The subject string
     * @return string The formatted reply subject
     */
    private function get_reply_subject($subject) {
        $prefix = get_string('replysubjectprefix', 'tool_messageinbound');
        if (!(substr($subject, 0, strlen($prefix)) == $prefix)) {
            $subject = $prefix . ' ' . $subject;
        }

        return $subject;
    }

    /**
     * Parse the address from the envelope.
     *
     * @param array $addresslist List of email addresses to parse.
     * @return array|null List of parsed email addresses.
     */
    protected function get_address_from_envelope(array $addresslist): array|null {
        if (empty($addresslist)) {
            return null;
        }

        $parsedaddressentry = [];
        foreach ($addresslist as $addressentry) {
            $parsedaddressentry[] = "{$addressentry[2]}@{$addressentry[3]}";
        }

        return $parsedaddressentry;
    }

    /**
     * Get the message sequence number from the message uid.
     *
     * @param int $messageuid The message uid to process.
     * @return int The message sequence number.
     */
    protected function get_message_sequence_from_uid(
        int $messageuid,
    ): int {
        $messages = $this->client->fetch(
            mailbox: $this->get_mailbox(),
            message_set: $messageuid,
            is_uid: true,
            query_items: [
                'SEQUENCE',
            ],
        );
        $messagedata = reset($messages);
        return $messagedata->sequence;
    }

    /**
     * Switch mailbox.
     *
     * @param string $mailbox The mailbox to switch to.
     */
    protected function select_mailbox(
        string $mailbox,
    ): void {
        $this->client->select(mailbox: $mailbox);
    }

    /**
     * We use Roundcube Framework to receive the emails.
     * This method will load the required dependencies.
     */
    protected function load_dependencies(): void {
        global $CFG;
        $dependencies = [
            'rcube_charset.php',
            'rcube_imap_generic.php',
            'rcube_message_header.php',
            'rcube_mime.php',
            'rcube_result_index.php',
            'rcube_result_thread.php',
            'rcube_utils.php',
        ];

        array_map(fn($file) => require_once("$CFG->dirroot/$CFG->admin/tool/messageinbound/roundcube/{$file}"), $dependencies);
    }
}
