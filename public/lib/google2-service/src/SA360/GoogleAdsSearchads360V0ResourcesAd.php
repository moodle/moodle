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

class GoogleAdsSearchads360V0ResourcesAd extends \Google\Collection
{
  /**
   * No value has been specified.
   */
  public const TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The ad is a text ad.
   */
  public const TYPE_TEXT_AD = 'TEXT_AD';
  /**
   * The ad is an expanded text ad.
   */
  public const TYPE_EXPANDED_TEXT_AD = 'EXPANDED_TEXT_AD';
  /**
   * The ad is a call only ad.
   */
  public const TYPE_CALL_ONLY_AD = 'CALL_ONLY_AD';
  /**
   * The ad is an expanded dynamic search ad.
   */
  public const TYPE_EXPANDED_DYNAMIC_SEARCH_AD = 'EXPANDED_DYNAMIC_SEARCH_AD';
  /**
   * The ad is a hotel ad.
   */
  public const TYPE_HOTEL_AD = 'HOTEL_AD';
  /**
   * The ad is a Smart Shopping ad.
   */
  public const TYPE_SHOPPING_SMART_AD = 'SHOPPING_SMART_AD';
  /**
   * The ad is a standard Shopping ad.
   */
  public const TYPE_SHOPPING_PRODUCT_AD = 'SHOPPING_PRODUCT_AD';
  /**
   * The ad is a video ad.
   */
  public const TYPE_VIDEO_AD = 'VIDEO_AD';
  /**
   * This ad is a Gmail ad.
   */
  public const TYPE_GMAIL_AD = 'GMAIL_AD';
  /**
   * This ad is an Image ad.
   */
  public const TYPE_IMAGE_AD = 'IMAGE_AD';
  /**
   * The ad is a responsive search ad.
   */
  public const TYPE_RESPONSIVE_SEARCH_AD = 'RESPONSIVE_SEARCH_AD';
  /**
   * The ad is a legacy responsive display ad.
   */
  public const TYPE_LEGACY_RESPONSIVE_DISPLAY_AD = 'LEGACY_RESPONSIVE_DISPLAY_AD';
  /**
   * The ad is an app ad.
   */
  public const TYPE_APP_AD = 'APP_AD';
  /**
   * The ad is a legacy app install ad.
   */
  public const TYPE_LEGACY_APP_INSTALL_AD = 'LEGACY_APP_INSTALL_AD';
  /**
   * The ad is a responsive display ad.
   */
  public const TYPE_RESPONSIVE_DISPLAY_AD = 'RESPONSIVE_DISPLAY_AD';
  /**
   * The ad is a local ad.
   */
  public const TYPE_LOCAL_AD = 'LOCAL_AD';
  /**
   * The ad is a display upload ad with the HTML5_UPLOAD_AD product type.
   */
  public const TYPE_HTML5_UPLOAD_AD = 'HTML5_UPLOAD_AD';
  /**
   * The ad is a display upload ad with one of the DYNAMIC_HTML5_* product
   * types.
   */
  public const TYPE_DYNAMIC_HTML5_AD = 'DYNAMIC_HTML5_AD';
  /**
   * The ad is an app engagement ad.
   */
  public const TYPE_APP_ENGAGEMENT_AD = 'APP_ENGAGEMENT_AD';
  /**
   * The ad is a Shopping Comparison Listing ad.
   */
  public const TYPE_SHOPPING_COMPARISON_LISTING_AD = 'SHOPPING_COMPARISON_LISTING_AD';
  /**
   * Video bumper ad.
   */
  public const TYPE_VIDEO_BUMPER_AD = 'VIDEO_BUMPER_AD';
  /**
   * Video non-skippable in-stream ad.
   */
  public const TYPE_VIDEO_NON_SKIPPABLE_IN_STREAM_AD = 'VIDEO_NON_SKIPPABLE_IN_STREAM_AD';
  /**
   * Video outstream ad.
   */
  public const TYPE_VIDEO_OUTSTREAM_AD = 'VIDEO_OUTSTREAM_AD';
  /**
   * Video TrueView in-display ad.
   */
  public const TYPE_VIDEO_TRUEVIEW_DISCOVERY_AD = 'VIDEO_TRUEVIEW_DISCOVERY_AD';
  /**
   * Video TrueView in-stream ad.
   */
  public const TYPE_VIDEO_TRUEVIEW_IN_STREAM_AD = 'VIDEO_TRUEVIEW_IN_STREAM_AD';
  /**
   * Video responsive ad.
   */
  public const TYPE_VIDEO_RESPONSIVE_AD = 'VIDEO_RESPONSIVE_AD';
  /**
   * Smart campaign ad.
   */
  public const TYPE_SMART_CAMPAIGN_AD = 'SMART_CAMPAIGN_AD';
  /**
   * Universal app pre-registration ad.
   */
  public const TYPE_APP_PRE_REGISTRATION_AD = 'APP_PRE_REGISTRATION_AD';
  /**
   * Discovery multi asset ad.
   */
  public const TYPE_DISCOVERY_MULTI_ASSET_AD = 'DISCOVERY_MULTI_ASSET_AD';
  /**
   * Discovery carousel ad.
   */
  public const TYPE_DISCOVERY_CAROUSEL_AD = 'DISCOVERY_CAROUSEL_AD';
  /**
   * Travel ad.
   */
  public const TYPE_TRAVEL_AD = 'TRAVEL_AD';
  /**
   * Discovery video responsive ad.
   */
  public const TYPE_DISCOVERY_VIDEO_RESPONSIVE_AD = 'DISCOVERY_VIDEO_RESPONSIVE_AD';
  /**
   * Multimedia ad.
   */
  public const TYPE_MULTIMEDIA_AD = 'MULTIMEDIA_AD';
  protected $collection_key = 'finalUrls';
  /**
   * The URL that appears in the ad description for some ad formats.
   *
   * @var string
   */
  public $displayUrl;
  protected $expandedDynamicSearchAdType = GoogleAdsSearchads360V0CommonSearchAds360ExpandedDynamicSearchAdInfo::class;
  protected $expandedDynamicSearchAdDataType = '';
  protected $expandedTextAdType = GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo::class;
  protected $expandedTextAdDataType = '';
  protected $finalAppUrlsType = GoogleAdsSearchads360V0CommonFinalAppUrl::class;
  protected $finalAppUrlsDataType = 'array';
  /**
   * The list of possible final mobile URLs after all cross-domain redirects for
   * the ad.
   *
   * @var string[]
   */
  public $finalMobileUrls;
  /**
   * The suffix to use when constructing a final URL.
   *
   * @var string
   */
  public $finalUrlSuffix;
  /**
   * The list of possible final URLs after all cross-domain redirects for the
   * ad.
   *
   * @var string[]
   */
  public $finalUrls;
  /**
   * Output only. The ID of the ad.
   *
   * @var string
   */
  public $id;
  /**
   * Immutable. The name of the ad. This is only used to be able to identify the
   * ad. It does not need to be unique and does not affect the served ad.
   *
   * @var string
   */
  public $name;
  protected $productAdType = GoogleAdsSearchads360V0CommonSearchAds360ProductAdInfo::class;
  protected $productAdDataType = '';
  /**
   * Immutable. The resource name of the ad. Ad resource names have the form:
   * `customers/{customer_id}/ads/{ad_id}`
   *
   * @var string
   */
  public $resourceName;
  protected $responsiveSearchAdType = GoogleAdsSearchads360V0CommonSearchAds360ResponsiveSearchAdInfo::class;
  protected $responsiveSearchAdDataType = '';
  protected $textAdType = GoogleAdsSearchads360V0CommonSearchAds360TextAdInfo::class;
  protected $textAdDataType = '';
  /**
   * The URL template for constructing a tracking URL.
   *
   * @var string
   */
  public $trackingUrlTemplate;
  /**
   * Output only. The type of ad.
   *
   * @var string
   */
  public $type;

