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

namespace core\route;

use core\oauth2\server\client_manager;
use core\oauth2\server\entity\access_token_entity;
use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\refresh_token_entity;
use core\oauth2\server\repository\client_repository;
use core\oauth2\server\token_revoker;
use core\tests\oauth2\credential_issuer;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;

/**
 * Tests for the OAuth2 token revocation endpoint.
 *
 * These cover the RFC 7009 wire contract. The revocation rules themselves are covered by
 * {@see \core\oauth2\server\token_revoker}.
 *
 * @package    core
 * @category   test
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(oauth2::class)]
final class oauth2_revoke_test extends \advanced_testcase {
    use credential_issuer;

    /** @var string The identifier of the access token issued for each test. */
    private const string TOKEN = 'accesstoken-under-test';

    /**
     * Get the route with a real client repository, so client authentication hits the database.
     *
     * @return oauth2
     */
    private function get_route(): oauth2 {
        return new oauth2(
            $this->createStub(AuthorizationServer::class),
            new client_repository(),
            $this->createStub(ScopeRepositoryInterface::class),
        );
    }

    /**
     * Register a client and return its identifier and plain text secret.
     *
     * @param bool $isconfidential Whether the client can keep a secret.
     * @return array{0: string, 1: string|null} The client identifier, and its secret if it has one.
     */
    private function create_client(bool $isconfidential): array {
        global $DB;

        $manager = \core\di::get(client_manager::class);

        $client = $manager->create_client(
            name: $isconfidential ? 'Confidential client' : 'Public client',
            ownercontext: \core\context\system::instance(),
            isconfidential: $isconfidential,
            granttypes: [
                client_entity::GRANT_TYPE_AUTHORIZATION_CODE,
                client_entity::GRANT_TYPE_REFRESH_TOKEN,
            ],
        );

        $identifier = $client->getIdentifier();
        $clientid = $DB->get_field(
            'oauth2_server_clients',
            'id',
            ['clientidentifier' => $identifier],
            MUST_EXIST,
        );

        return [$identifier, $isconfidential ? $manager->create_secret($clientid) : null];
    }

    /**
     * Issue an access token to a client.
     *
     * @param string $clientidentifier The client to issue it to.
     * @param string $identifier The token identifier.
     * @return string The serialised access token the client would receive.
     */
    private function issue_access_token(string $clientidentifier, string $identifier = self::TOKEN): string {
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
     * Issue a refresh token alongside an access token, returning the encrypted value it is issued as.
     *
     * @param string $identifier The identifier to store it under.
     * @param string $accesstokenidentifier The access token it is issued alongside.
     * @return string The serialised refresh token.
     */
    private function issue_refresh_token(
        string $identifier,
        string $accesstokenidentifier = self::TOKEN,
    ): string {
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
     * Get the revoked flag stored against a refresh token.
     *
     * @param string $identifier The refresh token identifier.
     * @return int The stored revoked flag.
     */
    private function get_refresh_token_revoked_flag(string $identifier): int {
        global $DB;

        return (int) $DB->get_field(
            'oauth2_server_client_refresh_tokens',
            'revoked',
            ['identifier' => $identifier],
            MUST_EXIST,
        );
    }

    /**
     * Post a form-encoded revocation request, as RFC 7009 section 2.1 requires.
     *
     * @param array $params The parameters to send in the request body.
     * @param array $headers Any additional headers.
     * @param string|null $contenttype The content type to declare, or null to send none.
     * @return ResponseInterface
     */
    private function post_revocation(
        array $params,
        array $headers = [],
        ?string $contenttype = 'application/x-www-form-urlencoded',
    ): ResponseInterface {
        if ($contenttype !== null) {
            $headers['Content-Type'] = $contenttype;
        }

        return $this->get_route()->revoke(
            new ServerRequest('POST', '/revoke', $headers, http_build_query($params)),
            new Response(),
            \core\di::get(token_revoker::class),
        );
    }

    /**
     * Get the revoked flag stored against an access token.
     *
     * @param string $identifier The token identifier.
     * @return int The stored revoked flag.
     */
    private function get_revoked_flag(string $identifier = self::TOKEN): int {
        global $DB;

        return (int) $DB->get_field(
            'oauth2_server_client_access_tokens',
            'revoked',
            ['identifier' => $identifier],
            MUST_EXIST,
        );
    }

    /**
     * Get the OAuth2 error code from a response body.
     *
     * @param ResponseInterface $response The response.
     * @return string|null The error code, if the body carried one.
     */
    private function get_error(ResponseInterface $response): ?string {
        return json_decode((string) $response->getBody(), true)['error'] ?? null;
    }

    /**
     * Test that a form-encoded request from a confidential client revokes the token.
     *
     * @return void
     */
    public function test_form_encoded_request_revokes_token(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => $token,
            'token_type_hint' => 'access_token',
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Test that submitting a serialised refresh token revokes it and its access token.
     *
     * Covers the encrypted wire format, and the cascade over HTTP.
     *
     * @return void
     */
    public function test_serialised_refresh_token_revokes_it_and_its_access_token(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $this->issue_access_token($clientidentifier);
        $refreshtoken = $this->issue_refresh_token('refreshtoken-under-test');

        $response = $this->post_revocation([
            'token' => $refreshtoken,
            'token_type_hint' => 'refresh_token',
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            refresh_token_entity::REVOKED_YES,
            $this->get_refresh_token_revoked_flag('refreshtoken-under-test'),
        );
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Test that a public client needs no secret to revoke its own token.
     *
     * @return void
     */
    public function test_public_client_needs_no_secret(): void {
        $this->resetAfterTest();

        [$clientidentifier] = $this->create_client(false);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => $token,
            'client_id' => $clientidentifier,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Test that HTTP Basic authentication is accepted, as RFC 6749 section 2.3.1 requires.
     *
     * @return void
     */
    public function test_basic_authentication_is_accepted(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation(
            ['token' => $token],
            ['Authorization' => 'Basic ' . base64_encode("{$clientidentifier}:{$secret}")],
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Test that an unknown token is answered with 200, per RFC 7009 section 2.2.
     *
     * @return void
     */
    public function test_unknown_token_is_answered_with_success(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => 'no-such-token',
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that unrecognised parameters are ignored rather than refused.
     *
     * @return void
     */
    public function test_unrecognised_parameters_are_ignored(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => $token,
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
            'some_extension_parameter' => 'ignored',
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Data provider for content types the endpoint must refuse.
     *
     * @return array[] The content type to send.
     */
    public static function refused_content_type_provider(): array {
        return [
            'json' => ['application/json'],
            'plain text' => ['text/plain'],
            'multipart form' => ['multipart/form-data'],
            'none at all' => [null],
        ];
    }

    /**
     * Test that a body in any format other than form-urlencoded is refused.
     *
     * @param string|null $contenttype The content type to send.
     * @return void
     */
    #[DataProvider('refused_content_type_provider')]
    public function test_body_that_is_not_form_encoded_is_refused(?string $contenttype): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation(
            [
                'token' => $token,
                'client_id' => $clientidentifier,
                'client_secret' => $secret,
            ],
            contenttype: $contenttype,
        );

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('invalid_request', $this->get_error($response));
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that a charset on the content type does not stop the body being accepted.
     *
     * @return void
     */
    public function test_content_type_parameters_and_case_are_tolerated(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation(
            [
                'token' => $token,
                'client_id' => $clientidentifier,
                'client_secret' => $secret,
            ],
            contenttype: 'Application/X-WWW-Form-Urlencoded; charset=UTF-8',
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(access_token_entity::REVOKED_YES, $this->get_revoked_flag());
    }

    /**
     * Test that an array-shaped token is treated as absent rather than coerced.
     *
     * @return void
     */
    public function test_array_shaped_token_is_refused_cleanly(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);
        $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => ['a', 'b'],
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('invalid_request', $this->get_error($response));
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that a request without a token is refused as an invalid request.
     *
     * @return void
     */
    public function test_missing_token_is_refused(): void {
        $this->resetAfterTest();

        [$clientidentifier, $secret] = $this->create_client(true);

        $response = $this->post_revocation([
            'client_id' => $clientidentifier,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('invalid_request', $this->get_error($response));
    }

    /**
     * Test that a confidential client presenting the wrong secret is refused.
     *
     * @return void
     */
    public function test_wrong_client_secret_is_refused(): void {
        $this->resetAfterTest();

        [$clientidentifier] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation([
            'token' => $token,
            'client_id' => $clientidentifier,
            'client_secret' => 'not-the-secret',
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('invalid_client', $this->get_error($response));
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that a request carrying no client credentials at all is refused.
     *
     * The caller is established before the token is looked at.
     *
     * @return void
     */
    public function test_request_without_client_credentials_is_refused(): void {
        $this->resetAfterTest();

        [$clientidentifier] = $this->create_client(true);
        $token = $this->issue_access_token($clientidentifier);

        $response = $this->post_revocation(['token' => $token]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('invalid_client', $this->get_error($response));
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that a token belonging to another client is answered with 200 but left alone.
     *
     * The response must be indistinguishable from a successful revocation. That is a property of
     * the response, so it can only be asserted here.
     *
     * @return void
     */
    public function test_another_clients_token_is_left_alone(): void {
        $this->resetAfterTest();

        [$owner] = $this->create_client(false);
        [$caller, $secret] = $this->create_client(true);
        $token = $this->issue_access_token($owner);

        $response = $this->post_revocation([
            'token' => $token,
            'client_id' => $caller,
            'client_secret' => $secret,
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull($this->get_error($response));
        $this->assertEquals(access_token_entity::REVOKED_NO, $this->get_revoked_flag());
    }

    /**
     * Test that an unknown client is refused.
     *
     * @return void
     */
    public function test_unknown_client_is_refused(): void {
        $this->resetAfterTest();

        $response = $this->post_revocation([
            'token' => self::TOKEN,
            'client_id' => 'no-such-client',
            'client_secret' => 'irrelevant',
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('invalid_client', $this->get_error($response));
    }

    /**
     * Test that an array-shaped client identifier is treated as absent rather than coerced.
     *
     * @return void
     */
    public function test_array_shaped_client_id_is_refused_cleanly(): void {
        $this->resetAfterTest();

        $response = $this->post_revocation([
            'token' => self::TOKEN,
            'client_id' => ['a', 'b'],
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('invalid_client', $this->get_error($response));
    }
}
