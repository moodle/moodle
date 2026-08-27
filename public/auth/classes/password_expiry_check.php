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

namespace core_auth;

/**
 * The outcome of checking a user's password against their authentication plugin's hard-expiry
 * state (see {@see password_expiry_checker}).
 *
 * @package    core_auth
 * @copyright  Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class password_expiry_check {
    /**
     * Private constructor - use one of the named constructors below instead.
     */
    private function __construct(
        /** @var password_expiry_state */
        public readonly password_expiry_state $state,
        /**
         * @var \core\url|null The plugin's own external change-password destination. Only set
         *      when $state is {@see password_expiry_state::HARD_EXPIRED_EXTERNAL}.
         */
        public readonly ?\core\url $externalchangepasswordurl = null,
    ) {
    }

    /**
     * The password is not hard-expired (expiry is disabled, not yet due, or only within the
     * non-blocking warning period).
     *
     * @return self
     */
    public static function ok(): self {
        return new self(password_expiry_state::OK);
    }

    /**
     * The password is hard-expired, and must be changed via Moodle's own internal
     * change-password page.
     *
     * @return self
     */
    public static function expired_internal(): self {
        return new self(password_expiry_state::HARD_EXPIRED_INTERNAL);
    }

    /**
     * The password is hard-expired, and must be changed via the plugin's own external
     * destination.
     *
     * @param \core\url $url
     * @return self
     */
    public static function expired_external(\core\url $url): self {
        return new self(password_expiry_state::HARD_EXPIRED_EXTERNAL, $url);
    }

    /**
     * The password is hard-expired, and the plugin cannot change the password at all.
     *
     * @return self
     */
    public static function expired_unsupported(): self {
        return new self(password_expiry_state::HARD_EXPIRED_UNSUPPORTED);
    }

    /**
     * Whether this represents a blocking, hard expiry of any kind.
     *
     * @return bool
     */
    public function is_hard_expired(): bool {
        return $this->state !== password_expiry_state::OK;
    }
}
