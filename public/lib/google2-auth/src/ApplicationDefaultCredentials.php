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

namespace Google\Auth;

use DomainException;
use Google\Auth\Credentials\AppIdentityCredentials;
use Google\Auth\Credentials\GCECredentials;
use Google\Auth\Credentials\ImpersonatedServiceAccountCredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Auth\HttpHandler\HttpClientCache;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Auth\Logging\StdOutLogger;
use Google\Auth\Middleware\AuthTokenMiddleware;
use Google\Auth\Middleware\ProxyAuthTokenMiddleware;
use Google\Auth\Subscriber\AuthTokenSubscriber;
use GuzzleHttp\Client;
use InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

/**
 * ApplicationDefaultCredentials obtains the default credentials for
 * authorizing a request to a Google service.
 *
 * Application Default Credentials are described here:
 * https://developers.google.com/accounts/docs/application-default-credentials
 *
 * This class implements the search for the application default credentials as
 * described in the link.
 *
 * It provides three factory methods:
 * - #get returns the computed credentials object
 * - #getSubscriber returns an AuthTokenSubscriber built from the credentials object
 * - #getMiddleware returns an AuthTokenMiddleware built from the credentials object
 *
 * This allows it to be used as follows with GuzzleHttp\Client:
 *
 * ```
 * use Google\Auth\ApplicationDefaultCredentials;
 * use GuzzleHttp\Client;
 * use GuzzleHttp\HandlerStack;
 *
 * $middleware = ApplicationDefaultCredentials::getMiddleware(
 *     'https://www.googleapis.com/auth/taskqueue'
 * );
 * $stack = HandlerStack::create();
 * $stack->push($middleware);
 *
 * $client = new Client([
 *     'handler' => $stack,
 *     'base_uri' => 'https://www.googleapis.com/taskqueue/v1beta2/projects/',
 *     'auth' => 'google_auth' // authorize all requests
 * ]);
 *
 * $res = $client->get('myproject/taskqueues/myqueue');
 * ```
 */
class ApplicationDefaultCredentials
{
    private const SDK_DEBUG_ENV_VAR = 'GOOGLE_SDK_PHP_LOGGING';

    /**
     * @deprecated
     *
     * Obtains an AuthTokenSubscriber that uses the default FetchAuthTokenInterface
     * implementation to use in this environment.
     *
     * If supplied, $scope is used to in creating the credentials instance if
     * this does not fallback to the compute engine defaults.
     *
     * @param string|string[] $scope the scope of the access request, expressed
     *        either as an Array or as a space-delimited String.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @return AuthTokenSubscriber
     * @throws DomainException if no implementation can be obtained.
     */
    public static function getSubscriber(// @phpstan-ignore-line
        $scope = null,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null
    ) {
        $creds = self::getCredentials($scope, $httpHandler, $cacheConfig, $cache);

        /** @phpstan-ignore-next-line */
        return new AuthTokenSubscriber($creds, $httpHandler);
    }

    /**
     * Obtains an AuthTokenMiddleware that uses the default FetchAuthTokenInterface
     * implementation to use in this environment.
     *
     * If supplied, $scope is used to in creating the credentials instance if
     * this does not fallback to the compute engine defaults.
     *
     * @param string|string[] $scope the scope of the access request, expressed
     *        either as an Array or as a space-delimited String.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @param string $quotaProject specifies a project to bill for access
     *   charges associated with the request.
     * @return AuthTokenMiddleware
     * @throws DomainException if no implementation can be obtained.
     */
    public static function getMiddleware(
        $scope = null,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null,
        $quotaProject = null
    ) {
        $creds = self::getCredentials($scope, $httpHandler, $cacheConfig, $cache, $quotaProject);

        return new AuthTokenMiddleware($creds, $httpHandler);
    }

