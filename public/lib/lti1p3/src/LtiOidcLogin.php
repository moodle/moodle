<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Helpers\Helpers;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\ICookie;
use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\Interfaces\ILtiRegistration;

class LtiOidcLogin
{
    public const COOKIE_PREFIX = 'lti1p3_';
    public const ERROR_MSG_LAUNCH_URL = 'No launch URL configured';
    public const ERROR_MSG_ISSUER = 'Could not find issuer';
    public const ERROR_MSG_LOGIN_HINT = 'Could not find login hint';

    public function __construct(
        public IDatabase $db,
        public ICache $cache,
        public ICookie $cookie
    ) {}

    /**
     * Static function to allow for method chaining without having to assign to a variable first.
     */
    public static function new(IDatabase $db, ICache $cache, ICookie $cookie): self
    {
        return new LtiOidcLogin($db, $cache, $cookie);
    }

    /**
     * Calculate the redirect location to return to based on an OIDC third party initiated login request.
     */
    public function getRedirectUrl(string $launchUrl, array $request): string
    {
        // Validate request data.
        $registration = $this->validateOidcLogin($request);

        // Build OIDC Auth response.
        $authParams = $this->getAuthParams($launchUrl, $registration->getClientId(), $request);

        return Helpers::buildUrlWithQueryParams($registration->getAuthLoginUrl(), $authParams);
    }

    public function validateOidcLogin(array $request): ILtiRegistration
    {
        if (!isset($request['iss'])) {
            throw new OidcException(static::ERROR_MSG_ISSUER);
        }

        if (!isset($request['login_hint'])) {
            throw new OidcException(static::ERROR_MSG_LOGIN_HINT);
        }

        // Fetch registration
        $clientId = $request['client_id'] ?? null;
        $registration = $this->db->findRegistrationByIssuer($request['iss'], $clientId);

        if (!isset($registration)) {
            $errorMsg = LtiMessageLaunch::getMissingRegistrationErrorMsg($request['iss'], $clientId);

            throw new OidcException($errorMsg);
        }

        return $registration;
    }

    public function getAuthParams(string $launchUrl, string $clientId, array $request): array
    {
        // Set cookie (short lived)
        $state = static::secureRandomString('state-');
        $this->cookie->setCookie(static::COOKIE_PREFIX.$state, $state, 60);

        $nonce = static::secureRandomString('nonce-');
        $this->cache->cacheNonce($nonce, $state);

        $authParams = [
            'scope' => 'openid', // OIDC Scope.
            'response_type' => 'id_token', // OIDC response is always an id token.
            'response_mode' => 'form_post', // OIDC response is always a form post.
            'prompt' => 'none', // Don't prompt user on redirect.
            'client_id' => $clientId, // Registered client id.
            'redirect_uri' => $launchUrl, // URL to return to after login.
            'state' => $state, // State to identify browser session.
            'nonce' => $nonce, // Prevent replay attacks.
            'login_hint' => $request['login_hint'], // Login hint to identify platform session.
        ];

        if (isset($request['lti_message_hint'])) {
            // LTI message hint to identify LTI context within the platform.
            $authParams['lti_message_hint'] = $request['lti_message_hint'];
        }

        return $authParams;
    }

    public static function secureRandomString(string $prefix = ''): string
    {
        return $prefix.hash('sha256', random_bytes(64));
    }
}
