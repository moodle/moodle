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

class GoogleAdsSearchads360V0ResourcesAssetSetAsset extends \Google\Model
{
  /**
   * The status has not been specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * The received value is not known in this version. This is a response-only
   * value.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The asset set asset is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * The asset set asset is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Immutable. The asset which this asset set asset is linking to.
   *
   * @var string
   */
  public $asset;
  /**
   * Immutable. The asset set which this asset set asset is linking to.
   *
   * @var string
   */
  public $assetSet;
  /**
   * Immutable. The resource name of the asset set asset. Asset set asset
   * resource names have the form:
   * `customers/{customer_id}/assetSetAssets/{asset_set_id}~{asset_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. The status of the asset set asset. Read-only.
   *
   * @var string
   */
  public $status;

  /**
   * Immutable. The asset which this asset set asset is linking to.
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
   * Immutable. The asset set which this asset set asset is linking to.
   *
   * @param string $assetSet
   */
  public function setAssetSet($assetSet)
  {
    $this->assetSet = $assetSet;
  }
  /**
   * @return string
   */
  public function getAssetSet()
  {
    return $this->assetSet;
  }
  /**
   * Immutable. The resource name of the asset set asset. Asset set asset
   * resource names have the form:
   * `customers/{customer_id}/assetSetAssets/{asset_set_id}~{asset_id}`
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
   * Output only. The status of the asset set asset. Read-only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAssetSetAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAssetSetAsset');