    /**
     * Obtains the default FetchAuthTokenInterface implementation to use
     * in this environment.
     *
     * @param string|string[] $scope the scope of the access request, expressed
     *        either as an Array or as a space-delimited String.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @param string|null $quotaProject specifies a project to bill for access
     *   charges associated with the request.
     * @param string|string[]|null $defaultScope The default scope to use if no
     *   user-defined scopes exist, expressed either as an Array or as a
     *   space-delimited string.
     * @param string|null $universeDomain Specifies a universe domain to use for the
     *   calling client library.
     * @param null|false|LoggerInterface $logger A PSR3 compliant LoggerInterface.
     *
     * @return FetchAuthTokenInterface
     * @throws DomainException if no implementation can be obtained.
     */
    public static function getCredentials(
        $scope = null,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null,
        $quotaProject = null,
        $defaultScope = null,
        ?string $universeDomain = null,
        null|false|LoggerInterface $logger = null,
    ) {
        $creds = null;
        $jsonKey = CredentialsLoader::fromEnv()
            ?: CredentialsLoader::fromWellKnownFile();
        $anyScope = $scope ?: $defaultScope;

        if (!$httpHandler) {
            if (!($client = HttpClientCache::getHttpClient())) {
                $client = new Client();
                HttpClientCache::setHttpClient($client);
            }

            $httpHandler = HttpHandlerFactory::build($client, $logger);
        }

        if (is_null($quotaProject)) {
            // if a quota project isn't specified, try to get one from the env var
            $quotaProject = CredentialsLoader::quotaProjectFromEnv();
        }

        if (!is_null($jsonKey)) {
            if ($quotaProject) {
                $jsonKey['quota_project_id'] = $quotaProject;
            }
            if ($universeDomain) {
                $jsonKey['universe_domain'] = $universeDomain;
            }
            $creds = CredentialsLoader::makeCredentials(
                $scope,
                $jsonKey,
                $defaultScope
            );
        } elseif (AppIdentityCredentials::onAppEngine() && !GCECredentials::onAppEngineFlexible()) {
            $creds = new AppIdentityCredentials($anyScope);
        } elseif (self::onGce($httpHandler, $cacheConfig, $cache)) {
            $creds = new GCECredentials(null, $anyScope, null, $quotaProject, null, $universeDomain);
            $creds->setIsOnGce(true); // save the credentials a trip to the metadata server
        }

        if (is_null($creds)) {
            throw new DomainException(self::notFound());
        }
        if (!is_null($cache)) {
            $creds = new FetchAuthTokenCache($creds, $cacheConfig, $cache);
        }
        return $creds;
    }

    /**
     * Obtains an AuthTokenMiddleware which will fetch an ID token to use in the
     * Authorization header. The middleware is configured with the default
     * FetchAuthTokenInterface implementation to use in this environment.
     *
     * If supplied, $targetAudience is used to set the "aud" on the resulting
     * ID token.
     *
     * @param string $targetAudience The audience for the ID token.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @return AuthTokenMiddleware
     * @throws DomainException if no implementation can be obtained.
     */
    public static function getIdTokenMiddleware(
        $targetAudience,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null
    ) {
        $creds = self::getIdTokenCredentials($targetAudience, $httpHandler, $cacheConfig, $cache);

        return new AuthTokenMiddleware($creds, $httpHandler);
    }

    /**
     * Obtains an ProxyAuthTokenMiddleware which will fetch an ID token to use in the
     * Authorization header. The middleware is configured with the default
     * FetchAuthTokenInterface implementation to use in this environment.
     *
     * If supplied, $targetAudience is used to set the "aud" on the resulting
     * ID token.
     *
     * @param string $targetAudience The audience for the ID token.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @return ProxyAuthTokenMiddleware
     * @throws DomainException if no implementation can be obtained.
     */
    public static function getProxyIdTokenMiddleware(
        $targetAudience,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null
    ) {
        $creds = self::getIdTokenCredentials($targetAudience, $httpHandler, $cacheConfig, $cache);

        return new ProxyAuthTokenMiddleware($creds, $httpHandler);
    }

