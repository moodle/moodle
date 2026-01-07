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

namespace Google\Service\CloudDeploy;

class RepairRolloutOperation extends \Google\Collection
{
  protected $collection_key = 'repairPhases';
  /**
   * Output only. The index of the current repair action in the repair sequence.
   *
   * @var string
   */
  public $currentRepairPhaseIndex;
  /**
   * Output only. The job ID for the Job to repair.
   *
   * @var string
   */
  public $jobId;
  /**
   * Output only. The phase ID of the phase that includes the job being
   * repaired.
   *
   * @var string
   */
  public $phaseId;
  protected $repairPhasesType = RepairPhase::class;
  protected $repairPhasesDataType = 'array';
  /**
   * Output only. The name of the rollout that initiates the `AutomationRun`.
   *
   * @var string
   */
  public $rollout;

  /**
   * Output only. The index of the current repair action in the repair sequence.
   *
   * @param string $currentRepairPhaseIndex
   */
  public function setCurrentRepairPhaseIndex($currentRepairPhaseIndex)
  {
    $this->currentRepairPhaseIndex = $currentRepairPhaseIndex;
  }
  /**
   * @return string
   */
  public function getCurrentRepairPhaseIndex()
  {
    return $this->currentRepairPhaseIndex;
  }
  /**
   * Output only. The job ID for the Job to repair.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * Output only. The phase ID of the phase that includes the job being
   * repaired.
   *
   * @param string $phaseId
   */
  public function setPhaseId($phaseId)
  {
    $this->phaseId = $phaseId;
  }
  /**
   * @return string
   */
  public function getPhaseId()
  {
    return $this->phaseId;
  }
  /**
   * Output only. Records of the repair attempts. Each repair phase may have
   * multiple retry attempts or single rollback attempt.
   *
   * @param RepairPhase[] $repairPhases
   */
  public function setRepairPhases($repairPhases)
  {
    $this->repairPhases = $repairPhases;
  }
  /**
   * @return RepairPhase[]
   */
  public function getRepairPhases()
  {
    return $this->repairPhases;
  }
  /**
   * Output only. The name of the rollout that initiates the `AutomationRun`.
   *
   * @param string $rollout
   */
  public function setRollout($rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return string
   */
  public function getRollout()
  {
    return $this->rollout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepairRolloutOperation::class, 'Google_Service_CloudDeploy_RepairRolloutOperation');
