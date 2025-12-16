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

namespace Google\Service\Spanner;

class Database extends \Google\Collection
{
  /**
   * Default value. This value will create a database with the
   * GOOGLE_STANDARD_SQL dialect.
   */
  public const DATABASE_DIALECT_DATABASE_DIALECT_UNSPECIFIED = 'DATABASE_DIALECT_UNSPECIFIED';
  /**
   * GoogleSQL supported SQL.
   */
  public const DATABASE_DIALECT_GOOGLE_STANDARD_SQL = 'GOOGLE_STANDARD_SQL';
  /**
   * PostgreSQL supported SQL.
   */
  public const DATABASE_DIALECT_POSTGRESQL = 'POSTGRESQL';
  /**
   * Not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The database is still being created. Operations on the database may fail
   * with `FAILED_PRECONDITION` in this state.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The database is fully created and ready for use.
   */
  public const STATE_READY = 'READY';
  /**
   * The database is fully created and ready for use, but is still being
   * optimized for performance and cannot handle full load. In this state, the
   * database still references the backup it was restore from, preventing the
   * backup from being deleted. When optimizations are complete, the full
   * performance of the database will be restored, and the database will
   * transition to `READY` state.
   */
  public const STATE_READY_OPTIMIZING = 'READY_OPTIMIZING';
  protected $collection_key = 'encryptionInfo';
  /**
   * Output only. If exists, the time at which the database creation started.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The dialect of the Cloud Spanner Database.
   *
   * @var string
   */
  public $databaseDialect;
  /**
   * Output only. The read-write region which contains the database's leader
   * replicas. This is the same as the value of default_leader database option
   * set using DatabaseAdmin.CreateDatabase or DatabaseAdmin.UpdateDatabaseDdl.
   * If not explicitly set, this is empty.
   *
   * @var string
   */
  public $defaultLeader;
  /**
   * Output only. Earliest timestamp at which older versions of the data can be
   * read. This value is continuously updated by Cloud Spanner and becomes stale
   * the moment it is queried. If you are using this value to recover data, make
   * sure to account for the time from the moment when the value is queried to
   * the moment when you initiate the recovery.
   *
   * @var string
   */
  public $earliestVersionTime;
  /**
   * Optional. Whether drop protection is enabled for this database. Defaults to
   * false, if not set. For more details, please see how to [prevent accidental
   * database deletion](https://cloud.google.com/spanner/docs/prevent-database-
   * deletion).
   *
   * @var bool
   */
  public $enableDropProtection;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = 'array';
  /**
   * Required. The name of the database. Values are of the form
   * `projects//instances//databases/`, where `` is as specified in the `CREATE
   * DATABASE` statement. This name can be passed to other API methods to
   * identify the database.
   *
   * @var string
   */
  public $name;
  protected $quorumInfoType = QuorumInfo::class;
  protected $quorumInfoDataType = '';
  /**
   * Output only. If true, the database is being updated. If false, there are no
   * ongoing update operations for the database.
   *
   * @var bool
   */
  public $reconciling;
  protected $restoreInfoType = RestoreInfo::class;
  protected $restoreInfoDataType = '';
  /**
   * Output only. The current database state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The period in which Cloud Spanner retains all versions of data
   * for the database. This is the same as the value of version_retention_period
   * database option set using UpdateDatabaseDdl. Defaults to 1 hour, if not
   * set.
   *
   * @var string
   */
  public $versionRetentionPeriod;

