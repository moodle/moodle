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

class BackupInfo extends \Google\Model
{
  /**
   * Output only. Name of the backup.
   *
   * @var string
   */
  public $backup;
  /**
   * Output only. This time that the backup was finished. Row data in the backup
   * will be no newer than this timestamp.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Name of the backup from which this backup was copied. If a
   * backup is not created by copying a backup, this field will be empty. Values
   * are of the form: projects//instances//clusters//backups/
   *
   * @var string
   */
  public $sourceBackup;
  /**
   * Output only. Name of the table the backup was created from.
   *
   * @var string
   */
  public $sourceTable;
  /**
   * Output only. The time that the backup was started. Row data in the backup
   * will be no older than this timestamp.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. Name of the backup.
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
   * Output only. This time that the backup was finished. Row data in the backup
   * will be no newer than this timestamp.
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
   * Output only. Name of the table the backup was created from.
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
   * Output only. The time that the backup was started. Row data in the backup
   * will be no older than this timestamp.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackupInfo::class, 'Google_Service_BigtableAdmin_BackupInfo');
