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

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use core\oauth2\server\entity\client_entity;

/**
 * OAuth2 server client repository.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client_repository implements ClientRepositoryInterface {
    #[\Override]
    public function getClientEntity(string $clientidentifier): ?ClientEntityInterface {
        global $DB;

        $clientrecord = $DB->get_record('oauth2_server_clients', ['clientidentifier' => $clientidentifier]);

        if (!$clientrecord) {
            return null;
        }

        // Fetch redirect URIs.
        $urirecords = $DB->get_records(
            'oauth2_server_client_redirect_uris',
            ['clientidentifier' => $clientidentifier]
        );

        return client_entity::create_from_record($clientrecord, $urirecords);
    }

    #[\Override]
    public function validateClient(string $clientidentifier, ?string $clientsecret, ?string $granttype): bool {
        global $DB;

        $cliententity = $this->getClientEntity($clientidentifier);

        // Check if client exists.
        if ($cliententity === null) {
            return false;
        }

        // Check if the grant type is supported.
        if ($granttype !== null && !$cliententity->supportsGrantType($granttype)) {
            return false;
        }

        // Handle Public (Non-Confidential) Clients.
        if (!$cliententity->isConfidential()) {
            // Public clients shouldn't provide a secret.
            if ($clientsecret !== null && $clientsecret !== '') {
                return false;
            }

            // Valid public client with no secret provided.
            return true;
        }

        // Handle Confidential Clients (Secrets are mandatory).
        if ($clientsecret === null || $clientsecret === '') {
            return false;
        }

        // Fetch all active, non-revoked, non-expired secrets for this client.
        $select = 'clientidentifier = :clientidentifier
           AND revoked = :revoked
           AND expirytime > :now';

        $params = [
            'clientidentifier' => $clientidentifier,
            'revoked' => client_entity::SECRET_REVOKED_NO,
            'now' => time(),
        ];

        $secrets = $DB->get_records_select('oauth2_server_client_secrets', $select, $params);

        foreach ($secrets as $secret) {
            if (password_verify($clientsecret, $secret->secret)) {
                return true;
            }
        }

        return false;
    }
}
