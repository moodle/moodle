<?php
/*
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Auth\Middleware;

use Google\Auth\FetchAuthTokenCache;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\GetQuotaProjectInterface;
use Google\Auth\UpdateMetadataInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;

/**
 * AuthTokenMiddleware is a Guzzle Middleware that adds an Authorization header
 * provided by an object implementing FetchAuthTokenInterface.
 *
 * The FetchAuthTokenInterface#fetchAuthToken is used to obtain a hash; one of
 * the values value in that hash is added as the authorization header.
 *
 * Requests will be accessed with the authorization header:
 *
 * 'authorization' 'Bearer <value of auth_token>'
 */
class AuthTokenMiddleware
{
    /**
     * @var callable
     */
    private $httpHandler;

    /**
     * It must be an implementation of FetchAuthTokenInterface.
     * It may also implement UpdateMetadataInterface allowing direct
     * retrieval of auth related headers
     * @var FetchAuthTokenInterface
     */
    private $fetcher;

    /**
     * @var ?callable
     */
    private $tokenCallback;

    /**
     * Creates a new AuthTokenMiddleware.
     *
     * @param FetchAuthTokenInterface $fetcher is used to fetch the auth token
     * @param callable|null $httpHandler (optional) callback which delivers psr7 request
     * @param callable|null $tokenCallback (optional) function to be called when a new token is fetched.
     */
    public function __construct(
        FetchAuthTokenInterface $fetcher,
        ?callable $httpHandler = null,
        ?callable $tokenCallback = null
    ) {
        $this->fetcher = $fetcher;
        $this->httpHandler = $httpHandler;
        $this->tokenCallback = $tokenCallback;
    }

    /**
     * Updates the request with an Authorization header when auth is 'google_auth'.
     *
     *   use Google\Auth\Middleware\AuthTokenMiddleware;
     *   use Google\Auth\OAuth2;
     *   use GuzzleHttp\Client;
     *   use GuzzleHttp\HandlerStack;
     *
     *   $config = [..<oauth config param>.];
     *   $oauth2 = new OAuth2($config)
     *   $middleware = new AuthTokenMiddleware($oauth2);
     *   $stack = HandlerStack::create();
     *   $stack->push($middleware);
     *
     *   $client = new Client([
     *       'handler' => $stack,
     *       'base_uri' => 'https://www.googleapis.com/taskqueue/v1beta2/projects/',
     *       'auth' => 'google_auth' // authorize all requests
     *   ]);
     *
     *   $res = $client->get('myproject/taskqueues/myqueue');
     *
     * @param callable $handler
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            // Requests using "auth"="google_auth" will be authorized.
            if (!isset($options['auth']) || $options['auth'] !== 'google_auth') {
                return $handler($request, $options);
            }

            $request = $this->addAuthHeaders($request);

            if ($quotaProject = $this->getQuotaProject()) {
                $request = $request->withHeader(
                    GetQuotaProjectInterface::X_GOOG_USER_PROJECT_HEADER,
                    $quotaProject
                );
            }

            return $handler($request, $options);
        };
    }

    /**
     * Adds auth related headers to the request.
     *
     * @param RequestInterface $request
     * @return RequestInterface
     */
    private function addAuthHeaders(RequestInterface $request)
    {
        if (!$this->fetcher instanceof UpdateMetadataInterface ||
            ($this->fetcher instanceof FetchAuthTokenCache &&
             !$this->fetcher->getFetcher() instanceof UpdateMetadataInterface)
        ) {
            $token = $this->fetcher->fetchAuthToken();
            $request = $request->withHeader(
                'authorization',
                'Bearer ' . ($token['access_token'] ?? $token['id_token'] ?? '')
            );
        } else {
            $headers = $this->fetcher->updateMetadata($request->getHeaders(), null, $this->httpHandler);
            $request = Utils::modifyRequest($request, ['set_headers' => $headers]);
        }

        if ($this->tokenCallback && ($token = $this->fetcher->getLastReceivedToken())) {
            if (array_key_exists('access_token', $token)) {
                call_user_func($this->tokenCallback, $this->fetcher->getCacheKey(), $token['access_token']);
            }
        }

        return $request;
    }

    /**
     * @return string|null
     */
    private function getQuotaProject()
    {
        if ($this->fetcher instanceof GetQuotaProjectInterface) {
            return $this->fetcher->getQuotaProject();
        }

        return null;
    }
}
