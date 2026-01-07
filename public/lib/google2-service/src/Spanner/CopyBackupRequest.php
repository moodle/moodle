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

class CopyBackupRequest extends \Google\Model
{
  /**
   * Required. The id of the backup copy. The `backup_id` appended to `parent`
   * forms the full backup_uri of the form `projects//instances//backups/`.
   *
   * @var string
   */
  public $backupId;
  protected $encryptionConfigType = CopyBackupEncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  /**
   * Required. The expiration time of the backup in microsecond granularity. The
   * expiration time must be at least 6 hours and at most 366 days from the
   * `create_time` of the source backup. Once the `expire_time` has passed, the
   * backup is eligible to be automatically deleted by Cloud Spanner to free the
   * resources used by the backup.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Required. The source backup to be copied. The source backup needs to be in
   * READY state for it to be copied. Once CopyBackup is in progress, the source
   * backup cannot be deleted or cleaned up on expiration until CopyBackup is
   * finished. Values are of the form: `projects//instances//backups/`.
   *
   * @var string
   */
  public $sourceBackup;

  /**
   * Required. The id of the backup copy. The `backup_id` appended to `parent`
   * forms the full backup_uri of the form `projects//instances//backups/`.
   *
   * @param string $backupId
   */
  public function setBackupId($backupId)
  {
    $this->backupId = $backupId;
  }
  /**
   * @return string
   */
  public function getBackupId()
  {
    return $this->backupId;
  }
  /**
   * Optional. The encryption configuration used to encrypt the backup. If this
   * field is not specified, the backup will use the same encryption
   * configuration as the source backup by default, namely encryption_type =
   * `USE_CONFIG_DEFAULT_OR_BACKUP_ENCRYPTION`.
   *
   * @param CopyBackupEncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(CopyBackupEncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return CopyBackupEncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Required. The expiration time of the backup in microsecond granularity. The
   * expiration time must be at least 6 hours and at most 366 days from the
   * `create_time` of the source backup. Once the `expire_time` has passed, the
   * backup is eligible to be automatically deleted by Cloud Spanner to free the
   * resources used by the backup.
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
   * Required. The source backup to be copied. The source backup needs to be in
   * READY state for it to be copied. Once CopyBackup is in progress, the source
   * backup cannot be deleted or cleaned up on expiration until CopyBackup is
   * finished. Values are of the form: `projects//instances//backups/`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CopyBackupRequest::class, 'Google_Service_Spanner_CopyBackupRequest');
