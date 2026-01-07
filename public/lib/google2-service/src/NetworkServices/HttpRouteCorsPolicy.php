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

namespace Google\Service\NetworkServices;

class HttpRouteCorsPolicy extends \Google\Collection
{
  protected $collection_key = 'exposeHeaders';
  /**
   * In response to a preflight request, setting this to true indicates that the
   * actual request can include user credentials. This translates to the Access-
   * Control-Allow-Credentials header. Default value is false.
   *
   * @var bool
   */
  public $allowCredentials;
  /**
   * Specifies the content for Access-Control-Allow-Headers header.
   *
   * @var string[]
   */
  public $allowHeaders;
  /**
   * Specifies the content for Access-Control-Allow-Methods header.
   *
   * @var string[]
   */
  public $allowMethods;
  /**
   * Specifies the regular expression patterns that match allowed origins. For
   * regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax.
   *
   * @var string[]
   */
  public $allowOriginRegexes;
  /**
   * Specifies the list of origins that will be allowed to do CORS requests. An
   * origin is allowed if it matches either an item in allow_origins or an item
   * in allow_origin_regexes.
   *
   * @var string[]
   */
  public $allowOrigins;
  /**
   * If true, the CORS policy is disabled. The default value is false, which
   * indicates that the CORS policy is in effect.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Specifies the content for Access-Control-Expose-Headers header.
   *
   * @var string[]
   */
  public $exposeHeaders;
  /**
   * Specifies how long result of a preflight request can be cached in seconds.
   * This translates to the Access-Control-Max-Age header.
   *
   * @var string
   */
  public $maxAge;

  /**
   * In response to a preflight request, setting this to true indicates that the
   * actual request can include user credentials. This translates to the Access-
   * Control-Allow-Credentials header. Default value is false.
   *
   * @param bool $allowCredentials
   */
  public function setAllowCredentials($allowCredentials)
  {
    $this->allowCredentials = $allowCredentials;
  }
  /**
   * @return bool
   */
  public function getAllowCredentials()
  {
    return $this->allowCredentials;
  }
  /**
   * Specifies the content for Access-Control-Allow-Headers header.
   *
   * @param string[] $allowHeaders
   */
  public function setAllowHeaders($allowHeaders)
  {
    $this->allowHeaders = $allowHeaders;
  }
  /**
   * @return string[]
   */
  public function getAllowHeaders()
  {
    return $this->allowHeaders;
  }
  /**
   * Specifies the content for Access-Control-Allow-Methods header.
   *
   * @param string[] $allowMethods
   */
  public function setAllowMethods($allowMethods)
  {
    $this->allowMethods = $allowMethods;
  }
  /**
   * @return string[]
   */
  public function getAllowMethods()
  {
    return $this->allowMethods;
  }
  /**
   * Specifies the regular expression patterns that match allowed origins. For
   * regular expression grammar, please see
   * https://github.com/google/re2/wiki/Syntax.
   *
   * @param string[] $allowOriginRegexes
   */
  public function setAllowOriginRegexes($allowOriginRegexes)
  {
    $this->allowOriginRegexes = $allowOriginRegexes;
  }
  /**
   * @return string[]
   */
  public function getAllowOriginRegexes()
  {
    return $this->allowOriginRegexes;
  }
  /**
   * Specifies the list of origins that will be allowed to do CORS requests. An
   * origin is allowed if it matches either an item in allow_origins or an item
   * in allow_origin_regexes.
   *
   * @param string[] $allowOrigins
   */
  public function setAllowOrigins($allowOrigins)
  {
    $this->allowOrigins = $allowOrigins;
  }
  /**
   * @return string[]
   */
  public function getAllowOrigins()
  {
    return $this->allowOrigins;
  }
  /**
   * If true, the CORS policy is disabled. The default value is false, which
   * indicates that the CORS policy is in effect.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Specifies the content for Access-Control-Expose-Headers header.
   *
   * @param string[] $exposeHeaders
   */
  public function setExposeHeaders($exposeHeaders)
  {
    $this->exposeHeaders = $exposeHeaders;
  }
  /**
   * @return string[]
   */
  public function getExposeHeaders()
  {
    return $this->exposeHeaders;
  }
  /**
   * Specifies how long result of a preflight request can be cached in seconds.
   * This translates to the Access-Control-Max-Age header.
   *
   * @param string $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
  }
  /**
   * @return string
   */
  public function getMaxAge()
  {
    return $this->maxAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteCorsPolicy::class, 'Google_Service_NetworkServices_HttpRouteCorsPolicy');
