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

class RestoreDatabaseMetadata extends \Google\Model
{
  /**
   * No restore associated.
   */
  public const SOURCE_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A backup was used as the source of the restore.
   */
  public const SOURCE_TYPE_BACKUP = 'BACKUP';
  protected $backupInfoType = BackupInfo::class;
  protected $backupInfoDataType = '';
  /**
   * The time at which cancellation of this operation was received.
   * Operations.CancelOperation starts asynchronous cancellation on a long-
   * running operation. The server makes a best effort to cancel the operation,
   * but success is not guaranteed. Clients can use Operations.GetOperation or
   * other methods to check whether the cancellation succeeded or whether the
   * operation completed despite cancellation. On successful cancellation, the
   * operation is not deleted; instead, it becomes an operation with an
   * Operation.error value with a google.rpc.Status.code of 1, corresponding to
   * `Code.CANCELLED`.
   *
   * @var string
   */
  public $cancelTime;
  /**
   * Name of the database being created and restored to.
   *
   * @var string
   */
  public $name;
  /**
   * If exists, the name of the long-running operation that will be used to
   * track the post-restore optimization process to optimize the performance of
   * the restored database, and remove the dependency on the restore source. The
   * name is of the form `projects//instances//databases//operations/` where the
   * is the name of database being created and restored to. The metadata type of
   * the long-running operation is OptimizeRestoredDatabaseMetadata. This long-
   * running operation will be automatically created by the system after the
   * RestoreDatabase long-running operation completes successfully. This
   * operation will not be created if the restore was not successful.
   *
   * @var string
   */
  public $optimizeDatabaseOperationName;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  /**
   * The type of the restore source.
   *
   * @var string
   */
  public $sourceType;

  /**
   * Information about the backup used to restore the database.
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
   * The time at which cancellation of this operation was received.
   * Operations.CancelOperation starts asynchronous cancellation on a long-
   * running operation. The server makes a best effort to cancel the operation,
   * but success is not guaranteed. Clients can use Operations.GetOperation or
   * other methods to check whether the cancellation succeeded or whether the
   * operation completed despite cancellation. On successful cancellation, the
   * operation is not deleted; instead, it becomes an operation with an
   * Operation.error value with a google.rpc.Status.code of 1, corresponding to
   * `Code.CANCELLED`.
   *
   * @param string $cancelTime
   */
  public function setCancelTime($cancelTime)
  {
    $this->cancelTime = $cancelTime;
  }
  /**
   * @return string
   */
  public function getCancelTime()
  {
    return $this->cancelTime;
  }
  /**
   * Name of the database being created and restored to.
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
   * the restored database, and remove the dependency on the restore source. The
   * name is of the form `projects//instances//databases//operations/` where the
   * is the name of database being created and restored to. The metadata type of
   * the long-running operation is OptimizeRestoredDatabaseMetadata. This long-
   * running operation will be automatically created by the system after the
   * RestoreDatabase long-running operation completes successfully. This
   * operation will not be created if the restore was not successful.
   *
   * @param string $optimizeDatabaseOperationName
   */
  public function setOptimizeDatabaseOperationName($optimizeDatabaseOperationName)
  {
    $this->optimizeDatabaseOperationName = $optimizeDatabaseOperationName;
  }
  /**
   * @return string
   */
  public function getOptimizeDatabaseOperationName()
  {
    return $this->optimizeDatabaseOperationName;
  }
  /**
   * The progress of the RestoreDatabase operation.
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
   * Accepted values: TYPE_UNSPECIFIED, BACKUP
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
class_alias(RestoreDatabaseMetadata::class, 'Google_Service_Spanner_RestoreDatabaseMetadata');
