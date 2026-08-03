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

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use core\oauth2\server\entity\access_token_entity;

/**
 * OAuth2 server access token repository.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_token_repository implements AccessTokenRepositoryInterface {
    #[\Override]
    public function getNewToken(
        ClientEntityInterface $cliententity,
        array $scopes,
        string|null $useridentifier = null
    ): AccessTokenEntityInterface {
        $accesstokenentity = new access_token_entity();
        $accesstokenentity->setClient($cliententity);

        if ($useridentifier !== null) {
            $accesstokenentity->setUserIdentifier($useridentifier);
        }

        foreach ($scopes as $scope) {
            $accesstokenentity->addScope($scope);
        }

        return $accesstokenentity;
    }

    #[\Override]
    public function persistNewAccessToken(AccessTokenEntityInterface $accesstokenentity): void {
        global $DB;

        $scopes = array_map(function ($scope) {
            return $scope->getIdentifier();
        }, $accesstokenentity->getScopes());

        $record = new \stdClass();
        $record->identifier = $accesstokenentity->getIdentifier();

        if ($userid = $accesstokenentity->getUserIdentifier()) {
            $record->userid = $userid;
        }

        $record->clientidentifier = $accesstokenentity->getClient()->getIdentifier();
        $record->scopes = implode(' ', $scopes);
        $record->expirytime = $accesstokenentity->getExpiryDateTime()->getTimestamp();
        $record->revoked = access_token_entity::REVOKED_NO;
        $record->timecreated = time();

        $DB->insert_record('oauth2_server_client_access_tokens', $record);
    }

    #[\Override]
    public function revokeAccessToken(string $tokenid): void {
        global $DB;

        $DB->set_field(
            'oauth2_server_client_access_tokens',
            'revoked',
            access_token_entity::REVOKED_YES,
            ['identifier' => $tokenid],
        );
    }

    #[\Override]
    public function isAccessTokenRevoked(string $tokenid): bool {
        global $DB;

        $revoked = $DB->get_field(
            'oauth2_server_client_access_tokens',
            'revoked',
            ['identifier' => $tokenid],
            MUST_EXIST
        );

        return (int) $revoked === access_token_entity::REVOKED_YES;
    }
}
