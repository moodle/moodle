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

class CacheKeyPolicy extends \Google\Collection
{
  protected $collection_key = 'queryStringWhitelist';
  /**
   * If true, requests to different hosts will be cached separately.
   *
   * @var bool
   */
  public $includeHost;
  /**
   * Allows HTTP request headers (by name) to be used in the cache key.
   *
   * @var string[]
   */
  public $includeHttpHeaders;
  /**
   * Allows HTTP cookies (by name) to be used in the cache key. The name=value
   * pair will be used in the cache key Cloud CDN generates.
   *
   * @var string[]
   */
  public $includeNamedCookies;
  /**
   * If true, http and https requests will be cached separately.
   *
   * @var bool
   */
  public $includeProtocol;
  /**
   * If true, include query string parameters in the cache key according to
   * query_string_whitelist and query_string_blacklist. If neither is set, the
   * entire query string will be included. If false, the query string will be
   * excluded from the cache key entirely.
   *
   * @var bool
   */
  public $includeQueryString;
  /**
   * Names of query string parameters to exclude in cache keys. All other
   * parameters will be included. Either specify query_string_whitelist or
   * query_string_blacklist, not both. '&' and '=' will be percent encoded and
   * not treated as delimiters.
   *
   * @var string[]
   */
  public $queryStringBlacklist;
  /**
   * Names of query string parameters to include in cache keys. All other
   * parameters will be excluded. Either specify query_string_whitelist or
   * query_string_blacklist, not both. '&' and '=' will be percent encoded and
   * not treated as delimiters.
   *
   * @var string[]
   */
  public $queryStringWhitelist;

  /**
   * If true, requests to different hosts will be cached separately.
   *
   * @param bool $includeHost
   */
  public function setIncludeHost($includeHost)
  {
    $this->includeHost = $includeHost;
  }
  /**
   * @return bool
   */
  public function getIncludeHost()
  {
    return $this->includeHost;
  }
  /**
   * Allows HTTP request headers (by name) to be used in the cache key.
   *
   * @param string[] $includeHttpHeaders
   */
  public function setIncludeHttpHeaders($includeHttpHeaders)
  {
    $this->includeHttpHeaders = $includeHttpHeaders;
  }
  /**
   * @return string[]
   */
  public function getIncludeHttpHeaders()
  {
    return $this->includeHttpHeaders;
  }
  /**
   * Allows HTTP cookies (by name) to be used in the cache key. The name=value
   * pair will be used in the cache key Cloud CDN generates.
   *
   * @param string[] $includeNamedCookies
   */
  public function setIncludeNamedCookies($includeNamedCookies)
  {
    $this->includeNamedCookies = $includeNamedCookies;
  }
  /**
   * @return string[]
   */
  public function getIncludeNamedCookies()
  {
    return $this->includeNamedCookies;
  }
  /**
   * If true, http and https requests will be cached separately.
   *
   * @param bool $includeProtocol
   */
  public function setIncludeProtocol($includeProtocol)
  {
    $this->includeProtocol = $includeProtocol;
  }
  /**
   * @return bool
   */
  public function getIncludeProtocol()
  {
    return $this->includeProtocol;
  }
  /**
   * If true, include query string parameters in the cache key according to
   * query_string_whitelist and query_string_blacklist. If neither is set, the
   * entire query string will be included. If false, the query string will be
   * excluded from the cache key entirely.
   *
   * @param bool $includeQueryString
   */
  public function setIncludeQueryString($includeQueryString)
  {
    $this->includeQueryString = $includeQueryString;
  }
  /**
   * @return bool
   */
  public function getIncludeQueryString()
  {
    return $this->includeQueryString;
  }
  /**
   * Names of query string parameters to exclude in cache keys. All other
   * parameters will be included. Either specify query_string_whitelist or
   * query_string_blacklist, not both. '&' and '=' will be percent encoded and
   * not treated as delimiters.
   *
   * @param string[] $queryStringBlacklist
   */
  public function setQueryStringBlacklist($queryStringBlacklist)
  {
    $this->queryStringBlacklist = $queryStringBlacklist;
  }
  /**
   * @return string[]
   */
  public function getQueryStringBlacklist()
  {
    return $this->queryStringBlacklist;
  }
  /**
   * Names of query string parameters to include in cache keys. All other
   * parameters will be excluded. Either specify query_string_whitelist or
   * query_string_blacklist, not both. '&' and '=' will be percent encoded and
   * not treated as delimiters.
   *
   * @param string[] $queryStringWhitelist
   */
  public function setQueryStringWhitelist($queryStringWhitelist)
  {
    $this->queryStringWhitelist = $queryStringWhitelist;
  }
  /**
   * @return string[]
   */
  public function getQueryStringWhitelist()
  {
    return $this->queryStringWhitelist;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CacheKeyPolicy::class, 'Google_Service_Compute_CacheKeyPolicy');
