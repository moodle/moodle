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

namespace Google\Service\AnalyticsData;

class DateRange extends \Google\Model
{
  /**
   * The inclusive end date for the query in the format `YYYY-MM-DD`. Cannot be
   * before `start_date`. The format `NdaysAgo`, `yesterday`, or `today` is also
   * accepted, and in that case, the date is inferred based on the property's
   * reporting time zone.
   *
   * @var string
   */
  public $endDate;
  /**
   * Assigns a name to this date range. The dimension `dateRange` is valued to
   * this name in a report response. If set, cannot begin with `date_range_` or
   * `RESERVED_`. If not set, date ranges are named by their zero based index in
   * the request: `date_range_0`, `date_range_1`, etc.
   *
   * @var string
   */
  public $name;
  /**
   * The inclusive start date for the query in the format `YYYY-MM-DD`. Cannot
   * be after `end_date`. The format `NdaysAgo`, `yesterday`, or `today` is also
   * accepted, and in that case, the date is inferred based on the property's
   * reporting time zone.
   *
   * @var string
   */
  public $startDate;

  /**
   * The inclusive end date for the query in the format `YYYY-MM-DD`. Cannot be
   * before `start_date`. The format `NdaysAgo`, `yesterday`, or `today` is also
   * accepted, and in that case, the date is inferred based on the property's
   * reporting time zone.
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
   * Assigns a name to this date range. The dimension `dateRange` is valued to
   * this name in a report response. If set, cannot begin with `date_range_` or
   * `RESERVED_`. If not set, date ranges are named by their zero based index in
   * the request: `date_range_0`, `date_range_1`, etc.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The inclusive start date for the query in the format `YYYY-MM-DD`. Cannot
   * be after `end_date`. The format `NdaysAgo`, `yesterday`, or `today` is also
   * accepted, and in that case, the date is inferred based on the property's
   * reporting time zone.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DateRange::class, 'Google_Service_AnalyticsData_DateRange');
