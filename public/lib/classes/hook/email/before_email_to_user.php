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

namespace core\hook\email;

use core\email;

/**
 * Hook to allow subscribers to modify or block sending email.
 *
 * @package    core
 * @copyright  2025 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 5.3
 */
#[\core\attribute\tags('email')]
#[\core\attribute\label('Allows plugins to modify contents or block sending an email')]
final class before_email_to_user {
    /**
     * Hook to allow subscribers to modify or block sending email.
     *
     * @param email $email The email message that is attempting to be sent.
     */
    public function __construct(
        /** @var email $email The email message that is attempting to be sent. */
        public email $email,
    ) {
    }
}
