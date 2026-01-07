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

class DeviceSession extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_SESSION_STATE_UNSPECIFIED = 'SESSION_STATE_UNSPECIFIED';
  /**
   * Initial state of a session request. The session is being validated for
   * correctness and a device is not yet requested.
   */
  public const STATE_REQUESTED = 'REQUESTED';
  /**
   * The session has been validated and is in the queue for a device.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The session has been granted and the device is accepting connections.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The session duration exceeded the device’s reservation time period and
   * timed out automatically.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * The user is finished with the session and it was canceled by the user while
   * the request was still getting allocated or after allocation and during
   * device usage period.
   */
  public const STATE_FINISHED = 'FINISHED';
  /**
   * Unable to complete the session because the device was unavailable and it
   * failed to allocate through the scheduler. For example, a device not in the
   * catalog was requested or the request expired in the allocation queue.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Unable to complete the session for an internal reason, such as an
   * infrastructure failure.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'stateHistories';
  /**
   * Output only. The timestamp that the session first became ACTIVE.
   *
   * @var string
   */
  public $activeStartTime;
  protected $androidDeviceType = AndroidDevice::class;
  protected $androidDeviceDataType = '';
  /**
   * Output only. The time that the Session was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The title of the DeviceSession to be presented in the UI.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. If the device is still in use at this time, any connections will
   * be ended and the SessionState will transition from ACTIVE to FINISHED.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The interval of time that this device must be interacted with
   * before it transitions from ACTIVE to TIMEOUT_INACTIVITY.
   *
   * @var string
   */
  public $inactivityTimeout;
  /**
   * Optional. Name of the DeviceSession, e.g.
   * "projects/{project_id}/deviceSessions/{session_id}"
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Current state of the DeviceSession.
   *
   * @var string
   */
  public $state;
  protected $stateHistoriesType = SessionStateEvent::class;
  protected $stateHistoriesDataType = 'array';
  /**
   * Optional. The amount of time that a device will be initially allocated for.
   * This can eventually be extended with the UpdateDeviceSession RPC. Default:
   * 15 minutes.
   *
   * @var string
   */
  public $ttl;

  /**
   * Output only. The timestamp that the session first became ACTIVE.
   *
   * @param string $activeStartTime
   */
  public function setActiveStartTime($activeStartTime)
  {
    $this->activeStartTime = $activeStartTime;
  }
  /**
   * @return string
   */
  public function getActiveStartTime()
  {
    return $this->activeStartTime;
  }
  /**
   * Required. The requested device
   *
   * @param AndroidDevice $androidDevice
   */
  public function setAndroidDevice(AndroidDevice $androidDevice)
  {
    $this->androidDevice = $androidDevice;
  }
  /**
   * @return AndroidDevice
   */
  public function getAndroidDevice()
  {
    return $this->androidDevice;
  }
  /**
   * Output only. The time that the Session was created.
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
   * Output only. The title of the DeviceSession to be presented in the UI.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. If the device is still in use at this time, any connections will
   * be ended and the SessionState will transition from ACTIVE to FINISHED.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The interval of time that this device must be interacted with
   * before it transitions from ACTIVE to TIMEOUT_INACTIVITY.
   *
   * @param string $inactivityTimeout
   */
  public function setInactivityTimeout($inactivityTimeout)
  {
    $this->inactivityTimeout = $inactivityTimeout;
  }
  /**
   * @return string
   */
  public function getInactivityTimeout()
  {
    return $this->inactivityTimeout;
  }
  /**
   * Optional. Name of the DeviceSession, e.g.
   * "projects/{project_id}/deviceSessions/{session_id}"
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
   * Output only. Current state of the DeviceSession.
   *
   * Accepted values: SESSION_STATE_UNSPECIFIED, REQUESTED, PENDING, ACTIVE,
   * EXPIRED, FINISHED, UNAVAILABLE, ERROR
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
   * Output only. The historical state transitions of the session_state message
   * including the current session state.
   *
   * @param SessionStateEvent[] $stateHistories
   */
  public function setStateHistories($stateHistories)
  {
    $this->stateHistories = $stateHistories;
  }
  /**
   * @return SessionStateEvent[]
   */
  public function getStateHistories()
  {
    return $this->stateHistories;
  }
  /**
   * Optional. The amount of time that a device will be initially allocated for.
   * This can eventually be extended with the UpdateDeviceSession RPC. Default:
   * 15 minutes.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceSession::class, 'Google_Service_Testing_DeviceSession');
