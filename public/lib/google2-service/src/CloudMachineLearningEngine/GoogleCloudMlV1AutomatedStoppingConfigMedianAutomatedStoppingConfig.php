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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1AutomatedStoppingConfigMedianAutomatedStoppingConfig extends \Google\Model
{
  /**
   * If true, the median automated stopping rule applies to
   * measurement.use_elapsed_time, which means the elapsed_time field of the
   * current trial's latest measurement is used to compute the median objective
   * value for each completed trial.
   *
   * @var bool
   */
  public $useElapsedTime;

  /**
   * If true, the median automated stopping rule applies to
   * measurement.use_elapsed_time, which means the elapsed_time field of the
   * current trial's latest measurement is used to compute the median objective
   * value for each completed trial.
   *
   * @param bool $useElapsedTime
   */
  public function setUseElapsedTime($useElapsedTime)
  {
    $this->useElapsedTime = $useElapsedTime;
  }
  /**
   * @return bool
   */
  public function getUseElapsedTime()
  {
    return $this->useElapsedTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1AutomatedStoppingConfigMedianAutomatedStoppingConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1AutomatedStoppingConfigMedianAutomatedStoppingConfig');
