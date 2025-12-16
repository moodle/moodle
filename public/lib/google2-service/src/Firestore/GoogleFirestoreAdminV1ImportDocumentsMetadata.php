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

class GoogleFirestoreAdminV1ImportDocumentsMetadata extends \Google\Collection
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
  protected $collection_key = 'namespaceIds';
  /**
   * Which collection IDs are being imported.
   *
   * @var string[]
   */
  public $collectionIds;
  /**
   * The time this operation completed. Will be unset if operation still in
   * progress.
   *
   * @var string
   */
  public $endTime;
  /**
   * The location of the documents being imported.
   *
   * @var string
   */
  public $inputUriPrefix;
  /**
   * Which namespace IDs are being imported.
   *
   * @var string[]
   */
  public $namespaceIds;
  /**
   * The state of the import operation.
   *
   * @var string
   */
  public $operationState;
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
   * Which collection IDs are being imported.
   *
   * @param string[] $collectionIds
   */
  public function setCollectionIds($collectionIds)
  {
    $this->collectionIds = $collectionIds;
  }
  /**
   * @return string[]
   */
  public function getCollectionIds()
  {
    return $this->collectionIds;
  }
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
   * The location of the documents being imported.
   *
   * @param string $inputUriPrefix
   */
  public function setInputUriPrefix($inputUriPrefix)
  {
    $this->inputUriPrefix = $inputUriPrefix;
  }
  /**
   * @return string
   */
  public function getInputUriPrefix()
  {
    return $this->inputUriPrefix;
  }
  /**
   * Which namespace IDs are being imported.
   *
   * @param string[] $namespaceIds
   */
  public function setNamespaceIds($namespaceIds)
  {
    $this->namespaceIds = $namespaceIds;
  }
  /**
   * @return string[]
   */
  public function getNamespaceIds()
  {
    return $this->namespaceIds;
  }
  /**
   * The state of the import operation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1ImportDocumentsMetadata::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1ImportDocumentsMetadata');
