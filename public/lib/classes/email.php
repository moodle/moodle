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

namespace core;

use core\exception\coding_exception;
use stdClass;

/**
 * Email container class.
 *
 * @package    core
 * @copyright  2025 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 5.3
 */
class email {
    /** @var array $blockreasons Reasons for this email being blocked */
    private array $blockreasons = [];

    /** @var array $additionalheaders Additional email headers */
    private array $additionalheaders = [];

    /**
     * Create email instance.
     *
     * @param stdClass $user A $USER object
     * @param stdClass $from A $USER object
     * @param string $subject plain text subject line of the email
     * @param string $messagetext plain text version of the message
     * @param string $messagehtml complete html version of the message (optional)
     * @param string $attachment a file on the filesystem, either relative to $CFG->dataroot or a full path to a file in one of
     *          the following directories: $CFG->cachedir, $CFG->dataroot, $CFG->dirroot, $CFG->localcachedir, $CFG->tempdir
     * @param string $attachname the name of the file (extension indicates MIME)
     * @param bool $usetrueaddress determines whether $from email address should
     *          be sent out. Will be overruled by user profile setting for maildisplay
     * @param string $replyto Email address to reply to
     * @param string $replytoname Name of reply to recipient
     * @param int $wordwrapwidth custom word wrap width
     */
    public function __construct(
        /** @var stdClass $user A $USER object (to) */
        public stdClass $user,
        /** @var stdClass $from A $USER object (from) */
        public stdClass $from,
        /** @var string $subject plain text subject line of the email */
        public string $subject,
        /** @var string $messagetext plain text version of the message */
        public string $messagetext,
        /** @var string $messagehtml complete html version of the message (optional) */
        public string $messagehtml,
        /** @var string $attachment a file on the filesystem, either relative to $CFG->dataroot or a full path to a file in one of
         * the following directories: $CFG->cachedir, $CFG->dataroot, $CFG->dirroot, $CFG->localcachedir, $CFG->tempdir */
        public string $attachment,
        /** @var string $attachname the name of the file (extension indicates MIME) */
        public string $attachname,
        /** @var bool $usetrueaddress determines whether $from email address should
         * be sent out. Will be overruled by user profile setting for maildisplay */
        public bool $usetrueaddress,
        /** @var string $replyto Email address to reply to */
        public string $replyto,
        /** @var string $replytoname Name of reply to recipient */
        public string $replytoname,
        /** @var int $wordwrapwidth custom word wrap width */
        public int $wordwrapwidth,
    ) {
        // This is a quirk from email_to_user where the headers are stored in the "from" user.
        // We break them out of there here for the clarity of hook subscribers.
        $this->additionalheaders = self::extract_headers_from_from_user($from);

        // Remove them from $from to avoid confusion / to avoid others accidentally updating those.
        unset($from->customheaders);
    }

    /**
     * Extract custom headers from "from" user.
     *
     * These may be set as a string or an array.
     *
     * @param stdClass $from user object
     * @return array list of custom headers
     */
    private static function extract_headers_from_from_user(stdClass $from): array {
        if (!isset($from->customheaders)) {
            return [];
        }

        if (is_string($from->customheaders)) {
            return [$from->customheaders];
        }

        if (is_array($from->customheaders)) {
            return $from->customheaders;
        }

        throw new coding_exception("Unknown custom headers set");
    }

    /**
     * Add a reason for blocking this email.
     *
     * @param string $reason the reason to add
     */
    public function add_block_reason(string $reason): void {
        $this->blockreasons[] = $reason;
    }

    /**
     * Return the reasons why this email was blocked.
     *
     * @return array list of reason strings
     */
    public function get_block_reasons(): array {
        return $this->blockreasons;
    }

    /**
     * Does this email have any block reasons?
     *
     * @return bool true if reasons found
     */
    public function is_blocked(): bool {
        return !empty($this->blockreasons);
    }

    /**
     * Add additional header to be sent with the email.
     *
     * @param string $header header name
     */
    public function add_additional_header(string $header): void {
        $this->additionalheaders[] = $header;
    }

    /**
     * Return list of additional headers set.
     *
     * @return array list of headers
     */
    public function get_additional_headers(): array {
        return $this->additionalheaders;
    }
}
