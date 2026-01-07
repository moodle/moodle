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

namespace Google\Service\Compute;

class BulkInsertOperationStatus extends \Google\Model
{
  /**
   * Rolling forward - creating VMs.
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * Done
   */
  public const STATUS_DONE = 'DONE';
  /**
   * Rolling back - cleaning up after an error.
   */
  public const STATUS_ROLLING_BACK = 'ROLLING_BACK';
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * [Output Only] Count of VMs successfully created so far.
   *
   * @var int
   */
  public $createdVmCount;
  /**
   * [Output Only] Count of VMs that got deleted during rollback.
   *
   * @var int
   */
  public $deletedVmCount;
  /**
   * [Output Only] Count of VMs that started creating but encountered an error.
   *
   * @var int
   */
  public $failedToCreateVmCount;
  /**
   * [Output Only] Creation status of BulkInsert operation - information if the
   * flow is rolling forward or rolling back.
   *
   * @var string
   */
  public $status;
  /**
   * [Output Only] Count of VMs originally planned to be created.
   *
   * @var int
   */
  public $targetVmCount;

  /**
   * [Output Only] Count of VMs successfully created so far.
   *
   * @param int $createdVmCount
   */
  public function setCreatedVmCount($createdVmCount)
  {
    $this->createdVmCount = $createdVmCount;
  }
  /**
   * @return int
   */
  public function getCreatedVmCount()
  {
    return $this->createdVmCount;
  }
  /**
   * [Output Only] Count of VMs that got deleted during rollback.
   *
   * @param int $deletedVmCount
   */
  public function setDeletedVmCount($deletedVmCount)
  {
    $this->deletedVmCount = $deletedVmCount;
  }
  /**
   * @return int
   */
  public function getDeletedVmCount()
  {
    return $this->deletedVmCount;
  }
  /**
   * [Output Only] Count of VMs that started creating but encountered an error.
   *
   * @param int $failedToCreateVmCount
   */
  public function setFailedToCreateVmCount($failedToCreateVmCount)
  {
    $this->failedToCreateVmCount = $failedToCreateVmCount;
  }
  /**
   * @return int
   */
  public function getFailedToCreateVmCount()
  {
    return $this->failedToCreateVmCount;
  }
  /**
   * [Output Only] Creation status of BulkInsert operation - information if the
   * flow is rolling forward or rolling back.
   *
   * Accepted values: CREATING, DONE, ROLLING_BACK, STATUS_UNSPECIFIED
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
  /**
   * [Output Only] Count of VMs originally planned to be created.
   *
   * @param int $targetVmCount
   */
  public function setTargetVmCount($targetVmCount)
  {
    $this->targetVmCount = $targetVmCount;
  }
  /**
   * @return int
   */
  public function getTargetVmCount()
  {
    return $this->targetVmCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BulkInsertOperationStatus::class, 'Google_Service_Compute_BulkInsertOperationStatus');
