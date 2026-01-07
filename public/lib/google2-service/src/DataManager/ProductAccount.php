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

namespace Google\Service\DataManager;

class ProductAccount extends \Google\Model
{
  /**
   * Unspecified product. Should never be used.
   */
  public const ACCOUNT_TYPE_ACCOUNT_TYPE_UNSPECIFIED = 'ACCOUNT_TYPE_UNSPECIFIED';
  /**
   * Google Ads.
   */
  public const ACCOUNT_TYPE_GOOGLE_ADS = 'GOOGLE_ADS';
  /**
   * Display & Video 360 partner.
   */
  public const ACCOUNT_TYPE_DISPLAY_VIDEO_PARTNER = 'DISPLAY_VIDEO_PARTNER';
  /**
   * Display & Video 360 advertiser.
   */
  public const ACCOUNT_TYPE_DISPLAY_VIDEO_ADVERTISER = 'DISPLAY_VIDEO_ADVERTISER';
  /**
   * Data Partner.
   */
  public const ACCOUNT_TYPE_DATA_PARTNER = 'DATA_PARTNER';
  /**
   * Google Analytics.
   */
  public const ACCOUNT_TYPE_GOOGLE_ANALYTICS_PROPERTY = 'GOOGLE_ANALYTICS_PROPERTY';
  /**
   * Unspecified product. Should never be used.
   */
  public const PRODUCT_PRODUCT_UNSPECIFIED = 'PRODUCT_UNSPECIFIED';
  /**
   * Google Ads.
   */
  public const PRODUCT_GOOGLE_ADS = 'GOOGLE_ADS';
  /**
   * Display & Video 360 partner.
   */
  public const PRODUCT_DISPLAY_VIDEO_PARTNER = 'DISPLAY_VIDEO_PARTNER';
  /**
   * Display & Video 360 advertiser.
   */
  public const PRODUCT_DISPLAY_VIDEO_ADVERTISER = 'DISPLAY_VIDEO_ADVERTISER';
  /**
   * Data Partner.
   */
  public const PRODUCT_DATA_PARTNER = 'DATA_PARTNER';
  /**
   * Required. The ID of the account. For example, your Google Ads account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. The type of the account. For example, `GOOGLE_ADS`. Either
   * `account_type` or the deprecated `product` is required. If both are set,
   * the values must match.
   *
   * @var string
   */
  public $accountType;
  /**
   * Deprecated. Use `account_type` instead.
   *
   * @deprecated
   * @var string
   */
  public $product;

  /**
   * Required. The ID of the account. For example, your Google Ads account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Optional. The type of the account. For example, `GOOGLE_ADS`. Either
   * `account_type` or the deprecated `product` is required. If both are set,
   * the values must match.
   *
   * Accepted values: ACCOUNT_TYPE_UNSPECIFIED, GOOGLE_ADS,
   * DISPLAY_VIDEO_PARTNER, DISPLAY_VIDEO_ADVERTISER, DATA_PARTNER,
   * GOOGLE_ANALYTICS_PROPERTY
   *
   * @param self::ACCOUNT_TYPE_* $accountType
   */
  public function setAccountType($accountType)
  {
    $this->accountType = $accountType;
  }
  /**
   * @return self::ACCOUNT_TYPE_*
   */
  public function getAccountType()
  {
    return $this->accountType;
  }
  /**
   * Deprecated. Use `account_type` instead.
   *
   * Accepted values: PRODUCT_UNSPECIFIED, GOOGLE_ADS, DISPLAY_VIDEO_PARTNER,
   * DISPLAY_VIDEO_ADVERTISER, DATA_PARTNER
   *
   * @deprecated
   * @param self::PRODUCT_* $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }
  /**
   * @deprecated
   * @return self::PRODUCT_*
   */
  public function getProduct()
  {
    return $this->product;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductAccount::class, 'Google_Service_DataManager_ProductAccount');
