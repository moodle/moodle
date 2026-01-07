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

class GoogleCloudAiplatformV1Trial extends \Google\Collection
{
  /**
   * The Trial state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that a specific Trial has been requested, but it has not yet been
   * suggested by the service.
   */
  public const STATE_REQUESTED = 'REQUESTED';
  /**
   * Indicates that the Trial has been suggested.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Indicates that the Trial should stop according to the service.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * Indicates that the Trial is completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Indicates that the Trial should not be attempted again. The service will
   * set a Trial to INFEASIBLE when it's done but missing the final_measurement.
   */
  public const STATE_INFEASIBLE = 'INFEASIBLE';
  protected $collection_key = 'parameters';
  /**
   * Output only. The identifier of the client that originally requested this
   * Trial. Each client is identified by a unique client_id. When a client asks
   * for a suggestion, Vertex AI Vizier will assign it a Trial. The client
   * should evaluate the Trial, complete it, and report back to Vertex AI
   * Vizier. If suggestion is asked again by same client_id before the Trial is
   * completed, the same Trial will be returned. Multiple clients with different
   * client_ids can ask for suggestions simultaneously, each of them will get
   * their own Trial.
   *
   * @var string
   */
  public $clientId;
  /**
   * Output only. The CustomJob name linked to the Trial. It's set for a
   * HyperparameterTuningJob's Trial.
   *
   * @var string
   */
  public $customJob;
  /**
   * Output only. Time when the Trial's status changed to `SUCCEEDED` or
   * `INFEASIBLE`.
   *
   * @var string
   */
  public $endTime;
  protected $finalMeasurementType = GoogleCloudAiplatformV1Measurement::class;
  protected $finalMeasurementDataType = '';
  /**
   * Output only. The identifier of the Trial assigned by the service.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. A human readable string describing why the Trial is
   * infeasible. This is set only if Trial state is `INFEASIBLE`.
   *
   * @var string
   */
  public $infeasibleReason;
  protected $measurementsType = GoogleCloudAiplatformV1Measurement::class;
  protected $measurementsDataType = 'array';
  /**
   * Output only. Resource name of the Trial assigned by the service.
   *
   * @var string
   */
  public $name;
  protected $parametersType = GoogleCloudAiplatformV1TrialParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Output only. Time when the Trial was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The detailed state of the Trial.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if this
   * trial is part of a HyperparameterTuningJob and the job's
   * trial_job_spec.enable_web_access field is `true`. The keys are names of
   * each node used for the trial; for example, `workerpool0-0` for the primary
   * node, `workerpool1-0` for the first node in the second worker pool, and
   * `workerpool1-1` for the second node in the second worker pool. The values
   * are the URIs for each node's interactive shell.
   *
   * @var string[]
   */
  public $webAccessUris;

  /**
   * Output only. The identifier of the client that originally requested this
   * Trial. Each client is identified by a unique client_id. When a client asks
   * for a suggestion, Vertex AI Vizier will assign it a Trial. The client
   * should evaluate the Trial, complete it, and report back to Vertex AI
   * Vizier. If suggestion is asked again by same client_id before the Trial is
   * completed, the same Trial will be returned. Multiple clients with different
   * client_ids can ask for suggestions simultaneously, each of them will get
   * their own Trial.
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
   * Output only. The CustomJob name linked to the Trial. It's set for a
   * HyperparameterTuningJob's Trial.
   *
   * @param string $customJob
   */
  public function setCustomJob($customJob)
  {
    $this->customJob = $customJob;
  }
  /**
   * @return string
   */
  public function getCustomJob()
  {
    return $this->customJob;
  }
  /**
   * Output only. Time when the Trial's status changed to `SUCCEEDED` or
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
   * Output only. The identifier of the Trial assigned by the service.
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
   * Output only. A human readable string describing why the Trial is
   * infeasible. This is set only if Trial state is `INFEASIBLE`.
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
   * Output only. A list of measurements that are strictly lexicographically
   * ordered by their induced tuples (steps, elapsed_duration). These are used
   * for early stopping computations.
   *
   * @param GoogleCloudAiplatformV1Measurement[] $measurements
   */
  public function setMeasurements($measurements)
  {
    $this->measurements = $measurements;
  }
  /**
   * @return GoogleCloudAiplatformV1Measurement[]
   */
  public function getMeasurements()
  {
    return $this->measurements;
  }
  /**
   * Output only. Resource name of the Trial assigned by the service.
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
   * Output only. The parameters of the Trial.
   *
   * @param GoogleCloudAiplatformV1TrialParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudAiplatformV1TrialParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Output only. Time when the Trial was started.
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
   * Output only. The detailed state of the Trial.
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
  /**
   * Output only. URIs for accessing [interactive
   * shells](https://cloud.google.com/vertex-ai/docs/training/monitor-debug-
   * interactive-shell) (one URI for each training node). Only available if this
   * trial is part of a HyperparameterTuningJob and the job's
   * trial_job_spec.enable_web_access field is `true`. The keys are names of
   * each node used for the trial; for example, `workerpool0-0` for the primary
   * node, `workerpool1-0` for the first node in the second worker pool, and
   * `workerpool1-1` for the second node in the second worker pool. The values
   * are the URIs for each node's interactive shell.
   *
   * @param string[] $webAccessUris
   */
  public function setWebAccessUris($webAccessUris)
  {
    $this->webAccessUris = $webAccessUris;
  }
  /**
   * @return string[]
   */
  public function getWebAccessUris()
  {
    return $this->webAccessUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Trial::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Trial');
