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

use core\oauth2\server\repository\user_repository;
use core\output\renderable;
use core\router\route;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The oauth2 routes which control the oauth2 authorization flows.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oauth2 {
    /**
     * The name of the query parameter used to correlate a browser flow (authorize -> login ->
     * approve) with its pending authorization request in the session.
     */
    private const REQUEST_ID_PARAM = 'authrequestid';

    /**
     * Constructor for OAuth2 Routes.
     *
     * @param AuthorizationServer $server
     * @param ClientRepositoryInterface $clientrepository
     * @param ScopeRepositoryInterface $scoperepository
     */
    public function __construct(
        /** @var AuthorizationServer */
        private AuthorizationServer $server,
        /** @var ClientRepositoryInterface */
        private ClientRepositoryInterface $clientrepository,
        /** @var ScopeRepositoryInterface */
        private ScopeRepositoryInterface $scoperepository,
    ) {
    }

    /**
     * Handle the authorization request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param user_repository $userrepository
     * @return ResponseInterface
     */
    #[route(
        path: '/authorize',
        method: ['GET'],
    )]
    public function authorize(
        ServerRequestInterface $request,
        ResponseInterface $response,
        user_repository $userrepository,
    ): ResponseInterface {
        try {
            [$requestid, $authrequest] = $this->get_auth_request($request);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response.
            return $exception->generateHttpResponse($response);
        }

        if (isloggedin() && !isguestuser()) {
            // User is logged in and not guest.
            // Set the user on the auth request.
            // Redirect to the login page to confirm that the user wishes to continue as this user.
            $authrequest->setUser($userrepository->get_current_user());
            $this->store_auth_request($requestid, $authrequest);
        }

        return \core\router\util::redirect_to_callable(
            $request,
            $response,
            [self::class, 'login'],
            queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
        );
    }

    /**
     * Fetch a token for the client.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/token',
        method: ['GET', 'POST'],
    )]
    public function token(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            // Try to respond to the request.
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response.
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // Unknown exception.
            $body = $response->getBody();
            $body->write($exception->getMessage());
            return $response->withStatus(500);
        }
    }

    /**
     * Fetch a token for the client_grant.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/access_token',
        method: ['GET', 'POST'],
    )]
    public function access_token(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        try {
            // Try to respond to the request.
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response.
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // Unknown exception.
            $body = $response->getBody();
            $body->write($exception->getMessage());
            return $response->withStatus(500);
        }
    }

    /**
     * Show the login form.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/login',
        method: ['GET'],
    )]
    public function login(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        global $SESSION;

        // If the user is already logged in, make this selectable.
        $loginurl = \core\router\util::get_path_for_callable([self::class, 'do_login']);
        $loginurl->params($request->getQueryParams());

        if (isloggedin() && !isguestuser()) {
            [, $authrequest] = $this->get_auth_request($request);

            $logouturl = \core\router\util::get_path_for_callable([self::class, 'logout']);
            $logouturl->params($request->getQueryParams());

            $continueform = new \core_auth\output\oauth2\continue_as_user_page(
                $authrequest->getClient(),
                $loginurl,
                $logouturl,
                \core\user::get_user($authrequest->getUser()->getIdentifier()),
            );

            return $this->render_page_from_renderable(
                $continueform,
                $response,
            );
        }

        $authentication = \core\di::get(\core\authentication::class);
        [
            'frm' => $frm,
        ] = $authentication->process_loginpage_hooks();

        $authsequence = $authentication->get_enabled_plugins(); // Auths, in sequence.

        if (!\is_object($frm)) {
            $frm = new \stdClass();
        }

        $username = $request->getQueryParams()['username'] ?? '';
        if ($username !== '') {
            $frm->username = clean_param($username, PARAM_RAW);
        } else {
            $frm->username = get_moodle_cookie();
        }

        $loginform = new \core_auth\output\login_form(
            $loginurl,
            $authsequence,
            $frm->username,
        );

        // Disable guest login and signup for OAuth2 login form.
        $loginform->set_can_login_as_guest(false);
        $loginform->set_signup_allowed(false);

        // Set the wantsurl to the /authorize route.
        // This allows any IDP login page to redirect back to the /authorize route after login.
        $SESSION->wantsurl = \core\router\util::get_path_for_callable(
            [self::class, 'authorize'],
            queryparams: $request->getQueryParams(),
        );

        return $this->render_page_from_renderable(
            $loginform,
            $response,
        );
    }

    /**
     * Log out to change the current user.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/logout',
        method: ['POST'],
    )]
    public function logout(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        \core\router\util::require_sesskey($request);

        $authentication = \core\di::get(\core\authentication::class);
        $authsequence = $authentication->get_enabled_plugins(); // Authentication plugins, in sequence.
        foreach ($authsequence as $authname) {
            $authplugin = $authentication->get_plugin($authname);
            $authplugin->logoutpage_hook();
        }

        require_logout();

        return \core\router\util::redirect_to_callable(
            $request,
            $response,
            [self::class, 'authorize'],
        );
    }

    /**
     * Process the login form submission.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param user_repository $userrepository
     * @return ResponseInterface
     */
    #[route(
        path: '/login',
        method: ['POST'],
    )]
    public function do_login(
        ServerRequestInterface $request,
        ResponseInterface $response,
        user_repository $userrepository,
    ): ResponseInterface {
        // Handle the login form submission.
        [$requestid, $authrequest] = $this->get_auth_request($request);

        $parsedbody = $request->getParsedBody();

        $user = null;
        if (($parsedbody['currentuser'] ?? '') === '1') {
            // Continue as the current user.
            // This is a state-changing action performed using only the ambient session cookie, so it must be
            // protected against CSRF. The continue_as_user_page always includes a sesskey field for this purpose.
            \core\router\util::require_sesskey($request);
            $user = $userrepository->get_current_user();
        } else if (!empty($parsedbody['username']) && !empty($parsedbody['password'])) {
            // Validate the user credentials.
            $user = $userrepository->getUserEntityByUserCredentials(
                $parsedbody['username'] ?? '',
                $parsedbody['password'] ?? '',
                '',
                $authrequest->getClient(),
            );
        }

        if ($user === null) {
            // Login failed, redirect back to login form.
            return \core\router\util::redirect_to_callable(
                $request,
                $response,
                [self::class, 'login'],
                queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
            );
        }

        $authrequest->setUser($user);
        $this->store_auth_request($requestid, $authrequest);

        return \core\router\util::redirect_to_callable(
            $request,
            $response,
            [self::class, 'approve'],
            queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
        );
    }

    /**
     * Handle the refresh token request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/refresh',
        method: ['POST'],
    )]
    public function refresh(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        return $this->server->respondToAccessTokenRequest($request, $response);
    }

    /**
     * Show the approval form.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param ScopeRepositoryInterface $scoperepository
     * @return ResponseInterface
     */
    #[route(
        path: '/approve',
        method: ['GET'],
    )]
    public function approve(
        ServerRequestInterface $request,
        ResponseInterface $response,
        ScopeRepositoryInterface $scoperepository,
        \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository,
    ): ResponseInterface {
        [$requestid, $authrequest] = $this->get_auth_request($request);

        $requestedscopes = array_map(fn($scope): string => $scope->getIdentifier(), $authrequest->getScopes());
        $client = $authrequest->getClient();
        $grantedscopes = $grantedscopesrepository->get_granted_scopes_for_user(
            $authrequest->getClient(),
            $authrequest->getUser(),
        );

        $newscopes = array_map(
            fn (string $identifier): ?ScopeEntityInterface => $scoperepository->getScopeEntityByIdentifier($identifier),
            array_values(array_diff($requestedscopes, $grantedscopes)),
        );

        // Carry the request id (and the rest of the flow's query params) forward, so that
        // do_approve() can find the same pending request in the session.
        $doapproveurl = \core\router\util::get_path_for_callable([self::class, 'do_approve']);
        $doapproveurl->params(array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]));

        // Render a simple approval form.
        $confirmscopesform = new \core_auth\output\oauth2\confirm_scopes_page(
            $client,
            $grantedscopes,
            $newscopes,
            $doapproveurl,
            \core\user::get_user($authrequest->getUser()->getIdentifier()),
        );

        return $this->render_page_from_renderable(
            $confirmscopesform,
            $response,
        );
    }

    /**
     * Process the approval form submission.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/approve',
        method: ['POST'],
    )]
    public function do_approve(
        ServerRequestInterface $request,
        ResponseInterface $response,
        \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository,
    ): ResponseInterface {
        \core\router\util::require_sesskey($request);

        $approved = $request->getParsedBody()['approve'] ?? '0';
        [$requestid, $authrequest] = $this->get_auth_request($request);

        if ($approved === '1') {
            $selectedscopes = $request->getParsedBody()['scopes'] ?? [];
            $grantedscopesrepository->store_granted_scopes_for_user(
                $authrequest->getClient(),
                $authrequest->getUser(),
                $selectedscopes,
            );
            $authrequest->setAuthorizationApproved(true);
        }

        // The flow for this request id is now complete, whether it was approved or denied.
        // Discard the pending request rather than leaving it in the session, where a resubmission
        // of this form (e.g. a replayed request) could otherwise re-trigger completion.
        $this->forget_auth_request($requestid);

        try {
            return $this->server->completeAuthorizationRequest($authrequest, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        }
    }

    /**
     * Helper to get the pending authentication request for the current flow from the session,
     * or validate and create a new one (and a new request id for it) if there isn't one.
     *
     * The request is looked up using the {@see self::REQUEST_ID_PARAM} query parameter, rather
     * than a single session-wide slot, so that multiple authorization flows (e.g. in separate
     * browser tabs) can be in progress concurrently within the same session without clobbering
     * one another.
     *
     * @param ServerRequestInterface $request
     * @return array{0: string, 1: \League\OAuth2\Server\RequestTypes\AuthorizationRequest} The
     *      request id the request is (or will be) stored under, and the request itself.
     */
    private function get_auth_request(
        ServerRequestInterface $request,
    ): array {
        $requestid = $request->getQueryParams()[self::REQUEST_ID_PARAM] ?? null;
        if ($requestid !== null) {
            $authrequest = $this->restore_auth_request($requestid);
            if ($authrequest !== null) {
                return [$requestid, $authrequest];
            }
        }

        // No request id was supplied, or there is no longer a stored request for it (for example,
        // it has never been seen before, or the session entry has expired) - validate a new
        // request from the current query string, and remember it under a freshly generated id.
        //
        // validateAuthorizationRequest() already resolves and sets both the client and the
        // redirect URI (validated against the client's registered redirect URIs) from the same
        // query string, so there is no need to look either of them up again here.
        $authrequest = $this->server->validateAuthorizationRequest($request);

        $requestid = \core\uuid::generate();
        $this->store_auth_request($requestid, $authrequest);

        return [$requestid, $authrequest];
    }

    /**
     * Helper to persist the authentication request in the session, keyed by request id.
     *
     * Only the plain scalar data needed to reconstruct the request is stored (rather than a
     * serialized copy of the object itself), to avoid unserializing arbitrary session data back
     * into objects.
     *
     * @param string $requestid
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest
     */
    private function store_auth_request(
        string $requestid,
        \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest,
    ): void {
        global $SESSION;

        $SESSION->oauth2requests ??= [];
        $SESSION->oauth2requests[$requestid] = [
            'granttypeid' => $authrequest->getGrantTypeId(),
            'clientid' => $authrequest->getClient()->getIdentifier(),
            'redirecturi' => $authrequest->getRedirectUri(),
            'state' => $authrequest->getState(),
            'scopes' => array_map(
                fn (ScopeEntityInterface $scope): string => $scope->getIdentifier(),
                $authrequest->getScopes(),
            ),
            'userid' => $authrequest->getUser()?->getIdentifier(),
            'authorizationapproved' => $authrequest->isAuthorizationApproved(),
            'codechallenge' => $authrequest->getCodeChallenge(),
            'codechallengemethod' => $authrequest->getCodeChallengeMethod(),
        ];
    }

    /**
     * Helper to rebuild an AuthorizationRequest from the plain data stored in the session for the
     * given request id.
     *
     * @param string $requestid
     * @return \League\OAuth2\Server\RequestTypes\AuthorizationRequest|null Null if there is no
     *      (or no longer a valid) stored request for the given id.
     */
    private function restore_auth_request(
        string $requestid,
    ): ?\League\OAuth2\Server\RequestTypes\AuthorizationRequest {
        global $SESSION;

        $data = $SESSION->oauth2requests[$requestid] ?? null;
        if ($data === null) {
            return null;
        }

        $client = $this->clientrepository->getClientEntity($data['clientid']);
        if ($client === null) {
            // The client may have been removed since the request was stored.
            return null;
        }

        $authrequest = new \League\OAuth2\Server\RequestTypes\AuthorizationRequest();
        $authrequest->setGrantTypeId($data['granttypeid']);
        $authrequest->setClient($client);
        $authrequest->setRedirectUri($data['redirecturi']);
        if ($data['state'] !== null) {
            $authrequest->setState($data['state']);
        }
        $authrequest->setScopes(array_values(array_filter(array_map(
            fn (string $identifier): ?ScopeEntityInterface => $this->scoperepository->getScopeEntityByIdentifier($identifier),
            $data['scopes'],
        ))));
        if ($data['userid'] !== null) {
            $user = new \core\oauth2\server\entity\user_entity();
            $user->setIdentifier($data['userid']);
            $authrequest->setUser($user);
        }
        $authrequest->setAuthorizationApproved($data['authorizationapproved']);
        if ($data['codechallenge'] !== null) {
            $authrequest->setCodeChallenge($data['codechallenge']);
        }
        if ($data['codechallengemethod'] !== null) {
            $authrequest->setCodeChallengeMethod($data['codechallengemethod']);
        }

        return $authrequest;
    }

    /**
     * Helper to discard the stored request for the given id, once its flow has completed.
     *
     * @param string $requestid
     */
    private function forget_auth_request(string $requestid): void {
        global $SESSION;

        unset($SESSION->oauth2requests[$requestid]);
    }

    /**
     * Helper to render a page with header and footer.
     *
     * @param string $title
     * @param renderable $content
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function render_page_from_renderable(
        renderable $content,
        ResponseInterface $response,
        ?string $title = null,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        $PAGE->set_pagelayout('login');
        $PAGE->set_context(\core\context\system::instance());

        if ($title) {
            $PAGE->set_title($title);
            $PAGE->set_heading($title);
        }

        $response->getBody()->write($OUTPUT->header());
        if ($title) {
            $response->getBody()->write($OUTPUT->heading($title));
        }

        $response->getBody()->write($OUTPUT->render($content));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }
}
