<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Compute;

class BackendServiceCdnPolicy extends \Google\Collection
{
  /**
   * Automatically cache static content, including common image formats, media
   * (video and audio), and web assets (JavaScript and CSS). Requests and
   * responses that are marked as uncacheable, as well as dynamic content
   * (including HTML), will not be cached.
   */
  public const CACHE_MODE_CACHE_ALL_STATIC = 'CACHE_ALL_STATIC';
  /**
   * Cache all content, ignoring any "private", "no-store" or "no-cache"
   * directives in Cache-Control response headers. Warning: this may result in
   * Cloud CDN caching private, per-user (user identifiable) content.
   */
  public const CACHE_MODE_FORCE_CACHE_ALL = 'FORCE_CACHE_ALL';
  public const CACHE_MODE_INVALID_CACHE_MODE = 'INVALID_CACHE_MODE';
  /**
   * Requires the origin to set valid caching headers to cache content.
   * Responses without these headers will not be cached at Google's edge, and
   * will require a full trip to the origin on every request, potentially
   * impacting performance and increasing load on the origin server.
   */
  public const CACHE_MODE_USE_ORIGIN_HEADERS = 'USE_ORIGIN_HEADERS';
  protected $collection_key = 'signedUrlKeyNames';
  protected $bypassCacheOnRequestHeadersType = BackendServiceCdnPolicyBypassCacheOnRequestHeader::class;
  protected $bypassCacheOnRequestHeadersDataType = 'array';
  protected $cacheKeyPolicyType = CacheKeyPolicy::class;
  protected $cacheKeyPolicyDataType = '';
  /**
   * Specifies the cache setting for all responses from this backend. The
   * possible values are:USE_ORIGIN_HEADERS Requires the origin to set valid
   * caching headers to cache content. Responses without these headers will not
   * be cached at Google's edge, and will require a full trip to the origin on
   * every request, potentially impacting performance and increasing load on the
   * origin server.FORCE_CACHE_ALL Cache all content, ignoring any "private",
   * "no-store" or "no-cache" directives in Cache-Control response headers.
   * Warning: this may result in Cloud CDN caching private, per-user (user
   * identifiable) content.CACHE_ALL_STATIC Automatically cache static content,
   * including common image formats, media (video and audio), and web assets
   * (JavaScript and CSS). Requests and responses that are marked as
   * uncacheable, as well as dynamic content (including HTML), will not be
   * cached.
   *
   * If no value is provided for cdnPolicy.cacheMode, it defaults to
   * CACHE_ALL_STATIC.
   *
   * @var string
   */
  public $cacheMode;
  /**
   * Specifies a separate client (e.g. browser client) maximum TTL. This is used
   * to clamp the max-age (or Expires) value sent to the client.  With
   * FORCE_CACHE_ALL, the lesser of client_ttl and default_ttl is used for the
   * response max-age directive, along with a "public" directive.  For cacheable
   * content in CACHE_ALL_STATIC mode, client_ttl clamps the max-age from the
   * origin (if specified), or else sets the response max-age directive to the
   * lesser of the client_ttl and default_ttl, and also ensures a "public"
   * cache-control directive is present. If a client TTL is not specified, a
   * default value (1 hour) will be used. The maximum allowed value is
   * 31,622,400s (1 year).
   *
   * @var int
   */
  public $clientTtl;
  /**
   * Specifies the default TTL for cached content served by this origin for
   * responses that do not have an existing valid TTL (max-age or s-maxage).
   * Setting a TTL of "0" means "always revalidate". The value of defaultTTL
   * cannot be set to a value greater than that of maxTTL, but can be equal.
   * When the cacheMode is set to FORCE_CACHE_ALL, the defaultTTL will overwrite
   * the TTL set in all responses. The maximum allowed value is 31,622,400s (1
   * year), noting that infrequently accessed objects may be evicted from the
   * cache before the defined TTL.
   *
   * @var int
   */
  public $defaultTtl;
  /**
   * Specifies the maximum allowed TTL for cached content served by this origin.
   * Cache directives that attempt to set a max-age or s-maxage higher than
   * this, or an Expires header more than maxTTL seconds in the future will be
   * capped at the value of maxTTL, as if it were the value of an s-maxage
   * Cache-Control directive. Headers sent to the client will not be modified.
   * Setting a TTL of "0" means "always revalidate". The maximum allowed value
   * is 31,622,400s (1 year), noting that infrequently accessed objects may be
   * evicted from the cache before the defined TTL.
   *
   * @var int
   */
  public $maxTtl;
  /**
   * Negative caching allows per-status code TTLs to be set, in order to apply
   * fine-grained caching for common errors or redirects. This can reduce the
   * load on your origin and improve end-user experience by reducing response
   * latency. When the cache mode is set to CACHE_ALL_STATIC or
   * USE_ORIGIN_HEADERS, negative caching applies to responses with the
   * specified response code that lack any Cache-Control, Expires, or Pragma:
   * no-cache directives. When the cache mode is set to FORCE_CACHE_ALL,
   * negative caching applies to all responses with the specified response code,
   * and override any caching headers. By default, Cloud CDN will apply the
   * following default TTLs to these status codes: HTTP 300 (Multiple Choice),
   * 301, 308 (Permanent Redirects): 10m HTTP 404 (Not Found), 410 (Gone), 451
   * (Unavailable For Legal Reasons): 120s HTTP 405 (Method Not Found), 501 (Not
   * Implemented): 60s. These defaults can be overridden in
   * negative_caching_policy.
   *
   * @var bool
   */
  public $negativeCaching;
  protected $negativeCachingPolicyType = BackendServiceCdnPolicyNegativeCachingPolicy::class;
  protected $negativeCachingPolicyDataType = 'array';
  /**
   * If true then Cloud CDN will combine multiple concurrent cache fill requests
   * into a small number of requests to the origin.
   *
   * @var bool
   */
  public $requestCoalescing;
  /**
   * Serve existing content from the cache (if available) when revalidating
   * content with the origin, or when an error is encountered when refreshing
   * the cache. This setting defines the default "max-stale" duration for any
   * cached responses that do not specify a max-stale directive. Stale responses
   * that exceed the TTL configured here will not be served. The default limit
   * (max-stale) is 86400s (1 day), which will allow stale content to be served
   * up to this limit beyond the max-age (or s-maxage) of a cached response. The
   * maximum allowed value is 604800 (1 week). Set this to zero (0) to disable
   * serve-while-stale.
   *
   * @var int
   */
  public $serveWhileStale;
  /**
   * Maximum number of seconds the response to a signed URL request will be
   * considered fresh. After this time period, the response will be revalidated
   * before being served. Defaults to 1hr (3600s).  When serving responses to
   * signed URL requests, Cloud CDN will internally behave as though all
   * responses from this backend had a "Cache-Control: public, max-age=[TTL]"
   * header, regardless of any existing Cache-Control header. The actual headers
   * served in responses will not be altered.
   *
   * @var string
   */
  public $signedUrlCacheMaxAgeSec;
  /**
   * [Output Only] Names of the keys for signing request URLs.
   *
   * @var string[]
   */
  public $signedUrlKeyNames;

