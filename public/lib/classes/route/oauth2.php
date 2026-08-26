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
     * @param \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository
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
        \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository,
    ): ResponseInterface {
        try {
            [$requestid, $authrequest] = $this->get_auth_request($request);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response.
            return $exception->generateHttpResponse($response);
        }

        if (isloggedin() && !isguestuser()) {
            // User is logged in and not guest.
            // Set the user on the auth request. This is always freshly derived from the live
            // session ($USER) here, never from any previously stored request, so
            // has_granted_all_scopes() below can only silently complete this request for the
            // actual logged-in, non-guest user - never a guest or some other, stale identity.
            $authrequest->setUser($userrepository->get_current_user());

            if ($grantedscopesrepository->has_granted_all_scopes(
                $authrequest->getClient(),
                $authrequest->getUser(),
                $authrequest->getScopes(),
            )) {
                // This user has already granted every scope this client is requesting for this
                // authorization request: approve and complete it immediately, without showing any
                // of the login, "continue as user", or scope confirmation screens.
                $authrequest->setAuthorizationApproved(true);
                return $this->complete_authorization_request($requestid, $authrequest, $response);
            }

            // Redirect to the login page to confirm that the user wishes to continue as this user.
            $this->store_auth_request($requestid, $authrequest);
        }

        return $this->redirect_to_login($request, $response, $requestid);
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
            // Unknown exception. Log the real message server-side, but do not expose internal
            // details (e.g. database errors, paths, class names) to the OAuth client.
            debugging('Unhandled exception in OAuth2 token endpoint: ' . $exception->getMessage(), DEBUG_DEVELOPER);
            $body = $response->getBody();
            $body->write(get_string('error', 'error'));
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
            // Unknown exception. Log the real message server-side, but do not expose internal
            // details (e.g. database errors, paths, class names) to the OAuth client.
            debugging('Unhandled exception in OAuth2 token endpoint: ' . $exception->getMessage(), DEBUG_DEVELOPER);
            $body = $response->getBody();
            $body->write(get_string('error', 'error'));
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
        global $SESSION, $PAGE, $USER;

        // If the user is already logged in, make this selectable.
        $loginurl = \core\router\util::get_path_for_callable([self::class, 'do_login']);
        $loginurl->params($request->getQueryParams());

        if (isloggedin() && !isguestuser()) {
            [, $authrequest] = $this->get_auth_request($request);

            $logouturl = \core\router\util::get_path_for_callable([self::class, 'logout']);
            $logouturl->params($request->getQueryParams());

            // The user shown here is the live session user ($USER), not the user stored on the
            // pending authorization request: the request may have been authenticated as a
            // different user (e.g. in another tab) since it was first created, and submitting
            // this form always continues as the live session user via get_current_user() in
            // do_login(), never the one recorded on the request. Showing anything else here
            // would display incorrect security-relevant information. The request itself is left
            // untouched - it is only updated once the user actually submits this form.
            $continueform = new \core_auth\output\oauth2\continue_as_user_page(
                $authrequest->getClient(),
                $loginurl,
                $logouturl,
                \core\user::get_user($USER->id),
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

        // A previous do_login() failure leaves a one-use flash, scoped to this request id,
        // recording the error to display and the username that was submitted. Consume it (so
        // it is not shown again on a later render of this same request id) before falling back
        // to the normal username sources.
        $requestid = $request->getQueryParams()[self::REQUEST_ID_PARAM] ?? null;
        $loginerror = $requestid !== null ? $this->consume_login_error($requestid) : null;

        // Retrieve the pending authorization request, if there is one, so that the requesting
        // client's identity can be shown on the login form before the user enters their
        // credentials. There may not be one yet (e.g. a request id has never been seen before),
        // in which case the login form renders exactly as it would outside an OAuth2 flow.
        $pendingauthrequest = $requestid !== null ? $this->restore_auth_request($requestid) : null;

        if ($loginerror !== null) {
            $frm->username = $loginerror['username'];
        } else {
            $username = $request->getQueryParams()['username'] ?? '';
            if ($username !== '') {
                $frm->username = clean_param($username, PARAM_RAW);
            } else {
                $frm->username = get_moodle_cookie();
            }
        }

        $PAGE->set_context(\context_system::instance());
        $loginform = new \core_auth\output\login(
            $authsequence,
            $frm->username,
        );

        $loginform->set_login_url($loginurl);

        // Disable guest login and signup for OAuth2 login form.
        $loginform->set_can_login_as_guest(false);
        $loginform->set_signup_allowed(false);

        if ($pendingauthrequest !== null) {
            $loginform->set_oauth2_client($pendingauthrequest->getClient());
        }

        if ($loginerror !== null) {
            // Precise authentication failure codes (unauthorised, recaptcha, etc.) are out of
            // scope here; always show the same generic invalid-login error shown by the
            // standalone login/index.php flow for a bad username/password.
            $loginform->set_error('', AUTH_LOGIN_FAILED);
        }

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
     * @param \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository
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
        \core\oauth2\server\repository\granted_scopes_repository $grantedscopesrepository,
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

            // A valid sesskey alone does not prove there is a genuine, non-guest session to
            // continue as: guest sessions carry a valid sesskey too, and the session may since
            // have been logged out entirely. Leave $user as null (falling through to the same
            // failed-login handling used below) rather than trusting get_current_user() to
            // return some other default identity in either case.
            if (isloggedin() && !isguestuser()) {
                $user = $userrepository->get_current_user();
            }
        } else if (!empty($parsedbody['username']) && !empty($parsedbody['password'])) {
            // Validate the user credentials and, on success, establish a full Moodle session for
            // the authenticated user (as the standard login form does), rather than only
            // accepting the credentials for the OAuth2 flow itself.
            $moodleuser = $userrepository->authenticate_user(
                $parsedbody['username'] ?? '',
                $parsedbody['password'] ?? '',
                $parsedbody['logintoken'] ?? '',
            );

            if ($moodleuser !== false) {
                complete_user_login($moodleuser);
                $user = $userrepository->get_current_user();
            }
        }

        if ($user === null) {
            // Login failed. Record a one-use flash (error + submitted username), scoped to this
            // request id, so the redisplayed login form can show it, then redirect back to the
            // login form using the existing POST-redirect-GET pattern. The pending OAuth
            // authorization request itself (stored separately under this same request id) is
            // left untouched, so a subsequent valid login can still continue the flow.
            $this->store_login_error($requestid, (string) ($parsedbody['username'] ?? ''));

            return \core\router\util::redirect_to_callable(
                $request,
                $response,
                [self::class, 'login'],
                queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
            );
        }

        // The authenticated user is always the live session user here: either just established
        // via complete_user_login() above, or (for 'continue as current user') only ever
        // assigned once isloggedin() && !isguestuser() has been confirmed. has_granted_all_scopes()
        // below can therefore only silently complete this request for that same real,
        // logged-in, non-guest user.
        $authrequest->setUser($user);

        if ($grantedscopesrepository->has_granted_all_scopes(
            $authrequest->getClient(),
            $user,
            $authrequest->getScopes(),
        )) {
            // This user has already granted every scope this client is requesting: approve and
            // complete the authorization request immediately, without showing the scope
            // confirmation screen.
            $authrequest->setAuthorizationApproved(true);
            return $this->complete_authorization_request($requestid, $authrequest, $response);
        }

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

        if (!$this->session_matches_authrequest_user($authrequest)) {
            // The live session can no longer be trusted to view this pending request's consent
            // screen - it may have been logged out, be a guest session, or (e.g. another tab)
            // now belong to a different user than the one this request was authenticated for.
            // Send it back to log in again, rather than rendering another user's consent
            // screen; the pending request itself is left in the session, so a subsequent valid
            // login can still continue this same flow.
            return $this->redirect_to_login($request, $response, $requestid);
        }

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
        // The sesskey and the session-identity check below protect different things: sesskey
        // proves this submission was not forged by another site (CSRF), while the check below
        // proves the session submitting it is still a real, logged-in session for the exact
        // user this pending request was authenticated for. Neither substitutes for the other -
        // a forged-but-otherwise-valid sesskey could still be replayed from a session that has
        // since been logged out, switched to guest, or switched to a different user entirely.
        \core\router\util::require_sesskey($request);

        [$requestid, $authrequest] = $this->get_auth_request($request);

        if (!$this->session_matches_authrequest_user($authrequest)) {
            // Never store granted scopes, mark the request approved, or complete it - for
            // either an approval or a denial - on behalf of a session that cannot be proven to
            // still be this user. Send it back to log in again instead; the pending request
            // itself is left in the session, so a subsequent valid login can still continue
            // this same flow.
            return $this->redirect_to_login($request, $response, $requestid);
        }

        $approved = $request->getParsedBody()['approve'] ?? '0';

        if ($approved === '1') {
            $selectedscopes = array_map(function ($scope) {
                return $scope->getIdentifier();
            }, $authrequest->getScopes());

            $grantedscopesrepository->store_granted_scopes_for_user(
                $authrequest->getClient(),
                $authrequest->getUser(),
                $selectedscopes,
            );
            $authrequest->setAuthorizationApproved(true);
        }

        return $this->complete_authorization_request($requestid, $authrequest, $response);
    }

    /**
     * Confirm that the live Moodle session is logged in, is not the guest user, and is logged
     * in as the exact user recorded on the given authorization request.
     *
     * This is the boundary that stops a stale, logged-out, guest, or different-user session
     * from being trusted to view or act on someone else's pending authorization request (used
     * by {@see self::approve()} and {@see self::do_approve()}). It deliberately checks only
     * session validity and user-identity matching; it does not check account-policy state
     * (forced password change, incomplete profile, password expiry, etc.), which is out of
     * scope here and left for separate handling.
     *
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest
     * @return bool
     */
    private function session_matches_authrequest_user(
        \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest,
    ): bool {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $requestuser = $authrequest->getUser();
        if ($requestuser === null) {
            return false;
        }

        // Identifiers are compared as strings: $USER->id is an int, while
        // UserEntityInterface::getIdentifier() is declared to return a string, so this avoids
        // relying on PHP's loose (==) numeric/string comparison rules.
        return (string) $USER->id === (string) $requestuser->getIdentifier();
    }

    /**
     * Helper to redirect back to the login page for the given pending request id, preserving it
     * (rather than discarding it), so the user can (re-)authenticate and continue the same flow.
     *
     * Used by {@see self::authorize()}, {@see self::approve()} and {@see self::do_approve()}
     * whenever the live session cannot be trusted to continue a pending authorization request -
     * for example, there is no session at all, it is a guest session, or it belongs to a
     * different user than the one recorded on the request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param string $requestid
     * @return ResponseInterface
     */
    private function redirect_to_login(
        ServerRequestInterface $request,
        ResponseInterface $response,
        string $requestid,
    ): ResponseInterface {
        return \core\router\util::redirect_to_callable(
            $request,
            $response,
            [self::class, 'login'],
            queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
        );
    }

    /**
     * Helper to discard the pending request and complete an authorization request against the
     * OAuth2 server, once it has been either approved or denied.
     *
     * Shared by {@see self::do_approve()} (the interactive scope-confirmation flow) and
     * {@see self::authorize()} (the non-interactive path taken when the user has already granted
     * every requested scope), so that the final approval/completion step is not duplicated
     * between them.
     *
     * @param string $requestid
     * @param \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function complete_authorization_request(
        string $requestid,
        \League\OAuth2\Server\RequestTypes\AuthorizationRequest $authrequest,
        ResponseInterface $response,
    ): ResponseInterface {
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
     * Also discards any pending invalid-login flash for the same request id (see
     * {@see self::store_login_error()}), since it belongs to this same abandoned flow and would
     * otherwise linger in the session indefinitely.
     *
     * Also clears $SESSION->wantsurl, but only if it is still exactly the OAuth authorize URL
     * that login() stored for this same request id (see {@see self::wantsurl_is_for_request()}).
     * login() reuses the site-wide wantsurl slot so that an external IdP-style auth plugin can
     * redirect back to /authorize after login, but never restores it afterwards; once this
     * request is forgotten, that URL is dangling and would otherwise be picked up by a later,
     * unrelated ordinary login and mistaken for its own destination, starting a brand new OAuth
     * flow. If wantsurl no longer points at this request (another tab or route has since
     * replaced it with something else, or it was never set) it is left untouched.
     *
     * @param string $requestid
     */
    private function forget_auth_request(string $requestid): void {
        global $SESSION;

        unset($SESSION->oauth2requests[$requestid]);
        unset($SESSION->oauth2loginerrors[$requestid]);

        if (isset($SESSION->wantsurl) && $this->wantsurl_is_for_request($SESSION->wantsurl, $requestid)) {
            unset($SESSION->wantsurl);
        }
    }

    /**
     * Helper to determine whether the given wantsurl value is the OAuth authorize URL that
     * login() would have stored for the given request id.
     *
     * $SESSION->wantsurl is a site-wide slot that may hold a plain string (as set by
     * require_login() or login/index.php) or a {@see \core\url} instance (as set by login()), so
     * it is parsed with the same URL API used to build it, and compared on its route and
     * "authrequestid" query parameter rather than by substring matching, to avoid mistaking an
     * unrelated URL that merely happens to contain the same id as a match.
     *
     * @param mixed $wantsurl The current value of $SESSION->wantsurl.
     * @param string $requestid
     * @return bool
     */
    private function wantsurl_is_for_request(mixed $wantsurl, string $requestid): bool {
        try {
            $url = new \core\url($wantsurl);
        } catch (\moodle_exception) {
            // Not a URL \core\url can parse at all, so it is definitely not the one login()
            // stored.
            return false;
        }

        $authorizeurl = \core\router\util::get_path_for_callable([self::class, 'authorize']);
        if (!$url->compare($authorizeurl, URL_MATCH_BASE)) {
            // Some other destination entirely (e.g. the originally-requested protected page, or
            // a different route), not the OAuth authorize route.
            return false;
        }

        return $url->get_param(self::REQUEST_ID_PARAM) === $requestid;
    }

    /**
     * Helper to store a one-use invalid-login flash (the error to display, and the username
     * that was submitted) for the given request id.
     *
     * This is kept in its own session structure, separate from the pending authorization
     * request data stored by {@see self::store_auth_request()}, and deliberately does not use
     * the global $SESSION->loginerrormsg/loginerrorcode keys used by the standalone
     * login/index.php flow, since multiple OAuth2 flows (one per request id) can be in progress
     * concurrently within the same session.
     *
     * The username is cleaned here (the same way as the query-string username is cleaned in
     * login()), so that whatever later reads it back via {@see self::consume_login_error()} can
     * use it as-is, without needing to clean it again.
     *
     * @param string $requestid
     * @param string $username The username submitted with the failed attempt.
     */
    private function store_login_error(string $requestid, string $username): void {
        global $SESSION;

        $SESSION->oauth2loginerrors ??= [];
        $SESSION->oauth2loginerrors[$requestid] = [
            'username' => clean_param($username, PARAM_RAW),
        ];
    }

    /**
     * Helper to fetch and discard the invalid-login flash for the given request id, if any.
     *
     * Callers must treat the returned state as one-use: it is removed from the session as soon
     * as it is read, so that it is not shown again on a later render of the same request id.
     *
     * @param string $requestid
     * @return array{username: string}|null Null if there is no pending flash for this request id.
     */
    private function consume_login_error(string $requestid): ?array {
        global $SESSION;

        $loginerror = $SESSION->oauth2loginerrors[$requestid] ?? null;
        unset($SESSION->oauth2loginerrors[$requestid]);

        return $loginerror;
    }

    /**
     * Helper to render a page with header and footer.
     *
     * @param renderable $content
     * @param ResponseInterface $response
     * @param string|null $title
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
