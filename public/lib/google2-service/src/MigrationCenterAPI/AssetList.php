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

namespace Google\Service\MigrationCenterAPI;

class AssetList extends \Google\Collection
{
  protected $collection_key = 'assetIds';
  /**
   * Required. A list of asset IDs
   *
   * @var string[]
   */
  public $assetIds;

  /**
   * Required. A list of asset IDs
   *
   * @param string[] $assetIds
   */
  public function setAssetIds($assetIds)
  {
    $this->assetIds = $assetIds;
  }
  /**
   * @return string[]
   */
  public function getAssetIds()
  {
    return $this->assetIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetList::class, 'Google_Service_MigrationCenterAPI_AssetList');
