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

defined('MOODLE_INTERNAL') || die();

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
     * @var Horde_Imap_Client_Socket A reference to the IMAP client.
     */
    protected $client = null;

    /**
     * @var \core\message\inbound\address_manager A reference to the Inbound Message Address Manager instance.
     */
    protected $addressmanager = null;

    /**
     * @var stdClass The data for the current message being processed.
     */
    protected $currentmessagedata = null;

    /**
     * Retrieve the connection to the IMAP client.
     *
     * @return bool Whether a connection was successfully established.
     */
    protected function get_imap_client() {
        global $CFG;

        if (!\core\message\inbound\manager::is_enabled()) {
            // E-mail processing not set up.
            mtrace("Inbound Message not fully configured - exiting early.");
            return false;
        }

        mtrace("Connecting to {$CFG->messageinbound_host} as {$CFG->messageinbound_hostuser}...");

        $configuration = array(
            'username' => $CFG->messageinbound_hostuser,
            'password' => $CFG->messageinbound_hostpass,
            'hostspec' => $CFG->messageinbound_host,
            'secure'   => $CFG->messageinbound_hostssl,
        );

        $this->client = new \Horde_Imap_Client_Socket($configuration);

        try {
            $this->client->login();
            mtrace("Connection established.");
            return true;

        } catch (\Horde_Imap_Client_Exception $e) {
            $message = $e->getMessage();
            mtrace("Unable to connect to IMAP server. Failed with '{$message}'");

            return false;
        }
    }

    /**
     * Shutdown and close the connection to the IMAP client.
     */
    protected function close_connection() {
        if ($this->client) {
            $this->client->close();
        }
        $this->client = null;
    }

    /**
     * Get the current mailbox information.
     *
     * @return \Horde_Imap_Client_Mailbox
     */
    protected function get_mailbox() {
        // Get the current mailbox.
        $mailbox = $this->client->currentMailbox();

        if (isset($mailbox['mailbox'])) {
            return $mailbox['mailbox'];
        } else {
            throw new \core\message\inbound\processing_failed_exception('couldnotopenmailbox', 'tool_messageinbound');
        }
    }

    /**
     * Execute the main Inbound Message pickup task.
     */
    public function pickup_messages() {
        if (!$this->get_imap_client()) {
            return false;
        }

        // Restrict results to messages which are unseen, and have not been flagged.
        $search = new \Horde_Imap_Client_Search_Query();
        $search->flag(self::MESSAGE_SEEN, false);
        $search->flag(self::MESSAGE_FLAGGED, false);
        mtrace("Searching for Unseen, Unflagged email in the folder '" . self::MAILBOX . "'");
        $results = $this->client->search(self::MAILBOX, $search);

        // We require the envelope data and structure of each message.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->envelope();
        $query->structure();

        // Retrieve the message id.
        $messages = $this->client->fetch(self::MAILBOX, $query, array('ids' => $results['match']));

        mtrace("Found " . $messages->count() . " messages to parse. Parsing...");
        $this->addressmanager = new \core\message\inbound\address_manager();
        foreach ($messages as $message) {
            $this->process_message($message);
        }

        // Close the client connection.
        $this->close_connection();

        return true;
    }

    /**
     * Process a message received and validated by the Inbound Message processor.
     *
     * @param stdClass $maildata The data retrieved from the database for the current record.
     * @return bool Whether the message was successfully processed.
     */
    public function process_existing_message(\stdClass $maildata) {
        // Grab the new IMAP client.
        if (!$this->get_imap_client()) {
            return false;
        }

        // Build the search.
        $search = new \Horde_Imap_Client_Search_Query();
        // When dealing with Inbound Message messages, we mark them as flagged and seen. Restrict the search to those criterion.
        $search->flag(self::MESSAGE_SEEN, true);
        $search->flag(self::MESSAGE_FLAGGED, true);
        mtrace("Searching for a Seen, Flagged message in the folder '" . self::CONFIRMATIONFOLDER . "'");

        // Match the message ID.
        $search->headerText('message-id', $maildata->messageid);
        $search->headerText('to', $maildata->address);

        $results = $this->client->search(self::CONFIRMATIONFOLDER, $search);

        // Build the base query.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->envelope();
        $query->structure();


        // Fetch the first message from the client.
        $messages = $this->client->fetch(self::CONFIRMATIONFOLDER, $query, array('ids' => $results['match']));
        $this->addressmanager = new \core\message\inbound\address_manager();
        if ($message = $messages->first()) {
            mtrace("--> Found the message. Passing back to the pickup system.");

            // Process the message.
            $this->process_message($message, true, true);

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
    public function tidy_old_messages() {
        // Grab the new IMAP client.
        if (!$this->get_imap_client()) {
            return false;
        }

        // Open the mailbox.
        mtrace("Searching for messages older than 24 hours in the '" .
                self::CONFIRMATIONFOLDER . "' folder.");
        $this->client->openMailbox(self::CONFIRMATIONFOLDER);

        $mailbox = $this->get_mailbox();

        // Build the search.
        $search = new \Horde_Imap_Client_Search_Query();

        // Delete messages older than 24 hours old.
        $search->intervalSearch(DAYSECS, \Horde_Imap_Client_Search_Query::INTERVAL_OLDER);

        $results = $this->client->search($mailbox, $search);

        // Build the base query.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->envelope();

        // Retrieve the messages and mark them for removal.
        $messages = $this->client->fetch($mailbox, $query, array('ids' => $results['match']));
        mtrace("Found " . $messages->count() . " messages for removal.");
        foreach ($messages as $message) {
            $this->add_flag_to_message($message->getUid(), self::MESSAGE_DELETED);
        }

        mtrace("Finished removing messages.");
        $this->close_connection();

        return true;
    }

    /**
     * Process a message and pass it through the Inbound Message handling systems.
     *
     * @param Horde_Imap_Client_Data_Fetch $message The message to process
     * @param bool $viewreadmessages Whether to also look at messages which have been marked as read
     * @param bool $skipsenderverification Whether to skip the sender verificiation stage
     */
    public function process_message(
            \Horde_Imap_Client_Data_Fetch $message,
            $viewreadmessages = false,
            $skipsenderverification = false) {
        global $USER;

        // We use the Client IDs several times - store them here.
        $messageid = new \Horde_Imap_Client_Ids($message->getUid());

        mtrace("- Parsing message " . $messageid);

        // First flag this message to prevent another running hitting this message while we look at the headers.
        $this->add_flag_to_message($messageid, self::MESSAGE_FLAGGED);

        if ($this->is_bulk_message($message, $messageid)) {
            mtrace("- The message has a bulk header set. This is likely an auto-generated reply - discarding.");
            return;
        }

        // Record the user that this script is currently being run as.  This is important when re-processing existing
        // messages, as cron_setup_user is called multiple times.
        $originaluser = $USER;

        $envelope = $message->getEnvelope();
        $recipients = $envelope->to->bare_addresses;
        foreach ($recipients as $recipient) {
            if (!\core\message\inbound\address_manager::is_correct_format($recipient)) {
                // Message did not contain a subaddress.
                mtrace("- Recipient '{$recipient}' did not match Inbound Message headers.");
                continue;
            }

            // Message contained a match.
            $senders = $message->getEnvelope()->from->bare_addresses;
            if (count($senders) !== 1) {
                mtrace("- Received multiple senders. Only the first sender will be used.");
            }
            $sender = array_shift($senders);

            mtrace("-- Subject:\t"      . $envelope->subject);
            mtrace("-- From:\t"         . $sender);
            mtrace("-- Recipient:\t"    . $recipient);

            // Grab messagedata including flags.
            $query = new \Horde_Imap_Client_Fetch_Query();
            $query->structure();
            $messagedata = $this->client->fetch($this->get_mailbox(), $query, array(
                'ids' => $messageid,
            ))->first();

            if (!$viewreadmessages && $this->message_has_flag($messageid, self::MESSAGE_SEEN)) {
                // Something else has already seen this message. Skip it now.
                mtrace("-- Skipping the message - it has been marked as seen - perhaps by another process.");
                continue;
            }

            // Mark it as read to lock the message.
            $this->add_flag_to_message($messageid, self::MESSAGE_SEEN);

            // Now pass it through the Inbound Message processor.
            $status = $this->addressmanager->process_envelope($recipient, $sender);

            if (($status & ~ \core\message\inbound\address_manager::VALIDATION_DISABLED_HANDLER) !== $status) {
                // The handler is disabled.
                mtrace("-- Skipped message - Handler is disabled. Fail code {$status}");
                // In order to handle the user error, we need more information about the message being failed.
                $this->process_message_data($envelope, $messagedata, $messageid);
                $this->inform_user_of_error(get_string('handlerdisabled', 'tool_messageinbound', $this->currentmessagedata));
                return;
            }

            // Check the validation status early. No point processing garbage messages, but we do need to process it
            // for some validation failure types.
            if (!$this->passes_key_validation($status, $messageid)) {
                // None of the above validation failures were found. Skip this message.
                mtrace("-- Skipped message - it does not appear to relate to a Inbound Message pickup. Fail code {$status}");

                // Remove the seen flag from the message as there may be multiple recipients.
                $this->remove_flag_from_message($messageid, self::MESSAGE_SEEN);

                // Skip further processing for this recipient.
                continue;
            }

            // Process the message as the user.
            $user = $this->addressmanager->get_data()->user;
            mtrace("-- Processing the message as user {$user->id} ({$user->username}).");
            cron_setup_user($user);

            // Process and retrieve the message data for this message.
            // This includes fetching the full content, as well as all headers, and attachments.
            if (!$this->process_message_data($envelope, $messagedata, $messageid)) {
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

                if ($this->handle_verification_failure($messageid, $recipient)) {
                    mtrace("--- Original message retained on mail server and confirmation message sent to user.");
                } else {
                    mtrace("--- Invalid Recipient Handler - unable to save. Informing the user of the failure.");
                    $this->inform_user_of_error(get_string('invalidrecipientfinal', 'tool_messageinbound', $this->currentmessagedata));
                }

                // Returning to normal cron user.
                mtrace("-- Returning to the original user.");
                cron_setup_user($originaluser);
                return;
            }

            // Add the content and attachment data.
            mtrace("-- Validation completed. Fetching rest of message content.");
            $this->process_message_data_body($messagedata, $messageid);

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
                cron_setup_user($originaluser);
                return;
            } catch (\Exception $e) {
                // An unknown error occurred. The user is not informed, but the administrator is.
                mtrace("-- Message processing failed. An unexpected exception was thrown. Details follow.");
                mtrace($e->getMessage());

                // Returning to normal cron user.
                mtrace("-- Returning to the original user.");
                cron_setup_user($originaluser);
                return;
            }

            if ($result) {
                // Handle message cleanup. Messages are deleted once fully processed.
                mtrace("-- Marking the message for removal.");
                $this->add_flag_to_message($messageid, self::MESSAGE_DELETED);
            } else {
                mtrace("-- The Inbound Message processor did not return a success status. Skipping message removal.");
            }

            // Returning to normal cron user.
            mtrace("-- Returning to the original user.");
            cron_setup_user($originaluser);

            mtrace("-- Finished processing " . $message->getUid());

            // Skip the outer loop too. The message has already been processed and it could be possible for there to
            // be two recipients in the envelope which match somehow.
            return;
        }
    }

    /**
     * Process a message to retrieve it's header data without body and attachemnts.
     *
     * @param Horde_Imap_Client_Data_Envelope $envelope The Envelope of the message
     * @param Horde_Imap_Client_Data_Fetch $messagedata The structure and part of the message body
     * @param string|Horde_Imap_Client_Ids $messageid The Hore message Uid
     * @return \stdClass The current value of the messagedata
     */
    private function process_message_data(
            \Horde_Imap_Client_Data_Envelope $envelope,
            \Horde_Imap_Client_Data_Fetch $basemessagedata,
            $messageid) {

        // Get the current mailbox.
        $mailbox = $this->get_mailbox();

        // We need the structure at various points below.
        $structure = $basemessagedata->getStructure();

        // Now fetch the rest of the message content.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->imapDate();

        // Fetch the message header.
        $query->headerText();

        // Retrieve the message with the above components.
        $messagedata = $this->client->fetch($mailbox, $query, array('ids' => $messageid))->first();

        if (!$messagedata) {
            // Message was not found! Somehow it has been removed or is no longer returned.
            return null;
        }

        // The message ID should always be in the first part.
        $data = new \stdClass();
        $data->messageid = $messagedata->getHeaderText(0, \Horde_Imap_Client_Data_Fetch::HEADER_PARSE)->getValue('Message-ID');
        $data->subject = $envelope->subject;
        $data->timestamp = $messagedata->getImapDate()->__toString();
        $data->envelope = $envelope;
        $data->data = $this->addressmanager->get_data();
        $data->headers = $messagedata->getHeaderText();

        $this->currentmessagedata = $data;

        return $this->currentmessagedata;
    }

    /**
     * Process a message again to add body and attachment data.
     *
     * @param \Horde_Imap_Client_Data_Fetch $basemessagedata The structure and part of the message body
     * @param string|\Horde_Imap_Client_Ids $messageid The Hore message Uid
     * @return \stdClass The current value of the messagedata
     */
    private function process_message_data_body(
            \Horde_Imap_Client_Data_Fetch $basemessagedata,
            $messageid) {
        global $CFG;

        // Get the current mailbox.
        $mailbox = $this->get_mailbox();

        // We need the structure at various points below.
        $structure = $basemessagedata->getStructure();

        // Now fetch the rest of the message content.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->fullText();

        // Fetch all of the message parts too.
        $typemap = $structure->contentTypeMap();
        foreach ($typemap as $part => $type) {
            // The body of the part - attempt to decode it on the server.
            $query->bodyPart($part, array(
                'decode' => true,
                'peek' => true,
            ));
            $query->bodyPartSize($part);
        }

        $messagedata = $this->client->fetch($mailbox, $query, array('ids' => $messageid))->first();

        // Store the data for this message.
        $contentplain = '';
        $contenthtml = '';
        $attachments = array(
            'inline' => array(),
            'attachment' => array(),
        );

        $plainpartid = $structure->findBody('plain');
        $htmlpartid = $structure->findBody('html');

        foreach ($typemap as $part => $type) {
            // Get the message data from the body part, and combine it with the structure to give a fully-formed output.
            $stream = $messagedata->getBodyPart($part, true);
            $partdata = $structure->getPart($part);
            $partdata->setContents($stream, array(
                'usestream' => true,
            ));

            if ($part === $plainpartid) {
                $contentplain = $this->process_message_part_body($messagedata, $partdata, $part);

            } else if ($part === $htmlpartid) {
                $contenthtml = $this->process_message_part_body($messagedata, $partdata, $part);

            } else if ($filename = $partdata->getName($part)) {
                if ($attachment = $this->process_message_part_attachment($messagedata, $partdata, $part, $filename)) {
                    // The disposition should be one of 'attachment', 'inline'.
                    // If an empty string is provided, default to 'attachment'.
                    $disposition = $partdata->getDisposition();
                    $disposition = $disposition == 'inline' ? 'inline' : 'attachment';
                    $attachments[$disposition][] = $attachment;
                }
            }

            // We don't handle any of the other MIME content at this stage.
        }

        // The message ID should always be in the first part.
        $this->currentmessagedata->plain = $contentplain;
        $this->currentmessagedata->html = $contenthtml;
        $this->currentmessagedata->attachments = $attachments;

        return $this->currentmessagedata;
    }

    /**
     * Process the messagedata and part data to extract the content of this part.
     *
     * @param $messagedata The structure and part of the message body
     * @param $partdata The part data
     * @param $part The part ID
     * @return string
     */
    private function process_message_part_body($messagedata, $partdata, $part) {
        // This is a content section for the main body.

        // Get the string version of it.
        $content = $messagedata->getBodyPart($part);
        if (!$messagedata->getBodyPartDecode($part)) {
            // Decode the content.
            $partdata->setContents($content);
            $content = $partdata->getContents();
        }

        // Convert the text from the current encoding to UTF8.
        $content = \core_text::convert($content, $partdata->getCharset());

        // Fix any invalid UTF8 characters.
        // Note: XSS cleaning is not the responsibility of this code. It occurs immediately before display when
        // format_text is called.
        $content = clean_param($content, PARAM_RAW);

        return $content;
    }

    /**
     * Process a message again to add body and attachment data.
     *
     * @param $messagedata The structure and part of the message body
     * @param $partdata The part data
     * @param $filename The filename of the attachment
     * @return \stdClass
     */
    private function process_message_part_attachment($messagedata, $partdata, $part, $filename) {
        global $CFG;

        // For Antivirus, the repository/lib.php must be included as it is not autoloaded.
        require_once($CFG->dirroot . '/repository/lib.php');

        // If a filename is present, assume that this part is an attachment.
        $attachment = new \stdClass();
        $attachment->filename       = $filename;
        $attachment->type           = $partdata->getType();
        $attachment->content        = $partdata->getContents();
        $attachment->charset        = $partdata->getCharset();
        $attachment->description    = $partdata->getDescription();
        $attachment->contentid      = $partdata->getContentId();
        $attachment->filesize       = $messagedata->getBodyPartSize($part);

        if (empty($CFG->runclamonupload) or empty($CFG->pathtoclam)) {
            mtrace("--> Attempting virus scan of '{$attachment->filename}'");

            // Store the file on disk - it will need to be virus scanned first.
            $itemid = rand(1, 999999999);;
            $directory = make_temp_directory("/messageinbound/{$itemid}", false);
            $filepath = $directory . "/" . $attachment->filename;
            if (!$fp = fopen($filepath, "w")) {
                // Unable to open the temporary file to write this to disk.
                mtrace("--> Unable to save the file to disk for virus scanning. Check file permissions.");

                throw new \core\message\inbound\processing_failed_exception('attachmentfilepermissionsfailed',
                        'tool_messageinbound');
            }

            fwrite($fp, $attachment->content);
            fclose($fp);

            // Perform a virus scan now.
            try {
                \repository::antivir_scan_file($filepath, $attachment->filename, true);
            } catch (\moodle_exception $e) {
                mtrace("--> A virus was found in the attachment '{$attachment->filename}'.");
                $this->inform_attachment_virus();
                return;
            }
        }

        return $attachment;
    }

    /**
     * Check whether the key provided is valid.
     *
     * @param $status The Message to process
     * @param $messageid The Hore message Uid
     * @return bool
     */
    private function passes_key_validation($status, $messageid) {
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
     * @param $messageid
     * @param string $flag The flag to add
     */
    private function add_flag_to_message($messageid, $flag) {
        // Get the current mailbox.
        $mailbox = $this->get_mailbox();

        // Mark it as read to lock the message.
        $this->client->store($mailbox, array(
            'ids' => new \Horde_Imap_Client_Ids($messageid),
            'add' => $flag,
        ));
    }

    /**
     * Remove the specified flag from the message.
     *
     * @param $messageid
     * @param string $flag The flag to remove
     */
    private function remove_flag_from_message($messageid, $flag) {
        // Get the current mailbox.
        $mailbox = $this->get_mailbox();

        // Mark it as read to lock the message.
        $this->client->store($mailbox, array(
            'ids' => $messageid,
            'delete' => $flag,
        ));
    }

    /**
     * Check whether the message has the specified flag
     *
     * @param $messageid
     * @param string $flag The flag to check
     * @return bool
     */
    private function message_has_flag($messageid, $flag) {
        // Get the current mailbox.
        $mailbox = $this->get_mailbox();

        // Grab messagedata including flags.
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->flags();
        $query->structure();
        $messagedata = $this->client->fetch($mailbox, $query, array(
            'ids' => $messageid,
        ))->first();
        $flags = $messagedata->getFlags();

        return in_array($flag, $flags);
    }

    /**
     * Attempt to determine whether this message is a bulk message (e.g. automated reply).
     *
     * @param \Horde_Imap_Client_Data_Fetch $message The message to process
     * @param string|\Horde_Imap_Client_Ids $messageid The Hore message Uid
     * @return boolean
     */
    private function is_bulk_message(
            \Horde_Imap_Client_Data_Fetch $message,
            $messageid) {
        $query = new \Horde_Imap_Client_Fetch_Query();
        $query->headerText(array('peek' => true));

        $messagedata = $this->client->fetch($this->get_mailbox(), $query, array('ids' => $messageid))->first();

        // Assume that this message is not bulk to begin with.
        $isbulk = false;

        // An auto-reply may itself include the Bulk Precedence.
        $precedence = $messagedata->getHeaderText(0, \Horde_Imap_Client_Data_Fetch::HEADER_PARSE)->getValue('Precedence');
        $isbulk = $isbulk || strtolower($precedence) == 'bulk';

        // If the X-Autoreply header is set, and not 'no', then this is an automatic reply.
        $autoreply = $messagedata->getHeaderText(0, \Horde_Imap_Client_Data_Fetch::HEADER_PARSE)->getValue('X-Autoreply');
        $isbulk = $isbulk || ($autoreply && $autoreply != 'no');

        // If the X-Autorespond header is set, and not 'no', then this is an automatic response.
        $autorespond = $messagedata->getHeaderText(0, \Horde_Imap_Client_Data_Fetch::HEADER_PARSE)->getValue('X-Autorespond');
        $isbulk = $isbulk || ($autorespond && $autorespond != 'no');

        // If the Auto-Submitted header is set, and not 'no', then this is a non-human response.
        $autosubmitted = $messagedata->getHeaderText(0, \Horde_Imap_Client_Data_Fetch::HEADER_PARSE)->getValue('Auto-Submitted');
        $isbulk = $isbulk || ($autosubmitted && $autosubmitted != 'no');

        return $isbulk;
    }

    /**
     * Send the message to the appropriate handler.
     *
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
     * @param $recipient The message recipient
     */
    private function handle_verification_failure(
            \Horde_Imap_Client_Ids $messageids,
            $recipient) {
        global $DB, $USER;

        if (!$messageid = $this->currentmessagedata->messageid) {
            mtrace("---> Warning: Unable to determine the Message-ID of the message.");
            return false;
        }

        // Move the message into a new mailbox.
        $this->client->copy(self::MAILBOX, self::CONFIRMATIONFOLDER, array(
                'create'    => true,
                'ids'       => $messageids,
                'move'      => true,
            ));

        // Store the data from the failed message in the associated table.
        $record = new \stdClass();
        $record->messageid = $messageid;
        $record->userid = $USER->id;
        $record->address = $recipient;
        $record->timecreated = time();
        $record->id = $DB->insert_record('messageinbound_messagelist', $record);

        // Setup the Inbound Message generator for the invalid recipient handler.
        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data($record->id);

        $eventdata = new \stdClass();
        $eventdata->component           = 'tool_messageinbound';
        $eventdata->name                = 'invalidrecipienthandler';

        $userfrom = clone $USER;
        $userfrom->customheaders = array();
        // Adding the In-Reply-To header ensures that it is seen as a reply.
        $userfrom->customheaders[] = 'In-Reply-To: ' . $messageid;

        // The message will be sent from the intended user.
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

        $eventdata = new \stdClass();
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
     * @param stdClass $messagedata The data for the current message being processed.
     * @param mixed $handlerresult The result returned by the handler.
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
        $messagepreferencesurl = new \moodle_url("/message/edit.php", array('id' => $USER->id));
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

        $eventdata = new \stdClass();
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
     * @param $subject string The subject string
     * @return string The formatted reply subject
     */
    private function get_reply_subject($subject) {
        $prefix = get_string('replysubjectprefix', 'tool_messageinbound');
        if (!(substr($subject, 0, strlen($prefix)) == $prefix)) {
            $subject = $prefix . ' ' . $subject;
        }

        return $subject;
    }
}
