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

class LinkMetadata extends \Google\Model
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
  protected $createLinkRequestType = CreateLinkRequest::class;
  protected $createLinkRequestDataType = '';
  protected $deleteLinkRequestType = DeleteLinkRequest::class;
  protected $deleteLinkRequestDataType = '';
  /**
   * The end time of an operation.
   *
   * @var string
   */
  public $endTime;
  /**
   * The start time of an operation.
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

  /**
   * CreateLink RPC request.
   *
   * @param CreateLinkRequest $createLinkRequest
   */
  public function setCreateLinkRequest(CreateLinkRequest $createLinkRequest)
  {
    $this->createLinkRequest = $createLinkRequest;
  }
  /**
   * @return CreateLinkRequest
   */
  public function getCreateLinkRequest()
  {
    return $this->createLinkRequest;
  }
  /**
   * DeleteLink RPC request.
   *
   * @param DeleteLinkRequest $deleteLinkRequest
   */
  public function setDeleteLinkRequest(DeleteLinkRequest $deleteLinkRequest)
  {
    $this->deleteLinkRequest = $deleteLinkRequest;
  }
  /**
   * @return DeleteLinkRequest
   */
  public function getDeleteLinkRequest()
  {
    return $this->deleteLinkRequest;
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
   * The start time of an operation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkMetadata::class, 'Google_Service_Logging_LinkMetadata');
