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

class YoutubeAssetAssociationLocationAssetFilter extends \Google\Model
{
  /**
   * Location matching type is not specified or is unknown in this version.
   */
  public const LOCATION_MATCHING_TYPE_LOCATION_MATCHING_TYPE_UNSPECIFIED = 'LOCATION_MATCHING_TYPE_UNSPECIFIED';
  /**
   * All available location assets are eligible for serving.
   */
  public const LOCATION_MATCHING_TYPE_SELECT_ALL = 'SELECT_ALL';
  /**
   * The location assets that match a provided business name and/or label
   * filters can serve.
   */
  public const LOCATION_MATCHING_TYPE_FILTER = 'FILTER';
  /**
   * Only the selected location assets can serve.
   */
  public const LOCATION_MATCHING_TYPE_SELECTED_ASSETS = 'SELECTED_ASSETS';
  /**
   * No location assets can serve.
   */
  public const LOCATION_MATCHING_TYPE_DISABLED = 'DISABLED';
  /**
   * Output only. The ID of the asset set that matches the location assets
   * eligible for serving.
   *
   * @var string
   */
  public $assetSetId;
  protected $locationMatchingFunctionType = YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction::class;
  protected $locationMatchingFunctionDataType = '';
  /**
   * Required. The matching type of this location asset filter.
   *
   * @var string
   */
  public $locationMatchingType;

  /**
   * Output only. The ID of the asset set that matches the location assets
   * eligible for serving.
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
  /**
   * Optional. The matching function that determines how the location asset
   * filter matches location assets. This field is required and can only be set
   * for if location_matching_type is `FILTER` or `SELECTED_ASSETS`.
   *
   * @param YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction $locationMatchingFunction
   */
  public function setLocationMatchingFunction(YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction $locationMatchingFunction)
  {
    $this->locationMatchingFunction = $locationMatchingFunction;
  }
  /**
   * @return YoutubeAssetAssociationLocationAssetFilterLocationMatchingFunction
   */
  public function getLocationMatchingFunction()
  {
    return $this->locationMatchingFunction;
  }
  /**
   * Required. The matching type of this location asset filter.
   *
   * Accepted values: LOCATION_MATCHING_TYPE_UNSPECIFIED, SELECT_ALL, FILTER,
   * SELECTED_ASSETS, DISABLED
   *
   * @param self::LOCATION_MATCHING_TYPE_* $locationMatchingType
   */
  public function setLocationMatchingType($locationMatchingType)
  {
    $this->locationMatchingType = $locationMatchingType;
  }
  /**
   * @return self::LOCATION_MATCHING_TYPE_*
   */
  public function getLocationMatchingType()
  {
    return $this->locationMatchingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(YoutubeAssetAssociationLocationAssetFilter::class, 'Google_Service_DisplayVideo_YoutubeAssetAssociationLocationAssetFilter');
