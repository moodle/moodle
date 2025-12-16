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

class GoogleAdsSearchads360V0ResourcesAssetGroupTopCombinationView extends \Google\Collection
{
  protected $collection_key = 'assetGroupTopCombinations';
  protected $assetGroupTopCombinationsType = GoogleAdsSearchads360V0ResourcesAssetGroupAssetCombinationData::class;
  protected $assetGroupTopCombinationsDataType = 'array';
  /**
   * Output only. The resource name of the asset group top combination view.
   * AssetGroup Top Combination view resource names have the form: `"customers/{
   * customer_id}/assetGroupTopCombinationViews/{asset_group_id}~{asset_combinat
   * ion_category}"
   *
   * @var string
   */
  public $resourceName;

  /**
   * Output only. The top combinations of assets that served together.
   *
   * @param GoogleAdsSearchads360V0ResourcesAssetGroupAssetCombinationData[] $assetGroupTopCombinations
   */
  public function setAssetGroupTopCombinations($assetGroupTopCombinations)
  {
    $this->assetGroupTopCombinations = $assetGroupTopCombinations;
  }
  /**
   * @return GoogleAdsSearchads360V0ResourcesAssetGroupAssetCombinationData[]
   */
  public function getAssetGroupTopCombinations()
  {
    return $this->assetGroupTopCombinations;
  }
  /**
   * Output only. The resource name of the asset group top combination view.
   * AssetGroup Top Combination view resource names have the form: `"customers/{
   * customer_id}/assetGroupTopCombinationViews/{asset_group_id}~{asset_combinat
   * ion_category}"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAssetGroupTopCombinationView::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAssetGroupTopCombinationView');
