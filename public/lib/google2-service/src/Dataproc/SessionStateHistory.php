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

class SessionStateHistory extends \Google\Model
{
  /**
   * The session state is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The session is created prior to running.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The session is running.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The session is terminating.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * The session is terminated successfully.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * The session is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The state of the session at this point in the session history.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Details about the state at this point in the session history.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * Output only. The time when the session entered the historical state.
   *
   * @var string
   */
  public $stateStartTime;

  /**
   * Output only. The state of the session at this point in the session history.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, TERMINATING,
   * TERMINATED, FAILED
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
   * Output only. Details about the state at this point in the session history.
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
   * Output only. The time when the session entered the historical state.
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
class_alias(SessionStateHistory::class, 'Google_Service_Dataproc_SessionStateHistory');
