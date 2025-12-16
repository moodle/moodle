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

class TransferJob extends \Google\Model
{
  /**
   * Zero is an illegal value.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * New transfers are performed based on the schedule.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * New transfers are not scheduled.
   */
  public const STATUS_DISABLED = 'DISABLED';
  /**
   * This is a soft delete state. After a transfer job is set to this state, the
   * job and all the transfer executions are subject to garbage collection.
   * Transfer jobs become eligible for garbage collection 30 days after their
   * status is set to `DELETED`.
   */
  public const STATUS_DELETED = 'DELETED';
  /**
   * Output only. The time that the transfer job was created.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Output only. The time that the transfer job was deleted.
   *
   * @var string
   */
  public $deletionTime;
  /**
   * A description provided by the user for the job. Its max length is 1024
   * bytes when Unicode-encoded.
   *
   * @var string
   */
  public $description;
  protected $eventStreamType = EventStream::class;
  protected $eventStreamDataType = '';
  /**
   * Output only. The time that the transfer job was last modified.
   *
   * @var string
   */
  public $lastModificationTime;
  /**
   * The name of the most recently started TransferOperation of this JobConfig.
   * Present if a TransferOperation has been created for this JobConfig.
   *
   * @var string
   */
  public $latestOperationName;
  protected $loggingConfigType = LoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * A unique name (within the transfer project) assigned when the job is
   * created. If this field is empty in a CreateTransferJobRequest, Storage
   * Transfer Service assigns a unique name. Otherwise, the specified name is
   * used as the unique name for this job. If the specified name is in use by a
   * job, the creation request fails with an ALREADY_EXISTS error. This name
   * must start with `"transferJobs/"` prefix and end with a letter or a number,
   * and should be no more than 128 characters. For transfers involving
   * PosixFilesystem, this name must start with `transferJobs/OPI` specifically.
   * For all other transfer types, this name must not start with
   * `transferJobs/OPI`. Non-PosixFilesystem example:
   * `"transferJobs/^(?!OPI)[A-Za-z0-9-._~]*[A-Za-z0-9]$"` PosixFilesystem
   * example: `"transferJobs/OPI^[A-Za-z0-9-._~]*[A-Za-z0-9]$"` Applications
   * must not rely on the enforcement of naming requirements involving OPI.
   * Invalid job names fail with an INVALID_ARGUMENT error.
   *
   * @var string
   */
  public $name;
  protected $notificationConfigType = NotificationConfig::class;
  protected $notificationConfigDataType = '';
  /**
   * The ID of the Google Cloud project that owns the job.
   *
   * @var string
   */
  public $projectId;
  protected $replicationSpecType = ReplicationSpec::class;
  protected $replicationSpecDataType = '';
  protected $scheduleType = Schedule::class;
  protected $scheduleDataType = '';
  /**
   * Optional. The user-managed service account to which to delegate service
   * agent permissions. You can grant Cloud Storage bucket permissions to this
   * service account instead of to the Transfer Service service agent. Either
   * the service account email
   * (`SERVICE_ACCOUNT_NAME@PROJECT_ID.iam.gserviceaccount.com`) or the unique
   * ID (`123456789012345678901`) are accepted. See
   * https://docs.cloud.google.com/storage-transfer/docs/delegate-service-agent-
   * permissions for required permissions.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Status of the job. This value MUST be specified for
   * `CreateTransferJobRequests`. **Note:** The effect of the new job status
   * takes place during a subsequent job run. For example, if you change the job
   * status from ENABLED to DISABLED, and an operation spawned by the transfer
   * is running, the status change would not affect the current operation.
   *
   * @var string
   */
  public $status;
  protected $transferSpecType = TransferSpec::class;
  protected $transferSpecDataType = '';

