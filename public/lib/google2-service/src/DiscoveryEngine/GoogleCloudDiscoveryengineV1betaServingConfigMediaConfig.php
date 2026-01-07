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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig extends \Google\Model
{
  /**
   * @var int
   */
  public $contentFreshnessCutoffDays;
  /**
   * @var float
   */
  public $contentWatchedPercentageThreshold;
  /**
   * @var float
   */
  public $contentWatchedSecondsThreshold;
  /**
   * @var string
   */
  public $demotionEventType;

  /**
   * @param int
   */
  public function setContentFreshnessCutoffDays($contentFreshnessCutoffDays)
  {
    $this->contentFreshnessCutoffDays = $contentFreshnessCutoffDays;
  }
  /**
   * @return int
   */
  public function getContentFreshnessCutoffDays()
  {
    return $this->contentFreshnessCutoffDays;
  }
  /**
   * @param float
   */
  public function setContentWatchedPercentageThreshold($contentWatchedPercentageThreshold)
  {
    $this->contentWatchedPercentageThreshold = $contentWatchedPercentageThreshold;
  }
  /**
   * @return float
   */
  public function getContentWatchedPercentageThreshold()
  {
    return $this->contentWatchedPercentageThreshold;
  }
  /**
   * @param float
   */
  public function setContentWatchedSecondsThreshold($contentWatchedSecondsThreshold)
  {
    $this->contentWatchedSecondsThreshold = $contentWatchedSecondsThreshold;
  }
  /**
   * @return float
   */
  public function getContentWatchedSecondsThreshold()
  {
    return $this->contentWatchedSecondsThreshold;
  }
  /**
   * @param string
   */
  public function setDemotionEventType($demotionEventType)
  {
    $this->demotionEventType = $demotionEventType;
  }
  /**
   * @return string
   */
  public function getDemotionEventType()
  {
    return $this->demotionEventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaServingConfigMediaConfig');
