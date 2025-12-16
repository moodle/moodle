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

namespace Google\Service\VMMigrationService;

class CloneJob extends \Google\Collection
{
  /**
   * The state is unknown. This is used for API compatibility only and is not
   * used by the system.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The clone job has not yet started.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The clone job is active and running.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The clone job finished with errors.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The clone job finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The clone job was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The clone job is being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * OS adaptation is running as part of the clone job to generate license.
   */
  public const STATE_ADAPTING_OS = 'ADAPTING_OS';
  protected $collection_key = 'steps';
  protected $computeEngineDisksTargetDetailsType = ComputeEngineDisksTargetDetails::class;
  protected $computeEngineDisksTargetDetailsDataType = '';
  protected $computeEngineTargetDetailsType = ComputeEngineTargetDetails::class;
  protected $computeEngineTargetDetailsDataType = '';
  /**
   * Output only. The time the clone job was created (as an API call, not when
   * it was actually created in the target).
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The time the clone job was ended.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The name of the clone.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. State of the clone job.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time the state was last updated.
   *
   * @var string
   */
  public $stateTime;
  protected $stepsType = CloneStep::class;
  protected $stepsDataType = 'array';

  /**
   * Output only. Details of the target Persistent Disks in Compute Engine.
   *
   * @param ComputeEngineDisksTargetDetails $computeEngineDisksTargetDetails
   */
  public function setComputeEngineDisksTargetDetails(ComputeEngineDisksTargetDetails $computeEngineDisksTargetDetails)
  {
    $this->computeEngineDisksTargetDetails = $computeEngineDisksTargetDetails;
  }
  /**
   * @return ComputeEngineDisksTargetDetails
   */
  public function getComputeEngineDisksTargetDetails()
  {
    return $this->computeEngineDisksTargetDetails;
  }
  /**
   * Output only. Details of the target VM in Compute Engine.
   *
   * @param ComputeEngineTargetDetails $computeEngineTargetDetails
   */
  public function setComputeEngineTargetDetails(ComputeEngineTargetDetails $computeEngineTargetDetails)
  {
    $this->computeEngineTargetDetails = $computeEngineTargetDetails;
  }
  /**
   * @return ComputeEngineTargetDetails
   */
  public function getComputeEngineTargetDetails()
  {
    return $this->computeEngineTargetDetails;
  }
  /**
   * Output only. The time the clone job was created (as an API call, not when
   * it was actually created in the target).
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The time the clone job was ended.
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
   * Output only. Provides details for the errors that led to the Clone Job's
   * state.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The name of the clone.
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
   * Output only. State of the clone job.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ACTIVE, FAILED, SUCCEEDED,
   * CANCELLED, CANCELLING, ADAPTING_OS
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
   * Output only. The time the state was last updated.
   *
   * @param string $stateTime
   */
  public function setStateTime($stateTime)
  {
    $this->stateTime = $stateTime;
  }
  /**
   * @return string
   */
  public function getStateTime()
  {
    return $this->stateTime;
  }
  /**
   * Output only. The clone steps list representing its progress.
   *
   * @param CloneStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return CloneStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloneJob::class, 'Google_Service_VMMigrationService_CloneJob');
