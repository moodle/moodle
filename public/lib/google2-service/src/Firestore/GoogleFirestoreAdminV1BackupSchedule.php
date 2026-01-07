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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1BackupSchedule extends \Google\Model
{
  /**
   * Output only. The timestamp at which this backup schedule was created and
   * effective since. No backups will be created for this schedule before this
   * time.
   *
   * @var string
   */
  public $createTime;
  protected $dailyRecurrenceType = GoogleFirestoreAdminV1DailyRecurrence::class;
  protected $dailyRecurrenceDataType = '';
  /**
   * Output only. The unique backup schedule identifier across all locations and
   * databases for the given project. This will be auto-assigned. Format is
   * `projects/{project}/databases/{database}/backupSchedules/{backup_schedule}`
   *
   * @var string
   */
  public $name;
  /**
   * At what relative time in the future, compared to its creation time, the
   * backup should be deleted, e.g. keep backups for 7 days. The maximum
   * supported retention period is 14 weeks.
   *
   * @var string
   */
  public $retention;
  /**
   * Output only. The timestamp at which this backup schedule was most recently
   * updated. When a backup schedule is first created, this is the same as
   * create_time.
   *
   * @var string
   */
  public $updateTime;
  protected $weeklyRecurrenceType = GoogleFirestoreAdminV1WeeklyRecurrence::class;
  protected $weeklyRecurrenceDataType = '';

  /**
   * Output only. The timestamp at which this backup schedule was created and
   * effective since. No backups will be created for this schedule before this
   * time.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * For a schedule that runs daily.
   *
   * @param GoogleFirestoreAdminV1DailyRecurrence $dailyRecurrence
   */
  public function setDailyRecurrence(GoogleFirestoreAdminV1DailyRecurrence $dailyRecurrence)
  {
    $this->dailyRecurrence = $dailyRecurrence;
  }
  /**
   * @return GoogleFirestoreAdminV1DailyRecurrence
   */
  public function getDailyRecurrence()
  {
    return $this->dailyRecurrence;
  }
  /**
   * Output only. The unique backup schedule identifier across all locations and
   * databases for the given project. This will be auto-assigned. Format is
   * `projects/{project}/databases/{database}/backupSchedules/{backup_schedule}`
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
   * At what relative time in the future, compared to its creation time, the
   * backup should be deleted, e.g. keep backups for 7 days. The maximum
   * supported retention period is 14 weeks.
   *
   * @param string $retention
   */
  public function setRetention($retention)
  {
    $this->retention = $retention;
  }
  /**
   * @return string
   */
  public function getRetention()
  {
    return $this->retention;
  }
  /**
   * Output only. The timestamp at which this backup schedule was most recently
   * updated. When a backup schedule is first created, this is the same as
   * create_time.
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
   * For a schedule that runs weekly on a specific day.
   *
   * @param GoogleFirestoreAdminV1WeeklyRecurrence $weeklyRecurrence
   */
  public function setWeeklyRecurrence(GoogleFirestoreAdminV1WeeklyRecurrence $weeklyRecurrence)
  {
    $this->weeklyRecurrence = $weeklyRecurrence;
  }
  /**
   * @return GoogleFirestoreAdminV1WeeklyRecurrence
   */
  public function getWeeklyRecurrence()
  {
    return $this->weeklyRecurrence;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1BackupSchedule::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1BackupSchedule');
