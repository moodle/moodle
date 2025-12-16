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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityActionConditionConfig extends \Google\Collection
{
  protected $collection_key = 'userAgents';
  /**
   * Optional. A list of access_tokens. Limit 1000 per action.
   *
   * @var string[]
   */
  public $accessTokens;
  /**
   * Optional. A list of API keys. Limit 1000 per action.
   *
   * @var string[]
   */
  public $apiKeys;
  /**
   * Optional. A list of API Products. Limit 1000 per action.
   *
   * @var string[]
   */
  public $apiProducts;
  /**
   * Optional. A list of ASN numbers to act on, e.g. 23.
   * https://en.wikipedia.org/wiki/Autonomous_system_(Internet) This uses int64
   * instead of uint32 because of https://linter.aip.dev/141/forbidden-types.
   *
   * @var string[]
   */
  public $asns;
  /**
   * Optional. A list of Bot Reasons. Current options: Flooder, Brute Guessor,
   * Static Content Scraper, OAuth Abuser, Robot Abuser, TorListRule, Advanced
   * Anomaly Detection, Advanced API Scraper, Search Engine Crawlers, Public
   * Clouds, Public Cloud AWS, Public Cloud Azure, and Public Cloud Google.
   *
   * @var string[]
   */
  public $botReasons;
  /**
   * Optional. A list of developer apps. Limit 1000 per action.
   *
   * @var string[]
   */
  public $developerApps;
  /**
   * Optional. A list of developers. Limit 1000 per action.
   *
   * @var string[]
   */
  public $developers;
  /**
   * Optional. Act only on particular HTTP methods. E.g. A read-only API can
   * block POST/PUT/DELETE methods. Accepted values are: GET, HEAD, POST, PUT,
   * DELETE, CONNECT, OPTIONS, TRACE and PATCH.
   *
   * @var string[]
   */
  public $httpMethods;
  /**
   * Optional. A list of IP addresses. This could be either IPv4 or IPv6.
   * Limited to 100 per action.
   *
   * @var string[]
   */
  public $ipAddressRanges;
  /**
   * Optional. A list of countries/region codes to act on, e.g. US. This follows
   * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2.
   *
   * @var string[]
   */
  public $regionCodes;
  /**
   * Optional. A list of user agents to deny. We look for exact matches. Limit
   * 50 per action.
   *
   * @var string[]
   */
  public $userAgents;

  /**
   * Optional. A list of access_tokens. Limit 1000 per action.
   *
   * @param string[] $accessTokens
   */
  public function setAccessTokens($accessTokens)
  {
    $this->accessTokens = $accessTokens;
  }
  /**
   * @return string[]
   */
  public function getAccessTokens()
  {
    return $this->accessTokens;
  }
  /**
   * Optional. A list of API keys. Limit 1000 per action.
   *
   * @param string[] $apiKeys
   */
  public function setApiKeys($apiKeys)
  {
    $this->apiKeys = $apiKeys;
  }
  /**
   * @return string[]
   */
  public function getApiKeys()
  {
    return $this->apiKeys;
  }
  /**
   * Optional. A list of API Products. Limit 1000 per action.
   *
   * @param string[] $apiProducts
   */
  public function setApiProducts($apiProducts)
  {
    $this->apiProducts = $apiProducts;
  }
  /**
   * @return string[]
   */
  public function getApiProducts()
  {
    return $this->apiProducts;
  }
  /**
   * Optional. A list of ASN numbers to act on, e.g. 23.
   * https://en.wikipedia.org/wiki/Autonomous_system_(Internet) This uses int64
   * instead of uint32 because of https://linter.aip.dev/141/forbidden-types.
   *
   * @param string[] $asns
   */
  public function setAsns($asns)
  {
    $this->asns = $asns;
  }
  /**
   * @return string[]
   */
  public function getAsns()
  {
    return $this->asns;
  }
  /**
   * Optional. A list of Bot Reasons. Current options: Flooder, Brute Guessor,
   * Static Content Scraper, OAuth Abuser, Robot Abuser, TorListRule, Advanced
   * Anomaly Detection, Advanced API Scraper, Search Engine Crawlers, Public
   * Clouds, Public Cloud AWS, Public Cloud Azure, and Public Cloud Google.
   *
   * @param string[] $botReasons
   */
  public function setBotReasons($botReasons)
  {
    $this->botReasons = $botReasons;
  }
  /**
   * @return string[]
   */
  public function getBotReasons()
  {
    return $this->botReasons;
  }
  /**
   * Optional. A list of developer apps. Limit 1000 per action.
   *
   * @param string[] $developerApps
   */
  public function setDeveloperApps($developerApps)
  {
    $this->developerApps = $developerApps;
  }
  /**
   * @return string[]
   */
  public function getDeveloperApps()
  {
    return $this->developerApps;
  }
  /**
   * Optional. A list of developers. Limit 1000 per action.
   *
   * @param string[] $developers
   */
  public function setDevelopers($developers)
  {
    $this->developers = $developers;
  }
  /**
   * @return string[]
   */
  public function getDevelopers()
  {
    return $this->developers;
  }
  /**
   * Optional. Act only on particular HTTP methods. E.g. A read-only API can
   * block POST/PUT/DELETE methods. Accepted values are: GET, HEAD, POST, PUT,
   * DELETE, CONNECT, OPTIONS, TRACE and PATCH.
   *
   * @param string[] $httpMethods
   */
  public function setHttpMethods($httpMethods)
  {
    $this->httpMethods = $httpMethods;
  }
  /**
   * @return string[]
   */
  public function getHttpMethods()
  {
    return $this->httpMethods;
  }
  /**
   * Optional. A list of IP addresses. This could be either IPv4 or IPv6.
   * Limited to 100 per action.
   *
   * @param string[] $ipAddressRanges
   */
  public function setIpAddressRanges($ipAddressRanges)
  {
    $this->ipAddressRanges = $ipAddressRanges;
  }
  /**
   * @return string[]
   */
  public function getIpAddressRanges()
  {
    return $this->ipAddressRanges;
  }
  /**
   * Optional. A list of countries/region codes to act on, e.g. US. This follows
   * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2.
   *
   * @param string[] $regionCodes
   */
  public function setRegionCodes($regionCodes)
  {
    $this->regionCodes = $regionCodes;
  }
  /**
   * @return string[]
   */
  public function getRegionCodes()
  {
    return $this->regionCodes;
  }
  /**
   * Optional. A list of user agents to deny. We look for exact matches. Limit
   * 50 per action.
   *
   * @param string[] $userAgents
   */
  public function setUserAgents($userAgents)
  {
    $this->userAgents = $userAgents;
  }
  /**
   * @return string[]
   */
  public function getUserAgents()
  {
    return $this->userAgents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityActionConditionConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityActionConditionConfig');
