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

/**
 * Generic message interface.
 *
 * @package    core
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Simey Lameze <simey@moodle.com>
 */
interface message {

    /**
     * Get the message subject.
     *
     * @return string
     */
    public function get_subject(): string;

    /**
     * Get the text representation of the body, if one was provided.
     *
     * @return null|string
     */
    public function get_body_text(): ?string;

    /**
     * Get the HTML representation of the body, if one was provided.
     *
     * @return null|string
     */
    public function get_body_html(): ?string;

    /**
     * Get the message sender.
     *
     * @return message_user
     */
    public function get_sender(): message_user;

    /**
     * Get the message recipients.
     *
     * @return iterable<message_user>
     */
    public function get_recipients(): iterable;

    /**
     * Whether the message has the specified recipient.
     *
     * @param string $email The email address.
     * @return bool
     */
    public function has_recipient(string $email): bool;

    /**
     * Get the message cc recipients.
     *
     * @return iterable<message_user>
     */
    public function get_cc(): iterable;

    /**
     * Get the message cc recipients.
     *
     * @return iterable<message_user>
     */
    public function get_bcc(): iterable;
}
