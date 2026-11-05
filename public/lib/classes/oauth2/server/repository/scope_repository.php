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

use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\client_entity_interface;
use core\router\scope\scope_entity_interface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * The OAuth2 scope repository.
 *
 * This repository is responsible for retrieving scope entities and finalizing scopes for access tokens.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scope_repository implements ScopeRepositoryInterface {
    /**
     * Create an instance of the scope repository.
     *
     * @param \core\router\scope\util $scopeutil The scope utility instance.
     */
    public function __construct(
        /** @var \core\router\scope\util The scope utility instance. */
        private \core\router\scope\util $scopeutil,
    ) {
    }

    #[\Override]
    public function getScopeEntityByIdentifier(string $identifier): ?scope_entity_interface {
        $scopemap = $this->scopeutil->get_scope_map();

        if (array_key_exists($identifier, $scopemap)) {
            $classname = $scopemap[$identifier];
            return new $classname();
        }

        return null;
    }

    #[\Override]
    public function finalizeScopes(
        array $scopes,
        string $granttype,
        ClientEntityInterface $cliententity,
        ?string $useridentifier = null,
        ?string $authcodeid = null
    ): array {
        global $DB;

        // The League interface only requires a ClientEntityInterface here, but every client entity
        // produced by this OAuth2 server also implements client_entity_interface, which is needed
        // below to check whether each scope is approved for this specific client.
        if (!$cliententity instanceof client_entity_interface) {
            throw new \coding_exception(
                'The client entity passed to finalizeScopes() must implement client_entity_interface.',
            );
        }

        if ($useridentifier === null) {
            // No user identifier means this is not a user-specific grant (for example, the
            // client_credentials grant). There are no user-granted scopes to check against, but
            // the scopes must still be restricted to those approved for this specific client.

            foreach ($scopes as $key => $scope) {
                if (!$cliententity->is_scope_approved($scope)) {
                    unset($scopes[$key]);
                    continue;
                }
            }

            return $scopes;
        }

        // Validate against the specific session if exchanging an authorization code.
        if ($granttype === client_entity::GRANT_TYPE_AUTHORIZATION_CODE && $authcodeid !== null) {
            $usergrantedscopes = $DB->get_field(
                'oauth2_server_client_auth_codes',
                'scopes',
                ['identifier' => $authcodeid],
            );
        } else { // Otherwise, fall back to the persistent global user grant table for refresh tokens.
            $usergrantedscopes = $DB->get_field(
                'oauth2_server_client_granted_scopes',
                'scope',
                [
                    'clientidentifier' => $cliententity->getIdentifier(),
                    'userid' => $useridentifier,
                ],
            );
        }

        // No approved scopes.
        if ($usergrantedscopes === false || empty(trim($usergrantedscopes))) {
            return [];
        }

        // Restrict the list of scopes to only those approved for this OAauth2 Client.
        $usergrantedscopesarray = explode(' ', $usergrantedscopes);

        // Remove any scopes that have not been approved.
        foreach ($scopes as $key => $scope) {
            if (!in_array($scope->getIdentifier(), $usergrantedscopesarray, true)) {
                unset($scopes[$key]);
                continue;
            }

            if (!$cliententity->is_scope_approved($scope)) {
                unset($scopes[$key]);
                continue;
            }
        }

        return $scopes;
    }

    /**
     * Get all available scope entities.
     *
     * @return ScopeEntityInterface[] The array of scope entities keyed by identifier.
     */
    public function get_all_scopes(): array {
        return $this->scopeutil->get_all_scopes();
    }

    /**
     * Get the map of scope identifiers to scope classes.
     *
     * @return array The map of scope identifiers to scope classes.
     */
    public function get_scope_map(): array {
        return $this->scopeutil->get_scope_map();
    }
}
