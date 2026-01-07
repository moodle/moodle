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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1CloneDatabaseMetadata extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const OPERATION_STATE_OPERATION_STATE_UNSPECIFIED = 'OPERATION_STATE_UNSPECIFIED';
  /**
   * Request is being prepared for processing.
   */
  public const OPERATION_STATE_INITIALIZING = 'INITIALIZING';
  /**
   * Request is actively being processed.
   */
  public const OPERATION_STATE_PROCESSING = 'PROCESSING';
  /**
   * Request is in the process of being cancelled after user called
   * google.longrunning.Operations.CancelOperation on the operation.
   */
  public const OPERATION_STATE_CANCELLING = 'CANCELLING';
  /**
   * Request has been processed and is in its finalization stage.
   */
  public const OPERATION_STATE_FINALIZING = 'FINALIZING';
  /**
   * Request has completed successfully.
   */
  public const OPERATION_STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * Request has finished being processed, but encountered an error.
   */
  public const OPERATION_STATE_FAILED = 'FAILED';
  /**
   * Request has finished being cancelled after user called
   * google.longrunning.Operations.CancelOperation.
   */
  public const OPERATION_STATE_CANCELLED = 'CANCELLED';
  /**
   * The name of the database being cloned to.
   *
   * @var string
   */
  public $database;
  /**
   * The time the clone finished, unset for ongoing clones.
   *
   * @var string
   */
  public $endTime;
  /**
   * The operation state of the clone.
   *
   * @var string
   */
  public $operationState;
  protected $pitrSnapshotType = GoogleFirestoreAdminV1PitrSnapshot::class;
  protected $pitrSnapshotDataType = '';
  protected $progressPercentageType = GoogleFirestoreAdminV1Progress::class;
  protected $progressPercentageDataType = '';
  /**
   * The time the clone was started.
   *
   * @var string
   */
  public $startTime;

  /**
   * The name of the database being cloned to.
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
   * The time the clone finished, unset for ongoing clones.
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
   * The operation state of the clone.
   *
   * Accepted values: OPERATION_STATE_UNSPECIFIED, INITIALIZING, PROCESSING,
   * CANCELLING, FINALIZING, SUCCESSFUL, FAILED, CANCELLED
   *
   * @param self::OPERATION_STATE_* $operationState
   */
  public function setOperationState($operationState)
  {
    $this->operationState = $operationState;
  }
  /**
   * @return self::OPERATION_STATE_*
   */
  public function getOperationState()
  {
    return $this->operationState;
  }
  /**
   * The snapshot from which this database was cloned.
   *
   * @param GoogleFirestoreAdminV1PitrSnapshot $pitrSnapshot
   */
  public function setPitrSnapshot(GoogleFirestoreAdminV1PitrSnapshot $pitrSnapshot)
  {
    $this->pitrSnapshot = $pitrSnapshot;
  }
  /**
   * @return GoogleFirestoreAdminV1PitrSnapshot
   */
  public function getPitrSnapshot()
  {
    return $this->pitrSnapshot;
  }
  /**
   * How far along the clone is as an estimated percentage of remaining time.
   *
   * @param GoogleFirestoreAdminV1Progress $progressPercentage
   */
  public function setProgressPercentage(GoogleFirestoreAdminV1Progress $progressPercentage)
  {
    $this->progressPercentage = $progressPercentage;
  }
  /**
   * @return GoogleFirestoreAdminV1Progress
   */
  public function getProgressPercentage()
  {
    return $this->progressPercentage;
  }
  /**
   * The time the clone was started.
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
class_alias(GoogleFirestoreAdminV1CloneDatabaseMetadata::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1CloneDatabaseMetadata');
