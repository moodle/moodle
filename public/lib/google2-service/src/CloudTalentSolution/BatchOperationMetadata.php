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

namespace Google\Service\CloudTalentSolution;

class BatchOperationMetadata extends \Google\Model
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The batch operation is being prepared for processing.
   */
  public const STATE_INITIALIZING = 'INITIALIZING';
  /**
   * The batch operation is actively being processed.
   */
  public const STATE_PROCESSING = 'PROCESSING';
  /**
   * The batch operation is processed, and at least one item has been
   * successfully processed.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The batch operation is done and no item has been successfully processed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The batch operation is in the process of cancelling after
   * google.longrunning.Operations.CancelOperation is called.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The batch operation is done after
   * google.longrunning.Operations.CancelOperation is called. Any items
   * processed before cancelling are returned in the response.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The time when the batch operation is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time when the batch operation is finished and
   * google.longrunning.Operation.done is set to `true`.
   *
   * @var string
   */
  public $endTime;
  /**
   * Count of failed item(s) inside an operation.
   *
   * @var int
   */
  public $failureCount;
  /**
   * The state of a long running operation.
   *
   * @var string
   */
  public $state;
  /**
   * More detailed information about operation state.
   *
   * @var string
   */
  public $stateDescription;
  /**
   * Count of successful item(s) inside an operation.
   *
   * @var int
   */
  public $successCount;
  /**
   * Count of total item(s) inside an operation.
   *
   * @var int
   */
  public $totalCount;
  /**
   * The time when the batch operation status is updated. The metadata and the
   * update_time is refreshed every minute otherwise cached data is returned.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The time when the batch operation is created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The time when the batch operation is finished and
   * google.longrunning.Operation.done is set to `true`.
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
   * Count of failed item(s) inside an operation.
   *
   * @param int $failureCount
   */
  public function setFailureCount($failureCount)
  {
    $this->failureCount = $failureCount;
  }
  /**
   * @return int
   */
  public function getFailureCount()
  {
    return $this->failureCount;
  }
  /**
   * The state of a long running operation.
   *
   * Accepted values: STATE_UNSPECIFIED, INITIALIZING, PROCESSING, SUCCEEDED,
   * FAILED, CANCELLING, CANCELLED
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
   * More detailed information about operation state.
   *
   * @param string $stateDescription
   */
  public function setStateDescription($stateDescription)
  {
    $this->stateDescription = $stateDescription;
  }
  /**
   * @return string
   */
  public function getStateDescription()
  {
    return $this->stateDescription;
  }
  /**
   * Count of successful item(s) inside an operation.
   *
   * @param int $successCount
   */
  public function setSuccessCount($successCount)
  {
    $this->successCount = $successCount;
  }
  /**
   * @return int
   */
  public function getSuccessCount()
  {
    return $this->successCount;
  }
  /**
   * Count of total item(s) inside an operation.
   *
   * @param int $totalCount
   */
  public function setTotalCount($totalCount)
  {
    $this->totalCount = $totalCount;
  }
  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->totalCount;
  }
  /**
   * The time when the batch operation status is updated. The metadata and the
   * update_time is refreshed every minute otherwise cached data is returned.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchOperationMetadata::class, 'Google_Service_CloudTalentSolution_BatchOperationMetadata');
