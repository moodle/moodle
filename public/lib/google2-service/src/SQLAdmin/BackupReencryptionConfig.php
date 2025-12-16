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

class BackupReencryptionConfig extends \Google\Model
{
  /**
   * Unknown backup type, will be defaulted to AUTOMATIC backup type
   */
  public const BACKUP_TYPE_BACKUP_TYPE_UNSPECIFIED = 'BACKUP_TYPE_UNSPECIFIED';
  /**
   * Reencrypt automatic backups
   */
  public const BACKUP_TYPE_AUTOMATED = 'AUTOMATED';
  /**
   * Reencrypt on-demand backups
   */
  public const BACKUP_TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Backup re-encryption limit
   *
   * @var int
   */
  public $backupLimit;
  /**
   * Type of backups users want to re-encrypt.
   *
   * @var string
   */
  public $backupType;

  /**
   * Backup re-encryption limit
   *
   * @param int $backupLimit
   */
  public function setBackupLimit($backupLimit)
  {
    $this->backupLimit = $backupLimit;
  }
  /**
   * @return int
   */
  public function getBackupLimit()
  {
    return $this->backupLimit;
  }
  /**
   * Type of backups users want to re-encrypt.
   *
   * Accepted values: BACKUP_TYPE_UNSPECIFIED, AUTOMATED, ON_DEMAND
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupReencryptionConfig::class, 'Google_Service_SQLAdmin_BackupReencryptionConfig');
