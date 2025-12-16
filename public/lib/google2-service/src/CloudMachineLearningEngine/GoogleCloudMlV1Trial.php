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

class GoogleCloudMlV1Trial extends \Google\Collection
{
  /**
   * The trial state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that a specific trial has been requested, but it has not yet been
   * suggested by the service.
   */
  public const STATE_REQUESTED = 'REQUESTED';
  /**
   * Indicates that the trial has been suggested.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Indicates that the trial is done, and either has a final_measurement set,
   * or is marked as trial_infeasible.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Indicates that the trial should stop according to the service.
   */
  public const STATE_STOPPING = 'STOPPING';
  protected $collection_key = 'parameters';
  /**
   * Output only. The identifier of the client that originally requested this
   * trial.
   *
   * @var string
   */
  public $clientId;
  /**
   * Output only. Time at which the trial's status changed to COMPLETED.
   *
   * @var string
   */
  public $endTime;
  protected $finalMeasurementType = GoogleCloudMlV1Measurement::class;
  protected $finalMeasurementDataType = '';
  /**
   * Output only. A human readable string describing why the trial is
   * infeasible. This should only be set if trial_infeasible is true.
   *
   * @var string
   */
  public $infeasibleReason;
  protected $measurementsType = GoogleCloudMlV1Measurement::class;
  protected $measurementsDataType = 'array';
  /**
   * Output only. Name of the trial assigned by the service.
   *
   * @var string
   */
  public $name;
  protected $parametersType = GoogleCloudMlV1TrialParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Output only. Time at which the trial was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The detailed state of a trial.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. If true, the parameters in this trial are not attempted again.
   *
   * @var bool
   */
  public $trialInfeasible;

  /**
   * Output only. The identifier of the client that originally requested this
   * trial.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Output only. Time at which the trial's status changed to COMPLETED.
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
   * The final measurement containing the objective value.
   *
   * @param GoogleCloudMlV1Measurement $finalMeasurement
   */
  public function setFinalMeasurement(GoogleCloudMlV1Measurement $finalMeasurement)
  {
    $this->finalMeasurement = $finalMeasurement;
  }
  /**
   * @return GoogleCloudMlV1Measurement
   */
  public function getFinalMeasurement()
  {
    return $this->finalMeasurement;
  }
  /**
   * Output only. A human readable string describing why the trial is
   * infeasible. This should only be set if trial_infeasible is true.
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
   * A list of measurements that are strictly lexicographically ordered by their
   * induced tuples (steps, elapsed_time). These are used for early stopping
   * computations.
   *
   * @param GoogleCloudMlV1Measurement[] $measurements
   */
  public function setMeasurements($measurements)
  {
    $this->measurements = $measurements;
  }
  /**
   * @return GoogleCloudMlV1Measurement[]
   */
  public function getMeasurements()
  {
    return $this->measurements;
  }
  /**
   * Output only. Name of the trial assigned by the service.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The parameters of the trial.
   *
   * @param GoogleCloudMlV1TrialParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudMlV1TrialParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Output only. Time at which the trial was started.
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
   * The detailed state of a trial.
   *
   * Accepted values: STATE_UNSPECIFIED, REQUESTED, ACTIVE, COMPLETED, STOPPING
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
  /**
   * Output only. If true, the parameters in this trial are not attempted again.
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
class_alias(GoogleCloudMlV1Trial::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Trial');
