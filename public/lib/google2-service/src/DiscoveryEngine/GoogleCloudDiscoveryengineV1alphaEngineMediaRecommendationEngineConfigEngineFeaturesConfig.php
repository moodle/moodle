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

class GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig extends \Google\Model
{
  protected $mostPopularConfigType = GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig::class;
  protected $mostPopularConfigDataType = '';
  protected $recommendedForYouConfigType = GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig::class;
  protected $recommendedForYouConfigDataType = '';

  /**
   * Most popular engine feature config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig $mostPopularConfig
   */
  public function setMostPopularConfig(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig $mostPopularConfig)
  {
    $this->mostPopularConfig = $mostPopularConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigMostPopularFeatureConfig
   */
  public function getMostPopularConfig()
  {
    return $this->mostPopularConfig;
  }
  /**
   * Recommended for you engine feature config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig $recommendedForYouConfig
   */
  public function setRecommendedForYouConfig(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig $recommendedForYouConfig)
  {
    $this->recommendedForYouConfig = $recommendedForYouConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigRecommendedForYouFeatureConfig
   */
  public function getRecommendedForYouConfig()
  {
    return $this->recommendedForYouConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig');