  /**
   * Output only. The time that the transfer job was created.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. The time that the transfer job was deleted.
   *
   * @param string $deletionTime
   */
  public function setDeletionTime($deletionTime)
  {
    $this->deletionTime = $deletionTime;
  }
  /**
   * @return string
   */
  public function getDeletionTime()
  {
    return $this->deletionTime;
  }
  /**
   * A description provided by the user for the job. Its max length is 1024
   * bytes when Unicode-encoded.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Specifies the event stream for the transfer job for event-driven transfers.
   * When EventStream is specified, the Schedule fields are ignored.
   *
   * @param EventStream $eventStream
   */
  public function setEventStream(EventStream $eventStream)
  {
    $this->eventStream = $eventStream;
  }
  /**
   * @return EventStream
   */
  public function getEventStream()
  {
    return $this->eventStream;
  }
  /**
   * Output only. The time that the transfer job was last modified.
   *
   * @param string $lastModificationTime
   */
  public function setLastModificationTime($lastModificationTime)
  {
    $this->lastModificationTime = $lastModificationTime;
  }
  /**
   * @return string
   */
  public function getLastModificationTime()
  {
    return $this->lastModificationTime;
  }
  /**
   * The name of the most recently started TransferOperation of this JobConfig.
   * Present if a TransferOperation has been created for this JobConfig.
   *
   * @param string $latestOperationName
   */
  public function setLatestOperationName($latestOperationName)
  {
    $this->latestOperationName = $latestOperationName;
  }
  /**
   * @return string
   */
  public function getLatestOperationName()
  {
    return $this->latestOperationName;
  }
  /**
   * Logging configuration.
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
   * A unique name (within the transfer project) assigned when the job is
   * created. If this field is empty in a CreateTransferJobRequest, Storage
   * Transfer Service assigns a unique name. Otherwise, the specified name is
   * used as the unique name for this job. If the specified name is in use by a
   * job, the creation request fails with an ALREADY_EXISTS error. This name
   * must start with `"transferJobs/"` prefix and end with a letter or a number,
   * and should be no more than 128 characters. For transfers involving
   * PosixFilesystem, this name must start with `transferJobs/OPI` specifically.
   * For all other transfer types, this name must not start with
   * `transferJobs/OPI`. Non-PosixFilesystem example:
   * `"transferJobs/^(?!OPI)[A-Za-z0-9-._~]*[A-Za-z0-9]$"` PosixFilesystem
   * example: `"transferJobs/OPI^[A-Za-z0-9-._~]*[A-Za-z0-9]$"` Applications
   * must not rely on the enforcement of naming requirements involving OPI.
   * Invalid job names fail with an INVALID_ARGUMENT error.
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
   * The ID of the Google Cloud project that owns the job.
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
   * Replication specification.
   *
   * @param ReplicationSpec $replicationSpec
   */
  public function setReplicationSpec(ReplicationSpec $replicationSpec)
  {
    $this->replicationSpec = $replicationSpec;
  }
  /**
   * @return ReplicationSpec
   */
  public function getReplicationSpec()
  {
    return $this->replicationSpec;
  }
  /**
   * Specifies schedule for the transfer job. This is an optional field. When
   * the field is not set, the job never executes a transfer, unless you invoke
   * RunTransferJob or update the job to have a non-empty schedule.
   *
   * @param Schedule $schedule
   */
  public function setSchedule(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return Schedule
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Optional. The user-managed service account to which to delegate service
   * agent permissions. You can grant Cloud Storage bucket permissions to this
   * service account instead of to the Transfer Service service agent. Either
   * the service account email
   * (`SERVICE_ACCOUNT_NAME@PROJECT_ID.iam.gserviceaccount.com`) or the unique
   * ID (`123456789012345678901`) are accepted. See
   * https://docs.cloud.google.com/storage-transfer/docs/delegate-service-agent-
   * permissions for required permissions.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Status of the job. This value MUST be specified for
   * `CreateTransferJobRequests`. **Note:** The effect of the new job status
   * takes place during a subsequent job run. For example, if you change the job
   * status from ENABLED to DISABLED, and an operation spawned by the transfer
   * is running, the status change would not affect the current operation.
   *
   * Accepted values: STATUS_UNSPECIFIED, ENABLED, DISABLED, DELETED
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
class_alias(TransferJob::class, 'Google_Service_Storagetransfer_TransferJob');
