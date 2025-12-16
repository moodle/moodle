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

namespace Google\Service\OracleDatabase;

class MaintenanceWindow extends \Google\Collection
{
  /**
   * Default unspecified value.
   */
  public const PATCHING_MODE_PATCHING_MODE_UNSPECIFIED = 'PATCHING_MODE_UNSPECIFIED';
  /**
   * Updates the Cloud Exadata database server hosts in a rolling fashion.
   */
  public const PATCHING_MODE_ROLLING = 'ROLLING';
  /**
   * The non-rolling maintenance method first updates your storage servers at
   * the same time, then your database servers at the same time.
   */
  public const PATCHING_MODE_NON_ROLLING = 'NON_ROLLING';
  /**
   * Default unspecified value.
   */
  public const PREFERENCE_MAINTENANCE_WINDOW_PREFERENCE_UNSPECIFIED = 'MAINTENANCE_WINDOW_PREFERENCE_UNSPECIFIED';
  /**
   * Custom preference.
   */
  public const PREFERENCE_CUSTOM_PREFERENCE = 'CUSTOM_PREFERENCE';
  /**
   * No preference.
   */
  public const PREFERENCE_NO_PREFERENCE = 'NO_PREFERENCE';
  protected $collection_key = 'weeksOfMonth';
  /**
   * Optional. Determines the amount of time the system will wait before the
   * start of each database server patching operation. Custom action timeout is
   * in minutes and valid value is between 15 to 120 (inclusive).
   *
   * @var int
   */
  public $customActionTimeoutMins;
  /**
   * Optional. Days during the week when maintenance should be performed.
   *
   * @var string[]
   */
  public $daysOfWeek;
  /**
   * Optional. The window of hours during the day when maintenance should be
   * performed. The window is a 4 hour slot. Valid values are: 0 - represents
   * time slot 0:00 - 3:59 UTC 4 - represents time slot 4:00 - 7:59 UTC 8 -
   * represents time slot 8:00 - 11:59 UTC 12 - represents time slot 12:00 -
   * 15:59 UTC 16 - represents time slot 16:00 - 19:59 UTC 20 - represents time
   * slot 20:00 - 23:59 UTC
   *
   * @var int[]
   */
  public $hoursOfDay;
  /**
   * Optional. If true, enables the configuration of a custom action timeout
   * (waiting period) between database server patching operations.
   *
   * @var bool
   */
  public $isCustomActionTimeoutEnabled;
  /**
   * Optional. Lead time window allows user to set a lead time to prepare for a
   * down time. The lead time is in weeks and valid value is between 1 to 4.
   *
   * @var int
   */
  public $leadTimeWeek;
  /**
   * Optional. Months during the year when maintenance should be performed.
   *
   * @var string[]
   */
  public $months;
  /**
   * Optional. Cloud CloudExadataInfrastructure node patching method, either
   * "ROLLING" or "NONROLLING". Default value is ROLLING.
   *
   * @var string
   */
  public $patchingMode;
  /**
   * Optional. The maintenance window scheduling preference.
   *
   * @var string
   */
  public $preference;
  /**
   * Optional. Weeks during the month when maintenance should be performed.
   * Weeks start on the 1st, 8th, 15th, and 22nd days of the month, and have a
   * duration of 7 days. Weeks start and end based on calendar dates, not days
   * of the week.
   *
   * @var int[]
   */
  public $weeksOfMonth;

