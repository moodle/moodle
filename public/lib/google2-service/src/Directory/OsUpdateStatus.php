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

namespace Google\Service\Directory;

class OsUpdateStatus extends \Google\Model
{
  /**
   * The update state is unspecified.
   */
  public const STATE_updateStateUnspecified = 'updateStateUnspecified';
  /**
   * There is an update pending but it hasn't started.
   */
  public const STATE_updateStateNotStarted = 'updateStateNotStarted';
  /**
   * The pending update is being downloaded.
   */
  public const STATE_updateStateDownloadInProgress = 'updateStateDownloadInProgress';
  /**
   * The device is ready to install the update, but must reboot.
   */
  public const STATE_updateStateNeedReboot = 'updateStateNeedReboot';
  /**
   * Date and time of the last reboot.
   *
   * @var string
   */
  public $rebootTime;
  /**
   * The update state of an OS update.
   *
   * @var string
   */
  public $state;
  /**
   * New required platform version from the pending updated kiosk app.
   *
   * @var string
   */
  public $targetKioskAppVersion;
  /**
   * New platform version of the OS image being downloaded and applied. It is
   * only set when update status is UPDATE_STATUS_DOWNLOAD_IN_PROGRESS or
   * UPDATE_STATUS_NEED_REBOOT. Note this could be a dummy "0.0.0.0" for
   * UPDATE_STATUS_NEED_REBOOT for some edge cases, e.g. update engine is
   * restarted without a reboot.
   *
   * @var string
   */
  public $targetOsVersion;
  /**
   * Date and time of the last update check.
   *
   * @var string
   */
  public $updateCheckTime;
  /**
   * Date and time of the last successful OS update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Date and time of the last reboot.
   *
   * @param string $rebootTime
   */
  public function setRebootTime($rebootTime)
  {
    $this->rebootTime = $rebootTime;
  }
  /**
   * @return string
   */
  public function getRebootTime()
  {
    return $this->rebootTime;
  }
  /**
   * The update state of an OS update.
   *
   * Accepted values: updateStateUnspecified, updateStateNotStarted,
   * updateStateDownloadInProgress, updateStateNeedReboot
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
   * New required platform version from the pending updated kiosk app.
   *
   * @param string $targetKioskAppVersion
   */
  public function setTargetKioskAppVersion($targetKioskAppVersion)
  {
    $this->targetKioskAppVersion = $targetKioskAppVersion;
  }
  /**
   * @return string
   */
  public function getTargetKioskAppVersion()
  {
    return $this->targetKioskAppVersion;
  }
  /**
   * New platform version of the OS image being downloaded and applied. It is
   * only set when update status is UPDATE_STATUS_DOWNLOAD_IN_PROGRESS or
   * UPDATE_STATUS_NEED_REBOOT. Note this could be a dummy "0.0.0.0" for
   * UPDATE_STATUS_NEED_REBOOT for some edge cases, e.g. update engine is
   * restarted without a reboot.
   *
   * @param string $targetOsVersion
   */
  public function setTargetOsVersion($targetOsVersion)
  {
    $this->targetOsVersion = $targetOsVersion;
  }
  /**
   * @return string
   */
  public function getTargetOsVersion()
  {
    return $this->targetOsVersion;
  }
  /**
   * Date and time of the last update check.
   *
   * @param string $updateCheckTime
   */
  public function setUpdateCheckTime($updateCheckTime)
  {
    $this->updateCheckTime = $updateCheckTime;
  }
  /**
   * @return string
   */
  public function getUpdateCheckTime()
  {
    return $this->updateCheckTime;
  }
  /**
   * Date and time of the last successful OS update.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OsUpdateStatus::class, 'Google_Service_Directory_OsUpdateStatus');
