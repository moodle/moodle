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

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * Class granted_scopes_repository.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class granted_scopes_repository {
    /**
     * Constructor for granted_scopes_repository.
     *
     * @param ScopeRepositoryInterface $scoperepository The scope repository object.
     */
    public function __construct(
        private ScopeRepositoryInterface $scoperepository,
    ) {}

    /**
     * Get the granted scopes for the specified client/user combination.
     *
     * @param ClientEntityInterface $client The client.
     * @param UserEntityInterface $user The user.
     * @return ScopeEntityInterface[] The granted scopes array, empty if none.
     */
    public function get_granted_scopes_for_user(
        ClientEntityInterface $client,
        UserEntityInterface $user,
    ): array {
        global $DB;

        $scope = $DB->get_field(
            'oauth2_server_client_granted_scopes',
            'scope',
            [
                'clientidentifier' => $client->getIdentifier(),
                'userid' => $user->getIdentifier(),
            ],
        );

        if ($scope === false || $scope === '') {
            return [];
        }

        $scopes = array_filter(explode(' ', $scope), static fn($s) => !empty($s));

        return array_map(
            fn(string $scope): ScopeEntityInterface => $this->scoperepository->getScopeEntityByIdentifier($scope),
            $scopes,
        );
    }

    /**
     * Whether the user has granted all of the requested scopes for the specified client.
     *
     * @param ClientEntityInterface $client The client.
     * @param UserEntityInterface $user The user.
     * @param ScopeEntityInterface[] $requestedscopes The requested scopes.
     * @return bool True if the user has granted all of the requested scopes, false otherwise.
     */
    public function has_granted_all_scopes(
        ClientEntityInterface $client,
        UserEntityInterface $user,
        array $requestedscopes,
    ): bool {
        $grantedscopeentities = $this->get_granted_scopes_for_user($client, $user);
        $grantedscopeidentifiers = array_map(
            static fn($scopeentity): string => $scopeentity->getIdentifier(),
            $grantedscopeentities,
        );

        foreach ($requestedscopes as $requestedscope) {
            if (!in_array($requestedscope->getIdentifier(), $grantedscopeidentifiers, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update all granted scopes for the user.
     *
     * @param ClientEntityInterface $client The client.
     * @param UserEntityInterface $user The user.
     * @param string[] $scopes The scopes to grant.
     * @return void
     */
    public function store_granted_scopes_for_user(
        ClientEntityInterface $client,
        UserEntityInterface $user,
        array $scopes,
    ): void {
        global $DB;

        $clientidentifier = $client->getIdentifier();
        $userid = $user->getIdentifier();

        $scopes = array_filter($scopes);
        sort($scopes);

        $scopestring = implode(' ', $scopes);

        $recordid = $DB->get_field(
            'oauth2_server_client_granted_scopes',
            'id',
            [
                'clientidentifier' => $clientidentifier,
                'userid' => $userid,
            ],
        );

        // If the record already exists, update it, otherwise create a new one.
        if ($recordid) {
            $DB->update_record(
                'oauth2_server_client_granted_scopes',
                (object) [
                    'id' => $recordid,
                    'scope' => $scopestring,
                ],
            );
        } else {
            $DB->insert_record(
                'oauth2_server_client_granted_scopes',
                (object) [
                    'clientidentifier' => $clientidentifier,
                    'userid' => $userid,
                    'scope' => $scopestring,
                    'timecreated' => time(),
                ],
            );
        }
    }
}
