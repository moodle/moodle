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

namespace core\oauth2\server\repository;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use core\oauth2\server\entity\user_entity;

/**
 * OAuth2 server user repository.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_repository implements UserRepositoryInterface {
    #[\Override]
    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $granttype,
        ClientEntityInterface $cliententity,
        string|bool $logintoken = false,
    ): ?UserEntityInterface {
        $user = $this->authenticate_user($username, $password, $logintoken);
        if (!$user) {
            return null;
        }

        $userentity = new user_entity();
        $userentity->setIdentifier((string) $user->id);

        return $userentity;
    }

    /**
     * Authenticate a user against Moodle's authentication mechanism.
     *
     * Unlike {@see self::getUserEntityByUserCredentials()}, this returns the full Moodle user
     * record (rather than a League user entity), so that callers which need to establish a real
     * Moodle session (via complete_user_login()) do not need to re-authenticate or reload the
     * user from the database.
     *
     * @param string $username
     * @param string $password
     * @param string|bool $logintoken If this is set to a string it is validated against the login token for the session.
     * @return \stdClass|false A {@see $USER} object or false if authentication failed.
     */
    public function authenticate_user(
        string $username,
        string $password,
        string|bool $logintoken = false,
    ): \stdClass|false {
        return authenticate_user_login(
            username: $username,
            password: $password,
            logintoken: $logintoken,
        );
    }

    /**
     * Get the current logged-in user as a user entity.
     *
     * @return user_entity
     */
    public function get_current_user(): user_entity {
        global $USER;

        $userentity = new user_entity();
        $userentity->setIdentifier($USER->id);

        return $userentity;
    }
}
