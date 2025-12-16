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

namespace Google\Service\CloudSearch;

class Date extends \Google\Model
{
  /**
   * Day of month. Must be from 1 to 31 and valid for the year and month.
   *
   * @var int
   */
  public $day;
  /**
   * Month of date. Must be from 1 to 12.
   *
   * @var int
   */
  public $month;
  /**
   * Year of date. Must be from 1 to 9999.
   *
   * @var int
   */
  public $year;

  /**
   * Day of month. Must be from 1 to 31 and valid for the year and month.
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
   * Month of date. Must be from 1 to 12.
   *
   * @param int $month
   */
  public function setMonth($month)
  {
    $this->month = $month;
  }
  /**
   * @return int
   */
  public function getMonth()
  {
    return $this->month;
  }
  /**
   * Year of date. Must be from 1 to 9999.
   *
   * @param int $year
   */
  public function setYear($year)
  {
    $this->year = $year;
  }
  /**
   * @return int
   */
  public function getYear()
  {
    return $this->year;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Date::class, 'Google_Service_CloudSearch_Date');
