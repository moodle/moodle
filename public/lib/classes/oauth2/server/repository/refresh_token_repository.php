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

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use core\oauth2\server\entity\refresh_token_entity;

/**
 * OAuth2 server refresh token repository.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class refresh_token_repository implements RefreshTokenRepositoryInterface {
    #[\Override]
    public function getNewRefreshToken(): ?RefreshTokenEntityInterface {
        return new refresh_token_entity();
    }

    #[\Override]
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshtokenentity): void {
        global $DB;

        $record = new \stdClass();
        $record->identifier = $refreshtokenentity->getIdentifier();
        $record->accesstokenidentifier = $refreshtokenentity->getAccessToken()->getIdentifier();
        $record->expirytime = $refreshtokenentity->getExpiryDateTime()->getTimestamp();
        $record->revoked = refresh_token_entity::REVOKED_NO;
        $record->timecreated = time();

        $DB->insert_record('oauth2_server_client_refresh_tokens', $record);
    }

    #[\Override]
    public function revokeRefreshToken(string $tokenid): void {
        global $DB;

        $DB->set_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            refresh_token_entity::REVOKED_YES,
            ['identifier' => $tokenid],
        );
    }

    #[\Override]
    public function isRefreshTokenRevoked(string $tokenid): bool {
        global $DB;

        $revoked = $DB->get_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            ['identifier' => $tokenid],
            MUST_EXIST
        );

        return (int) $revoked === refresh_token_entity::REVOKED_YES;
    }

    /**
     * Get the access token a refresh token was issued alongside, if it belongs to a given client.
     *
     * Refresh tokens carry no client identifier of their own, so ownership is established through
     * that access token.
     *
     * @param string $tokenid
     * @param string $clientidentifier
     * @return string|null Null if the refresh token is unknown or belongs to another client.
     */
    public function get_owning_access_token_identifier(string $tokenid, string $clientidentifier): ?string {
        global $DB;

        $sql = "SELECT rtok.accesstokenidentifier
                  FROM {oauth2_server_client_refresh_tokens} rtok
                  JOIN {oauth2_server_client_access_tokens} atok ON atok.identifier = rtok.accesstokenidentifier
                 WHERE rtok.identifier = :identifier
                   AND atok.clientidentifier = :clientidentifier";

        $identifier = $DB->get_field_sql($sql, [
            'identifier' => $tokenid,
            'clientidentifier' => $clientidentifier,
        ]);

        return $identifier === false ? null : $identifier;
    }
}
