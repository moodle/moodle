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

namespace Google\Service\BigtableAdmin;

class Backup extends \Google\Model
{
  /**
   * Not specified.
   */
  public const BACKUP_TYPE_BACKUP_TYPE_UNSPECIFIED = 'BACKUP_TYPE_UNSPECIFIED';
  /**
   * The default type for Cloud Bigtable managed backups. Supported for backups
   * created in both HDD and SSD instances. Requires optimization when restored
   * to a table in an SSD instance.
   */
  public const BACKUP_TYPE_STANDARD = 'STANDARD';
  /**
   * A backup type with faster restore to SSD performance. Only supported for
   * backups created in SSD instances. A new SSD table restored from a hot
   * backup reaches production performance more quickly than a standard backup.
   */
  public const BACKUP_TYPE_HOT = 'HOT';
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
  /**
   * Indicates the backup type of the backup.
   *
   * @var string
   */
  public $backupType;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  /**
   * Output only. `end_time` is the time that the backup was finished. The row
   * data in the backup will be no newer than this timestamp.
   *
   * @var string
   */
  public $endTime;
  /**
   * Required. The expiration time of the backup. When creating a backup or
   * updating its `expire_time`, the value must be greater than the backup
   * creation time by: - At least 6 hours - At most 90 days Once the
   * `expire_time` has passed, Cloud Bigtable will delete the backup.
   *
   * @var string
   */
  public $expireTime;
  /**
   * The time at which the hot backup will be converted to a standard backup.
   * Once the `hot_to_standard_time` has passed, Cloud Bigtable will convert the
   * hot backup to a standard backup. This value must be greater than the backup
   * creation time by: - At least 24 hours This field only applies for hot
   * backups. When creating or updating a standard backup, attempting to set
   * this field will fail the request.
   *
   * @var string
   */
  public $hotToStandardTime;
  /**
   * A globally unique identifier for the backup which cannot be changed. Values
   * are of the form
   * `projects/{project}/instances/{instance}/clusters/{cluster}/ backups/_a-
   * zA-Z0-9*` The final segment of the name must be between 1 and 50 characters
   * in length. The backup is stored in the cluster identified by the prefix of
   * the backup name of the form
   * `projects/{project}/instances/{instance}/clusters/{cluster}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Size of the backup in bytes.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. Name of the backup from which this backup was copied. If a
   * backup is not created by copying a backup, this field will be empty. Values
   * are of the form: projects//instances//clusters//backups/
   *
   * @var string
   */
  public $sourceBackup;
  /**
   * Required. Immutable. Name of the table from which this backup was created.
   * This needs to be in the same instance as the backup. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{source_table}`.
   *
   * @var string
   */
  public $sourceTable;
  /**
   * Output only. `start_time` is the time that the backup was started (i.e.
   * approximately the time the CreateBackup request is received). The row data
   * in this backup will be no older than this timestamp.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current state of the backup.
   *
   * @var string
   */
  public $state;

  /**
   * Indicates the backup type of the backup.
   *
   * Accepted values: BACKUP_TYPE_UNSPECIFIED, STANDARD, HOT
   *
   * @param self::BACKUP_TYPE_* $backupType
   */
  public function setBackupType($backupType)
  {
    $this->backupType = $backupType;
  }
  /**
   * @return self::BACKUP_TYPE_*
   */
  public function getBackupType()
  {
    return $this->backupType;
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
   * Output only. `end_time` is the time that the backup was finished. The row
   * data in the backup will be no newer than this timestamp.
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
   * Required. The expiration time of the backup. When creating a backup or
   * updating its `expire_time`, the value must be greater than the backup
   * creation time by: - At least 6 hours - At most 90 days Once the
   * `expire_time` has passed, Cloud Bigtable will delete the backup.
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
   * The time at which the hot backup will be converted to a standard backup.
   * Once the `hot_to_standard_time` has passed, Cloud Bigtable will convert the
   * hot backup to a standard backup. This value must be greater than the backup
   * creation time by: - At least 24 hours This field only applies for hot
   * backups. When creating or updating a standard backup, attempting to set
   * this field will fail the request.
   *
   * @param string $hotToStandardTime
   */
  public function setHotToStandardTime($hotToStandardTime)
  {
    $this->hotToStandardTime = $hotToStandardTime;
  }
  /**
   * @return string
   */
  public function getHotToStandardTime()
  {
    return $this->hotToStandardTime;
  }
  /**
   * A globally unique identifier for the backup which cannot be changed. Values
   * are of the form
   * `projects/{project}/instances/{instance}/clusters/{cluster}/ backups/_a-
   * zA-Z0-9*` The final segment of the name must be between 1 and 50 characters
   * in length. The backup is stored in the cluster identified by the prefix of
   * the backup name of the form
   * `projects/{project}/instances/{instance}/clusters/{cluster}`.
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
   * Output only. Size of the backup in bytes.
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
   * Output only. Name of the backup from which this backup was copied. If a
   * backup is not created by copying a backup, this field will be empty. Values
   * are of the form: projects//instances//clusters//backups/
   *
   * @param string $sourceBackup
   */
  public function setSourceBackup($sourceBackup)
  {
    $this->sourceBackup = $sourceBackup;
  }
  /**
   * @return string
   */
  public function getSourceBackup()
  {
    return $this->sourceBackup;
  }
  /**
   * Required. Immutable. Name of the table from which this backup was created.
   * This needs to be in the same instance as the backup. Values are of the form
   * `projects/{project}/instances/{instance}/tables/{source_table}`.
   *
   * @param string $sourceTable
   */
  public function setSourceTable($sourceTable)
  {
    $this->sourceTable = $sourceTable;
  }
  /**
   * @return string
   */
  public function getSourceTable()
  {
    return $this->sourceTable;
  }
  /**
   * Output only. `start_time` is the time that the backup was started (i.e.
   * approximately the time the CreateBackup request is received). The row data
   * in this backup will be no older than this timestamp.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_BigtableAdmin_Backup');
