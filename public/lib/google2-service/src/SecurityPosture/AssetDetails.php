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

namespace Google\Service\SecurityPosture;

class AssetDetails extends \Google\Model
{
  /**
   * Information about the Cloud Asset Inventory asset that violated a policy.
   * The format of this information can change at any time without prior notice.
   * Your application must not depend on this information in any way.
   *
   * @var string
   */
  public $asset;
  /**
   * The type of Cloud Asset Inventory asset. For a list of asset types, see
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types).
   *
   * @var string
   */
  public $assetType;

  /**
   * Information about the Cloud Asset Inventory asset that violated a policy.
   * The format of this information can change at any time without prior notice.
   * Your application must not depend on this information in any way.
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
   * The type of Cloud Asset Inventory asset. For a list of asset types, see
   * [Supported asset types](https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types).
   *
   * @param string $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return string
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetDetails::class, 'Google_Service_SecurityPosture_AssetDetails');
