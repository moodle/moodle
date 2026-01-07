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

class GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigOptimizationObjectiveConfig extends \Google\Model
{
  /**
   * Required. The name of the field to target. Currently supported values:
   * `watch-percentage`, `watch-time`.
   *
   * @var string
   */
  public $targetField;
  /**
   * Required. The threshold to be applied to the target (e.g., 0.5).
   *
   * @var float
   */
  public $targetFieldValueFloat;

  /**
   * Required. The name of the field to target. Currently supported values:
   * `watch-percentage`, `watch-time`.
   *
   * @param string $targetField
   */
  public function setTargetField($targetField)
  {
    $this->targetField = $targetField;
  }
  /**
   * @return string
   */
  public function getTargetField()
  {
    return $this->targetField;
  }
  /**
   * Required. The threshold to be applied to the target (e.g., 0.5).
   *
   * @param float $targetFieldValueFloat
   */
  public function setTargetFieldValueFloat($targetFieldValueFloat)
  {
    $this->targetFieldValueFloat = $targetFieldValueFloat;
  }
  /**
   * @return float
   */
  public function getTargetFieldValueFloat()
  {
    return $this->targetFieldValueFloat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigOptimizationObjectiveConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1EngineMediaRecommendationEngineConfigOptimizationObjectiveConfig');
