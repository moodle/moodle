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

namespace core\test;

use stdClass;

/**
 * Mailpit message handling implementation.
 *
 * @package    core
 * @category   test
 * @copyright  Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mailpit_message implements message {

    /** @var bool Whether the message has been loaded. */
    protected bool $messageloaded = false;

    /**
     * Constructor.
     *
     * @param email_catcher $client The email catcher client.
     * @param string $id The message ID.
     * @param stdClass $sender The sender.
     * @param string $subject The subject.
     * @param array $recipients The recipients.
     * @param array $cc The cc recipients.
     * @param array $bcc The bcc recipients.
     * @param int $attachmentcount The number of attachments.
     * @param string|null $text The text body.
     * @param string|null $html The HTML body.
     * @param array $attachments The attachments.
     * @param array $inline The inline attachments.
     */
    protected function __construct(
        /** @var email_catcher $client The email catcher client used for message operations.*/
        private readonly email_catcher $client,
        /** @var string $id The unique identifier for the message. */
        private readonly string $id,
        /** @var stdClass $sender The sender of the message, represented as an object with email details. */
        private readonly stdClass $sender,
        /** @var string $subject The subject line of the message. */
        private readonly string $subject,
        /** @var array $recipients List of primary recipients for the message. */
        private readonly array $recipients,
        /** @var array $cc List of carbon copy recipients (optional, defaults to an empty array). */
        private readonly array $cc = [],
        /** @var array $bcc List of blind carbon copy recipients (optional, defaults to an empty array). */
        private readonly array $bcc = [],
        /** @var int $attachmentcount The number of attachments in the message (default is 0). */
        private readonly int $attachmentcount = 0,
        /** @var ?string $text The plain text body of the message (nullable, might be loaded later). */
        private ?string $text = null,
        /** @var ?string $html The HTML body of the message (nullable, might be loaded later). */
        private ?string $html = null,
        /** @var array $attachments An array of attachment details (defaults to empty array). */
        private array $attachments = [],
        /** @var array $inline An array of inline elements like images or styles (defaults to empty array). */
        private array $inline = [],
    ) {
    }

    /**
     * Load the message content.
     */
    protected function load_message_content(): void {
        if (!$this->messageloaded) {
            $message = $this->client->get_message_data($this->id);
            $this->text = $message->Text;
            $this->html = $message->HTML;
            $this->attachments = $message->Attachments;
            $this->inline = $message->Inline;
            $this->messageloaded = true;
        }
    }

    /**
     * Create a message from an api response.
     *
     * @param email_catcher $client The email catcher client.
     * @param stdClass $message The api response.
     * @param bool $showdetails Optional. Whether to include detailed information in the messages. Default is false.
     * @return mailpit_message The created mailpit message instance.
     */
    public static function create_from_api_response(
        email_catcher $client,
        stdClass $message,
        bool $showdetails = false,
    ): self {
        $message = new self(
            client: $client,
            id: $message->ID,
            sender: $message->From,
            subject: $message->Subject,
            recipients: $message->To,
            cc: $message->Cc,
            attachmentcount: $message->Attachments,
        );

        if ($showdetails) {
            $message->load_message_content();
        }

        return $message;
    }

    /**
     * Get the text representation of the body, if one was provided.
     *
     * @return null|string The text body.
     */
    public function get_body_text(): ?string {
        return $this->text;
    }

    /**
     * Get the HTML representation of the body, if one was provided.
     *
     * @return null|string The HTML body.
     */
    public function get_body_html(): ?string {
        return $this->html;
    }

    /**
     * Get the message ID.
     *
     * @return string The ID.
     */
    public function get_id(): string {
        return $this->id;
    }

    /**
     * Get the message recipients.
     *
     * @return iterable The recipients.
     */
    public function get_recipients(): iterable {
        foreach ($this->recipients as $user) {
            yield mailpit_message_user::from_recipient($user);
        }
    }

    /**
     * Get the first recipient of the message.
     *
     * @return string The email address of the first recipient.
     */
    public function get_first_recipient(): string {
        $recipients = $this->get_recipients();
        foreach ($recipients as $recipient) {
            return $recipient->get_address();
        }
        return '';
    }

    /**
     * Whether the message has the specified recipient.
     *
     * @param string $email The email address.
     * @return bool Whether the message has the recipient.
     */
    public function has_recipient(string $email): bool {
        foreach ($this->get_recipients() as $recipient) {
            if ($recipient->get_address() === $email) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the message cc recipients.
     *
     * @return iterable The cc recipients.
     */
    public function get_cc(): iterable {
        foreach ($this->cc as $user) {
            yield mailpit_message_user::from_recipient($user);
        }
    }

    /**
     * Get the message bcc recipients.
     *
     * @return iterable The bcc recipients.
     */
    public function get_bcc(): iterable {
        foreach ($this->bcc as $user) {
            yield mailpit_message_user::from_recipient($user);
        }
    }

    /**
     * Get the message subject.
     *
     * @return string The subject.
     */
    public function get_subject(): string {
        return $this->subject;
    }

    /**
     * Get the message sender.
     *
     * @return message_user The sender.
     */
    public function get_sender(): message_user {
        return mailpit_message_user::from_sender($this->sender);
    }
}
