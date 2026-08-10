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

use core\oauth2\server\entity\client_entity;
use core\oauth2\server\entity\user_entity;
use core\oauth2\server\repository\granted_scopes_repository;
use core\oauth2\server\repository\user_repository;
use core_auth\output\login;
use core_auth\output\oauth2\confirm_scopes_page;
use core_auth\output\oauth2\continue_as_user_page;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Unit tests for the oauth2 route class.
 *
 * @package    core
 * @copyright  2026 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(oauth2::class)]
final class oauth2_test extends \advanced_testcase {
    /**
     * Get an oauth2 route instance with stubbed OAuth2 server dependencies, and
     * render_page_from_renderable() replaced with a stub so that no real page rendering
     * takes place.
     *
     * Actual page rendering is exercised separately by login_test.php, so it is safe
     * to bypass here in order to focus on the wiring performed by the routes themselves.
     *
     * @param AuthorizationServer|null $server
     * @param ClientRepositoryInterface|null $clientrepository
     * @param ScopeRepositoryInterface|null $scoperepository
     * @return oauth2&\PHPUnit\Framework\MockObject\MockObject
     */
    protected function get_route_with_stubbed_rendering(
        ?AuthorizationServer $server = null,
        ?ClientRepositoryInterface $clientrepository = null,
        ?ScopeRepositoryInterface $scoperepository = null,
    ): oauth2 {
        return $this->getMockBuilder(oauth2::class)
            ->setConstructorArgs([
                $server ?? $this->createStub(AuthorizationServer::class),
                $clientrepository ?? $this->createStub(ClientRepositoryInterface::class),
                $scoperepository ?? $this->createStub(ScopeRepositoryInterface::class),
            ])
            ->onlyMethods(['render_page_from_renderable'])
            ->getMock();
    }

    /**
     * Get a real oauth2 route instance, for tests which do not need to intercept rendering.
     *
     * @param AuthorizationServer|null $server
     * @param ClientRepositoryInterface|null $clientrepository
     * @param ScopeRepositoryInterface|null $scoperepository
     */
    protected function get_route(
        ?AuthorizationServer $server = null,
        ?ClientRepositoryInterface $clientrepository = null,
        ?ScopeRepositoryInterface $scoperepository = null,
    ): oauth2 {
        return new oauth2(
            $server ?? $this->createStub(AuthorizationServer::class),
            $clientrepository ?? $this->createStub(ClientRepositoryInterface::class),
            $scoperepository ?? $this->createStub(ScopeRepositoryInterface::class),
        );
    }

    /**
     * Create a client entity fixture with the specified identifier.
     *
     * @param string $identifier
     */
    protected function make_client_entity(string $identifier = 'client1'): client_entity {
        $client = new client_entity();
        $client->setIdentifier($identifier);
        return $client;
    }

    /**
     * Create a user entity fixture with the specified identifier.
     *
     * @param int|string $identifier
     */
    protected function make_user_entity(int|string $identifier = 2): user_entity {
        $user = new user_entity();
        $user->setIdentifier($identifier);
        return $user;
    }

    /**
     * Create a real AuthorizationRequest fixture, with a client already set (as is always the
     * case by the time one is stored in the session).
     *
     * @param client_entity|null $client
     * @param string|null $state
     */
    protected function make_auth_request(
        ?client_entity $client = null,
        ?string $state = null,
    ): AuthorizationRequest {
        $authrequest = new AuthorizationRequest();
        $authrequest->setGrantTypeId('authorization_code');
        $authrequest->setClient($client ?? $this->make_client_entity());
        if ($state !== null) {
            $authrequest->setState($state);
        }
        return $authrequest;
    }

