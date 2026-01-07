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

class Creative extends \Google\Collection
{
  /**
   * The status is unknown.
   */
  public const DEALS_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The creative has not been checked.
   */
  public const DEALS_STATUS_NOT_CHECKED = 'NOT_CHECKED';
  /**
   * The creative has been conditionally approved. See serving_restrictions for
   * details.
   */
  public const DEALS_STATUS_CONDITIONALLY_APPROVED = 'CONDITIONALLY_APPROVED';
  /**
   * The creative has been approved.
   */
  public const DEALS_STATUS_APPROVED = 'APPROVED';
  /**
   * The creative has been disapproved.
   */
  public const DEALS_STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * Placeholder for transition to v1beta1. Currently not used.
   */
  public const DEALS_STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * Placeholder for transition to v1beta1. Currently not used.
   */
  public const DEALS_STATUS_STATUS_TYPE_UNSPECIFIED = 'STATUS_TYPE_UNSPECIFIED';
  /**
   * The status is unknown.
   */
  public const OPEN_AUCTION_STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The creative has not been checked.
   */
  public const OPEN_AUCTION_STATUS_NOT_CHECKED = 'NOT_CHECKED';
  /**
   * The creative has been conditionally approved. See serving_restrictions for
   * details.
   */
  public const OPEN_AUCTION_STATUS_CONDITIONALLY_APPROVED = 'CONDITIONALLY_APPROVED';
  /**
   * The creative has been approved.
   */
  public const OPEN_AUCTION_STATUS_APPROVED = 'APPROVED';
  /**
   * The creative has been disapproved.
   */
  public const OPEN_AUCTION_STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * Placeholder for transition to v1beta1. Currently not used.
   */
  public const OPEN_AUCTION_STATUS_PENDING_REVIEW = 'PENDING_REVIEW';
  /**
   * Placeholder for transition to v1beta1. Currently not used.
   */
  public const OPEN_AUCTION_STATUS_STATUS_TYPE_UNSPECIFIED = 'STATUS_TYPE_UNSPECIFIED';
  protected $collection_key = 'vendorIds';
  /**
   * The account that this creative belongs to. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var string
   */
  public $accountId;
  /**
   * The link to AdChoices destination page.
   *
   * @var string
   */
  public $adChoicesDestinationUrl;
  protected $adTechnologyProvidersType = AdTechnologyProviders::class;
  protected $adTechnologyProvidersDataType = '';
  /**
   * The name of the company being advertised in the creative.
   *
   * @var string
   */
  public $advertiserName;
  /**
   * The agency ID for this creative.
   *
   * @var string
   */
  public $agencyId;
  /**
   * Output only. The last update timestamp of the creative through the API.
   *
   * @var string
   */
  public $apiUpdateTime;
  /**
   * All attributes for the ads that may be shown from this creative. Can be
   * used to filter the response of the creatives.list method.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * The set of destination URLs for the creative.
   *
   * @var string[]
   */
  public $clickThroughUrls;
  protected $correctionsType = Correction::class;
  protected $correctionsDataType = 'array';
  /**
   * The buyer-defined creative ID of this creative. Can be used to filter the
   * response of the creatives.list method.
   *
   * @var string
   */
  public $creativeId;
  /**
   * Output only. The top-level deals status of this creative. If disapproved,
   * an entry for 'auctionType=DIRECT_DEALS' (or 'ALL') in serving_restrictions
   * will also exist. Note that this may be nuanced with other contextual
   * restrictions, in which case, it may be preferable to read from
   * serving_restrictions directly. Can be used to filter the response of the
   * creatives.list method.
   *
   * @var string
   */
  public $dealsStatus;
  /**
   * The set of declared destination URLs for the creative.
   *
   * @var string[]
   */
  public $declaredClickThroughUrls;
  /**
   * Output only. Detected advertiser IDs, if any.
   *
   * @var string[]
   */
  public $detectedAdvertiserIds;
  /**
   * Output only. The detected domains for this creative.
   *
   * @var string[]
   */
  public $detectedDomains;
  /**
   * Output only. The detected languages for this creative. The order is
   * arbitrary. The codes are 2 or 5 characters and are documented at
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes.
   *
   * @var string[]
   */
  public $detectedLanguages;
  /**
   * Output only. Detected product categories, if any. See the ad-product-
   * categories.txt file in the technical documentation for a list of IDs.
   *
   * @var int[]
   */
  public $detectedProductCategories;
  /**
   * Output only. Detected sensitive categories, if any. See the ad-sensitive-
   * categories.txt file in the technical documentation for a list of IDs. You
   * should use these IDs along with the excluded-sensitive-category field in
   * the bid request to filter your bids.
   *
   * @var int[]
   */
  public $detectedSensitiveCategories;
  protected $htmlType = HtmlContent::class;
  protected $htmlDataType = '';
  /**
   * The set of URLs to be called to record an impression.
   *
   * @var string[]
   */
  public $impressionTrackingUrls;
  protected $nativeType = NativeContent::class;
  protected $nativeDataType = '';
  /**
   * Output only. The top-level open auction status of this creative. If
   * disapproved, an entry for 'auctionType = OPEN_AUCTION' (or 'ALL') in
   * serving_restrictions will also exist. Note that this may be nuanced with
   * other contextual restrictions, in which case, it may be preferable to read
   * from serving_restrictions directly. Can be used to filter the response of
   * the creatives.list method.
   *
   * @var string
   */
  public $openAuctionStatus;
  /**
   * All restricted categories for the ads that may be shown from this creative.
   *
   * @var string[]
   */
  public $restrictedCategories;
  protected $servingRestrictionsType = ServingRestriction::class;
  protected $servingRestrictionsDataType = 'array';
  /**
   * All vendor IDs for the ads that may be shown from this creative. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/vendors.txt for
   * possible values.
   *
   * @var int[]
   */
  public $vendorIds;
  /**
   * Output only. The version of this creative.
   *
   * @var int
   */
  public $version;
  protected $videoType = VideoContent::class;
  protected $videoDataType = '';

