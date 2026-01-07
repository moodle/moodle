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

namespace Google\Service\SQLAdmin;

class MaintenanceWindow extends \Google\Model
{
  /**
   * This is an unknown maintenance timing preference.
   */
  public const UPDATE_TRACK_SQL_UPDATE_TRACK_UNSPECIFIED = 'SQL_UPDATE_TRACK_UNSPECIFIED';
  /**
   * For an instance with a scheduled maintenance window, this maintenance
   * timing indicates that the maintenance update is scheduled 7 to 14 days
   * after the notification is sent out. Also referred to as `Week 1` (Console)
   * and `preview` (gcloud CLI).
   */
  public const UPDATE_TRACK_canary = 'canary';
  /**
   * For an instance with a scheduled maintenance window, this maintenance
   * timing indicates that the maintenance update is scheduled 15 to 21 days
   * after the notification is sent out. Also referred to as `Week 2` (Console)
   * and `production` (gcloud CLI).
   */
  public const UPDATE_TRACK_stable = 'stable';
  /**
   * For instance with a scheduled maintenance window, this maintenance timing
   * indicates that the maintenance update is scheduled 35 to 42 days after the
   * notification is sent out.
   */
  public const UPDATE_TRACK_week5 = 'week5';
  /**
   * Day of week - `MONDAY`, `TUESDAY`, `WEDNESDAY`, `THURSDAY`, `FRIDAY`,
   * `SATURDAY`, or `SUNDAY`. Specify in the UTC time zone. Returned in output
   * as an integer, 1 to 7, where `1` equals Monday.
   *
   * @var int
   */
  public $day;
  /**
   * Hour of day - 0 to 23. Specify in the UTC time zone.
   *
   * @var int
   */
  public $hour;
  /**
   * This is always `sql#maintenanceWindow`.
   *
   * @var string
   */
  public $kind;
  /**
   * Maintenance timing settings: `canary`, `stable`, or `week5`. For more
   * information, see [About maintenance on Cloud SQL
   * instances](https://cloud.google.com/sql/docs/mysql/maintenance).
   *
   * @var string
   */
  public $updateTrack;

  /**
   * Day of week - `MONDAY`, `TUESDAY`, `WEDNESDAY`, `THURSDAY`, `FRIDAY`,
   * `SATURDAY`, or `SUNDAY`. Specify in the UTC time zone. Returned in output
   * as an integer, 1 to 7, where `1` equals Monday.
   *
   * @param int $day
   */
  public function setDay($day)
  {
    $this->day = $day;
  }
  /**
   * @return int
   */
  public function getDay()
  {
    return $this->day;
  }
  /**
   * Hour of day - 0 to 23. Specify in the UTC time zone.
   *
   * @param int $hour
   */
  public function setHour($hour)
  {
    $this->hour = $hour;
  }
  /**
   * @return int
   */
  public function getHour()
  {
    return $this->hour;
  }
  /**
   * This is always `sql#maintenanceWindow`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Maintenance timing settings: `canary`, `stable`, or `week5`. For more
   * information, see [About maintenance on Cloud SQL
   * instances](https://cloud.google.com/sql/docs/mysql/maintenance).
   *
   * Accepted values: SQL_UPDATE_TRACK_UNSPECIFIED, canary, stable, week5
   *
   * @param self::UPDATE_TRACK_* $updateTrack
   */
  public function setUpdateTrack($updateTrack)
  {
    $this->updateTrack = $updateTrack;
  }
  /**
   * @return self::UPDATE_TRACK_*
   */
  public function getUpdateTrack()
  {
    return $this->updateTrack;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceWindow::class, 'Google_Service_SQLAdmin_MaintenanceWindow');
