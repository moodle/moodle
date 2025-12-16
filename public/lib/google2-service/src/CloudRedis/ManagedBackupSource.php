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

namespace Google\Service\CloudRedis;

class ManagedBackupSource extends \Google\Model
{
  /**
   * Optional. Example: //redis.googleapis.com/projects/{project}/locations/{loc
   * ation}/backupCollections/{collection}/backups/{backup} A shorter version
   * (without the prefix) of the backup name is also supported, like projects/{p
   * roject}/locations/{location}/backupCollections/{collection}/backups/{backup
   * _id} In this case, it assumes the backup is under redis.googleapis.com.
   *
   * @var string
   */
  public $backup;

  /**
   * Optional. Example: //redis.googleapis.com/projects/{project}/locations/{loc
   * ation}/backupCollections/{collection}/backups/{backup} A shorter version
   * (without the prefix) of the backup name is also supported, like projects/{p
   * roject}/locations/{location}/backupCollections/{collection}/backups/{backup
   * _id} In this case, it assumes the backup is under redis.googleapis.com.
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
class_alias(ManagedBackupSource::class, 'Google_Service_CloudRedis_ManagedBackupSource');
