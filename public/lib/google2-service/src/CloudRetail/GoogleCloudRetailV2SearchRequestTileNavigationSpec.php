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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchRequestTileNavigationSpec extends \Google\Collection
{
  protected $collection_key = 'appliedTiles';
  protected $appliedTilesType = GoogleCloudRetailV2Tile::class;
  protected $appliedTilesDataType = 'array';
  /**
   * This field specifies whether the customer would like to request tile
   * navigation.
   *
   * @var bool
   */
  public $tileNavigationRequested;

  /**
   * This optional field specifies the tiles which are already clicked in client
   * side. While the feature works without this field set, particularly for an
   * initial query, it is highly recommended to set this field because it can
   * improve the quality of the search response and removes possible duplicate
   * tiles. NOTE: This field is not being used for filtering search products.
   * Client side should also put all the applied tiles in SearchRequest.filter.
   *
   * @param GoogleCloudRetailV2Tile[] $appliedTiles
   */
  public function setAppliedTiles($appliedTiles)
  {
    $this->appliedTiles = $appliedTiles;
  }
  /**
   * @return GoogleCloudRetailV2Tile[]
   */
  public function getAppliedTiles()
  {
    return $this->appliedTiles;
  }
  /**
   * This field specifies whether the customer would like to request tile
   * navigation.
   *
   * @param bool $tileNavigationRequested
   */
  public function setTileNavigationRequested($tileNavigationRequested)
  {
    $this->tileNavigationRequested = $tileNavigationRequested;
  }
  /**
   * @return bool
   */
  public function getTileNavigationRequested()
  {
    return $this->tileNavigationRequested;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequestTileNavigationSpec::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestTileNavigationSpec');
