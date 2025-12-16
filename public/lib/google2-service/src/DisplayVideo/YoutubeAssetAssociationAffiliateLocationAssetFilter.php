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

namespace Google\Service\DisplayVideo;

class YoutubeAssetAssociationAffiliateLocationAssetFilter extends \Google\Model
{
  /**
   * Affiliate location matching type is not specified or is unknown in this
   * version.
   */
  public const AFFILIATE_LOCATION_MATCHING_TYPE_AFFILIATE_LOCATION_MATCHING_TYPE_UNSPECIFIED = 'AFFILIATE_LOCATION_MATCHING_TYPE_UNSPECIFIED';
  /**
   * All available affiliate location assets are eligible for serving.
   */
  public const AFFILIATE_LOCATION_MATCHING_TYPE_SELECT_ALL = 'SELECT_ALL';
  /**
   * The selected affiliate location assets can serve.
   */
  public const AFFILIATE_LOCATION_MATCHING_TYPE_SELECTED_CHAINS = 'SELECTED_CHAINS';
  /**
   * No affiliate location assets can serve.
   */
  public const AFFILIATE_LOCATION_MATCHING_TYPE_DISABLED = 'DISABLED';
  protected $affiliateLocationMatchingFunctionType = YoutubeAssetAssociationAffiliateLocationAssetFilterAffiliateLocationMatchingFunction::class;
  protected $affiliateLocationMatchingFunctionDataType = '';
  /**
   * Required. The matching type of this affiliate location asset filter.
   *
   * @var string
   */
  public $affiliateLocationMatchingType;
  /**
   * Output only. The ID of the asset set that matches the affiliate location
   * assets eligible for serving.
   *
   * @var string
   */
  public $assetSetId;

  /**
   * Optional. The matching function that determines how the affiliate location
   * asset filter matches affiliate location assets. This field is required and
   * can only be set for if affiliate_location_matching_type is
   * `SELECTED_CHAINS`.
   *
   * @param YoutubeAssetAssociationAffiliateLocationAssetFilterAffiliateLocationMatchingFunction $affiliateLocationMatchingFunction
   */
  public function setAffiliateLocationMatchingFunction(YoutubeAssetAssociationAffiliateLocationAssetFilterAffiliateLocationMatchingFunction $affiliateLocationMatchingFunction)
  {
    $this->affiliateLocationMatchingFunction = $affiliateLocationMatchingFunction;
  }
  /**
   * @return YoutubeAssetAssociationAffiliateLocationAssetFilterAffiliateLocationMatchingFunction
   */
  public function getAffiliateLocationMatchingFunction()
  {
    return $this->affiliateLocationMatchingFunction;
  }
  /**
   * Required. The matching type of this affiliate location asset filter.
   *
   * Accepted values: AFFILIATE_LOCATION_MATCHING_TYPE_UNSPECIFIED, SELECT_ALL,
   * SELECTED_CHAINS, DISABLED
   *
   * @param self::AFFILIATE_LOCATION_MATCHING_TYPE_* $affiliateLocationMatchingType
   */
  public function setAffiliateLocationMatchingType($affiliateLocationMatchingType)
  {
    $this->affiliateLocationMatchingType = $affiliateLocationMatchingType;
  }
  /**
   * @return self::AFFILIATE_LOCATION_MATCHING_TYPE_*
   */
  public function getAffiliateLocationMatchingType()
  {
    return $this->affiliateLocationMatchingType;
  }
  /**
   * Output only. The ID of the asset set that matches the affiliate location
   * assets eligible for serving.
   *
   * @param string $assetSetId
   */
  public function setAssetSetId($assetSetId)
  {
    $this->assetSetId = $assetSetId;
  }
  /**
   * @return string
   */
  public function getAssetSetId()
  {
    return $this->assetSetId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAssetAssociationAffiliateLocationAssetFilter::class, 'Google_Service_DisplayVideo_YoutubeAssetAssociationAffiliateLocationAssetFilter');