    /**
     * Obtains the default FetchAuthTokenInterface implementation to use
     * in this environment, configured with a $targetAudience for fetching an ID
     * token.
     *
     * @param string $targetAudience The audience for the ID token.
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @param array<mixed>|null $cacheConfig configuration for the cache when it's present
     * @param CacheItemPoolInterface|null $cache A cache implementation, may be
     *        provided if you have one already available for use.
     * @return FetchAuthTokenInterface
     * @throws DomainException if no implementation can be obtained.
     * @throws InvalidArgumentException if JSON "type" key is invalid
     */
    public static function getIdTokenCredentials(
        $targetAudience,
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null
    ) {
        $creds = null;
        $jsonKey = CredentialsLoader::fromEnv()
            ?: CredentialsLoader::fromWellKnownFile();

        if (!$httpHandler) {
            if (!($client = HttpClientCache::getHttpClient())) {
                $client = new Client();
                HttpClientCache::setHttpClient($client);
            }

            $httpHandler = HttpHandlerFactory::build($client);
        }

        if (!is_null($jsonKey)) {
            if (!array_key_exists('type', $jsonKey)) {
                throw new \InvalidArgumentException('json key is missing the type field');
            }

            $creds = match ($jsonKey['type']) {
                'authorized_user' => new UserRefreshCredentials(null, $jsonKey, $targetAudience),
                'impersonated_service_account' => new ImpersonatedServiceAccountCredentials(null, $jsonKey, $targetAudience),
                'service_account' => new ServiceAccountCredentials(null, $jsonKey, null, $targetAudience),
                default => throw new InvalidArgumentException('invalid value in the type field')
            };
        } elseif (self::onGce($httpHandler, $cacheConfig, $cache)) {
            $creds = new GCECredentials(null, null, $targetAudience);
            $creds->setIsOnGce(true); // save the credentials a trip to the metadata server
        }

        if (is_null($creds)) {
            throw new DomainException(self::notFound());
        }
        if (!is_null($cache)) {
            $creds = new FetchAuthTokenCache($creds, $cacheConfig, $cache);
        }
        return $creds;
    }

    /**
     * Returns a StdOutLogger instance
     *
     * @internal
     *
     * @return null|LoggerInterface
     */
    public static function getDefaultLogger(): null|LoggerInterface
    {
        $loggingFlag = getenv(self::SDK_DEBUG_ENV_VAR);

        // Env var is not set
        if (empty($loggingFlag)) {
            return null;
        }

        $loggingFlag = strtolower($loggingFlag);

        // Env Var is not true
        if ($loggingFlag !== 'true') {
            if ($loggingFlag !== 'false') {
                trigger_error('The ' . self::SDK_DEBUG_ENV_VAR . ' is set, but it is set to another value than false or true. Logging is disabled');
            }

            return null;
        }

        return new StdOutLogger();
    }

    /**
     * @return string
     */
    private static function notFound()
    {
        $msg = 'Your default credentials were not found. To set up ';
        $msg .= 'Application Default Credentials, see ';
        $msg .= 'https://cloud.google.com/docs/authentication/external/set-up-adc';

        return $msg;
    }

    /**
     * @param callable|null $httpHandler
     * @param array<mixed>|null $cacheConfig
     * @param CacheItemPoolInterface|null $cache
     * @return bool
     */
    private static function onGce(
        ?callable $httpHandler = null,
        ?array $cacheConfig = null,
        ?CacheItemPoolInterface $cache = null
    ) {
        $gceCacheConfig = [];
        foreach (['lifetime', 'prefix'] as $key) {
            if (isset($cacheConfig['gce_' . $key])) {
                $gceCacheConfig[$key] = $cacheConfig['gce_' . $key];
            }
        }

        return (new GCECache($gceCacheConfig, $cache))->onGce($httpHandler);
    }
}
