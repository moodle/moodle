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

class CutoverStep extends \Google\Model
{
  /**
   * The time the step has ended.
   *
   * @var string
   */
  public $endTime;
  protected $finalSyncType = ReplicationCycle::class;
  protected $finalSyncDataType = '';
  protected $instantiatingMigratedVmType = InstantiatingMigratedVMStep::class;
  protected $instantiatingMigratedVmDataType = '';
  protected $preparingVmDisksType = PreparingVMDisksStep::class;
  protected $preparingVmDisksDataType = '';
  protected $previousReplicationCycleType = ReplicationCycle::class;
  protected $previousReplicationCycleDataType = '';
  protected $shuttingDownSourceVmType = ShuttingDownSourceVMStep::class;
  protected $shuttingDownSourceVmDataType = '';
  /**
   * The time the step has started.
   *
   * @var string
   */
  public $startTime;

  /**
   * The time the step has ended.
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
   * Final sync step.
   *
   * @param ReplicationCycle $finalSync
   */
  public function setFinalSync(ReplicationCycle $finalSync)
  {
    $this->finalSync = $finalSync;
  }
  /**
   * @return ReplicationCycle
   */
  public function getFinalSync()
  {
    return $this->finalSync;
  }
  /**
   * Instantiating migrated VM step.
   *
   * @param InstantiatingMigratedVMStep $instantiatingMigratedVm
   */
  public function setInstantiatingMigratedVm(InstantiatingMigratedVMStep $instantiatingMigratedVm)
  {
    $this->instantiatingMigratedVm = $instantiatingMigratedVm;
  }
  /**
   * @return InstantiatingMigratedVMStep
   */
  public function getInstantiatingMigratedVm()
  {
    return $this->instantiatingMigratedVm;
  }
  /**
   * Preparing VM disks step.
   *
   * @param PreparingVMDisksStep $preparingVmDisks
   */
  public function setPreparingVmDisks(PreparingVMDisksStep $preparingVmDisks)
  {
    $this->preparingVmDisks = $preparingVmDisks;
  }
  /**
   * @return PreparingVMDisksStep
   */
  public function getPreparingVmDisks()
  {
    return $this->preparingVmDisks;
  }
  /**
   * A replication cycle prior cutover step.
   *
   * @param ReplicationCycle $previousReplicationCycle
   */
  public function setPreviousReplicationCycle(ReplicationCycle $previousReplicationCycle)
  {
    $this->previousReplicationCycle = $previousReplicationCycle;
  }
  /**
   * @return ReplicationCycle
   */
  public function getPreviousReplicationCycle()
  {
    return $this->previousReplicationCycle;
  }
  /**
   * Shutting down VM step.
   *
   * @param ShuttingDownSourceVMStep $shuttingDownSourceVm
   */
  public function setShuttingDownSourceVm(ShuttingDownSourceVMStep $shuttingDownSourceVm)
  {
    $this->shuttingDownSourceVm = $shuttingDownSourceVm;
  }
  /**
   * @return ShuttingDownSourceVMStep
   */
  public function getShuttingDownSourceVm()
  {
    return $this->shuttingDownSourceVm;
  }
  /**
   * The time the step has started.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CutoverStep::class, 'Google_Service_VMMigrationService_CutoverStep');
