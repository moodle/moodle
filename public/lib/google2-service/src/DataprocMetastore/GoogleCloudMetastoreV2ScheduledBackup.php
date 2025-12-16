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

namespace Google\Service\DataprocMetastore;

class GoogleCloudMetastoreV2ScheduledBackup extends \Google\Model
{
  /**
   * @var string
   */
  public $backupLocation;
  /**
   * @var string
   */
  public $cronSchedule;
  /**
   * @var bool
   */
  public $enabled;
  protected $latestBackupType = GoogleCloudMetastoreV2LatestBackup::class;
  protected $latestBackupDataType = '';
  /**
   * @var string
   */
  public $nextScheduledTime;
  /**
   * @var string
   */
  public $timeZone;

  /**
   * @param string
   */
  public function setBackupLocation($backupLocation)
  {
    $this->backupLocation = $backupLocation;
  }
  /**
   * @return string
   */
  public function getBackupLocation()
  {
    return $this->backupLocation;
  }
  /**
   * @param string
   */
  public function setCronSchedule($cronSchedule)
  {
    $this->cronSchedule = $cronSchedule;
  }
  /**
   * @return string
   */
  public function getCronSchedule()
  {
    return $this->cronSchedule;
  }
  /**
   * @param bool
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * @param GoogleCloudMetastoreV2LatestBackup
   */
  public function setLatestBackup(GoogleCloudMetastoreV2LatestBackup $latestBackup)
  {
    $this->latestBackup = $latestBackup;
  }
  /**
   * @return GoogleCloudMetastoreV2LatestBackup
   */
  public function getLatestBackup()
  {
    return $this->latestBackup;
  }
  /**
   * @param string
   */
  public function setNextScheduledTime($nextScheduledTime)
  {
    $this->nextScheduledTime = $nextScheduledTime;
  }
  /**
   * @return string
   */
  public function getNextScheduledTime()
  {
    return $this->nextScheduledTime;
  }
  /**
   * @param string
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMetastoreV2ScheduledBackup::class, 'Google_Service_DataprocMetastore_GoogleCloudMetastoreV2ScheduledBackup');
