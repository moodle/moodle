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

namespace Google\Service\PaymentsResellerSubscription;

class GoogleOnePayload extends \Google\Collection
{
  /**
   * The type of partner offering is unspecified.
   */
  public const OFFERING_OFFERING_UNSPECIFIED = 'OFFERING_UNSPECIFIED';
  /**
   * Google One product purchased as a Value added service in addition to
   * existing partner's products. Customer pays additional amount for Google One
   * product.
   */
  public const OFFERING_OFFERING_VAS_BUNDLE = 'OFFERING_VAS_BUNDLE';
  /**
   * Google One product purchased by itself by customer as a value add service.
   * Customer pays additional amount for Google One product.
   */
  public const OFFERING_OFFERING_VAS_STANDALONE = 'OFFERING_VAS_STANDALONE';
  /**
   * Product purchased as part of a hard bundle where Google One was included
   * with the bundle. Google One pricing is included in the bundle.
   */
  public const OFFERING_OFFERING_HARD_BUNDLE = 'OFFERING_HARD_BUNDLE';
  /**
   * Purchased as part of a bundle where Google One was provided as an option.
   * Google One pricing is included in the bundle.
   */
  public const OFFERING_OFFERING_SOFT_BUNDLE = 'OFFERING_SOFT_BUNDLE';
  /**
   * The channel type is unspecified.
   */
  public const SALES_CHANNEL_CHANNEL_UNSPECIFIED = 'CHANNEL_UNSPECIFIED';
  /**
   * Sold at store.
   */
  public const SALES_CHANNEL_CHANNEL_RETAIL = 'CHANNEL_RETAIL';
  /**
   * Sold through partner website.
   */
  public const SALES_CHANNEL_CHANNEL_ONLINE_WEB = 'CHANNEL_ONLINE_WEB';
  /**
   * Sold through partner android app.
   */
  public const SALES_CHANNEL_CHANNEL_ONLINE_ANDROID_APP = 'CHANNEL_ONLINE_ANDROID_APP';
  /**
   * Sold through partner iOS app.
   */
  public const SALES_CHANNEL_CHANNEL_ONLINE_IOS_APP = 'CHANNEL_ONLINE_IOS_APP';
  protected $collection_key = 'campaigns';
  /**
   * Campaign attributed to sales of this subscription.
   *
   * @var string[]
   */
  public $campaigns;
  /**
   * The type of offering the subscription was sold by the partner. e.g. VAS.
   *
   * @var string
   */
  public $offering;
  /**
   * The type of sales channel through which the subscription was sold.
   *
   * @var string
   */
  public $salesChannel;
  /**
   * The identifier for the partner store where the subscription was sold.
   *
   * @var string
   */
  public $storeId;

  /**
   * Campaign attributed to sales of this subscription.
   *
   * @param string[] $campaigns
   */
  public function setCampaigns($campaigns)
  {
    $this->campaigns = $campaigns;
  }
  /**
   * @return string[]
   */
  public function getCampaigns()
  {
    return $this->campaigns;
  }
  /**
   * The type of offering the subscription was sold by the partner. e.g. VAS.
   *
   * Accepted values: OFFERING_UNSPECIFIED, OFFERING_VAS_BUNDLE,
   * OFFERING_VAS_STANDALONE, OFFERING_HARD_BUNDLE, OFFERING_SOFT_BUNDLE
   *
   * @param self::OFFERING_* $offering
   */
  public function setOffering($offering)
  {
    $this->offering = $offering;
  }
  /**
   * @return self::OFFERING_*
   */
  public function getOffering()
  {
    return $this->offering;
  }
  /**
   * The type of sales channel through which the subscription was sold.
   *
   * Accepted values: CHANNEL_UNSPECIFIED, CHANNEL_RETAIL, CHANNEL_ONLINE_WEB,
   * CHANNEL_ONLINE_ANDROID_APP, CHANNEL_ONLINE_IOS_APP
   *
   * @param self::SALES_CHANNEL_* $salesChannel
   */
  public function setSalesChannel($salesChannel)
  {
    $this->salesChannel = $salesChannel;
  }
  /**
   * @return self::SALES_CHANNEL_*
   */
  public function getSalesChannel()
  {
    return $this->salesChannel;
  }
  /**
   * The identifier for the partner store where the subscription was sold.
   *
   * @param string $storeId
   */
  public function setStoreId($storeId)
  {
    $this->storeId = $storeId;
  }
  /**
   * @return string
   */
  public function getStoreId()
  {
    return $this->storeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleOnePayload::class, 'Google_Service_PaymentsResellerSubscription_GoogleOnePayload');
