<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;

class LtiOidcLogin
{
    public const COOKIE_PREFIX = 'lti1p3_';
    public const ERROR_MSG_LAUNCH_URL = 'No launch URL configured';
    public const ERROR_MSG_ISSUER = 'Could not find issuer';
    public const ERROR_MSG_LOGIN_HINT = 'Could not find login hint';
    private $db;
    private $cache;
    private $cookie;

    /**
     * Constructor.
     *
     * @param  IDatabase  $database Instance of the Database interface used for looking up registrations and deployments
     * @param  ICache  $cache    instance of the Cache interface used to loading and storing launches
     * @param  ICookie  $cookie   instance of the Cookie interface used to set and read cookies
     */
    public function __construct(IDatabase $database, ICache $cache = null, ICookie $cookie = null)
    {
        $this->db = $database;
        $this->cache = $cache;
        $this->cookie = $cookie;
    }

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(IDatabase $database, ICache $cache = null, ICookie $cookie = null)
    {
        return new LtiOidcLogin($database, $cache, $cookie);
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     *
     * @param  string  $launch_url URL to redirect back to after the OIDC login. This URL must match exactly a URL white listed in the platform.
     * @param  array|string  $request    An array of request parameters. If not set will default to $_REQUEST.
     * @return Redirect returns a redirect object containing the fully formed OIDC login URL
     */
    public function doOidcLoginRedirect($launch_url, array $request = null)
    {
        if ($request === null) {
            $request = $_REQUEST;
        }

        if (empty($launch_url)) {
            throw new OidcException(static::ERROR_MSG_LAUNCH_URL, 1);
        }

        // Validate Request Data.
        $registration = $this->validateOidcLogin($request);

        /*
         * Build OIDC Auth Response.
         */

        // Generate State.
        // Set cookie (short lived)
        $state = static::secureRandomString('state-');
        $this->cookie->setCookie(static::COOKIE_PREFIX.$state, $state, 60);

        // Generate Nonce.
        $nonce = static::secureRandomString('nonce-');
        $this->cache->cacheNonce($nonce, $state);

        // Build Response.
        $auth_params = [
            'scope' => 'openid', // OIDC Scope.
            'response_type' => 'id_token', // OIDC response is always an id token.
            'response_mode' => 'form_post', // OIDC response is always a form post.
            'prompt' => 'none', // Don't prompt user on redirect.
            'client_id' => $registration->getClientId(), // Registered client id.
            'redirect_uri' => $launch_url, // URL to return to after login.
            'state' => $state, // State to identify browser session.
            'nonce' => $nonce, // Prevent replay attacks.
            'login_hint' => $request['login_hint'], // Login hint to identify platform session.
        ];

        // Pass back LTI message hint if we have it.
        if (isset($request['lti_message_hint'])) {
            // LTI message hint to identify LTI context within the platform.
            $auth_params['lti_message_hint'] = $request['lti_message_hint'];
        }

        if (parse_url($registration->getAuthLoginUrl(), PHP_URL_QUERY)) {
            $separator = '&';
        } else {
            $separator = '?';
        }
        $auth_login_return_url = $registration->getAuthLoginUrl().$separator.http_build_query($auth_params, '', '&');

        // Return auth redirect.
        return new Redirect($auth_login_return_url, http_build_query($request, '', '&'));
    }

    public function validateOidcLogin($request)
    {
        // Validate Issuer.
        if (empty($request['iss'])) {
            throw new OidcException(static::ERROR_MSG_ISSUER, 1);
        }

        // Validate Login Hint.
        if (empty($request['login_hint'])) {
            throw new OidcException(static::ERROR_MSG_LOGIN_HINT, 1);
        }

        // Fetch Registration Details.
        $clientId = $request['client_id'] ?? null;
        $registration = $this->db->findRegistrationByIssuer($request['iss'], $clientId);

        // Check we got something.
        if (empty($registration)) {
            $errorMsg = LtiMessageLaunch::getMissingRegistrationErrorMsg($request['iss'], $clientId);

            throw new OidcException($errorMsg, 1);
        }

        // Return Registration.
        return $registration;
    }

    public static function secureRandomString(string $prefix = ''): string
    {
        return $prefix.hash('sha256', random_bytes(64));
    }
}
