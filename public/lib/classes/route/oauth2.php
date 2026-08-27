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
use core\oauth2\server\token_revoker;
use core\output\renderable;
use core\router\route;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\ClientEntityInterface;
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
            // Whether the live session user has already been confirmed for this exact pending
            // request - i.e. do_login() has already run for this authrequestid and this user,
            // either via a fresh credential login or a 'continue as current user' submission.
            // This is checked against the request as restored from the session, before it is
            // overwritten below: do_login() always persists the user it just confirmed before
            // redirecting back here, so a match here means this visit to authorize() is that
            // same redirect coming back, not a fresh, unconfirmed arrival.
            $currentuser = $userrepository->get_current_user();
            $previoususer = $authrequest->getUser();
            $userconfirmedforthisrequest = $previoususer !== null
                && $previoususer->getIdentifier() === $currentuser->getIdentifier();

            // Set the user on the auth request. This is always freshly derived from the live
            // session ($USER) here, never trusted from the previously stored request, so
            // has_granted_all_scopes() below can only silently complete this request for the
            // actual logged-in, non-guest user - never a guest or some other, stale identity.
            $authrequest->setUser($currentuser);

            // Persist the freshly-derived user before require_login() runs, so that if it
            // redirects away, the pending request already reflects this user when the browser
            // is eventually brought back here.
            $this->store_auth_request($requestid, $authrequest);

            // This method is the single gate every logged-in visit to this flow passes through
            // (do_login() always redirects back here rather than completing anything itself),
            // so this is the one place that needs to confirm the live session satisfies
            // Moodle's mandatory account-policy requirements (forced password change,
            // incomplete profile, site policy agreement) before anything - silent completion or
            // the consent screen - is allowed to proceed. If any apply, require_login() redirects
            // the browser away and sets $SESSION->wantsurl to this exact
            // /authorize?authrequestid=... URL, so the user is brought straight back here, with
            // the same authrequestid, once the requirement is resolved. Called with no course
            // (site-level check only) and autologinguest disabled (a session already confirmed
            // logged-in and non-guest above must never be silently replaced with a guest one).
            require_login(null, false);

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

            if ($userconfirmedforthisrequest) {
                // This user has already confirmed (via do_login()) that they wish to continue as
                // themselves for this exact request: proceed straight to the scope-consent
                // screen, rather than showing that same "continue as this user?" confirmation
                // again - which, for a 'continue as current user' submission, would otherwise
                // redirect back here in an endless loop.
                return \core\router\util::redirect_to_callable(
                    $request,
                    $response,
                    [self::class, 'approve'],
                    queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
                );
            }

            // Redirect to the login page to confirm that the user wishes to continue as this user.
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
     * Revoke a credential, per RFC 7009. Only problems with the caller are reported.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param token_revoker $revoker
     * @return ResponseInterface
     */
    #[route(
        path: '/revoke',
        method: ['POST'],
    )]
    public function revoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        token_revoker $revoker,
    ): ResponseInterface {
        try {
            // RFC 7009 section 2.1 fixes the wire format, so a body in any other format is refused
            // rather than guessed at. The header may carry parameters such as "; charset=UTF-8".
            $mediatype = explode(';', $request->getHeaderLine('Content-Type'), 2)[0];

            if (strtolower(trim($mediatype)) !== 'application/x-www-form-urlencoded') {
                throw OAuthServerException::invalidRequest('Content-Type');
            }

            $body = $this->get_form_parameters($request);
            [$clientidentifier, $clientsecret] = $this->get_client_credentials($request, $body);

            if ($clientidentifier === null || $clientidentifier === '') {
                throw OAuthServerException::invalidClient($request);
            }

            $client = $this->clientrepository->getClientEntity($clientidentifier);

            if ($client === null) {
                throw OAuthServerException::invalidClient($request);
            }

            // Only a confidential client holds a secret to prove itself with. A public client cannot,
            // so it is taken at its word here and constrained instead by ownership: the revoker will
            // not touch a credential that was not issued to this client identifier.
            if (
                $client->isConfidential()
                && !$this->clientrepository->validateClient($clientidentifier, $clientsecret, null)
            ) {
                throw OAuthServerException::invalidClient($request);
            }

            $token = $this->get_string_parameter($body, 'token');

            if ($token === null || $token === '') {
                throw OAuthServerException::invalidRequest('token');
            }

            $revoker->revoke($token, $clientidentifier, $this->get_string_parameter($body, 'token_type_hint'));

            return $response;
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response.
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            // Unknown exception. Log the real message server-side, but do not expose internal
            // details (e.g. database errors, paths, class names) to the OAuth client.
            debugging('Unhandled exception in OAuth2 revocation endpoint: ' . $exception->getMessage(), DEBUG_DEVELOPER);
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
            if (!empty($loginerror['unconfirmed'])) {
                // Correct credentials, but this account has never been confirmed - materially
                // different from a bad username/password, so show a message that reflects that,
                // reusing the same string login/lib.php already uses for exactly this situation
                // elsewhere (unlike login/index.php's own full unconfirmed-account handling,
                // this deliberately does not expose the account's email address or offer to
                // resend the confirmation email - both are out of scope here).
                $loginform->set_error(get_string('confirmednot'));
            } else {
                // Precise authentication failure codes (unauthorised, recaptcha, etc.) are out of
                // scope here; always show the same generic invalid-login error shown by the
                // standalone login/index.php flow for a bad username/password.
                $loginform->set_error('', AUTH_LOGIN_FAILED);
            }
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
        global $USER;

        // Handle the login form submission.
        [$requestid, $authrequest] = $this->get_auth_request($request);

        $parsedbody = $request->getParsedBody();

        $user = null;
        $unconfirmed = false;
        if (($parsedbody['currentuser'] ?? '') === '1') {
            // Continue as the current user.
            // This is a state-changing action performed using only the ambient session cookie, so it must be
            // protected against CSRF. The continue_as_user_page always includes a sesskey field for this purpose.
            \core\router\util::require_sesskey($request);

            // A valid sesskey alone does not prove there is a genuine, non-guest session to
            // continue as: guest sessions carry a valid sesskey too, and the session may since
            // have been logged out entirely. Leave $user as null (falling through to the same
            // failed-login handling used below) rather than trusting get_current_user() to
            // return some other default identity in either case. A genuinely logged-in session
            // should not normally exist for an unconfirmed account in the first place, so - unlike
            // the credential branch below - this path does not need its own confirmation check;
            // the live-session checks above remain authoritative.
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
                if (empty($moodleuser->confirmed)) {
                    // Valid credentials, but this account has never been confirmed. Matching
                    // login/index.php's own ordering, this must be checked before
                    // complete_user_login() is ever called: correct credentials alone do not
                    // establish a session through OAuth if the standalone login form would not
                    // have allowed it either. Leave $user as null, falling through to the same
                    // failed-login handling used below, but flagged so that handling can show an
                    // accurate message rather than the generic invalid-login one.
                    $unconfirmed = true;
                } else {
                    // Determine whether this user's password is hard-expired (and, if so, how it
                    // can be changed) before establishing a session, exactly as login/index.php
                    // determines the same thing before showing its own expiry notice. This is
                    // deliberately checked here, ahead of complete_user_login(), for the two
                    // outcomes below that must never establish a usable session at all.
                    $passwordexpiry = \core\di::get(\core_auth\password_expiry_checker::class)->check($moodleuser);

                    if ($passwordexpiry->state === \core_auth\password_expiry_state::HARD_EXPIRED_UNSUPPORTED) {
                        // The plugin cannot change this password at all, internally or
                        // externally: there is no requirement this flow can wait to be resolved,
                        // so - unlike every other failure path here - the pending request is
                        // discarded outright rather than preserved for a later retry, and no
                        // session is established at all. This reuses the same error Moodle's own
                        // require_login() would throw for this exact plugin configuration
                        // elsewhere in this flow (see authorize()), so the message shown is
                        // already an established, generic one that does not expose plugin
                        // internals.
                        $this->forget_auth_request($requestid);
                        throw new \moodle_exception('nopasswordchangeforced', 'auth');
                    }

                    if ($passwordexpiry->state === \core_auth\password_expiry_state::HARD_EXPIRED_EXTERNAL) {
                        // Deliberately never establish a session for this login attempt at all
                        // (unlike login/index.php, which always logs in first and only ends the
                        // session again afterwards): a full require_logout() would discard the
                        // *entire* session, including anything unrelated to this request (for
                        // example, another pending flow's own wantsurl), which is more than this
                        // situation calls for. Not logging in in the first place achieves the
                        // same outcome - no session capable of authorizing this client - without
                        // that collateral effect. There is no way for Moodle to know when, or
                        // whether, the user actually changes their password on the external site,
                        // so - unlike every other failure path here - the pending request is not
                        // preserved for a later retry; the user must restart authorization from
                        // the client instead.
                        return $this->terminate_for_external_password_expiry(
                            $response,
                            $requestid,
                            $authrequest->getClient(),
                            $passwordexpiry->externalchangepasswordurl,
                        );
                    }

                    complete_user_login($moodleuser);

                    if ($passwordexpiry->state === \core_auth\password_expiry_state::HARD_EXPIRED_INTERNAL) {
                        // Set the same preference login/index.php would for this same situation,
                        // then let authorize()'s own, already-integrated require_login() call
                        // (see authorize()) take over from here: it will redirect to Moodle's
                        // internal change-password page, pointing $SESSION->wantsurl at this
                        // exact /authorize?authrequestid=... URL first, so the flow resumes
                        // automatically once the password has been changed.
                        set_user_preference('auth_forcepasswordchange', 1, $USER);
                    }

                    $user = $userrepository->get_current_user();
                }
            }
        }

        if ($user === null) {
            // Login failed (either invalid credentials, or valid credentials for an unconfirmed
            // account). Record a one-use flash (error + submitted username + whether this was
            // specifically the unconfirmed-account case), scoped to this request id, so the
            // redisplayed login form can show the right message, then redirect back to the login
            // form using the existing POST-redirect-GET pattern. The pending OAuth authorization
            // request itself (stored separately under this same request id) is left completely
            // untouched - no user is attached to it - so a subsequent valid login can still
            // continue the flow.
            $this->store_login_error($requestid, (string) ($parsedbody['username'] ?? ''), $unconfirmed);

            return \core\router\util::redirect_to_callable(
                $request,
                $response,
                [self::class, 'login'],
                queryparams: array_merge($request->getQueryParams(), [self::REQUEST_ID_PARAM => $requestid]),
            );
        }

        // The authenticated user is always the live session user here: either just established
        // via complete_user_login() above, or (for 'continue as current user') only ever
        // assigned once isloggedin() && !isguestuser() has been confirmed.
        $authrequest->setUser($user);
        $this->store_auth_request($requestid, $authrequest);

        // This method never completes authorization, silently or otherwise, itself: every
        // successful login (both the username/password and 'continue as current user' paths)
        // returns through authorize(), which is the single place that checks account-policy
        // requirements (via require_login()) and already-granted scopes before anything can
        // proceed. This also means at least one further request always happens after
        // complete_user_login() before a token can be issued, which gives hooks that only fire
        // on ordinary page loads (e.g. MFA's bootstrap-time hook) a chance to run - though
        // nothing here depends on or calls into MFA directly.
        return \core\router\util::redirect_to_callable(
            $request,
            $response,
            [self::class, 'authorize'],
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

        // Defensive gate: authorize() already checked this when the flow first reached a
        // logged-in session, but this request could be reached again later (or hit directly),
        // and an account-policy requirement (forced password change, incomplete profile, site
        // policy) could have newly arisen for this same, still-correctly-identified user since
        // then - for example, an admin force-flagging a password change mid-flow. See
        // authorize() for how require_login() sets $SESSION->wantsurl to bring the browser back
        // here, to this exact /approve?authrequestid=... URL, once resolved.
        require_login(null, false);

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

        // Defensive gate: see approve() for why this is needed here too, even though
        // authorize() already checked it earlier in the same flow.
        require_login(null, false);

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
     * Helper to end the given pending request for a user whose password has hard-expired on a
     * plugin with an external change-password destination, and show them where to go next.
     *
     * Unlike {@see self::complete_authorization_request()}, this never approves or completes the
     * request against the OAuth2 server - it only discards it (see {@see self::forget_auth_request()})
     * so it cannot later be replayed, since there is no way for Moodle to know when, or whether,
     * the user actually changes their password on the external site. No Moodle session is ever
     * established for this login attempt in the first place (see {@see self::do_login()}), so
     * there is nothing left afterwards that could be used to authorize the client.
     *
     * @param ResponseInterface $response
     * @param string $requestid
     * @param ClientEntityInterface $client
     * @param \core\url $externalchangepasswordurl
     * @return ResponseInterface
     */
    private function terminate_for_external_password_expiry(
        ResponseInterface $response,
        string $requestid,
        ClientEntityInterface $client,
        \core\url $externalchangepasswordurl,
    ): ResponseInterface {
        $this->forget_auth_request($requestid);

        return $this->render_external_password_expiry_page($response, $client, $externalchangepasswordurl);
    }

    /**
     * Helper to render the notice shown when {@see self::terminate_for_external_password_expiry()}
     * has ended a pending request because the user's password is hard-expired on a plugin with an
     * external change-password destination.
     *
     * Reuses $OUTPUT->confirm(), exactly as login/index.php does for its own external-hard-expiry
     * notice: a "Continue" button to the plugin's own change-password destination, and a
     * "Cancel" button back to the site front page.
     *
     * A separate, protected (and so overridable/stubbable in tests) method for exactly the same
     * reason {@see self::render_page_from_renderable()} is: real page rendering is exercised
     * separately by login_test.php, so tests of this route's own wiring can stub this out.
     *
     * @param ResponseInterface $response
     * @param ClientEntityInterface $client
     * @param \core\url $externalchangepasswordurl
     * @return ResponseInterface
     */
    protected function render_external_password_expiry_page(
        ResponseInterface $response,
        ClientEntityInterface $client,
        \core\url $externalchangepasswordurl,
    ): ResponseInterface {
        global $OUTPUT, $PAGE;

        $PAGE->set_pagelayout('login');
        $PAGE->set_context(\core\context\system::instance());

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->confirm(
            get_string('oauth2:passwordexpiredexternal', 'moodle', $client->getName()),
            $externalchangepasswordurl,
            new \core\url('/'),
        ));
        $response->getBody()->write($OUTPUT->footer());

        return $response;
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
     * Read the form-urlencoded parameters from a request body.
     *
     * The route declares no request body schema, so the parsed body is discarded and the raw body
     * is read instead.
     *
     * @param ServerRequestInterface $request
     * @return array The submitted parameters.
     */
    private function get_form_parameters(ServerRequestInterface $request): array {
        parse_str((string) $request->getBody(), $parameters);

        return $parameters;
    }

    /**
     * Read a single string parameter from a submitted body, treating a non-string as absent.
     *
     * @param array $body The submitted parameters.
     * @param string $name The parameter to read.
     * @return string|null The value, or null if it was absent or not a string.
     */
    private function get_string_parameter(array $body, string $name): ?string {
        return isset($body[$name]) && is_string($body[$name]) ? $body[$name] : null;
    }

    /**
     * Read the calling client's credentials, preferring HTTP Basic per RFC 6749 section 2.3.1.
     *
     * @param ServerRequestInterface $request
     * @param array $body The submitted parameters.
     * @return array{0: string|null, 1: string|null} The client identifier and secret, either of which may be null.
     */
    private function get_client_credentials(ServerRequestInterface $request, array $body): array {
        $header = $request->getHeaderLine('Authorization');

        if (stripos($header, 'Basic ') === 0) {
            $decoded = base64_decode(substr($header, 6), true);

            if ($decoded === false || !str_contains($decoded, ':')) {
                return [null, null];
            }

            // RFC 6749 section 2.3.1 form-encodes both halves before they are base64 encoded.
            [$identifier, $secret] = explode(':', $decoded, 2);

            return [urldecode($identifier), urldecode($secret)];
        }

        return [
            $this->get_string_parameter($body, 'client_id'),
            $this->get_string_parameter($body, 'client_secret'),
        ];
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
     * Also clears $SESSION->wantsurl, but only if it is still exactly one of this same request's
     * own OAuth URLs (see {@see self::wantsurl_is_for_request()}) - either the authorize URL that
     * login() stored (reusing the site-wide wantsurl slot so that an external IdP-style auth
     * plugin can redirect back to /authorize after login, but never restoring it afterwards), or
     * an authorize/approve URL that require_login() stored while resolving a mandatory
     * account-policy requirement (forced password change, incomplete profile, site policy) for
     * this request's user. Once this request is forgotten, any such URL is dangling and would
     * otherwise be picked up by a later, unrelated ordinary login and mistaken for its own
     * destination, starting a brand new OAuth flow. If wantsurl no longer points at this request
     * (another tab or route has since replaced it with something else, or it was never set) it is
     * left untouched.
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
     * Helper to determine whether the given wantsurl value is one of this flow's own OAuth URLs
     * for the given request id - either the authorize URL that login() would have stored, or the
     * authorize/approve URL that require_login() would have stored while resolving a mandatory
     * account-policy requirement partway through the flow.
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
            // Not a URL \core\url can parse at all, so it is definitely not one of the ones
            // this flow itself would have stored.
            return false;
        }

        $oauthurls = [
            \core\router\util::get_path_for_callable([self::class, 'authorize']),
            \core\router\util::get_path_for_callable([self::class, 'approve']),
        ];

        $matchesownurl = false;
        foreach ($oauthurls as $oauthurl) {
            if ($url->compare($oauthurl, URL_MATCH_BASE)) {
                $matchesownurl = true;
                break;
            }
        }

        if (!$matchesownurl) {
            // Some other destination entirely (e.g. the originally-requested protected page, or
            // a different route), not one of this flow's own OAuth routes.
            return false;
        }

        return $url->get_param(self::REQUEST_ID_PARAM) === $requestid;
    }

    /**
     * Helper to store a one-use invalid-login flash (the error to display, the username that was
     * submitted, and whether this was specifically valid credentials for an unconfirmed account
     * rather than invalid credentials) for the given request id.
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
     * @param bool $unconfirmed Whether this was valid credentials for an unconfirmed account,
     *      rather than invalid credentials.
     */
    private function store_login_error(string $requestid, string $username, bool $unconfirmed = false): void {
        global $SESSION;

        $SESSION->oauth2loginerrors ??= [];
        $SESSION->oauth2loginerrors[$requestid] = [
            'username' => clean_param($username, PARAM_RAW),
            'unconfirmed' => $unconfirmed,
        ];
    }

    /**
     * Helper to fetch and discard the invalid-login flash for the given request id, if any.
     *
     * Callers must treat the returned state as one-use: it is removed from the session as soon
     * as it is read, so that it is not shown again on a later render of the same request id.
     *
     * @param string $requestid
     * @return array{username: string, unconfirmed: bool}|null Null if there is no pending flash
     *      for this request id.
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
