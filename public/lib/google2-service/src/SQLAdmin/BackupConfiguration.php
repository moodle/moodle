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

namespace Google\Service\SQLAdmin;

class BackupConfiguration extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const BACKUP_TIER_BACKUP_TIER_UNSPECIFIED = 'BACKUP_TIER_UNSPECIFIED';
  /**
   * Instance is managed by Cloud SQL.
   */
  public const BACKUP_TIER_STANDARD = 'STANDARD';
  /**
   * Deprecated: ADVANCED is deprecated. Please use ENHANCED instead.
   *
   * @deprecated
   */
  public const BACKUP_TIER_ADVANCED = 'ADVANCED';
  /**
   * Instance is managed by Google Cloud Backup and DR Service.
   */
  public const BACKUP_TIER_ENHANCED = 'ENHANCED';
  /**
   * Unspecified.
   */
  public const TRANSACTIONAL_LOG_STORAGE_STATE_TRANSACTIONAL_LOG_STORAGE_STATE_UNSPECIFIED = 'TRANSACTIONAL_LOG_STORAGE_STATE_UNSPECIFIED';
  /**
   * The transaction logs used for PITR for the instance are stored on a data
   * disk.
   */
  public const TRANSACTIONAL_LOG_STORAGE_STATE_DISK = 'DISK';
  /**
   * The transaction logs used for PITR for the instance are switching from
   * being stored on a data disk to being stored in Cloud Storage. Only
   * applicable to MySQL.
   */
  public const TRANSACTIONAL_LOG_STORAGE_STATE_SWITCHING_TO_CLOUD_STORAGE = 'SWITCHING_TO_CLOUD_STORAGE';
  /**
   * The transaction logs used for PITR for the instance are now stored in Cloud
   * Storage. Previously, they were stored on a data disk. Only applicable to
   * MySQL.
   */
  public const TRANSACTIONAL_LOG_STORAGE_STATE_SWITCHED_TO_CLOUD_STORAGE = 'SWITCHED_TO_CLOUD_STORAGE';
  /**
   * The transaction logs used for PITR for the instance are stored in Cloud
   * Storage. Only applicable to MySQL and PostgreSQL.
   */
  public const TRANSACTIONAL_LOG_STORAGE_STATE_CLOUD_STORAGE = 'CLOUD_STORAGE';
  protected $backupRetentionSettingsType = BackupRetentionSettings::class;
  protected $backupRetentionSettingsDataType = '';
  /**
   * Output only. Backup tier that manages the backups for the instance.
   *
   * @var string
   */
  public $backupTier;
  /**
   * (MySQL only) Whether binary log is enabled. If backup configuration is
   * disabled, binarylog must be disabled as well.
   *
   * @var bool
   */
  public $binaryLogEnabled;
  /**
   * Whether this configuration is enabled.
   *
   * @var bool
   */
  public $enabled;
  /**
   * This is always `sql#backupConfiguration`.
   *
   * @var string
   */
  public $kind;
  /**
   * Location of the backup
   *
   * @var string
   */
  public $location;
  /**
   * Whether point in time recovery is enabled.
   *
   * @var bool
   */
  public $pointInTimeRecoveryEnabled;
  /**
   * Reserved for future use.
   *
   * @var bool
   */
  public $replicationLogArchivingEnabled;
  /**
   * Start time for the daily backup configuration in UTC timezone in the 24
   * hour format - `HH:MM`.
   *
   * @var string
   */
  public $startTime;
  /**
   * The number of days of transaction logs we retain for point in time restore,
   * from 1-7.
   *
   * @var int
   */
  public $transactionLogRetentionDays;
  /**
   * Output only. This value contains the storage location of transactional logs
   * used to perform point-in-time recovery (PITR) for the database.
   *
   * @var string
   */
  public $transactionalLogStorageState;

  /**
   * Backup retention settings.
   *
   * @param BackupRetentionSettings $backupRetentionSettings
   */
  public function setBackupRetentionSettings(BackupRetentionSettings $backupRetentionSettings)
  {
    $this->backupRetentionSettings = $backupRetentionSettings;
  }
  /**
   * @return BackupRetentionSettings
   */
  public function getBackupRetentionSettings()
  {
    return $this->backupRetentionSettings;
  }
  /**
   * Output only. Backup tier that manages the backups for the instance.
   *
   * Accepted values: BACKUP_TIER_UNSPECIFIED, STANDARD, ADVANCED, ENHANCED
   *
   * @param self::BACKUP_TIER_* $backupTier
   */
  public function setBackupTier($backupTier)
  {
    $this->backupTier = $backupTier;
  }
  /**
   * @return self::BACKUP_TIER_*
   */
  public function getBackupTier()
  {
    return $this->backupTier;
  }
  /**
   * (MySQL only) Whether binary log is enabled. If backup configuration is
   * disabled, binarylog must be disabled as well.
   *
   * @param bool $binaryLogEnabled
   */
  public function setBinaryLogEnabled($binaryLogEnabled)
  {
    $this->binaryLogEnabled = $binaryLogEnabled;
  }
  /**
   * @return bool
   */
  public function getBinaryLogEnabled()
  {
    return $this->binaryLogEnabled;
  }
  /**
   * Whether this configuration is enabled.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * This is always `sql#backupConfiguration`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Location of the backup
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Whether point in time recovery is enabled.
   *
   * @param bool $pointInTimeRecoveryEnabled
   */
  public function setPointInTimeRecoveryEnabled($pointInTimeRecoveryEnabled)
  {
    $this->pointInTimeRecoveryEnabled = $pointInTimeRecoveryEnabled;
  }
  /**
   * @return bool
   */
  public function getPointInTimeRecoveryEnabled()
  {
    return $this->pointInTimeRecoveryEnabled;
  }
  /**
   * Reserved for future use.
   *
   * @param bool $replicationLogArchivingEnabled
   */
  public function setReplicationLogArchivingEnabled($replicationLogArchivingEnabled)
  {
    $this->replicationLogArchivingEnabled = $replicationLogArchivingEnabled;
  }
  /**
   * @return bool
   */
  public function getReplicationLogArchivingEnabled()
  {
    return $this->replicationLogArchivingEnabled;
  }
  /**
   * Start time for the daily backup configuration in UTC timezone in the 24
   * hour format - `HH:MM`.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The number of days of transaction logs we retain for point in time restore,
   * from 1-7.
   *
   * @param int $transactionLogRetentionDays
   */
  public function setTransactionLogRetentionDays($transactionLogRetentionDays)
  {
    $this->transactionLogRetentionDays = $transactionLogRetentionDays;
  }
  /**
   * @return int
   */
  public function getTransactionLogRetentionDays()
  {
    return $this->transactionLogRetentionDays;
  }
  /**
   * Output only. This value contains the storage location of transactional logs
   * used to perform point-in-time recovery (PITR) for the database.
   *
   * Accepted values: TRANSACTIONAL_LOG_STORAGE_STATE_UNSPECIFIED, DISK,
   * SWITCHING_TO_CLOUD_STORAGE, SWITCHED_TO_CLOUD_STORAGE, CLOUD_STORAGE
   *
   * @param self::TRANSACTIONAL_LOG_STORAGE_STATE_* $transactionalLogStorageState
   */
  public function setTransactionalLogStorageState($transactionalLogStorageState)
  {
    $this->transactionalLogStorageState = $transactionalLogStorageState;
  }
  /**
   * @return self::TRANSACTIONAL_LOG_STORAGE_STATE_*
   */
  public function getTransactionalLogStorageState()
  {
    return $this->transactionalLogStorageState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupConfiguration::class, 'Google_Service_SQLAdmin_BackupConfiguration');
