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

class GoogleAdsSearchads360V0ResourcesAdGroupAsset extends \Google\Model
{
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Asset link is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Asset link has been removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Asset link is paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * Required. Immutable. The ad group to which the asset is linked.
   *
   * @var string
   */
  public $adGroup;
  /**
   * Required. Immutable. The asset which is linked to the ad group.
   *
   * @var string
   */
  public $asset;
  /**
   * Immutable. The resource name of the ad group asset. AdGroupAsset resource
   * names have the form: `customers/{customer_id}/adGroupAssets/{ad_group_id}~{
   * asset_id}~{field_type}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Status of the ad group asset.
   *
   * @var string
   */
  public $status;

  /**
   * Required. Immutable. The ad group to which the asset is linked.
   *
   * @param string $adGroup
   */
  public function setAdGroup($adGroup)
  {
    $this->adGroup = $adGroup;
  }
  /**
   * @return string
   */
  public function getAdGroup()
  {
    return $this->adGroup;
  }
  /**
   * Required. Immutable. The asset which is linked to the ad group.
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
   * Immutable. The resource name of the ad group asset. AdGroupAsset resource
   * names have the form: `customers/{customer_id}/adGroupAssets/{ad_group_id}~{
   * asset_id}~{field_type}`
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
   * Status of the ad group asset.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED, PAUSED
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
class_alias(GoogleAdsSearchads360V0ResourcesAdGroupAsset::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAdGroupAsset');
