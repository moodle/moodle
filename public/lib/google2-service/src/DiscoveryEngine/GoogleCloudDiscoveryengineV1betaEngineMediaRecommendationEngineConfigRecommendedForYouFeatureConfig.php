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

class GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig extends \Google\Model
{
  /**
   * The type of event with which the engine is queried at prediction time. If
   * set to `generic`, only `view-item`, `media-play`,and `media-complete` will
   * be used as `context-event` in engine training. If set to `view-home-page`,
   * `view-home-page` will also be used as `context-events` in addition to
   * `view-item`, `media-play`, and `media-complete`. Currently supported for
   * the `recommended-for-you` engine. Currently supported values: `view-home-
   * page`, `generic`.
   *
   * @var string
   */
  public $contextEventType;

  /**
   * The type of event with which the engine is queried at prediction time. If
   * set to `generic`, only `view-item`, `media-play`,and `media-complete` will
   * be used as `context-event` in engine training. If set to `view-home-page`,
   * `view-home-page` will also be used as `context-events` in addition to
   * `view-item`, `media-play`, and `media-complete`. Currently supported for
   * the `recommended-for-you` engine. Currently supported values: `view-home-
   * page`, `generic`.
   *
   * @param string $contextEventType
   */
  public function setContextEventType($contextEventType)
  {
    $this->contextEventType = $contextEventType;
  }
  /**
   * @return string
   */
  public function getContextEventType()
  {
    return $this->contextEventType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig');
