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

namespace Google\Service\MyBusinessBusinessInformation;

class SpecialHourPeriod extends \Google\Model
{
  protected $closeTimeType = TimeOfDay::class;
  protected $closeTimeDataType = '';
  /**
   * Optional. If true, `end_date`, `open_time`, and `close_time` are ignored,
   * and the date specified in `start_date` is treated as the location being
   * closed for the entire day.
   *
   * @var bool
   */
  public $closed;
  protected $endDateType = Date::class;
  protected $endDateDataType = '';
  protected $openTimeType = TimeOfDay::class;
  protected $openTimeDataType = '';
  protected $startDateType = Date::class;
  protected $startDateDataType = '';

  /**
   * Optional. Valid values are 00:00-24:00, where 24:00 represents midnight at
   * the end of the specified day field. Must be specified if `closed` is false.
   *
   * @param TimeOfDay $closeTime
   */
  public function setCloseTime(TimeOfDay $closeTime)
  {
    $this->closeTime = $closeTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getCloseTime()
  {
    return $this->closeTime;
  }
  /**
   * Optional. If true, `end_date`, `open_time`, and `close_time` are ignored,
   * and the date specified in `start_date` is treated as the location being
   * closed for the entire day.
   *
   * @param bool $closed
   */
  public function setClosed($closed)
  {
    $this->closed = $closed;
  }
  /**
   * @return bool
   */
  public function getClosed()
  {
    return $this->closed;
  }
  /**
   * Optional. The calendar date this special hour period ends on. If `end_date`
   * field is not set, default to the date specified in `start_date`. If set,
   * this field must be equal to or at most 1 day after `start_date`.
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
   * Optional. Valid values are 00:00-24:00 where 24:00 represents midnight at
   * the end of the specified day field. Must be specified if `closed` is false.
   *
   * @param TimeOfDay $openTime
   */
  public function setOpenTime(TimeOfDay $openTime)
  {
    $this->openTime = $openTime;
  }
  /**
   * @return TimeOfDay
   */
  public function getOpenTime()
  {
    return $this->openTime;
  }
  /**
   * Required. The calendar date this special hour period starts on.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpecialHourPeriod::class, 'Google_Service_MyBusinessBusinessInformation_SpecialHourPeriod');
