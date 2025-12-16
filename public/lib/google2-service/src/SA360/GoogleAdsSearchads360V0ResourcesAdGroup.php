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

class GoogleAdsSearchads360V0ResourcesAdGroup extends \Google\Collection
{
  /**
   * The ad rotation mode has not been specified.
   */
  public const AD_ROTATION_MODE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const AD_ROTATION_MODE_UNKNOWN = 'UNKNOWN';
  /**
   * Optimize ad group ads based on clicks or conversions.
   */
  public const AD_ROTATION_MODE_OPTIMIZE = 'OPTIMIZE';
  /**
   * Rotate evenly forever.
   */
  public const AD_ROTATION_MODE_ROTATE_FOREVER = 'ROTATE_FOREVER';
  /**
   * Not specified.
   */
  public const ENGINE_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ENGINE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Deprecated. Do not use.
   *
   * @deprecated
   */
  public const ENGINE_STATUS_AD_GROUP_ELIGIBLE = 'AD_GROUP_ELIGIBLE';
  /**
   * No ads are running for this ad group, because the ad group's end date has
   * passed.
   */
  public const ENGINE_STATUS_AD_GROUP_EXPIRED = 'AD_GROUP_EXPIRED';
  /**
   * The ad group has been deleted.
   */
  public const ENGINE_STATUS_AD_GROUP_REMOVED = 'AD_GROUP_REMOVED';
  /**
   * No ads are running for this ad group because the associated ad group is
   * still in draft form.
   */
  public const ENGINE_STATUS_AD_GROUP_DRAFT = 'AD_GROUP_DRAFT';
  /**
   * The ad group has been paused.
   */
  public const ENGINE_STATUS_AD_GROUP_PAUSED = 'AD_GROUP_PAUSED';
  /**
   * The ad group is active and currently serving ads.
   */
  public const ENGINE_STATUS_AD_GROUP_SERVING = 'AD_GROUP_SERVING';
  /**
   * The ad group has been submitted (Microsoft Bing Ads legacy status).
   */
  public const ENGINE_STATUS_AD_GROUP_SUBMITTED = 'AD_GROUP_SUBMITTED';
  /**
   * No ads are running for this ad group, because the campaign has been paused.
   */
  public const ENGINE_STATUS_CAMPAIGN_PAUSED = 'CAMPAIGN_PAUSED';
  /**
   * No ads are running for this ad group, because the account has been paused.
   */
  public const ENGINE_STATUS_ACCOUNT_PAUSED = 'ACCOUNT_PAUSED';
  /**
   * The status has not been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The ad group is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The ad group is paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * The ad group is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * The type has not been specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The default ad group type for Search campaigns.
   */
  public const TYPE_SEARCH_STANDARD = 'SEARCH_STANDARD';
  /**
   * The default ad group type for Display campaigns.
   */
  public const TYPE_DISPLAY_STANDARD = 'DISPLAY_STANDARD';
  /**
   * The ad group type for Shopping campaigns serving standard product ads.
   */
  public const TYPE_SHOPPING_PRODUCT_ADS = 'SHOPPING_PRODUCT_ADS';
  /**
   * The type for ad groups that are limited to serving Showcase or Merchant ads
   * in Shopping results.
   */
  public const TYPE_SHOPPING_SHOWCASE_ADS = 'SHOPPING_SHOWCASE_ADS';
  /**
   * The default ad group type for Hotel campaigns.
   */
  public const TYPE_HOTEL_ADS = 'HOTEL_ADS';
  /**
   * The type for ad groups in Smart Shopping campaigns.
   */
  public const TYPE_SHOPPING_SMART_ADS = 'SHOPPING_SMART_ADS';
  /**
   * Short unskippable in-stream video ads.
   */
  public const TYPE_VIDEO_BUMPER = 'VIDEO_BUMPER';
  /**
   * TrueView (skippable) in-stream video ads.
   */
  public const TYPE_VIDEO_TRUE_VIEW_IN_STREAM = 'VIDEO_TRUE_VIEW_IN_STREAM';
  /**
   * TrueView in-display video ads.
   */
  public const TYPE_VIDEO_TRUE_VIEW_IN_DISPLAY = 'VIDEO_TRUE_VIEW_IN_DISPLAY';
  /**
   * Unskippable in-stream video ads.
   */
  public const TYPE_VIDEO_NON_SKIPPABLE_IN_STREAM = 'VIDEO_NON_SKIPPABLE_IN_STREAM';
  /**
   * Outstream video ads.
   */
  public const TYPE_VIDEO_OUTSTREAM = 'VIDEO_OUTSTREAM';
  /**
   * Ad group type for Dynamic Search Ads ad groups.
   */
  public const TYPE_SEARCH_DYNAMIC_ADS = 'SEARCH_DYNAMIC_ADS';
  /**
   * The type for ad groups in Shopping Comparison Listing campaigns.
   */
  public const TYPE_SHOPPING_COMPARISON_LISTING_ADS = 'SHOPPING_COMPARISON_LISTING_ADS';
  /**
   * The ad group type for Promoted Hotel ad groups.
   */
  public const TYPE_PROMOTED_HOTEL_ADS = 'PROMOTED_HOTEL_ADS';
  /**
   * Video responsive ad groups.
   */
  public const TYPE_VIDEO_RESPONSIVE = 'VIDEO_RESPONSIVE';
  /**
   * Video efficient reach ad groups.
   */
  public const TYPE_VIDEO_EFFICIENT_REACH = 'VIDEO_EFFICIENT_REACH';
  /**
   * Ad group type for Smart campaigns.
   */
  public const TYPE_SMART_CAMPAIGN_ADS = 'SMART_CAMPAIGN_ADS';
  /**
   * Ad group type for Travel campaigns.
   */
  public const TYPE_TRAVEL_ADS = 'TRAVEL_ADS';
  protected $collection_key = 'labels';
  /**
   * The ad rotation mode of the ad group.
   *
   * @var string
   */
  public $adRotationMode;
  /**
   * The maximum CPC (cost-per-click) bid.
   *
   * @var string
   */
  public $cpcBidMicros;
  /**
   * Output only. The timestamp when this ad_group was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. The resource names of effective labels attached to this ad
   * group. An effective label is a label inherited or directly assigned to this
   * ad group.
   *
   * @var string[]
   */
  public $effectiveLabels;
  /**
   * Output only. Date when the ad group ends serving ads. By default, the ad
   * group ends on the ad group's end date. If this field is set, then the ad
   * group ends at the end of the specified date in the customer's time zone.
   * This field is only available for Microsoft Advertising and Facebook gateway
   * accounts. Format: YYYY-MM-DD Example: 2019-03-14
   *
   * @var string
   */
  public $endDate;
  /**
   * Output only. ID of the ad group in the external engine account. This field
   * is for non-Google Ads account only, for example, Yahoo Japan, Microsoft,
   * Baidu etc. For Google Ads entity, use "ad_group.id" instead.
   *
   * @var string
   */
  public $engineId;
  /**
   * Output only. The Engine Status for ad group.
   *
   * @var string
   */
  public $engineStatus;
  /**
   * URL template for appending params to Final URL.
   *
   * @var string
   */
  public $finalUrlSuffix;
  /**
   * Output only. The ID of the ad group.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The resource names of labels attached to this ad group.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The language of the ads and keywords in an ad group. This
   * field is only available for Microsoft Advertising accounts. More details:
   * https://docs.microsoft.com/en-us/advertising/guides/ad-
   * languages?view=bingads-13#adlanguage
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. The datetime when this ad group was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The name of the ad group. This field is required and should not be empty
   * when creating new ad groups. It must contain fewer than 255 UTF-8 full-
   * width characters. It must not contain any null (code point 0x0), NL line
   * feed (code point 0xA) or carriage return (code point 0xD) characters.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The resource name of the ad group. Ad group resource names have
   * the form: `customers/{customer_id}/adGroups/{ad_group_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Date when this ad group starts serving ads. By default, the ad
   * group starts now or the ad group's start date, whichever is later. If this
   * field is set, then the ad group starts at the beginning of the specified
   * date in the customer's time zone. This field is only available for
   * Microsoft Advertising and Facebook gateway accounts. Format: YYYY-MM-DD
   * Example: 2019-03-14
   *
   * @var string
   */
  public $startDate;
  /**
   * The status of the ad group.
   *
   * @var string
   */
  public $status;
  protected $targetingSettingType = GoogleAdsSearchads360V0CommonTargetingSetting::class;
  protected $targetingSettingDataType = '';
  /**
   * The URL template for constructing a tracking URL.
   *
   * @var string
   */
  public $trackingUrlTemplate;
  /**
   * Immutable. The type of the ad group.
   *
   * @var string
   */
  public $type;

