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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\access_token_entity;

/**
 * Tests for {@see access_token_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(access_token_repository::class)]
final class access_token_repository_test extends \advanced_testcase {
    /**
     * Test getting a new token under different scenarios.
     *
     * @param string $clientidentifier Client identifier.
     * @param array $scopeidentifiers Array of scope identifier strings.
     * @param string|null $userid User identifier or null.
     * @return void
     */
    #[DataProvider('get_new_token_provider')]
    public function test_get_new_token(string $clientidentifier, array $scopeidentifiers, ?string $userid): void {
        $repository = new access_token_repository();
        $client = new client_entity();
        $client->setIdentifier($clientidentifier);

        $scopes = [];
        foreach ($scopeidentifiers as $scopeidentifier) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($scopeidentifier);
            $scopes[] = $scope;
        }

        $token = $repository->getNewToken($client, $scopes, $userid);

        $this->assertInstanceOf(access_token_entity::class, $token);
        $this->assertSame($client, $token->getClient());
        $this->assertSame($userid, $token->getUserIdentifier());
        $this->assertCount(count($scopeidentifiers), $token->getScopes());

        for ($i = 0; $i < count($scopes); $i++) {
            $this->assertSame($scopes[$i], $token->getScopes()[$i]);
        }
    }

    /**
     * Data provider for getNewToken tests.
     *
     * @return array
     */
    public static function get_new_token_provider(): array {
        return [
            'client-1, single scope, user-1' => [
                'client-1',
                ['profile'],
                'user-1',
            ],
            'client-2, multiple scopes, no user' => [
                'client-2',
                ['profile', 'email'],
                null,
            ],
            'client-3, no scopes, user-2' => [
                'client-3',
                [],
                'user-2',
            ],
        ];
    }

    /**
     * Test persisting a new access token under various scenarios.
     *
     * @param string $tokenid Access token identifier string.
     * @param string $clientid Client identifier string.
     * @param string|null $userid User identifier string.
     * @param array $scopeidentifiers Array of scope identifiers.
     * @return void
     */
    #[DataProvider('persist_access_token_provider')]
    public function test_persist_new_access_token(
        string $tokenid,
        string $clientid,
        ?string $userid,
        array $scopeidentifiers
    ): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new access_token_repository();

        $client = new client_entity();
        $client->setIdentifier($clientid);

        $scopes = [];
        foreach ($scopeidentifiers as $scopeidentifier) {
            $scope = $this->createMock(ScopeEntityInterface::class);
            $scope->method('getIdentifier')->willReturn($scopeidentifier);
            $scopes[] = $scope;
        }

        $token = new access_token_entity();
        $token->setIdentifier($tokenid);
        $token->setClient($client);

        if ($userid !== null) {
            $token->setUserIdentifier($userid);
        }

        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }

        $token->setExpiryDateTime(new \DateTimeImmutable('+1 hour'));

        $repository->persistNewAccessToken($token);
        $record = $DB->get_record('oauth2_server_client_access_tokens', ['identifier' => $tokenid]);

        $this->assertNotEmpty($record);
        $this->assertSame($tokenid, $record->identifier);
        $this->assertEquals($userid, $record->userid);
        $this->assertSame($clientid, $record->clientidentifier);
        $this->assertSame(implode(' ', $scopeidentifiers), $record->scopes);
        $this->assertEquals(access_token_entity::REVOKED_NO, (int) $record->revoked);
    }

    /**
     * Data provider for persistNewAccessToken tests.
     *
     * @return array
     */
    public static function persist_access_token_provider(): array {
        return [
            'token-1 with user and single scope' => [
                'token-1',
                'client-a',
                '101',
                ['profile'],
            ],
            'token-2 with null user and multiple scopes' => [
                'token-2',
                'client-b',
                null,
                ['profile', 'email'],
            ],
        ];
    }

    /**
     * Test checking access token revocation and revoking under different scenarios.
     *
     * @return void
     */
    public function test_access_token_revocation(): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new access_token_repository();

        $DB->insert_record('oauth2_server_client_access_tokens', [
            'identifier' => 'token-1',
            'userid' => 123,
            'clientidentifier' => 'client-a',
            'scopes' => 'profile',
            'expirytime' => time() + 3600,
            'revoked' => 0,
            'timecreated' => time(),
        ]);

        $this->assertFalse($repository->isAccessTokenRevoked('token-1'));

        $repository->revokeAccessToken('token-1');

        $this->assertTrue($repository->isAccessTokenRevoked('token-1'));
    }

    /**
     * Test checking non-existent access token throws exception.
     *
     * @return void
     */
    public function test_is_access_token_revoked_non_existent(): void {
        $repository = new access_token_repository();

        $this->expectException(\dml_missing_record_exception::class);
        $repository->isAccessTokenRevoked('non-existent-token');
    }
}