  /**
   * Output only. If exists, the time at which the database creation started.
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
   * Output only. The dialect of the Cloud Spanner Database.
   *
   * Accepted values: DATABASE_DIALECT_UNSPECIFIED, GOOGLE_STANDARD_SQL,
   * POSTGRESQL
   *
   * @param self::DATABASE_DIALECT_* $databaseDialect
   */
  public function setDatabaseDialect($databaseDialect)
  {
    $this->databaseDialect = $databaseDialect;
  }
  /**
   * @return self::DATABASE_DIALECT_*
   */
  public function getDatabaseDialect()
  {
    return $this->databaseDialect;
  }
  /**
   * Output only. The read-write region which contains the database's leader
   * replicas. This is the same as the value of default_leader database option
   * set using DatabaseAdmin.CreateDatabase or DatabaseAdmin.UpdateDatabaseDdl.
   * If not explicitly set, this is empty.
   *
   * @param string $defaultLeader
   */
  public function setDefaultLeader($defaultLeader)
  {
    $this->defaultLeader = $defaultLeader;
  }
  /**
   * @return string
   */
  public function getDefaultLeader()
  {
    return $this->defaultLeader;
  }
  /**
   * Output only. Earliest timestamp at which older versions of the data can be
   * read. This value is continuously updated by Cloud Spanner and becomes stale
   * the moment it is queried. If you are using this value to recover data, make
   * sure to account for the time from the moment when the value is queried to
   * the moment when you initiate the recovery.
   *
   * @param string $earliestVersionTime
   */
  public function setEarliestVersionTime($earliestVersionTime)
  {
    $this->earliestVersionTime = $earliestVersionTime;
  }
  /**
   * @return string
   */
  public function getEarliestVersionTime()
  {
    return $this->earliestVersionTime;
  }
  /**
   * Optional. Whether drop protection is enabled for this database. Defaults to
   * false, if not set. For more details, please see how to [prevent accidental
   * database deletion](https://cloud.google.com/spanner/docs/prevent-database-
   * deletion).
   *
   * @param bool $enableDropProtection
   */
  public function setEnableDropProtection($enableDropProtection)
  {
    $this->enableDropProtection = $enableDropProtection;
  }
  /**
   * @return bool
   */
  public function getEnableDropProtection()
  {
    return $this->enableDropProtection;
  }
  /**
   * Output only. For databases that are using customer managed encryption, this
   * field contains the encryption configuration for the database. For databases
   * that are using Google default or other types of encryption, this field is
   * empty.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Output only. For databases that are using customer managed encryption, this
   * field contains the encryption information for the database, such as all
   * Cloud KMS key versions that are in use. The `encryption_status` field
   * inside of each `EncryptionInfo` is not populated. For databases that are
   * using Google default or other types of encryption, this field is empty.
   * This field is propagated lazily from the backend. There might be a delay
   * from when a key version is being used and when it appears in this field.
   *
   * @param EncryptionInfo[] $encryptionInfo
   */
  public function setEncryptionInfo($encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo[]
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Required. The name of the database. Values are of the form
   * `projects//instances//databases/`, where `` is as specified in the `CREATE
   * DATABASE` statement. This name can be passed to other API methods to
   * identify the database.
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
   * Output only. Applicable only for databases that use dual-region instance
   * configurations. Contains information about the quorum.
   *
   * @param QuorumInfo $quorumInfo
   */
  public function setQuorumInfo(QuorumInfo $quorumInfo)
  {
    $this->quorumInfo = $quorumInfo;
  }
  /**
   * @return QuorumInfo
   */
  public function getQuorumInfo()
  {
    return $this->quorumInfo;
  }
  /**
   * Output only. If true, the database is being updated. If false, there are no
   * ongoing update operations for the database.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. Applicable only for restored databases. Contains information
   * about the restore source.
   *
   * @param RestoreInfo $restoreInfo
   */
  public function setRestoreInfo(RestoreInfo $restoreInfo)
  {
    $this->restoreInfo = $restoreInfo;
  }
  /**
   * @return RestoreInfo
   */
  public function getRestoreInfo()
  {
    return $this->restoreInfo;
  }
  /**
   * Output only. The current database state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, READY_OPTIMIZING
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
   * Output only. The period in which Cloud Spanner retains all versions of data
   * for the database. This is the same as the value of version_retention_period
   * database option set using UpdateDatabaseDdl. Defaults to 1 hour, if not
   * set.
   *
   * @param string $versionRetentionPeriod
   */
  public function setVersionRetentionPeriod($versionRetentionPeriod)
  {
    $this->versionRetentionPeriod = $versionRetentionPeriod;
  }
  /**
   * @return string
   */
  public function getVersionRetentionPeriod()
  {
    return $this->versionRetentionPeriod;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Database::class, 'Google_Service_Spanner_Database');
