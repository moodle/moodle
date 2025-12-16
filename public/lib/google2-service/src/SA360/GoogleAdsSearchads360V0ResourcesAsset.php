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

class GoogleAdsSearchads360V0ResourcesAsset extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const ENGINE_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ENGINE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The asset is active.
   */
  public const ENGINE_STATUS_SERVING = 'SERVING';
  /**
   * The asset is active limited.
   */
  public const ENGINE_STATUS_SERVING_LIMITED = 'SERVING_LIMITED';
  /**
   * The asset is disapproved (not eligible).
   */
  public const ENGINE_STATUS_DISAPPROVED = 'DISAPPROVED';
  /**
   * The asset is inactive (pending).
   */
  public const ENGINE_STATUS_DISABLED = 'DISABLED';
  /**
   * The asset has been removed.
   */
  public const ENGINE_STATUS_REMOVED = 'REMOVED';
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
   * The asset is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The asset is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * The asset is archived.
   */
  public const STATUS_ARCHIVED = 'ARCHIVED';
  /**
   * The asset is system generated pending user review.
   */
  public const STATUS_PENDING_SYSTEM_GENERATED = 'PENDING_SYSTEM_GENERATED';
  /**
   * Not specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * YouTube video asset.
   */
  public const TYPE_YOUTUBE_VIDEO = 'YOUTUBE_VIDEO';
  /**
   * Media bundle asset.
   */
  public const TYPE_MEDIA_BUNDLE = 'MEDIA_BUNDLE';
  /**
   * Image asset.
   */
  public const TYPE_IMAGE = 'IMAGE';
  /**
   * Text asset.
   */
  public const TYPE_TEXT = 'TEXT';
  /**
   * Lead form asset.
   */
  public const TYPE_LEAD_FORM = 'LEAD_FORM';
  /**
   * Book on Google asset.
   */
  public const TYPE_BOOK_ON_GOOGLE = 'BOOK_ON_GOOGLE';
  /**
   * Promotion asset.
   */
  public const TYPE_PROMOTION = 'PROMOTION';
  /**
   * Callout asset.
   */
  public const TYPE_CALLOUT = 'CALLOUT';
  /**
   * Structured Snippet asset.
   */
  public const TYPE_STRUCTURED_SNIPPET = 'STRUCTURED_SNIPPET';
  /**
   * Sitelink asset.
   */
  public const TYPE_SITELINK = 'SITELINK';
  /**
   * Page Feed asset.
   */
  public const TYPE_PAGE_FEED = 'PAGE_FEED';
  /**
   * Dynamic Education asset.
   */
  public const TYPE_DYNAMIC_EDUCATION = 'DYNAMIC_EDUCATION';
  /**
   * Mobile app asset.
   */
  public const TYPE_MOBILE_APP = 'MOBILE_APP';
  /**
   * Hotel callout asset.
   */
  public const TYPE_HOTEL_CALLOUT = 'HOTEL_CALLOUT';
  /**
   * Call asset.
   */
  public const TYPE_CALL = 'CALL';
  /**
   * Price asset.
   */
  public const TYPE_PRICE = 'PRICE';
  /**
   * Call to action asset.
   */
  public const TYPE_CALL_TO_ACTION = 'CALL_TO_ACTION';
  /**
   * Dynamic real estate asset.
   */
  public const TYPE_DYNAMIC_REAL_ESTATE = 'DYNAMIC_REAL_ESTATE';
  /**
   * Dynamic custom asset.
   */
  public const TYPE_DYNAMIC_CUSTOM = 'DYNAMIC_CUSTOM';
  /**
   * Dynamic hotels and rentals asset.
   */
  public const TYPE_DYNAMIC_HOTELS_AND_RENTALS = 'DYNAMIC_HOTELS_AND_RENTALS';
  /**
   * Dynamic flights asset.
   */
  public const TYPE_DYNAMIC_FLIGHTS = 'DYNAMIC_FLIGHTS';
  /**
   * Discovery Carousel Card asset.
   */
  public const TYPE_DISCOVERY_CAROUSEL_CARD = 'DISCOVERY_CAROUSEL_CARD';
  /**
   * Dynamic travel asset.
   */
  public const TYPE_DYNAMIC_TRAVEL = 'DYNAMIC_TRAVEL';
  /**
   * Dynamic local asset.
   */
  public const TYPE_DYNAMIC_LOCAL = 'DYNAMIC_LOCAL';
  /**
   * Dynamic jobs asset.
   */
  public const TYPE_DYNAMIC_JOBS = 'DYNAMIC_JOBS';
  /**
   * Location asset.
   */
  public const TYPE_LOCATION = 'LOCATION';
  /**
   * Hotel property asset.
   */
  public const TYPE_HOTEL_PROPERTY = 'HOTEL_PROPERTY';
  protected $collection_key = 'urlCustomParameters';
  protected $callAssetType = GoogleAdsSearchads360V0CommonUnifiedCallAsset::class;
  protected $callAssetDataType = '';
  protected $callToActionAssetType = GoogleAdsSearchads360V0CommonCallToActionAsset::class;
  protected $callToActionAssetDataType = '';
  protected $calloutAssetType = GoogleAdsSearchads360V0CommonUnifiedCalloutAsset::class;
  protected $calloutAssetDataType = '';
  /**
   * Output only. The timestamp when this asset was created. The timestamp is in
   * the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. The Engine Status for an asset.
   *
   * @var string
   */
  public $engineStatus;
  /**
   * A list of possible final mobile URLs after all cross domain redirects.
   *
   * @var string[]
   */
  public $finalMobileUrls;
  /**
   * URL template for appending params to landing page URLs served with parallel
   * tracking.
   *
   * @var string
   */
  public $finalUrlSuffix;
  /**
   * A list of possible final URLs after all cross domain redirects.
   *
   * @var string[]
   */
  public $finalUrls;
  /**
   * Output only. The ID of the asset.
   *
   * @var string
   */
  public $id;
  protected $imageAssetType = GoogleAdsSearchads360V0CommonImageAsset::class;
  protected $imageAssetDataType = '';
  /**
   * Output only. The datetime when this asset was last modified. The datetime
   * is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss" format.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $locationAssetType = GoogleAdsSearchads360V0CommonUnifiedLocationAsset::class;
  protected $locationAssetDataType = '';
  protected $mobileAppAssetType = GoogleAdsSearchads360V0CommonMobileAppAsset::class;
  protected $mobileAppAssetDataType = '';
  /**
   * Optional name of the asset.
   *
   * @var string
   */
  public $name;
  protected $pageFeedAssetType = GoogleAdsSearchads360V0CommonUnifiedPageFeedAsset::class;
  protected $pageFeedAssetDataType = '';
  /**
   * Immutable. The resource name of the asset. Asset resource names have the
   * form: `customers/{customer_id}/assets/{asset_id}`
   *
   * @var string
   */
  public $resourceName;
  protected $sitelinkAssetType = GoogleAdsSearchads360V0CommonUnifiedSitelinkAsset::class;
  protected $sitelinkAssetDataType = '';
  /**
   * Output only. The status of the asset.
   *
   * @var string
   */
  public $status;
  protected $textAssetType = GoogleAdsSearchads360V0CommonTextAsset::class;
  protected $textAssetDataType = '';
  /**
   * URL template for constructing a tracking URL.
   *
   * @var string
   */
  public $trackingUrlTemplate;
  /**
   * Output only. Type of the asset.
   *
   * @var string
   */
  public $type;
  protected $urlCustomParametersType = GoogleAdsSearchads360V0CommonCustomParameter::class;
  protected $urlCustomParametersDataType = 'array';
  protected $youtubeVideoAssetType = GoogleAdsSearchads360V0CommonYoutubeVideoAsset::class;
  protected $youtubeVideoAssetDataType = '';

  /**
   * Output only. A unified call asset.
   *
   * @param GoogleAdsSearchads360V0CommonUnifiedCallAsset $callAsset
   */
  public function setCallAsset(GoogleAdsSearchads360V0CommonUnifiedCallAsset $callAsset)
  {
    $this->callAsset = $callAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUnifiedCallAsset
   */
  public function getCallAsset()
  {
    return $this->callAsset;
  }
  /**
   * Immutable. A call to action asset.
   *
   * @param GoogleAdsSearchads360V0CommonCallToActionAsset $callToActionAsset
   */
  public function setCallToActionAsset(GoogleAdsSearchads360V0CommonCallToActionAsset $callToActionAsset)
  {
    $this->callToActionAsset = $callToActionAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonCallToActionAsset
   */
  public function getCallToActionAsset()
  {
    return $this->callToActionAsset;
  }
  /**
   * Output only. A unified callout asset.
   *
   * @param GoogleAdsSearchads360V0CommonUnifiedCalloutAsset $calloutAsset
   */
  public function setCalloutAsset(GoogleAdsSearchads360V0CommonUnifiedCalloutAsset $calloutAsset)
  {
    $this->calloutAsset = $calloutAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUnifiedCalloutAsset
   */
  public function getCalloutAsset()
  {
    return $this->calloutAsset;
  }
  /**
   * Output only. The timestamp when this asset was created. The timestamp is in
   * the customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
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
   * Output only. The Engine Status for an asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, SERVING, SERVING_LIMITED,
   * DISAPPROVED, DISABLED, REMOVED
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
   * A list of possible final mobile URLs after all cross domain redirects.
   *
   * @param string[] $finalMobileUrls
   */
  public function setFinalMobileUrls($finalMobileUrls)
  {
    $this->finalMobileUrls = $finalMobileUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalMobileUrls()
  {
    return $this->finalMobileUrls;
  }
  /**
   * URL template for appending params to landing page URLs served with parallel
   * tracking.
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
   * A list of possible final URLs after all cross domain redirects.
   *
   * @param string[] $finalUrls
   */
  public function setFinalUrls($finalUrls)
  {
    $this->finalUrls = $finalUrls;
  }
  /**
   * @return string[]
   */
  public function getFinalUrls()
  {
    return $this->finalUrls;
  }
  /**
   * Output only. The ID of the asset.
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
   * Output only. An image asset.
   *
   * @param GoogleAdsSearchads360V0CommonImageAsset $imageAsset
   */
  public function setImageAsset(GoogleAdsSearchads360V0CommonImageAsset $imageAsset)
  {
    $this->imageAsset = $imageAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonImageAsset
   */
  public function getImageAsset()
  {
    return $this->imageAsset;
  }
  /**
   * Output only. The datetime when this asset was last modified. The datetime
   * is in the customer's time zone and in "yyyy-MM-dd HH:mm:ss.ssssss" format.
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
   * Output only. A unified location asset.
   *
   * @param GoogleAdsSearchads360V0CommonUnifiedLocationAsset $locationAsset
   */
  public function setLocationAsset(GoogleAdsSearchads360V0CommonUnifiedLocationAsset $locationAsset)
  {
    $this->locationAsset = $locationAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUnifiedLocationAsset
   */
  public function getLocationAsset()
  {
    return $this->locationAsset;
  }
  /**
   * A mobile app asset.
   *
   * @param GoogleAdsSearchads360V0CommonMobileAppAsset $mobileAppAsset
   */
  public function setMobileAppAsset(GoogleAdsSearchads360V0CommonMobileAppAsset $mobileAppAsset)
  {
    $this->mobileAppAsset = $mobileAppAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonMobileAppAsset
   */
  public function getMobileAppAsset()
  {
    return $this->mobileAppAsset;
  }
  /**
   * Optional name of the asset.
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
   * Output only. A unified page feed asset.
   *
   * @param GoogleAdsSearchads360V0CommonUnifiedPageFeedAsset $pageFeedAsset
   */
  public function setPageFeedAsset(GoogleAdsSearchads360V0CommonUnifiedPageFeedAsset $pageFeedAsset)
  {
    $this->pageFeedAsset = $pageFeedAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUnifiedPageFeedAsset
   */
  public function getPageFeedAsset()
  {
    return $this->pageFeedAsset;
  }
  /**
   * Immutable. The resource name of the asset. Asset resource names have the
   * form: `customers/{customer_id}/assets/{asset_id}`
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
   * Output only. A unified sitelink asset.
   *
   * @param GoogleAdsSearchads360V0CommonUnifiedSitelinkAsset $sitelinkAsset
   */
  public function setSitelinkAsset(GoogleAdsSearchads360V0CommonUnifiedSitelinkAsset $sitelinkAsset)
  {
    $this->sitelinkAsset = $sitelinkAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonUnifiedSitelinkAsset
   */
  public function getSitelinkAsset()
  {
    return $this->sitelinkAsset;
  }
  /**
   * Output only. The status of the asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED, ARCHIVED,
   * PENDING_SYSTEM_GENERATED
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
   * Output only. A text asset.
   *
   * @param GoogleAdsSearchads360V0CommonTextAsset $textAsset
   */
  public function setTextAsset(GoogleAdsSearchads360V0CommonTextAsset $textAsset)
  {
    $this->textAsset = $textAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTextAsset
   */
  public function getTextAsset()
  {
    return $this->textAsset;
  }
  /**
   * URL template for constructing a tracking URL.
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
   * Output only. Type of the asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, YOUTUBE_VIDEO, MEDIA_BUNDLE, IMAGE,
   * TEXT, LEAD_FORM, BOOK_ON_GOOGLE, PROMOTION, CALLOUT, STRUCTURED_SNIPPET,
   * SITELINK, PAGE_FEED, DYNAMIC_EDUCATION, MOBILE_APP, HOTEL_CALLOUT, CALL,
   * PRICE, CALL_TO_ACTION, DYNAMIC_REAL_ESTATE, DYNAMIC_CUSTOM,
   * DYNAMIC_HOTELS_AND_RENTALS, DYNAMIC_FLIGHTS, DISCOVERY_CAROUSEL_CARD,
   * DYNAMIC_TRAVEL, DYNAMIC_LOCAL, DYNAMIC_JOBS, LOCATION, HOTEL_PROPERTY
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
  /**
   * A list of mappings to be used for substituting URL custom parameter tags in
   * the tracking_url_template, final_urls, and/or final_mobile_urls.
   *
   * @param GoogleAdsSearchads360V0CommonCustomParameter[] $urlCustomParameters
   */
  public function setUrlCustomParameters($urlCustomParameters)
  {
    $this->urlCustomParameters = $urlCustomParameters;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonCustomParameter[]
   */
  public function getUrlCustomParameters()
  {
    return $this->urlCustomParameters;
  }
  /**
   * Immutable. A YouTube video asset.
   *
   * @param GoogleAdsSearchads360V0CommonYoutubeVideoAsset $youtubeVideoAsset
   */
  public function setYoutubeVideoAsset(GoogleAdsSearchads360V0CommonYoutubeVideoAsset $youtubeVideoAsset)
  {
    $this->youtubeVideoAsset = $youtubeVideoAsset;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonYoutubeVideoAsset
   */
  public function getYoutubeVideoAsset()
  {
    return $this->youtubeVideoAsset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAsset');
