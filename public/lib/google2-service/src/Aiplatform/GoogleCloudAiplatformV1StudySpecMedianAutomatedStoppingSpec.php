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

class GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec extends \Google\Model
{
  /**
   * True if median automated stopping rule applies on
   * Measurement.elapsed_duration. It means that elapsed_duration field of
   * latest measurement of current Trial is used to compute median objective
   * value for each completed Trials.
   *
   * @var bool
   */
  public $useElapsedDuration;

  /**
   * True if median automated stopping rule applies on
   * Measurement.elapsed_duration. It means that elapsed_duration field of
   * latest measurement of current Trial is used to compute median objective
   * value for each completed Trials.
   *
   * @param bool $useElapsedDuration
   */
  public function setUseElapsedDuration($useElapsedDuration)
  {
    $this->useElapsedDuration = $useElapsedDuration;
  }
  /**
   * @return bool
   */
  public function getUseElapsedDuration()
  {
    return $this->useElapsedDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecMedianAutomatedStoppingSpec');
