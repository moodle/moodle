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

namespace Google\Service\Testing;

class SessionStateEvent extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SESSION_STATE_SESSION_STATE_UNSPECIFIED = 'SESSION_STATE_UNSPECIFIED';
  /**
   * Initial state of a session request. The session is being validated for
   * correctness and a device is not yet requested.
   */
  public const SESSION_STATE_REQUESTED = 'REQUESTED';
  /**
   * The session has been validated and is in the queue for a device.
   */
  public const SESSION_STATE_PENDING = 'PENDING';
  /**
   * The session has been granted and the device is accepting connections.
   */
  public const SESSION_STATE_ACTIVE = 'ACTIVE';
  /**
   * The session duration exceeded the device’s reservation time period and
   * timed out automatically.
   */
  public const SESSION_STATE_EXPIRED = 'EXPIRED';
  /**
   * The user is finished with the session and it was canceled by the user while
   * the request was still getting allocated or after allocation and during
   * device usage period.
   */
  public const SESSION_STATE_FINISHED = 'FINISHED';
  /**
   * Unable to complete the session because the device was unavailable and it
   * failed to allocate through the scheduler. For example, a device not in the
   * catalog was requested or the request expired in the allocation queue.
   */
  public const SESSION_STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Unable to complete the session for an internal reason, such as an
   * infrastructure failure.
   */
  public const SESSION_STATE_ERROR = 'ERROR';
  /**
   * Output only. The time that the session_state first encountered that state.
   *
   * @var string
   */
  public $eventTime;
  /**
   * Output only. The session_state tracked by this event
   *
   * @var string
   */
  public $sessionState;
  /**
   * Output only. A human-readable message to explain the state.
   *
   * @var string
   */
  public $stateMessage;

  /**
   * Output only. The time that the session_state first encountered that state.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * Output only. The session_state tracked by this event
   *
   * Accepted values: SESSION_STATE_UNSPECIFIED, REQUESTED, PENDING, ACTIVE,
   * EXPIRED, FINISHED, UNAVAILABLE, ERROR
   *
   * @param self::SESSION_STATE_* $sessionState
   */
  public function setSessionState($sessionState)
  {
    $this->sessionState = $sessionState;
  }
  /**
   * @return self::SESSION_STATE_*
   */
  public function getSessionState()
  {
    return $this->sessionState;
  }
  /**
   * Output only. A human-readable message to explain the state.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SessionStateEvent::class, 'Google_Service_Testing_SessionStateEvent');
