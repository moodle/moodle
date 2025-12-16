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

class GoogleAdsSearchads360V0ResourcesVisit extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ASSET_FIELD_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const ASSET_FIELD_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The asset is linked for use as a headline.
   */
  public const ASSET_FIELD_TYPE_HEADLINE = 'HEADLINE';
  /**
   * The asset is linked for use as a description.
   */
  public const ASSET_FIELD_TYPE_DESCRIPTION = 'DESCRIPTION';
  /**
   * The asset is linked for use as mandatory ad text.
   */
  public const ASSET_FIELD_TYPE_MANDATORY_AD_TEXT = 'MANDATORY_AD_TEXT';
  /**
   * The asset is linked for use as a marketing image.
   */
  public const ASSET_FIELD_TYPE_MARKETING_IMAGE = 'MARKETING_IMAGE';
  /**
   * The asset is linked for use as a media bundle.
   */
  public const ASSET_FIELD_TYPE_MEDIA_BUNDLE = 'MEDIA_BUNDLE';
  /**
   * The asset is linked for use as a YouTube video.
   */
  public const ASSET_FIELD_TYPE_YOUTUBE_VIDEO = 'YOUTUBE_VIDEO';
  /**
   * The asset is linked to indicate that a hotels campaign is "Book on Google"
   * enabled.
   */
  public const ASSET_FIELD_TYPE_BOOK_ON_GOOGLE = 'BOOK_ON_GOOGLE';
  /**
   * The asset is linked for use as a Lead Form extension.
   */
  public const ASSET_FIELD_TYPE_LEAD_FORM = 'LEAD_FORM';
  /**
   * The asset is linked for use as a Promotion extension.
   */
  public const ASSET_FIELD_TYPE_PROMOTION = 'PROMOTION';
  /**
   * The asset is linked for use as a Callout extension.
   */
  public const ASSET_FIELD_TYPE_CALLOUT = 'CALLOUT';
  /**
   * The asset is linked for use as a Structured Snippet extension.
   */
  public const ASSET_FIELD_TYPE_STRUCTURED_SNIPPET = 'STRUCTURED_SNIPPET';
  /**
   * The asset is linked for use as a Sitelink.
   */
  public const ASSET_FIELD_TYPE_SITELINK = 'SITELINK';
  /**
   * The asset is linked for use as a Mobile App extension.
   */
  public const ASSET_FIELD_TYPE_MOBILE_APP = 'MOBILE_APP';
  /**
   * The asset is linked for use as a Hotel Callout extension.
   */
  public const ASSET_FIELD_TYPE_HOTEL_CALLOUT = 'HOTEL_CALLOUT';
  /**
   * The asset is linked for use as a Call extension.
   */
  public const ASSET_FIELD_TYPE_CALL = 'CALL';
  /**
   * The asset is linked for use as a Price extension.
   */
  public const ASSET_FIELD_TYPE_PRICE = 'PRICE';
  /**
   * The asset is linked for use as a long headline.
   */
  public const ASSET_FIELD_TYPE_LONG_HEADLINE = 'LONG_HEADLINE';
  /**
   * The asset is linked for use as a business name.
   */
  public const ASSET_FIELD_TYPE_BUSINESS_NAME = 'BUSINESS_NAME';
  /**
   * The asset is linked for use as a square marketing image.
   */
  public const ASSET_FIELD_TYPE_SQUARE_MARKETING_IMAGE = 'SQUARE_MARKETING_IMAGE';
  /**
   * The asset is linked for use as a portrait marketing image.
   */
  public const ASSET_FIELD_TYPE_PORTRAIT_MARKETING_IMAGE = 'PORTRAIT_MARKETING_IMAGE';
  /**
   * The asset is linked for use as a logo.
   */
  public const ASSET_FIELD_TYPE_LOGO = 'LOGO';
  /**
   * The asset is linked for use as a landscape logo.
   */
  public const ASSET_FIELD_TYPE_LANDSCAPE_LOGO = 'LANDSCAPE_LOGO';
  /**
   * The asset is linked for use as a non YouTube logo.
   */
  public const ASSET_FIELD_TYPE_VIDEO = 'VIDEO';
  /**
   * The asset is linked for use to select a call-to-action.
   */
  public const ASSET_FIELD_TYPE_CALL_TO_ACTION_SELECTION = 'CALL_TO_ACTION_SELECTION';
  /**
   * The asset is linked for use to select an ad image.
   */
  public const ASSET_FIELD_TYPE_AD_IMAGE = 'AD_IMAGE';
  /**
   * The asset is linked for use as a business logo.
   */
  public const ASSET_FIELD_TYPE_BUSINESS_LOGO = 'BUSINESS_LOGO';
  /**
   * The asset is linked for use as a hotel property in a Performance Max for
   * travel goals campaign.
   */
  public const ASSET_FIELD_TYPE_HOTEL_PROPERTY = 'HOTEL_PROPERTY';
  /**
   * The asset is linked for use as a discovery carousel card.
   */
  public const ASSET_FIELD_TYPE_DISCOVERY_CAROUSEL_CARD = 'DISCOVERY_CAROUSEL_CARD';
  /**
   * The asset is linked for use as a long description.
   */
  public const ASSET_FIELD_TYPE_LONG_DESCRIPTION = 'LONG_DESCRIPTION';
  /**
   * The asset is linked for use as a call-to-action.
   */
  public const ASSET_FIELD_TYPE_CALL_TO_ACTION = 'CALL_TO_ACTION';
  /**
   * Not specified.
   */
  public const PRODUCT_CHANNEL_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const PRODUCT_CHANNEL_UNKNOWN = 'UNKNOWN';
  /**
   * The item is sold online.
   */
  public const PRODUCT_CHANNEL_ONLINE = 'ONLINE';
  /**
   * The item is sold in local stores.
   */
  public const PRODUCT_CHANNEL_LOCAL = 'LOCAL';
  /**
   * Output only. Ad ID. A value of 0 indicates that the ad is unattributed.
   *
   * @var string
   */
  public $adId;
  /**
   * Output only. Asset field type of the visit event.
   *
   * @var string
   */
  public $assetFieldType;
  /**
   * Output only. ID of the asset which was interacted with during the visit
   * event.
   *
   * @var string
   */
  public $assetId;
  /**
   * Output only. A unique string for each visit that is passed to the landing
   * page as the click id URL parameter.
   *
   * @var string
   */
  public $clickId;
  /**
   * Output only. Search Ads 360 keyword ID. A value of 0 indicates that the
   * keyword is unattributed.
   *
   * @var string
   */
  public $criterionId;
  /**
   * Output only. The ID of the visit.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. The Search Ads 360 inventory account ID containing the product
   * that was clicked on. Search Ads 360 generates this ID when you link an
   * inventory account in Search Ads 360.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Output only. The sales channel of the product that was clicked on: Online
   * or Local.
   *
   * @var string
   */
  public $productChannel;
  /**
   * Output only. The country (ISO-3166 format) registered for the inventory
   * feed that contains the product clicked on.
   *
   * @var string
   */
  public $productCountryCode;
  /**
   * Output only. The ID of the product clicked on.
   *
   * @var string
   */
  public $productId;
  /**
   * Output only. The language (ISO-639-1) that has been set for the Merchant
   * Center feed containing data about the product.
   *
   * @var string
   */
  public $productLanguageCode;
  /**
   * Output only. The store in the Local Inventory Ad that was clicked on. This
   * should match the store IDs used in your local products feed.
   *
   * @var string
   */
  public $productStoreId;
  /**
   * Output only. The resource name of the visit. Visit resource names have the
   * form:
   * `customers/{customer_id}/visits/{ad_group_id}~{criterion_id}~{ds_visit_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The timestamp of the visit event. The timestamp is in the
   * customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @var string
   */
  public $visitDateTime;

  /**
   * Output only. Ad ID. A value of 0 indicates that the ad is unattributed.
   *
   * @param string $adId
   */
  public function setAdId($adId)
  {
    $this->adId = $adId;
  }
  /**
   * @return string
   */
  public function getAdId()
  {
    return $this->adId;
  }
  /**
   * Output only. Asset field type of the visit event.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, HEADLINE, DESCRIPTION,
   * MANDATORY_AD_TEXT, MARKETING_IMAGE, MEDIA_BUNDLE, YOUTUBE_VIDEO,
   * BOOK_ON_GOOGLE, LEAD_FORM, PROMOTION, CALLOUT, STRUCTURED_SNIPPET,
   * SITELINK, MOBILE_APP, HOTEL_CALLOUT, CALL, PRICE, LONG_HEADLINE,
   * BUSINESS_NAME, SQUARE_MARKETING_IMAGE, PORTRAIT_MARKETING_IMAGE, LOGO,
   * LANDSCAPE_LOGO, VIDEO, CALL_TO_ACTION_SELECTION, AD_IMAGE, BUSINESS_LOGO,
   * HOTEL_PROPERTY, DISCOVERY_CAROUSEL_CARD, LONG_DESCRIPTION, CALL_TO_ACTION
   *
   * @param self::ASSET_FIELD_TYPE_* $assetFieldType
   */
  public function setAssetFieldType($assetFieldType)
  {
    $this->assetFieldType = $assetFieldType;
  }
  /**
   * @return self::ASSET_FIELD_TYPE_*
   */
  public function getAssetFieldType()
  {
    return $this->assetFieldType;
  }
  /**
   * Output only. ID of the asset which was interacted with during the visit
   * event.
   *
   * @param string $assetId
   */
  public function setAssetId($assetId)
  {
    $this->assetId = $assetId;
  }
  /**
   * @return string
   */
  public function getAssetId()
  {
    return $this->assetId;
  }
  /**
   * Output only. A unique string for each visit that is passed to the landing
   * page as the click id URL parameter.
   *
   * @param string $clickId
   */
  public function setClickId($clickId)
  {
    $this->clickId = $clickId;
  }
  /**
   * @return string
   */
  public function getClickId()
  {
    return $this->clickId;
  }
  /**
   * Output only. Search Ads 360 keyword ID. A value of 0 indicates that the
   * keyword is unattributed.
   *
   * @param string $criterionId
   */
  public function setCriterionId($criterionId)
  {
    $this->criterionId = $criterionId;
  }
  /**
   * @return string
   */
  public function getCriterionId()
  {
    return $this->criterionId;
  }
  /**
   * Output only. The ID of the visit.
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
   * Output only. The Search Ads 360 inventory account ID containing the product
   * that was clicked on. Search Ads 360 generates this ID when you link an
   * inventory account in Search Ads 360.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  /**
   * Output only. The sales channel of the product that was clicked on: Online
   * or Local.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ONLINE, LOCAL
   *
   * @param self::PRODUCT_CHANNEL_* $productChannel
   */
  public function setProductChannel($productChannel)
  {
    $this->productChannel = $productChannel;
  }
  /**
   * @return self::PRODUCT_CHANNEL_*
   */
  public function getProductChannel()
  {
    return $this->productChannel;
  }
  /**
   * Output only. The country (ISO-3166 format) registered for the inventory
   * feed that contains the product clicked on.
   *
   * @param string $productCountryCode
   */
  public function setProductCountryCode($productCountryCode)
  {
    $this->productCountryCode = $productCountryCode;
  }
  /**
   * @return string
   */
  public function getProductCountryCode()
  {
    return $this->productCountryCode;
  }
  /**
   * Output only. The ID of the product clicked on.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * Output only. The language (ISO-639-1) that has been set for the Merchant
   * Center feed containing data about the product.
   *
   * @param string $productLanguageCode
   */
  public function setProductLanguageCode($productLanguageCode)
  {
    $this->productLanguageCode = $productLanguageCode;
  }
  /**
   * @return string
   */
  public function getProductLanguageCode()
  {
    return $this->productLanguageCode;
  }
  /**
   * Output only. The store in the Local Inventory Ad that was clicked on. This
   * should match the store IDs used in your local products feed.
   *
   * @param string $productStoreId
   */
  public function setProductStoreId($productStoreId)
  {
    $this->productStoreId = $productStoreId;
  }
  /**
   * @return string
   */
  public function getProductStoreId()
  {
    return $this->productStoreId;
  }
  /**
   * Output only. The resource name of the visit. Visit resource names have the
   * form:
   * `customers/{customer_id}/visits/{ad_group_id}~{criterion_id}~{ds_visit_id}`
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
   * Output only. The timestamp of the visit event. The timestamp is in the
   * customer's time zone and in "yyyy-MM-dd HH:mm:ss" format.
   *
   * @param string $visitDateTime
   */
  public function setVisitDateTime($visitDateTime)
  {
    $this->visitDateTime = $visitDateTime;
  }
  /**
   * @return string
   */
  public function getVisitDateTime()
  {
    return $this->visitDateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesVisit::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesVisit');
