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

class TimePeriod extends \Google\Model
{
  /**
   * The day of the week is unspecified.
   */
  public const CLOSE_DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const CLOSE_DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const CLOSE_DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const CLOSE_DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const CLOSE_DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const CLOSE_DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const CLOSE_DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const CLOSE_DAY_SUNDAY = 'SUNDAY';
  /**
   * The day of the week is unspecified.
   */
  public const OPEN_DAY_DAY_OF_WEEK_UNSPECIFIED = 'DAY_OF_WEEK_UNSPECIFIED';
  /**
   * Monday
   */
  public const OPEN_DAY_MONDAY = 'MONDAY';
  /**
   * Tuesday
   */
  public const OPEN_DAY_TUESDAY = 'TUESDAY';
  /**
   * Wednesday
   */
  public const OPEN_DAY_WEDNESDAY = 'WEDNESDAY';
  /**
   * Thursday
   */
  public const OPEN_DAY_THURSDAY = 'THURSDAY';
  /**
   * Friday
   */
  public const OPEN_DAY_FRIDAY = 'FRIDAY';
  /**
   * Saturday
   */
  public const OPEN_DAY_SATURDAY = 'SATURDAY';
  /**
   * Sunday
   */
  public const OPEN_DAY_SUNDAY = 'SUNDAY';
  /**
   * Required. Indicates the day of the week this period ends on.
   *
   * @var string
   */
  public $closeDay;
  protected $closeTimeType = TimeOfDay::class;
  protected $closeTimeDataType = '';
  /**
   * Required. Indicates the day of the week this period starts on.
   *
   * @var string
   */
  public $openDay;
  protected $openTimeType = TimeOfDay::class;
  protected $openTimeDataType = '';

  /**
   * Required. Indicates the day of the week this period ends on.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::CLOSE_DAY_* $closeDay
   */
  public function setCloseDay($closeDay)
  {
    $this->closeDay = $closeDay;
  }
  /**
   * @return self::CLOSE_DAY_*
   */
  public function getCloseDay()
  {
    return $this->closeDay;
  }
  /**
   * Required. Valid values are 00:00-24:00, where 24:00 represents midnight at
   * the end of the specified day field.
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
   * Required. Indicates the day of the week this period starts on.
   *
   * Accepted values: DAY_OF_WEEK_UNSPECIFIED, MONDAY, TUESDAY, WEDNESDAY,
   * THURSDAY, FRIDAY, SATURDAY, SUNDAY
   *
   * @param self::OPEN_DAY_* $openDay
   */
  public function setOpenDay($openDay)
  {
    $this->openDay = $openDay;
  }
  /**
   * @return self::OPEN_DAY_*
   */
  public function getOpenDay()
  {
    return $this->openDay;
  }
  /**
   * Required. Valid values are 00:00-24:00, where 24:00 represents midnight at
   * the end of the specified day field.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TimePeriod::class, 'Google_Service_MyBusinessBusinessInformation_TimePeriod');
