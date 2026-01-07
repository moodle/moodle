<?php

/*
 * Copyright 2022 Google Inc.
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

namespace Google\Auth\Credentials;

use Google\Auth\CacheTrait;
use Google\Auth\CredentialsLoader;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\GetUniverseDomainInterface;
use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Auth\IamSignerTrait;
use Google\Auth\SignBlobInterface;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use LogicException;

/**
 * **IMPORTANT**:
 * This class does not validate the credential configuration. A security
 * risk occurs when a credential configuration configured with malicious urls
 * is used.
 * When the credential configuration is accepted from an
 * untrusted source, you should validate it before creating this class.
 * @see https://cloud.google.com/docs/authentication/external/externally-sourced-credentials
 */
class ImpersonatedServiceAccountCredentials extends CredentialsLoader implements
    SignBlobInterface,
    GetUniverseDomainInterface
{
    use CacheTrait;
    use IamSignerTrait;

    private const CRED_TYPE = 'imp';
    private const IAM_SCOPE = 'https://www.googleapis.com/auth/iam';
    private const ID_TOKEN_IMPERSONATION_URL =
        'https://iamcredentials.UNIVERSE_DOMAIN/v1/projects/-/serviceAccounts/%s:generateIdToken';

    /**
     * @var string
     */
    protected $impersonatedServiceAccountName;

    protected FetchAuthTokenInterface $sourceCredentials;

    private string $serviceAccountImpersonationUrl;

    /**
     * @var string[]
     */
    private array $delegates;

    /**
     * @var string|string[]
     */
    private string|array $targetScope;

    private int $lifetime;

    /**
     * Instantiate an instance of ImpersonatedServiceAccountCredentials from a credentials file that
     * has be created with the --impersonate-service-account flag.
     *
     * @param string|string[]|null $scope   The scope of the access request, expressed either as an
     *                                      array or as a space-delimited string.
     * @param string|array<mixed>  $jsonKey JSON credential file path or JSON array credentials {
     *     JSON credentials as an associative array.
     *
     *     @type string                         $service_account_impersonation_url The URL to the service account
     *     @type string|FetchAuthTokenInterface $source_credentials The source credentials to impersonate
     *     @type int                            $lifetime The lifetime of the impersonated credentials
     *     @type string[]                       $delegates The delegates to impersonate
     * }
     * @param string|null $targetAudience The audience to request an ID token.
     * @param string|string[]|null $defaultScope The scopes to be used if no "scopes" field exists
     *                                           in the `$jsonKey`.
     */
    public function __construct(
        string|array|null $scope,
        string|array $jsonKey,
        private ?string $targetAudience = null,
        string|array|null $defaultScope = null,
    ) {
        if (is_string($jsonKey)) {
            if (!file_exists($jsonKey)) {
                throw new InvalidArgumentException('file does not exist');
            }
            $json = file_get_contents($jsonKey);
            if (!$jsonKey = json_decode((string) $json, true)) {
                throw new LogicException('invalid json for auth config');
            }
        }
        if (!array_key_exists('service_account_impersonation_url', $jsonKey)) {
            throw new LogicException(
                'json key is missing the service_account_impersonation_url field'
            );
        }
        if (!array_key_exists('source_credentials', $jsonKey)) {
            throw new LogicException('json key is missing the source_credentials field');
        }

        $jsonKeyScope = $jsonKey['scopes'] ?? null;
        $scope = $scope ?: $jsonKeyScope ?: $defaultScope;
        if ($scope && $targetAudience) {
            throw new InvalidArgumentException(
                'Scope and targetAudience cannot both be supplied'
            );
        }
        if (is_array($jsonKey['source_credentials'])) {
            if (!array_key_exists('type', $jsonKey['source_credentials'])) {
                throw new InvalidArgumentException('json key source credentials are missing the type field');
            }
            if (
                $targetAudience !== null
                && $jsonKey['source_credentials']['type'] === 'service_account'
            ) {
                // Service account tokens MUST request a scope, and as this token is only used to impersonate
                // an ID token, the narrowest scope we can request is `iam`.
                $scope = self::IAM_SCOPE;
            }
            $jsonKey['source_credentials'] = match ($jsonKey['source_credentials']['type'] ?? null) {
                // Do not pass $defaultScope to ServiceAccountCredentials
                'service_account' => new ServiceAccountCredentials($scope, $jsonKey['source_credentials']),
                'authorized_user' => new UserRefreshCredentials($scope, $jsonKey['source_credentials']),
                'external_account' => new ExternalAccountCredentials($scope, $jsonKey['source_credentials']),
                default => throw new \InvalidArgumentException('invalid value in the type field'),
            };
        }

        $this->targetScope = $scope ?? [];
        $this->lifetime = $jsonKey['lifetime'] ?? 3600;
        $this->delegates = $jsonKey['delegates'] ?? [];

        $this->serviceAccountImpersonationUrl = $jsonKey['service_account_impersonation_url'];
        $this->impersonatedServiceAccountName = $this->getImpersonatedServiceAccountNameFromUrl(
            $this->serviceAccountImpersonationUrl
        );

        $this->sourceCredentials = $jsonKey['source_credentials'];
    }

    /**
     * Helper function for extracting the Server Account Name from the URL saved in the account
     * credentials file.
     *
     * @param $serviceAccountImpersonationUrl string URL from "service_account_impersonation_url"
     * @return string Service account email or ID.
     */
    private function getImpersonatedServiceAccountNameFromUrl(
        string $serviceAccountImpersonationUrl
    ): string {
        $fields = explode('/', $serviceAccountImpersonationUrl);
        $lastField = end($fields);
        $splitter = explode(':', $lastField);
        return $splitter[0];
    }

    /**
     * Get the client name from the keyfile
     *
     * In this implementation, it will return the issuers email from the oauth token.
     *
     * @param callable|null $unusedHttpHandler not used by this credentials type.
     * @return string Token issuer email
     */
    public function getClientName(?callable $unusedHttpHandler = null)
    {
        return $this->impersonatedServiceAccountName;
    }

    /**
     * @param callable|null $httpHandler
     *
     * @return array<mixed> {
     *     A set of auth related metadata, containing the following
     *
     *     @type string $access_token
     *     @type int $expires_in
     *     @type string $scope
     *     @type string $token_type
     *     @type string $id_token
     * }
     */
    public function fetchAuthToken(?callable $httpHandler = null)
    {
        $httpHandler = $httpHandler ?? HttpHandlerFactory::build(HttpClientCache::getHttpClient());

        // The FetchAuthTokenInterface technically does not have a "headers" argument, but all of
        // the implementations do. Additionally, passing in more parameters than the function has
        // defined is allowed in PHP. So we'll just ignore the phpstan error here.
        // @phpstan-ignore-next-line
        $authToken = $this->sourceCredentials->fetchAuthToken(
            $httpHandler,
            $this->applyTokenEndpointMetrics([], 'at')
        );

        $headers = $this->applyTokenEndpointMetrics([
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store',
            'Authorization' => sprintf('Bearer %s', $authToken['access_token'] ?? $authToken['id_token']),
        ], $this->isIdTokenRequest() ? 'it' : 'at');

        $body = match ($this->isIdTokenRequest()) {
            true => [
                'audience' => $this->targetAudience,
                'includeEmail' => true,
            ],
            false => [
                'scope' => $this->targetScope,
                'delegates' => $this->delegates,
                'lifetime' => sprintf('%ss', $this->lifetime),
            ]
        };

        $url = $this->serviceAccountImpersonationUrl;
        if ($this->isIdTokenRequest()) {
            $regex = '/serviceAccounts\/(?<email>[^:]+):generateAccessToken$/';
            if (!preg_match($regex, $url, $matches)) {
                throw new InvalidArgumentException(
                    'Invalid service account impersonation URL - unable to parse service account email'
                );
            }
            $url = str_replace(
                'UNIVERSE_DOMAIN',
                $this->getUniverseDomain(),
                sprintf(self::ID_TOKEN_IMPERSONATION_URL, $matches['email'])
            );
        }

        $request = new Request(
            'POST',
            $url,
            $headers,
            (string) json_encode($body)
        );

        $response = $httpHandler($request);
        $body = json_decode((string) $response->getBody(), true);

        return match ($this->isIdTokenRequest()) {
            true => ['id_token' => $body['token']],
            false => [
                'access_token' => $body['accessToken'],
                'expires_at' => strtotime($body['expireTime']),
            ]
        };
    }

    /**
     * Returns the Cache Key for the credentials
     * The cache key is the same as the UserRefreshCredentials class
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->getFullCacheKey(
            $this->serviceAccountImpersonationUrl . $this->sourceCredentials->getCacheKey()
        );
    }

    /**
     * @return array<mixed>
     */
    public function getLastReceivedToken()
    {
        return $this->sourceCredentials->getLastReceivedToken();
    }

    protected function getCredType(): string
    {
        return self::CRED_TYPE;
    }

    private function isIdTokenRequest(): bool
    {
        return !is_null($this->targetAudience);
    }

    public function getUniverseDomain(): string
    {
        return $this->sourceCredentials instanceof GetUniverseDomainInterface
            ? $this->sourceCredentials->getUniverseDomain()
            : self::DEFAULT_UNIVERSE_DOMAIN;
    }
}
