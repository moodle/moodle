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

namespace Google\Service\CloudAlloyDBAdmin;

class BackupSource extends \Google\Model
{
  /**
   * Required. The name of the backup resource with the format: *
   * projects/{project}/locations/{region}/backups/{backup_id}
   *
   * @var string
   */
  public $backupName;
  /**
   * Output only. The system-generated UID of the backup which was used to
   * create this resource. The UID is generated when the backup is created, and
   * it is retained until the backup is deleted.
   *
   * @var string
   */
  public $backupUid;

  /**
   * Required. The name of the backup resource with the format: *
   * projects/{project}/locations/{region}/backups/{backup_id}
   *
   * @param string $backupName
   */
  public function setBackupName($backupName)
  {
    $this->backupName = $backupName;
  }
  /**
   * @return string
   */
  public function getBackupName()
  {
    return $this->backupName;
  }
  /**
   * Output only. The system-generated UID of the backup which was used to
   * create this resource. The UID is generated when the backup is created, and
   * it is retained until the backup is deleted.
   *
   * @param string $backupUid
   */
  public function setBackupUid($backupUid)
  {
    $this->backupUid = $backupUid;
  }
  /**
   * @return string
   */
  public function getBackupUid()
  {
    return $this->backupUid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupSource::class, 'Google_Service_CloudAlloyDBAdmin_BackupSource');
