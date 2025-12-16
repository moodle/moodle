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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1IdentityScheduleConfig extends \Google\Model
{
  protected $nextSyncTimeType = GoogleTypeDateTime::class;
  protected $nextSyncTimeDataType = '';
  /**
   * Optional. The refresh interval to sync the Access Control List information
   * for the documents ingested by this connector. If not set, the access
   * control list will be refreshed at the default interval of 30 minutes. The
   * identity refresh interval can be at least 30 minutes and at most 7 days.
   *
   * @var string
   */
  public $refreshInterval;

  /**
   * Optional. The UTC time when the next data sync is expected to start for the
   * Data Connector. Customers are only able to specify the hour and minute to
   * schedule the data sync. This is utilized when the data connector has a
   * refresh interval greater than 1 day.
   *
   * @param GoogleTypeDateTime $nextSyncTime
   */
  public function setNextSyncTime(GoogleTypeDateTime $nextSyncTime)
  {
    $this->nextSyncTime = $nextSyncTime;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getNextSyncTime()
  {
    return $this->nextSyncTime;
  }
  /**
   * Optional. The refresh interval to sync the Access Control List information
   * for the documents ingested by this connector. If not set, the access
   * control list will be refreshed at the default interval of 30 minutes. The
   * identity refresh interval can be at least 30 minutes and at most 7 days.
   *
   * @param string $refreshInterval
   */
  public function setRefreshInterval($refreshInterval)
  {
    $this->refreshInterval = $refreshInterval;
  }
  /**
   * @return string
   */
  public function getRefreshInterval()
  {
    return $this->refreshInterval;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1IdentityScheduleConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1IdentityScheduleConfig');
