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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ErrorsQuotaErrorDetails extends \Google\Model
{
  /**
   * Unspecified enum
   */
  public const RATE_SCOPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const RATE_SCOPE_UNKNOWN = 'UNKNOWN';
  /**
   * Per customer account quota
   */
  public const RATE_SCOPE_ACCOUNT = 'ACCOUNT';
  /**
   * Per project quota
   */
  public const RATE_SCOPE_DEVELOPER = 'DEVELOPER';
  /**
   * The high level description of the quota bucket. Examples are "Get requests
   * for standard access" or "Requests per account".
   *
   * @var string
   */
  public $rateName;
  /**
   * The rate scope of the quota limit.
   *
   * @var string
   */
  public $rateScope;
  /**
   * Backoff period that customers should wait before sending next request.
   *
   * @var string
   */
  public $retryDelay;

  /**
   * The high level description of the quota bucket. Examples are "Get requests
   * for standard access" or "Requests per account".
   *
   * @param string $rateName
   */
  public function setRateName($rateName)
  {
    $this->rateName = $rateName;
  }
  /**
   * @return string
   */
  public function getRateName()
  {
    return $this->rateName;
  }
  /**
   * The rate scope of the quota limit.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ACCOUNT, DEVELOPER
   *
   * @param self::RATE_SCOPE_* $rateScope
   */
  public function setRateScope($rateScope)
  {
    $this->rateScope = $rateScope;
  }
  /**
   * @return self::RATE_SCOPE_*
   */
  public function getRateScope()
  {
    return $this->rateScope;
  }
  /**
   * Backoff period that customers should wait before sending next request.
   *
   * @param string $retryDelay
   */
  public function setRetryDelay($retryDelay)
  {
    $this->retryDelay = $retryDelay;
  }
  /**
   * @return string
   */
  public function getRetryDelay()
  {
    return $this->retryDelay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ErrorsQuotaErrorDetails::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ErrorsQuotaErrorDetails');
