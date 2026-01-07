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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ModelMonitoringAlertConfig extends \Google\Collection
{
  protected $collection_key = 'notificationChannels';
  protected $emailAlertConfigType = GoogleCloudAiplatformV1ModelMonitoringAlertConfigEmailAlertConfig::class;
  protected $emailAlertConfigDataType = '';
  /**
   * Dump the anomalies to Cloud Logging. The anomalies will be put to json
   * payload encoded from proto ModelMonitoringStatsAnomalies. This can be
   * further synced to Pub/Sub or any other services supported by Cloud Logging.
   *
   * @var bool
   */
  public $enableLogging;
  /**
   * Resource names of the NotificationChannels to send alert. Must be of the
   * format `projects//notificationChannels/`
   *
   * @var string[]
   */
  public $notificationChannels;

  /**
   * Email alert config.
   *
   * @param GoogleCloudAiplatformV1ModelMonitoringAlertConfigEmailAlertConfig $emailAlertConfig
   */
  public function setEmailAlertConfig(GoogleCloudAiplatformV1ModelMonitoringAlertConfigEmailAlertConfig $emailAlertConfig)
  {
    $this->emailAlertConfig = $emailAlertConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1ModelMonitoringAlertConfigEmailAlertConfig
   */
  public function getEmailAlertConfig()
  {
    return $this->emailAlertConfig;
  }
  /**
   * Dump the anomalies to Cloud Logging. The anomalies will be put to json
   * payload encoded from proto ModelMonitoringStatsAnomalies. This can be
   * further synced to Pub/Sub or any other services supported by Cloud Logging.
   *
   * @param bool $enableLogging
   */
  public function setEnableLogging($enableLogging)
  {
    $this->enableLogging = $enableLogging;
  }
  /**
   * @return bool
   */
  public function getEnableLogging()
  {
    return $this->enableLogging;
  }
  /**
   * Resource names of the NotificationChannels to send alert. Must be of the
   * format `projects//notificationChannels/`
   *
   * @param string[] $notificationChannels
   */
  public function setNotificationChannels($notificationChannels)
  {
    $this->notificationChannels = $notificationChannels;
  }
  /**
   * @return string[]
   */
  public function getNotificationChannels()
  {
    return $this->notificationChannels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelMonitoringAlertConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelMonitoringAlertConfig');
