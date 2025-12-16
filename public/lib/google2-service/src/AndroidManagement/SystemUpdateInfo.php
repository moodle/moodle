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

namespace Google\Service\AndroidManagement;

class SystemUpdateInfo extends \Google\Model
{
  /**
   * It is unknown whether there is a pending system update. This happens when,
   * for example, the device API level is less than 26, or if the version of
   * Android Device Policy is outdated.
   */
  public const UPDATE_STATUS_UPDATE_STATUS_UNKNOWN = 'UPDATE_STATUS_UNKNOWN';
  /**
   * There is no pending system update available on the device.
   */
  public const UPDATE_STATUS_UP_TO_DATE = 'UP_TO_DATE';
  /**
   * There is a pending system update available, but its type is not known.
   */
  public const UPDATE_STATUS_UNKNOWN_UPDATE_AVAILABLE = 'UNKNOWN_UPDATE_AVAILABLE';
  /**
   * There is a pending security update available.
   */
  public const UPDATE_STATUS_SECURITY_UPDATE_AVAILABLE = 'SECURITY_UPDATE_AVAILABLE';
  /**
   * There is a pending OS update available.
   */
  public const UPDATE_STATUS_OS_UPDATE_AVAILABLE = 'OS_UPDATE_AVAILABLE';
  /**
   * The time when the update was first available. A zero value indicates that
   * this field is not set. This field is set only if an update is available
   * (that is, updateStatus is neither UPDATE_STATUS_UNKNOWN nor UP_TO_DATE).
   *
   * @var string
   */
  public $updateReceivedTime;
  /**
   * The status of an update: whether an update exists and what type it is.
   *
   * @var string
   */
  public $updateStatus;

  /**
   * The time when the update was first available. A zero value indicates that
   * this field is not set. This field is set only if an update is available
   * (that is, updateStatus is neither UPDATE_STATUS_UNKNOWN nor UP_TO_DATE).
   *
   * @param string $updateReceivedTime
   */
  public function setUpdateReceivedTime($updateReceivedTime)
  {
    $this->updateReceivedTime = $updateReceivedTime;
  }
  /**
   * @return string
   */
  public function getUpdateReceivedTime()
  {
    return $this->updateReceivedTime;
  }
  /**
   * The status of an update: whether an update exists and what type it is.
   *
   * Accepted values: UPDATE_STATUS_UNKNOWN, UP_TO_DATE,
   * UNKNOWN_UPDATE_AVAILABLE, SECURITY_UPDATE_AVAILABLE, OS_UPDATE_AVAILABLE
   *
   * @param self::UPDATE_STATUS_* $updateStatus
   */
  public function setUpdateStatus($updateStatus)
  {
    $this->updateStatus = $updateStatus;
  }
  /**
   * @return self::UPDATE_STATUS_*
   */
  public function getUpdateStatus()
  {
    return $this->updateStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SystemUpdateInfo::class, 'Google_Service_AndroidManagement_SystemUpdateInfo');
