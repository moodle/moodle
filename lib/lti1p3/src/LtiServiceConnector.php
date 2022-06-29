<?php

namespace Packback\Lti1p3;

use Firebase\JWT\JWT;
use Packback\Lti1p3\Interfaces\ICache;
use Packback\Lti1p3\Interfaces\IHttpClient;
use Packback\Lti1p3\Interfaces\IHttpException;
use Packback\Lti1p3\Interfaces\IHttpResponse;
use Packback\Lti1p3\Interfaces\ILtiRegistration;
use Packback\Lti1p3\Interfaces\ILtiServiceConnector;
use Packback\Lti1p3\Interfaces\IServiceRequest;

class LtiServiceConnector implements ILtiServiceConnector
{
    public const NEXT_PAGE_REGEX = '/<([^>]*)>; ?rel="next"/i';

    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    private $cache;
    private $client;
    private $debuggingMode = false;

    public function __construct(ICache $cache, IHttpClient $client)
    {
        $this->cache = $cache;
        $this->client = $client;
    }

    public function setDebuggingMode(bool $enable): void
    {
        $this->debuggingMode = $enable;
    }

    public function getAccessToken(ILtiRegistration $registration, array $scopes)
    {
        // Get a unique cache key for the access token
        $accessTokenKey = $this->getAccessTokenCacheKey($registration, $scopes);
        // Get access token from cache if it exists
        $accessToken = $this->cache->getAccessToken($accessTokenKey);
        if ($accessToken) {
            return $accessToken;
        }

        // Build up JWT to exchange for an auth token
        $clientId = $registration->getClientId();
        $jwtClaim = [
            'iss' => $clientId,
            'sub' => $clientId,
            'aud' => $registration->getAuthServer(),
            'iat' => time() - 5,
            'exp' => time() + 60,
            'jti' => 'lti-service-token'.hash('sha256', random_bytes(64)),
        ];

        // Sign the JWT with our private key (given by the platform on registration)
        $jwt = JWT::encode($jwtClaim, $registration->getToolPrivateKey(), 'RS256', $registration->getKid());

        // Build auth token request headers
        $authRequest = [
            'grant_type' => 'client_credentials',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => $jwt,
            'scope' => implode(' ', $scopes),
        ];

        $url = $registration->getAuthTokenUrl();

        // Get Access
        $tokenRequest = new ServiceRequest('POST', $url);
        $tokenRequest->setBody(http_build_query($authRequest, '', '&'));
        $tokenRequest->setContentType('application/x-www-form-urlencoded');
        $tokenRequest->setAccept('application/json');
        $response = $this->client->request(
            $tokenRequest->getMethod(),
            $tokenRequest->getUrl(),
            [
                'headers' => $tokenRequest->getPayload()['headers'],
                'body' => $tokenRequest->getPayload()['body']
            ]
        );
        $tokenData = $this->getResponseBody($response);

        // Cache access token
        $this->cache->cacheAccessToken($accessTokenKey, $tokenData['access_token']);

        return $tokenData['access_token'];
    }


    public function makeRequest(IServiceRequest $request)
    {
        return $this->client->request(
            $request->getMethod(),
            $request->getUrl(),
            $request->getPayload()
        );
    }

    public function getResponseBody(IHttpResponse $response): ?array
    {
        $responseBody = (string) $response->getBody();

        return json_decode($responseBody, true);
    }

    public function makeServiceRequest(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        bool $shouldRetry = true
    ): array {
        $request->setAccessToken($this->getAccessToken($registration, $scopes));
        try {
            $response = $this->makeRequest($request);
        } catch (IHttpException $e) {
            $status = $e->getResponse()->getStatusCode();
            // If the error was due to invalid authentication and the request
            // should be retried, clear the access token and retry it.
            if ($status === 401 && $shouldRetry) {
                $key = $this->getAccessTokenCacheKey($registration, $scopes);
                $this->cache->clearAccessToken($key);

                return $this->makeServiceRequest($registration, $scopes, $request, false);
            }
            throw $e;
        }

        $responseHeaders = $response->getHeaders();
        $responseBody = $this->getResponseBody($response);

        if ($this->debuggingMode) {
            error_log('Syncing grade for this lti_user_id: '.
                json_decode($request->getPayload()['body'])->userId.' '.print_r([
                    'request_method' => $request->getMethod(),
                    'request_url' => $request->getUrl(),
                    'request_body' => $request->getPayload()['body'],
                    'response_headers' => $responseHeaders,
                    'response_body' => json_encode($responseBody),
                ], true));
        }

        return [
            'headers' => $responseHeaders,
            'body' => $responseBody,
            'status' => $response->getStatusCode(),
        ];
    }

    public function getAll(
        ILtiRegistration $registration,
        array $scopes,
        IServiceRequest $request,
        string $key = null
    ): array {
        if ($request->getMethod() !== static::METHOD_GET) {
            throw new \Exception('An invalid method was specified by an LTI service requesting all items.');
        }

        $results = [];
        $nextUrl = $request->getUrl();

        while ($nextUrl) {
            $response = $this->makeServiceRequest($registration, $scopes, $request);

            $page_results = $key === null ? ($response['body'] ?? []) : ($response['body'][$key] ?? []);
            $results = array_merge($results, $page_results);

            $nextUrl = $this->getNextUrl($response['headers']);
            if ($nextUrl) {
                $request->setUrl($nextUrl);
            }
        }

        return $results;
    }

    private function getAccessTokenCacheKey(ILtiRegistration $registration, array $scopes)
    {
        sort($scopes);
        $scopeKey = md5(implode('|', $scopes));

        return $registration->getIssuer().$registration->getClientId().$scopeKey;
    }

    private function getNextUrl(array $headers)
    {
        $subject = $headers['Link'] ?? '';
        preg_match(LtiServiceConnector::NEXT_PAGE_REGEX, $subject, $matches);

        return $matches[1] ?? null;
    }
}
