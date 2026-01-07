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

namespace Google\Service\CloudAlloyDBAdmin;

class ContinuousBackupInfo extends \Google\Collection
{
  protected $collection_key = 'schedule';
  /**
   * Output only. The earliest restorable time that can be restored to. If
   * continuous backups and recovery was recently enabled, the earliest
   * restorable time is the creation time of the earliest eligible backup within
   * this cluster's continuous backup recovery window. After a cluster has had
   * continuous backups enabled for the duration of its recovery window, the
   * earliest restorable time becomes "now minus the recovery window". For
   * example, assuming a point in time recovery is attempted at 04/16/2025
   * 3:23:00PM with a 14d recovery window, the earliest restorable time would be
   * 04/02/2025 3:23:00PM. This field is only visible if the
   * CLUSTER_VIEW_CONTINUOUS_BACKUP cluster view is provided.
   *
   * @var string
   */
  public $earliestRestorableTime;
  /**
   * Output only. When ContinuousBackup was most recently enabled. Set to null
   * if ContinuousBackup is not enabled.
   *
   * @var string
   */
  public $enabledTime;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  /**
   * Output only. Days of the week on which a continuous backup is taken.
   *
   * @var string[]
   */
  public $schedule;

  /**
   * Output only. The earliest restorable time that can be restored to. If
   * continuous backups and recovery was recently enabled, the earliest
   * restorable time is the creation time of the earliest eligible backup within
   * this cluster's continuous backup recovery window. After a cluster has had
   * continuous backups enabled for the duration of its recovery window, the
   * earliest restorable time becomes "now minus the recovery window". For
   * example, assuming a point in time recovery is attempted at 04/16/2025
   * 3:23:00PM with a 14d recovery window, the earliest restorable time would be
   * 04/02/2025 3:23:00PM. This field is only visible if the
   * CLUSTER_VIEW_CONTINUOUS_BACKUP cluster view is provided.
   *
   * @param string $earliestRestorableTime
   */
  public function setEarliestRestorableTime($earliestRestorableTime)
  {
    $this->earliestRestorableTime = $earliestRestorableTime;
  }
  /**
   * @return string
   */
  public function getEarliestRestorableTime()
  {
    return $this->earliestRestorableTime;
  }
  /**
   * Output only. When ContinuousBackup was most recently enabled. Set to null
   * if ContinuousBackup is not enabled.
   *
   * @param string $enabledTime
   */
  public function setEnabledTime($enabledTime)
  {
    $this->enabledTime = $enabledTime;
  }
  /**
   * @return string
   */
  public function getEnabledTime()
  {
    return $this->enabledTime;
  }
  /**
   * Output only. The encryption information for the WALs and backups required
   * for ContinuousBackup.
   *
   * @param EncryptionInfo $encryptionInfo
   */
  public function setEncryptionInfo(EncryptionInfo $encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Output only. Days of the week on which a continuous backup is taken.
   *
   * @param string[] $schedule
   */
  public function setSchedule($schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return string[]
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContinuousBackupInfo::class, 'Google_Service_CloudAlloyDBAdmin_ContinuousBackupInfo');
