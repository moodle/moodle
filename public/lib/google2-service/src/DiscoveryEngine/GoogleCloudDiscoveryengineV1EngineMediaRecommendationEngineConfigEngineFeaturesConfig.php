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

class GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigEngineFeaturesConfig extends \Google\Model
{
  protected $mostPopularConfigType = GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigMostPopularFeatureConfig::class;
  protected $mostPopularConfigDataType = '';
  protected $recommendedForYouConfigType = GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig::class;
  protected $recommendedForYouConfigDataType = '';

  /**
   * Most popular engine feature config.
   *
   * @param GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigMostPopularFeatureConfig $mostPopularConfig
   */
  public function setMostPopularConfig(GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigMostPopularFeatureConfig $mostPopularConfig)
  {
    $this->mostPopularConfig = $mostPopularConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigMostPopularFeatureConfig
   */
  public function getMostPopularConfig()
  {
    return $this->mostPopularConfig;
  }
  /**
   * Recommended for you engine feature config.
   *
   * @param GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig $recommendedForYouConfig
   */
  public function setRecommendedForYouConfig(GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig $recommendedForYouConfig)
  {
    $this->recommendedForYouConfig = $recommendedForYouConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig
   */
  public function getRecommendedForYouConfig()
  {
    return $this->recommendedForYouConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigEngineFeaturesConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigEngineFeaturesConfig');
