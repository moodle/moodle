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

class GoogleCloudAiplatformV1NasTrial extends \Google\Model
{
  /**
   * The NasTrial state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that a specific NasTrial has been requested, but it has not yet
   * been suggested by the service.
   */
  public const STATE_REQUESTED = 'REQUESTED';
  /**
   * Indicates that the NasTrial has been suggested.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Indicates that the NasTrial should stop according to the service.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * Indicates that the NasTrial is completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Indicates that the NasTrial should not be attempted again. The service will
   * set a NasTrial to INFEASIBLE when it's done but missing the
   * final_measurement.
   */
  public const STATE_INFEASIBLE = 'INFEASIBLE';
  /**
   * Output only. Time when the NasTrial's status changed to `SUCCEEDED` or
   * `INFEASIBLE`.
   *
   * @var string
   */
  public $endTime;
  protected $finalMeasurementType = GoogleCloudAiplatformV1Measurement::class;
  protected $finalMeasurementDataType = '';
  /**
   * Output only. The identifier of the NasTrial assigned by the service.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Time when the NasTrial was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the NasTrial.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Time when the NasTrial's status changed to `SUCCEEDED` or
   * `INFEASIBLE`.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The final measurement containing the objective value.
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
   * Output only. The identifier of the NasTrial assigned by the service.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Time when the NasTrial was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The detailed state of the NasTrial.
   *
   * Accepted values: STATE_UNSPECIFIED, REQUESTED, ACTIVE, STOPPING, SUCCEEDED,
   * INFEASIBLE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1NasTrial::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1NasTrial');
