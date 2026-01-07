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

class GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec extends \Google\Model
{
  /**
   * True if Measurement.elapsed_duration is used as the x-axis of each Trials
   * Decay Curve. Otherwise, Measurement.step_count will be used as the x-axis.
   *
   * @var bool
   */
  public $useElapsedDuration;

  /**
   * True if Measurement.elapsed_duration is used as the x-axis of each Trials
   * Decay Curve. Otherwise, Measurement.step_count will be used as the x-axis.
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
class_alias(GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecDecayCurveAutomatedStoppingSpec');
