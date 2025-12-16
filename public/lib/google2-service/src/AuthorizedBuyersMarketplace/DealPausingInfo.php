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

namespace Google\Service\AuthorizedBuyersMarketplace;

class DealPausingInfo extends \Google\Model
{
  /**
   * A placeholder for an undefined buyer/seller role.
   */
  public const PAUSE_ROLE_BUYER_SELLER_ROLE_UNSPECIFIED = 'BUYER_SELLER_ROLE_UNSPECIFIED';
  /**
   * Specifies the role as buyer.
   */
  public const PAUSE_ROLE_BUYER = 'BUYER';
  /**
   * Specifies the role as seller.
   */
  public const PAUSE_ROLE_SELLER = 'SELLER';
  /**
   * The reason for the pausing of the deal; empty for active deals.
   *
   * @var string
   */
  public $pauseReason;
  /**
   * The party that first paused the deal; unspecified for active deals.
   *
   * @var string
   */
  public $pauseRole;
  /**
   * Whether pausing is consented between buyer and seller for the deal.
   *
   * @var bool
   */
  public $pausingConsented;

  /**
   * The reason for the pausing of the deal; empty for active deals.
   *
   * @param string $pauseReason
   */
  public function setPauseReason($pauseReason)
  {
    $this->pauseReason = $pauseReason;
  }
  /**
   * @return string
   */
  public function getPauseReason()
  {
    return $this->pauseReason;
  }
  /**
   * The party that first paused the deal; unspecified for active deals.
   *
   * Accepted values: BUYER_SELLER_ROLE_UNSPECIFIED, BUYER, SELLER
   *
   * @param self::PAUSE_ROLE_* $pauseRole
   */
  public function setPauseRole($pauseRole)
  {
    $this->pauseRole = $pauseRole;
  }
  /**
   * @return self::PAUSE_ROLE_*
   */
  public function getPauseRole()
  {
    return $this->pauseRole;
  }
  /**
   * Whether pausing is consented between buyer and seller for the deal.
   *
   * @param bool $pausingConsented
   */
  public function setPausingConsented($pausingConsented)
  {
    $this->pausingConsented = $pausingConsented;
  }
  /**
   * @return bool
   */
  public function getPausingConsented()
  {
    return $this->pausingConsented;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DealPausingInfo::class, 'Google_Service_AuthorizedBuyersMarketplace_DealPausingInfo');
