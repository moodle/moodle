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
        ClientEntityInterface $cliententity
    ): ?UserEntityInterface {
        $user = authenticate_user_login($username, $password);
        if (!$user) {
            return null;
        }

        $userentity = new user_entity();
        $userentity->setIdentifier((string) $user->id);

        return $userentity;
    }
}
