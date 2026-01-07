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

class BackupDrBackupSource extends \Google\Model
{
  /**
   * Required. The name of the backup resource with the format: * projects/{proj
   * ect}/locations/{location}/backupVaults/{backupvault_id}/dataSources/{dataso
   * urce_id}/backups/{backup_id}
   *
   * @var string
   */
  public $backup;

  /**
   * Required. The name of the backup resource with the format: * projects/{proj
   * ect}/locations/{location}/backupVaults/{backupvault_id}/dataSources/{dataso
   * urce_id}/backups/{backup_id}
   *
   * @param string $backup
   */
  public function setBackup($backup)
  {
    $this->backup = $backup;
  }
  /**
   * @return string
   */
  public function getBackup()
  {
    return $this->backup;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupDrBackupSource::class, 'Google_Service_CloudAlloyDBAdmin_BackupDrBackupSource');
