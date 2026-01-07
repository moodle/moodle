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

class RestoreInfo extends \Google\Model
{
  /**
   * No restore associated.
   */
  public const SOURCE_TYPE_RESTORE_SOURCE_TYPE_UNSPECIFIED = 'RESTORE_SOURCE_TYPE_UNSPECIFIED';
  /**
   * A backup was used as the source of the restore.
   */
  public const SOURCE_TYPE_BACKUP = 'BACKUP';
  protected $backupInfoType = BackupInfo::class;
  protected $backupInfoDataType = '';
  /**
   * The type of the restore source.
   *
   * @var string
   */
  public $sourceType;

  /**
   * Information about the backup used to restore the table. The backup may no
   * longer exist.
   *
   * @param BackupInfo $backupInfo
   */
  public function setBackupInfo(BackupInfo $backupInfo)
  {
    $this->backupInfo = $backupInfo;
  }
  /**
   * @return BackupInfo
   */
  public function getBackupInfo()
  {
    return $this->backupInfo;
  }
  /**
   * The type of the restore source.
   *
   * Accepted values: RESTORE_SOURCE_TYPE_UNSPECIFIED, BACKUP
   *
   * @param self::SOURCE_TYPE_* $sourceType
   */
  public function setSourceType($sourceType)
  {
    $this->sourceType = $sourceType;
  }
  /**
   * @return self::SOURCE_TYPE_*
   */
  public function getSourceType()
  {
    return $this->sourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestoreInfo::class, 'Google_Service_BigtableAdmin_RestoreInfo');
