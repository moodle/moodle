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

class GoogleAdsSearchads360V0CommonAssetUsage extends \Google\Model
{
  /**
   * No value has been specified.
   */
  public const SERVED_ASSET_FIELD_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const SERVED_ASSET_FIELD_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * The asset is used in headline 1.
   */
  public const SERVED_ASSET_FIELD_TYPE_HEADLINE_1 = 'HEADLINE_1';
  /**
   * The asset is used in headline 2.
   */
  public const SERVED_ASSET_FIELD_TYPE_HEADLINE_2 = 'HEADLINE_2';
  /**
   * The asset is used in headline 3.
   */
  public const SERVED_ASSET_FIELD_TYPE_HEADLINE_3 = 'HEADLINE_3';
  /**
   * The asset is used in description 1.
   */
  public const SERVED_ASSET_FIELD_TYPE_DESCRIPTION_1 = 'DESCRIPTION_1';
  /**
   * The asset is used in description 2.
   */
  public const SERVED_ASSET_FIELD_TYPE_DESCRIPTION_2 = 'DESCRIPTION_2';
  /**
   * The asset was used in a headline. Use this only if there is only one
   * headline in the ad. Otherwise, use the HEADLINE_1, HEADLINE_2 or HEADLINE_3
   * enums
   */
  public const SERVED_ASSET_FIELD_TYPE_HEADLINE = 'HEADLINE';
  /**
   * The asset was used as a headline in portrait image.
   */
  public const SERVED_ASSET_FIELD_TYPE_HEADLINE_IN_PORTRAIT = 'HEADLINE_IN_PORTRAIT';
  /**
   * The asset was used in a long headline (used in MultiAssetResponsiveAd).
   */
  public const SERVED_ASSET_FIELD_TYPE_LONG_HEADLINE = 'LONG_HEADLINE';
  /**
   * The asset was used in a description. Use this only if there is only one
   * description in the ad. Otherwise, use the DESCRIPTION_1 or DESCRIPTION_@
   * enums
   */
  public const SERVED_ASSET_FIELD_TYPE_DESCRIPTION = 'DESCRIPTION';
  /**
   * The asset was used as description in portrait image.
   */
  public const SERVED_ASSET_FIELD_TYPE_DESCRIPTION_IN_PORTRAIT = 'DESCRIPTION_IN_PORTRAIT';
  /**
   * The asset was used as business name in portrait image.
   */
  public const SERVED_ASSET_FIELD_TYPE_BUSINESS_NAME_IN_PORTRAIT = 'BUSINESS_NAME_IN_PORTRAIT';
  /**
   * The asset was used as business name.
   */
  public const SERVED_ASSET_FIELD_TYPE_BUSINESS_NAME = 'BUSINESS_NAME';
  /**
   * The asset was used as a marketing image.
   */
  public const SERVED_ASSET_FIELD_TYPE_MARKETING_IMAGE = 'MARKETING_IMAGE';
  /**
   * The asset was used as a marketing image in portrait image.
   */
  public const SERVED_ASSET_FIELD_TYPE_MARKETING_IMAGE_IN_PORTRAIT = 'MARKETING_IMAGE_IN_PORTRAIT';
  /**
   * The asset was used as a square marketing image.
   */
  public const SERVED_ASSET_FIELD_TYPE_SQUARE_MARKETING_IMAGE = 'SQUARE_MARKETING_IMAGE';
  /**
   * The asset was used as a portrait marketing image.
   */
  public const SERVED_ASSET_FIELD_TYPE_PORTRAIT_MARKETING_IMAGE = 'PORTRAIT_MARKETING_IMAGE';
  /**
   * The asset was used as a logo.
   */
  public const SERVED_ASSET_FIELD_TYPE_LOGO = 'LOGO';
  /**
   * The asset was used as a landscape logo.
   */
  public const SERVED_ASSET_FIELD_TYPE_LANDSCAPE_LOGO = 'LANDSCAPE_LOGO';
  /**
   * The asset was used as a call-to-action.
   */
  public const SERVED_ASSET_FIELD_TYPE_CALL_TO_ACTION = 'CALL_TO_ACTION';
  /**
   * The asset was used as a YouTube video.
   */
  public const SERVED_ASSET_FIELD_TYPE_YOU_TUBE_VIDEO = 'YOU_TUBE_VIDEO';
  /**
   * This asset is used as a sitelink.
   */
  public const SERVED_ASSET_FIELD_TYPE_SITELINK = 'SITELINK';
  /**
   * This asset is used as a call.
   */
  public const SERVED_ASSET_FIELD_TYPE_CALL = 'CALL';
  /**
   * This asset is used as a mobile app.
   */
  public const SERVED_ASSET_FIELD_TYPE_MOBILE_APP = 'MOBILE_APP';
  /**
   * This asset is used as a callout.
   */
  public const SERVED_ASSET_FIELD_TYPE_CALLOUT = 'CALLOUT';
  /**
   * This asset is used as a structured snippet.
   */
  public const SERVED_ASSET_FIELD_TYPE_STRUCTURED_SNIPPET = 'STRUCTURED_SNIPPET';
  /**
   * This asset is used as a price.
   */
  public const SERVED_ASSET_FIELD_TYPE_PRICE = 'PRICE';
  /**
   * This asset is used as a promotion.
   */
  public const SERVED_ASSET_FIELD_TYPE_PROMOTION = 'PROMOTION';
  /**
   * This asset is used as an image.
   */
  public const SERVED_ASSET_FIELD_TYPE_AD_IMAGE = 'AD_IMAGE';
  /**
   * The asset is used as a lead form.
   */
  public const SERVED_ASSET_FIELD_TYPE_LEAD_FORM = 'LEAD_FORM';
  /**
   * The asset is used as a business logo.
   */
  public const SERVED_ASSET_FIELD_TYPE_BUSINESS_LOGO = 'BUSINESS_LOGO';
  /**
   * The asset is used as a description prefix.
   */
  public const SERVED_ASSET_FIELD_TYPE_DESCRIPTION_PREFIX = 'DESCRIPTION_PREFIX';
  /**
   * Resource name of the asset.
   *
   * @var string
   */
  public $asset;
  /**
   * The served field type of the asset.
   *
   * @var string
   */
  public $servedAssetFieldType;

