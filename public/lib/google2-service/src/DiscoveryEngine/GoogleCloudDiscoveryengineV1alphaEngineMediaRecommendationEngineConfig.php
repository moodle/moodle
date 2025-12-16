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

class GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfig extends \Google\Model
{
  /**
   * Unspecified training state.
   */
  public const TRAINING_STATE_TRAINING_STATE_UNSPECIFIED = 'TRAINING_STATE_UNSPECIFIED';
  /**
   * The engine training is paused.
   */
  public const TRAINING_STATE_PAUSED = 'PAUSED';
  /**
   * The engine is training.
   */
  public const TRAINING_STATE_TRAINING = 'TRAINING';
  protected $engineFeaturesConfigType = GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig::class;
  protected $engineFeaturesConfigDataType = '';
  /**
   * The optimization objective. e.g., `cvr`. This field together with
   * optimization_objective describe engine metadata to use to control engine
   * training and serving. Currently supported values: `ctr`, `cvr`. If not
   * specified, we choose default based on engine type. Default depends on type
   * of recommendation: `recommended-for-you` => `ctr` `others-you-may-like` =>
   * `ctr`
   *
   * @var string
   */
  public $optimizationObjective;
  protected $optimizationObjectiveConfigType = GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigOptimizationObjectiveConfig::class;
  protected $optimizationObjectiveConfigDataType = '';
  /**
   * The training state that the engine is in (e.g. `TRAINING` or `PAUSED`).
   * Since part of the cost of running the service is frequency of training -
   * this can be used to determine when to train engine in order to control
   * cost. If not specified: the default value for `CreateEngine` method is
   * `TRAINING`. The default value for `UpdateEngine` method is to keep the
   * state the same as before.
   *
   * @var string
   */
  public $trainingState;
  /**
   * Required. The type of engine. e.g., `recommended-for-you`. This field
   * together with optimization_objective describe engine metadata to use to
   * control engine training and serving. Currently supported values:
   * `recommended-for-you`, `others-you-may-like`, `more-like-this`, `most-
   * popular-items`.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Additional engine features config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig $engineFeaturesConfig
   */
  public function setEngineFeaturesConfig(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig $engineFeaturesConfig)
  {
    $this->engineFeaturesConfig = $engineFeaturesConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigEngineFeaturesConfig
   */
  public function getEngineFeaturesConfig()
  {
    return $this->engineFeaturesConfig;
  }
  /**
   * The optimization objective. e.g., `cvr`. This field together with
   * optimization_objective describe engine metadata to use to control engine
   * training and serving. Currently supported values: `ctr`, `cvr`. If not
   * specified, we choose default based on engine type. Default depends on type
   * of recommendation: `recommended-for-you` => `ctr` `others-you-may-like` =>
   * `ctr`
   *
   * @param string $optimizationObjective
   */
  public function setOptimizationObjective($optimizationObjective)
  {
    $this->optimizationObjective = $optimizationObjective;
  }
  /**
   * @return string
   */
  public function getOptimizationObjective()
  {
    return $this->optimizationObjective;
  }
  /**
   * Name and value of the custom threshold for cvr optimization_objective. For
   * target_field `watch-time`, target_field_value must be an integer value
   * indicating the media progress time in seconds between (0, 86400] (excludes
   * 0, includes 86400) (e.g., 90). For target_field `watch-percentage`, the
   * target_field_value must be a valid float value between (0, 1.0] (excludes
   * 0, includes 1.0) (e.g., 0.5).
   *
   * @param GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigOptimizationObjectiveConfig $optimizationObjectiveConfig
   */
  public function setOptimizationObjectiveConfig(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigOptimizationObjectiveConfig $optimizationObjectiveConfig)
  {
    $this->optimizationObjectiveConfig = $optimizationObjectiveConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfigOptimizationObjectiveConfig
   */
  public function getOptimizationObjectiveConfig()
  {
    return $this->optimizationObjectiveConfig;
  }
  /**
   * The training state that the engine is in (e.g. `TRAINING` or `PAUSED`).
   * Since part of the cost of running the service is frequency of training -
   * this can be used to determine when to train engine in order to control
   * cost. If not specified: the default value for `CreateEngine` method is
   * `TRAINING`. The default value for `UpdateEngine` method is to keep the
   * state the same as before.
   *
   * Accepted values: TRAINING_STATE_UNSPECIFIED, PAUSED, TRAINING
   *
   * @param self::TRAINING_STATE_* $trainingState
   */
  public function setTrainingState($trainingState)
  {
    $this->trainingState = $trainingState;
  }
  /**
   * @return self::TRAINING_STATE_*
   */
  public function getTrainingState()
  {
    return $this->trainingState;
  }
  /**
   * Required. The type of engine. e.g., `recommended-for-you`. This field
   * together with optimization_objective describe engine metadata to use to
   * control engine training and serving. Currently supported values:
   * `recommended-for-you`, `others-you-may-like`, `more-like-this`, `most-
   * popular-items`.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaEngineMediaRecommendationEngineConfig');
