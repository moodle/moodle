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

class TransferConfig extends \Google\Model
{
  /**
   * Type unspecified. This defaults to `NATIVE` table.
   */
  public const MANAGED_TABLE_TYPE_MANAGED_TABLE_TYPE_UNSPECIFIED = 'MANAGED_TABLE_TYPE_UNSPECIFIED';
  /**
   * The managed table is a native BigQuery table. This is the default value.
   */
  public const MANAGED_TABLE_TYPE_NATIVE = 'NATIVE';
  /**
   * The managed table is a BigQuery table for Apache Iceberg (formerly BigLake
   * managed tables), with a BigLake configuration.
   */
  public const MANAGED_TABLE_TYPE_BIGLAKE = 'BIGLAKE';
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
   * The number of days to look back to automatically refresh the data. For
   * example, if `data_refresh_window_days = 10`, then every day BigQuery
   * reingests data for [today-10, today-1], rather than ingesting data for just
   * [today-1]. Only valid if the data source supports the feature. Set the
   * value to 0 to use the default value.
   *
   * @var int
   */
  public $dataRefreshWindowDays;
  /**
   * Data source ID. This cannot be changed once data transfer is created. The
   * full list of available data source IDs can be returned through an API call:
   * https://cloud.google.com/bigquery-transfer/docs/reference/datatransfer/rest
   * /v1/projects.locations.dataSources/list
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Output only. Region in which BigQuery dataset is located.
   *
   * @var string
   */
  public $datasetRegion;
  /**
   * The BigQuery target dataset id.
   *
   * @var string
   */
  public $destinationDatasetId;
  /**
   * Is this config disabled. When set to true, no runs will be scheduled for
   * this transfer config.
   *
   * @var bool
   */
  public $disabled;
  /**
   * User specified display name for the data transfer.
   *
   * @var string
   */
  public $displayName;
  protected $emailPreferencesType = EmailPreferences::class;
  protected $emailPreferencesDataType = '';
  protected $encryptionConfigurationType = EncryptionConfiguration::class;
  protected $encryptionConfigurationDataType = '';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The classification of the destination table.
   *
   * @var string
   */
  public $managedTableType;
  /**
   * Identifier. The resource name of the transfer config. Transfer config names
   * have the form either
   * `projects/{project_id}/locations/{region}/transferConfigs/{config_id}` or
   * `projects/{project_id}/transferConfigs/{config_id}`, where `config_id` is
   * usually a UUID, even though it is not guaranteed or required. The name is
   * ignored when creating a transfer config.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Next time when data transfer will run.
   *
   * @var string
   */
  public $nextRunTime;
  /**
   * Pub/Sub topic where notifications will be sent after transfer runs
   * associated with this transfer config finish. The format for specifying a
   * pubsub topic is: `projects/{project_id}/topics/{topic_id}`
   *
   * @var string
   */
  public $notificationPubsubTopic;
  protected $ownerInfoType = UserInfo::class;
  protected $ownerInfoDataType = '';
  /**
   * Parameters specific to each data source. For more information see the bq
   * tab in the 'Setting up a data transfer' section for each data source. For
   * example the parameters for Cloud Storage transfers are listed here:
   * https://cloud.google.com/bigquery-transfer/docs/cloud-storage-transfer#bq
   *
   * @var array[]
   */
  public $params;
  /**
   * Data transfer schedule. If the data source does not support a custom
   * schedule, this should be empty. If it is empty, the default value for the
   * data source will be used. The specified times are in UTC. Examples of valid
   * format: `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`,
   * and `first sunday of quarter 00:00`. See more explanation about the format
   * here: https://cloud.google.com/appengine/docs/flexible/python/scheduling-
   * jobs-with-cron-yaml#the_schedule_format NOTE: The minimum interval time
   * between recurring transfers depends on the data source; refer to the
   * documentation for your data source.
   *
   * @var string
   */
  public $schedule;
  protected $scheduleOptionsType = ScheduleOptions::class;
  protected $scheduleOptionsDataType = '';
  protected $scheduleOptionsV2Type = ScheduleOptionsV2::class;
  protected $scheduleOptionsV2DataType = '';
  /**
   * Output only. State of the most recently updated transfer run.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Data transfer modification time. Ignored by server on input.
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
   * The number of days to look back to automatically refresh the data. For
   * example, if `data_refresh_window_days = 10`, then every day BigQuery
   * reingests data for [today-10, today-1], rather than ingesting data for just
   * [today-1]. Only valid if the data source supports the feature. Set the
   * value to 0 to use the default value.
   *
   * @param int $dataRefreshWindowDays
   */
  public function setDataRefreshWindowDays($dataRefreshWindowDays)
  {
    $this->dataRefreshWindowDays = $dataRefreshWindowDays;
  }
  /**
   * @return int
   */
  public function getDataRefreshWindowDays()
  {
    return $this->dataRefreshWindowDays;
  }
  /**
   * Data source ID. This cannot be changed once data transfer is created. The
   * full list of available data source IDs can be returned through an API call:
   * https://cloud.google.com/bigquery-transfer/docs/reference/datatransfer/rest
   * /v1/projects.locations.dataSources/list
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
   * Output only. Region in which BigQuery dataset is located.
   *
   * @param string $datasetRegion
   */
  public function setDatasetRegion($datasetRegion)
  {
    $this->datasetRegion = $datasetRegion;
  }
  /**
   * @return string
   */
  public function getDatasetRegion()
  {
    return $this->datasetRegion;
  }
  /**
   * The BigQuery target dataset id.
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
   * Is this config disabled. When set to true, no runs will be scheduled for
   * this transfer config.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * User specified display name for the data transfer.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Email notifications will be sent according to these preferences to the
   * email address of the user who owns this transfer config.
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
   * The encryption configuration part. Currently, it is only used for the
   * optional KMS key name. The BigQuery service account of your project must be
   * granted permissions to use the key. Read methods will return the key name
   * applied in effect. Write methods will apply the key if it is present, or
   * otherwise try to apply project default keys if it is absent.
   *
   * @param EncryptionConfiguration $encryptionConfiguration
   */
  public function setEncryptionConfiguration(EncryptionConfiguration $encryptionConfiguration)
  {
    $this->encryptionConfiguration = $encryptionConfiguration;
  }
  /**
   * @return EncryptionConfiguration
   */
  public function getEncryptionConfiguration()
  {
    return $this->encryptionConfiguration;
  }
  /**
   * Output only. Error code with detailed information about reason of the
   * latest config failure.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The classification of the destination table.
   *
   * Accepted values: MANAGED_TABLE_TYPE_UNSPECIFIED, NATIVE, BIGLAKE
   *
   * @param self::MANAGED_TABLE_TYPE_* $managedTableType
   */
  public function setManagedTableType($managedTableType)
  {
    $this->managedTableType = $managedTableType;
  }
  /**
   * @return self::MANAGED_TABLE_TYPE_*
   */
  public function getManagedTableType()
  {
    return $this->managedTableType;
  }
  /**
   * Identifier. The resource name of the transfer config. Transfer config names
   * have the form either
   * `projects/{project_id}/locations/{region}/transferConfigs/{config_id}` or
   * `projects/{project_id}/transferConfigs/{config_id}`, where `config_id` is
   * usually a UUID, even though it is not guaranteed or required. The name is
   * ignored when creating a transfer config.
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
   * Output only. Next time when data transfer will run.
   *
   * @param string $nextRunTime
   */
  public function setNextRunTime($nextRunTime)
  {
    $this->nextRunTime = $nextRunTime;
  }
  /**
   * @return string
   */
  public function getNextRunTime()
  {
    return $this->nextRunTime;
  }
  /**
   * Pub/Sub topic where notifications will be sent after transfer runs
   * associated with this transfer config finish. The format for specifying a
   * pubsub topic is: `projects/{project_id}/topics/{topic_id}`
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
   * Output only. Information about the user whose credentials are used to
   * transfer data. Populated only for `transferConfigs.get` requests. In case
   * the user information is not available, this field will not be populated.
   *
   * @param UserInfo $ownerInfo
   */
  public function setOwnerInfo(UserInfo $ownerInfo)
  {
    $this->ownerInfo = $ownerInfo;
  }
  /**
   * @return UserInfo
   */
  public function getOwnerInfo()
  {
    return $this->ownerInfo;
  }
  /**
   * Parameters specific to each data source. For more information see the bq
   * tab in the 'Setting up a data transfer' section for each data source. For
   * example the parameters for Cloud Storage transfers are listed here:
   * https://cloud.google.com/bigquery-transfer/docs/cloud-storage-transfer#bq
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
   * Data transfer schedule. If the data source does not support a custom
   * schedule, this should be empty. If it is empty, the default value for the
   * data source will be used. The specified times are in UTC. Examples of valid
   * format: `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`,
   * and `first sunday of quarter 00:00`. See more explanation about the format
   * here: https://cloud.google.com/appengine/docs/flexible/python/scheduling-
   * jobs-with-cron-yaml#the_schedule_format NOTE: The minimum interval time
   * between recurring transfers depends on the data source; refer to the
   * documentation for your data source.
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
   * Options customizing the data transfer schedule.
   *
   * @param ScheduleOptions $scheduleOptions
   */
  public function setScheduleOptions(ScheduleOptions $scheduleOptions)
  {
    $this->scheduleOptions = $scheduleOptions;
  }
  /**
   * @return ScheduleOptions
   */
  public function getScheduleOptions()
  {
    return $this->scheduleOptions;
  }
  /**
   * Options customizing different types of data transfer schedule. This field
   * replaces "schedule" and "schedule_options" fields. ScheduleOptionsV2 cannot
   * be used together with ScheduleOptions/Schedule.
   *
   * @param ScheduleOptionsV2 $scheduleOptionsV2
   */
  public function setScheduleOptionsV2(ScheduleOptionsV2 $scheduleOptionsV2)
  {
    $this->scheduleOptionsV2 = $scheduleOptionsV2;
  }
  /**
   * @return ScheduleOptionsV2
   */
  public function getScheduleOptionsV2()
  {
    return $this->scheduleOptionsV2;
  }
  /**
   * Output only. State of the most recently updated transfer run.
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
   * Output only. Data transfer modification time. Ignored by server on input.
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
class_alias(TransferConfig::class, 'Google_Service_BigQueryDataTransfer_TransferConfig');
