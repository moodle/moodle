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

class GoogleCloudAiplatformV1StudySpecMetricSpecSafetyMetricConfig extends \Google\Model
{
  /**
   * Desired minimum fraction of safe trials (over total number of trials) that
   * should be targeted by the algorithm at any time during the study (best
   * effort). This should be between 0.0 and 1.0 and a value of 0.0 means that
   * there is no minimum and an algorithm proceeds without targeting any
   * specific fraction. A value of 1.0 means that the algorithm attempts to only
   * Suggest safe Trials.
   *
   * @var 
   */
  public $desiredMinSafeTrialsFraction;
  /**
   * Safety threshold (boundary value between safe and unsafe). NOTE that if you
   * leave SafetyMetricConfig unset, a default value of 0 will be used.
   *
   * @var 
   */
  public $safetyThreshold;

  public function setDesiredMinSafeTrialsFraction($desiredMinSafeTrialsFraction)
  {
    $this->desiredMinSafeTrialsFraction = $desiredMinSafeTrialsFraction;
  }
  public function getDesiredMinSafeTrialsFraction()
  {
    return $this->desiredMinSafeTrialsFraction;
  }
  public function setSafetyThreshold($safetyThreshold)
  {
    $this->safetyThreshold = $safetyThreshold;
  }
  public function getSafetyThreshold()
  {
    return $this->safetyThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1StudySpecMetricSpecSafetyMetricConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1StudySpecMetricSpecSafetyMetricConfig');
