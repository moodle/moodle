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

namespace Google\Service\RealTimeBidding;

class HttpCookieEvidence extends \Google\Collection
{
  protected $collection_key = 'cookieNames';
  /**
   * Names of cookies that violate Google policies. For TOO_MANY_COOKIES policy,
   * this will be the cookie names of top domains with the largest number of
   * cookies. For other policies, this will be all the cookie names that violate
   * the policy.
   *
   * @var string[]
   */
  public $cookieNames;
  /**
   * The largest number of cookies set by a creative. If this field is set,
   * cookie_names above will be set to the cookie names of top domains with the
   * largest number of cookies. This field will only be set for TOO_MANY_COOKIES
   * policy.
   *
   * @var int
   */
  public $maxCookieCount;

  /**
   * Names of cookies that violate Google policies. For TOO_MANY_COOKIES policy,
   * this will be the cookie names of top domains with the largest number of
   * cookies. For other policies, this will be all the cookie names that violate
   * the policy.
   *
   * @param string[] $cookieNames
   */
  public function setCookieNames($cookieNames)
  {
    $this->cookieNames = $cookieNames;
  }
  /**
   * @return string[]
   */
  public function getCookieNames()
  {
    return $this->cookieNames;
  }
  /**
   * The largest number of cookies set by a creative. If this field is set,
   * cookie_names above will be set to the cookie names of top domains with the
   * largest number of cookies. This field will only be set for TOO_MANY_COOKIES
   * policy.
   *
   * @param int $maxCookieCount
   */
  public function setMaxCookieCount($maxCookieCount)
  {
    $this->maxCookieCount = $maxCookieCount;
  }
  /**
   * @return int
   */
  public function getMaxCookieCount()
  {
    return $this->maxCookieCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpCookieEvidence::class, 'Google_Service_RealTimeBidding_HttpCookieEvidence');
