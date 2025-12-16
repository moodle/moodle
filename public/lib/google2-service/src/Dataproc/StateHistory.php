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

namespace Google\Service\Dataproc;

class StateHistory extends \Google\Model
{
  /**
   * The batch state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The batch is created before running.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The batch is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The batch is cancelling.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The batch cancellation was successful.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The batch completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The batch is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The state of the batch at this point in history.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Details about the state at this point in history.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. The time when the batch entered the historical state.
   *
   * @var string
   */
  public $stateStartTime;

  /**
   * Output only. The state of the batch at this point in history.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, RUNNING, CANCELLING,
   * CANCELLED, SUCCEEDED, FAILED
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
   * Output only. Details about the state at this point in history.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * Output only. The time when the batch entered the historical state.
   *
   * @param string $stateStartTime
   */
  public function setStateStartTime($stateStartTime)
  {
    $this->stateStartTime = $stateStartTime;
  }
  /**
   * @return string
   */
  public function getStateStartTime()
  {
    return $this->stateStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StateHistory::class, 'Google_Service_Dataproc_StateHistory');