  /**
   * The account that this creative belongs to. Can be used to filter the
   * response of the creatives.list method.
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
   * The link to AdChoices destination page.
   *
   * @param string $adChoicesDestinationUrl
   */
  public function setAdChoicesDestinationUrl($adChoicesDestinationUrl)
  {
    $this->adChoicesDestinationUrl = $adChoicesDestinationUrl;
  }
  /**
   * @return string
   */
  public function getAdChoicesDestinationUrl()
  {
    return $this->adChoicesDestinationUrl;
  }
  /**
   * Output only. The detected ad technology providers.
   *
   * @param AdTechnologyProviders $adTechnologyProviders
   */
  public function setAdTechnologyProviders(AdTechnologyProviders $adTechnologyProviders)
  {
    $this->adTechnologyProviders = $adTechnologyProviders;
  }
  /**
   * @return AdTechnologyProviders
   */
  public function getAdTechnologyProviders()
  {
    return $this->adTechnologyProviders;
  }
  /**
   * The name of the company being advertised in the creative.
   *
   * @param string $advertiserName
   */
  public function setAdvertiserName($advertiserName)
  {
    $this->advertiserName = $advertiserName;
  }
  /**
   * @return string
   */
  public function getAdvertiserName()
  {
    return $this->advertiserName;
  }
  /**
   * The agency ID for this creative.
   *
   * @param string $agencyId
   */
  public function setAgencyId($agencyId)
  {
    $this->agencyId = $agencyId;
  }
  /**
   * @return string
   */
  public function getAgencyId()
  {
    return $this->agencyId;
  }
  /**
   * Output only. The last update timestamp of the creative through the API.
   *
   * @param string $apiUpdateTime
   */
  public function setApiUpdateTime($apiUpdateTime)
  {
    $this->apiUpdateTime = $apiUpdateTime;
  }
  /**
   * @return string
   */
  public function getApiUpdateTime()
  {
    return $this->apiUpdateTime;
  }
  /**
   * All attributes for the ads that may be shown from this creative. Can be
   * used to filter the response of the creatives.list method.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * The set of destination URLs for the creative.
   *
   * @param string[] $clickThroughUrls
   */
  public function setClickThroughUrls($clickThroughUrls)
  {
    $this->clickThroughUrls = $clickThroughUrls;
  }
  /**
   * @return string[]
   */
  public function getClickThroughUrls()
  {
    return $this->clickThroughUrls;
  }
  /**
   * Output only. Shows any corrections that were applied to this creative.
   *
   * @deprecated
   * @param Correction[] $corrections
   */
  public function setCorrections($corrections)
  {
    $this->corrections = $corrections;
  }
  /**
   * @deprecated
   * @return Correction[]
   */
  public function getCorrections()
  {
    return $this->corrections;
  }
  /**
   * The buyer-defined creative ID of this creative. Can be used to filter the
   * response of the creatives.list method.
   *
   * @param string $creativeId
   */
  public function setCreativeId($creativeId)
  {
    $this->creativeId = $creativeId;
  }
  /**
   * @return string
   */
  public function getCreativeId()
  {
    return $this->creativeId;
  }
  /**
   * Output only. The top-level deals status of this creative. If disapproved,
   * an entry for 'auctionType=DIRECT_DEALS' (or 'ALL') in serving_restrictions
   * will also exist. Note that this may be nuanced with other contextual
   * restrictions, in which case, it may be preferable to read from
   * serving_restrictions directly. Can be used to filter the response of the
   * creatives.list method.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_CHECKED, CONDITIONALLY_APPROVED,
   * APPROVED, DISAPPROVED, PENDING_REVIEW, STATUS_TYPE_UNSPECIFIED
   *
   * @param self::DEALS_STATUS_* $dealsStatus
   */
  public function setDealsStatus($dealsStatus)
  {
    $this->dealsStatus = $dealsStatus;
  }
  /**
   * @return self::DEALS_STATUS_*
   */
  public function getDealsStatus()
  {
    return $this->dealsStatus;
  }
  /**
   * The set of declared destination URLs for the creative.
   *
   * @param string[] $declaredClickThroughUrls
   */
  public function setDeclaredClickThroughUrls($declaredClickThroughUrls)
  {
    $this->declaredClickThroughUrls = $declaredClickThroughUrls;
  }
  /**
   * @return string[]
   */
  public function getDeclaredClickThroughUrls()
  {
    return $this->declaredClickThroughUrls;
  }
  /**
   * Output only. Detected advertiser IDs, if any.
   *
   * @param string[] $detectedAdvertiserIds
   */
  public function setDetectedAdvertiserIds($detectedAdvertiserIds)
  {
    $this->detectedAdvertiserIds = $detectedAdvertiserIds;
  }
  /**
   * @return string[]
   */
  public function getDetectedAdvertiserIds()
  {
    return $this->detectedAdvertiserIds;
  }
  /**
   * Output only. The detected domains for this creative.
   *
   * @param string[] $detectedDomains
   */
  public function setDetectedDomains($detectedDomains)
  {
    $this->detectedDomains = $detectedDomains;
  }
  /**
   * @return string[]
   */
  public function getDetectedDomains()
  {
    return $this->detectedDomains;
  }
  /**
   * Output only. The detected languages for this creative. The order is
   * arbitrary. The codes are 2 or 5 characters and are documented at
   * https://developers.google.com/adwords/api/docs/appendix/languagecodes.
   *
   * @param string[] $detectedLanguages
   */
  public function setDetectedLanguages($detectedLanguages)
  {
    $this->detectedLanguages = $detectedLanguages;
  }
  /**
   * @return string[]
   */
  public function getDetectedLanguages()
  {
    return $this->detectedLanguages;
  }
  /**
   * Output only. Detected product categories, if any. See the ad-product-
   * categories.txt file in the technical documentation for a list of IDs.
   *
   * @param int[] $detectedProductCategories
   */
  public function setDetectedProductCategories($detectedProductCategories)
  {
    $this->detectedProductCategories = $detectedProductCategories;
  }
  /**
   * @return int[]
   */
  public function getDetectedProductCategories()
  {
    return $this->detectedProductCategories;
  }
  /**
   * Output only. Detected sensitive categories, if any. See the ad-sensitive-
   * categories.txt file in the technical documentation for a list of IDs. You
   * should use these IDs along with the excluded-sensitive-category field in
   * the bid request to filter your bids.
   *
   * @param int[] $detectedSensitiveCategories
   */
  public function setDetectedSensitiveCategories($detectedSensitiveCategories)
  {
    $this->detectedSensitiveCategories = $detectedSensitiveCategories;
  }
  /**
   * @return int[]
   */
  public function getDetectedSensitiveCategories()
  {
    return $this->detectedSensitiveCategories;
  }
  /**
   * An HTML creative.
   *
   * @param HtmlContent $html
   */
  public function setHtml(HtmlContent $html)
  {
    $this->html = $html;
  }
  /**
   * @return HtmlContent
   */
  public function getHtml()
  {
    return $this->html;
  }
  /**
   * The set of URLs to be called to record an impression.
   *
   * @param string[] $impressionTrackingUrls
   */
  public function setImpressionTrackingUrls($impressionTrackingUrls)
  {
    $this->impressionTrackingUrls = $impressionTrackingUrls;
  }
  /**
   * @return string[]
   */
  public function getImpressionTrackingUrls()
  {
    return $this->impressionTrackingUrls;
  }
  /**
   * A native creative.
   *
   * @param NativeContent $native
   */
  public function setNative(NativeContent $native)
  {
    $this->native = $native;
  }
  /**
   * @return NativeContent
   */
  public function getNative()
  {
    return $this->native;
  }
  /**
   * Output only. The top-level open auction status of this creative. If
   * disapproved, an entry for 'auctionType = OPEN_AUCTION' (or 'ALL') in
   * serving_restrictions will also exist. Note that this may be nuanced with
   * other contextual restrictions, in which case, it may be preferable to read
   * from serving_restrictions directly. Can be used to filter the response of
   * the creatives.list method.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_CHECKED, CONDITIONALLY_APPROVED,
   * APPROVED, DISAPPROVED, PENDING_REVIEW, STATUS_TYPE_UNSPECIFIED
   *
   * @param self::OPEN_AUCTION_STATUS_* $openAuctionStatus
   */
  public function setOpenAuctionStatus($openAuctionStatus)
  {
    $this->openAuctionStatus = $openAuctionStatus;
  }
  /**
   * @return self::OPEN_AUCTION_STATUS_*
   */
  public function getOpenAuctionStatus()
  {
    return $this->openAuctionStatus;
  }
  /**
   * All restricted categories for the ads that may be shown from this creative.
   *
   * @param string[] $restrictedCategories
   */
  public function setRestrictedCategories($restrictedCategories)
  {
    $this->restrictedCategories = $restrictedCategories;
  }
  /**
   * @return string[]
   */
  public function getRestrictedCategories()
  {
    return $this->restrictedCategories;
  }
  /**
   * Output only. The granular status of this ad in specific contexts. A context
   * here relates to where something ultimately serves (for example, a physical
   * location, a platform, an HTTPS versus HTTP request, or the type of
   * auction).
   *
   * @param ServingRestriction[] $servingRestrictions
   */
  public function setServingRestrictions($servingRestrictions)
  {
    $this->servingRestrictions = $servingRestrictions;
  }
  /**
   * @return ServingRestriction[]
   */
  public function getServingRestrictions()
  {
    return $this->servingRestrictions;
  }
  /**
   * All vendor IDs for the ads that may be shown from this creative. See
   * https://storage.googleapis.com/adx-rtb-dictionaries/vendors.txt for
   * possible values.
   *
   * @param int[] $vendorIds
   */
  public function setVendorIds($vendorIds)
  {
    $this->vendorIds = $vendorIds;
  }
  /**
   * @return int[]
   */
  public function getVendorIds()
  {
    return $this->vendorIds;
  }
  /**
   * Output only. The version of this creative.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * A video creative.
   *
   * @param VideoContent $video
   */
  public function setVideo(VideoContent $video)
  {
    $this->video = $video;
  }
  /**
   * @return VideoContent
   */
  public function getVideo()
  {
    return $this->video;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Creative::class, 'Google_Service_AdExchangeBuyerII_Creative');
