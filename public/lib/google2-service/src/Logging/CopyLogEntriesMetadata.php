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

class CopyLogEntriesMetadata extends \Google\Model
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
  /**
   * Identifies whether the user has requested cancellation of the operation.
   *
   * @var bool
   */
  public $cancellationRequested;
  /**
   * Destination to which to copy log entries.For example, a Cloud Storage
   * bucket:"storage.googleapis.com/my-cloud-storage-bucket"
   *
   * @var string
   */
  public $destination;
  /**
   * The end time of an operation.
   *
   * @var string
   */
  public $endTime;
  /**
   * Estimated progress of the operation (0 - 100%).
   *
   * @var int
   */
  public $progress;
  protected $requestType = CopyLogEntriesRequest::class;
  protected $requestDataType = '';
  /**
   * Source from which to copy log entries.For example, a log
   * bucket:"projects/my-project/locations/global/buckets/my-source-bucket"
   *
   * @var string
   */
  public $source;
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
  /**
   * Name of the verb executed by the operation.For example,"copy"
   *
   * @var string
   */
  public $verb;
  /**
   * The IAM identity of a service account that must be granted access to the
   * destination.If the service account is not granted permission to the
   * destination within an hour, the operation will be cancelled.For example:
   * "serviceAccount:foo@bar.com"
   *
   * @var string
   */
  public $writerIdentity;

  /**
   * Identifies whether the user has requested cancellation of the operation.
   *
   * @param bool $cancellationRequested
   */
  public function setCancellationRequested($cancellationRequested)
  {
    $this->cancellationRequested = $cancellationRequested;
  }
  /**
   * @return bool
   */
  public function getCancellationRequested()
  {
    return $this->cancellationRequested;
  }
  /**
   * Destination to which to copy log entries.For example, a Cloud Storage
   * bucket:"storage.googleapis.com/my-cloud-storage-bucket"
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
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
   * Estimated progress of the operation (0 - 100%).
   *
   * @param int $progress
   */
  public function setProgress($progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return int
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * CopyLogEntries RPC request. This field is deprecated and not used.
   *
   * @deprecated
   * @param CopyLogEntriesRequest $request
   */
  public function setRequest(CopyLogEntriesRequest $request)
  {
    $this->request = $request;
  }
  /**
   * @deprecated
   * @return CopyLogEntriesRequest
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Source from which to copy log entries.For example, a log
   * bucket:"projects/my-project/locations/global/buckets/my-source-bucket"
   *
   * @param string $source
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
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
   * Name of the verb executed by the operation.For example,"copy"
   *
   * @param string $verb
   */
  public function setVerb($verb)
  {
    $this->verb = $verb;
  }
  /**
   * @return string
   */
  public function getVerb()
  {
    return $this->verb;
  }
  /**
   * The IAM identity of a service account that must be granted access to the
   * destination.If the service account is not granted permission to the
   * destination within an hour, the operation will be cancelled.For example:
   * "serviceAccount:foo@bar.com"
   *
   * @param string $writerIdentity
   */
  public function setWriterIdentity($writerIdentity)
  {
    $this->writerIdentity = $writerIdentity;
  }
  /**
   * @return string
   */
  public function getWriterIdentity()
  {
    return $this->writerIdentity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CopyLogEntriesMetadata::class, 'Google_Service_Logging_CopyLogEntriesMetadata');
