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

namespace Google\Service\AdExchangeBuyerII;

class DealPauseStatus extends \Google\Model
{
  /**
   * A placeholder for an undefined buyer/seller role.
   */
  public const FIRST_PAUSED_BY_BUYER_SELLER_ROLE_UNSPECIFIED = 'BUYER_SELLER_ROLE_UNSPECIFIED';
  /**
   * Specifies the role as buyer.
   */
  public const FIRST_PAUSED_BY_BUYER = 'BUYER';
  /**
   * Specifies the role as seller.
   */
  public const FIRST_PAUSED_BY_SELLER = 'SELLER';
  /**
   * The buyer's reason for pausing, if the buyer paused the deal.
   *
   * @var string
   */
  public $buyerPauseReason;
  /**
   * The role of the person who first paused this deal.
   *
   * @var string
   */
  public $firstPausedBy;
  /**
   * True, if the buyer has paused the deal unilaterally.
   *
   * @var bool
   */
  public $hasBuyerPaused;
  /**
   * True, if the seller has paused the deal unilaterally.
   *
   * @var bool
   */
  public $hasSellerPaused;
  /**
   * The seller's reason for pausing, if the seller paused the deal.
   *
   * @var string
   */
  public $sellerPauseReason;

  /**
   * The buyer's reason for pausing, if the buyer paused the deal.
   *
   * @param string $buyerPauseReason
   */
  public function setBuyerPauseReason($buyerPauseReason)
  {
    $this->buyerPauseReason = $buyerPauseReason;
  }
  /**
   * @return string
   */
  public function getBuyerPauseReason()
  {
    return $this->buyerPauseReason;
  }
  /**
   * The role of the person who first paused this deal.
   *
   * Accepted values: BUYER_SELLER_ROLE_UNSPECIFIED, BUYER, SELLER
   *
   * @param self::FIRST_PAUSED_BY_* $firstPausedBy
   */
  public function setFirstPausedBy($firstPausedBy)
  {
    $this->firstPausedBy = $firstPausedBy;
  }
  /**
   * @return self::FIRST_PAUSED_BY_*
   */
  public function getFirstPausedBy()
  {
    return $this->firstPausedBy;
  }
  /**
   * True, if the buyer has paused the deal unilaterally.
   *
   * @param bool $hasBuyerPaused
   */
  public function setHasBuyerPaused($hasBuyerPaused)
  {
    $this->hasBuyerPaused = $hasBuyerPaused;
  }
  /**
   * @return bool
   */
  public function getHasBuyerPaused()
  {
    return $this->hasBuyerPaused;
  }
  /**
   * True, if the seller has paused the deal unilaterally.
   *
   * @param bool $hasSellerPaused
   */
  public function setHasSellerPaused($hasSellerPaused)
  {
    $this->hasSellerPaused = $hasSellerPaused;
  }
  /**
   * @return bool
   */
  public function getHasSellerPaused()
  {
    return $this->hasSellerPaused;
  }
  /**
   * The seller's reason for pausing, if the seller paused the deal.
   *
   * @param string $sellerPauseReason
   */
  public function setSellerPauseReason($sellerPauseReason)
  {
    $this->sellerPauseReason = $sellerPauseReason;
  }
  /**
   * @return string
   */
  public function getSellerPauseReason()
  {
    return $this->sellerPauseReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DealPauseStatus::class, 'Google_Service_AdExchangeBuyerII_DealPauseStatus');
