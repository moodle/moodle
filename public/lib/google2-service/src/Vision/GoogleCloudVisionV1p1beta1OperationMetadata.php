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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p1beta1OperationMetadata extends \Google\Model
{
  /**
   * Invalid.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Request is received.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * Request is actively being processed.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The batch processing is done.
   */
  public const STATE_DONE = 'DONE';
  /**
   * The batch processing was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The time when the batch request was received.
   *
   * @var string
   */
  public $createTime;
  /**
   * Current state of the batch operation.
   *
   * @var string
   */
  public $state;
  /**
   * The time when the operation result was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The time when the batch request was received.
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
   * Current state of the batch operation.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATED, RUNNING, DONE, CANCELLED
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
   * The time when the operation result was last updated.
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
class_alias(GoogleCloudVisionV1p1beta1OperationMetadata::class, 'Google_Service_Vision_GoogleCloudVisionV1p1beta1OperationMetadata');