  /**
   * Resource name of the asset.
   *
   * @param string $asset
   */
  public function setAsset($asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return string
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * The served field type of the asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, HEADLINE_1, HEADLINE_2, HEADLINE_3,
   * DESCRIPTION_1, DESCRIPTION_2, HEADLINE, HEADLINE_IN_PORTRAIT,
   * LONG_HEADLINE, DESCRIPTION, DESCRIPTION_IN_PORTRAIT,
   * BUSINESS_NAME_IN_PORTRAIT, BUSINESS_NAME, MARKETING_IMAGE,
   * MARKETING_IMAGE_IN_PORTRAIT, SQUARE_MARKETING_IMAGE,
   * PORTRAIT_MARKETING_IMAGE, LOGO, LANDSCAPE_LOGO, CALL_TO_ACTION,
   * YOU_TUBE_VIDEO, SITELINK, CALL, MOBILE_APP, CALLOUT, STRUCTURED_SNIPPET,
   * PRICE, PROMOTION, AD_IMAGE, LEAD_FORM, BUSINESS_LOGO, DESCRIPTION_PREFIX
   *
   * @param self::SERVED_ASSET_FIELD_TYPE_* $servedAssetFieldType
   */
  public function setServedAssetFieldType($servedAssetFieldType)
  {
    $this->servedAssetFieldType = $servedAssetFieldType;
  }
  /**
   * @return self::SERVED_ASSET_FIELD_TYPE_*
   */
  public function getServedAssetFieldType()
  {
    return $this->servedAssetFieldType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonAssetUsage::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonAssetUsage');
