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

class GoogleCloudAiplatformV1ThresholdConfig extends \Google\Model
{
  /**
   * Specify a threshold value that can trigger the alert. If this threshold
   * config is for feature distribution distance: 1. For categorical feature,
   * the distribution distance is calculated by L-inifinity norm. 2. For
   * numerical feature, the distribution distance is calculated by
   * Jensen–Shannon divergence. Each feature must have a non-zero threshold if
   * they need to be monitored. Otherwise no alert will be triggered for that
   * feature.
   *
   * @var 
   */
  public $value;

  public function setValue($value)
  {
    $this->value = $value;
  }
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ThresholdConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ThresholdConfig');
