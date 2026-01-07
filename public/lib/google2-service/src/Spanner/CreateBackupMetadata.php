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

class CreateBackupMetadata extends \Google\Model
{
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
   * The name of the database the backup is created from.
   *
   * @var string
   */
  public $database;
  /**
   * The name of the backup being created.
   *
   * @var string
   */
  public $name;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';

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
   * The name of the database the backup is created from.
   *
   * @param string $database
   */
  public function setDatabase($database)
  {
    $this->database = $database;
  }
  /**
   * @return string
   */
  public function getDatabase()
  {
    return $this->database;
  }
  /**
   * The name of the backup being created.
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
   * The progress of the CreateBackup operation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateBackupMetadata::class, 'Google_Service_Spanner_CreateBackupMetadata');
