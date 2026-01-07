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

namespace Google\Service\SecurityCommandCenter;

class ListAssetsResult extends \Google\Model
{
  /**
   * State change is unused, this is the canonical default for this enum.
   */
  public const STATE_CHANGE_UNUSED = 'UNUSED';
  /**
   * Asset was added between the points in time.
   */
  public const STATE_CHANGE_ADDED = 'ADDED';
  /**
   * Asset was removed between the points in time.
   */
  public const STATE_CHANGE_REMOVED = 'REMOVED';
  /**
   * Asset was present at both point(s) in time.
   */
  public const STATE_CHANGE_ACTIVE = 'ACTIVE';
  protected $assetType = Asset::class;
  protected $assetDataType = '';
  /**
   * State change of the asset between the points in time.
   *
   * @var string
   */
  public $stateChange;

  /**
   * Asset matching the search request.
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
   * State change of the asset between the points in time.
   *
   * Accepted values: UNUSED, ADDED, REMOVED, ACTIVE
   *
   * @param self::STATE_CHANGE_* $stateChange
   */
  public function setStateChange($stateChange)
  {
    $this->stateChange = $stateChange;
  }
  /**
   * @return self::STATE_CHANGE_*
   */
  public function getStateChange()
  {
    return $this->stateChange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListAssetsResult::class, 'Google_Service_SecurityCommandCenter_ListAssetsResult');
