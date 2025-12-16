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

namespace Google\Service\WorkloadManager;

class BackupProperties extends \Google\Model
{
  /**
   * unspecified
   */
  public const LATEST_BACKUP_STATUS_BACKUP_STATE_UNSPECIFIED = 'BACKUP_STATE_UNSPECIFIED';
  /**
   * SUCCESS state
   */
  public const LATEST_BACKUP_STATUS_BACKUP_STATE_SUCCESS = 'BACKUP_STATE_SUCCESS';
  /**
   * FAILURE state
   */
  public const LATEST_BACKUP_STATUS_BACKUP_STATE_FAILURE = 'BACKUP_STATE_FAILURE';
  /**
   * Output only. The state of the latest backup.
   *
   * @var string
   */
  public $latestBackupStatus;
  /**
   * The time when the latest backup was performed.
   *
   * @var string
   */
  public $latestBackupTime;

  /**
   * Output only. The state of the latest backup.
   *
   * Accepted values: BACKUP_STATE_UNSPECIFIED, BACKUP_STATE_SUCCESS,
   * BACKUP_STATE_FAILURE
   *
   * @param self::LATEST_BACKUP_STATUS_* $latestBackupStatus
   */
  public function setLatestBackupStatus($latestBackupStatus)
  {
    $this->latestBackupStatus = $latestBackupStatus;
  }
  /**
   * @return self::LATEST_BACKUP_STATUS_*
   */
  public function getLatestBackupStatus()
  {
    return $this->latestBackupStatus;
  }
  /**
   * The time when the latest backup was performed.
   *
   * @param string $latestBackupTime
   */
  public function setLatestBackupTime($latestBackupTime)
  {
    $this->latestBackupTime = $latestBackupTime;
  }
  /**
   * @return string
   */
  public function getLatestBackupTime()
  {
    return $this->latestBackupTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupProperties::class, 'Google_Service_WorkloadManager_BackupProperties');
