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

namespace core\oauth2\server;

use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\auth_code_entity;
use core\oauth2\server\entity\refresh_token_entity;
use core\oauth2\server\repository\access_token_repository;
use core\oauth2\server\repository\refresh_token_repository;
use core\tests\oauth2\credential_issuer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see token_revoker}.
 *
 * Credentials are issued and submitted in their serialised form, because that is what a client
 * holds. Submitting the stored identifier instead would pass against a broken revoker.
 *
 * @package    core
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(token_revoker::class)]
final class token_revoker_test extends \advanced_testcase {
    use credential_issuer;

    /** @var string The client whose credentials are under test. */
    private const string CLIENT = 'client-under-test';

    /** @var string A second client, used to prove one client cannot revoke another's credentials. */
    private const string OTHER_CLIENT = 'other-client';

    /**
     * Issue an access token to a client, returning the signed JWT the client would receive.
     *
     * @param string $clientidentifier The client to issue it to.
     * @param string $identifier The identifier to store it under.
     * @return string The serialised access token.
     */
    private function issue_access_token(string $clientidentifier, string $identifier): string {
        global $DB;

        $userid = $this->getDataGenerator()->create_user()->id;

        $DB->insert_record('oauth2_server_client_access_tokens', (object) [
            'identifier' => $identifier,
            'userid' => $userid,
            'clientidentifier' => $clientidentifier,
            'scopes' => 'core_user:user:read',
            'expirytime' => time() + HOURSECS,
            'revoked' => access_token_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        return $this->serialise_access_token($clientidentifier, $identifier, $userid);
    }

    /**
     * Issue a refresh token alongside an access token, returning the value the client would receive.
     *
     * @param string $identifier The identifier to store it under.
     * @param string $accesstokenidentifier The access token it is issued alongside.
     * @return string The serialised refresh token.
     */
    private function issue_refresh_token(string $identifier, string $accesstokenidentifier): string {
        global $DB;

        $DB->insert_record('oauth2_server_client_refresh_tokens', (object) [
            'identifier' => $identifier,
            'accesstokenidentifier' => $accesstokenidentifier,
            'expirytime' => time() + DAYSECS,
            'revoked' => refresh_token_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        return $this->encrypt_credential_payload(['refresh_token_id' => $identifier]);
    }

    /**
     * Issue an authorisation code to a client, returning the value the client would receive.
     *
     * @param string $clientidentifier The client to issue it to.
     * @param string $identifier The identifier to store it under.
     * @return string The serialised authorisation code.
     */
    private function issue_auth_code(string $clientidentifier, string $identifier): string {
        global $DB;

        $DB->insert_record('oauth2_server_client_auth_codes', (object) [
            'identifier' => $identifier,
            'userid' => $this->getDataGenerator()->create_user()->id,
            'clientidentifier' => $clientidentifier,
            'redirecturi' => 'https://example.com/callback',
            'scopes' => 'core_user:user:read',
            'expirytime' => time() + MINSECS,
            'revoked' => auth_code_entity::REVOKED_NO,
            'timecreated' => time(),
        ]);

        return $this->encrypt_credential_payload(['auth_code_id' => $identifier]);
    }

    /**
     * Get the revoked flag stored against a credential.
     *
     * @param string $table The table holding the credential.
     * @param string $identifier The credential identifier.
     * @return int The stored revoked flag.
     */
    private function get_revoked_flag(string $table, string $identifier): int {
        global $DB;

        return (int) $DB->get_field($table, 'revoked', ['identifier' => $identifier], MUST_EXIST);
    }

    /**
     * Issue one of each credential to both clients and return the revoker.
     *
     * @return array{0: token_revoker, 1: array<string, string>} The revoker, and the serialised
     *      credentials keyed by the identifier they are stored under.
     */
    private function get_revoker(): array {
        $this->resetAfterTest();

        $serialised = [];

        foreach ([self::CLIENT, self::OTHER_CLIENT] as $client) {
            $serialised["accesstoken-{$client}"] = $this->issue_access_token($client, "accesstoken-{$client}");
            $serialised["refreshtoken-{$client}"] = $this->issue_refresh_token(
                "refreshtoken-{$client}",
                "accesstoken-{$client}",
            );
            $serialised["authcode-{$client}"] = $this->issue_auth_code($client, "authcode-{$client}");
        }

        return [\core\di::get(token_revoker::class), $serialised];
    }

    /**
     * Test that the revoker can be resolved from the dependency injection container.
     *
     * @return void
     */
    public function test_resolvable_via_dependency_injection(): void {
        $this->resetAfterTest();

        $this->assertInstanceOf(token_revoker::class, \core\di::get(token_revoker::class));
    }

    /**
     * Test that submitting a serialised access token revokes the record it refers to.
     *
     * @return void
     */
    public function test_revoke_serialised_access_token(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(
            token: $serialised['accesstoken-' . self::CLIENT],
            clientidentifier: self::CLIENT,
            hint: token_revoker::HINT_ACCESS_TOKEN,
        );

        $this->assertEquals(
            access_token_entity::REVOKED_YES,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
    }

    /**
     * Test that submitting the stored identifier rather than the serialised token revokes nothing.
     *
     * A client never holds the identifier, so no legitimate caller can present one.
     *
     * @return void
     */
    public function test_submitting_the_stored_identifier_revokes_nothing(): void {
        [$revoker] = $this->get_revoker();

        $revoker->revoke(token: 'accesstoken-' . self::CLIENT, clientidentifier: self::CLIENT);

        $this->assertEquals(
            access_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
    }

    /**
     * Test that submitting a serialised authorisation code revokes the record it refers to.
     *
     * @return void
     */
    public function test_revoke_serialised_auth_code(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(
            token: $serialised['authcode-' . self::CLIENT],
            clientidentifier: self::CLIENT,
            hint: token_revoker::HINT_AUTH_CODE,
        );

        $this->assertEquals(
            auth_code_entity::REVOKED_YES,
            $this->get_revoked_flag('oauth2_server_client_auth_codes', 'authcode-' . self::CLIENT),
        );
    }

    /**
     * Test that revoking an authorisation code leaves the client's tokens alone.
     *
     * @return void
     */
    public function test_revoke_auth_code_does_not_cascade(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(token: $serialised['authcode-' . self::CLIENT], clientidentifier: self::CLIENT);

        $this->assertEquals(
            access_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
        $this->assertEquals(
            refresh_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_refresh_tokens', 'refreshtoken-' . self::CLIENT),
        );
    }

    /**
     * Test that submitting a serialised refresh token also revokes its access token.
     *
     * @return void
     */
    public function test_revoke_serialised_refresh_token_cascades_to_access_token(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(
            token: $serialised['refreshtoken-' . self::CLIENT],
            clientidentifier: self::CLIENT,
            hint: token_revoker::HINT_REFRESH_TOKEN,
        );

        $this->assertEquals(
            refresh_token_entity::REVOKED_YES,
            $this->get_revoked_flag('oauth2_server_client_refresh_tokens', 'refreshtoken-' . self::CLIENT),
        );
        $this->assertEquals(
            access_token_entity::REVOKED_YES,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
    }

    /**
     * Test that the cascade stops at the client that owns the refresh token.
     *
     * @return void
     */
    public function test_revoke_refresh_token_does_not_affect_other_clients(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(token: $serialised['refreshtoken-' . self::CLIENT], clientidentifier: self::CLIENT);

        $this->assertEquals(
            refresh_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_refresh_tokens', 'refreshtoken-' . self::OTHER_CLIENT),
        );
        $this->assertEquals(
            access_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::OTHER_CLIENT),
        );
    }

    /**
     * Test that a revoked access token is reported as revoked to whoever validates it.
     *
     * @return void
     */
    public function test_revoked_access_token_fails_the_validation_check(): void {
        [$revoker, $serialised] = $this->get_revoker();
        $repository = \core\di::get(access_token_repository::class);

        $this->assertFalse($repository->isAccessTokenRevoked('accesstoken-' . self::CLIENT));

        $revoker->revoke(token: $serialised['accesstoken-' . self::CLIENT], clientidentifier: self::CLIENT);

        $this->assertTrue($repository->isAccessTokenRevoked('accesstoken-' . self::CLIENT));
    }

    /**
     * Test that a cascaded revocation is visible to whoever validates either credential.
     *
     * @return void
     */
    public function test_cascaded_revocation_fails_the_validation_check(): void {
        [$revoker, $serialised] = $this->get_revoker();
        $accesstokens = \core\di::get(access_token_repository::class);
        $refreshtokens = \core\di::get(refresh_token_repository::class);

        $revoker->revoke(token: $serialised['refreshtoken-' . self::CLIENT], clientidentifier: self::CLIENT);

        $this->assertTrue($refreshtokens->isRefreshTokenRevoked('refreshtoken-' . self::CLIENT));
        $this->assertTrue($accesstokens->isAccessTokenRevoked('accesstoken-' . self::CLIENT));
    }

    /**
     * Test that an access token signed by somebody else is not honoured.
     *
     * The identifier comes from a claim inside the token, so an unverified signature must not be
     * trusted for the identifier it names.
     *
     * @return void
     */
    public function test_access_token_signed_with_another_key_is_ignored(): void {
        [$revoker] = $this->get_revoker();

        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        openssl_pkey_export($key, $foreignprivatekey);

        $forged = $this->serialise_access_token(
            self::CLIENT,
            'accesstoken-' . self::CLIENT,
            $this->getDataGenerator()->create_user()->id,
            $foreignprivatekey,
        );

        $revoker->revoke(token: $forged, clientidentifier: self::CLIENT);

        $this->assertEquals(
            access_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
    }

    /**
     * Data provider for values that are not credentials this server issued.
     *
     * @return array[] The value to submit.
     */
    public static function unknown_token_provider(): array {
        return [
            'not a token at all' => ['no-such-token'],
            'empty string' => [''],
            'malformed jwt' => ['a.b.c'],
        ];
    }

    /**
     * Test that a value which is not a credential of ours is accepted without complaint.
     *
     * @param string $token The value to submit.
     * @return void
     */
    #[DataProvider('unknown_token_provider')]
    public function test_unknown_token_is_silent(string $token): void {
        [$revoker] = $this->get_revoker();

        $revoker->revoke(token: $token, clientidentifier: self::CLIENT);

        $this->assertEquals(
            access_token_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
        $this->assertEquals(
            auth_code_entity::REVOKED_NO,
            $this->get_revoked_flag('oauth2_server_client_auth_codes', 'authcode-' . self::CLIENT),
        );
    }

    /**
     * Test that revoking a credential twice is accepted without complaint.
     *
     * @return void
     */
    public function test_revoke_already_revoked_token_is_silent(): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(token: $serialised['accesstoken-' . self::CLIENT], clientidentifier: self::CLIENT);
        $revoker->revoke(token: $serialised['accesstoken-' . self::CLIENT], clientidentifier: self::CLIENT);

        $this->assertEquals(
            access_token_entity::REVOKED_YES,
            $this->get_revoked_flag('oauth2_server_client_access_tokens', 'accesstoken-' . self::CLIENT),
        );
    }

    /**
     * Data provider covering every credential a client may hold.
     *
     * @return array[] The credential identifier prefix, its table, and the unrevoked flag.
     */
    public static function credential_provider(): array {
        return [
            'access token' => [
                'accesstoken-',
                'oauth2_server_client_access_tokens',
                access_token_entity::REVOKED_NO,
            ],
            'refresh token' => [
                'refreshtoken-',
                'oauth2_server_client_refresh_tokens',
                refresh_token_entity::REVOKED_NO,
            ],
            'authorisation code' => [
                'authcode-',
                'oauth2_server_client_auth_codes',
                auth_code_entity::REVOKED_NO,
            ],
        ];
    }

    /**
     * Test that a client cannot revoke a credential belonging to another client.
     *
     * @param string $prefix The credential identifier prefix.
     * @param string $table The table the credential is stored in.
     * @param int $unrevoked The flag the credential should still carry.
     * @return void
     */
    #[DataProvider('credential_provider')]
    public function test_revoke_leaves_another_clients_credential_alone(
        string $prefix,
        string $table,
        int $unrevoked,
    ): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(
            token: $serialised[$prefix . self::OTHER_CLIENT],
            clientidentifier: self::CLIENT,
        );

        $this->assertEquals($unrevoked, $this->get_revoked_flag($table, $prefix . self::OTHER_CLIENT));
    }

    /**
     * Data provider pairing each credential with a hint naming the wrong type.
     *
     * @return array[] The prefix, its table, a misleading hint, and the expected revoked flag.
     */
    public static function wrong_hint_provider(): array {
        return [
            'access token hinted as refresh token' => [
                'accesstoken-',
                'oauth2_server_client_access_tokens',
                token_revoker::HINT_REFRESH_TOKEN,
                access_token_entity::REVOKED_YES,
            ],
            'refresh token hinted as access token' => [
                'refreshtoken-',
                'oauth2_server_client_refresh_tokens',
                token_revoker::HINT_ACCESS_TOKEN,
                refresh_token_entity::REVOKED_YES,
            ],
            'authorisation code hinted as access token' => [
                'authcode-',
                'oauth2_server_client_auth_codes',
                token_revoker::HINT_ACCESS_TOKEN,
                auth_code_entity::REVOKED_YES,
            ],
            'access token with an unrecognised hint' => [
                'accesstoken-',
                'oauth2_server_client_access_tokens',
                'not_a_real_hint',
                access_token_entity::REVOKED_YES,
            ],
            'access token with no hint at all' => [
                'accesstoken-',
                'oauth2_server_client_access_tokens',
                null,
                access_token_entity::REVOKED_YES,
            ],
        ];
    }

    /**
     * Test that a credential is still revoked when the client hints at the wrong type.
     *
     * @param string $prefix The credential identifier prefix.
     * @param string $table The table the credential is stored in.
     * @param string|null $hint The misleading hint.
     * @param int $revoked The flag the credential should end up with.
     * @return void
     */
    #[DataProvider('wrong_hint_provider')]
    public function test_revoke_falls_back_when_the_hint_is_wrong(
        string $prefix,
        string $table,
        ?string $hint,
        int $revoked,
    ): void {
        [$revoker, $serialised] = $this->get_revoker();

        $revoker->revoke(
            token: $serialised[$prefix . self::CLIENT],
            clientidentifier: self::CLIENT,
            hint: $hint,
        );

        $this->assertEquals($revoked, $this->get_revoked_flag($table, $prefix . self::CLIENT));
    }
}
