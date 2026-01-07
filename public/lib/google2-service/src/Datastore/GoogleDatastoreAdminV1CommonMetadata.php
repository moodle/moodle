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

namespace Google\Service\Datastore;

class GoogleDatastoreAdminV1CommonMetadata extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const OPERATION_TYPE_OPERATION_TYPE_UNSPECIFIED = 'OPERATION_TYPE_UNSPECIFIED';
  /**
   * ExportEntities.
   */
  public const OPERATION_TYPE_EXPORT_ENTITIES = 'EXPORT_ENTITIES';
  /**
   * ImportEntities.
   */
  public const OPERATION_TYPE_IMPORT_ENTITIES = 'IMPORT_ENTITIES';
  /**
   * CreateIndex.
   */
  public const OPERATION_TYPE_CREATE_INDEX = 'CREATE_INDEX';
  /**
   * DeleteIndex.
   */
  public const OPERATION_TYPE_DELETE_INDEX = 'DELETE_INDEX';
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Request is being prepared for processing.
   */
  public const STATE_INITIALIZING = 'INITIALIZING';
  /**
   * Request is actively being processed.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * Request is in the process of being cancelled after user called
   * google.longrunning.Operations.CancelOperation on the operation.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * Request has been processed and is in its finalization stage.
   */
  public const STATE_FINALIZING = 'FINALIZING';
  /**
   * Request has completed successfully.
   */
  public const STATE_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * Request has finished being processed, but encountered an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Request has finished being cancelled after user called
   * google.longrunning.Operations.CancelOperation.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The time the operation ended, either successfully or otherwise.
   *
   * @var string
   */
  public $endTime;
  /**
   * The client-assigned labels which were provided when the operation was
   * created. May also include additional labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The type of the operation. Can be used as a filter in
   * ListOperationsRequest.
   *
   * @var string
   */
  public $operationType;
  /**
   * The time that work began on the operation.
   *
   * @var string
   */
  public $startTime;
  /**
   * The current state of the Operation.
   *
   * @var string
   */
  public $state;

  /**
   * The time the operation ended, either successfully or otherwise.
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
   * The client-assigned labels which were provided when the operation was
   * created. May also include additional labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The type of the operation. Can be used as a filter in
   * ListOperationsRequest.
   *
   * Accepted values: OPERATION_TYPE_UNSPECIFIED, EXPORT_ENTITIES,
   * IMPORT_ENTITIES, CREATE_INDEX, DELETE_INDEX
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * The time that work began on the operation.
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
  /**
   * The current state of the Operation.
   *
   * Accepted values: STATE_UNSPECIFIED, INITIALIZING, PROCESSING, CANCELLING,
   * FINALIZING, SUCCESSFUL, FAILED, CANCELLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDatastoreAdminV1CommonMetadata::class, 'Google_Service_Datastore_GoogleDatastoreAdminV1CommonMetadata');
