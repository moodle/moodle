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

class FinalizedDeal extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const DEAL_SERVING_STATUS_DEAL_SERVING_STATUS_UNSPECIFIED = 'DEAL_SERVING_STATUS_UNSPECIFIED';
  /**
   * The deal is actively serving or ready to serve when the start date is
   * reached.
   */
  public const DEAL_SERVING_STATUS_ACTIVE = 'ACTIVE';
  /**
   * The deal serving has ended.
   */
  public const DEAL_SERVING_STATUS_ENDED = 'ENDED';
  /**
   * The deal serving is paused by buyer.
   */
  public const DEAL_SERVING_STATUS_PAUSED_BY_BUYER = 'PAUSED_BY_BUYER';
  /**
   * The deal serving is paused by seller.
   */
  public const DEAL_SERVING_STATUS_PAUSED_BY_SELLER = 'PAUSED_BY_SELLER';
  protected $dealType = Deal::class;
  protected $dealDataType = '';
  protected $dealPausingInfoType = DealPausingInfo::class;
  protected $dealPausingInfoDataType = '';
  /**
   * Serving status of the deal.
   *
   * @var string
   */
  public $dealServingStatus;
  /**
   * The resource name of the finalized deal. Format:
   * `buyers/{accountId}/finalizedDeals/{finalizedDealId}`
   *
   * @var string
   */
  public $name;
  /**
   * Whether the Programmatic Guaranteed deal is ready for serving.
   *
   * @var bool
   */
  public $readyToServe;
  protected $rtbMetricsType = RtbMetrics::class;
  protected $rtbMetricsDataType = '';

  /**
   * A copy of the Deal made upon finalization. During renegotiation, this will
   * reflect the last finalized deal before renegotiation was initiated.
   *
   * @param Deal $deal
   */
  public function setDeal(Deal $deal)
  {
    $this->deal = $deal;
  }
  /**
   * @return Deal
   */
  public function getDeal()
  {
    return $this->deal;
  }
  /**
   * Information related to deal pausing for the deal.
   *
   * @param DealPausingInfo $dealPausingInfo
   */
  public function setDealPausingInfo(DealPausingInfo $dealPausingInfo)
  {
    $this->dealPausingInfo = $dealPausingInfo;
  }
  /**
   * @return DealPausingInfo
   */
  public function getDealPausingInfo()
  {
    return $this->dealPausingInfo;
  }
  /**
   * Serving status of the deal.
   *
   * Accepted values: DEAL_SERVING_STATUS_UNSPECIFIED, ACTIVE, ENDED,
   * PAUSED_BY_BUYER, PAUSED_BY_SELLER
   *
   * @param self::DEAL_SERVING_STATUS_* $dealServingStatus
   */
  public function setDealServingStatus($dealServingStatus)
  {
    $this->dealServingStatus = $dealServingStatus;
  }
  /**
   * @return self::DEAL_SERVING_STATUS_*
   */
  public function getDealServingStatus()
  {
    return $this->dealServingStatus;
  }
  /**
   * The resource name of the finalized deal. Format:
   * `buyers/{accountId}/finalizedDeals/{finalizedDealId}`
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Whether the Programmatic Guaranteed deal is ready for serving.
   *
   * @param bool $readyToServe
   */
  public function setReadyToServe($readyToServe)
  {
    $this->readyToServe = $readyToServe;
  }
  /**
   * @return bool
   */
  public function getReadyToServe()
  {
    return $this->readyToServe;
  }
  /**
   * Real-time bidding metrics for this deal.
   *
   * @param RtbMetrics $rtbMetrics
   */
  public function setRtbMetrics(RtbMetrics $rtbMetrics)
  {
    $this->rtbMetrics = $rtbMetrics;
  }
  /**
   * @return RtbMetrics
   */
  public function getRtbMetrics()
  {
    return $this->rtbMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FinalizedDeal::class, 'Google_Service_AuthorizedBuyersMarketplace_FinalizedDeal');
