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

class ReplicationCycle extends \Google\Collection
{
  /**
   * The state is unknown. This is used for API compatibility only and is not
   * used by the system.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The replication cycle is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The replication cycle is paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * The replication cycle finished with errors.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The replication cycle finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  protected $collection_key = 'warnings';
  /**
   * The cycle's ordinal number.
   *
   * @var int
   */
  public $cycleNumber;
  /**
   * The time the replication cycle has ended.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The identifier of the ReplicationCycle.
   *
   * @var string
   */
  public $name;
  /**
   * The current progress in percentage of this cycle. Was replaced by 'steps'
   * field, which breaks down the cycle progression more accurately.
   *
   * @deprecated
   * @var int
   */
  public $progressPercent;
  /**
   * The time the replication cycle has started.
   *
   * @var string
   */
  public $startTime;
  /**
   * State of the ReplicationCycle.
   *
   * @var string
   */
  public $state;
  protected $stepsType = CycleStep::class;
  protected $stepsDataType = 'array';
  /**
   * The accumulated duration the replication cycle was paused.
   *
   * @var string
   */
  public $totalPauseDuration;
  protected $warningsType = MigrationWarning::class;
  protected $warningsDataType = 'array';

  /**
   * The cycle's ordinal number.
   *
   * @param int $cycleNumber
   */
  public function setCycleNumber($cycleNumber)
  {
    $this->cycleNumber = $cycleNumber;
  }
  /**
   * @return int
   */
  public function getCycleNumber()
  {
    return $this->cycleNumber;
  }
  /**
   * The time the replication cycle has ended.
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
   * Output only. Provides details on the state of the cycle in case of an
   * error.
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
   * The identifier of the ReplicationCycle.
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
   * The current progress in percentage of this cycle. Was replaced by 'steps'
   * field, which breaks down the cycle progression more accurately.
   *
   * @deprecated
   * @param int $progressPercent
   */
  public function setProgressPercent($progressPercent)
  {
    $this->progressPercent = $progressPercent;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getProgressPercent()
  {
    return $this->progressPercent;
  }
  /**
   * The time the replication cycle has started.
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
   * State of the ReplicationCycle.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, PAUSED, FAILED, SUCCEEDED
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
   * The cycle's steps list representing its progress.
   *
   * @param CycleStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return CycleStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
  /**
   * The accumulated duration the replication cycle was paused.
   *
   * @param string $totalPauseDuration
   */
  public function setTotalPauseDuration($totalPauseDuration)
  {
    $this->totalPauseDuration = $totalPauseDuration;
  }
  /**
   * @return string
   */
  public function getTotalPauseDuration()
  {
    return $this->totalPauseDuration;
  }
  /**
   * Output only. Warnings that occurred during the cycle.
   *
   * @param MigrationWarning[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return MigrationWarning[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicationCycle::class, 'Google_Service_VMMigrationService_ReplicationCycle');
