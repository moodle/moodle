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

namespace Google\Service\Merchant;

class ProductReviewItemLevelIssue extends \Google\Model
{
  /**
   * Not specified.
   */
  public const REPORTING_CONTEXT_REPORTING_CONTEXT_ENUM_UNSPECIFIED = 'REPORTING_CONTEXT_ENUM_UNSPECIFIED';
  /**
   * [Shopping ads](https://support.google.com/merchants/answer/6149970).
   */
  public const REPORTING_CONTEXT_SHOPPING_ADS = 'SHOPPING_ADS';
  /**
   * Deprecated: Use `DEMAND_GEN_ADS` instead. [Discovery and Demand Gen
   * ads](https://support.google.com/merchants/answer/13389785).
   *
   * @deprecated
   */
  public const REPORTING_CONTEXT_DISCOVERY_ADS = 'DISCOVERY_ADS';
  /**
   * [Demand Gen ads](https://support.google.com/merchants/answer/13389785).
   */
  public const REPORTING_CONTEXT_DEMAND_GEN_ADS = 'DEMAND_GEN_ADS';
  /**
   * [Demand Gen ads on Discover
   * surface](https://support.google.com/merchants/answer/13389785).
   */
  public const REPORTING_CONTEXT_DEMAND_GEN_ADS_DISCOVER_SURFACE = 'DEMAND_GEN_ADS_DISCOVER_SURFACE';
  /**
   * [Video ads](https://support.google.com/google-ads/answer/6340491).
   */
  public const REPORTING_CONTEXT_VIDEO_ADS = 'VIDEO_ADS';
  /**
   * [Display ads](https://support.google.com/merchants/answer/6069387).
   */
  public const REPORTING_CONTEXT_DISPLAY_ADS = 'DISPLAY_ADS';
  /**
   * [Local inventory ads](https://support.google.com/merchants/answer/3271956).
   */
  public const REPORTING_CONTEXT_LOCAL_INVENTORY_ADS = 'LOCAL_INVENTORY_ADS';
  /**
   * [Vehicle inventory
   * ads](https://support.google.com/merchants/answer/11544533).
   */
  public const REPORTING_CONTEXT_VEHICLE_INVENTORY_ADS = 'VEHICLE_INVENTORY_ADS';
  /**
   * [Free product
   * listings](https://support.google.com/merchants/answer/9199328).
   */
  public const REPORTING_CONTEXT_FREE_LISTINGS = 'FREE_LISTINGS';
  /**
   * [Free local product
   * listings](https://support.google.com/merchants/answer/9825611).
   */
  public const REPORTING_CONTEXT_FREE_LOCAL_LISTINGS = 'FREE_LOCAL_LISTINGS';
  /**
   * [Free local vehicle
   * listings](https://support.google.com/merchants/answer/11544533).
   */
  public const REPORTING_CONTEXT_FREE_LOCAL_VEHICLE_LISTINGS = 'FREE_LOCAL_VEHICLE_LISTINGS';
  /**
   * [Youtube Affiliate](https://support.google.com/youtube/answer/13376398).
   */
  public const REPORTING_CONTEXT_YOUTUBE_AFFILIATE = 'YOUTUBE_AFFILIATE';
  /**
   * [YouTube Shopping](https://support.google.com/merchants/answer/13478370).
   */
  public const REPORTING_CONTEXT_YOUTUBE_SHOPPING = 'YOUTUBE_SHOPPING';
  /**
   * [Cloud retail](https://cloud.google.com/solutions/retail).
   */
  public const REPORTING_CONTEXT_CLOUD_RETAIL = 'CLOUD_RETAIL';
  /**
   * [Local cloud retail](https://cloud.google.com/solutions/retail).
   */
  public const REPORTING_CONTEXT_LOCAL_CLOUD_RETAIL = 'LOCAL_CLOUD_RETAIL';
  /**
   * [Product Reviews](https://support.google.com/merchants/answer/14620732).
   */
  public const REPORTING_CONTEXT_PRODUCT_REVIEWS = 'PRODUCT_REVIEWS';
  /**
   * [Merchant Reviews](https://developers.google.com/merchant-review-feeds).
   */
  public const REPORTING_CONTEXT_MERCHANT_REVIEWS = 'MERCHANT_REVIEWS';
  /**
   * YouTube Checkout .
   */
  public const REPORTING_CONTEXT_YOUTUBE_CHECKOUT = 'YOUTUBE_CHECKOUT';
  /**
   * Not specified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * This issue represents a warning and does not have a direct affect on the
   * product review.
   */
  public const SEVERITY_NOT_IMPACTED = 'NOT_IMPACTED';
  /**
   * Issue disapproves the product review.
   */
  public const SEVERITY_DISAPPROVED = 'DISAPPROVED';
  /**
   * Output only. The attribute's name, if the issue is caused by a single
   * attribute.
   *
   * @var string
   */
  public $attribute;
  /**
   * Output only. The error code of the issue.
   *
   * @var string
   */
  public $code;
  /**
   * Output only. A short issue description in English.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. A detailed issue description in English.
   *
   * @var string
   */
  public $detail;
  /**
   * Output only. The URL of a web page to help with resolving this issue.
   *
   * @var string
   */
  public $documentation;
  /**
   * Output only. The reporting context the issue applies to.
   *
   * @var string
   */
  public $reportingContext;
  /**
   * Output only. Whether the issue can be resolved by the merchant.
   *
   * @var string
   */
  public $resolution;
  /**
   * Output only. How this issue affects serving of the product review.
   *
   * @var string
   */
  public $severity;

  /**
   * Output only. The attribute's name, if the issue is caused by a single
   * attribute.
   *
   * @param string $attribute
   */
  public function setAttribute($attribute)
  {
    $this->attribute = $attribute;
  }
  /**
   * @return string
   */
  public function getAttribute()
  {
    return $this->attribute;
  }
  /**
   * Output only. The error code of the issue.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Output only. A short issue description in English.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. A detailed issue description in English.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Output only. The URL of a web page to help with resolving this issue.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Output only. The reporting context the issue applies to.
   *
   * Accepted values: REPORTING_CONTEXT_ENUM_UNSPECIFIED, SHOPPING_ADS,
   * DISCOVERY_ADS, DEMAND_GEN_ADS, DEMAND_GEN_ADS_DISCOVER_SURFACE, VIDEO_ADS,
   * DISPLAY_ADS, LOCAL_INVENTORY_ADS, VEHICLE_INVENTORY_ADS, FREE_LISTINGS,
   * FREE_LOCAL_LISTINGS, FREE_LOCAL_VEHICLE_LISTINGS, YOUTUBE_AFFILIATE,
   * YOUTUBE_SHOPPING, CLOUD_RETAIL, LOCAL_CLOUD_RETAIL, PRODUCT_REVIEWS,
   * MERCHANT_REVIEWS, YOUTUBE_CHECKOUT
   *
   * @param self::REPORTING_CONTEXT_* $reportingContext
   */
  public function setReportingContext($reportingContext)
  {
    $this->reportingContext = $reportingContext;
  }
  /**
   * @return self::REPORTING_CONTEXT_*
   */
  public function getReportingContext()
  {
    return $this->reportingContext;
  }
  /**
   * Output only. Whether the issue can be resolved by the merchant.
   *
   * @param string $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return string
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * Output only. How this issue affects serving of the product review.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, NOT_IMPACTED, DISAPPROVED
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductReviewItemLevelIssue::class, 'Google_Service_Merchant_ProductReviewItemLevelIssue');
