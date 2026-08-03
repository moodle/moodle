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
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\refresh_token_entity;

/**
 * Tests for {@see refresh_token_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(refresh_token_repository::class)]
final class refresh_token_repository_test extends \advanced_testcase {
    /**
     * Test getting a new refresh token.
     */
    public function test_get_new_refresh_token(): void {
        $repository = new refresh_token_repository();
        $token = $repository->getNewRefreshToken();

        $this->assertInstanceOf(refresh_token_entity::class, $token);
    }

    /**
     * Test persisting a new refresh token.
     */
    public function test_persist_new_refresh_token(): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new refresh_token_repository();

        $DB->insert_record('oauth2_server_clients', [
            'name' => 'Test client',
            'clientidentifier' => 'client-id',
            'ownercontext' => \context_system::instance()->id,
            'status' => client_entity::STATUS_ACTIVE,
            'isconfidential' => 1,
            'timecreated' => time(),
        ]);

        $DB->insert_record('oauth2_server_client_access_tokens', [
            'identifier' => 'access-token-id',
            'userid' => 123,
            'clientidentifier' => 'client-id',
            'scopes' => 'profile',
            'expirytime' => time() + 3600,
            'revoked' => access_token_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        $accesstoken = new access_token_entity();
        $accesstoken->setIdentifier('access-token-id');

        $refreshtoken = new refresh_token_entity();
        $refreshtoken->setIdentifier('refresh-token-id');
        $refreshtoken->setAccessToken($accesstoken);
        $refreshtoken->setExpiryDateTime(new \DateTimeImmutable('+1 day'));

        $repository->persistNewRefreshToken($refreshtoken);

        $record = $DB->get_record('oauth2_server_client_refresh_tokens', ['identifier' => 'refresh-token-id']);
        $this->assertNotEmpty($record);
        $this->assertSame('refresh-token-id', $record->identifier);
        $this->assertEquals('access-token-id', $record->accesstokenidentifier);
        $this->assertEquals(0, $record->revoked);

        // Duplicate identifier.
        $this->expectException(\dml_write_exception::class);
        $repository->persistNewRefreshToken($refreshtoken);
    }

    /**
     * Test revoking refresh token.
     */
    public function test_revoke_refresh_token(): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new refresh_token_repository();

        $DB->insert_record('oauth2_server_clients', [
            'name' => 'Test client',
            'clientidentifier' => 'client-id',
            'ownercontext' => \context_system::instance()->id,
            'status' => client_entity::STATUS_ACTIVE,
            'isconfidential' => 1,
            'timecreated' => time(),
        ]);

        $DB->insert_record('oauth2_server_client_access_tokens', [
            'identifier' => 'access-token-id',
            'userid' => 123,
            'clientidentifier' => 'client-id',
            'scopes' => 'profile',
            'expirytime' => time() + 3600,
            'revoked' => access_token_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        $DB->insert_record('oauth2_server_client_refresh_tokens', [
            'identifier' => 'refresh-token-1',
            'accesstokenid' => 'access-token-id',
            'expirytime' => time() + 86400,
            'revoked' => refresh_token_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        $this->assertFalse($repository->isRefreshTokenRevoked('refresh-token-1'));

        $repository->revokeRefreshToken('refresh-token-1');

        $this->assertTrue($repository->isRefreshTokenRevoked('refresh-token-1'));
    }

    /**
     * Test check if refresh token is revoked.
     */
    public function test_is_refresh_token_revoked(): void {
        $repository = new refresh_token_repository();

        $this->expectException(\dml_missing_record_exception::class);
        $this->assertTrue($repository->isRefreshTokenRevoked('non-existent-token'));
    }
}
