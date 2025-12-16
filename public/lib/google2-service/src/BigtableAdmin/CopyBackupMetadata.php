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

class CopyBackupMetadata extends \Google\Model
{
  /**
   * The name of the backup being created through the copy operation. Values are
   * of the form `projects//instances//clusters//backups/`.
   *
   * @var string
   */
  public $name;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  protected $sourceBackupInfoType = BackupInfo::class;
  protected $sourceBackupInfoDataType = '';

  /**
   * The name of the backup being created through the copy operation. Values are
   * of the form `projects//instances//clusters//backups/`.
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
   * The progress of the CopyBackup operation.
   *
   * @param OperationProgress $progress
   */
  public function setProgress(OperationProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return OperationProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Information about the source backup that is being copied from.
   *
   * @param BackupInfo $sourceBackupInfo
   */
  public function setSourceBackupInfo(BackupInfo $sourceBackupInfo)
  {
    $this->sourceBackupInfo = $sourceBackupInfo;
  }
  /**
   * @return BackupInfo
   */
  public function getSourceBackupInfo()
  {
    return $this->sourceBackupInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CopyBackupMetadata::class, 'Google_Service_BigtableAdmin_CopyBackupMetadata');
