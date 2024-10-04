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
 * Message user interface for Mailpit.
 *
 * @package    core
 * @category   test
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright Andrew Lyons <andrew@nicols.co.uk>
 */
class mailpit_message_user implements message_user {

    /**
     * Create a new message user object from a sender.
     *
     * @param \stdClass $user The user object.
     * @return self
     */
    public static function from_sender(
        \stdClass $user,
    ): self {
        return new self(
            name: $user->Name,
            address: $user->Address,
        );
    }

    /**
     * Create a new message user object from a recipient.
     *
     * @param \stdClass $user The user object.
     * @return self
     */
    public static function from_recipient(
        \stdClass $user,
    ): self {
        return new self(
            name: $user->Name,
            address: $user->Address,
        );
    }

    /**
     * Constructor.
     *
     * @param string $name The name of the user.
     * @param string $address The email address of the user.
     */
    protected function __construct(
        /** @var string The name of the user. */
        protected string $name,
        /** @var string The email address of the user. */
        protected string $address
    ) {
    }

    /**
     * Get the display name of the user.
     *
     * @return string
     */
    public function get_name(): string {
        return $this->name;
    }

    /**
     * Get the email address of the user.
     *
     * @return string
     */
    public function get_address(): string {
        return $this->address;
    }
}
