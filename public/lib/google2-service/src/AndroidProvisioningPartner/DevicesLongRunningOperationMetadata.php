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

namespace Google\Service\AndroidProvisioningPartner;

class DevicesLongRunningOperationMetadata extends \Google\Model
{
  /**
   * Invalid code. Shouldn't be used.
   */
  public const PROCESSING_STATUS_BATCH_PROCESS_STATUS_UNSPECIFIED = 'BATCH_PROCESS_STATUS_UNSPECIFIED';
  /**
   * Pending.
   */
  public const PROCESSING_STATUS_BATCH_PROCESS_PENDING = 'BATCH_PROCESS_PENDING';
  /**
   * In progress.
   */
  public const PROCESSING_STATUS_BATCH_PROCESS_IN_PROGRESS = 'BATCH_PROCESS_IN_PROGRESS';
  /**
   * Processed. This doesn't mean all items were processed successfully, you
   * should check the `response` field for the result of every item.
   */
  public const PROCESSING_STATUS_BATCH_PROCESS_PROCESSED = 'BATCH_PROCESS_PROCESSED';
  /**
   * The number of metadata updates in the operation. This might be different
   * from the number of updates in the request if the API can't parse some of
   * the updates.
   *
   * @var int
   */
  public $devicesCount;
  /**
   * The processing status of the operation.
   *
   * @var string
   */
  public $processingStatus;
  /**
   * The processing progress of the operation. Measured as a number from 0 to
   * 100. A value of 10O doesn't always mean the operation completed—check for
   * the inclusion of a `done` field.
   *
   * @var int
   */
  public $progress;

  /**
   * The number of metadata updates in the operation. This might be different
   * from the number of updates in the request if the API can't parse some of
   * the updates.
   *
   * @param int $devicesCount
   */
  public function setDevicesCount($devicesCount)
  {
    $this->devicesCount = $devicesCount;
  }
  /**
   * @return int
   */
  public function getDevicesCount()
  {
    return $this->devicesCount;
  }
  /**
   * The processing status of the operation.
   *
   * Accepted values: BATCH_PROCESS_STATUS_UNSPECIFIED, BATCH_PROCESS_PENDING,
   * BATCH_PROCESS_IN_PROGRESS, BATCH_PROCESS_PROCESSED
   *
   * @param self::PROCESSING_STATUS_* $processingStatus
   */
  public function setProcessingStatus($processingStatus)
  {
    $this->processingStatus = $processingStatus;
  }
  /**
   * @return self::PROCESSING_STATUS_*
   */
  public function getProcessingStatus()
  {
    return $this->processingStatus;
  }
  /**
   * The processing progress of the operation. Measured as a number from 0 to
   * 100. A value of 10O doesn't always mean the operation completed—check for
   * the inclusion of a `done` field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DevicesLongRunningOperationMetadata::class, 'Google_Service_AndroidProvisioningPartner_DevicesLongRunningOperationMetadata');
