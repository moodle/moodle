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

class Backup extends \Google\Collection
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
   * The pending backup is still being created. Operations on the backup may
   * fail with `FAILED_PRECONDITION` in this state.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup is complete and ready for use.
   */
  public const STATE_READY = 'READY';
  protected $collection_key = 'referencingDatabases';
  /**
   * Output only. List of backup schedule URIs that are associated with creating
   * this backup. This is only applicable for scheduled backups, and is empty
   * for on-demand backups. To optimize for storage, whenever possible, multiple
   * schedules are collapsed together to create one backup. In such cases, this
   * field captures the list of all backup schedule URIs that are associated
   * with creating this backup. If collapsing is not done, then this field
   * captures the single backup schedule URI associated with creating this
   * backup.
   *
   * @var string[]
   */
  public $backupSchedules;
  /**
   * Output only. The time the CreateBackup request is received. If the request
   * does not specify `version_time`, the `version_time` of the backup will be
   * equivalent to the `create_time`.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required for the CreateBackup operation. Name of the database from which
   * this backup was created. This needs to be in the same instance as the
   * backup. Values are of the form `projects//instances//databases/`.
   *
   * @var string
   */
  public $database;
  /**
   * Output only. The database dialect information for the backup.
   *
   * @var string
   */
  public $databaseDialect;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  protected $encryptionInformationType = EncryptionInfo::class;
  protected $encryptionInformationDataType = 'array';
  /**
   * Output only. For a backup in an incremental backup chain, this is the
   * storage space needed to keep the data that has changed since the previous
   * backup. For all other backups, this is always the size of the backup. This
   * value may change if backups on the same chain get deleted or expired. This
   * field can be used to calculate the total storage space used by a set of
   * backups. For example, the total space used by all backups of a database can
   * be computed by summing up this field.
   *
   * @var string
   */
  public $exclusiveSizeBytes;
  /**
   * Required for the CreateBackup operation. The expiration time of the backup,
   * with microseconds granularity that must be at least 6 hours and at most 366
   * days from the time the CreateBackup request is processed. Once the
   * `expire_time` has passed, the backup is eligible to be automatically
   * deleted by Cloud Spanner to free the resources used by the backup.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. The number of bytes that will be freed by deleting this
   * backup. This value will be zero if, for example, this backup is part of an
   * incremental backup chain and younger backups in the chain require that we
   * keep its data. For backups not in an incremental backup chain, this is
   * always the size of the backup. This value may change if backups on the same
   * chain get created, deleted or expired.
   *
   * @var string
   */
  public $freeableSizeBytes;
  /**
   * Output only. Populated only for backups in an incremental backup chain.
   * Backups share the same chain id if and only if they belong to the same
   * incremental backup chain. Use this field to determine which backups are
   * part of the same incremental backup chain. The ordering of backups in the
   * chain can be determined by ordering the backup `version_time`.
   *
   * @var string
   */
  public $incrementalBackupChainId;
  protected $instancePartitionsType = BackupInstancePartition::class;
  protected $instancePartitionsDataType = 'array';
  /**
   * Output only. The max allowed expiration time of the backup, with
   * microseconds granularity. A backup's expiration time can be configured in
   * multiple APIs: CreateBackup, UpdateBackup, CopyBackup. When updating or
   * copying an existing backup, the expiration time specified must be less than
   * `Backup.max_expire_time`.
   *
   * @var string
   */
  public $maxExpireTime;
  /**
   * Output only for the CreateBackup operation. Required for the UpdateBackup
   * operation. A globally unique identifier for the backup which cannot be
   * changed. Values are of the form `projects//instances//backups/a-z*[a-z0-9]`
   * The final segment of the name must be between 2 and 60 characters in
   * length. The backup is stored in the location(s) specified in the instance
   * configuration of the instance containing the backup, identified by the
   * prefix of the backup name of the form `projects//instances/`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Data deleted at a time older than this is guaranteed not to be
   * retained in order to support this backup. For a backup in an incremental
   * backup chain, this is the version time of the oldest backup that exists or
   * ever existed in the chain. For all other backups, this is the version time
   * of the backup. This field can be used to understand what data is being
   * retained by the backup system.
   *
   * @var string
   */
  public $oldestVersionTime;
  /**
   * Output only. The names of the destination backups being created by copying
   * this source backup. The backup names are of the form
   * `projects//instances//backups/`. Referencing backups may exist in different
   * instances. The existence of any referencing backup prevents the backup from
   * being deleted. When the copy operation is done (either successfully
   * completed or cancelled or the destination backup is deleted), the reference
   * to the backup is removed.
   *
   * @var string[]
   */
  public $referencingBackups;
  /**
   * Output only. The names of the restored databases that reference the backup.
   * The database names are of the form `projects//instances//databases/`.
   * Referencing databases may exist in different instances. The existence of
   * any referencing database prevents the backup from being deleted. When a
   * restored database from the backup enters the `READY` state, the reference
   * to the backup is removed.
   *
   * @var string[]
   */
  public $referencingDatabases;
  /**
   * Output only. Size of the backup in bytes. For a backup in an incremental
   * backup chain, this is the sum of the `exclusive_size_bytes` of itself and
   * all older backups in the chain.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. The current state of the backup.
   *
   * @var string
   */
  public $state;
  /**
   * The backup will contain an externally consistent copy of the database at
   * the timestamp specified by `version_time`. If `version_time` is not
   * specified, the system will set `version_time` to the `create_time` of the
   * backup.
   *
   * @var string
   */
  public $versionTime;

  /**
   * Output only. List of backup schedule URIs that are associated with creating
   * this backup. This is only applicable for scheduled backups, and is empty
   * for on-demand backups. To optimize for storage, whenever possible, multiple
   * schedules are collapsed together to create one backup. In such cases, this
   * field captures the list of all backup schedule URIs that are associated
   * with creating this backup. If collapsing is not done, then this field
   * captures the single backup schedule URI associated with creating this
   * backup.
   *
   * @param string[] $backupSchedules
   */
  public function setBackupSchedules($backupSchedules)
  {
    $this->backupSchedules = $backupSchedules;
  }
  /**
   * @return string[]
   */
  public function getBackupSchedules()
  {
    return $this->backupSchedules;
  }
  /**
   * Output only. The time the CreateBackup request is received. If the request
   * does not specify `version_time`, the `version_time` of the backup will be
   * equivalent to the `create_time`.
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
   * Required for the CreateBackup operation. Name of the database from which
   * this backup was created. This needs to be in the same instance as the
   * backup. Values are of the form `projects//instances//databases/`.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * Output only. The database dialect information for the backup.
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
   * Output only. The encryption information for the backup.
   *
   * @param EncryptionInfo $encryptionInfo
   */
  public function setEncryptionInfo(EncryptionInfo $encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Output only. The encryption information for the backup, whether it is
   * protected by one or more KMS keys. The information includes all Cloud KMS
   * key versions used to encrypt the backup. The `encryption_status` field
   * inside of each `EncryptionInfo` is not populated. At least one of the key
   * versions must be available for the backup to be restored. If a key version
   * is revoked in the middle of a restore, the restore behavior is undefined.
   *
   * @param EncryptionInfo[] $encryptionInformation
   */
  public function setEncryptionInformation($encryptionInformation)
  {
    $this->encryptionInformation = $encryptionInformation;
  }
  /**
   * @return EncryptionInfo[]
   */
  public function getEncryptionInformation()
  {
    return $this->encryptionInformation;
  }
  /**
   * Output only. For a backup in an incremental backup chain, this is the
   * storage space needed to keep the data that has changed since the previous
   * backup. For all other backups, this is always the size of the backup. This
   * value may change if backups on the same chain get deleted or expired. This
   * field can be used to calculate the total storage space used by a set of
   * backups. For example, the total space used by all backups of a database can
   * be computed by summing up this field.
   *
   * @param string $exclusiveSizeBytes
   */
  public function setExclusiveSizeBytes($exclusiveSizeBytes)
  {
    $this->exclusiveSizeBytes = $exclusiveSizeBytes;
  }
  /**
   * @return string
   */
  public function getExclusiveSizeBytes()
  {
    return $this->exclusiveSizeBytes;
  }
  /**
   * Required for the CreateBackup operation. The expiration time of the backup,
   * with microseconds granularity that must be at least 6 hours and at most 366
   * days from the time the CreateBackup request is processed. Once the
   * `expire_time` has passed, the backup is eligible to be automatically
   * deleted by Cloud Spanner to free the resources used by the backup.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. The number of bytes that will be freed by deleting this
   * backup. This value will be zero if, for example, this backup is part of an
   * incremental backup chain and younger backups in the chain require that we
   * keep its data. For backups not in an incremental backup chain, this is
   * always the size of the backup. This value may change if backups on the same
   * chain get created, deleted or expired.
   *
   * @param string $freeableSizeBytes
   */
  public function setFreeableSizeBytes($freeableSizeBytes)
  {
    $this->freeableSizeBytes = $freeableSizeBytes;
  }
  /**
   * @return string
   */
  public function getFreeableSizeBytes()
  {
    return $this->freeableSizeBytes;
  }
  /**
   * Output only. Populated only for backups in an incremental backup chain.
   * Backups share the same chain id if and only if they belong to the same
   * incremental backup chain. Use this field to determine which backups are
   * part of the same incremental backup chain. The ordering of backups in the
   * chain can be determined by ordering the backup `version_time`.
   *
   * @param string $incrementalBackupChainId
   */
  public function setIncrementalBackupChainId($incrementalBackupChainId)
  {
    $this->incrementalBackupChainId = $incrementalBackupChainId;
  }
  /**
   * @return string
   */
  public function getIncrementalBackupChainId()
  {
    return $this->incrementalBackupChainId;
  }
  /**
   * Output only. The instance partition storing the backup. This is the same as
   * the list of the instance partitions that the database recorded at the
   * backup's `version_time`.
   *
   * @param BackupInstancePartition[] $instancePartitions
   */
  public function setInstancePartitions($instancePartitions)
  {
    $this->instancePartitions = $instancePartitions;
  }
  /**
   * @return BackupInstancePartition[]
   */
  public function getInstancePartitions()
  {
    return $this->instancePartitions;
  }
  /**
   * Output only. The max allowed expiration time of the backup, with
   * microseconds granularity. A backup's expiration time can be configured in
   * multiple APIs: CreateBackup, UpdateBackup, CopyBackup. When updating or
   * copying an existing backup, the expiration time specified must be less than
   * `Backup.max_expire_time`.
   *
   * @param string $maxExpireTime
   */
  public function setMaxExpireTime($maxExpireTime)
  {
    $this->maxExpireTime = $maxExpireTime;
  }
  /**
   * @return string
   */
  public function getMaxExpireTime()
  {
    return $this->maxExpireTime;
  }
  /**
   * Output only for the CreateBackup operation. Required for the UpdateBackup
   * operation. A globally unique identifier for the backup which cannot be
   * changed. Values are of the form `projects//instances//backups/a-z*[a-z0-9]`
   * The final segment of the name must be between 2 and 60 characters in
   * length. The backup is stored in the location(s) specified in the instance
   * configuration of the instance containing the backup, identified by the
   * prefix of the backup name of the form `projects//instances/`.
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
   * Output only. Data deleted at a time older than this is guaranteed not to be
   * retained in order to support this backup. For a backup in an incremental
   * backup chain, this is the version time of the oldest backup that exists or
   * ever existed in the chain. For all other backups, this is the version time
   * of the backup. This field can be used to understand what data is being
   * retained by the backup system.
   *
   * @param string $oldestVersionTime
   */
  public function setOldestVersionTime($oldestVersionTime)
  {
    $this->oldestVersionTime = $oldestVersionTime;
  }
  /**
   * @return string
   */
  public function getOldestVersionTime()
  {
    return $this->oldestVersionTime;
  }
  /**
   * Output only. The names of the destination backups being created by copying
   * this source backup. The backup names are of the form
   * `projects//instances//backups/`. Referencing backups may exist in different
   * instances. The existence of any referencing backup prevents the backup from
   * being deleted. When the copy operation is done (either successfully
   * completed or cancelled or the destination backup is deleted), the reference
   * to the backup is removed.
   *
   * @param string[] $referencingBackups
   */
  public function setReferencingBackups($referencingBackups)
  {
    $this->referencingBackups = $referencingBackups;
  }
  /**
   * @return string[]
   */
  public function getReferencingBackups()
  {
    return $this->referencingBackups;
  }
  /**
   * Output only. The names of the restored databases that reference the backup.
   * The database names are of the form `projects//instances//databases/`.
   * Referencing databases may exist in different instances. The existence of
   * any referencing database prevents the backup from being deleted. When a
   * restored database from the backup enters the `READY` state, the reference
   * to the backup is removed.
   *
   * @param string[] $referencingDatabases
   */
  public function setReferencingDatabases($referencingDatabases)
  {
    $this->referencingDatabases = $referencingDatabases;
  }
  /**
   * @return string[]
   */
  public function getReferencingDatabases()
  {
    return $this->referencingDatabases;
  }
  /**
   * Output only. Size of the backup in bytes. For a backup in an incremental
   * backup chain, this is the sum of the `exclusive_size_bytes` of itself and
   * all older backups in the chain.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. The current state of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY
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
   * The backup will contain an externally consistent copy of the database at
   * the timestamp specified by `version_time`. If `version_time` is not
   * specified, the system will set `version_time` to the `create_time` of the
   * backup.
   *
   * @param string $versionTime
   */
  public function setVersionTime($versionTime)
  {
    $this->versionTime = $versionTime;
  }
  /**
   * @return string
   */
  public function getVersionTime()
  {
    return $this->versionTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_Spanner_Backup');