    /**
     * Store an AuthorizationRequest in the session, in the same way that
     * oauth2::store_auth_request() does, keyed by request id.
     *
     * @param AuthorizationRequest $authrequest
     * @param string|null $requestid
     * @return string The request id the request was stored under.
     */
    protected function store_auth_request_in_session(
        AuthorizationRequest $authrequest,
        ?string $requestid = null,
    ): string {
        global $SESSION;

        $requestid ??= \core\uuid::generate();
        $SESSION->oauth2requests ??= [];
        $SESSION->oauth2requests[$requestid] = [
            'granttypeid' => $authrequest->getGrantTypeId(),
            'clientid' => $authrequest->getClient()->getIdentifier(),
            'redirecturi' => $authrequest->getRedirectUri(),
            'state' => $authrequest->getState(),
            'scopes' => array_map(
                fn ($scope): string => $scope->getIdentifier(),
                $authrequest->getScopes(),
            ),
            'userid' => $authrequest->getUser()?->getIdentifier(),
            'authorizationapproved' => $authrequest->isAuthorizationApproved(),
            'codechallenge' => $authrequest->getCodeChallenge(),
            'codechallengemethod' => $authrequest->getCodeChallengeMethod(),
        ];

        return $requestid;
    }

    /**
     * Fetch the raw array stored in the session for the given request id.
     *
     * @param string $requestid
     */
    protected function get_auth_request_from_session(string $requestid): array {
        global $SESSION;

        return $SESSION->oauth2requests[$requestid];
    }