  /**
   * The ad rotation mode of the ad group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, OPTIMIZE, ROTATE_FOREVER
   *
   * @param self::AD_ROTATION_MODE_* $adRotationMode
   */
  public function setAdRotationMode($adRotationMode)
  {
    $this->adRotationMode = $adRotationMode;
  }
  /**
   * @return self::AD_ROTATION_MODE_*
   */
  public function getAdRotationMode()
  {
    return $this->adRotationMode;
  }
  /**
   * The maximum CPC (cost-per-click) bid.
   *
   * @param string $cpcBidMicros
   */
  public function setCpcBidMicros($cpcBidMicros)
  {
    $this->cpcBidMicros = $cpcBidMicros;
  }
  /**
   * @return string
   */
  public function getCpcBidMicros()
  {
    return $this->cpcBidMicros;
  }
  /**
   * Output only. The timestamp when this ad_group was created. The timestamp is
   * in the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. The resource names of effective labels attached to this ad
   * group. An effective label is a label inherited or directly assigned to this
   * ad group.
   *
   * @param string[] $effectiveLabels
   */
  public function setEffectiveLabels($effectiveLabels)
  {
    $this->effectiveLabels = $effectiveLabels;
  }
  /**
   * @return string[]
   */
  public function getEffectiveLabels()
  {
    return $this->effectiveLabels;
  }
  /**
   * Output only. Date when the ad group ends serving ads. By default, the ad
   * group ends on the ad group's end date. If this field is set, then the ad
   * group ends at the end of the specified date in the customer's time zone.
   * This field is only available for Microsoft Advertising and Facebook gateway
   * accounts. Format: YYYY-MM-DD Example: 2019-03-14
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Output only. ID of the ad group in the external engine account. This field
   * is for non-Google Ads account only, for example, Yahoo Japan, Microsoft,
   * Baidu etc. For Google Ads entity, use "ad_group.id" instead.
   *
   * @param string $engineId
   */
  public function setEngineId($engineId)
  {
    $this->engineId = $engineId;
  }
  /**
   * @return string
   */
  public function getEngineId()
  {
    return $this->engineId;
  }
  /**
   * Output only. The Engine Status for ad group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, AD_GROUP_ELIGIBLE, AD_GROUP_EXPIRED,
   * AD_GROUP_REMOVED, AD_GROUP_DRAFT, AD_GROUP_PAUSED, AD_GROUP_SERVING,
   * AD_GROUP_SUBMITTED, CAMPAIGN_PAUSED, ACCOUNT_PAUSED
   *
   * @param self::ENGINE_STATUS_* $engineStatus
   */
  public function setEngineStatus($engineStatus)
  {
    $this->engineStatus = $engineStatus;
  }
  /**
   * @return self::ENGINE_STATUS_*
   */
  public function getEngineStatus()
  {
    return $this->engineStatus;
  }
  /**
   * URL template for appending params to Final URL.
   *
   * @param string $finalUrlSuffix
   */
  public function setFinalUrlSuffix($finalUrlSuffix)
  {
    $this->finalUrlSuffix = $finalUrlSuffix;
  }
  /**
   * @return string
   */
  public function getFinalUrlSuffix()
  {
    return $this->finalUrlSuffix;
  }
  /**
   * Output only. The ID of the ad group.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. The resource names of labels attached to this ad group.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The language of the ads and keywords in an ad group. This
   * field is only available for Microsoft Advertising accounts. More details:
   * https://docs.microsoft.com/en-us/advertising/guides/ad-
   * languages?view=bingads-13#adlanguage
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Output only. The datetime when this ad group was last modified. The
   * datetime is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss"
   * format.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * The name of the ad group. This field is required and should not be empty
   * when creating new ad groups. It must contain fewer than 255 UTF-8 full-
   * width characters. It must not contain any null (code point 0x0), NL line
   * feed (code point 0xA) or carriage return (code point 0xD) characters.
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
   * Immutable. The resource name of the ad group. Ad group resource names have
   * the form: `customers/{customer_id}/adGroups/{ad_group_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Date when this ad group starts serving ads. By default, the ad
   * group starts now or the ad group's start date, whichever is later. If this
   * field is set, then the ad group starts at the beginning of the specified
   * date in the customer's time zone. This field is only available for
   * Microsoft Advertising and Facebook gateway accounts. Format: YYYY-MM-DD
   * Example: 2019-03-14
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * The status of the ad group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, PAUSED, REMOVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Setting for targeting related features.
   *
   * @param GoogleAdsSearchads360V0CommonTargetingSetting $targetingSetting
   */
  public function setTargetingSetting(GoogleAdsSearchads360V0CommonTargetingSetting $targetingSetting)
  {
    $this->targetingSetting = $targetingSetting;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTargetingSetting
   */
  public function getTargetingSetting()
  {
    return $this->targetingSetting;
  }
  /**
   * The URL template for constructing a tracking URL.
   *
   * @param string $trackingUrlTemplate
   */
  public function setTrackingUrlTemplate($trackingUrlTemplate)
  {
    $this->trackingUrlTemplate = $trackingUrlTemplate;
  }
  /**
   * @return string
   */
  public function getTrackingUrlTemplate()
  {
    return $this->trackingUrlTemplate;
  }
  /**
   * Immutable. The type of the ad group.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SEARCH_STANDARD, DISPLAY_STANDARD,
   * SHOPPING_PRODUCT_ADS, SHOPPING_SHOWCASE_ADS, HOTEL_ADS, SHOPPING_SMART_ADS,
   * VIDEO_BUMPER, VIDEO_TRUE_VIEW_IN_STREAM, VIDEO_TRUE_VIEW_IN_DISPLAY,
   * VIDEO_NON_SKIPPABLE_IN_STREAM, VIDEO_OUTSTREAM, SEARCH_DYNAMIC_ADS,
   * SHOPPING_COMPARISON_LISTING_ADS, PROMOTED_HOTEL_ADS, VIDEO_RESPONSIVE,
   * VIDEO_EFFICIENT_REACH, SMART_CAMPAIGN_ADS, TRAVEL_ADS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAdGroup::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroup');
