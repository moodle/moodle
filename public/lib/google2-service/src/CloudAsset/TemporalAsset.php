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

namespace Google\Service\CloudAsset;

class TemporalAsset extends \Google\Model
{
  /**
   * prior_asset is not applicable for the current asset.
   */
  public const PRIOR_ASSET_STATE_PRIOR_ASSET_STATE_UNSPECIFIED = 'PRIOR_ASSET_STATE_UNSPECIFIED';
  /**
   * prior_asset is populated correctly.
   */
  public const PRIOR_ASSET_STATE_PRESENT = 'PRESENT';
  /**
   * Failed to set prior_asset.
   */
  public const PRIOR_ASSET_STATE_INVALID = 'INVALID';
  /**
   * Current asset is the first known state.
   */
  public const PRIOR_ASSET_STATE_DOES_NOT_EXIST = 'DOES_NOT_EXIST';
  /**
   * prior_asset is a deletion.
   */
  public const PRIOR_ASSET_STATE_DELETED = 'DELETED';
  protected $assetType = Asset::class;
  protected $assetDataType = '';
  /**
   * Whether the asset has been deleted or not.
   *
   * @var bool
   */
  public $deleted;
  protected $priorAssetType = Asset::class;
  protected $priorAssetDataType = '';
  /**
   * State of prior_asset.
   *
   * @var string
   */
  public $priorAssetState;
  protected $windowType = TimeWindow::class;
  protected $windowDataType = '';

  /**
   * An asset in Google Cloud.
   *
   * @param Asset $asset
   */
  public function setAsset(Asset $asset)
  {
    $this->asset = $asset;
  }
  /**
   * @return Asset
   */
  public function getAsset()
  {
    return $this->asset;
  }
  /**
   * Whether the asset has been deleted or not.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Prior copy of the asset. Populated if prior_asset_state is PRESENT.
   * Currently this is only set for responses in Real-Time Feed.
   *
   * @param Asset $priorAsset
   */
  public function setPriorAsset(Asset $priorAsset)
  {
    $this->priorAsset = $priorAsset;
  }
  /**
   * @return Asset
   */
  public function getPriorAsset()
  {
    return $this->priorAsset;
  }
  /**
   * State of prior_asset.
   *
   * Accepted values: PRIOR_ASSET_STATE_UNSPECIFIED, PRESENT, INVALID,
   * DOES_NOT_EXIST, DELETED
   *
   * @param self::PRIOR_ASSET_STATE_* $priorAssetState
   */
  public function setPriorAssetState($priorAssetState)
  {
    $this->priorAssetState = $priorAssetState;
  }
  /**
   * @return self::PRIOR_ASSET_STATE_*
   */
  public function getPriorAssetState()
  {
    return $this->priorAssetState;
  }
  /**
   * The time window when the asset data and state was observed.
   *
   * @param TimeWindow $window
   */
  public function setWindow(TimeWindow $window)
  {
    $this->window = $window;
  }
  /**
   * @return TimeWindow
   */
  public function getWindow()
  {
    return $this->window;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TemporalAsset::class, 'Google_Service_CloudAsset_TemporalAsset');
