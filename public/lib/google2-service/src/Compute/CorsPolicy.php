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

class CorsPolicy extends \Google\Collection
{
  protected $collection_key = 'exposeHeaders';
  /**
   * In response to a preflight request, setting this to true indicates that the
   * actual request can include user credentials. This field translates to the
   * Access-Control-Allow-Credentials header.
   *
   * Default is false.
   *
   * @var bool
   */
  public $allowCredentials;
  /**
   * Specifies the content for the Access-Control-Allow-Headers header.
   *
   * @var string[]
   */
  public $allowHeaders;
  /**
   * Specifies the content for the Access-Control-Allow-Methods header.
   *
   * @var string[]
   */
  public $allowMethods;
  /**
   * Specifies a regular expression that matches allowed origins. For more
   * information, see regular expression syntax.
   *
   * An origin is allowed if it matches either an item inallowOrigins or an item
   * inallowOriginRegexes.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED.
   *
   * @var string[]
   */
  public $allowOriginRegexes;
  /**
   * Specifies the list of origins that is allowed to do CORS requests.
   *
   * An origin is allowed if it matches either an item inallowOrigins or an item
   * inallowOriginRegexes.
   *
   * @var string[]
   */
  public $allowOrigins;
  /**
   * If true, disables the CORS policy. The default value is false, which
   * indicates that the CORS policy is in effect.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Specifies the content for the Access-Control-Expose-Headers header.
   *
   * @var string[]
   */
  public $exposeHeaders;
  /**
   * Specifies how long results of a preflight request can be cached in seconds.
   * This field translates to the Access-Control-Max-Age header.
   *
   * @var int
   */
  public $maxAge;

  /**
   * In response to a preflight request, setting this to true indicates that the
   * actual request can include user credentials. This field translates to the
   * Access-Control-Allow-Credentials header.
   *
   * Default is false.
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
   * Specifies the content for the Access-Control-Allow-Headers header.
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
   * Specifies the content for the Access-Control-Allow-Methods header.
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
   * Specifies a regular expression that matches allowed origins. For more
   * information, see regular expression syntax.
   *
   * An origin is allowed if it matches either an item inallowOrigins or an item
   * inallowOriginRegexes.
   *
   * Regular expressions can only be used when the loadBalancingScheme is set to
   * INTERNAL_SELF_MANAGED.
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
   * Specifies the list of origins that is allowed to do CORS requests.
   *
   * An origin is allowed if it matches either an item inallowOrigins or an item
   * inallowOriginRegexes.
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
   * If true, disables the CORS policy. The default value is false, which
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
   * Specifies the content for the Access-Control-Expose-Headers header.
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
   * Specifies how long results of a preflight request can be cached in seconds.
   * This field translates to the Access-Control-Max-Age header.
   *
   * @param int $maxAge
   */
  public function setMaxAge($maxAge)
  {
    $this->maxAge = $maxAge;
  }
  /**
   * @return int
   */
  public function getMaxAge()
  {
    return $this->maxAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CorsPolicy::class, 'Google_Service_Compute_CorsPolicy');
