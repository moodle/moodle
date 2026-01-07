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

namespace Google\Service\CloudMemorystoreforMemcached;

class DenyMaintenancePeriod extends \Google\Model
{
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  protected $startDateType = Date::class;
  protected $startDateDataType = '';
  protected $timeType = TimeOfDay::class;
  protected $timeDataType = '';

  /**
   * Deny period end date. This can be: * A full date, with non-zero year, month
   * and day values. * A month and day value, with a zero year. Allows recurring
   * deny periods each year. Date matching this period will have to be before
   * the end.
   *
   * @param Date $endDate
   */
  public function setEndDate(Date $endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return Date
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * Deny period start date. This can be: * A full date, with non-zero year,
   * month and day values. * A month and day value, with a zero year. Allows
   * recurring deny periods each year. Date matching this period will have to be
   * the same or after the start.
   *
   * @param Date $startDate
   */
  public function setStartDate(Date $startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return Date
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Time in UTC when the Blackout period starts on start_date and ends on
   * end_date. This can be: * Full time. * All zeros for 00:00:00 UTC
   *
   * @param TimeOfDay $time
   */
  public function setTime(TimeOfDay $time)
  {
    $this->time = $time;
  }
  /**
   * @return TimeOfDay
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DenyMaintenancePeriod::class, 'Google_Service_CloudMemorystoreforMemcached_DenyMaintenancePeriod');
