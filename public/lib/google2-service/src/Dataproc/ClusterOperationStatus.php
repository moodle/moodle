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

class ClusterOperationStatus extends \Google\Model
{
  /**
   * Unused.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The operation has been created.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The operation is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The operation is done; either cancelled or completed.
   */
  public const STATE_DONE = 'DONE';
  /**
   * Output only. A message containing any operation metadata details.
   *
   * @var string
   */
  public $details;
  /**
   * Output only. A message containing the detailed operation state.
   *
   * @var string
   */
  public $innerState;
  /**
   * Output only. A message containing the operation state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time this state was entered.
   *
   * @var string
   */
  public $stateStartTime;

  /**
   * Output only. A message containing any operation metadata details.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Output only. A message containing the detailed operation state.
   *
   * @param string $innerState
   */
  public function setInnerState($innerState)
  {
    $this->innerState = $innerState;
  }
  /**
   * @return string
   */
  public function getInnerState()
  {
    return $this->innerState;
  }
  /**
   * Output only. A message containing the operation state.
   *
   * Accepted values: UNKNOWN, PENDING, RUNNING, DONE
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
   * Output only. The time this state was entered.
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
class_alias(ClusterOperationStatus::class, 'Google_Service_Dataproc_ClusterOperationStatus');
