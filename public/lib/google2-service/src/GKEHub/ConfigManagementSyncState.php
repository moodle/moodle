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

namespace Google\Service\GKEHub;

class ConfigManagementSyncState extends \Google\Collection
{
  /**
   * Config Sync cannot determine a sync code
   */
  public const CODE_SYNC_CODE_UNSPECIFIED = 'SYNC_CODE_UNSPECIFIED';
  /**
   * Config Sync successfully synced the git Repo with the cluster
   */
  public const CODE_SYNCED = 'SYNCED';
  /**
   * Config Sync is in the progress of syncing a new change
   */
  public const CODE_PENDING = 'PENDING';
  /**
   * Indicates an error configuring Config Sync, and user action is required
   */
  public const CODE_ERROR = 'ERROR';
  /**
   * Config Sync has been installed but not configured
   */
  public const CODE_NOT_CONFIGURED = 'NOT_CONFIGURED';
  /**
   * Config Sync has not been installed
   */
  public const CODE_NOT_INSTALLED = 'NOT_INSTALLED';
  /**
   * Error authorizing with the cluster
   */
  public const CODE_UNAUTHORIZED = 'UNAUTHORIZED';
  /**
   * Cluster could not be reached
   */
  public const CODE_UNREACHABLE = 'UNREACHABLE';
  protected $collection_key = 'errors';
  /**
   * Sync status code.
   *
   * @var string
   */
  public $code;
  protected $errorsType = ConfigManagementSyncError::class;
  protected $errorsDataType = 'array';
  /**
   * Token indicating the state of the importer.
   *
   * @var string
   */
  public $importToken;
  /**
   * Deprecated: use last_sync_time instead. Timestamp of when ACM last
   * successfully synced the repo. The time format is specified in
   * https://golang.org/pkg/time/#Time.String
   *
   * @deprecated
   * @var string
   */
  public $lastSync;
  /**
   * Timestamp type of when ACM last successfully synced the repo.
   *
   * @var string
   */
  public $lastSyncTime;
  /**
   * Token indicating the state of the repo.
   *
   * @var string
   */
  public $sourceToken;
  /**
   * Token indicating the state of the syncer.
   *
   * @var string
   */
  public $syncToken;

  /**
   * Sync status code.
   *
   * Accepted values: SYNC_CODE_UNSPECIFIED, SYNCED, PENDING, ERROR,
   * NOT_CONFIGURED, NOT_INSTALLED, UNAUTHORIZED, UNREACHABLE
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A list of errors resulting from problematic configs. This list will be
   * truncated after 100 errors, although it is unlikely for that many errors to
   * simultaneously exist.
   *
   * @param ConfigManagementSyncError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ConfigManagementSyncError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Token indicating the state of the importer.
   *
   * @param string $importToken
   */
  public function setImportToken($importToken)
  {
    $this->importToken = $importToken;
  }
  /**
   * @return string
   */
  public function getImportToken()
  {
    return $this->importToken;
  }
  /**
   * Deprecated: use last_sync_time instead. Timestamp of when ACM last
   * successfully synced the repo. The time format is specified in
   * https://golang.org/pkg/time/#Time.String
   *
   * @deprecated
   * @param string $lastSync
   */
  public function setLastSync($lastSync)
  {
    $this->lastSync = $lastSync;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLastSync()
  {
    return $this->lastSync;
  }
  /**
   * Timestamp type of when ACM last successfully synced the repo.
   *
   * @param string $lastSyncTime
   */
  public function setLastSyncTime($lastSyncTime)
  {
    $this->lastSyncTime = $lastSyncTime;
  }
  /**
   * @return string
   */
  public function getLastSyncTime()
  {
    return $this->lastSyncTime;
  }
  /**
   * Token indicating the state of the repo.
   *
   * @param string $sourceToken
   */
  public function setSourceToken($sourceToken)
  {
    $this->sourceToken = $sourceToken;
  }
  /**
   * @return string
   */
  public function getSourceToken()
  {
    return $this->sourceToken;
  }
  /**
   * Token indicating the state of the syncer.
   *
   * @param string $syncToken
   */
  public function setSyncToken($syncToken)
  {
    $this->syncToken = $syncToken;
  }
  /**
   * @return string
   */
  public function getSyncToken()
  {
    return $this->syncToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigManagementSyncState::class, 'Google_Service_GKEHub_ConfigManagementSyncState');
