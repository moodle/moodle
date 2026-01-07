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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1CommonOperationMetadata extends \Google\Model
{
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Operation is still running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Operation is being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * Operation succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Operation failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Operation is cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The creation time of the operation.
   *
   * @var string
   */
  public $createTime;
  /**
   * A related resource to this operation.
   *
   * @var string
   */
  public $resource;
  /**
   * The state of the operation.
   *
   * @var string
   */
  public $state;
  /**
   * A message providing more details about the current state of processing.
   *
   * @var string
   */
  public $stateMessage;
  /**
   * The last update time of the operation.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The creation time of the operation.
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
   * A related resource to this operation.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The state of the operation.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, CANCELLING, SUCCEEDED, FAILED,
   * CANCELLED
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
   * A message providing more details about the current state of processing.
   *
   * @param string $stateMessage
   */
  public function setStateMessage($stateMessage)
  {
    $this->stateMessage = $stateMessage;
  }
  /**
   * @return string
   */
  public function getStateMessage()
  {
    return $this->stateMessage;
  }
  /**
   * The last update time of the operation.
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
class_alias(GoogleCloudDocumentaiV1CommonOperationMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1CommonOperationMetadata');
