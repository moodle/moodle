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

class TrackingFloodlightActivityConfig extends \Google\Model
{
  /**
   * Required. The ID of the Floodlight activity.
   *
   * @var string
   */
  public $floodlightActivityId;
  /**
   * Required. The number of days after an ad has been clicked in which a
   * conversion may be counted. Must be between 0 and 90 inclusive.
   *
   * @var int
   */
  public $postClickLookbackWindowDays;
  /**
   * Required. The number of days after an ad has been viewed in which a
   * conversion may be counted. Must be between 0 and 90 inclusive.
   *
   * @var int
   */
  public $postViewLookbackWindowDays;

  /**
   * Required. The ID of the Floodlight activity.
   *
   * @param string $floodlightActivityId
   */
  public function setFloodlightActivityId($floodlightActivityId)
  {
    $this->floodlightActivityId = $floodlightActivityId;
  }
  /**
   * @return string
   */
  public function getFloodlightActivityId()
  {
    return $this->floodlightActivityId;
  }
  /**
   * Required. The number of days after an ad has been clicked in which a
   * conversion may be counted. Must be between 0 and 90 inclusive.
   *
   * @param int $postClickLookbackWindowDays
   */
  public function setPostClickLookbackWindowDays($postClickLookbackWindowDays)
  {
    $this->postClickLookbackWindowDays = $postClickLookbackWindowDays;
  }
  /**
   * @return int
   */
  public function getPostClickLookbackWindowDays()
  {
    return $this->postClickLookbackWindowDays;
  }
  /**
   * Required. The number of days after an ad has been viewed in which a
   * conversion may be counted. Must be between 0 and 90 inclusive.
   *
   * @param int $postViewLookbackWindowDays
   */
  public function setPostViewLookbackWindowDays($postViewLookbackWindowDays)
  {
    $this->postViewLookbackWindowDays = $postViewLookbackWindowDays;
  }
  /**
   * @return int
   */
  public function getPostViewLookbackWindowDays()
  {
    return $this->postViewLookbackWindowDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TrackingFloodlightActivityConfig::class, 'Google_Service_DisplayVideo_TrackingFloodlightActivityConfig');
