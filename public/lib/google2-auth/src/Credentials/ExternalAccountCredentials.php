<?php
/*
 * Copyright 2023 Google Inc.
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

use Google\Auth\CredentialSource\AwsNativeSource;
use Google\Auth\CredentialSource\ExecutableSource;
use Google\Auth\CredentialSource\FileSource;
use Google\Auth\CredentialSource\UrlSource;
use Google\Auth\ExecutableHandler\ExecutableHandler;
use Google\Auth\ExternalAccountCredentialSourceInterface;
use Google\Auth\FetchAuthTokenInterface;
use Google\Auth\GetQuotaProjectInterface;
use Google\Auth\GetUniverseDomainInterface;
use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Auth\OAuth2;
use Google\Auth\ProjectIdProviderInterface;
use Google\Auth\UpdateMetadataInterface;
use Google\Auth\UpdateMetadataTrait;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;

/**
 * **IMPORTANT**:
 * This class does not validate the credential configuration. A security
 * risk occurs when a credential configuration configured with malicious urls
 * is used.
 * When the credential configuration is accepted from an
 * untrusted source, you should validate it before creating this class.
 * @see https://cloud.google.com/docs/authentication/external/externally-sourced-credentials
 */
class ExternalAccountCredentials implements
    FetchAuthTokenInterface,
    UpdateMetadataInterface,
    GetQuotaProjectInterface,
    GetUniverseDomainInterface,
    ProjectIdProviderInterface
{
    use UpdateMetadataTrait;

    private const EXTERNAL_ACCOUNT_TYPE = 'external_account';
    private const CLOUD_RESOURCE_MANAGER_URL = 'https://cloudresourcemanager.UNIVERSE_DOMAIN/v1/projects/%s';

    private OAuth2 $auth;
    private ?string $quotaProject;
    private ?string $serviceAccountImpersonationUrl;
    private ?string $workforcePoolUserProject;
    private ?string $projectId;
    /** @var array<mixed> */
    private ?array $lastImpersonatedAccessToken;
    private string $universeDomain;

    /**
     * @param string|string[] $scope   The scope of the access request, expressed either as an array
     *                                 or as a space-delimited string.
     * @param array<mixed>    $jsonKey JSON credentials as an associative array.
     */
    public function __construct(
        $scope,
        array $jsonKey
    ) {
        if (!array_key_exists('type', $jsonKey)) {
            throw new InvalidArgumentException('json key is missing the type field');
        }
        if ($jsonKey['type'] !== self::EXTERNAL_ACCOUNT_TYPE) {
            throw new InvalidArgumentException(sprintf(
                'expected "%s" type but received "%s"',
                self::EXTERNAL_ACCOUNT_TYPE,
                $jsonKey['type']
            ));
        }

        if (!array_key_exists('token_url', $jsonKey)) {
            throw new InvalidArgumentException(
                'json key is missing the token_url field'
            );
        }

        if (!array_key_exists('audience', $jsonKey)) {
            throw new InvalidArgumentException(
                'json key is missing the audience field'
            );
        }

        if (!array_key_exists('subject_token_type', $jsonKey)) {
            throw new InvalidArgumentException(
                'json key is missing the subject_token_type field'
            );
        }

        if (!array_key_exists('credential_source', $jsonKey)) {
            throw new InvalidArgumentException(
                'json key is missing the credential_source field'
            );
        }

        $this->serviceAccountImpersonationUrl = $jsonKey['service_account_impersonation_url'] ?? null;

        $this->quotaProject = $jsonKey['quota_project_id'] ?? null;
        $this->workforcePoolUserProject = $jsonKey['workforce_pool_user_project'] ?? null;
        $this->universeDomain = $jsonKey['universe_domain'] ?? GetUniverseDomainInterface::DEFAULT_UNIVERSE_DOMAIN;

        $this->auth = new OAuth2([
            'tokenCredentialUri' => $jsonKey['token_url'],
            'audience' => $jsonKey['audience'],
            'scope' => $scope,
            'subjectTokenType' => $jsonKey['subject_token_type'],
            'subjectTokenFetcher' => self::buildCredentialSource($jsonKey),
            'additionalOptions' => $this->workforcePoolUserProject
                ? ['userProject' => $this->workforcePoolUserProject]
                : [],
        ]);

        if (!$this->isWorkforcePool() && $this->workforcePoolUserProject) {
            throw new InvalidArgumentException(
                'workforce_pool_user_project should not be set for non-workforce pool credentials.'
            );
        }
    }

    /**
     * @param array<mixed> $jsonKey
     */
    private static function buildCredentialSource(array $jsonKey): ExternalAccountCredentialSourceInterface
    {
        $credentialSource = $jsonKey['credential_source'];
        if (isset($credentialSource['file'])) {
            return new FileSource(
                $credentialSource['file'],
                $credentialSource['format']['type'] ?? null,
                $credentialSource['format']['subject_token_field_name'] ?? null
            );
        }

        if (
            isset($credentialSource['environment_id'])
            && 1 === preg_match('/^aws(\d+)$/', $credentialSource['environment_id'], $matches)
        ) {
            if ($matches[1] !== '1') {
                throw new InvalidArgumentException(
                    "aws version \"$matches[1]\" is not supported in the current build."
                );
            }
            if (!array_key_exists('regional_cred_verification_url', $credentialSource)) {
                throw new InvalidArgumentException(
                    'The regional_cred_verification_url field is required for aws1 credential source.'
                );
            }

            return new AwsNativeSource(
                $jsonKey['audience'],
                $credentialSource['regional_cred_verification_url'],   // $regionalCredVerificationUrl
                $credentialSource['region_url'] ?? null,               // $regionUrl
                $credentialSource['url'] ?? null,                      // $securityCredentialsUrl
                $credentialSource['imdsv2_session_token_url'] ?? null, // $imdsV2TokenUrl
            );
        }

        if (isset($credentialSource['url'])) {
            return new UrlSource(
                $credentialSource['url'],
                $credentialSource['format']['type'] ?? null,
                $credentialSource['format']['subject_token_field_name'] ?? null,
                $credentialSource['headers'] ?? null,
            );
        }

        if (isset($credentialSource['executable'])) {
            if (!array_key_exists('command', $credentialSource['executable'])) {
                throw new InvalidArgumentException(
                    'executable source requires a command to be set in the JSON file.'
                );
            }

            // Build command environment variables
            $env = [
                'GOOGLE_EXTERNAL_ACCOUNT_AUDIENCE' => $jsonKey['audience'],
                'GOOGLE_EXTERNAL_ACCOUNT_TOKEN_TYPE' => $jsonKey['subject_token_type'],
                // Always set to 0 because interactive mode is not supported.
                'GOOGLE_EXTERNAL_ACCOUNT_INTERACTIVE' => '0',
            ];

            if ($outputFile = $credentialSource['executable']['output_file'] ?? null) {
                $env['GOOGLE_EXTERNAL_ACCOUNT_OUTPUT_FILE'] = $outputFile;
            }

            if ($serviceAccountImpersonationUrl = $jsonKey['service_account_impersonation_url'] ?? null) {
                // Parse email from URL. The formal looks as follows:
                // https://iamcredentials.googleapis.com/v1/projects/-/serviceAccounts/name@project-id.iam.gserviceaccount.com:generateAccessToken
                $regex = '/serviceAccounts\/(?<email>[^:]+):generateAccessToken$/';
                if (preg_match($regex, $serviceAccountImpersonationUrl, $matches)) {
                    $env['GOOGLE_EXTERNAL_ACCOUNT_IMPERSONATED_EMAIL'] = $matches['email'];
                }
            }

            $timeoutMs = $credentialSource['executable']['timeout_millis'] ?? null;

            return new ExecutableSource(
                $credentialSource['executable']['command'],
                $outputFile,
                $timeoutMs ? new ExecutableHandler($env, $timeoutMs) : new ExecutableHandler($env)
            );
        }

        throw new InvalidArgumentException('Unable to determine credential source from json key.');
    }

    /**
     * @param string $stsToken
     * @param callable|null $httpHandler
     *
     * @return array<mixed> {
     *     A set of auth related metadata, containing the following
     *
     *     @type string $access_token
     *     @type int $expires_at
     * }
     */
    private function getImpersonatedAccessToken(string $stsToken, ?callable $httpHandler = null): array
    {
        if (!isset($this->serviceAccountImpersonationUrl)) {
            throw new InvalidArgumentException(
                'service_account_impersonation_url must be set in JSON credentials.'
            );
        }
        $request = new Request(
            'POST',
            $this->serviceAccountImpersonationUrl,
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $stsToken,
            ],
            (string) json_encode([
                'lifetime' => sprintf('%ss', OAuth2::DEFAULT_EXPIRY_SECONDS),
                'scope' => explode(' ', $this->auth->getScope()),
            ]),
        );
        if (is_null($httpHandler)) {
            $httpHandler = HttpHandlerFactory::build(HttpClientCache::getHttpClient());
        }
        $response = $httpHandler($request);
        $body = json_decode((string) $response->getBody(), true);
        return [
            'access_token' => $body['accessToken'],
            'expires_at' => strtotime($body['expireTime']),
        ];
    }

    /**
     * @param callable|null $httpHandler
     * @param array<mixed> $headers [optional] Metrics headers to be inserted
     *     into the token endpoint request present.
     *
     * @return array<mixed> {
     *     A set of auth related metadata, containing the following
     *
     *     @type string $access_token
     *     @type int $expires_at (impersonated service accounts only)
     *     @type int $expires_in (identity pool only)
     *     @type string $issued_token_type (identity pool only)
     *     @type string $token_type (identity pool only)
     * }
     */
    public function fetchAuthToken(?callable $httpHandler = null, array $headers = [])
    {
        $stsToken = $this->auth->fetchAuthToken($httpHandler, $headers);

        if (isset($this->serviceAccountImpersonationUrl)) {
            return $this->lastImpersonatedAccessToken = $this->getImpersonatedAccessToken(
                $stsToken['access_token'],
                $httpHandler
            );
        }

        return $stsToken;
    }

    /**
     * Get the cache token key for the credentials.
     * The cache token key format depends on the type of source
     * The format for the cache key one of the following:
     * FetcherCacheKey.Scope.[ServiceAccount].[TokenType].[WorkforcePoolUserProject]
     * FetcherCacheKey.Audience.[ServiceAccount].[TokenType].[WorkforcePoolUserProject]
     *
     * @return ?string;
     */
    public function getCacheKey(): ?string
    {
        $scopeOrAudience = $this->auth->getAudience();
        if (!$scopeOrAudience) {
            $scopeOrAudience = $this->auth->getScope();
        }

        return $this->auth->getSubjectTokenFetcher()->getCacheKey() .
            '.' . $scopeOrAudience .
            '.' . ($this->serviceAccountImpersonationUrl ?? '') .
            '.' . ($this->auth->getSubjectTokenType() ?? '') .
            '.' . ($this->workforcePoolUserProject ?? '');
    }

    public function getLastReceivedToken()
    {
        return $this->lastImpersonatedAccessToken ?? $this->auth->getLastReceivedToken();
    }

    /**
     * Get the quota project used for this API request
     *
     * @return string|null
     */
    public function getQuotaProject()
    {
        return $this->quotaProject;
    }

    /**
     * Get the universe domain used for this API request
     *
     * @return string
     */
    public function getUniverseDomain(): string
    {
        return $this->universeDomain;
    }

    /**
     * Get the project ID.
     *
     * @param callable|null $httpHandler Callback which delivers psr7 request
     * @param string|null $accessToken The access token to use to sign the blob. If
     *        provided, saves a call to the metadata server for a new access
     *        token. **Defaults to** `null`.
     * @return string|null
     */
    public function getProjectId(?callable $httpHandler = null, ?string $accessToken = null)
    {
        if (isset($this->projectId)) {
            return $this->projectId;
        }

        $projectNumber = $this->getProjectNumber() ?: $this->workforcePoolUserProject;
        if (!$projectNumber) {
            return null;
        }

        if (is_null($httpHandler)) {
            $httpHandler = HttpHandlerFactory::build(HttpClientCache::getHttpClient());
        }

        $url = str_replace(
            'UNIVERSE_DOMAIN',
            $this->getUniverseDomain(),
            sprintf(self::CLOUD_RESOURCE_MANAGER_URL, $projectNumber)
        );

        if (is_null($accessToken)) {
            $accessToken = $this->fetchAuthToken($httpHandler)['access_token'];
        }

        $request = new Request('GET', $url, ['authorization' => 'Bearer ' . $accessToken]);
        $response = $httpHandler($request);

        $body = json_decode((string) $response->getBody(), true);
        return $this->projectId = $body['projectId'];
    }

    private function getProjectNumber(): ?string
    {
        $parts = explode('/', $this->auth->getAudience());
        $i = array_search('projects', $parts);
        return $parts[$i + 1] ?? null;
    }

    private function isWorkforcePool(): bool
    {
        $regex = '#//iam\.googleapis\.com/locations/[^/]+/workforcePools/#';
        return preg_match($regex, $this->auth->getAudience()) === 1;
    }
}
