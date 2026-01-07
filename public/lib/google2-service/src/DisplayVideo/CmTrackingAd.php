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

class CmTrackingAd extends \Google\Model
{
  /**
   * Optional. The ad ID of the campaign manager 360 tracking Ad.
   *
   * @var string
   */
  public $cmAdId;
  /**
   * Optional. The creative ID of the campaign manager 360 tracking Ad.
   *
   * @var string
   */
  public $cmCreativeId;
  /**
   * Optional. The placement ID of the campaign manager 360 tracking Ad.
   *
   * @var string
   */
  public $cmPlacementId;

  /**
   * Optional. The ad ID of the campaign manager 360 tracking Ad.
   *
   * @param string $cmAdId
   */
  public function setCmAdId($cmAdId)
  {
    $this->cmAdId = $cmAdId;
  }
  /**
   * @return string
   */
  public function getCmAdId()
  {
    return $this->cmAdId;
  }
  /**
   * Optional. The creative ID of the campaign manager 360 tracking Ad.
   *
   * @param string $cmCreativeId
   */
  public function setCmCreativeId($cmCreativeId)
  {
    $this->cmCreativeId = $cmCreativeId;
  }
  /**
   * @return string
   */
  public function getCmCreativeId()
  {
    return $this->cmCreativeId;
  }
  /**
   * Optional. The placement ID of the campaign manager 360 tracking Ad.
   *
   * @param string $cmPlacementId
   */
  public function setCmPlacementId($cmPlacementId)
  {
    $this->cmPlacementId = $cmPlacementId;
  }
  /**
   * @return string
   */
  public function getCmPlacementId()
  {
    return $this->cmPlacementId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CmTrackingAd::class, 'Google_Service_DisplayVideo_CmTrackingAd');
