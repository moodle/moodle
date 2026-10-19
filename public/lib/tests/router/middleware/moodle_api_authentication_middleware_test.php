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

namespace core\router\middleware;

use core\api\repository\api_token_repository;
use core\api\token_manager;
use core\di;
use core\router\route;
use core\router\scope\scopeset;
use core\tests\fake_plugins_test_trait;
use core\tests\router\route_testcase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Tests for the Moodle API Authentication middleware.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(moodle_api_authentication_middleware::class)]
final class moodle_api_authentication_middleware_test extends route_testcase {
    use fake_plugins_test_trait;

    /**
     * Build a middleware instance, optionally with a mocked OAuth2 Resource Server.
     *
     * @param null|ResourceServer $server A configured mock, or null for a bare mock which is never expected to be used.
     * @return moodle_api_authentication_middleware
     */
    private function get_middleware(?ResourceServer $server = null): moodle_api_authentication_middleware {
        di::set(
            ResourceServer::class,
            $server ?? $this->createMock(ResourceServer::class),
        );
        return di::make(moodle_api_authentication_middleware::class, [
            'app' => $this->get_simple_app(),
        ]);
    }

    /**
     * Get a request handler which records the request it was called with, and returns a 200 response.
     *
     * @return RequestHandlerInterface&object{capturedrequest: ?ServerRequestInterface}
     */
    private function get_recording_handler(): RequestHandlerInterface {
        return new class implements RequestHandlerInterface {
            /** @var null|ServerRequestInterface The request the handler was called with. */
            public ?ServerRequestInterface $capturedrequest = null;

            #[\Override]
            public function handle(ServerRequestInterface $request): ResponseInterface {
                $this->capturedrequest = $request;

                return new Response(200);
            }
        };
    }

    /**
     * Build a Bearer API token string of the shape issued by {@see token_manager::issue_token}.
     *
     * @param int $tokenid
     * @param string $secret
     * @return string
     */
    private function build_bearer_token(int $tokenid, string $secret): string {
        return rtrim(
            token_manager::TOKEN_PREFIX . base64_encode("{$tokenid}/{$secret}"),
            '=',
        );
    }

    public function test_require_login_without_course(): void {
        $app = $this->get_simple_app();
        $app->add(di::get(moodle_api_authentication_middleware::class));
        $app->addRoutingMiddleware();

        $app->map(['GET'], '/test', function ($request, $response) {
            return $response;
        });

        $route = new \core\router\route(
            requirelogin: new \core\router\require_login(
                requirelogin: true,
                autologinguest: false,
            ),
        );

        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute(\core\router\route::class, $route);

        // We expect a redirect to be returned as the user is not logged in and autologin guest is disabled.
        // This will be updated in the future to check for a 401 response once `require_login` and `require_course_login`
        // are rewritten.
        $this->expectException(\core\exception\moodle_exception::class);

        // Handle the request.
        $returns = $app->handle($request);
    }

    /**
     * A request bearing a valid API key token, authenticates the request.
     */
    public function test_api_key_auth_with_valid_token_authenticates(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $repository = di::get(api_token_repository::class);
        $token = $repository->create_token('Test', 'correctsecret', $user->id, ['core_user:user:read']);
        $bearer = $this->build_bearer_token($token->get_id(), 'correctsecret');

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', "Bearer {$bearer}")
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, []);

