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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ExecutionReference extends \Google\Model
{
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const COMPLETION_STATUS_COMPLETION_STATUS_UNSPECIFIED = 'COMPLETION_STATUS_UNSPECIFIED';
  /**
   * Job execution has succeeded.
   */
  public const COMPLETION_STATUS_EXECUTION_SUCCEEDED = 'EXECUTION_SUCCEEDED';
  /**
   * Job execution has failed.
   */
  public const COMPLETION_STATUS_EXECUTION_FAILED = 'EXECUTION_FAILED';
  /**
   * Job execution is running normally.
   */
  public const COMPLETION_STATUS_EXECUTION_RUNNING = 'EXECUTION_RUNNING';
  /**
   * Waiting for backing resources to be provisioned.
   */
  public const COMPLETION_STATUS_EXECUTION_PENDING = 'EXECUTION_PENDING';
  /**
   * Job execution has been cancelled by the user.
   */
  public const COMPLETION_STATUS_EXECUTION_CANCELLED = 'EXECUTION_CANCELLED';
  /**
   * Status for the execution completion.
   *
   * @var string
   */
  public $completionStatus;
  /**
   * Creation timestamp of the execution.
   *
   * @var string
   */
  public $completionTime;
  /**
   * Creation timestamp of the execution.
   *
   * @var string
   */
  public $createTime;
  /**
   * The deletion time of the execution. It is only populated as a response to a
   * Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Name of the execution.
   *
   * @var string
   */
  public $name;

  /**
   * Status for the execution completion.
   *
   * Accepted values: COMPLETION_STATUS_UNSPECIFIED, EXECUTION_SUCCEEDED,
   * EXECUTION_FAILED, EXECUTION_RUNNING, EXECUTION_PENDING, EXECUTION_CANCELLED
   *
   * @param self::COMPLETION_STATUS_* $completionStatus
   */
  public function setCompletionStatus($completionStatus)
  {
    $this->completionStatus = $completionStatus;
  }
  /**
   * @return self::COMPLETION_STATUS_*
   */
  public function getCompletionStatus()
  {
    return $this->completionStatus;
  }
  /**
   * Creation timestamp of the execution.
   *
   * @param string $completionTime
   */
  public function setCompletionTime($completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return string
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * Creation timestamp of the execution.
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
   * The deletion time of the execution. It is only populated as a response to a
   * Delete request.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Name of the execution.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ExecutionReference::class, 'Google_Service_CloudRun_GoogleCloudRunV2ExecutionReference');
