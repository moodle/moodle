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

namespace Google\Service\CloudAlloyDBAdmin;

class StorageDatabasecenterPartnerapiV1mainBackupRun extends \Google\Model
{
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The backup was successful.
   */
  public const STATUS_SUCCESSFUL = 'SUCCESSFUL';
  /**
   * The backup was unsuccessful.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * The time the backup operation completed. REQUIRED
   *
   * @var string
   */
  public $endTime;
  protected $errorType = StorageDatabasecenterPartnerapiV1mainOperationError::class;
  protected $errorDataType = '';
  /**
   * The time the backup operation started. REQUIRED
   *
   * @var string
   */
  public $startTime;
  /**
   * The status of this run. REQUIRED
   *
   * @var string
   */
  public $status;

  /**
   * The time the backup operation completed. REQUIRED
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
   * Information about why the backup operation failed. This is only present if
   * the run has the FAILED status. OPTIONAL
   *
   * @param StorageDatabasecenterPartnerapiV1mainOperationError $error
   */
  public function setError(StorageDatabasecenterPartnerapiV1mainOperationError $error)
  {
    $this->error = $error;
  }
  /**
   * @return StorageDatabasecenterPartnerapiV1mainOperationError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The time the backup operation started. REQUIRED
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
   * The status of this run. REQUIRED
   *
   * Accepted values: STATUS_UNSPECIFIED, SUCCESSFUL, FAILED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainBackupRun::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainBackupRun');
