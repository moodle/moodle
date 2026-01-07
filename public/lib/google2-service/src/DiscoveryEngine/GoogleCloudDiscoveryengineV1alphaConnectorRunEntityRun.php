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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun extends \Google\Collection
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The data sync is ongoing.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The data sync is finished.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The data sync is failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Data sync has been running longer than expected and is still running at the
   * time the next run is supposed to start.
   */
  public const STATE_OVERRUN = 'OVERRUN';
  /**
   * Data sync was scheduled but has been cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Data sync is about to start.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The data sync completed with non-fatal errors.
   */
  public const STATE_WARNING = 'WARNING';
  /**
   * An ongoing connector run has been running longer than expected, causing
   * this run to be skipped.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Sync type unspecified.
   */
  public const SYNC_TYPE_SYNC_TYPE_UNSPECIFIED = 'SYNC_TYPE_UNSPECIFIED';
  /**
   * Sync triggers full sync of all documents.
   */
  public const SYNC_TYPE_FULL = 'FULL';
  /**
   * Incremental sync of updated documents.
   */
  public const SYNC_TYPE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Realtime sync.
   */
  public const SYNC_TYPE_REALTIME = 'REALTIME';
  /**
   * Scala sync.
   */
  public const SYNC_TYPE_SCALA_SYNC = 'SCALA_SYNC';
  protected $collection_key = 'errors';
  /**
   * Optional. The number of documents deleted.
   *
   * @var string
   */
  public $deletedRecordCount;
  /**
   * The name of the source entity.
   *
   * @var string
   */
  public $entityName;
  /**
   * Optional. The total number of documents failed at sync at indexing stage.
   *
   * @var string
   */
  public $errorRecordCount;
  protected $errorsType = GoogleRpcStatus::class;
  protected $errorsDataType = 'array';
  /**
   * Optional. The number of documents extracted from connector source, ready to
   * be ingested to VAIS.
   *
   * @var string
   */
  public $extractedRecordCount;
  /**
   * Optional. The number of documents indexed.
   *
   * @var string
   */
  public $indexedRecordCount;
  protected $progressType = GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress::class;
  protected $progressDataType = '';
  /**
   * Optional. The number of documents scheduled to be crawled/extracted from
   * connector source. This only applies to third party connectors.
   *
   * @var string
   */
  public $scheduledRecordCount;
  /**
   * Optional. The number of requests sent to 3p API.
   *
   * @var string
   */
  public $sourceApiRequestCount;
  /**
   * The state of the entity's sync run.
   *
   * @var string
   */
  public $state;
  /**
   * Timestamp at which the entity sync state was last updated.
   *
   * @var string
   */
  public $stateUpdateTime;
  /**
   * The timestamp for either extracted_documents_count, indexed_documents_count
   * and error_documents_count was last updated.
   *
   * @var string
   */
  public $statsUpdateTime;
  /**
   * Sync type of this run.
   *
   * @var string
   */
  public $syncType;

  /**
   * Optional. The number of documents deleted.
   *
   * @param string $deletedRecordCount
   */
  public function setDeletedRecordCount($deletedRecordCount)
  {
    $this->deletedRecordCount = $deletedRecordCount;
  }
  /**
   * @return string
   */
  public function getDeletedRecordCount()
  {
    return $this->deletedRecordCount;
  }
  /**
   * The name of the source entity.
   *
   * @param string $entityName
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;
  }
  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }
  /**
   * Optional. The total number of documents failed at sync at indexing stage.
   *
   * @param string $errorRecordCount
   */
  public function setErrorRecordCount($errorRecordCount)
  {
    $this->errorRecordCount = $errorRecordCount;
  }
  /**
   * @return string
   */
  public function getErrorRecordCount()
  {
    return $this->errorRecordCount;
  }
  /**
   * The errors from the entity's sync run. Only exist if running into an error
   * state. Contains error code and error message.
   *
   * @param GoogleRpcStatus[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleRpcStatus[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Optional. The number of documents extracted from connector source, ready to
   * be ingested to VAIS.
   *
   * @param string $extractedRecordCount
   */
  public function setExtractedRecordCount($extractedRecordCount)
  {
    $this->extractedRecordCount = $extractedRecordCount;
  }
  /**
   * @return string
   */
  public function getExtractedRecordCount()
  {
    return $this->extractedRecordCount;
  }
  /**
   * Optional. The number of documents indexed.
   *
   * @param string $indexedRecordCount
   */
  public function setIndexedRecordCount($indexedRecordCount)
  {
    $this->indexedRecordCount = $indexedRecordCount;
  }
  /**
   * @return string
   */
  public function getIndexedRecordCount()
  {
    return $this->indexedRecordCount;
  }
  /**
   * Metadata to generate the progress bar.
   *
   * @param GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress $progress
   */
  public function setProgress(GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRunProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Optional. The number of documents scheduled to be crawled/extracted from
   * connector source. This only applies to third party connectors.
   *
   * @param string $scheduledRecordCount
   */
  public function setScheduledRecordCount($scheduledRecordCount)
  {
    $this->scheduledRecordCount = $scheduledRecordCount;
  }
  /**
   * @return string
   */
  public function getScheduledRecordCount()
  {
    return $this->scheduledRecordCount;
  }
  /**
   * Optional. The number of requests sent to 3p API.
   *
   * @param string $sourceApiRequestCount
   */
  public function setSourceApiRequestCount($sourceApiRequestCount)
  {
    $this->sourceApiRequestCount = $sourceApiRequestCount;
  }
  /**
   * @return string
   */
  public function getSourceApiRequestCount()
  {
    return $this->sourceApiRequestCount;
  }
  /**
   * The state of the entity's sync run.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, SUCCEEDED, FAILED, OVERRUN,
   * CANCELLED, PENDING, WARNING, SKIPPED
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
  /**
   * Timestamp at which the entity sync state was last updated.
   *
   * @param string $stateUpdateTime
   */
  public function setStateUpdateTime($stateUpdateTime)
  {
    $this->stateUpdateTime = $stateUpdateTime;
  }
  /**
   * @return string
   */
  public function getStateUpdateTime()
  {
    return $this->stateUpdateTime;
  }
  /**
   * The timestamp for either extracted_documents_count, indexed_documents_count
   * and error_documents_count was last updated.
   *
   * @param string $statsUpdateTime
   */
  public function setStatsUpdateTime($statsUpdateTime)
  {
    $this->statsUpdateTime = $statsUpdateTime;
  }
  /**
   * @return string
   */
  public function getStatsUpdateTime()
  {
    return $this->statsUpdateTime;
  }
  /**
   * Sync type of this run.
   *
   * Accepted values: SYNC_TYPE_UNSPECIFIED, FULL, INCREMENTAL, REALTIME,
   * SCALA_SYNC
   *
   * @param self::SYNC_TYPE_* $syncType
   */
  public function setSyncType($syncType)
  {
    $this->syncType = $syncType;
  }
  /**
   * @return self::SYNC_TYPE_*
   */
  public function getSyncType()
  {
    return $this->syncType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaConnectorRunEntityRun');
