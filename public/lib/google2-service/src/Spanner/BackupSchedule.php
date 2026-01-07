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

class BackupSchedule extends \Google\Model
{
  protected $encryptionConfigType = CreateBackupEncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $fullBackupSpecType = FullBackupSpec::class;
  protected $fullBackupSpecDataType = '';
  protected $incrementalBackupSpecType = IncrementalBackupSpec::class;
  protected $incrementalBackupSpecDataType = '';
  /**
   * Identifier. Output only for the CreateBackupSchedule operation. Required
   * for the UpdateBackupSchedule operation. A globally unique identifier for
   * the backup schedule which cannot be changed. Values are of the form
   * `projects//instances//databases//backupSchedules/a-z*[a-z0-9]` The final
   * segment of the name must be between 2 and 60 characters in length.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The retention duration of a backup that must be at least 6 hours
   * and at most 366 days. The backup is eligible to be automatically deleted
   * once the retention period has elapsed.
   *
   * @var string
   */
  public $retentionDuration;
  protected $specType = BackupScheduleSpec::class;
  protected $specDataType = '';
  /**
   * Output only. The timestamp at which the schedule was last updated. If the
   * schedule has never been updated, this field contains the timestamp when the
   * schedule was first created.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The encryption configuration that is used to encrypt the backup.
   * If this field is not specified, the backup uses the same encryption
   * configuration as the database.
   *
   * @param CreateBackupEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(CreateBackupEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return CreateBackupEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * The schedule creates only full backups.
   *
   * @param FullBackupSpec $fullBackupSpec
   */
  public function setFullBackupSpec(FullBackupSpec $fullBackupSpec)
  {
    $this->fullBackupSpec = $fullBackupSpec;
  }
  /**
   * @return FullBackupSpec
   */
  public function getFullBackupSpec()
  {
    return $this->fullBackupSpec;
  }
  /**
   * The schedule creates incremental backup chains.
   *
   * @param IncrementalBackupSpec $incrementalBackupSpec
   */
  public function setIncrementalBackupSpec(IncrementalBackupSpec $incrementalBackupSpec)
  {
    $this->incrementalBackupSpec = $incrementalBackupSpec;
  }
  /**
   * @return IncrementalBackupSpec
   */
  public function getIncrementalBackupSpec()
  {
    return $this->incrementalBackupSpec;
  }
  /**
   * Identifier. Output only for the CreateBackupSchedule operation. Required
   * for the UpdateBackupSchedule operation. A globally unique identifier for
   * the backup schedule which cannot be changed. Values are of the form
   * `projects//instances//databases//backupSchedules/a-z*[a-z0-9]` The final
   * segment of the name must be between 2 and 60 characters in length.
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
   * Optional. The retention duration of a backup that must be at least 6 hours
   * and at most 366 days. The backup is eligible to be automatically deleted
   * once the retention period has elapsed.
   *
   * @param string $retentionDuration
   */
  public function setRetentionDuration($retentionDuration)
  {
    $this->retentionDuration = $retentionDuration;
  }
  /**
   * @return string
   */
  public function getRetentionDuration()
  {
    return $this->retentionDuration;
  }
  /**
   * Optional. The schedule specification based on which the backup creations
   * are triggered.
   *
   * @param BackupScheduleSpec $spec
   */
  public function setSpec(BackupScheduleSpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return BackupScheduleSpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
  /**
   * Output only. The timestamp at which the schedule was last updated. If the
   * schedule has never been updated, this field contains the timestamp when the
   * schedule was first created.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupSchedule::class, 'Google_Service_Spanner_BackupSchedule');
