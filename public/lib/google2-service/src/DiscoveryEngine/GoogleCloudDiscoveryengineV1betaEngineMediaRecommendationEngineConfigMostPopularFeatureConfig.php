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

class GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig extends \Google\Model
{
  /**
   * The time window of which the engine is queried at training and prediction
   * time. Positive integers only. The value translates to the last X days of
   * events. Currently required for the `most-popular-items` engine.
   *
   * @var string
   */
  public $timeWindowDays;

  /**
   * The time window of which the engine is queried at training and prediction
   * time. Positive integers only. The value translates to the last X days of
   * events. Currently required for the `most-popular-items` engine.
   *
   * @param string $timeWindowDays
   */
  public function setTimeWindowDays($timeWindowDays)
  {
    $this->timeWindowDays = $timeWindowDays;
  }
  /**
   * @return string
   */
  public function getTimeWindowDays()
  {
    return $this->timeWindowDays;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig');
