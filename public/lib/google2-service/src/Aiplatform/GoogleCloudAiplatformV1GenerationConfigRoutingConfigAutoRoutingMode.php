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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode extends \Google\Model
{
  /**
   * Unspecified model routing preference.
   */
  public const MODEL_ROUTING_PREFERENCE_UNKNOWN = 'UNKNOWN';
  /**
   * The model will be selected to prioritize the quality of the response.
   */
  public const MODEL_ROUTING_PREFERENCE_PRIORITIZE_QUALITY = 'PRIORITIZE_QUALITY';
  /**
   * The model will be selected to balance quality and cost.
   */
  public const MODEL_ROUTING_PREFERENCE_BALANCED = 'BALANCED';
  /**
   * The model will be selected to prioritize the cost of the request.
   */
  public const MODEL_ROUTING_PREFERENCE_PRIORITIZE_COST = 'PRIORITIZE_COST';
  /**
   * The model routing preference.
   *
   * @var string
   */
  public $modelRoutingPreference;

  /**
   * The model routing preference.
   *
   * Accepted values: UNKNOWN, PRIORITIZE_QUALITY, BALANCED, PRIORITIZE_COST
   *
   * @param self::MODEL_ROUTING_PREFERENCE_* $modelRoutingPreference
   */
  public function setModelRoutingPreference($modelRoutingPreference)
  {
    $this->modelRoutingPreference = $modelRoutingPreference;
  }
  /**
   * @return self::MODEL_ROUTING_PREFERENCE_*
   */
  public function getModelRoutingPreference()
  {
    return $this->modelRoutingPreference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerationConfigRoutingConfigAutoRoutingMode');