    /**
     * Extract the "authrequestid" query parameter from a redirect response's Location header.
     *
     * @param ResponseInterface $response
     */
    protected function get_requestid_from_response(ResponseInterface $response): string {
        $location = $response->getHeaderLine('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $params);

        $this->assertArrayHasKey('authrequestid', $params);
        return $params['authrequestid'];
    }

    /**
     * The OAuth2 login page never offers guest login or signup, regardless of the site's
     * standard login settings.
     */
    public function test_login_disables_guest_login_and_signup(): void {
        global $CFG, $PAGE;

        $this->resetAfterTest();

        // Configure the site so that, on the standard login page, both guest login and signup
        // would be available. The OAuth2 login page must override these regardless.
        $CFG->guestloginbutton = 1;
        $CFG->registerauth = 'email';

        $route = $this->get_route_with_stubbed_rendering();

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->login(
            new ServerRequest('GET', '/login'),
            new Response(),
        );

        $this->assertInstanceOf(login::class, $capturedcontent);

        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertFalse($data->canloginasguest);
        $this->assertFalse($data->cansignup);
    }

    /**
     * When a user is already logged in (and is not the guest user), login() offers a
     * "continue as this user" page instead of the login form.
     */
    public function test_login_offers_continue_as_user_when_logged_in(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $route = $this->get_route_with_stubbed_rendering();

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->login(
            new ServerRequest('GET', '/login'),
            new Response(),
        );

        $this->assertInstanceOf(continue_as_user_page::class, $capturedcontent);
    }

    /**
     * authorize() redirects anonymous users to the login page.
     */
    public function test_authorize_redirects_to_login_for_anonymous_user(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('validateAuthorizationRequest')
            ->willReturn($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route($server, $clientrepository);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams(['client_id' => 'client1']),
            new Response(),
            new user_repository(),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * authorize() sets the current user on the auth request when the user is already logged in.
     */
    public function test_authorize_sets_current_user_when_logged_in(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('validateAuthorizationRequest')->willReturn($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route($server, $clientrepository);

        $userentity = $this->make_user_entity($user->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->expects($this->once())
            ->method('get_current_user')
            ->willReturn($userentity);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams(['client_id' => 'client1']),
            new Response(),
            $userrepository,
        );

        $requestid = $this->get_requestid_from_response($response);
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $user->id, (string) $storedrequest['userid']);
    }

    /**
     * authorize() ignores any stale, unrelated pending request already in the session (e.g. left
     * over from a previous, unrelated flow) and always validates a fresh request when the
     * incoming request does not carry a matching "authrequestid".
     */
    public function test_authorize_ignores_unrelated_stale_session_entry(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();

        // An existing, unrelated pending request already in the session.
        $this->store_auth_request_in_session($this->make_auth_request($client, 'old-state'));

        $newauthrequest = $this->make_auth_request($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('validateAuthorizationRequest')
            ->willReturn($newauthrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route($server, $clientrepository);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams(['client_id' => 'client1']),
            new Response(),
            new user_repository(),
        );

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * authorize() converts an OAuthServerException raised while validating the request into an
     * HTTP response, rather than allowing it to propagate.
     */
    public function test_authorize_handles_oauth_server_exception(): void {
        $this->resetAfterTest();

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('validateAuthorizationRequest')
            ->willThrowException(OAuthServerException::invalidCredentials());

        $route = $this->get_route($server);

        $response = $route->authorize(
            new ServerRequest('GET', '/authorize'),
            new Response(),
            new user_repository(),
        );

        $this->assertEquals(
            OAuthServerException::invalidCredentials()->getHttpStatusCode(),
            $response->getStatusCode(),
        );
    }

    /**
     * Data provider of the two token-issuing routes which share identical implementations.
     *
     * @return \Generator
     */
    public static function token_route_provider(): \Generator {
        yield 'token' => ['token'];
        yield 'access_token' => ['access_token'];
    }

    /**
     * token()/access_token() pass through directly to the OAuth2 server on success.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('token_route_provider')]
    public function test_token_routes_success(string $method): void {
        $this->resetAfterTest();

        $expectedresponse = new Response(200, [], 'the-token-response');

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('respondToAccessTokenRequest')
            ->willReturn($expectedresponse);

        $route = $this->get_route($server);

        $response = $route->{$method}(
            new ServerRequest('POST', '/' . $method),
            new Response(),
        );

        $this->assertSame($expectedresponse, $response);
    }

    /**
     * token()/access_token() convert an OAuthServerException into an HTTP response.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('token_route_provider')]
    public function test_token_routes_oauth_server_exception(string $method): void {
        $this->resetAfterTest();

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('respondToAccessTokenRequest')
            ->willThrowException(OAuthServerException::invalidCredentials());

        $route = $this->get_route($server);

        $response = $route->{$method}(
            new ServerRequest('POST', '/' . $method),
            new Response(),
        );

        $this->assertEquals(
            OAuthServerException::invalidCredentials()->getHttpStatusCode(),
            $response->getStatusCode(),
        );
    }

    /**
     * token()/access_token() convert any other exception into a HTTP 500 response, with the
     * exception message written into the response body.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('token_route_provider')]
    public function test_token_routes_generic_exception(string $method): void {
        $this->resetAfterTest();

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('respondToAccessTokenRequest')
            ->willThrowException(new \Exception('Something went wrong'));

        $route = $this->get_route($server);

        $response = $route->{$method}(
            new ServerRequest('POST', '/' . $method),
            new Response(),
        );

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Something went wrong', (string) $response->getBody());
    }

    /**
     * refresh() passes through directly to the OAuth2 server.
     */
    public function test_refresh(): void {
        $this->resetAfterTest();

        $expectedresponse = new Response(200, [], 'the-refresh-response');

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('respondToAccessTokenRequest')
            ->willReturn($expectedresponse);

        $route = $this->get_route($server);

        $response = $route->refresh(
            new ServerRequest('POST', '/refresh'),
            new Response(),
        );

        $this->assertSame($expectedresponse, $response);
    }

    /**
     * logout() refuses to proceed without a valid sesskey.
     */
    public function test_logout_requires_sesskey(): void {
        $this->resetAfterTest();

        $route = $this->get_route();

        $this->expectException(\core\exception\moodle_exception::class);
        $route->logout(
            new ServerRequest('POST', '/logout'),
            new Response(),
        );
    }

    /**
     * logout() logs the current user out and redirects to authorize().
     */
    public function test_logout_success(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $route = $this->get_route();

        $request = (new ServerRequest('POST', '/logout'))->withParsedBody(['sesskey' => sesskey()]);
        $response = $route->logout($request, new Response());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));
        $this->assertFalse(isloggedin());
    }

    /**
     * do_login() redirects to approve() and stores the current user on the auth request when
     * the user chooses to continue as their existing session's user.
     */
    public function test_do_login_continue_as_current_user(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->expects($this->once())
            ->method('get_current_user')
            ->willReturn($this->make_user_entity($user->id));

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody(['currentuser' => '1', 'sesskey' => sesskey()]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/approve', $response->getHeaderLine('Location'));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $user->id, (string) $storedrequest['userid']);
    }

    /**
     * do_login() rejects a 'continue as current user' submission that is missing a valid sesskey, to
     * protect this state-changing, cookie-authenticated action against CSRF.
     */
    public function test_do_login_continue_as_current_user_requires_sesskey(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->expects($this->never())->method('get_current_user');

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody(['currentuser' => '1', 'sesskey' => 'invalid']);

        $this->expectException(\moodle_exception::class);
        $route->do_login($request, new Response(), $userrepository);
    }

    /**
     * do_login() redirects to approve() when valid credentials are supplied.
     */
    public function test_do_login_valid_credentials(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $user = $this->make_user_entity(2);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['getUserEntityByUserCredentials'])
            ->getMock();
        $userrepository->expects($this->once())
            ->method('getUserEntityByUserCredentials')
            ->with('bob', 'secret', '', $client)
            ->willReturn($user);

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/approve', $response->getHeaderLine('Location'));
    }

    /**
     * do_login() redirects back to the login form when the supplied credentials are invalid.
     */
    public function test_do_login_invalid_credentials(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['getUserEntityByUserCredentials'])
            ->getMock();
        $userrepository->method('getUserEntityByUserCredentials')->willReturn(null);

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'wrong',
            ]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));
    }

    /**
     * do_login() does not treat an unset 'currentuser' field as "continue as current user".
     */
    public function test_do_login_currentuser_not_set_falls_through_to_credentials(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['getUserEntityByUserCredentials'])
            ->getMock();
        $userrepository->expects($this->never())->method('getUserEntityByUserCredentials');

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * approve() renders a confirm_scopes_page with the wiring provided by the route, without
     * asserting on the (as yet unimplemented) scope-diffing behaviour.
     */
    public function test_approve_renders_confirm_scopes_page(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity(2));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->method('get_granted_scopes_for_user')->willReturn([]);

        $route = $this->get_route_with_stubbed_rendering(
            clientrepository: $clientrepository,
            scoperepository: $scoperepository,
        );

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->approve(
            (new ServerRequest('GET', '/approve'))->withQueryParams([
                'client_id' => 'client1',
                'authrequestid' => $requestid,
            ]),
            new Response(),
            $scoperepository,
            $grantedscopesrepository,
        );

        $this->assertInstanceOf(confirm_scopes_page::class, $capturedcontent);
    }

    /**
     * do_approve() stores the selected scopes and marks the request as approved when the user
     * approves the request.
     */
    public function test_do_approve_when_approved(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $user = $this->make_user_entity(2);
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($user);
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->once())
            ->method('store_granted_scopes_for_user')
            ->with($client, $user, ['moodle']);

        $expectedresponse = new Response(200, [], 'authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authrequest) use ($expectedresponse): ResponseInterface {
                $this->assertTrue($authrequest->isAuthorizationApproved());
                return $expectedresponse;
            });

        $route = $this->get_route($server, $clientrepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
                'scopes' => ['moodle'],
            ]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertSame($expectedresponse, $response);

        // The pending request must be discarded once the flow has completed, so that it cannot
        // be replayed (e.g. by resubmitting the approval form).
        global $SESSION;
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * do_approve() requires a valid sesskey to be submitted, to protect against CSRF.
     */
    public function test_do_approve_requires_sesskey(): void {
        $this->resetAfterTest();

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $route = $this->get_route();

        $this->expectException(\core\exception\moodle_exception::class);
        $route->do_approve(
            new ServerRequest('POST', '/approve'),
            new Response(),
            $grantedscopesrepository,
        );
    }

    /**
     * do_approve() does not store any scopes, and does not mark the request as approved, when
     * the user does not approve the request.
     */
    public function test_do_approve_when_not_approved(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $user = $this->make_user_entity(2);
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($user);
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $expectedresponse = new Response(200, [], 'not-authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authrequest) use ($expectedresponse): ResponseInterface {
                $this->assertFalse($authrequest->isAuthorizationApproved());
                return $expectedresponse;
            });

        $route = $this->get_route($server, $clientrepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody(['sesskey' => sesskey()]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertSame($expectedresponse, $response);
    }
}
