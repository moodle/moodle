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
use core_auth\password_expiry_check;
use core_auth\password_expiry_checker;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
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
            ->onlyMethods(['render_page_from_renderable', 'render_external_password_expiry_page'])
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
     * Create a client entity fixture with the specified identifier, name and description.
     *
     * Built via create_from_record() (rather than a bare new client_entity() with only its
     * identifier set), so that a test exercising anything that reads the client's name or
     * description (e.g. oauth2_page::describe_client(), used by the OAuth2 login screen and
     * the other OAuth2 pages) does not fail on those typed properties being uninitialised.
     *
     * @param string $identifier
     * @param string $name
     * @param string $description
     */
    protected function make_client_entity(
        string $name = 'Example client',
        string $description = 'This application would like to access your account.',
    ): client_entity {
        $clientmanager = \core\di::get(\core\oauth2\server\client_manager::class);

        $client = $clientmanager->create_client(
            name: $name,
            ownercontext: \core\context\system::instance(),
            granttypes: [],
            description: $description,
            isconfidential: true,
        );

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
     * @param string[] $scopes Scope identifiers to set on the request, if any.
     */
    protected function make_auth_request(
        ?client_entity $client = null,
        ?string $state = null,
        array $scopes = [],
    ): AuthorizationRequest {
        $authrequest = new AuthorizationRequest();
        $authrequest->setGrantTypeId('authorization_code');
        $authrequest->setClient($client ?? $this->make_client_entity());
        if ($state !== null) {
            $authrequest->setState($state);
        }
        if ($scopes !== []) {
            $authrequest->setScopes(array_map(
                fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier),
                $scopes,
            ));
        }
        return $authrequest;
    }

    /**
     * Create a scope entity fixture with the specified identifier.
     *
     * @param string $identifier
     */
    protected function make_scope_entity(string $identifier): ScopeEntityInterface {
        $scope = $this->createStub(ScopeEntityInterface::class);
        $scope->method('getIdentifier')->willReturn($identifier);
        return $scope;
    }

    /**
     * Create a granted_scopes_repository stub with has_granted_all_scopes() configured to
     * always return the given value.
     *
     * Used by authorize() tests which do not otherwise care about the granted scopes repository's
     * wiring; tests which do care construct their own mock/real instance instead.
     *
     * @param bool $hasgrantedallscopes
     * @return granted_scopes_repository
     */
    protected function make_granted_scopes_repository_stub(bool $hasgrantedallscopes = false): granted_scopes_repository {
        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has_granted_all_scopes'])
            ->getMock();
        $grantedscopesrepository->method('has_granted_all_scopes')->willReturn($hasgrantedallscopes);
        return $grantedscopesrepository;
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
     * Set $PAGE->url as moodle_bootstrap_middleware would for a real routed GET request to
     * /authorize?authrequestid=$requestid, so that require_login()'s wantsurl mechanism (via
     * qualified_me(), which prefers $PAGE->url when set) resolves the same way it would in
     * production. Needed by tests that call authorize() directly, bypassing the router's own
     * middleware stack.
     *
     * @param string $requestid
     */
    protected function set_page_url_for_authorize(string $requestid): void {
        global $PAGE;

        $PAGE->set_url('/authorize', ['authrequestid' => $requestid]);
    }

    /**
     * Set $PAGE->url as moodle_bootstrap_middleware would for a real routed request to
     * /approve?authrequestid=$requestid. See {@see self::set_page_url_for_authorize()}.
     *
     * @param string $requestid
     */
    protected function set_page_url_for_approve(string $requestid): void {
        global $PAGE;

        $PAGE->set_url('/approve', ['authrequestid' => $requestid]);
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

        // The login() route is only ever reached, in production, via authorize()'s redirect,
        // which always attaches "authrequestid" for a request already validated and stored in
        // the session. Simulate that here, rather than hitting the unconfigured
        // AuthorizationServer stub's validateAuthorizationRequest() fallback.
        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($user->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        $this->assertInstanceOf(continue_as_user_page::class, $capturedcontent);
    }

    /**
     * login() displays the live session user on the "continue as this user" page, not the user
     * stored on the pending authorization request: the request may have been authenticated as a
     * different user (e.g. in another browser tab) since it was first created, and submitting
     * the form always continues as the live session user, never the one recorded on the request.
     * Showing the stored request's user here would display incorrect security-relevant
     * information, even though it is not itself an authorization bypass.
     *
     * The requesting client's identity must still come from the pending request.
     */
    public function test_login_continue_as_user_page_shows_live_session_user_not_stored_request_user(): void {
        global $PAGE;

        $this->resetAfterTest();

        // Two clearly different users: usera is recorded on the pending request, userb is who
        // is actually logged in when login() is reached.
        $usera = $this->getDataGenerator()->create_user(['username' => 'usera']);
        $userb = $this->getDataGenerator()->create_user(['username' => 'userb']);
        $this->setUser($userb);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($usera->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        $this->assertInstanceOf(continue_as_user_page::class, $capturedcontent);

        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals('userb', $data->userinfo->username);
        $this->assertStringContainsString('/user/profile.php?id=' . $userb->id, $data->userinfo->profileurl);

        // The requesting client's identity must still come from the pending request.
        $this->assertSame('Example client', $data->client->name);
        $this->assertSame($client->getIdentifier(), $data->client->identifier);

        // Rendering the page must not itself have modified the pending request's stored user.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $usera->id, (string) $storedrequest['userid']);
    }

    /**
     * login() supplies the requesting client from the pending authorization request to the
     * login renderable, so its identity can be shown before the user enters their credentials.
     */
    public function test_login_supplies_client_from_pending_request_to_login_form(): void {
        global $PAGE;

        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        $this->assertInstanceOf(login::class, $capturedcontent);

        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertTrue($data->hasoauth2client);
        $this->assertNotNull($data->client);
        $this->assertSame('Example client', $data->client->name);
        $this->assertSame($client->getIdentifier(), $data->client->identifier);
    }

    /**
     * login() does not set any OAuth2 client on the login renderable when there is no pending
     * authorization request to take it from (e.g. no request id was supplied at all).
     */
    public function test_login_does_not_set_oauth2_client_when_no_pending_request(): void {
        global $PAGE;

        $this->resetAfterTest();

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
        $this->assertFalse($data->hasoauth2client);
        $this->assertNull($data->client);
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
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            new user_repository(),
            $this->make_granted_scopes_repository_stub(),
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
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            $userrepository,
            $this->make_granted_scopes_repository_stub(),
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
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            new user_repository(),
            $this->make_granted_scopes_repository_stub(),
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
            $this->make_granted_scopes_repository_stub(),
        );

        $this->assertEquals(
            OAuthServerException::invalidCredentials()->getHttpStatusCode(),
            $response->getStatusCode(),
        );
    }

    /**
     * authorize() approves and completes the authorization request immediately, without
     * redirecting to the login, "continue as user", or scope confirmation screens, when the
     * logged-in user has already granted every scope this client is requesting.
     */
    public function test_authorize_completes_immediately_when_all_requested_scopes_already_granted(): void {
        global $SESSION;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $expectedresponse = new Response(200, [], 'authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('validateAuthorizationRequest');
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authrequest) use ($expectedresponse): ResponseInterface {
                $this->assertTrue($authrequest->isAuthorizationApproved());
                return $expectedresponse;
            });

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $userentity = $this->make_user_entity($user->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($userentity);

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has_granted_all_scopes'])
            ->getMock();
        $grantedscopesrepository->expects($this->once())
            ->method('has_granted_all_scopes')
            ->with(
                $this->identicalTo($client),
                $this->identicalTo($userentity),
                $this->callback(
                    fn (array $scopes): bool => array_map(fn ($s) => $s->getIdentifier(), $scopes) === ['moodle'],
                ),
            )
            ->willReturn(true);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
            $userrepository,
            $grantedscopesrepository,
        );

        $this->assertSame($expectedresponse, $response);

        // No redirect: the pending request is discarded entirely, rather than being left in the
        // session for a later leg of the interactive flow (login/continue-as-user/scope
        // confirmation) to pick up.
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * authorize() preserves the existing interactive flow (redirecting to login()) when the
     * logged-in user has granted some, but not all, of the scopes this client is requesting.
     */
    public function test_authorize_preserves_interactive_flow_when_some_requested_scopes_not_granted(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle', 'email']);

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('validateAuthorizationRequest')->willReturn($authrequest);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route($server, $clientrepository);

        $userentity = $this->make_user_entity($user->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($userentity);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            $userrepository,
            $this->make_granted_scopes_repository_stub(hasgrantedallscopes: false),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));

        $requestid = $this->get_requestid_from_response($response);
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $user->id, (string) $storedrequest['userid']);
    }

    /**
     * authorize() preserves the existing interactive flow when the logged-in user has never
     * granted any of the requested scopes, using a real granted_scopes_repository backed by the
     * (empty) database table, rather than a mocked return value.
     */
    public function test_authorize_preserves_interactive_flow_when_no_requested_scopes_granted(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('validateAuthorizationRequest')->willReturn($authrequest);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $userentity = $this->make_user_entity($user->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($userentity);

        $grantedscopesrepository = new granted_scopes_repository($scoperepository);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            $userrepository,
            $grantedscopesrepository,
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * authorize()'s already-granted check is scoped to the current user and current client:
     * a grant belonging to a different user (even for the same client and scope), or to a
     * different client (even for the same user and scope), must not cause silent authorization.
     */
    public function test_authorize_does_not_auto_approve_using_grant_for_different_user_or_client(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);

        // A grant already exists for the same client and scope, but a *different* user.
        $DB->insert_record('oauth2_server_client_granted_scopes', (object) [
            'clientidentifier' => $client->getIdentifier(),
            'userid' => $otheruser->id,
            'scope' => 'moodle',
            'timecreated' => time(),
        ]);

        // ...and another for the same user, but a *different* client.
        $DB->insert_record('oauth2_server_client_granted_scopes', (object) [
            'clientidentifier' => 'other-client',
            'userid' => $user->id,
            'scope' => 'moodle',
            'timecreated' => time(),
        ]);

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('validateAuthorizationRequest')->willReturn($authrequest);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $userentity = $this->make_user_entity($user->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($userentity);

        $grantedscopesrepository = new granted_scopes_repository($scoperepository);

        $response = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams([
                'client_id' => $client->getIdentifier(),
            ]),
            new Response(),
            $userrepository,
            $grantedscopesrepository,
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
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
     * token()/access_token() convert any other exception into a generic HTTP 500 response,
     * without exposing the unexpected exception's own message (which could contain database
     * errors, paths, class names, or other internal details) to the OAuth client.
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
        $this->assertStringNotContainsString('Something went wrong', (string) $response->getBody());

        // The unexpected exception is still logged server-side (as a developer debugging
        // message), just not exposed to the client.
        $this->assertDebuggingCalled();
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
     * do_login() redirects to authorize() and stores the live session user on the auth request
     * when the user chooses to continue as their existing session's user - even when the
     * pending request was previously recorded (e.g. from an earlier session) for a different
     * user, submitting 'continue as current user' always uses and stores the live session user,
     * never the one previously on the request.
     */
    public function test_do_login_continue_as_current_user(): void {
        $this->resetAfterTest();

        // Two clearly different users: usera is initially recorded on the pending request,
        // userb is the live session user who actually submits the form.
        $usera = $this->getDataGenerator()->create_user(['username' => 'usera']);
        $userb = $this->getDataGenerator()->create_user(['username' => 'userb']);
        $this->setUser($userb);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($usera->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->expects($this->once())
            ->method('get_current_user')
            ->willReturn($this->make_user_entity($userb->id));

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody(['currentuser' => '1', 'sesskey' => sesskey()]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        // The pending request must now record userb (the live session user who submitted the
        // form), not usera (whoever it was previously recorded for).
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $userb->id, (string) $storedrequest['userid']);
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
     * A valid sesskey alone does not prove there is a genuine session to continue as: do_login()
     * rejects a 'continue as current user' submission with a valid sesskey but no live logged-in
     * session at all, rather than falling through to some other default identity.
     */
    public function test_do_login_continue_as_current_user_rejects_logged_out_session(): void {
        $this->resetAfterTest();

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
            ->withParsedBody(['currentuser' => '1', 'sesskey' => sesskey()]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));
    }

    /**
     * do_login() rejects a 'continue as current user' submission with a valid sesskey when the
     * live session is the guest user: guest sessions carry a valid sesskey too, so the sesskey
     * check alone would not otherwise catch this.
     */
    public function test_do_login_continue_as_current_user_rejects_guest_session(): void {
        $this->resetAfterTest();
        $this->setGuestUser();

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
            ->withParsedBody(['currentuser' => '1', 'sesskey' => sesskey()]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));
    }

    /**
     * do_login() redirects to authorize() when valid credentials are supplied.
     */
    public function test_do_login_valid_credentials(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $moodleuser = $this->getDataGenerator()->create_user();
        $user = $this->make_user_entity($moodleuser->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user', 'get_current_user'])
            ->getMock();
        $userrepository->expects($this->once())
            ->method('authenticate_user')
            ->with('bob', 'secret', '')
            ->willReturn($moodleuser);
        $userrepository->method('get_current_user')->willReturn($user);

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        // The '@' suppresses a session_regenerate_id() warning from complete_user_login(),
        // which only occurs because there is no real active PHP session in this test environment.
        $response = @$route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $moodleuser->id, (string) $storedrequest['userid']);
    }

    /**
     * do_login() never completes the authorization request itself, silently or otherwise, even
     * when the just authenticated user has already granted every scope this client is
     * requesting: it always redirects to authorize(), which is the only place that decision is
     * made (see test_authorize_completes_immediately_when_all_requested_scopes_already_granted()
     * and test_do_login_then_authorize_completes_silently_when_all_scopes_already_granted() for
     * that decision, and the full round trip, respectively).
     */
    public function test_do_login_does_not_complete_authorization_itself(): void {
        global $SESSION;

        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $moodleuser = $this->getDataGenerator()->create_user();
        $user = $this->make_user_entity($moodleuser->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user', 'get_current_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn($moodleuser);
        $userrepository->method('get_current_user')->willReturn($user);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        $response = @$route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        // The pending request is preserved (not discarded), unlike a genuine completion, so
        // authorize() can pick it up and make the actual completion decision.
        $this->assertArrayHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * The full round trip - do_login() followed by the authorize() redirect it produces -
     * completes the authorization request silently, without displaying the scope confirmation
     * screen, when the just authenticated user has already granted every scope this client is
     * requesting. Uses a real granted_scopes_repository (backed by the database), not a mock of
     * the value being checked, so this proves the two routes actually cooperate correctly, not
     * just that each one individually contains the right logic.
     */
    public function test_do_login_then_authorize_completes_silently_when_all_scopes_already_granted(): void {
        global $SESSION, $DB;

        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $moodleuser = $this->getDataGenerator()->create_user();

        // This scope has already been granted, by this exact user, to this exact client.
        $DB->insert_record('oauth2_server_client_granted_scopes', (object) [
            'clientidentifier' => $client->getIdentifier(),
            'userid' => $moodleuser->id,
            'scope' => 'moodle',
            'timecreated' => time(),
        ]);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user', 'get_current_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn($moodleuser);
        $userrepository->method('get_current_user')
            ->willReturn($this->make_user_entity($moodleuser->id));

        $expectedresponse = new Response(200, [], 'authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturnCallback(function (AuthorizationRequest $authrequest) use ($expectedresponse): ResponseInterface {
                $this->assertTrue($authrequest->isAuthorizationApproved());
                return $expectedresponse;
            });

        $route = $this->get_route($server, $clientrepository, $scoperepository);
        $grantedscopesrepository = new granted_scopes_repository($scoperepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        $dologinresponse = @$route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $dologinresponse->getStatusCode());
        $authorizerequestid = $this->get_requestid_from_response($dologinresponse);
        $this->assertEquals($requestid, $authorizerequestid);

        $authorizerequest = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $authorizerequestid]);
        $authorizeresponse = $route->authorize(
            $authorizerequest,
            new Response(),
            $userrepository,
            $grantedscopesrepository,
        );

        $this->assertSame($expectedresponse, $authorizeresponse);

        // No pending request left behind: the flow has genuinely completed.
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * do_login() redirects to authorize() with the same request id regardless of the
     * authenticated user's scope-grant state: that decision (silent completion vs. the consent
     * screen) is made entirely by authorize() (see test_authorize_preserves_interactive_flow_*()
     * and test_authorize_does_not_auto_approve_using_grant_for_different_user_or_client() for
     * that decision itself), not by do_login().
     */
    public function test_do_login_redirects_to_authorize_regardless_of_scope_grant_state(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session(
            $this->make_auth_request($client, scopes: ['moodle', 'email']),
        );

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $moodleuser = $this->getDataGenerator()->create_user();
        $user = $this->make_user_entity($moodleuser->id);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user', 'get_current_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn($moodleuser);
        $userrepository->method('get_current_user')->willReturn($user);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        $response = @$route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $user->getIdentifier(), (string) $storedrequest['userid']);
    }

    /**
     * authorize() requires a live session that satisfies Moodle's mandatory account-policy
     * requirements (via require_login()) before consent or silent completion can proceed. A
     * forced password change interrupts the flow, preserves the pending request, and points
     * $SESSION->wantsurl at the exact same /authorize?authrequestid=... URL so the flow can
     * resume once the requirement is resolved.
     *
     * require_login()'s redirect is observed here as the moodle_exception redirect() throws,
     * rather than a real HTTP redirect, because PHPUnit runs as CLI_SCRIPT, which redirect()
     * refuses to run under (see test_require_login_without_course() in
     * moodle_authentication_middleware_test.php for the same pattern against the equivalent
     * router middleware). $SESSION->wantsurl is still set by the time it does so.
     */
    public function test_authorize_redirects_for_forced_password_change_and_preserves_request(): void {
        global $SESSION;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        set_user_preference('auth_forcepasswordchange', 1, $moodleuser);
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        // In production, moodle_bootstrap_middleware sets $PAGE->url from the routed request's
        // URI before the route handler runs, which is what require_login()'s wantsurl mechanism
        // (via qualified_me()) relies on. Reproduce that here, since this test drives authorize()
        // directly rather than through the full router middleware stack.
        $this->set_page_url_for_authorize($requestid);

        $request = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->authorize($request, new Response(), $userrepository, $this->make_granted_scopes_repository_stub());
            $this->fail('Expected require_login() to attempt a redirect for the forced password change.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see this test's docblock.
        }

        $this->assertStringContainsString('/authorize', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        // The pending request itself is preserved (not discarded), so a subsequent visit (once
        // the password is changed) can still continue this same flow.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $moodleuser->id, (string) $storedrequest['userid']);
        $this->assertFalse($storedrequest['authorizationapproved']);
    }

    /**
     * authorize() redirects for an incomplete profile, exactly as it does for a forced password
     * change (see test_authorize_redirects_for_forced_password_change_and_preserves_request()),
     * preserving the pending request and pointing $SESSION->wantsurl at this same request.
     */
    public function test_authorize_redirects_for_incomplete_profile_and_preserves_request(): void {
        global $SESSION;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'house',
            'name' => 'House',
            'required' => 1,
            'visible' => 1,
            'locked' => 0,
            'datatype' => 'text',
        ]);

        // Created without profile_field_house, so this user is missing a required field.
        $moodleuser = $this->getDataGenerator()->create_user();
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        $this->set_page_url_for_authorize($requestid);

        $request = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->authorize($request, new Response(), $userrepository, $this->make_granted_scopes_repository_stub());
            $this->fail('Expected require_login() to attempt a redirect for the incomplete profile.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see test_authorize_redirects_for_forced_password_change_and_preserves_request().
        }

        $this->assertStringContainsString('/authorize', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $moodleuser->id, (string) $storedrequest['userid']);
        $this->assertFalse($storedrequest['authorizationapproved']);
    }

    /**
     * authorize() redirects when a site policy is defined and not yet agreed to, exactly as it
     * does for a forced password change (see
     * test_authorize_redirects_for_forced_password_change_and_preserves_request()), preserving
     * the pending request and pointing $SESSION->wantsurl at this same request.
     */
    public function test_authorize_redirects_for_site_policy_not_agreed_and_preserves_request(): void {
        global $SESSION;

        $this->resetAfterTest();

        set_config('sitepolicy', 'https://example.com/sitepolicy.html');

        // A freshly created user has not agreed to anything (policyagreed defaults to 0).
        $moodleuser = $this->getDataGenerator()->create_user();
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        $this->set_page_url_for_authorize($requestid);

        $request = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->authorize($request, new Response(), $userrepository, $this->make_granted_scopes_repository_stub());
            $this->fail('Expected require_login() to attempt a redirect for the unagreed site policy.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see test_authorize_redirects_for_forced_password_change_and_preserves_request().
        }

        $this->assertStringContainsString('/authorize', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $moodleuser->id, (string) $storedrequest['userid']);
        $this->assertFalse($storedrequest['authorizationapproved']);
    }

    /**
     * Once the account-policy requirement blocking authorize() is resolved (here, the forced
     * password change preference is cleared, exactly as login/change_password.php clears it on
     * success), resuming the same authrequestid completes the flow normally - proving the
     * "resume via $SESSION->wantsurl" story actually round-trips, not just that the initial
     * redirect looks right.
     */
    public function test_authorize_resumes_and_completes_after_forced_password_change_resolved(): void {
        global $SESSION;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        set_user_preference('auth_forcepasswordchange', 1, $moodleuser);
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $expectedresponse = new Response(200, [], 'authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturn($expectedresponse);

        $route = $this->get_route($server, $clientrepository);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        $this->set_page_url_for_authorize($requestid);

        $request = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->authorize($request, new Response(), $userrepository, $this->make_granted_scopes_repository_stub());
            $this->fail('Expected require_login() to attempt a redirect for the forced password change.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected on the first visit.
        }

        // Simulate login/change_password.php's own success behaviour: it clears the preference
        // (see unset_user_preference('auth_forcepasswordchange', ...) there) and returns the
        // browser to $SESSION->wantsurl, which require_login() has already pointed at this exact
        // authorize() URL.
        unset_user_preference('auth_forcepasswordchange', $moodleuser);
        $wantsurl = $SESSION->wantsurl;
        unset($SESSION->wantsurl);

        $resumedrequest = new ServerRequest('GET', $wantsurl);
        parse_str((string) parse_url($wantsurl, PHP_URL_QUERY), $resumedparams);
        $resumedrequest = $resumedrequest->withQueryParams($resumedparams);

        $this->assertEquals($requestid, $resumedparams['authrequestid']);

        $response = $route->authorize(
            $resumedrequest,
            new Response(),
            $userrepository,
            $this->make_granted_scopes_repository_stub(hasgrantedallscopes: true),
        );

        $this->assertSame($expectedresponse, $response);
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * do_login() redirects back to the login form when the supplied credentials are invalid, and
     * does not establish a Moodle session for the request's ambient session.
     */
    public function test_do_login_invalid_credentials(): void {
        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn(false);

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
        $this->assertFalse(isloggedin());
    }

    /**
     * A full failed-login redisplay cycle: do_login() with invalid credentials stashes a
     * one-use flash (error + submitted username) scoped to the request id, without disturbing
     * the pending OAuth authorization request; login() then consumes and displays that flash
     * exactly once.
     *
     * The submitted username is a plausible extended username (containing an '@' and mixed
     * case), rather than a plain alphanumeric one, so that the assertion on the redisplayed
     * value would fail if the route applied an inappropriate filter such as PARAM_USERNAME
     * (which lowercases and strips characters) instead of the PARAM_RAW cleaning also used by
     * the existing query-string username path. This test does not otherwise attempt to prove
     * clean_param()'s internal behaviour, which is covered by Moodle's own parameter tests.
     */
    public function test_do_login_invalid_credentials_shows_error_and_retains_username_once(): void {
        global $PAGE;

        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn(false);

        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $submittedusername = 'Bob@example.com';
        $dologinrequest = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => $submittedusername,
                'password' => 'wrong',
            ]);
        $response = $route->do_login(
            $dologinrequest,
            new Response(),
            $userrepository,
        );

        $this->assertEquals(302, $response->getStatusCode());
        $location = $response->getHeaderLine('Location');
        $this->assertStringContainsString('/login', $location);
        $this->assertStringNotContainsString('/approve', $location);

        // Neither the submitted username nor the error message may leak into the redirect URL.
        $this->assertStringNotContainsString('Bob', $location);
        $this->assertStringNotContainsString('wrong', $location);

        // The pending OAuth authorization request must survive the failed attempt.
        $this->assertNotNull($this->get_auth_request_from_session($requestid));

        // Feed the redirect straight back into login(), as the browser would.
        parse_str((string) parse_url($location, PHP_URL_QUERY), $redirectparams);
        $loginrequest = (new ServerRequest('GET', '/login'))->withQueryParams($redirectparams);

        $route->login($loginrequest, new Response());

        $this->assertInstanceOf(login::class, $capturedcontent);
        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals(get_string('logininvalidlogintitle'), $data->errortitle);
        $this->assertEquals(get_string('logininvalidlogindetail'), $data->error);
        // Retained exactly as submitted: same case, and the '@' preserved.
        $this->assertEquals($submittedusername, $data->username);

        // A second render for the same request id must no longer show the one-use error.
        $capturedcontent = null;
        $route->login($loginrequest, new Response());

        $this->assertInstanceOf(login::class, $capturedcontent);
        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertEmpty($data->errortitle);
        $this->assertEmpty($data->error);
    }

    /**
     * do_login() rejects valid credentials for an unconfirmed account before ever establishing a
     * Moodle session, matching login/index.php's own ordering (confirmation is checked before
     * complete_user_login() is called). Uses the real user_repository and real
     * authenticate_user_login(), rather than mocking the confirmation property being tested, so
     * this also proves authenticate_user_login() itself does not already reject unconfirmed
     * users (confirmed here, since the OAuth route's own confirmation check is the only thing
     * standing between valid credentials and a live session).
     */
    public function test_do_login_unconfirmed_account_does_not_establish_session(): void {
        global $DB;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user([
            'username' => 'bob',
            'password' => 'password1',
            'confirmed' => 0,
        ]);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        // Never reached: proof that the flow cannot be silently completed for an unconfirmed
        // account, however scopes happen to be configured.
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $logintoken = \core\session\manager::get_login_token();

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => $logintoken,
            ]);
        $response = $route->do_login($request, new Response(), new user_repository());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));

        // No Moodle session was established: authenticate_user_login() returned the full
        // (unconfirmed) user record, but complete_user_login() was never reached.
        $this->assertFalse(isloggedin());

        // The pending request is preserved (not discarded), with no user attached to it, so a
        // subsequent valid login (once the account is confirmed) can still continue this same
        // flow.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertNotNull($storedrequest);
        $this->assertNull($storedrequest['userid']);
        $this->assertFalse($storedrequest['authorizationapproved']);

        // No scopes were stored for this user against this client.
        $this->assertFalse($DB->record_exists('oauth2_server_client_granted_scopes', ['userid' => $moodleuser->id]));
    }

    /**
     * A full unconfirmed-account redisplay cycle: do_login() with valid credentials for an
     * unconfirmed account stashes a one-use flash distinguishing this from invalid credentials,
     * without disturbing the pending OAuth authorization request; login() then consumes and
     * displays the confirmation-required message - not the generic invalid-login one - exactly
     * once.
     */
    public function test_do_login_unconfirmed_account_shows_confirmation_message_once(): void {
        global $PAGE;

        $this->resetAfterTest();

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $moodleuser = $this->getDataGenerator()->create_user(['confirmed' => 0]);
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn($moodleuser);

        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);

        $capturedcontent = null;
        $route->method('render_page_from_renderable')
            ->willReturnCallback(function ($content, ResponseInterface $response) use (&$capturedcontent): ResponseInterface {
                $capturedcontent = $content;
                return $response;
            });

        $submittedusername = 'unconfirmeduser';
        $dologinrequest = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => $submittedusername,
                'password' => 'whatever',
            ]);
        $response = $route->do_login($dologinrequest, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $location = $response->getHeaderLine('Location');
        $this->assertStringContainsString('/login', $location);
        $this->assertStringNotContainsString('/approve', $location);
        $this->assertFalse(isloggedin());

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertNotNull($storedrequest);
        $this->assertNull($storedrequest['userid']);

        // Feed the redirect straight back into login(), as the browser would.
        parse_str((string) parse_url($location, PHP_URL_QUERY), $redirectparams);
        $loginrequest = (new ServerRequest('GET', '/login'))->withQueryParams($redirectparams);

        $route->login($loginrequest, new Response());

        $this->assertInstanceOf(login::class, $capturedcontent);
        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertEquals(get_string('confirmednot'), $data->error);
        // Not the generic invalid-login message.
        $this->assertEmpty($data->errortitle);
        $this->assertNotEquals(get_string('logininvalidlogindetail'), $data->error);
        // Retained exactly as submitted.
        $this->assertEquals($submittedusername, $data->username);

        // A second render for the same request id must no longer show the one-use message.
        $capturedcontent = null;
        $route->login($loginrequest, new Response());

        $this->assertInstanceOf(login::class, $capturedcontent);
        $data = $capturedcontent->export_for_template($PAGE->get_renderer('core'));
        $this->assertEmpty($data->errortitle);
        $this->assertEmpty($data->error);
    }

    /**
     * After the account is marked confirmed, retrying with the same credentials against the same
     * pending request establishes the session and proceeds through the central authorize() gate
     * normally - the user does not need to do anything OAuth-specific to resume; they simply log
     * in again, exactly as they would be expected to on the standalone login/index.php flow after
     * confirming their account.
     */
    public function test_do_login_can_retry_after_account_confirmed(): void {
        global $DB;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user([
            'username' => 'bob',
            'password' => 'password1',
            'confirmed' => 0,
        ]);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $logintoken = \core\session\manager::get_login_token();
        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => $logintoken,
            ]);

        // First attempt: valid credentials, but the account is not yet confirmed - rejected.
        $response = $route->do_login($request, new Response(), new user_repository());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertFalse(isloggedin());

        // Confirm the account, exactly as clicking the link in the confirmation email would.
        $DB->set_field('user', 'confirmed', 1, ['id' => $moodleuser->id]);

        // Retry with the same credentials, against the same pending request.
        $logintoken = \core\session\manager::get_login_token();
        $retryrequest = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => $logintoken,
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        $response = @$route->do_login($retryrequest, new Response(), new user_repository());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));
        $this->assertTrue(isloggedin());
        $this->assertEquals($requestid, $this->get_requestid_from_response($response));

        // The flow now proceeds through the central authorize() gate normally.
        $this->set_page_url_for_authorize($requestid);
        $authorizeresponse = $route->authorize(
            (new ServerRequest('GET', '/authorize'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
            new user_repository(),
            $this->make_granted_scopes_repository_stub(),
        );

        $this->assertEquals(302, $authorizeresponse->getStatusCode());
        $this->assertStringContainsString('/approve', $authorizeresponse->getHeaderLine('Location'));
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
            ->onlyMethods(['authenticate_user'])
            ->getMock();
        $userrepository->expects($this->never())->method('authenticate_user');

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([]);
        $response = $route->do_login($request, new Response(), $userrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * do_login() authenticates a user supplying valid username/password credentials together
     * with a valid Moodle login token (the same CSRF-style token mechanism used to guard the
     * standard login/index.php form), and, on success, establishes a full, persistent Moodle
     * session for that user (via complete_user_login()) rather than only accepting the
     * credentials for the OAuth2 flow.
     *
     * This is exercised through the real user_repository (rather than a mock of it), so that the
     * login token is actually validated by authenticate_user_login(), not merely assumed to have
     * been forwarded correctly, and against a real generated Moodle user, so that the resulting
     * Moodle session is genuine rather than merely asserted to have been requested.
     */
    public function test_do_login_valid_credentials_and_valid_logintoken_authenticates(): void {
        global $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user([
            'username' => 'bob',
            'password' => 'password1',
        ]);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $logintoken = \core\session\manager::get_login_token();

        $this->assertFalse(isloggedin());

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => $logintoken,
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        $response = @$route->do_login(
            $request,
            new Response(),
            new user_repository(),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $user->id, (string) $storedrequest['userid']);

        // A real, persistent Moodle session has been established for the authenticated user, not
        // merely a League user entity accepted for the OAuth2 flow.
        $this->assertTrue(isloggedin());
        $this->assertEquals($user->id, $USER->id);
    }

    /**
     * do_login() rejects otherwise-valid username/password credentials when the submitted
     * Moodle login token does not match the one issued for the current session, exactly as the
     * standard login form's submission is validated, rather than authenticating on
     * username/password alone.
     */
    public function test_do_login_valid_credentials_with_invalid_logintoken_does_not_authenticate(): void {
        $this->resetAfterTest();

        // Rejected login-token attempts are logged with the requesting user agent; supply one so
        // that this does not trigger an unrelated PHP warning for a missing array key.
        $_SERVER['HTTP_USER_AGENT'] = 'no browser';

        $this->getDataGenerator()->create_user([
            'username' => 'bob',
            'password' => 'password1',
        ]);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        // Ensure a real login token exists for the session, so that this is genuinely a mismatch
        // rather than there being no token to compare against.
        \core\session\manager::get_login_token();

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => 'not-the-real-token',
            ]);

        $response = $route->do_login($request, new Response(), new user_repository());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));

        // No Moodle session was established for the rejected credentials.
        $this->assertFalse(isloggedin());
    }

    /**
     * do_login() does not treat a missing 'logintoken' field in the POST body as an implicit
     * bypass of login token validation. authenticate_user_login() only skips the login token
     * check when it is passed the boolean `false`; do_login() must not default a missing
     * 'logintoken' to something that is treated the same way (e.g. `false` itself), or an
     * attacker could authenticate with valid credentials simply by omitting the field.
     */
    public function test_do_login_valid_credentials_missing_logintoken_does_not_bypass_validation(): void {
        $this->resetAfterTest();

        // Rejected login-token attempts are logged with the requesting user agent; supply one so
        // that this does not trigger an unrelated PHP warning for a missing array key.
        $_SERVER['HTTP_USER_AGENT'] = 'no browser';

        $this->getDataGenerator()->create_user([
            'username' => 'bob',
            'password' => 'password1',
        ]);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                // No 'logintoken' key at all.
            ]);
        $response = $route->do_login($request, new Response(), new user_repository());

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
        $this->assertStringNotContainsString('/approve', $response->getHeaderLine('Location'));

        // No Moodle session was established for the rejected credentials.
        $this->assertFalse(isloggedin());
    }

    /**
     * Create a manual-auth user ('bob'/'password1'), with password expiration configured on
     * auth_manual, and their own password last updated far enough in the past that
     * $daystoexpire days remain until it is next due to expire (negative for already
     * hard-expired). Uses auth_manual's own real password_expire() implementation (via its
     * config and the auth_manual_passwordupdatetime user preference), rather than mocking the
     * expiry calculation being tested.
     *
     * @param int $daystoexpire
     * @param int $expirationtime Days configured on auth_manual before a password is due to expire.
     * @return \stdClass
     */
    protected function create_manual_user_with_password_expiry(int $daystoexpire, int $expirationtime = 30): \stdClass {
        set_config('expiration', 1, 'auth_manual');
        set_config('expirationtime', $expirationtime, 'auth_manual');

        $moodleuser = $this->getDataGenerator()->create_user([
            'auth' => 'manual',
            'username' => 'bob',
            'password' => 'password1',
        ]);
        set_user_preference(
            'auth_manual_passwordupdatetime',
            time() - ($expirationtime - $daystoexpire) * DAYSECS,
            $moodleuser,
        );

        return $moodleuser;
    }

    /**
     * Submit a do_login() POST for the given pending request id, using the real user_repository
     * (so credentials are genuinely validated), and a real login token.
     *
     * @param oauth2 $route
     * @param string $requestid
     * @param string $username
     * @param string $password
     * @return ResponseInterface
     */
    protected function submit_do_login_credentials(
        oauth2 $route,
        string $requestid,
        string $username = 'bob',
        string $password = 'password1',
    ): ResponseInterface {
        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => $username,
                'password' => $password,
                'logintoken' => \core\session\manager::get_login_token(),
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        return @$route->do_login($request, new Response(), new user_repository());
    }

    /**
     * do_login() proceeds normally (establishing a session and redirecting to authorize(),
     * without setting the forced-password-change preference) when password expiration is not
     * enabled on the user's authentication plugin at all - even though, were it enabled, this
     * same password would already be hard-expired.
     */
    public function test_do_login_proceeds_normally_when_expiration_disabled(): void {
        $this->resetAfterTest();

        set_config('expiration', 0, 'auth_manual');
        $moodleuser = $this->getDataGenerator()->create_user([
            'auth' => 'manual',
            'username' => 'bob',
            'password' => 'password1',
        ]);
        set_user_preference('auth_manual_passwordupdatetime', time() - 60 * DAYSECS, $moodleuser);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $response = $this->submit_do_login_credentials($route, $requestid);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));
        $this->assertTrue(isloggedin());
        $this->assertEquals(0, (int) get_user_preferences('auth_forcepasswordchange', 0, $moodleuser->id));
    }

    /**
     * do_login() proceeds normally when the password is not yet due to expire.
     */
    public function test_do_login_proceeds_normally_when_not_expired(): void {
        $this->resetAfterTest();

        $moodleuser = $this->create_manual_user_with_password_expiry(daystoexpire: 25);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $response = $this->submit_do_login_credentials($route, $requestid);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));
        $this->assertTrue(isloggedin());
        $this->assertEquals(0, (int) get_user_preferences('auth_forcepasswordchange', 0, $moodleuser->id));
    }

    /**
     * do_login() proceeds normally, without blocking, while a password is only within the
     * non-blocking warning period (not yet hard-expired) - the warning notice itself is out of
     * scope for this route.
     */
    public function test_do_login_proceeds_normally_during_warning_period(): void {
        $this->resetAfterTest();

        set_config('expiration_warning', 10, 'auth_manual');
        $moodleuser = $this->create_manual_user_with_password_expiry(daystoexpire: 5);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $route = $this->get_route(clientrepository: $clientrepository);

        $response = $this->submit_do_login_credentials($route, $requestid);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));
        $this->assertTrue(isloggedin());
        $this->assertEquals(0, (int) get_user_preferences('auth_forcepasswordchange', 0, $moodleuser->id));
    }

    /**
     * A full internal hard-expiry round trip: do_login() detects the hard expiry on a plugin
     * with no external change-password destination (auth_manual), sets the same
     * auth_forcepasswordchange preference login/index.php would for this same situation, and
     * redirects to authorize() without completing anything. authorize()'s own,
     * already-integrated require_login() gate (see
     * test_authorize_redirects_for_forced_password_change_and_preserves_request()) then
     * redirects for the password change and preserves the pending request, pointing
     * $SESSION->wantsurl at this exact /authorize?authrequestid=... URL; once the preference is
     * cleared (as login/change_password.php does on success) and the flow resumes via
     * $SESSION->wantsurl, it completes normally (see
     * test_authorize_resumes_and_completes_after_forced_password_change_resolved()).
     */
    public function test_do_login_hard_expiry_internal_forces_password_change_and_resumes(): void {
        global $SESSION, $DB;

        $this->resetAfterTest();

        $moodleuser = $this->create_manual_user_with_password_expiry(daystoexpire: -5);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $expectedresponse = new Response(200, [], 'authorized');
        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->once())
            ->method('completeAuthorizationRequest')
            ->willReturn($expectedresponse);

        $route = $this->get_route($server, $clientrepository);

        $response = $this->submit_do_login_credentials($route, $requestid);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/authorize', $response->getHeaderLine('Location'));

        // A session was established, but the forced-password-change preference is now set.
        $this->assertTrue(isloggedin());
        $this->assertEquals(1, (int) get_user_preferences('auth_forcepasswordchange', 0, $moodleuser->id));

        // The pending request survives, with this user attached, but is not yet approved.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $moodleuser->id, (string) $storedrequest['userid']);
        $this->assertFalse($storedrequest['authorizationapproved']);

        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['get_current_user'])
            ->getMock();
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        $this->set_page_url_for_authorize($requestid);

        $authorizerequest = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->authorize($authorizerequest, new Response(), $userrepository, $this->make_granted_scopes_repository_stub());
            $this->fail('Expected require_login() to attempt a redirect for the forced password change.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see test_authorize_redirects_for_forced_password_change_and_preserves_request().
        }

        $this->assertStringContainsString('/authorize', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        // Authorization has not completed: no scopes were stored for this user against this client.
        $this->assertFalse($DB->record_exists('oauth2_server_client_granted_scopes', ['userid' => $moodleuser->id]));

        // Simulate login/change_password.php's own success behaviour: it clears the preference
        // and returns the browser to $SESSION->wantsurl, which require_login() has already
        // pointed at this exact authorize() URL.
        unset_user_preference('auth_forcepasswordchange', $moodleuser);
        $wantsurl = $SESSION->wantsurl;
        unset($SESSION->wantsurl);

        $resumedrequest = new ServerRequest('GET', $wantsurl);
        parse_str((string) parse_url($wantsurl, PHP_URL_QUERY), $resumedparams);
        $resumedrequest = $resumedrequest->withQueryParams($resumedparams);
        $this->assertEquals($requestid, $resumedparams['authrequestid']);

        $resumedresponse = $route->authorize(
            $resumedrequest,
            new Response(),
            $userrepository,
            $this->make_granted_scopes_repository_stub(hasgrantedallscopes: true),
        );

        $this->assertSame($expectedresponse, $resumedresponse);
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);
    }

    /**
     * do_login() terminates the pending request outright, without ever establishing a session,
     * when the authentication plugin's password-expiry state is hard-expired with an external
     * change-password destination - directing the user to that exact, unmodified destination.
     * Uses a mocked password_expiry_checker (substituted via the DI container, the same
     * mechanism other tests in this codebase use for test doubles - see authlib_test.php), since
     * no bundled authentication plugin both supports real, testable expiry configuration and
     * exposes an external change_password_url() at the same time. Real page rendering (and so
     * the actual wording shown, which reuses get_string('oauth2:passwordexpiredexternal',
     * 'moodle') - see render_external_password_expiry_page()) is out of scope here, exactly as
     * for every other page this route renders (see get_route_with_stubbed_rendering()).
     */
    public function test_do_login_hard_expiry_external_terminates_request(): void {
        global $SESSION, $DB;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['username' => 'bob', 'password' => 'password1']);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $externalurl = new \core\url('https://example.com/change-password');
        $checker = $this->createMock(password_expiry_checker::class);
        $checker->method('check')->willReturn(password_expiry_check::expired_external($externalurl));
        \core\di::set(password_expiry_checker::class, $checker);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        // Real page rendering is exercised separately by login_test.php (see
        // get_route_with_stubbed_rendering()); stub it here so this test can focus on the
        // wiring - which client and destination URL reach it, not how they are drawn.
        $route = $this->get_route_with_stubbed_rendering(server: $server, clientrepository: $clientrepository);

        $capturedclient = null;
        $capturedurl = null;
        $capture = function (
            ResponseInterface $response,
            $client,
            \core\url $url,
        ) use (&$capturedclient, &$capturedurl): ResponseInterface {
            $capturedclient = $client;
            $capturedurl = $url;
            return $response;
        };
        $route->method('render_external_password_expiry_page')->willReturnCallback($capture);

        $response = $this->submit_do_login_credentials($route, $requestid);

        $this->assertEquals(200, $response->getStatusCode());
        // User directed to the plugin's own external URL, unmodified - no return URL appended.
        $this->assertSame($client->getIdentifier(), $capturedclient->getIdentifier());
        $this->assertTrue($capturedurl->compare($externalurl, URL_MATCH_BASE));
        $this->assertEmpty($capturedurl->params());

        // No session was established at all, so nothing here could go on to authorize the client.
        $this->assertFalse(isloggedin());

        // The pending request is discarded outright, not preserved for a later retry.
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);

        // Authorization was not completed: no scopes were stored for this user against this client.
        $this->assertFalse($DB->record_exists('oauth2_server_client_granted_scopes', ['userid' => $moodleuser->id]));
    }

    /**
     * do_login()'s external-hard-expiry termination clears $SESSION->wantsurl when it still
     * holds this same request's own OAuth URL (as login() would have stored it), exactly as
     * forget_auth_request() already does for a genuine completion (see
     * test_do_approve_when_approved_clears_matching_oauth_wantsurl()).
     */
    public function test_do_login_hard_expiry_external_clears_matching_oauth_wantsurl(): void {
        global $SESSION;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_user(['username' => 'bob', 'password' => 'password1']);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $checker = $this->createMock(password_expiry_checker::class);
        $checker->method('check')->willReturn(
            password_expiry_check::expired_external(new \core\url('https://example.com/change-password')),
        );
        \core\di::set(password_expiry_checker::class, $checker);

        // See test_do_login_hard_expiry_external_terminates_request() for why rendering is
        // stubbed here.
        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);
        $route->method('render_page_from_renderable')
            ->willReturnCallback(fn ($content, ResponseInterface $response): ResponseInterface => $response);
        $route->method('render_external_password_expiry_page')
            ->willReturnCallback(fn (ResponseInterface $response): ResponseInterface => $response);

        // Matches what login() would have stored for this exact pending request.
        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );
        $this->assertTrue(isset($SESSION->wantsurl));

        $this->submit_do_login_credentials($route, $requestid);

        $this->assertFalse(isset($SESSION->wantsurl));
    }

    /**
     * do_login()'s external-hard-expiry termination leaves $SESSION->wantsurl untouched when it
     * holds an unrelated, newer destination - for example, another in-progress flow's own - not
     * one of this request's own OAuth URLs.
     */
    public function test_do_login_hard_expiry_external_leaves_unrelated_wantsurl(): void {
        global $SESSION;

        $this->resetAfterTest();

        $this->getDataGenerator()->create_user(['username' => 'bob', 'password' => 'password1']);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $checker = $this->createMock(password_expiry_checker::class);
        $checker->method('check')->willReturn(
            password_expiry_check::expired_external(new \core\url('https://example.com/change-password')),
        );
        \core\di::set(password_expiry_checker::class, $checker);

        // See test_do_login_hard_expiry_external_terminates_request() for why rendering is
        // stubbed here.
        $route = $this->get_route_with_stubbed_rendering(clientrepository: $clientrepository);
        $route->method('render_external_password_expiry_page')
            ->willReturnCallback(fn (ResponseInterface $response): ResponseInterface => $response);

        $unrelatedwantsurl = new \core\url('/course/view.php', ['id' => 2]);
        $SESSION->wantsurl = $unrelatedwantsurl;

        $this->submit_do_login_credentials($route, $requestid);

        $this->assertTrue(isset($SESSION->wantsurl));
        $this->assertTrue($SESSION->wantsurl->compare($unrelatedwantsurl, URL_MATCH_BASE));
    }

    /**
     * do_login() fails closed - without establishing a session, and discarding the pending
     * request outright rather than preserving it - when the authentication plugin's password is
     * hard-expired but the plugin cannot change it at all, internally or externally. Reuses the
     * same error message Moodle's own require_login() would show for this exact plugin
     * configuration elsewhere in this flow (see authlib.php's can_change_password()), so nothing
     * about the plugin's internal state is exposed beyond that established, generic message.
     */
    public function test_do_login_hard_expiry_unsupported_fails_closed_and_cleans_up_request(): void {
        global $SESSION, $DB;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['username' => 'bob', 'password' => 'password1']);

        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client, scopes: ['moodle']));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $checker = $this->createMock(password_expiry_checker::class);
        $checker->method('check')->willReturn(password_expiry_check::expired_unsupported());
        \core\di::set(password_expiry_checker::class, $checker);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $route = $this->get_route($server, $clientrepository);

        $request = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'password1',
                'logintoken' => \core\session\manager::get_login_token(),
            ]);

        try {
            $route->do_login($request, new Response(), new user_repository());
            $this->fail('Expected an unsupported password-expiry configuration to fail closed.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            $this->assertEquals(get_string('nopasswordchangeforced', 'auth'), $e->getMessage());
        }

        // No session was established at all.
        $this->assertFalse(isloggedin());

        // The pending request is discarded outright, not preserved for a later retry.
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);

        // Authorization was not completed: no scopes were stored for this user against this client.
        $this->assertFalse($DB->record_exists('oauth2_server_client_granted_scopes', ['userid' => $moodleuser->id]));
    }

    /**
     * approve() renders a confirm_scopes_page with the wiring provided by the route, without
     * asserting on the (as yet unimplemented) scope-diffing behaviour.
     */
    public function test_approve_renders_confirm_scopes_page(): void {
        $this->resetAfterTest();

        // The route now requires the live session to be logged in as the exact user recorded on
        // the pending request, so a real user is both stored on the request and set as the
        // current session user here.
        $moodleuser = $this->getDataGenerator()->create_user();
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($moodleuser->id));
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
                'client_id' => $client->getIdentifier(),
                'authrequestid' => $requestid,
            ]),
            new Response(),
            $scoperepository,
            $grantedscopesrepository,
        );

        $this->assertInstanceOf(confirm_scopes_page::class, $capturedcontent);
    }

    /**
     * approve()'s defensive require_login() gate blocks display of the consent screen for a
     * forced password change, even though session_matches_authrequest_user() passes (a real,
     * correctly-identified, live session for exactly the user this request was authenticated
     * for) - this can happen if the requirement arose after the flow first reached authorize(),
     * e.g. an admin force-flagging a password change while the user still has the consent screen
     * open in their browser.
     */
    public function test_approve_defensive_gate_blocks_for_forced_password_change(): void {
        global $SESSION;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        set_user_preference('auth_forcepasswordchange', 1, $moodleuser);
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($this->make_user_entity($moodleuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('get_granted_scopes_for_user');

        $route = $this->get_route(clientrepository: $clientrepository, scoperepository: $scoperepository);

        $this->set_page_url_for_approve($requestid);

        $request = (new ServerRequest('GET', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid]);

        try {
            $route->approve($request, new Response(), $scoperepository, $grantedscopesrepository);
            $this->fail('Expected require_login() to attempt a redirect for the forced password change.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see test_authorize_redirects_for_forced_password_change_and_preserves_request().
        }

        $this->assertStringContainsString('/approve', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertFalse($storedrequest['authorizationapproved']);
    }

    /**
     * do_approve()'s defensive require_login() gate blocks a forced-password-change session from
     * approving (or denying) the request - it must never store granted scopes, mark the request
     * approved, or complete it - even though session_matches_authrequest_user() and the sesskey
     * check both pass.
     */
    public function test_do_approve_defensive_gate_blocks_for_forced_password_change(): void {
        global $SESSION;

        $this->resetAfterTest();

        $moodleuser = $this->getDataGenerator()->create_user(['auth' => 'manual']);
        set_user_preference('auth_forcepasswordchange', 1, $moodleuser);
        // Calling setUser() re-initialises $SESSION as a side effect, so it must run before the
        // pending request is stored below, not after.
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($this->make_user_entity($moodleuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $route = $this->get_route($server, $clientrepository);

        $this->set_page_url_for_approve($requestid);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
            ]);

        try {
            $route->do_approve($request, new Response(), $grantedscopesrepository);
            $this->fail('Expected require_login() to attempt a redirect for the forced password change.');
        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
        } catch (\core\exception\moodle_exception $e) {
            // Expected: see test_authorize_redirects_for_forced_password_change_and_preserves_request().
        }

        $this->assertStringContainsString('/approve', (string) ($SESSION->wantsurl ?? ''));
        $this->assertStringContainsString($requestid, (string) ($SESSION->wantsurl ?? ''));

        // Never approved, never completed: the requirement was not resolved.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertFalse($storedrequest['authorizationapproved']);
    }

    /**
     * do_approve() stores the selected scopes and marks the request as approved when the user
     * approves the request.
     *
     * The granted scopes must be derived solely from the scopes recorded against the stored
     * AuthorizationRequest ('moodle' here), never from the submitted POST body: the approval
     * form does not submit a 'scopes' field at all, and this test's POST body carries a crafted
     * 'scopes' value naming scopes never requested by, or granted to, this authorization request
     * (a client attempting to widen its own grant). That crafted value must be ignored entirely,
     * rather than being passed through to store_granted_scopes_for_user() as-is.
     *
     * This also covers that completing a flow discards any pending invalid-login flash left
     * over from an earlier failed attempt against the same request id (e.g. the user got their
     * password wrong once before eventually logging in and approving), alongside the pending
     * authorization request itself, so it cannot linger in the session.
     */
    public function test_do_approve_when_approved(): void {
        $this->resetAfterTest();

        // The route now requires the live session to be logged in as the exact user recorded
        // on the pending request, so a real user is both stored on the request and set as the
        // current session user here.
        $moodleuser = $this->getDataGenerator()->create_user();
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $user = $this->make_user_entity($moodleuser->id);
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($user);
        $requestid = $this->store_auth_request_in_session($authrequest);

        // Simulate a login-error flash left over from an earlier failed attempt in this same
        // flow, as do_login() would stash via store_login_error().
        global $SESSION;
        $SESSION->oauth2loginerrors[$requestid] = [
            'username' => 'bob',
        ];

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        // Needed so that restore_auth_request() can rebuild the 'moodle' scope from the plain
        // session data when do_approve() re-fetches the pending request.
        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

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

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
                // A crafted/malicious 'scopes' value, entirely different from (and broader
                // than) the 'moodle' scope actually recorded against the stored authorization
                // request, must be ignored.
                'scopes' => ['siteadmin', 'unauthorized_scope'],
            ]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertSame($expectedresponse, $response);

        // The pending request must be discarded once the flow has completed, so that it cannot
        // be replayed (e.g. by resubmitting the approval form).
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2requests ?? []);

        // Any leftover login-error flash for this request id must be discarded alongside it.
        $this->assertArrayNotHasKey($requestid, $SESSION->oauth2loginerrors ?? []);
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

        // The route now requires the live session to be logged in as the exact user recorded
        // on the pending request, so a real user is both stored on the request and set as the
        // current session user here.
        $moodleuser = $this->getDataGenerator()->create_user();
        $this->setUser($moodleuser);

        $client = $this->make_client_entity();
        $user = $this->make_user_entity($moodleuser->id);
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

    /**
     * approve() redirects back to the login page, instead of rendering the consent screen, when
     * there is no live logged-in session at all.
     */
    public function test_approve_redirects_to_login_when_logged_out(): void {
        $this->resetAfterTest();

        $targetuser = $this->getDataGenerator()->create_user();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);

        $route = $this->get_route(clientrepository: $clientrepository, scoperepository: $scoperepository);

        $response = $route->approve(
            (new ServerRequest('GET', '/approve'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
            $scoperepository,
            $this->make_granted_scopes_repository_stub(),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));

        // The pending request must survive, unmodified, so a subsequent valid login can still
        // continue this same flow.
        $requestidfromresponse = $this->get_requestid_from_response($response);
        $this->assertEquals($requestid, $requestidfromresponse);
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $targetuser->id, (string) $storedrequest['userid']);
    }

    /**
     * approve() redirects back to the login page, instead of rendering the consent screen, when
     * the live session is the guest user.
     */
    public function test_approve_redirects_to_login_when_guest(): void {
        $this->resetAfterTest();
        $this->setGuestUser();

        $targetuser = $this->getDataGenerator()->create_user();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);

        $route = $this->get_route(clientrepository: $clientrepository, scoperepository: $scoperepository);

        $response = $route->approve(
            (new ServerRequest('GET', '/approve'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
            $scoperepository,
            $this->make_granted_scopes_repository_stub(),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * approve() redirects back to the login page, instead of rendering another user's consent
     * screen, when the live session is logged in as a real, different user than the one recorded
     * on the pending request.
     */
    public function test_approve_redirects_to_login_for_different_logged_in_user(): void {
        $this->resetAfterTest();

        $targetuser = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);

        $route = $this->get_route(clientrepository: $clientrepository, scoperepository: $scoperepository);

        $response = $route->approve(
            (new ServerRequest('GET', '/approve'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
            $scoperepository,
            $this->make_granted_scopes_repository_stub(),
        );

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));

        // The pending request must still record the original target user: it must not be
        // silently replaced with the different, currently logged-in user.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertEquals((string) $targetuser->id, (string) $storedrequest['userid']);
    }

    /**
     * do_approve() redirects back to the login page - without storing any granted scopes or
     * completing the authorization request - when there is no live logged-in session at all,
     * even though a valid sesskey is presented.
     */
    public function test_do_approve_redirects_to_login_when_logged_out(): void {
        $this->resetAfterTest();

        $targetuser = $this->getDataGenerator()->create_user();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
            ]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));

        // The pending request must survive, unmodified and unapproved, so a subsequent valid
        // login can still continue this same flow.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertFalse($storedrequest['authorizationapproved']);
        $this->assertEquals((string) $targetuser->id, (string) $storedrequest['userid']);
    }

    /**
     * do_approve() redirects back to the login page - without storing any granted scopes or
     * completing the authorization request - when the live session is the guest user, even
     * though a valid sesskey is presented.
     */
    public function test_do_approve_redirects_to_login_when_guest(): void {
        $this->resetAfterTest();
        $this->setGuestUser();

        $targetuser = $this->getDataGenerator()->create_user();

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
            ]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));
    }

    /**
     * do_approve() redirects back to the login page - without storing any granted scopes or
     * completing the authorization request - when the live session is logged in as a real,
     * different user than the one recorded on the pending request, even though a valid sesskey
     * is presented (sesskey proves the submission was not forged; it does not prove the session
     * belongs to the stored user).
     */
    public function test_do_approve_redirects_to_login_for_different_logged_in_user(): void {
        $this->resetAfterTest();

        $targetuser = $this->getDataGenerator()->create_user();
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $client = $this->make_client_entity();
        $authrequest = $this->make_auth_request($client, scopes: ['moodle']);
        $authrequest->setUser($this->make_user_entity($targetuser->id));
        $requestid = $this->store_auth_request_in_session($authrequest);

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $scoperepository = $this->createStub(ScopeRepositoryInterface::class);
        $scoperepository->method('getScopeEntityByIdentifier')
            ->willReturnCallback(fn (string $identifier): ScopeEntityInterface => $this->make_scope_entity($identifier));

        $server = $this->createMock(AuthorizationServer::class);
        $server->expects($this->never())->method('completeAuthorizationRequest');

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();
        $grantedscopesrepository->expects($this->never())->method('store_granted_scopes_for_user');

        $route = $this->get_route($server, $clientrepository, $scoperepository);

        $request = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => '1',
            ]);
        $response = $route->do_approve($request, new Response(), $grantedscopesrepository);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->getHeaderLine('Location'));

        // The pending request must still record the original target user: it must not be
        // silently approved for the different, currently logged-in user.
        $storedrequest = $this->get_auth_request_from_session($requestid);
        $this->assertFalse($storedrequest['authorizationapproved']);
        $this->assertEquals((string) $targetuser->id, (string) $storedrequest['userid']);
    }

    /**
     * Build an oauth2 route, with a pending request already stored under a fresh request id,
     * ready to drive through login()/do_login()/do_approve() in a test.
     *
     * @return array{0: oauth2, 1: string} The route, and the request id of the pending request.
     */
    protected function get_route_with_pending_request_for_wantsurl_tests(): array {
        $client = $this->make_client_entity();
        $requestid = $this->store_auth_request_in_session($this->make_auth_request($client));

        $clientrepository = $this->createStub(ClientRepositoryInterface::class);
        $clientrepository->method('getClientEntity')->willReturn($client);

        $server = $this->createMock(AuthorizationServer::class);
        $server->method('completeAuthorizationRequest')->willReturn(new Response(200, [], 'authorized'));

        $route = $this->get_route_with_stubbed_rendering(server: $server, clientrepository: $clientrepository);
        $route->method('render_page_from_renderable')
            ->willReturnCallback(fn ($content, ResponseInterface $response): ResponseInterface => $response);

        return [$route, $requestid];
    }

    /**
     * Submit valid credentials for the given pending request id, and then submit the approval
     * form for it, entirely through the route's public methods.
     *
     * @param oauth2 $route A route already constructed with a client repository that resolves
     *      the request's client, and a server that can complete the request.
     * @param string $requestid
     * @param bool $approved Whether the approval form is submitted as approved or denied.
     */
    protected function submit_valid_login_and_approve(oauth2 $route, string $requestid, bool $approved): void {
        $moodleuser = $this->getDataGenerator()->create_user();
        $userrepository = $this->getMockBuilder(user_repository::class)
            ->onlyMethods(['authenticate_user', 'get_current_user'])
            ->getMock();
        $userrepository->method('authenticate_user')->willReturn($moodleuser);
        $userrepository->method('get_current_user')->willReturn($this->make_user_entity($moodleuser->id));

        $dologinrequest = (new ServerRequest('POST', '/login'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'username' => 'bob',
                'password' => 'secret',
            ]);
        // See test_do_login_valid_credentials() for why '@' is used here.
        @$route->do_login($dologinrequest, new Response(), $userrepository);

        // This always redirects to authorize() now, rather than completing (or redirecting to
        // approve()) itself; drive that next request too, exactly as the browser would, so this
        // helper ends up on the approval screen for do_approve() to act on below.
        $authorizerequest = (new ServerRequest('GET', '/authorize'))
            ->withQueryParams(['authrequestid' => $requestid]);
        $route->authorize(
            $authorizerequest,
            new Response(),
            $userrepository,
            $this->make_granted_scopes_repository_stub(),
        );

        $grantedscopesrepository = $this->getMockBuilder(granted_scopes_repository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['store_granted_scopes_for_user'])
            ->getMock();

        $approverequest = (new ServerRequest('POST', '/approve'))
            ->withQueryParams(['authrequestid' => $requestid])
            ->withParsedBody([
                'sesskey' => sesskey(),
                'approve' => $approved ? '1' : '0',
                'scopes' => ['moodle'],
            ]);
        $route->do_approve($approverequest, new Response(), $grantedscopesrepository);
    }

    /**
     * Approving a completed OAuth flow clears $SESSION->wantsurl, when it still holds the
     * OAuth authorize URL that login() stored for this exact request id.
     */
    public function test_do_approve_when_approved_clears_matching_oauth_wantsurl(): void {
        global $SESSION;

        $this->resetAfterTest();

        [$route, $requestid] = $this->get_route_with_pending_request_for_wantsurl_tests();

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        // The login() route must have stored something in wantsurl for this to be a meaningful test.
        $this->assertTrue(isset($SESSION->wantsurl));

        $this->submit_valid_login_and_approve($route, $requestid, approved: true);

        $this->assertFalse(isset($SESSION->wantsurl));
    }

    /**
     * The same cleanup happens when the pending request is denied, not just approved:
     * forget_auth_request() runs regardless of the outcome.
     */
    public function test_do_approve_when_denied_clears_matching_oauth_wantsurl(): void {
        global $SESSION;

        $this->resetAfterTest();

        [$route, $requestid] = $this->get_route_with_pending_request_for_wantsurl_tests();

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        $this->assertTrue(isset($SESSION->wantsurl));

        $this->submit_valid_login_and_approve($route, $requestid, approved: false);

        $this->assertFalse(isset($SESSION->wantsurl));
    }

    /**
     * If something else (e.g. an unrelated ordinary login in another tab of the same session)
     * has since replaced wantsurl with a different destination, completing this OAuth flow must
     * leave it untouched, rather than assuming it still owns the slot.
     */
    public function test_do_approve_leaves_unrelated_newer_wantsurl_untouched(): void {
        global $SESSION;

        $this->resetAfterTest();

        [$route, $requestid] = $this->get_route_with_pending_request_for_wantsurl_tests();

        $route->login(
            (new ServerRequest('GET', '/login'))->withQueryParams(['authrequestid' => $requestid]),
            new Response(),
        );

        $newerwantsurl = 'https://example.com/course/view.php?id=7';
        $SESSION->wantsurl = $newerwantsurl;

        $this->submit_valid_login_and_approve($route, $requestid, approved: true);

        $this->assertEquals($newerwantsurl, $SESSION->wantsurl);
    }

    /**
     * Completing an OAuth flow for which wantsurl was never set at all (for example, the user
     * was already logged in and went straight through "continue as current user") must not
     * error, and must leave wantsurl absent.
     */
    public function test_do_approve_with_absent_wantsurl_causes_no_error(): void {
        global $SESSION;

        $this->resetAfterTest();

        [$route, $requestid] = $this->get_route_with_pending_request_for_wantsurl_tests();

        unset($SESSION->wantsurl);

        $this->submit_valid_login_and_approve($route, $requestid, approved: true);

        $this->assertFalse(isset($SESSION->wantsurl));
    }

    /**
     * A URL that merely happens to carry the same "authrequestid" value as a query parameter,
     * but is not the OAuth authorize route itself, must not be mistaken for a match by
     * substring-style checking, and so must not be cleared.
     */
    public function test_do_approve_ignores_non_oauth_url_with_matching_authrequestid_param(): void {
        global $SESSION;

        $this->resetAfterTest();

        [$route, $requestid] = $this->get_route_with_pending_request_for_wantsurl_tests();

        $unrelatedwantsurl = 'https://example.com/course/view.php?authrequestid=' . $requestid;
        $SESSION->wantsurl = $unrelatedwantsurl;

        $this->submit_valid_login_and_approve($route, $requestid, approved: true);

        $this->assertEquals($unrelatedwantsurl, $SESSION->wantsurl);
    }
}
