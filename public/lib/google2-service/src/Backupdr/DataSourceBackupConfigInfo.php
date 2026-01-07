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

namespace Google\Service\Backupdr;

class DataSourceBackupConfigInfo extends \Google\Model
{
  /**
   * Status not set.
   */
  public const LAST_BACKUP_STATE_LAST_BACKUP_STATE_UNSPECIFIED = 'LAST_BACKUP_STATE_UNSPECIFIED';
  /**
   * The first backup has not yet completed
   */
  public const LAST_BACKUP_STATE_FIRST_BACKUP_PENDING = 'FIRST_BACKUP_PENDING';
  /**
   * The most recent backup was successful
   */
  public const LAST_BACKUP_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The most recent backup failed
   */
  public const LAST_BACKUP_STATE_FAILED = 'FAILED';
  /**
   * The most recent backup could not be run/failed because of the lack of
   * permissions
   */
  public const LAST_BACKUP_STATE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * Output only. The status of the last backup in this DataSource
   *
   * @var string
   */
  public $lastBackupState;
  /**
   * Output only. Timestamp of the last successful backup to this DataSource.
   *
   * @var string
   */
  public $lastSuccessfulBackupConsistencyTime;

  /**
   * Output only. The status of the last backup in this DataSource
   *
   * Accepted values: LAST_BACKUP_STATE_UNSPECIFIED, FIRST_BACKUP_PENDING,
   * SUCCEEDED, FAILED, PERMISSION_DENIED
   *
   * @param self::LAST_BACKUP_STATE_* $lastBackupState
   */
  public function setLastBackupState($lastBackupState)
  {
    $this->lastBackupState = $lastBackupState;
  }
  /**
   * @return self::LAST_BACKUP_STATE_*
   */
  public function getLastBackupState()
  {
    return $this->lastBackupState;
  }
  /**
   * Output only. Timestamp of the last successful backup to this DataSource.
   *
   * @param string $lastSuccessfulBackupConsistencyTime
   */
  public function setLastSuccessfulBackupConsistencyTime($lastSuccessfulBackupConsistencyTime)
  {
    $this->lastSuccessfulBackupConsistencyTime = $lastSuccessfulBackupConsistencyTime;
  }
  /**
   * @return string
   */
  public function getLastSuccessfulBackupConsistencyTime()
  {
    return $this->lastSuccessfulBackupConsistencyTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSourceBackupConfigInfo::class, 'Google_Service_Backupdr_DataSourceBackupConfigInfo');
