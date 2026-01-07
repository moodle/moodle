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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoTaskExecutionDetails extends \Google\Collection
{
  public const TASK_EXECUTION_STATE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Task is waiting for its precondition tasks to finish to start the
   * execution.
   */
  public const TASK_EXECUTION_STATE_PENDING_EXECUTION = 'PENDING_EXECUTION';
  /**
   * Task is under processing.
   */
  public const TASK_EXECUTION_STATE_IN_PROCESS = 'IN_PROCESS';
  /**
   * Task execution successfully finished. There's no more change after this
   * state.
   */
  public const TASK_EXECUTION_STATE_SUCCEED = 'SUCCEED';
  /**
   * Task execution failed. There's no more change after this state.
   */
  public const TASK_EXECUTION_STATE_FAILED = 'FAILED';
  /**
   * Task execution failed and cause the whole event execution to fail
   * immediately. There's no more change after this state.
   */
  public const TASK_EXECUTION_STATE_FATAL = 'FATAL';
  /**
   * Task execution failed and waiting for retry.
   */
  public const TASK_EXECUTION_STATE_RETRY_ON_HOLD = 'RETRY_ON_HOLD';
  /**
   * Task execution skipped. This happens when its precondition wasn't met, or
   * the event execution been canceled before reach to the task. There's no more
   * changes after this state.
   */
  public const TASK_EXECUTION_STATE_SKIPPED = 'SKIPPED';
  /**
   * Task execution canceled when in progress. This happens when event execution
   * been canceled or any other task fall in fatal state.
   */
  public const TASK_EXECUTION_STATE_CANCELED = 'CANCELED';
  /**
   * Task is waiting for its dependency tasks' rollback to finish to start its
   * rollback.
   */
  public const TASK_EXECUTION_STATE_PENDING_ROLLBACK = 'PENDING_ROLLBACK';
  /**
   * Task is rolling back.
   */
  public const TASK_EXECUTION_STATE_ROLLBACK_IN_PROCESS = 'ROLLBACK_IN_PROCESS';
  /**
   * Task is rolled back. This is the state we will set regardless of rollback
   * succeeding or failing.
   */
  public const TASK_EXECUTION_STATE_ROLLEDBACK = 'ROLLEDBACK';
  /**
   * Task is a SuspensionTask which has executed once, creating a pending
   * suspension.
   */
  public const TASK_EXECUTION_STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'taskAttemptStats';
  /**
   * Indicates whether the task was skipped on failure. Only relevant if the
   * task is in SKIPPED state.
   *
   * @var bool
   */
  public $skippedOnFailure;
  protected $taskAttemptStatsType = EnterpriseCrmEventbusProtoTaskExecutionDetailsTaskAttemptStats::class;
  protected $taskAttemptStatsDataType = 'array';
  /**
   * @var string
   */
  public $taskExecutionState;
  /**
   * Pointer to the task config it used for execution.
   *
   * @var string
   */
  public $taskNumber;

  /**
   * Indicates whether the task was skipped on failure. Only relevant if the
   * task is in SKIPPED state.
   *
   * @param bool $skippedOnFailure
   */
  public function setSkippedOnFailure($skippedOnFailure)
  {
    $this->skippedOnFailure = $skippedOnFailure;
  }
  /**
   * @return bool
   */
  public function getSkippedOnFailure()
  {
    return $this->skippedOnFailure;
  }
  /**
   * @param EnterpriseCrmEventbusProtoTaskExecutionDetailsTaskAttemptStats[] $taskAttemptStats
   */
  public function setTaskAttemptStats($taskAttemptStats)
  {
    $this->taskAttemptStats = $taskAttemptStats;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskExecutionDetailsTaskAttemptStats[]
   */
  public function getTaskAttemptStats()
  {
    return $this->taskAttemptStats;
  }
  /**
   * @param self::TASK_EXECUTION_STATE_* $taskExecutionState
   */
  public function setTaskExecutionState($taskExecutionState)
  {
    $this->taskExecutionState = $taskExecutionState;
  }
  /**
   * @return self::TASK_EXECUTION_STATE_*
   */
  public function getTaskExecutionState()
  {
    return $this->taskExecutionState;
  }
  /**
   * Pointer to the task config it used for execution.
   *
   * @param string $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return string
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoTaskExecutionDetails::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoTaskExecutionDetails');
