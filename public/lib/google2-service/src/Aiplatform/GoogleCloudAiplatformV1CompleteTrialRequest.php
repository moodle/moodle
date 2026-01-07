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

class GoogleCloudAiplatformV1CompleteTrialRequest extends \Google\Model
{
  protected $finalMeasurementType = GoogleCloudAiplatformV1Measurement::class;
  protected $finalMeasurementDataType = '';
  /**
   * Optional. A human readable reason why the trial was infeasible. This should
   * only be provided if `trial_infeasible` is true.
   *
   * @var string
   */
  public $infeasibleReason;
  /**
   * Optional. True if the Trial cannot be run with the given Parameter, and
   * final_measurement will be ignored.
   *
   * @var bool
   */
  public $trialInfeasible;

  /**
   * Optional. If provided, it will be used as the completed Trial's
   * final_measurement; Otherwise, the service will auto-select a previously
   * reported measurement as the final-measurement
   *
   * @param GoogleCloudAiplatformV1Measurement $finalMeasurement
   */
  public function setFinalMeasurement(GoogleCloudAiplatformV1Measurement $finalMeasurement)
  {
    $this->finalMeasurement = $finalMeasurement;
  }
  /**
   * @return GoogleCloudAiplatformV1Measurement
   */
  public function getFinalMeasurement()
  {
    return $this->finalMeasurement;
  }
  /**
   * Optional. A human readable reason why the trial was infeasible. This should
   * only be provided if `trial_infeasible` is true.
   *
   * @param string $infeasibleReason
   */
  public function setInfeasibleReason($infeasibleReason)
  {
    $this->infeasibleReason = $infeasibleReason;
  }
  /**
   * @return string
   */
  public function getInfeasibleReason()
  {
    return $this->infeasibleReason;
  }
  /**
   * Optional. True if the Trial cannot be run with the given Parameter, and
   * final_measurement will be ignored.
   *
   * @param bool $trialInfeasible
   */
  public function setTrialInfeasible($trialInfeasible)
  {
    $this->trialInfeasible = $trialInfeasible;
  }
  /**
   * @return bool
   */
  public function getTrialInfeasible()
  {
    return $this->trialInfeasible;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CompleteTrialRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CompleteTrialRequest');
