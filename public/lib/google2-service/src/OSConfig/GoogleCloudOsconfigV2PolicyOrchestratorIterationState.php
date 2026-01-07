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

namespace Google\Service\OSConfig;

class GoogleCloudOsconfigV2PolicyOrchestratorIterationState extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Iteration is in progress.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * Iteration completed, with all actions being successful.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * Iteration completed, with failures.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Iteration was explicitly cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Impossible to determine current state of the iteration.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. Number of orchestration actions which failed so far. For more
   * details, query the Cloud Logs.
   *
   * @var string
   */
  public $failedActions;
  /**
   * Output only. Finish time of the wave iteration.
   *
   * @var string
   */
  public $finishTime;
  /**
   * Output only. Unique identifier of the iteration.
   *
   * @var string
   */
  public $iterationId;
  /**
   * Output only. Overall number of actions done by the orchestrator so far.
   *
   * @var string
   */
  public $performedActions;
  /**
   * Output only. An estimated percentage of the progress. Number between 0 and
   * 100.
   *
   * @var float
   */
  public $progress;
  /**
   * Output only. Start time of the wave iteration.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. State of the iteration.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Error thrown in the wave iteration.
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
   * Output only. Number of orchestration actions which failed so far. For more
   * details, query the Cloud Logs.
   *
   * @param string $failedActions
   */
  public function setFailedActions($failedActions)
  {
    $this->failedActions = $failedActions;
  }
  /**
   * @return string
   */
  public function getFailedActions()
  {
    return $this->failedActions;
  }
  /**
   * Output only. Finish time of the wave iteration.
   *
   * @param string $finishTime
   */
  public function setFinishTime($finishTime)
  {
    $this->finishTime = $finishTime;
  }
  /**
   * @return string
   */
  public function getFinishTime()
  {
    return $this->finishTime;
  }
  /**
   * Output only. Unique identifier of the iteration.
   *
   * @param string $iterationId
   */
  public function setIterationId($iterationId)
  {
    $this->iterationId = $iterationId;
  }
  /**
   * @return string
   */
  public function getIterationId()
  {
    return $this->iterationId;
  }
  /**
   * Output only. Overall number of actions done by the orchestrator so far.
   *
   * @param string $performedActions
   */
  public function setPerformedActions($performedActions)
  {
    $this->performedActions = $performedActions;
  }
  /**
   * @return string
   */
  public function getPerformedActions()
  {
    return $this->performedActions;
  }
  /**
   * Output only. An estimated percentage of the progress. Number between 0 and
   * 100.
   *
   * @param float $progress
   */
  public function setProgress($progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return float
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Output only. Start time of the wave iteration.
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
   * Output only. State of the iteration.
   *
   * Accepted values: STATE_UNSPECIFIED, PROCESSING, COMPLETED, FAILED,
   * CANCELLED, UNKNOWN
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
class_alias(GoogleCloudOsconfigV2PolicyOrchestratorIterationState::class, 'Google_Service_OSConfig_GoogleCloudOsconfigV2PolicyOrchestratorIterationState');
