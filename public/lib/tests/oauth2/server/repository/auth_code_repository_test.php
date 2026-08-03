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
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\auth_code_entity;

/**
 * Tests for {@see auth_code_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(auth_code_repository::class)]
final class auth_code_repository_test extends \advanced_testcase {
    /**
     * Test getting a new auth code.
     */
    public function test_get_new_auth_code(): void {
        $repository = new auth_code_repository();
        $code = $repository->getNewAuthCode();

        $this->assertInstanceOf(auth_code_entity::class, $code);
    }

    /**
     * Test persisting a new auth code.
     */
    public function test_persist_new_auth_code(): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new auth_code_repository();

        $DB->insert_record('oauth2_server_clients', [
            'name' => 'Test client',
            'clientidentifier' => 'client-id',
            'ownercontext' => \context_system::instance()->id,
            'status' => client_entity::STATUS_ACTIVE,
            'isconfidential' => 1,
            'timecreated' => time(),
        ]);

        $client = new client_entity();
        $client->setIdentifier('client-id');

        $scope = $this->createMock(ScopeEntityInterface::class);
        $scope->method('getIdentifier')->willReturn('profile');

        $code = new auth_code_entity();
        $code->setIdentifier('code-id');
        $code->setClient($client);
        $code->setUserIdentifier('123');
        $code->addScope($scope);
        $code->setRedirectUri('https://example.test/callback');
        $code->setExpiryDateTime(new \DateTimeImmutable('+10 minutes'));

        $repository->persistNewAuthCode($code);

        $record = $DB->get_record('oauth2_server_client_auth_codes', ['identifier' => 'code-id']);
        $this->assertNotEmpty($record);
        $this->assertSame('code-id', $record->identifier);
        $this->assertEquals(123, $record->userid);
        $this->assertEquals('client-id', $record->clientidentifier);
        $this->assertSame('https://example.test/callback', $record->redirecturi);
        $this->assertSame('profile', $record->scopes);
        $this->assertEquals(0, $record->revoked);

        // Duplicate identifier.
        $this->expectException(\dml_write_exception::class);
        $repository->persistNewAuthCode($code);
    }

    /**
     * Test revoking auth code.
     */
    public function test_revoke_auth_code(): void {
        global $DB;

        $this->resetAfterTest();

        $repository = new auth_code_repository();

        $clientid = $DB->insert_record('oauth2_server_clients', [
            'name' => 'Test client',
            'clientidentifier' => 'client-id',
            'ownercontext' => \context_system::instance()->id,
            'status' => client_entity::STATUS_ACTIVE,
            'isconfidential' => 1,
            'timecreated' => time(),
        ]);

        $DB->insert_record('oauth2_server_client_auth_codes', [
            'identifier' => 'code-id-to-revoke',
            'userid' => 123,
            'clientidentifier' => $clientid,
            'redirecturi' => 'https://example.test/callback',
            'scopes' => 'profile',
            'expirytime' => time() + 600,
            'revoked' => auth_code_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        $this->assertFalse($repository->isAuthCodeRevoked('code-id-to-revoke'));

        $repository->revokeAuthCode('code-id-to-revoke');

        $this->assertTrue($repository->isAuthCodeRevoked('code-id-to-revoke'));
    }

    /**
     * Test check if auth code is revoked.
     */
    public function test_is_auth_code_revoked(): void {
        $repository = new auth_code_repository();

        $this->expectException(\dml_missing_record_exception::class);
        $repository->isAuthCodeRevoked('non-existent-code');
    }
}
