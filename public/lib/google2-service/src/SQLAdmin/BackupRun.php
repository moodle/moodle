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

class BackupRun extends \Google\Model
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
   * The status of the run is unknown.
   */
  public const STATUS_SQL_BACKUP_RUN_STATUS_UNSPECIFIED = 'SQL_BACKUP_RUN_STATUS_UNSPECIFIED';
  /**
   * The backup operation was enqueued.
   */
  public const STATUS_ENQUEUED = 'ENQUEUED';
  /**
   * The backup is overdue across a given backup window. Indicates a problem.
   * Example: Long-running operation in progress during the whole window.
   */
  public const STATUS_OVERDUE = 'OVERDUE';
  /**
   * The backup is in progress.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The backup failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * The backup was successful.
   */
  public const STATUS_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * The backup was skipped (without problems) for a given backup window.
   * Example: Instance was idle.
   */
  public const STATUS_SKIPPED = 'SKIPPED';
  /**
   * The backup is about to be deleted.
   */
  public const STATUS_DELETION_PENDING = 'DELETION_PENDING';
  /**
   * The backup deletion failed.
   */
  public const STATUS_DELETION_FAILED = 'DELETION_FAILED';
  /**
   * The backup has been deleted.
   */
  public const STATUS_DELETED = 'DELETED';
  /**
   * This is an unknown BackupRun type.
   */
  public const TYPE_SQL_BACKUP_RUN_TYPE_UNSPECIFIED = 'SQL_BACKUP_RUN_TYPE_UNSPECIFIED';
  /**
   * The backup schedule automatically triggers a backup.
   */
  public const TYPE_AUTOMATED = 'AUTOMATED';
  /**
   * The user manually triggers a backup.
   */
  public const TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Specifies the kind of backup, PHYSICAL or DEFAULT_SNAPSHOT.
   *
   * @var string
   */
  public $backupKind;
  /**
   * Output only. The instance database version at the time this backup was
   * made.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * The description of this run, only applicable to on-demand backups.
   *
   * @var string
   */
  public $description;
  protected $diskEncryptionConfigurationType = DiskEncryptionConfiguration::class;
  protected $diskEncryptionConfigurationDataType = '';
  protected $diskEncryptionStatusType = DiskEncryptionStatus::class;
  protected $diskEncryptionStatusDataType = '';
  /**
   * The time the backup operation completed in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $endTime;
  /**
   * The time the run was enqueued in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $enqueuedTime;
  protected $errorType = OperationError::class;
  protected $errorDataType = '';
  /**
   * The identifier for this backup run. Unique only for a specific Cloud SQL
   * instance.
   *
   * @var string
   */
  public $id;
  /**
   * Name of the database instance.
   *
   * @var string
   */
  public $instance;
  /**
   * This is always `sql#backupRun`.
   *
   * @var string
   */
  public $kind;
  /**
   * Location of the backups.
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
   * The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The time the backup operation actually started in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $startTime;
  /**
   * The status of this run.
   *
   * @var string
   */
  public $status;
  /**
   * Backup time zone to prevent restores to an instance with a different time
   * zone. Now relevant only for SQL Server.
   *
   * @var string
   */
  public $timeZone;
  /**
   * The type of this run; can be either "AUTOMATED" or "ON_DEMAND" or "FINAL".
   * This field defaults to "ON_DEMAND" and is ignored, when specified for
   * insert requests.
   *
   * @var string
   */
  public $type;
  /**
   * The start time of the backup window during which this the backup was
   * attempted in [RFC 3339](https://tools.ietf.org/html/rfc3339) format, for
   * example `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $windowStartTime;

  /**
   * Specifies the kind of backup, PHYSICAL or DEFAULT_SNAPSHOT.
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
   * Output only. The instance database version at the time this backup was
   * made.
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
   * The description of this run, only applicable to on-demand backups.
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
   * Encryption configuration specific to a backup.
   *
   * @param DiskEncryptionConfiguration $diskEncryptionConfiguration
   */
  public function setDiskEncryptionConfiguration(DiskEncryptionConfiguration $diskEncryptionConfiguration)
  {
    $this->diskEncryptionConfiguration = $diskEncryptionConfiguration;
  }
  /**
   * @return DiskEncryptionConfiguration
   */
  public function getDiskEncryptionConfiguration()
  {
    return $this->diskEncryptionConfiguration;
  }
  /**
   * Encryption status specific to a backup.
   *
   * @param DiskEncryptionStatus $diskEncryptionStatus
   */
  public function setDiskEncryptionStatus(DiskEncryptionStatus $diskEncryptionStatus)
  {
    $this->diskEncryptionStatus = $diskEncryptionStatus;
  }
  /**
   * @return DiskEncryptionStatus
   */
  public function getDiskEncryptionStatus()
  {
    return $this->diskEncryptionStatus;
  }
  /**
   * The time the backup operation completed in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The time the run was enqueued in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @param string $enqueuedTime
   */
  public function setEnqueuedTime($enqueuedTime)
  {
    $this->enqueuedTime = $enqueuedTime;
  }
  /**
   * @return string
   */
  public function getEnqueuedTime()
  {
    return $this->enqueuedTime;
  }
  /**
   * Information about why the backup operation failed. This is only present if
   * the run has the FAILED status.
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
   * The identifier for this backup run. Unique only for a specific Cloud SQL
   * instance.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Name of the database instance.
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
   * This is always `sql#backupRun`.
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
   * Location of the backups.
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
   * The URI of this resource.
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
   * The time the backup operation actually started in UTC timezone in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
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
   * The status of this run.
   *
   * Accepted values: SQL_BACKUP_RUN_STATUS_UNSPECIFIED, ENQUEUED, OVERDUE,
   * RUNNING, FAILED, SUCCESSFUL, SKIPPED, DELETION_PENDING, DELETION_FAILED,
   * DELETED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Backup time zone to prevent restores to an instance with a different time
   * zone. Now relevant only for SQL Server.
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
   * The type of this run; can be either "AUTOMATED" or "ON_DEMAND" or "FINAL".
   * This field defaults to "ON_DEMAND" and is ignored, when specified for
   * insert requests.
   *
   * Accepted values: SQL_BACKUP_RUN_TYPE_UNSPECIFIED, AUTOMATED, ON_DEMAND
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
  /**
   * The start time of the backup window during which this the backup was
   * attempted in [RFC 3339](https://tools.ietf.org/html/rfc3339) format, for
   * example `2012-11-15T16:19:00.094Z`.
   *
   * @param string $windowStartTime
   */
  public function setWindowStartTime($windowStartTime)
  {
    $this->windowStartTime = $windowStartTime;
  }
  /**
   * @return string
   */
  public function getWindowStartTime()
  {
    return $this->windowStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupRun::class, 'Google_Service_SQLAdmin_BackupRun');