  /**
   * The URL that appears in the ad description for some ad formats.
   *
   * @param string $displayUrl
   */
  public function setDisplayUrl($displayUrl)
  {
    $this->displayUrl = $displayUrl;
  }
  /**
   * @return string
   */
  public function getDisplayUrl()
  {
    return $this->displayUrl;
  }
  /**
   * Immutable. Details pertaining to an expanded dynamic search ad.
   *
   * @param GoogleAdsSearchads360V0CommonSearchAds360ExpandedDynamicSearchAdInfo $expandedDynamicSearchAd
   */
  public function setExpandedDynamicSearchAd(GoogleAdsSearchads360V0CommonSearchAds360ExpandedDynamicSearchAdInfo $expandedDynamicSearchAd)
  {
    $this->expandedDynamicSearchAd = $expandedDynamicSearchAd;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonSearchAds360ExpandedDynamicSearchAdInfo
   */
  public function getExpandedDynamicSearchAd()
  {
    return $this->expandedDynamicSearchAd;
  }
  /**
   * Immutable. Details pertaining to an expanded text ad.
   *
   * @param GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo $expandedTextAd
   */
  public function setExpandedTextAd(GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo $expandedTextAd)
  {
    $this->expandedTextAd = $expandedTextAd;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonSearchAds360ExpandedTextAdInfo
   */
  public function getExpandedTextAd()
  {
    return $this->expandedTextAd;
  }
  /**
   * A list of final app URLs that will be used on mobile if the user has the
   * specific app installed.
   *
   * @param GoogleAdsSearchads360V0CommonFinalAppUrl[] $finalAppUrls
   */
  public function setFinalAppUrls($finalAppUrls)
  {
    $this->finalAppUrls = $finalAppUrls;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonFinalAppUrl[]
   */
  public function getFinalAppUrls()
  {
    return $this->finalAppUrls;
  }
  /**
   * The list of possible final mobile URLs after all cross-domain redirects for
   * the ad.
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
   * The suffix to use when constructing a final URL.
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
   * The list of possible final URLs after all cross-domain redirects for the
   * ad.
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
   * Output only. The ID of the ad.
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
   * Immutable. The name of the ad. This is only used to be able to identify the
   * ad. It does not need to be unique and does not affect the served ad.
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
   * Immutable. Details pertaining to a product ad.
   *
   * @param GoogleAdsSearchads360V0CommonSearchAds360ProductAdInfo $productAd
   */
  public function setProductAd(GoogleAdsSearchads360V0CommonSearchAds360ProductAdInfo $productAd)
  {
    $this->productAd = $productAd;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonSearchAds360ProductAdInfo
   */
  public function getProductAd()
  {
    return $this->productAd;
  }
  /**
   * Immutable. The resource name of the ad. Ad resource names have the form:
   * `customers/{customer_id}/ads/{ad_id}`
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
   * Immutable. Details pertaining to a responsive search ad.
   *
   * @param GoogleAdsSearchads360V0CommonSearchAds360ResponsiveSearchAdInfo $responsiveSearchAd
   */
  public function setResponsiveSearchAd(GoogleAdsSearchads360V0CommonSearchAds360ResponsiveSearchAdInfo $responsiveSearchAd)
  {
    $this->responsiveSearchAd = $responsiveSearchAd;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonSearchAds360ResponsiveSearchAdInfo
   */
  public function getResponsiveSearchAd()
  {
    return $this->responsiveSearchAd;
  }
  /**
   * Immutable. Details pertaining to a text ad.
   *
   * @param GoogleAdsSearchads360V0CommonSearchAds360TextAdInfo $textAd
   */
  public function setTextAd(GoogleAdsSearchads360V0CommonSearchAds360TextAdInfo $textAd)
  {
    $this->textAd = $textAd;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonSearchAds360TextAdInfo
   */
  public function getTextAd()
  {
    return $this->textAd;
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
   * Output only. The type of ad.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, TEXT_AD, EXPANDED_TEXT_AD,
   * CALL_ONLY_AD, EXPANDED_DYNAMIC_SEARCH_AD, HOTEL_AD, SHOPPING_SMART_AD,
   * SHOPPING_PRODUCT_AD, VIDEO_AD, GMAIL_AD, IMAGE_AD, RESPONSIVE_SEARCH_AD,
   * LEGACY_RESPONSIVE_DISPLAY_AD, APP_AD, LEGACY_APP_INSTALL_AD,
   * RESPONSIVE_DISPLAY_AD, LOCAL_AD, HTML5_UPLOAD_AD, DYNAMIC_HTML5_AD,
   * APP_ENGAGEMENT_AD, SHOPPING_COMPARISON_LISTING_AD, VIDEO_BUMPER_AD,
   * VIDEO_NON_SKIPPABLE_IN_STREAM_AD, VIDEO_OUTSTREAM_AD,
   * VIDEO_TRUEVIEW_DISCOVERY_AD, VIDEO_TRUEVIEW_IN_STREAM_AD,
   * VIDEO_RESPONSIVE_AD, SMART_CAMPAIGN_AD, APP_PRE_REGISTRATION_AD,
   * DISCOVERY_MULTI_ASSET_AD, DISCOVERY_CAROUSEL_AD, TRAVEL_AD,
   * DISCOVERY_VIDEO_RESPONSIVE_AD, MULTIMEDIA_AD
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
class_alias(GoogleAdsSearchads360V0ResourcesAd::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAd');
