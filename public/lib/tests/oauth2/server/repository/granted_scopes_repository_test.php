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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

/**
 * Tests for {@see granted_scopes_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(granted_scopes_repository::class)]
final class granted_scopes_repository_test extends \advanced_testcase {
    /**
     * Test get_granted_scopes_for_user with varying database configurations.
     *
     * @param string|null $grantedscopes Granted scopes in DB, or null if no record.
     * @param array $expectedscopes Scopes expected to be returned.
     */
    #[DataProvider('get_granted_scopes_provider')]
    public function test_get_granted_scopes_for_user(?string $grantedscopes, array $expectedscopes): void {
        global $DB;

        $this->resetAfterTest();

        $clientidentifier = 'client_abc';
        $user = $this->getDataGenerator()->create_user();

        // Register a client.
        $DB->insert_record(
            'oauth2_server_clients',
            (object) [
                'clientidentifier' => $clientidentifier,
                'name' => 'Test Client',
                'ownercontext' => \context_system::instance()->id,
                'status' => client_entity::STATUS_ACTIVE,
                'isconfidential' => 1,
                'timecreated' => time(),
            ],
        );

        if ($grantedscopes !== null) {
            $DB->insert_record(
                'oauth2_server_client_granted_scopes',
                (object) [
                    'clientidentifier' => $clientidentifier,
                    'userid' => $user->id,
                    'scope' => $grantedscopes,
                    'timecreated' => time(),
                ],
            );
        }

        // Mock scope repository and scope entities.
        $scoperepository = $this->createMock(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')->willReturnCallback(function ($scopename) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($scopename);
            return $scope;
        });

        // Mock client entity.
        $client = $this->createMock(ClientEntityInterface::class);
        $client->method('getIdentifier')->willReturn($clientidentifier);

        // Mock user entity.
        $userentity = $this->createMock(UserEntityInterface::class);
        $userentity->method('getIdentifier')->willReturn((string) $user->id);

        $repository = new granted_scopes_repository($scoperepository);
        $granted = $repository->get_granted_scopes_for_user($client, $userentity);

        $actualscopes = array_values(array_map(function ($s) {
            return $s->getIdentifier();
        }, $granted));

        $this->assertEquals($expectedscopes, $actualscopes);
    }

    /**
     * Data provider for get_granted_scopes_for_user.
     *
     * @return array
     */
    public static function get_granted_scopes_provider(): array {
        return [
            'no granted scopes (no DB record)' => [
                null,
                [],
            ],
            'no granted scopes (empty scope string)' => [
                '',
                [],
            ],
            'single granted scope' => [
                'profile',
                ['profile'],
            ],
            'multiple granted scopes' => [
                'profile email address',
                ['profile', 'email', 'address'],
            ],
        ];
    }

    /**
     * Test has_granted_all_scopes with varying states.
     *
     * @param string|null $grantedscopes Granted scopes in DB, or null if no record.
     * @param array $requestedscopes Requested scopes.
     * @param bool $expected Whether all requested scopes are granted.
     */
    #[DataProvider('has_granted_all_scopes_provider')]
    public function test_has_granted_all_scopes(?string $grantedscopes, array $requestedscopes, bool $expected): void {
        global $DB;

        $this->resetAfterTest();

        $clientidentifier = 'client_abc';
        $user = $this->getDataGenerator()->create_user();

        // Register a client.
        $DB->insert_record(
            'oauth2_server_clients',
            (object) [
                'clientidentifier' => $clientidentifier,
                'name' => 'Test Client',
                'ownercontext' => \context_system::instance()->id,
                'status' => client_entity::STATUS_ACTIVE,
                'isconfidential' => 1,
                'timecreated' => time(),
            ]
        );

        if ($grantedscopes !== null) {
            $DB->insert_record(
                'oauth2_server_client_granted_scopes',
                (object) [
                    'clientidentifier' => $clientidentifier,
                    'userid' => $user->id,
                    'scope' => $grantedscopes,
                    'timecreated' => time(),
                ]
            );
        }

        // Mock scope repository and scope entities.
        $scoperepository = $this->createMock(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')->willReturnCallback(function ($id) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($id);
            return $scope;
        });

        // Mock client entity.
        $client = $this->createMock(ClientEntityInterface::class);
        $client->method('getIdentifier')->willReturn($clientidentifier);

        // Mock user entity.
        $userentity = $this->createMock(UserEntityInterface::class);
        $userentity->method('getIdentifier')->willReturn((string) $user->id);

        $requestedscopeentities = array_map(function ($scopename) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($scopename);
            return $scope;
        }, $requestedscopes);

        $repository = new granted_scopes_repository($scoperepository);
        $this->assertSame($expected, $repository->has_granted_all_scopes($client, $userentity, $requestedscopeentities));
    }

    /**
     * Data provider for has_granted_all_scopes.
     *
     * @return array
     */
    public static function has_granted_all_scopes_provider(): array {
        return [
            'no granted scopes (no DB record), empty request scopes' => [null, [], true],
            'no granted scopes (no DB record), non-empty request scopes' => [null, ['profile'], false],
            'all requested scopes are granted (partial match)' => ['profile email', ['profile'], true],
            'all requested scopes are granted (exact match)' => ['profile email', ['profile', 'email'], true],
            'requested scopes partially granted' => ['profile', ['profile', 'email'], false],
            'none of the requested scopes are granted' => ['email', ['profile'], false],
        ];
    }

    /**
     * Test store_granted_scopes_for_user to insert and update DB state.
     *
     * @param string|null $initialgrantedscopes The initially granted scopes, or null if no record exists initially.
     * @param array $newscopes Scopes to be stored.
     * @param string $expectedscopes The expected space-separated sorted string in the DB.
     */
    #[DataProvider('store_granted_scopes_provider')]
    public function test_store_granted_scopes_for_user(
        ?string $initialgrantedscopes,
        array $newscopes,
        string $expectedscopes,
    ): void {
        global $DB;
        $this->resetAfterTest();

        $clientidentifier = 'client_abc';
        $user = $this->getDataGenerator()->create_user();

        // Register a client.
        $DB->insert_record(
            'oauth2_server_clients',
            (object) [
                'clientidentifier' => $clientidentifier,
                'name' => 'Test Client',
                'ownercontext' => \context_system::instance()->id,
                'status' => client_entity::STATUS_ACTIVE,
                'isconfidential' => 1,
                'timecreated' => time(),
            ]
        );

        if ($initialgrantedscopes !== null) {
            $DB->insert_record(
                'oauth2_server_client_granted_scopes',
                (object) [
                    'clientidentifier' => $clientidentifier,
                    'userid' => $user->id,
                    'scope' => $initialgrantedscopes,
                    'timecreated' => time(),
                ]
            );
        }

        // Mock client entity.
        $client = $this->createMock(ClientEntityInterface::class);
        $client->method('getIdentifier')->willReturn($clientidentifier);

        // Mock user entity.
        $userentity = $this->createMock(UserEntityInterface::class);
        $userentity->method('getIdentifier')->willReturn((string)$user->id);

        // Mock scope repository.
        $scoperepository = $this->createMock(ScopeRepositoryInterface::class);

        $repository = new granted_scopes_repository($scoperepository);
        $repository->store_granted_scopes_for_user($client, $userentity, $newscopes);

        $record = $DB->get_record(
            'oauth2_server_client_granted_scopes',
            [
                'clientidentifier' => $clientidentifier,
                'userid' => $user->id,
            ],
        );

        $this->assertSame($expectedscopes, $record->scope);
    }

    /**
     * Data provider for store_granted_scopes_for_user.
     *
     * @return array
     */
    public static function store_granted_scopes_provider(): array {
        return [
            'insert new scopes' => [
                null,
                ['email', 'profile'],
                'email profile',
            ],
            'update existing scopes' => [
                'profile',
                ['email', 'profile', 'address'],
                'address email profile',
            ],
        ];
    }
}
