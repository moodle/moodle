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

namespace core\api\repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use core\api\entity\api_token_entity;

/**
 * Tests for {@see api_token_repository}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(api_token_repository::class)]
final class api_token_repository_test extends \advanced_testcase {
    /**
     * Test token creation.
     */
    public function test_create_token(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token(
            'Token 1',
            'secret',
            $user->id,
            'scope',
            'A description',
            1700000000
        );

        $this->assertInstanceOf(api_token_entity::class, $token);

        $record = $DB->get_record('rest_api_tokens', ['id' => $token->get_id()], '*', MUST_EXIST);

        $this->assertEquals('Token 1', $record->name);
        $this->assertTrue(password_verify('secret', $record->token));
        $this->assertEquals($user->id, $record->userid);
        $this->assertEquals('scope', $record->scopes);
        $this->assertEquals('A description', $record->description);
        $this->assertEquals(1700000000, $record->expirytime);
        $this->assertEquals(api_token_entity::REVOKED_NO, $record->revoked);
    }

    /**
     * Test getting a token by ID.
     */
    public function test_get_by_id(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        // Create a new token and obtain its ID.
        $tokenid = $repository->create_token(
            'Test',
            'secret',
            $user->id,
            'scope'
        )->get_id();

        $token = $repository->get_by_id($tokenid);

        $this->assertEquals($tokenid, $token->get_id());
        $this->assertEquals('Test', $token->get_name());
        $this->assertEquals($user->id, $token->get_userid());
        $this->assertEquals('scope', $token->get_scopes());
    }

    /**
     * Test missing record exceptions.
     */
    public function test_get_missing_throws(): void {
        $repository = new api_token_repository();

        $this->expectException(\dml_missing_record_exception::class);
        $repository->get_by_id(999999);
    }

    /**
     * Test token update.
     */
    public function test_update_token(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token(
            'Original Name',
            'secret',
            $user->id,
            'scope',
            'Original Description',
            1700000000
        );

        $updates = [
            'name' => 'Updated Name',
            'token' => 'updatedsecrethash',
            'userid' => 99999,
            'description' => 'Updated Description',
            'scopes' => 'updatedscope',
            'expirytime' => 1800000000,
        ];

        $repository->update_token($token->get_id(), $updates);

        $updatedtoken = $repository->get_by_id($token->get_id());

        // Verify allowed updates successfully persisted.
        $this->assertEquals('Updated Name', $updatedtoken->get_name());
        $this->assertEquals('Updated Description', $updatedtoken->get_description());
        $this->assertEquals('updatedscope', $updatedtoken->get_scopes());
        $this->assertEquals(1800000000, $updatedtoken->get_expirytime());
        // Verify that protected fields were NOT modified.
        // The user ID should not have changed.
        $this->assertEquals($user->id, $updatedtoken->get_userid());
        // The token hash should not have changed.
        $this->assertNotEquals('updatedsecrethash', $updatedtoken->get_token());
    }

    /**
     * Test revoking a token.
     */
    public function test_revoke_token(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token('Test', 'secret', $user->id, 'scope');
        $this->assertFalse($token->is_revoked());

        $repository->revoke_token($token->get_id());

        $updatedtoken = $repository->get_by_id($token->get_id());
        $this->assertTrue($updatedtoken->is_revoked());
    }

    /**
     * Test deleting a token.
     */
    public function test_delete_token(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token('Test', 'secret', $user->id, 'scope');

        $repository->delete_token($token->get_id());

        $this->expectException(\dml_missing_record_exception::class);
        $repository->get_by_id($token->get_id());
    }

    /**
     * Test token validation.
     *
     * @param bool $revoked
     * @param int|null $expirytime
     * @param string $checksecret
     * @param string|null $expectedexception
     */
    #[DataProvider('validate_token_provider')]
    public function test_validate_token(
        bool $revoked,
        ?int $expirytime,
        string $checksecret,
        ?string $expectedexception
    ): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token(
            'Test',
            'correctsecret',
            $user->id,
            'scope',
            null,
            $expirytime
        );

        if ($revoked) {
            $repository->revoke_token($token->get_id());
        }

        if ($expectedexception) {
            $this->expectException($expectedexception);
        }

        $validatedtoken = $repository->validate_token($token->get_id(), $checksecret);

        if (!$expectedexception) { // The token should be valid.
            $this->assertEquals($token->get_id(), $validatedtoken->get_id());
        }
    }

    /**
     * Data provider for token validation tests.
     */
    public static function validate_token_provider(): array {
        return [
            'valid token' => [
                false,
                time() + 3600,
                'correctsecret',
                null,
            ],
            'invalid secret' => [
                false,
                time() + 3600,
                'wrongsecret',
                \core\exception\invalid_api_token_exception::class,
            ],
            'expired token' => [
                false,
                time() - 3600,
                'correctsecret',
                \core\exception\expired_api_token_exception::class,
            ],
            'revoked token' => [
                true,
                time() + 3600,
                'correctsecret',
                \core\exception\revoked_api_token_exception::class,
            ],
        ];
    }

    /**
     * Test logging token access.
     */
    public function test_log_token_access(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = new api_token_repository();

        $token = $repository->create_token('Test', 'secret', $user->id, 'scope');
        $this->assertNull($token->get_lastaccessed());

        $repository->log_token_access($token->get_id());

        $updatedtoken = $repository->get_by_id($token->get_id());

        $this->assertNotNull($updatedtoken->get_lastaccessed());
        $this->assertGreaterThanOrEqual(time(), $updatedtoken->get_lastaccessed());
    }

    /**
     * Test retrieving user tokens.
     *
     * @param string $userkey The user key to retrieve tokens for.
     * @param bool $includeinactive Whether to include inactive tokens.
     * @param array $expectednames The expected token names.
     */
    #[DataProvider('get_user_tokens_provider')]
    public function test_get_user_tokens(string $userkey, bool $includeinactive, array $expectednames): void {
        global $DB;

        $this->resetAfterTest();

        // Create users.
        $users = [
            'user1' => $this->getDataGenerator()->create_user(),
            'user2' => $this->getDataGenerator()->create_user(),
            'user3' => $this->getDataGenerator()->create_user(),
        ];

        $repository = new api_token_repository();

        // Create an active token for user1.
        $repository->create_token('Active Token 1', 'secret', $users['user1']->id, 'scope');
        // Create revoked token for user 1.
        $token2 = $repository->create_token('Revoked Token', 'secret', $users['user1']->id, 'scope');
        $repository->revoke_token($token2->get_id());
        // Create an expired token for user1.
        $token3 = $repository->create_token('Expired Token', 'secret', $users['user1']->id, 'scope');
        $DB->set_field('rest_api_tokens', 'expirytime', time() - 3600, ['id' => $token3->get_id()]);

        // Create an active token for user2.
        $repository->create_token('Active Token 2', 'secret', $users['user2']->id, 'scope');

        // No tokens for user3.

        $tokens = $repository->get_user_tokens($users[$userkey]->id, $includeinactive);

        $this->assertCount(count($expectednames), $tokens);

        $actualnames = array_map(function ($token) {
            return $token->get_name();
        }, $tokens);

        $this->assertEqualsCanonicalizing($expectednames, $actualnames);
    }

    /**
     * Data provider for get_user_tokens tests.
     */
    public static function get_user_tokens_provider(): array {
        return [
            'user 1, active only' => [
                'user1',
                false,
                [
                    'Active Token 1',
                ],
            ],
            'user 1, include inactive' => [
                'user1',
                true,
                [
                    'Active Token 1',
                    'Revoked Token',
                    'Expired Token',
                ],
            ],
            'user 2, active only' => [
                'user2',
                false,
                [
                    'Active Token 2',
                ],
            ],
            'user 2, include inactive' => [
                'user2',
                true,
                [
                    'Active Token 2',
                ],
            ],
            'user 3, active only' => [
                'user3',
                false,
                [],
            ],
        ];
    }
}
