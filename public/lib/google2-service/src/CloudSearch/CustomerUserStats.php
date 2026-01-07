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

class CustomerUserStats extends \Google\Model
{
  protected $dateType = Date::class;
  protected $dateDataType = '';
  /**
   * The count of unique active users in the past one day
   *
   * @var string
   */
  public $oneDayActiveUsersCount;
  /**
   * The count of unique active users in the past seven days
   *
   * @var string
   */
  public $sevenDaysActiveUsersCount;
  /**
   * The count of unique active users in the past thirty days
   *
   * @var string
   */
  public $thirtyDaysActiveUsersCount;

  /**
   * The date for which session stats were calculated. Stats calculated on the
   * next day close to midnight are returned.
   *
   * @param Date $date
   */
  public function setDate(Date $date)
  {
    $this->date = $date;
  }
  /**
   * @return Date
   */
  public function getDate()
  {
    return $this->date;
  }
  /**
   * The count of unique active users in the past one day
   *
   * @param string $oneDayActiveUsersCount
   */
  public function setOneDayActiveUsersCount($oneDayActiveUsersCount)
  {
    $this->oneDayActiveUsersCount = $oneDayActiveUsersCount;
  }
  /**
   * @return string
   */
  public function getOneDayActiveUsersCount()
  {
    return $this->oneDayActiveUsersCount;
  }
  /**
   * The count of unique active users in the past seven days
   *
   * @param string $sevenDaysActiveUsersCount
   */
  public function setSevenDaysActiveUsersCount($sevenDaysActiveUsersCount)
  {
    $this->sevenDaysActiveUsersCount = $sevenDaysActiveUsersCount;
  }
  /**
   * @return string
   */
  public function getSevenDaysActiveUsersCount()
  {
    return $this->sevenDaysActiveUsersCount;
  }
  /**
   * The count of unique active users in the past thirty days
   *
   * @param string $thirtyDaysActiveUsersCount
   */
  public function setThirtyDaysActiveUsersCount($thirtyDaysActiveUsersCount)
  {
    $this->thirtyDaysActiveUsersCount = $thirtyDaysActiveUsersCount;
  }
  /**
   * @return string
   */
  public function getThirtyDaysActiveUsersCount()
  {
    return $this->thirtyDaysActiveUsersCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerUserStats::class, 'Google_Service_CloudSearch_CustomerUserStats');
