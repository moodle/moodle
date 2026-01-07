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

class GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting extends \Google\Model
{
  /**
   * Not specified.
   */
  public const NEGATIVE_GEO_TARGET_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const NEGATIVE_GEO_TARGET_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Specifies that a user is excluded from seeing the ad if they are in, or
   * show interest in, advertiser's excluded locations.
   */
  public const NEGATIVE_GEO_TARGET_TYPE_PRESENCE_OR_INTEREST = 'PRESENCE_OR_INTEREST';
  /**
   * Specifies that a user is excluded from seeing the ad if they are in
   * advertiser's excluded locations.
   */
  public const NEGATIVE_GEO_TARGET_TYPE_PRESENCE = 'PRESENCE';
  /**
   * Not specified.
   */
  public const POSITIVE_GEO_TARGET_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The value is unknown in this version.
   */
  public const POSITIVE_GEO_TARGET_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Specifies that an ad is triggered if the user is in, or shows interest in,
   * advertiser's targeted locations.
   */
  public const POSITIVE_GEO_TARGET_TYPE_PRESENCE_OR_INTEREST = 'PRESENCE_OR_INTEREST';
  /**
   * Specifies that an ad is triggered if the user searches for advertiser's
   * targeted locations. This can only be used with Search and standard Shopping
   * campaigns.
   */
  public const POSITIVE_GEO_TARGET_TYPE_SEARCH_INTEREST = 'SEARCH_INTEREST';
  /**
   * Specifies that an ad is triggered if the user is in or regularly in
   * advertiser's targeted locations.
   */
  public const POSITIVE_GEO_TARGET_TYPE_PRESENCE = 'PRESENCE';
  /**
   * The setting used for negative geotargeting in this particular campaign.
   *
   * @var string
   */
  public $negativeGeoTargetType;
  /**
   * The setting used for positive geotargeting in this particular campaign.
   *
   * @var string
   */
  public $positiveGeoTargetType;

  /**
   * The setting used for negative geotargeting in this particular campaign.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, PRESENCE_OR_INTEREST, PRESENCE
   *
   * @param self::NEGATIVE_GEO_TARGET_TYPE_* $negativeGeoTargetType
   */
  public function setNegativeGeoTargetType($negativeGeoTargetType)
  {
    $this->negativeGeoTargetType = $negativeGeoTargetType;
  }
  /**
   * @return self::NEGATIVE_GEO_TARGET_TYPE_*
   */
  public function getNegativeGeoTargetType()
  {
    return $this->negativeGeoTargetType;
  }
  /**
   * The setting used for positive geotargeting in this particular campaign.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, PRESENCE_OR_INTEREST,
   * SEARCH_INTEREST, PRESENCE
   *
   * @param self::POSITIVE_GEO_TARGET_TYPE_* $positiveGeoTargetType
   */
  public function setPositiveGeoTargetType($positiveGeoTargetType)
  {
    $this->positiveGeoTargetType = $positiveGeoTargetType;
  }
  /**
   * @return self::POSITIVE_GEO_TARGET_TYPE_*
   */
  public function getPositiveGeoTargetType()
  {
    return $this->positiveGeoTargetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesCampaignGeoTargetTypeSetting');
