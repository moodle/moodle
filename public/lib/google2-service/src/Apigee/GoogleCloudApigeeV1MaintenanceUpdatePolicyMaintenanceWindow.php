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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow extends \Google\Model
{
  /**
   * The day of the week is unspecified.
   */
  public const DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const DAY_SUNDAY = 'SUNDAY';
  /**
   * Required. Preferred day of the week for maintenance, e.g. MONDAY, TUESDAY,
   * etc.
   *
   * @var string
   */
  public $day;
  protected $startTimeType = GoogleTypeTimeOfDay::class;
  protected $startTimeDataType = '';

  /**
   * Required. Preferred day of the week for maintenance, e.g. MONDAY, TUESDAY,
   * etc.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::DAY_* $day
   */
  public function setDay($day)
  {
    $this->day = $day;
  }
  /**
   * @return self::DAY_*
   */
  public function getDay()
  {
    return $this->day;
  }
  /**
   * Required. The start time (UTC) of the maintenance window.
   *
   * @param GoogleTypeTimeOfDay $startTime
   */
  public function setStartTime(GoogleTypeTimeOfDay $startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return GoogleTypeTimeOfDay
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow::class, 'Google_Service_Apigee_GoogleCloudApigeeV1MaintenanceUpdatePolicyMaintenanceWindow');
