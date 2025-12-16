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

namespace Google\Service\Logging;

class BucketMetadata extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const STATE_OPERATION_STATE_UNSPECIFIED = 'OPERATION_STATE_UNSPECIFIED';
  /**
   * The operation is scheduled.
   */
  public const STATE_OPERATION_STATE_SCHEDULED = 'OPERATION_STATE_SCHEDULED';
  /**
   * Waiting for necessary permissions.
   */
  public const STATE_OPERATION_STATE_WAITING_FOR_PERMISSIONS = 'OPERATION_STATE_WAITING_FOR_PERMISSIONS';
  /**
   * The operation is running.
   */
  public const STATE_OPERATION_STATE_RUNNING = 'OPERATION_STATE_RUNNING';
  /**
   * The operation was completed successfully.
   */
  public const STATE_OPERATION_STATE_SUCCEEDED = 'OPERATION_STATE_SUCCEEDED';
  /**
   * The operation failed.
   */
  public const STATE_OPERATION_STATE_FAILED = 'OPERATION_STATE_FAILED';
  /**
   * The operation was cancelled by the user.
   */
  public const STATE_OPERATION_STATE_CANCELLED = 'OPERATION_STATE_CANCELLED';
  /**
   * The operation is waiting for quota.
   */
  public const STATE_OPERATION_STATE_PENDING = 'OPERATION_STATE_PENDING';
  protected $createBucketRequestType = CreateBucketRequest::class;
  protected $createBucketRequestDataType = '';
  /**
   * The end time of an operation.
   *
   * @var string
   */
  public $endTime;
  /**
   * The create time of an operation.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. State of an operation.
   *
   * @var string
   */
  public $state;
  protected $updateBucketRequestType = UpdateBucketRequest::class;
  protected $updateBucketRequestDataType = '';

  /**
   * LongRunningCreateBucket RPC request.
   *
   * @param CreateBucketRequest $createBucketRequest
   */
  public function setCreateBucketRequest(CreateBucketRequest $createBucketRequest)
  {
    $this->createBucketRequest = $createBucketRequest;
  }
  /**
   * @return CreateBucketRequest
   */
  public function getCreateBucketRequest()
  {
    return $this->createBucketRequest;
  }
  /**
   * The end time of an operation.
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
   * The create time of an operation.
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
   * Output only. State of an operation.
   *
   * Accepted values: OPERATION_STATE_UNSPECIFIED, OPERATION_STATE_SCHEDULED,
   * OPERATION_STATE_WAITING_FOR_PERMISSIONS, OPERATION_STATE_RUNNING,
   * OPERATION_STATE_SUCCEEDED, OPERATION_STATE_FAILED,
   * OPERATION_STATE_CANCELLED, OPERATION_STATE_PENDING
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
   * LongRunningUpdateBucket RPC request.
   *
   * @param UpdateBucketRequest $updateBucketRequest
   */
  public function setUpdateBucketRequest(UpdateBucketRequest $updateBucketRequest)
  {
    $this->updateBucketRequest = $updateBucketRequest;
  }
  /**
   * @return UpdateBucketRequest
   */
  public function getUpdateBucketRequest()
  {
    return $this->updateBucketRequest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BucketMetadata::class, 'Google_Service_Logging_BucketMetadata');