  /**
   * Optional. Determines the amount of time the system will wait before the
   * start of each database server patching operation. Custom action timeout is
   * in minutes and valid value is between 15 to 120 (inclusive).
   *
   * @param int $customActionTimeoutMins
   */
  public function setCustomActionTimeoutMins($customActionTimeoutMins)
  {
    $this->customActionTimeoutMins = $customActionTimeoutMins;
  }
  /**
   * @return int
   */
  public function getCustomActionTimeoutMins()
  {
    return $this->customActionTimeoutMins;
  }
  /**
   * Optional. Days during the week when maintenance should be performed.
   *
   * @param string[] $daysOfWeek
   */
  public function setDaysOfWeek($daysOfWeek)
  {
    $this->daysOfWeek = $daysOfWeek;
  }
  /**
   * @return string[]
   */
  public function getDaysOfWeek()
  {
    return $this->daysOfWeek;
  }
  /**
   * Optional. The window of hours during the day when maintenance should be
   * performed. The window is a 4 hour slot. Valid values are: 0 - represents
   * time slot 0:00 - 3:59 UTC 4 - represents time slot 4:00 - 7:59 UTC 8 -
   * represents time slot 8:00 - 11:59 UTC 12 - represents time slot 12:00 -
   * 15:59 UTC 16 - represents time slot 16:00 - 19:59 UTC 20 - represents time
   * slot 20:00 - 23:59 UTC
   *
   * @param int[] $hoursOfDay
   */
  public function setHoursOfDay($hoursOfDay)
  {
    $this->hoursOfDay = $hoursOfDay;
  }
  /**
   * @return int[]
   */
  public function getHoursOfDay()
  {
    return $this->hoursOfDay;
  }
  /**
   * Optional. If true, enables the configuration of a custom action timeout
   * (waiting period) between database server patching operations.
   *
   * @param bool $isCustomActionTimeoutEnabled
   */
  public function setIsCustomActionTimeoutEnabled($isCustomActionTimeoutEnabled)
  {
    $this->isCustomActionTimeoutEnabled = $isCustomActionTimeoutEnabled;
  }
  /**
   * @return bool
   */
  public function getIsCustomActionTimeoutEnabled()
  {
    return $this->isCustomActionTimeoutEnabled;
  }
  /**
   * Optional. Lead time window allows user to set a lead time to prepare for a
   * down time. The lead time is in weeks and valid value is between 1 to 4.
   *
   * @param int $leadTimeWeek
   */
  public function setLeadTimeWeek($leadTimeWeek)
  {
    $this->leadTimeWeek = $leadTimeWeek;
  }
  /**
   * @return int
   */
  public function getLeadTimeWeek()
  {
    return $this->leadTimeWeek;
  }
  /**
   * Optional. Months during the year when maintenance should be performed.
   *
   * @param string[] $months
   */
  public function setMonths($months)
  {
    $this->months = $months;
  }
  /**
   * @return string[]
   */
  public function getMonths()
  {
    return $this->months;
  }
  /**
   * Optional. Cloud CloudExadataInfrastructure node patching method, either
   * "ROLLING" or "NONROLLING". Default value is ROLLING.
   *
   * Accepted values: PATCHING_MODE_UNSPECIFIED, ROLLING, NON_ROLLING
   *
   * @param self::PATCHING_MODE_* $patchingMode
   */
  public function setPatchingMode($patchingMode)
  {
    $this->patchingMode = $patchingMode;
  }
  /**
   * @return self::PATCHING_MODE_*
   */
  public function getPatchingMode()
  {
    return $this->patchingMode;
  }
  /**
   * Optional. The maintenance window scheduling preference.
   *
   * Accepted values: MAINTENANCE_WINDOW_PREFERENCE_UNSPECIFIED,
   * CUSTOM_PREFERENCE, NO_PREFERENCE
   *
   * @param self::PREFERENCE_* $preference
   */
  public function setPreference($preference)
  {
    $this->preference = $preference;
  }
  /**
   * @return self::PREFERENCE_*
   */
  public function getPreference()
  {
    return $this->preference;
  }
  /**
   * Optional. Weeks during the month when maintenance should be performed.
   * Weeks start on the 1st, 8th, 15th, and 22nd days of the month, and have a
   * duration of 7 days. Weeks start and end based on calendar dates, not days
   * of the week.
   *
   * @param int[] $weeksOfMonth
   */
  public function setWeeksOfMonth($weeksOfMonth)
  {
    $this->weeksOfMonth = $weeksOfMonth;
  }
  /**
   * @return int[]
   */
  public function getWeeksOfMonth()
  {
    return $this->weeksOfMonth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaintenanceWindow::class, 'Google_Service_OracleDatabase_MaintenanceWindow');
