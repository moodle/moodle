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

class BackupConfigInfo extends \Google\Model
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
  protected $backupApplianceBackupConfigType = BackupApplianceBackupConfig::class;
  protected $backupApplianceBackupConfigDataType = '';
  protected $gcpBackupConfigType = GcpBackupConfig::class;
  protected $gcpBackupConfigDataType = '';
  protected $lastBackupErrorType = Status::class;
  protected $lastBackupErrorDataType = '';
  /**
   * Output only. The status of the last backup to this BackupVault
   *
   * @var string
   */
  public $lastBackupState;
  /**
   * Output only. If the last backup were successful, this field has the
   * consistency date.
   *
   * @var string
   */
  public $lastSuccessfulBackupConsistencyTime;

  /**
   * Configuration for an application backed up by a Backup Appliance.
   *
   * @param BackupApplianceBackupConfig $backupApplianceBackupConfig
   */
  public function setBackupApplianceBackupConfig(BackupApplianceBackupConfig $backupApplianceBackupConfig)
  {
    $this->backupApplianceBackupConfig = $backupApplianceBackupConfig;
  }
  /**
   * @return BackupApplianceBackupConfig
   */
  public function getBackupApplianceBackupConfig()
  {
    return $this->backupApplianceBackupConfig;
  }
  /**
   * Configuration for a Google Cloud resource.
   *
   * @param GcpBackupConfig $gcpBackupConfig
   */
  public function setGcpBackupConfig(GcpBackupConfig $gcpBackupConfig)
  {
    $this->gcpBackupConfig = $gcpBackupConfig;
  }
  /**
   * @return GcpBackupConfig
   */
  public function getGcpBackupConfig()
  {
    return $this->gcpBackupConfig;
  }
  /**
   * Output only. If the last backup failed, this field has the error message.
   *
   * @param Status $lastBackupError
   */
  public function setLastBackupError(Status $lastBackupError)
  {
    $this->lastBackupError = $lastBackupError;
  }
  /**
   * @return Status
   */
  public function getLastBackupError()
  {
    return $this->lastBackupError;
  }
  /**
   * Output only. The status of the last backup to this BackupVault
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
   * Output only. If the last backup were successful, this field has the
   * consistency date.
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
class_alias(BackupConfigInfo::class, 'Google_Service_Backupdr_BackupConfigInfo');