        $handler = $this->get_recording_handler();
        $response = $this->get_middleware()->process($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->id, $handler->capturedrequest->getAttribute('user')->id);
        $this->assertEquals($token->get_id(), $handler->capturedrequest->getAttribute('api_token_id'));
    }

    /**
     * A syntactically valid, but unknown or incorrect, API key token is rejected.
     */
    public function test_api_key_auth_with_invalid_token_throws(): void {
        $this->resetAfterTest();

        $bearer = $this->build_bearer_token(999999, 'somesecret');

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', "Bearer {$bearer}")
            ->withAttribute(route::class, $route);

        $this->expectException(\core\exception\invalid_api_token_exception::class);
        $this->get_middleware()->process($request, $this->get_recording_handler());
    }

    /**
     * A valid OAuth2 bearer token, logs the user in.
     */
    public function test_oauth2_login_authenticates(): void {
        global $USER;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willReturnCallback(
                fn (ServerRequestInterface $request) => $request->withAttribute('oauth_user_id', (string) $user->id)
            );

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', 'Bearer sometoken')
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, []);

        $response = $this->get_middleware($server)->process($request, $this->get_recording_handler());

        $this->assertEquals(200, $response->getStatusCode());
        // Note: Unlike API key auth, a successful OAuth2 login does not set a `user` request attribute - the
        // user is only made available via the global session (`$USER`) and Moodle session state.
        $this->assertEquals($user->id, $USER->id);
    }

    /**
     * An OAuth2 bearer token with no associated Moodle user (for example, a client_credentials
     * grant) logs in as the system user, rather than a real Moodle account.
     */
    public function test_oauth2_login_with_no_user_authenticates_as_system_user(): void {
        global $USER;

        $this->resetAfterTest();

        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willReturnCallback(
                fn (ServerRequestInterface $request) => $request->withAttribute('oauth_user_id', '0')
            );

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', '******')
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, []);

        $response = $this->get_middleware($server)->process($request, $this->get_recording_handler());

        $this->assertEquals(200, $response->getStatusCode());
        // The system user is based on the admin account, but is not a specific real Moodle user.
        $this->assertEquals(\core\user::get_system_user()->id, $USER->id);
        $this->assertEquals(get_admin()->id, $USER->id);
    }

    /**
     * A user who is not permitted to log in (for example, a suspended account) is denied with a 401 response,
     * rather than the exception propagating unhandled.
     */
    public function test_oauth2_login_denied_user_returns_access_denied_response(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user(['suspended' => 1]);
        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willReturnCallback(
                fn (ServerRequestInterface $request) => $request->withAttribute('oauth_user_id', (string) $user->id)
            );

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', 'Bearer sometoken')
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, []);

        $response = $this->get_middleware($server)->process($request, $this->get_recording_handler());

        $this->assertEquals(401, $response->getStatusCode());
        $payload = json_decode((string) $response->getBody());
        $this->assertEquals('access_denied', $payload->error);
    }

    /**
     * An invalid OAuth2 Authorization header (for example, a malformed or expired JWT) is converted into
     * a standard OAuth2 error response, rather than propagating out of the middleware.
     */
    public function test_oauth2_header_with_invalid_credentials_returns_error_response(): void {
        $this->resetAfterTest();

        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willThrowException(OAuthServerException::invalidCredentials());

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', 'Bearer badtoken')
            ->withAttribute(route::class, $route);

        $response = $this->get_middleware($server)->process($request, $this->get_recording_handler());

        $this->assertEquals(
            OAuthServerException::invalidCredentials()->getHttpStatusCode(),
            $response->getStatusCode(),
        );
    }

    /**
     * A request with no Authorization header at all, on a route which permits cookies, falls back to
     * cookie-based authentication rather than being rejected outright.
     */
    public function test_no_authorization_header_falls_back_to_cookie_auth(): void {
        $this->resetAfterTest();

        $route = new route(
            cookies: true,
        );
        $request = (new ServerRequest('GET', '/test'))
            ->withAttribute(route::class, $route);

        $response = $this->get_middleware()->process($request, $this->get_recording_handler());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Install the fake_oauth2scope fixture plugin used by the scope-validation tests below.
     */
    protected function add_fake_oauth2scope_plugin(): void {
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );
    }

    /**
     * A valid API key token which does not carry a scope required by the route is denied with a 403
     * (forbidden) response, rather than being permitted to authenticate.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_api_key_auth_with_missing_scope_is_denied(): void {
        $this->resetAfterTest();
        $this->add_fake_oauth2scope_plugin();

        $user = $this->getDataGenerator()->create_user();
        $repository = di::get(api_token_repository::class);
        // The token does not carry the scope required by the route below.
        $token = $repository->create_token('Test', 'correctsecret', $user->id, []);
        $bearer = $this->build_bearer_token($token->get_id(), 'correctsecret');

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', "Bearer {$bearer}")
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, [
                new scopeset(new \fake_oauth2scope\route\scope\resource\read()),
            ]);

        $this->expectException(\core\exception\insufficient_scope_exception::class);
        $this->get_middleware()->process($request, $this->get_recording_handler());
    }

    /**
     * A valid API key token which carries the scope required by the route is permitted to authenticate.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_api_key_auth_with_required_scope_authenticates(): void {
        $this->resetAfterTest();
        $this->add_fake_oauth2scope_plugin();

        $user = $this->getDataGenerator()->create_user();
        $repository = di::get(api_token_repository::class);
        $token = $repository->create_token('Test', 'correctsecret', $user->id, ['fake_oauth2scope:resource:read']);
        $bearer = $this->build_bearer_token($token->get_id(), 'correctsecret');

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', "Bearer {$bearer}")
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, [
                new scopeset(new \fake_oauth2scope\route\scope\resource\read()),
            ]);

        $handler = $this->get_recording_handler();
        $response = $this->get_middleware()->process($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->id, $handler->capturedrequest->getAttribute('user')->id);
    }

    /**
     * A valid OAuth2 login which does not carry a scope required by the route is denied with a 403
     * (forbidden) response.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_oauth2_login_with_missing_scope_is_denied(): void {
        $this->resetAfterTest();
        $this->add_fake_oauth2scope_plugin();

        $user = $this->getDataGenerator()->create_user();
        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willReturnCallback(fn (ServerRequestInterface $request) => $request
                ->withAttribute('oauth_user_id', (string) $user->id)
                ->withAttribute('oauth_scopes', [])
            );

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', '******')
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, [
                new scopeset(new \fake_oauth2scope\route\scope\resource\read()),
            ]);

        $this->expectException(\core\exception\insufficient_scope_exception::class);
        $this->get_middleware($server)->process($request, $this->get_recording_handler());
    }

    /**
     * A valid OAuth2 login which carries the scope required by the route is permitted to authenticate.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_oauth2_login_with_required_scope_authenticates(): void {
        global $USER;

        $this->resetAfterTest();
        $this->add_fake_oauth2scope_plugin();

        $user = $this->getDataGenerator()->create_user();
        $server = $this->createMock(ResourceServer::class);
        $server->method('validateAuthenticatedRequest')
            ->willReturnCallback(fn (ServerRequestInterface $request) => $request
                ->withAttribute('oauth_user_id', (string) $user->id)
                ->withAttribute('oauth_scopes', ['fake_oauth2scope:resource:read'])
            );

        $route = new route();
        $request = (new ServerRequest('GET', '/test'))
            ->withHeader('Authorization', '******')
            ->withAttribute(route::class, $route)
            ->withAttribute(scopeset::class, [
                new scopeset(new \fake_oauth2scope\route\scope\resource\read()),
            ]);

        $response = $this->get_middleware($server)->process($request, $this->get_recording_handler());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($user->id, $USER->id);
    }
}
