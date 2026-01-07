<?php
/*
 * Copyright 2019 Google LLC
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

namespace Google\Auth;

use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Utils;

/**
 * Tools for using the IAM API.
 *
 * @see https://cloud.google.com/iam/docs IAM Documentation
 */
class Iam
{
    /**
     * @deprecated
     */
    const IAM_API_ROOT = 'https://iamcredentials.googleapis.com/v1';
    const SIGN_BLOB_PATH = '%s:signBlob?alt=json';
    const SERVICE_ACCOUNT_NAME = 'projects/-/serviceAccounts/%s';
    private const IAM_API_ROOT_TEMPLATE = 'https://iamcredentials.UNIVERSE_DOMAIN/v1';
    private const GENERATE_ID_TOKEN_PATH = '%s:generateIdToken';

    /**
     * @var callable
     */
    private $httpHandler;

    private string $universeDomain;

    /**
     * @param callable|null $httpHandler [optional] The HTTP Handler to send requests.
     */
    public function __construct(
        ?callable $httpHandler = null,
        string $universeDomain = GetUniverseDomainInterface::DEFAULT_UNIVERSE_DOMAIN
    ) {
        $this->httpHandler = $httpHandler
            ?: HttpHandlerFactory::build(HttpClientCache::getHttpClient());
        $this->universeDomain = $universeDomain;
    }

    /**
     * Sign a string using the IAM signBlob API.
     *
     * Note that signing using IAM requires your service account to have the
     * `iam.serviceAccounts.signBlob` permission, part of the "Service Account
     * Token Creator" IAM role.
     *
     * @param string $email The service account email.
     * @param string $accessToken An access token from the service account.
     * @param string $stringToSign The string to be signed.
     * @param array<string> $delegates [optional] A list of service account emails to
     *        add to the delegate chain. If omitted, the value of `$email` will
     *        be used.
     * @return string The signed string, base64-encoded.
     */
    public function signBlob($email, $accessToken, $stringToSign, array $delegates = [])
    {
        $name = sprintf(self::SERVICE_ACCOUNT_NAME, $email);
        $apiRoot = str_replace('UNIVERSE_DOMAIN', $this->universeDomain, self::IAM_API_ROOT_TEMPLATE);
        $uri = $apiRoot . '/' . sprintf(self::SIGN_BLOB_PATH, $name);

        if ($delegates) {
            foreach ($delegates as &$delegate) {
                $delegate = sprintf(self::SERVICE_ACCOUNT_NAME, $delegate);
            }
        } else {
            $delegates = [$name];
        }

        $body = [
            'delegates' => $delegates,
            'payload' => base64_encode($stringToSign),
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken
        ];

        $request = new Psr7\Request(
            'POST',
            $uri,
            $headers,
            Utils::streamFor(json_encode($body))
        );

        $res = ($this->httpHandler)($request);
        $body = json_decode((string) $res->getBody(), true);

        return $body['signedBlob'];
    }

    /**
     * Sign a string using the IAM signBlob API.
     *
     * Note that signing using IAM requires your service account to have the
     * `iam.serviceAccounts.signBlob` permission, part of the "Service Account
     * Token Creator" IAM role.
     *
     * @param string $clientEmail The service account email.
     * @param string $targetAudience The audience for the ID token.
     * @param string $bearerToken The token to authenticate the IAM request.
     * @param array<string, string> $headers [optional] Additional headers to send with the request.
     *
     * @return string The signed string, base64-encoded.
     */
    public function generateIdToken(
        string $clientEmail,
        string $targetAudience,
        string $bearerToken,
        array $headers = []
    ): string {
        $name = sprintf(self::SERVICE_ACCOUNT_NAME, $clientEmail);
        $apiRoot = str_replace('UNIVERSE_DOMAIN', $this->universeDomain, self::IAM_API_ROOT_TEMPLATE);
        $uri = $apiRoot . '/' . sprintf(self::GENERATE_ID_TOKEN_PATH, $name);

        $headers['Authorization'] = 'Bearer ' . $bearerToken;

        $body = [
            'audience' => $targetAudience,
            'includeEmail' => true,
            'useEmailAzp' => true,
        ];

        $request = new Psr7\Request(
            'POST',
            $uri,
            $headers,
            Utils::streamFor(json_encode($body))
        );

        $res = ($this->httpHandler)($request);
        $body = json_decode((string) $res->getBody(), true);

        return $body['token'];
    }
}
