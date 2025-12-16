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

class DenyMaintenancePeriod extends \Google\Model
{
  /**
   * "deny maintenance period" end date. If the year of the end date is empty,
   * the year of the start date also must be empty. In this case, it means the
   * no maintenance interval recurs every year. The date is in format yyyy-mm-dd
   * i.e., 2020-11-01, or mm-dd, i.e., 11-01
   *
   * @var string
   */
  public $endDate;
  /**
   * "deny maintenance period" start date. If the year of the start date is
   * empty, the year of the end date also must be empty. In this case, it means
   * the deny maintenance period recurs every year. The date is in format yyyy-
   * mm-dd i.e., 2020-11-01, or mm-dd, i.e., 11-01
   *
   * @var string
   */
  public $startDate;
  /**
   * Time in UTC when the "deny maintenance period" starts on start_date and
   * ends on end_date. The time is in format: HH:mm:SS, i.e., 00:00:00
   *
   * @var string
   */
  public $time;

  /**
   * "deny maintenance period" end date. If the year of the end date is empty,
   * the year of the start date also must be empty. In this case, it means the
   * no maintenance interval recurs every year. The date is in format yyyy-mm-dd
   * i.e., 2020-11-01, or mm-dd, i.e., 11-01
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * "deny maintenance period" start date. If the year of the start date is
   * empty, the year of the end date also must be empty. In this case, it means
   * the deny maintenance period recurs every year. The date is in format yyyy-
   * mm-dd i.e., 2020-11-01, or mm-dd, i.e., 11-01
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Time in UTC when the "deny maintenance period" starts on start_date and
   * ends on end_date. The time is in format: HH:mm:SS, i.e., 00:00:00
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DenyMaintenancePeriod::class, 'Google_Service_SQLAdmin_DenyMaintenancePeriod');
