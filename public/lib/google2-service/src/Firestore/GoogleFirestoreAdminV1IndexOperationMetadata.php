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

class GoogleFirestoreAdminV1IndexOperationMetadata extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_OPERATION_STATE_UNSPECIFIED = 'OPERATION_STATE_UNSPECIFIED';
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
   * The time this operation completed. Will be unset if operation still in
   * progress.
   *
   * @var string
   */
  public $endTime;
  /**
   * The index resource that this operation is acting on. For example: `projects
   * /{project_id}/databases/{database_id}/collectionGroups/{collection_id}/inde
   * xes/{index_id}`
   *
   * @var string
   */
  public $index;
  protected $progressBytesType = GoogleFirestoreAdminV1Progress::class;
  protected $progressBytesDataType = '';
  protected $progressDocumentsType = GoogleFirestoreAdminV1Progress::class;
  protected $progressDocumentsDataType = '';
  /**
   * The time this operation started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The state of the operation.
   *
   * @var string
   */
  public $state;

  /**
   * The time this operation completed. Will be unset if operation still in
   * progress.
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
   * The index resource that this operation is acting on. For example: `projects
   * /{project_id}/databases/{database_id}/collectionGroups/{collection_id}/inde
   * xes/{index_id}`
   *
   * @param string $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return string
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * The progress, in bytes, of this operation.
   *
   * @param GoogleFirestoreAdminV1Progress $progressBytes
   */
  public function setProgressBytes(GoogleFirestoreAdminV1Progress $progressBytes)
  {
    $this->progressBytes = $progressBytes;
  }
  /**
   * @return GoogleFirestoreAdminV1Progress
   */
  public function getProgressBytes()
  {
    return $this->progressBytes;
  }
  /**
   * The progress, in documents, of this operation.
   *
   * @param GoogleFirestoreAdminV1Progress $progressDocuments
   */
  public function setProgressDocuments(GoogleFirestoreAdminV1Progress $progressDocuments)
  {
    $this->progressDocuments = $progressDocuments;
  }
  /**
   * @return GoogleFirestoreAdminV1Progress
   */
  public function getProgressDocuments()
  {
    return $this->progressDocuments;
  }
  /**
   * The time this operation started.
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
   * The state of the operation.
   *
   * Accepted values: OPERATION_STATE_UNSPECIFIED, INITIALIZING, PROCESSING,
   * CANCELLING, FINALIZING, SUCCESSFUL, FAILED, CANCELLED
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
class_alias(GoogleFirestoreAdminV1IndexOperationMetadata::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1IndexOperationMetadata');
