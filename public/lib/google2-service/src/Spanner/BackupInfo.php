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

class BackupInfo extends \Google\Model
{
  /**
   * Name of the backup.
   *
   * @var string
   */
  public $backup;
  /**
   * The time the CreateBackup request was received.
   *
   * @var string
   */
  public $createTime;
  /**
   * Name of the database the backup was created from.
   *
   * @var string
   */
  public $sourceDatabase;
  /**
   * The backup contains an externally consistent copy of `source_database` at
   * the timestamp specified by `version_time`. If the CreateBackup request did
   * not specify `version_time`, the `version_time` of the backup is equivalent
   * to the `create_time`.
   *
   * @var string
   */
  public $versionTime;

  /**
   * Name of the backup.
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
  /**
   * The time the CreateBackup request was received.
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
   * Name of the database the backup was created from.
   *
   * @param string $sourceDatabase
   */
  public function setSourceDatabase($sourceDatabase)
  {
    $this->sourceDatabase = $sourceDatabase;
  }
  /**
   * @return string
   */
  public function getSourceDatabase()
  {
    return $this->sourceDatabase;
  }
  /**
   * The backup contains an externally consistent copy of `source_database` at
   * the timestamp specified by `version_time`. If the CreateBackup request did
   * not specify `version_time`, the `version_time` of the backup is equivalent
   * to the `create_time`.
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
class_alias(BackupInfo::class, 'Google_Service_Spanner_BackupInfo');