  /**
   * Bypass the cache when the specified request headers are matched - e.g.
   * Pragma or Authorization headers. Up to 5 headers can be specified. The
   * cache is bypassed for all cdnPolicy.cacheMode settings.
   *
   * @param BackendServiceCdnPolicyBypassCacheOnRequestHeader[] $bypassCacheOnRequestHeaders
   */
  public function setBypassCacheOnRequestHeaders($bypassCacheOnRequestHeaders)
  {
    $this->bypassCacheOnRequestHeaders = $bypassCacheOnRequestHeaders;
  }
  /**
   * @return BackendServiceCdnPolicyBypassCacheOnRequestHeader[]
   */
  public function getBypassCacheOnRequestHeaders()
  {
    return $this->bypassCacheOnRequestHeaders;
  }
  /**
   * The CacheKeyPolicy for this CdnPolicy.
   *
   * @param CacheKeyPolicy $cacheKeyPolicy
   */
  public function setCacheKeyPolicy(CacheKeyPolicy $cacheKeyPolicy)
  {
    $this->cacheKeyPolicy = $cacheKeyPolicy;
  }
  /**
   * @return CacheKeyPolicy
   */
  public function getCacheKeyPolicy()
  {
    return $this->cacheKeyPolicy;
  }
  /**
   * Specifies the cache setting for all responses from this backend. The
   * possible values are:USE_ORIGIN_HEADERS Requires the origin to set valid
   * caching headers to cache content. Responses without these headers will not
   * be cached at Google's edge, and will require a full trip to the origin on
   * every request, potentially impacting performance and increasing load on the
   * origin server.FORCE_CACHE_ALL Cache all content, ignoring any "private",
   * "no-store" or "no-cache" directives in Cache-Control response headers.
   * Warning: this may result in Cloud CDN caching private, per-user (user
   * identifiable) content.CACHE_ALL_STATIC Automatically cache static content,
   * including common image formats, media (video and audio), and web assets
   * (JavaScript and CSS). Requests and responses that are marked as
   * uncacheable, as well as dynamic content (including HTML), will not be
   * cached.
   *
   * If no value is provided for cdnPolicy.cacheMode, it defaults to
   * CACHE_ALL_STATIC.
   *
   * Accepted values: CACHE_ALL_STATIC, FORCE_CACHE_ALL, INVALID_CACHE_MODE,
   * USE_ORIGIN_HEADERS
   *
   * @param self::CACHE_MODE_* $cacheMode
   */
  public function setCacheMode($cacheMode)
  {
    $this->cacheMode = $cacheMode;
  }
  /**
   * @return self::CACHE_MODE_*
   */
  public function getCacheMode()
  {
    return $this->cacheMode;
  }
  /**
   * Specifies a separate client (e.g. browser client) maximum TTL. This is used
   * to clamp the max-age (or Expires) value sent to the client.  With
   * FORCE_CACHE_ALL, the lesser of client_ttl and default_ttl is used for the
   * response max-age directive, along with a "public" directive.  For cacheable
   * content in CACHE_ALL_STATIC mode, client_ttl clamps the max-age from the
   * origin (if specified), or else sets the response max-age directive to the
   * lesser of the client_ttl and default_ttl, and also ensures a "public"
   * cache-control directive is present. If a client TTL is not specified, a
   * default value (1 hour) will be used. The maximum allowed value is
   * 31,622,400s (1 year).
   *
   * @param int $clientTtl
   */
  public function setClientTtl($clientTtl)
  {
    $this->clientTtl = $clientTtl;
  }
  /**
   * @return int
   */
  public function getClientTtl()
  {
    return $this->clientTtl;
  }
  /**
   * Specifies the default TTL for cached content served by this origin for
   * responses that do not have an existing valid TTL (max-age or s-maxage).
   * Setting a TTL of "0" means "always revalidate". The value of defaultTTL
   * cannot be set to a value greater than that of maxTTL, but can be equal.
   * When the cacheMode is set to FORCE_CACHE_ALL, the defaultTTL will overwrite
   * the TTL set in all responses. The maximum allowed value is 31,622,400s (1
   * year), noting that infrequently accessed objects may be evicted from the
   * cache before the defined TTL.
   *
   * @param int $defaultTtl
   */
  public function setDefaultTtl($defaultTtl)
  {
    $this->defaultTtl = $defaultTtl;
  }
  /**
   * @return int
   */
  public function getDefaultTtl()
  {
    return $this->defaultTtl;
  }
  /**
   * Specifies the maximum allowed TTL for cached content served by this origin.
   * Cache directives that attempt to set a max-age or s-maxage higher than
   * this, or an Expires header more than maxTTL seconds in the future will be
   * capped at the value of maxTTL, as if it were the value of an s-maxage
   * Cache-Control directive. Headers sent to the client will not be modified.
   * Setting a TTL of "0" means "always revalidate". The maximum allowed value
   * is 31,622,400s (1 year), noting that infrequently accessed objects may be
   * evicted from the cache before the defined TTL.
   *
   * @param int $maxTtl
   */
  public function setMaxTtl($maxTtl)
  {
    $this->maxTtl = $maxTtl;
  }
  /**
   * @return int
   */
  public function getMaxTtl()
  {
    return $this->maxTtl;
  }
  /**
   * Negative caching allows per-status code TTLs to be set, in order to apply
   * fine-grained caching for common errors or redirects. This can reduce the
   * load on your origin and improve end-user experience by reducing response
   * latency. When the cache mode is set to CACHE_ALL_STATIC or
   * USE_ORIGIN_HEADERS, negative caching applies to responses with the
   * specified response code that lack any Cache-Control, Expires, or Pragma:
   * no-cache directives. When the cache mode is set to FORCE_CACHE_ALL,
   * negative caching applies to all responses with the specified response code,
   * and override any caching headers. By default, Cloud CDN will apply the
   * following default TTLs to these status codes: HTTP 300 (Multiple Choice),
   * 301, 308 (Permanent Redirects): 10m HTTP 404 (Not Found), 410 (Gone), 451
   * (Unavailable For Legal Reasons): 120s HTTP 405 (Method Not Found), 501 (Not
   * Implemented): 60s. These defaults can be overridden in
   * negative_caching_policy.
   *
   * @param bool $negativeCaching
   */
  public function setNegativeCaching($negativeCaching)
  {
    $this->negativeCaching = $negativeCaching;
  }
  /**
   * @return bool
   */
  public function getNegativeCaching()
  {
    return $this->negativeCaching;
  }
  /**
   * Sets a cache TTL for the specified HTTP status code. negative_caching must
   * be enabled to configure negative_caching_policy. Omitting the policy and
   * leaving negative_caching enabled will use Cloud CDN's default cache TTLs.
   * Note that when specifying an explicit negative_caching_policy, you should
   * take care to specify a cache TTL for all response codes that you wish to
   * cache. Cloud CDN will not apply any default negative caching when a policy
   * exists.
   *
   * @param BackendServiceCdnPolicyNegativeCachingPolicy[] $negativeCachingPolicy
   */
  public function setNegativeCachingPolicy($negativeCachingPolicy)
  {
    $this->negativeCachingPolicy = $negativeCachingPolicy;
  }
  /**
   * @return BackendServiceCdnPolicyNegativeCachingPolicy[]
   */
  public function getNegativeCachingPolicy()
  {
    return $this->negativeCachingPolicy;
  }
  /**
   * If true then Cloud CDN will combine multiple concurrent cache fill requests
   * into a small number of requests to the origin.
   *
   * @param bool $requestCoalescing
   */
  public function setRequestCoalescing($requestCoalescing)
  {
    $this->requestCoalescing = $requestCoalescing;
  }
  /**
   * @return bool
   */
  public function getRequestCoalescing()
  {
    return $this->requestCoalescing;
  }
  /**
   * Serve existing content from the cache (if available) when revalidating
   * content with the origin, or when an error is encountered when refreshing
   * the cache. This setting defines the default "max-stale" duration for any
   * cached responses that do not specify a max-stale directive. Stale responses
   * that exceed the TTL configured here will not be served. The default limit
   * (max-stale) is 86400s (1 day), which will allow stale content to be served
   * up to this limit beyond the max-age (or s-maxage) of a cached response. The
   * maximum allowed value is 604800 (1 week). Set this to zero (0) to disable
   * serve-while-stale.
   *
   * @param int $serveWhileStale
   */
  public function setServeWhileStale($serveWhileStale)
  {
    $this->serveWhileStale = $serveWhileStale;
  }
  /**
   * @return int
   */
  public function getServeWhileStale()
  {
    return $this->serveWhileStale;
  }
  /**
   * Maximum number of seconds the response to a signed URL request will be
   * considered fresh. After this time period, the response will be revalidated
   * before being served. Defaults to 1hr (3600s).  When serving responses to
   * signed URL requests, Cloud CDN will internally behave as though all
   * responses from this backend had a "Cache-Control: public, max-age=[TTL]"
   * header, regardless of any existing Cache-Control header. The actual headers
   * served in responses will not be altered.
   *
   * @param string $signedUrlCacheMaxAgeSec
   */
  public function setSignedUrlCacheMaxAgeSec($signedUrlCacheMaxAgeSec)
  {
    $this->signedUrlCacheMaxAgeSec = $signedUrlCacheMaxAgeSec;
  }
  /**
   * @return string
   */
  public function getSignedUrlCacheMaxAgeSec()
  {
    return $this->signedUrlCacheMaxAgeSec;
  }
  /**
   * [Output Only] Names of the keys for signing request URLs.
   *
   * @param string[] $signedUrlKeyNames
   */
  public function setSignedUrlKeyNames($signedUrlKeyNames)
  {
    $this->signedUrlKeyNames = $signedUrlKeyNames;
  }
  /**
   * @return string[]
   */
  public function getSignedUrlKeyNames()
  {
    return $this->signedUrlKeyNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendServiceCdnPolicy::class, 'Google_Service_Compute_BackendServiceCdnPolicy');
