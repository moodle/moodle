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

namespace Google\Service\BigQueryDataTransfer;

class TransferRun extends \Google\Model
{
  /**
   * State placeholder (0).
   */
  public const STATE_TRANSFER_STATE_UNSPECIFIED = 'TRANSFER_STATE_UNSPECIFIED';
  /**
   * Data transfer is scheduled and is waiting to be picked up by data transfer
   * backend (2).
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Data transfer is in progress (3).
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Data transfer completed successfully (4).
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Data transfer failed (5).
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Data transfer is cancelled (6).
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Output only. Data source id.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Output only. The BigQuery target dataset id.
   *
   * @var string
   */
  public $destinationDatasetId;
  protected $emailPreferencesType = EmailPreferences::class;
  protected $emailPreferencesDataType = '';
  /**
   * Output only. Time when transfer run ended. Parameter ignored by server for
   * input requests.
   *
   * @var string
   */
  public $endTime;
  protected $errorStatusType = Status::class;
  protected $errorStatusDataType = '';
  /**
   * Identifier. The resource name of the transfer run. Transfer run names have
   * the form `projects/{project_id}/locations/{location}/transferConfigs/{confi
   * g_id}/runs/{run_id}`. The name is ignored when creating a transfer run.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Pub/Sub topic where a notification will be sent after this
   * transfer run finishes. The format for specifying a pubsub topic is:
   * `projects/{project_id}/topics/{topic_id}`
   *
   * @var string
   */
  public $notificationPubsubTopic;
  /**
   * Output only. Parameters specific to each data source. For more information
   * see the bq tab in the 'Setting up a data transfer' section for each data
   * source. For example the parameters for Cloud Storage transfers are listed
   * here: https://cloud.google.com/bigquery-transfer/docs/cloud-storage-
   * transfer#bq
   *
   * @var array[]
   */
  public $params;
  /**
   * For batch transfer runs, specifies the date and time of the data should be
   * ingested.
   *
   * @var string
   */
  public $runTime;
  /**
   * Output only. Describes the schedule of this transfer run if it was created
   * as part of a regular schedule. For batch transfer runs that are scheduled
   * manually, this is empty. NOTE: the system might choose to delay the
   * schedule depending on the current load, so `schedule_time` doesn't always
   * match this.
   *
   * @var string
   */
  public $schedule;
  /**
   * Minimum time after which a transfer run can be started.
   *
   * @var string
   */
  public $scheduleTime;
  /**
   * Output only. Time when transfer run was started. Parameter ignored by
   * server for input requests.
   *
   * @var string
   */
  public $startTime;
  /**
   * Data transfer run state. Ignored for input requests.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Last time the data transfer run state was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Deprecated. Unique ID of the user on whose behalf transfer is done.
   *
   * @var string
   */
  public $userId;

  /**
   * Output only. Data source id.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * Output only. The BigQuery target dataset id.
   *
   * @param string $destinationDatasetId
   */
  public function setDestinationDatasetId($destinationDatasetId)
  {
    $this->destinationDatasetId = $destinationDatasetId;
  }
  /**
   * @return string
   */
  public function getDestinationDatasetId()
  {
    return $this->destinationDatasetId;
  }
  /**
   * Output only. Email notifications will be sent according to these
   * preferences to the email address of the user who owns the transfer config
   * this run was derived from.
   *
   * @param EmailPreferences $emailPreferences
   */
  public function setEmailPreferences(EmailPreferences $emailPreferences)
  {
    $this->emailPreferences = $emailPreferences;
  }
  /**
   * @return EmailPreferences
   */
  public function getEmailPreferences()
  {
    return $this->emailPreferences;
  }
  /**
   * Output only. Time when transfer run ended. Parameter ignored by server for
   * input requests.
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
   * Status of the transfer run.
   *
   * @param Status $errorStatus
   */
  public function setErrorStatus(Status $errorStatus)
  {
    $this->errorStatus = $errorStatus;
  }
  /**
   * @return Status
   */
  public function getErrorStatus()
  {
    return $this->errorStatus;
  }
  /**
   * Identifier. The resource name of the transfer run. Transfer run names have
   * the form `projects/{project_id}/locations/{location}/transferConfigs/{confi
   * g_id}/runs/{run_id}`. The name is ignored when creating a transfer run.
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
   * Output only. Pub/Sub topic where a notification will be sent after this
   * transfer run finishes. The format for specifying a pubsub topic is:
   * `projects/{project_id}/topics/{topic_id}`
   *
   * @param string $notificationPubsubTopic
   */
  public function setNotificationPubsubTopic($notificationPubsubTopic)
  {
    $this->notificationPubsubTopic = $notificationPubsubTopic;
  }
  /**
   * @return string
   */
  public function getNotificationPubsubTopic()
  {
    return $this->notificationPubsubTopic;
  }
  /**
   * Output only. Parameters specific to each data source. For more information
   * see the bq tab in the 'Setting up a data transfer' section for each data
   * source. For example the parameters for Cloud Storage transfers are listed
   * here: https://cloud.google.com/bigquery-transfer/docs/cloud-storage-
   * transfer#bq
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * For batch transfer runs, specifies the date and time of the data should be
   * ingested.
   *
   * @param string $runTime
   */
  public function setRunTime($runTime)
  {
    $this->runTime = $runTime;
  }
  /**
   * @return string
   */
  public function getRunTime()
  {
    return $this->runTime;
  }
  /**
   * Output only. Describes the schedule of this transfer run if it was created
   * as part of a regular schedule. For batch transfer runs that are scheduled
   * manually, this is empty. NOTE: the system might choose to delay the
   * schedule depending on the current load, so `schedule_time` doesn't always
   * match this.
   *
   * @param string $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * Minimum time after which a transfer run can be started.
   *
   * @param string $scheduleTime
   */
  public function setScheduleTime($scheduleTime)
  {
    $this->scheduleTime = $scheduleTime;
  }
  /**
   * @return string
   */
  public function getScheduleTime()
  {
    return $this->scheduleTime;
  }
  /**
   * Output only. Time when transfer run was started. Parameter ignored by
   * server for input requests.
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
   * Data transfer run state. Ignored for input requests.
   *
   * Accepted values: TRANSFER_STATE_UNSPECIFIED, PENDING, RUNNING, SUCCEEDED,
   * FAILED, CANCELLED
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
   * Output only. Last time the data transfer run state was updated.
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
  /**
   * Deprecated. Unique ID of the user on whose behalf transfer is done.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferRun::class, 'Google_Service_BigQueryDataTransfer_TransferRun');
