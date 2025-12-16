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

namespace Google\Service\Looker;

class DenyMaintenancePeriod extends \Google\Model
{
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  protected $startDateType = Date::class;
  protected $startDateDataType = '';
  protected $timeType = TimeOfDay::class;
  protected $timeDataType = '';

  /**
   * Required. End date of the deny maintenance period.
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
   * Required. Start date of the deny maintenance period.
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
   * Required. Time in UTC when the period starts and ends.
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
class_alias(DenyMaintenancePeriod::class, 'Google_Service_Looker_DenyMaintenancePeriod');
