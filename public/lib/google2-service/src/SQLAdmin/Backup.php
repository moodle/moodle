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

class Backup extends \Google\Model
{
  /**
   * This is an unknown BackupKind.
   */
  public const BACKUP_KIND_SQL_BACKUP_KIND_UNSPECIFIED = 'SQL_BACKUP_KIND_UNSPECIFIED';
  /**
   * Snapshot-based backups.
   */
  public const BACKUP_KIND_SNAPSHOT = 'SNAPSHOT';
  /**
   * Physical backups.
   */
  public const BACKUP_KIND_PHYSICAL = 'PHYSICAL';
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_SQL_DATABASE_VERSION_UNSPECIFIED = 'SQL_DATABASE_VERSION_UNSPECIFIED';
  /**
   * The database version is MySQL 5.1.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_1 = 'MYSQL_5_1';
  /**
   * The database version is MySQL 5.5.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_5 = 'MYSQL_5_5';
  /**
   * The database version is MySQL 5.6.
   */
  public const DATABASE_VERSION_MYSQL_5_6 = 'MYSQL_5_6';
  /**
   * The database version is MySQL 5.7.
   */
  public const DATABASE_VERSION_MYSQL_5_7 = 'MYSQL_5_7';
  /**
   * The database version is MySQL 8.
   */
  public const DATABASE_VERSION_MYSQL_8_0 = 'MYSQL_8_0';
  /**
   * The database major version is MySQL 8.0 and the minor version is 18.
   */
  public const DATABASE_VERSION_MYSQL_8_0_18 = 'MYSQL_8_0_18';
  /**
   * The database major version is MySQL 8.0 and the minor version is 26.
   */
  public const DATABASE_VERSION_MYSQL_8_0_26 = 'MYSQL_8_0_26';
  /**
   * The database major version is MySQL 8.0 and the minor version is 27.
   */
  public const DATABASE_VERSION_MYSQL_8_0_27 = 'MYSQL_8_0_27';
  /**
   * The database major version is MySQL 8.0 and the minor version is 28.
   */
  public const DATABASE_VERSION_MYSQL_8_0_28 = 'MYSQL_8_0_28';
  /**
   * The database major version is MySQL 8.0 and the minor version is 29.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_8_0_29 = 'MYSQL_8_0_29';
  /**
   * The database major version is MySQL 8.0 and the minor version is 30.
   */
  public const DATABASE_VERSION_MYSQL_8_0_30 = 'MYSQL_8_0_30';
  /**
   * The database major version is MySQL 8.0 and the minor version is 31.
   */
  public const DATABASE_VERSION_MYSQL_8_0_31 = 'MYSQL_8_0_31';
  /**
   * The database major version is MySQL 8.0 and the minor version is 32.
   */
  public const DATABASE_VERSION_MYSQL_8_0_32 = 'MYSQL_8_0_32';
  /**
   * The database major version is MySQL 8.0 and the minor version is 33.
   */
  public const DATABASE_VERSION_MYSQL_8_0_33 = 'MYSQL_8_0_33';
  /**
   * The database major version is MySQL 8.0 and the minor version is 34.
   */
  public const DATABASE_VERSION_MYSQL_8_0_34 = 'MYSQL_8_0_34';
  /**
   * The database major version is MySQL 8.0 and the minor version is 35.
   */
  public const DATABASE_VERSION_MYSQL_8_0_35 = 'MYSQL_8_0_35';
  /**
   * The database major version is MySQL 8.0 and the minor version is 36.
   */
  public const DATABASE_VERSION_MYSQL_8_0_36 = 'MYSQL_8_0_36';
  /**
   * The database major version is MySQL 8.0 and the minor version is 37.
   */
  public const DATABASE_VERSION_MYSQL_8_0_37 = 'MYSQL_8_0_37';
  /**
   * The database major version is MySQL 8.0 and the minor version is 39.
   */
  public const DATABASE_VERSION_MYSQL_8_0_39 = 'MYSQL_8_0_39';
  /**
   * The database major version is MySQL 8.0 and the minor version is 40.
   */
  public const DATABASE_VERSION_MYSQL_8_0_40 = 'MYSQL_8_0_40';
  /**
   * The database major version is MySQL 8.0 and the minor version is 41.
   */
  public const DATABASE_VERSION_MYSQL_8_0_41 = 'MYSQL_8_0_41';
  /**
   * The database major version is MySQL 8.0 and the minor version is 42.
   */
  public const DATABASE_VERSION_MYSQL_8_0_42 = 'MYSQL_8_0_42';
  /**
   * The database major version is MySQL 8.0 and the minor version is 43.
   */
  public const DATABASE_VERSION_MYSQL_8_0_43 = 'MYSQL_8_0_43';
  /**
   * The database major version is MySQL 8.0 and the minor version is 44.
   */
  public const DATABASE_VERSION_MYSQL_8_0_44 = 'MYSQL_8_0_44';
  /**
   * The database major version is MySQL 8.0 and the minor version is 45.
   */
  public const DATABASE_VERSION_MYSQL_8_0_45 = 'MYSQL_8_0_45';
  /**
   * The database major version is MySQL 8.0 and the minor version is 46.
   */
  public const DATABASE_VERSION_MYSQL_8_0_46 = 'MYSQL_8_0_46';
  /**
   * The database version is MySQL 8.4.
   */
  public const DATABASE_VERSION_MYSQL_8_4 = 'MYSQL_8_4';
  /**
   * The database version is SQL Server 2017 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_STANDARD = 'SQLSERVER_2017_STANDARD';
  /**
   * The database version is SQL Server 2017 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_ENTERPRISE = 'SQLSERVER_2017_ENTERPRISE';
  /**
   * The database version is SQL Server 2017 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_EXPRESS = 'SQLSERVER_2017_EXPRESS';
  /**
   * The database version is SQL Server 2017 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_WEB = 'SQLSERVER_2017_WEB';
  /**
   * The database version is PostgreSQL 9.6.
   */
  public const DATABASE_VERSION_POSTGRES_9_6 = 'POSTGRES_9_6';
  /**
   * The database version is PostgreSQL 10.
   */
  public const DATABASE_VERSION_POSTGRES_10 = 'POSTGRES_10';
  /**
   * The database version is PostgreSQL 11.
   */
  public const DATABASE_VERSION_POSTGRES_11 = 'POSTGRES_11';
  /**
   * The database version is PostgreSQL 12.
   */
  public const DATABASE_VERSION_POSTGRES_12 = 'POSTGRES_12';
  /**
   * The database version is PostgreSQL 13.
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is PostgreSQL 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is PostgreSQL 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is PostgreSQL 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is PostgreSQL 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The database version is PostgreSQL 18.
   */
  public const DATABASE_VERSION_POSTGRES_18 = 'POSTGRES_18';
  /**
   * The database version is SQL Server 2019 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_STANDARD = 'SQLSERVER_2019_STANDARD';
  /**
   * The database version is SQL Server 2019 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_ENTERPRISE = 'SQLSERVER_2019_ENTERPRISE';
  /**
   * The database version is SQL Server 2019 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_EXPRESS = 'SQLSERVER_2019_EXPRESS';
  /**
   * The database version is SQL Server 2019 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_WEB = 'SQLSERVER_2019_WEB';
  /**
   * The database version is SQL Server 2022 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_STANDARD = 'SQLSERVER_2022_STANDARD';
  /**
   * The database version is SQL Server 2022 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_ENTERPRISE = 'SQLSERVER_2022_ENTERPRISE';
  /**
   * The database version is SQL Server 2022 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_EXPRESS = 'SQLSERVER_2022_EXPRESS';
  /**
   * The database version is SQL Server 2022 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_WEB = 'SQLSERVER_2022_WEB';
  /**
   * The state of the backup is unknown.
   */
  public const STATE_SQL_BACKUP_STATE_UNSPECIFIED = 'SQL_BACKUP_STATE_UNSPECIFIED';
  /**
   * The backup that's added to a queue.
   */
  public const STATE_ENQUEUED = 'ENQUEUED';
  /**
   * The backup is in progress.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The backup failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The backup is successful.
   */
  public const STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * The backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Deletion of the backup failed.
   */
  public const STATE_DELETION_FAILED = 'DELETION_FAILED';
  /**
   * This is an unknown backup type.
   */
  public const TYPE_SQL_BACKUP_TYPE_UNSPECIFIED = 'SQL_BACKUP_TYPE_UNSPECIFIED';
  /**
   * The backup schedule triggers a backup automatically.
   */
  public const TYPE_AUTOMATED = 'AUTOMATED';
  /**
   * The user triggers a backup manually.
   */
  public const TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * The backup created when instance is deleted.
   */
  public const TYPE_FINAL = 'FINAL';
  protected $backupIntervalType = Interval::class;
  protected $backupIntervalDataType = '';
  /**
   * Output only. Specifies the kind of backup, PHYSICAL or DEFAULT_SNAPSHOT.
   *
   * @var string
   */
  public $backupKind;
  /**
   * Output only. The mapping to backup run resource used for IAM validations.
   *
   * @var string
   */
  public $backupRun;
  /**
   * Output only. The database version of the instance of at the time this
   * backup was made.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * The description of this backup.
   *
   * @var string
   */
  public $description;
  protected $errorType = OperationError::class;
  protected $errorDataType = '';
  /**
   * Backup expiration time. A UTC timestamp of when this backup expired.
   *
   * @var string
   */
  public $expiryTime;
  /**
   * The name of the source database instance.
   *
   * @var string
   */
  public $instance;
  /**
   * Optional. Output only. Timestamp in UTC of when the instance associated
   * with this backup is deleted.
   *
   * @var string
   */
  public $instanceDeletionTime;
  protected $instanceSettingsType = DatabaseInstance::class;
  protected $instanceSettingsDataType = '';
  /**
   * Output only. This is always `sql#backup`.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. This output contains the encryption configuration for a backup
   * and the resource name of the KMS key for disk encryption.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Output only. This output contains the encryption status for a backup and
   * the version of the KMS key that's used to encrypt the Cloud SQL instance.
   *
   * @var string
   */
  public $kmsKeyVersion;
  /**
   * The storage location of the backups. The location can be multi-regional.
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The maximum chargeable bytes for the backup.
   *
   * @var string
   */
  public $maxChargeableBytes;
  /**
   * Output only. The resource name of the backup. Format:
   * projects/{project}/backups/{backup}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. This status indicates whether the backup satisfies PZI. The
   * status is reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. This status indicates whether the backup satisfies PZS. The
   * status is reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The status of this backup.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. This output contains a backup time zone. If a Cloud SQL for
   * SQL Server instance has a different time zone from the backup's time zone,
   * then the restore to the instance doesn't happen.
   *
   * @var string
   */
  public $timeZone;
  /**
   * Input only. The time-to-live (TTL) interval for this resource (in days).
   * For example: ttlDays:7, means 7 days from the current time. The expiration
   * time can't exceed 365 days from the time that the backup is created.
   *
   * @var string
   */
  public $ttlDays;
  /**
   * Output only. The type of this backup. The type can be "AUTOMATED",
   * "ON_DEMAND" or “FINAL”.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. This output contains the following values: start_time: All
   * database writes up to this time are available. end_time: Any database
   * writes after this time aren't available.
   *
   * @param Interval $backupInterval
   */
  public function setBackupInterval(Interval $backupInterval)
  {
    $this->backupInterval = $backupInterval;
  }
  /**
   * @return Interval
   */
  public function getBackupInterval()
  {
    return $this->backupInterval;
  }
  /**
   * Output only. Specifies the kind of backup, PHYSICAL or DEFAULT_SNAPSHOT.
   *
   * Accepted values: SQL_BACKUP_KIND_UNSPECIFIED, SNAPSHOT, PHYSICAL
   *
   * @param self::BACKUP_KIND_* $backupKind
   */
  public function setBackupKind($backupKind)
  {
    $this->backupKind = $backupKind;
  }
  /**
   * @return self::BACKUP_KIND_*
   */
  public function getBackupKind()
  {
    return $this->backupKind;
  }
  /**
   * Output only. The mapping to backup run resource used for IAM validations.
   *
   * @param string $backupRun
   */
  public function setBackupRun($backupRun)
  {
    $this->backupRun = $backupRun;
  }
  /**
   * @return string
   */
  public function getBackupRun()
  {
    return $this->backupRun;
  }
  /**
   * Output only. The database version of the instance of at the time this
   * backup was made.
   *
   * Accepted values: SQL_DATABASE_VERSION_UNSPECIFIED, MYSQL_5_1, MYSQL_5_5,
   * MYSQL_5_6, MYSQL_5_7, MYSQL_8_0, MYSQL_8_0_18, MYSQL_8_0_26, MYSQL_8_0_27,
   * MYSQL_8_0_28, MYSQL_8_0_29, MYSQL_8_0_30, MYSQL_8_0_31, MYSQL_8_0_32,
   * MYSQL_8_0_33, MYSQL_8_0_34, MYSQL_8_0_35, MYSQL_8_0_36, MYSQL_8_0_37,
   * MYSQL_8_0_39, MYSQL_8_0_40, MYSQL_8_0_41, MYSQL_8_0_42, MYSQL_8_0_43,
   * MYSQL_8_0_44, MYSQL_8_0_45, MYSQL_8_0_46, MYSQL_8_4,
   * SQLSERVER_2017_STANDARD, SQLSERVER_2017_ENTERPRISE, SQLSERVER_2017_EXPRESS,
   * SQLSERVER_2017_WEB, POSTGRES_9_6, POSTGRES_10, POSTGRES_11, POSTGRES_12,
   * POSTGRES_13, POSTGRES_14, POSTGRES_15, POSTGRES_16, POSTGRES_17,
   * POSTGRES_18, SQLSERVER_2019_STANDARD, SQLSERVER_2019_ENTERPRISE,
   * SQLSERVER_2019_EXPRESS, SQLSERVER_2019_WEB, SQLSERVER_2022_STANDARD,
   * SQLSERVER_2022_ENTERPRISE, SQLSERVER_2022_EXPRESS, SQLSERVER_2022_WEB
   *
   * @param self::DATABASE_VERSION_* $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return self::DATABASE_VERSION_*
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * The description of this backup.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Information about why the backup operation fails (for example,
   * when the backup state fails).
   *
   * @param OperationError $error
   */
  public function setError(OperationError $error)
  {
    $this->error = $error;
  }
  /**
   * @return OperationError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Backup expiration time. A UTC timestamp of when this backup expired.
   *
   * @param string $expiryTime
   */
  public function setExpiryTime($expiryTime)
  {
    $this->expiryTime = $expiryTime;
  }
  /**
   * @return string
   */
  public function getExpiryTime()
  {
    return $this->expiryTime;
  }
  /**
   * The name of the source database instance.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Optional. Output only. Timestamp in UTC of when the instance associated
   * with this backup is deleted.
   *
   * @param string $instanceDeletionTime
   */
  public function setInstanceDeletionTime($instanceDeletionTime)
  {
    $this->instanceDeletionTime = $instanceDeletionTime;
  }
  /**
   * @return string
   */
  public function getInstanceDeletionTime()
  {
    return $this->instanceDeletionTime;
  }
  /**
   * Optional. Output only. The instance setting of the source instance that's
   * associated with this backup.
   *
   * @param DatabaseInstance $instanceSettings
   */
  public function setInstanceSettings(DatabaseInstance $instanceSettings)
  {
    $this->instanceSettings = $instanceSettings;
  }
  /**
   * @return DatabaseInstance
   */
  public function getInstanceSettings()
  {
    return $this->instanceSettings;
  }
  /**
   * Output only. This is always `sql#backup`.
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
   * Output only. This output contains the encryption configuration for a backup
   * and the resource name of the KMS key for disk encryption.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Output only. This output contains the encryption status for a backup and
   * the version of the KMS key that's used to encrypt the Cloud SQL instance.
   *
   * @param string $kmsKeyVersion
   */
  public function setKmsKeyVersion($kmsKeyVersion)
  {
    $this->kmsKeyVersion = $kmsKeyVersion;
  }
  /**
   * @return string
   */
  public function getKmsKeyVersion()
  {
    return $this->kmsKeyVersion;
  }
  /**
   * The storage location of the backups. The location can be multi-regional.
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
   * Output only. The maximum chargeable bytes for the backup.
   *
   * @param string $maxChargeableBytes
   */
  public function setMaxChargeableBytes($maxChargeableBytes)
  {
    $this->maxChargeableBytes = $maxChargeableBytes;
  }
  /**
   * @return string
   */
  public function getMaxChargeableBytes()
  {
    return $this->maxChargeableBytes;
  }
  /**
   * Output only. The resource name of the backup. Format:
   * projects/{project}/backups/{backup}.
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
   * Output only. This status indicates whether the backup satisfies PZI. The
   * status is reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. This status indicates whether the backup satisfies PZS. The
   * status is reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The URI of this resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. The status of this backup.
   *
   * Accepted values: SQL_BACKUP_STATE_UNSPECIFIED, ENQUEUED, RUNNING, FAILED,
   * SUCCESSFUL, DELETING, DELETION_FAILED
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
   * Output only. This output contains a backup time zone. If a Cloud SQL for
   * SQL Server instance has a different time zone from the backup's time zone,
   * then the restore to the instance doesn't happen.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Input only. The time-to-live (TTL) interval for this resource (in days).
   * For example: ttlDays:7, means 7 days from the current time. The expiration
   * time can't exceed 365 days from the time that the backup is created.
   *
   * @param string $ttlDays
   */
  public function setTtlDays($ttlDays)
  {
    $this->ttlDays = $ttlDays;
  }
  /**
   * @return string
   */
  public function getTtlDays()
  {
    return $this->ttlDays;
  }
  /**
   * Output only. The type of this backup. The type can be "AUTOMATED",
   * "ON_DEMAND" or “FINAL”.
   *
   * Accepted values: SQL_BACKUP_TYPE_UNSPECIFIED, AUTOMATED, ON_DEMAND, FINAL
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_SQLAdmin_Backup');
