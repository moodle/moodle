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

namespace Google\Service\Storagetransfer;

class TransferOperation extends \Google\Collection
{
  /**
   * Zero is an illegal value.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * In progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * Completed successfully.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * Terminated due to an unrecoverable failure.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * Aborted by the user.
   */
  public const STATUS_ABORTED = 'ABORTED';
  /**
   * Temporarily delayed by the system. No user action is required.
   */
  public const STATUS_QUEUED = 'QUEUED';
  /**
   * The operation is suspending and draining the ongoing work to completion.
   */
  public const STATUS_SUSPENDING = 'SUSPENDING';
  protected $collection_key = 'errorBreakdowns';
  protected $countersType = TransferCounters::class;
  protected $countersDataType = '';
  /**
   * End time of this transfer execution.
   *
   * @var string
   */
  public $endTime;
  protected $errorBreakdownsType = ErrorSummary::class;
  protected $errorBreakdownsDataType = 'array';
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * A globally unique ID assigned by the system.
   *
   * @var string
   */
  public $name;
  protected $notificationConfigType = NotificationConfig::class;
  protected $notificationConfigDataType = '';
  /**
   * The ID of the Google Cloud project that owns the operation.
   *
   * @var string
   */
  public $projectId;
  /**
   * Start time of this transfer execution.
   *
   * @var string
   */
  public $startTime;
  /**
   * Status of the transfer operation.
   *
   * @var string
   */
  public $status;
  /**
   * The name of the transfer job that triggers this transfer operation.
   *
   * @var string
   */
  public $transferJobName;
  protected $transferSpecType = TransferSpec::class;
  protected $transferSpecDataType = '';

  /**
   * Information about the progress of the transfer operation.
   *
   * @param TransferCounters $counters
   */
  public function setCounters(TransferCounters $counters)
  {
    $this->counters = $counters;
  }
  /**
   * @return TransferCounters
   */
  public function getCounters()
  {
    return $this->counters;
  }
  /**
   * End time of this transfer execution.
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
   * Summarizes errors encountered with sample error log entries.
   *
   * @param ErrorSummary[] $errorBreakdowns
   */
  public function setErrorBreakdowns($errorBreakdowns)
  {
    $this->errorBreakdowns = $errorBreakdowns;
  }
  /**
   * @return ErrorSummary[]
   */
  public function getErrorBreakdowns()
  {
    return $this->errorBreakdowns;
  }
  /**
   * Cloud Logging configuration.
   *
   * @param LoggingConfig $loggingConfig
   */
  public function setLoggingConfig(LoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return LoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * A globally unique ID assigned by the system.
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
  /**
   * Notification configuration.
   *
   * @param NotificationConfig $notificationConfig
   */
  public function setNotificationConfig(NotificationConfig $notificationConfig)
  {
    $this->notificationConfig = $notificationConfig;
  }
  /**
   * @return NotificationConfig
   */
  public function getNotificationConfig()
  {
    return $this->notificationConfig;
  }
  /**
   * The ID of the Google Cloud project that owns the operation.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Start time of this transfer execution.
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
   * Status of the transfer operation.
   *
   * Accepted values: STATUS_UNSPECIFIED, IN_PROGRESS, PAUSED, SUCCESS, FAILED,
   * ABORTED, QUEUED, SUSPENDING
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
   * The name of the transfer job that triggers this transfer operation.
   *
   * @param string $transferJobName
   */
  public function setTransferJobName($transferJobName)
  {
    $this->transferJobName = $transferJobName;
  }
  /**
   * @return string
   */
  public function getTransferJobName()
  {
    return $this->transferJobName;
  }
  /**
   * Transfer specification.
   *
   * @param TransferSpec $transferSpec
   */
  public function setTransferSpec(TransferSpec $transferSpec)
  {
    $this->transferSpec = $transferSpec;
  }
  /**
   * @return TransferSpec
   */
  public function getTransferSpec()
  {
    return $this->transferSpec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferOperation::class, 'Google_Service_Storagetransfer_TransferOperation');
