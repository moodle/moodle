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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1OsUpdateStatus extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const UPDATE_STATE_UPDATE_STATE_UNSPECIFIED = 'UPDATE_STATE_UNSPECIFIED';
  /**
   * OS has not started downloading.
   */
  public const UPDATE_STATE_OS_IMAGE_DOWNLOAD_NOT_STARTED = 'OS_IMAGE_DOWNLOAD_NOT_STARTED';
  /**
   * OS has started download on device.
   */
  public const UPDATE_STATE_OS_IMAGE_DOWNLOAD_IN_PROGRESS = 'OS_IMAGE_DOWNLOAD_IN_PROGRESS';
  /**
   * Device needs reboot to finish upload.
   */
  public const UPDATE_STATE_OS_UPDATE_NEED_REBOOT = 'OS_UPDATE_NEED_REBOOT';
  /**
   * Output only. Timestamp of the last reboot.
   *
   * @var string
   */
  public $lastRebootTime;
  /**
   * Output only. Timestamp of the last update check.
   *
   * @var string
   */
  public $lastUpdateCheckTime;
  /**
   * Output only. Timestamp of the last successful update.
   *
   * @var string
   */
  public $lastUpdateTime;
  /**
   * Output only. New platform version of the os image being downloaded and
   * applied. It is only set when update status is OS_IMAGE_DOWNLOAD_IN_PROGRESS
   * or OS_UPDATE_NEED_REBOOT. Note this could be a dummy "0.0.0.0" for
   * OS_UPDATE_NEED_REBOOT status for some edge cases, e.g. update engine is
   * restarted without a reboot.
   *
   * @var string
   */
  public $newPlatformVersion;
  /**
   * Output only. New requested platform version from the pending updated kiosk
   * app.
   *
   * @var string
   */
  public $newRequestedPlatformVersion;
  /**
   * Output only. Current state of the os update.
   *
   * @var string
   */
  public $updateState;

  /**
   * Output only. Timestamp of the last reboot.
   *
   * @param string $lastRebootTime
   */
  public function setLastRebootTime($lastRebootTime)
  {
    $this->lastRebootTime = $lastRebootTime;
  }
  /**
   * @return string
   */
  public function getLastRebootTime()
  {
    return $this->lastRebootTime;
  }
  /**
   * Output only. Timestamp of the last update check.
   *
   * @param string $lastUpdateCheckTime
   */
  public function setLastUpdateCheckTime($lastUpdateCheckTime)
  {
    $this->lastUpdateCheckTime = $lastUpdateCheckTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateCheckTime()
  {
    return $this->lastUpdateCheckTime;
  }
  /**
   * Output only. Timestamp of the last successful update.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Output only. New platform version of the os image being downloaded and
   * applied. It is only set when update status is OS_IMAGE_DOWNLOAD_IN_PROGRESS
   * or OS_UPDATE_NEED_REBOOT. Note this could be a dummy "0.0.0.0" for
   * OS_UPDATE_NEED_REBOOT status for some edge cases, e.g. update engine is
   * restarted without a reboot.
   *
   * @param string $newPlatformVersion
   */
  public function setNewPlatformVersion($newPlatformVersion)
  {
    $this->newPlatformVersion = $newPlatformVersion;
  }
  /**
   * @return string
   */
  public function getNewPlatformVersion()
  {
    return $this->newPlatformVersion;
  }
  /**
   * Output only. New requested platform version from the pending updated kiosk
   * app.
   *
   * @param string $newRequestedPlatformVersion
   */
  public function setNewRequestedPlatformVersion($newRequestedPlatformVersion)
  {
    $this->newRequestedPlatformVersion = $newRequestedPlatformVersion;
  }
  /**
   * @return string
   */
  public function getNewRequestedPlatformVersion()
  {
    return $this->newRequestedPlatformVersion;
  }
  /**
   * Output only. Current state of the os update.
   *
   * Accepted values: UPDATE_STATE_UNSPECIFIED, OS_IMAGE_DOWNLOAD_NOT_STARTED,
   * OS_IMAGE_DOWNLOAD_IN_PROGRESS, OS_UPDATE_NEED_REBOOT
   *
   * @param self::UPDATE_STATE_* $updateState
   */
  public function setUpdateState($updateState)
  {
    $this->updateState = $updateState;
  }
  /**
   * @return self::UPDATE_STATE_*
   */
  public function getUpdateState()
  {
    return $this->updateState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1OsUpdateStatus::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1OsUpdateStatus');
