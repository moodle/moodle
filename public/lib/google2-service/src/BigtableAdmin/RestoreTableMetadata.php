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

class RestoreTableMetadata extends \Google\Model
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
   * Name of the table being created and restored to.
   *
   * @var string
   */
  public $name;
  /**
   * If exists, the name of the long-running operation that will be used to
   * track the post-restore optimization process to optimize the performance of
   * the restored table. The metadata type of the long-running operation is
   * OptimizeRestoredTableMetadata. The response type is Empty. This long-
   * running operation may be automatically created by the system if applicable
   * after the RestoreTable long-running operation completes successfully. This
   * operation may not be created if the table is already optimized or the
   * restore was not successful.
   *
   * @var string
   */
  public $optimizeTableOperationName;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  /**
   * The type of the restore source.
   *
   * @var string
   */
  public $sourceType;

  /**
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
   * Name of the table being created and restored to.
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
   * If exists, the name of the long-running operation that will be used to
   * track the post-restore optimization process to optimize the performance of
   * the restored table. The metadata type of the long-running operation is
   * OptimizeRestoredTableMetadata. The response type is Empty. This long-
   * running operation may be automatically created by the system if applicable
   * after the RestoreTable long-running operation completes successfully. This
   * operation may not be created if the table is already optimized or the
   * restore was not successful.
   *
   * @param string $optimizeTableOperationName
   */
  public function setOptimizeTableOperationName($optimizeTableOperationName)
  {
    $this->optimizeTableOperationName = $optimizeTableOperationName;
  }
  /**
   * @return string
   */
  public function getOptimizeTableOperationName()
  {
    return $this->optimizeTableOperationName;
  }
  /**
   * The progress of the RestoreTable operation.
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
class_alias(RestoreTableMetadata::class, 'Google_Service_BigtableAdmin_RestoreTableMetadata');
