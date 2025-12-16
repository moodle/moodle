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

namespace Google\Service\Dfareporting;

class DateRange extends \Google\Model
{
  public const RELATIVE_DATE_RANGE_TODAY = 'TODAY';
  public const RELATIVE_DATE_RANGE_YESTERDAY = 'YESTERDAY';
  public const RELATIVE_DATE_RANGE_WEEK_TO_DATE = 'WEEK_TO_DATE';
  public const RELATIVE_DATE_RANGE_MONTH_TO_DATE = 'MONTH_TO_DATE';
  public const RELATIVE_DATE_RANGE_QUARTER_TO_DATE = 'QUARTER_TO_DATE';
  public const RELATIVE_DATE_RANGE_YEAR_TO_DATE = 'YEAR_TO_DATE';
  public const RELATIVE_DATE_RANGE_PREVIOUS_WEEK = 'PREVIOUS_WEEK';
  public const RELATIVE_DATE_RANGE_PREVIOUS_MONTH = 'PREVIOUS_MONTH';
  public const RELATIVE_DATE_RANGE_PREVIOUS_QUARTER = 'PREVIOUS_QUARTER';
  public const RELATIVE_DATE_RANGE_PREVIOUS_YEAR = 'PREVIOUS_YEAR';
  public const RELATIVE_DATE_RANGE_LAST_7_DAYS = 'LAST_7_DAYS';
  public const RELATIVE_DATE_RANGE_LAST_30_DAYS = 'LAST_30_DAYS';
  public const RELATIVE_DATE_RANGE_LAST_90_DAYS = 'LAST_90_DAYS';
  public const RELATIVE_DATE_RANGE_LAST_365_DAYS = 'LAST_365_DAYS';
  public const RELATIVE_DATE_RANGE_LAST_24_MONTHS = 'LAST_24_MONTHS';
  public const RELATIVE_DATE_RANGE_LAST_14_DAYS = 'LAST_14_DAYS';
  public const RELATIVE_DATE_RANGE_LAST_60_DAYS = 'LAST_60_DAYS';
  /**
   * @var string
   */
  public $endDate;
  /**
   * The kind of resource this is, in this case dfareporting#dateRange.
   *
   * @var string
   */
  public $kind;
  /**
   * The date range relative to the date of when the report is run.
   *
   * @var string
   */
  public $relativeDateRange;
  /**
   * @var string
   */
  public $startDate;

  /**
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
   * The kind of resource this is, in this case dfareporting#dateRange.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The date range relative to the date of when the report is run.
   *
   * Accepted values: TODAY, YESTERDAY, WEEK_TO_DATE, MONTH_TO_DATE,
   * QUARTER_TO_DATE, YEAR_TO_DATE, PREVIOUS_WEEK, PREVIOUS_MONTH,
   * PREVIOUS_QUARTER, PREVIOUS_YEAR, LAST_7_DAYS, LAST_30_DAYS, LAST_90_DAYS,
   * LAST_365_DAYS, LAST_24_MONTHS, LAST_14_DAYS, LAST_60_DAYS
   *
   * @param self::RELATIVE_DATE_RANGE_* $relativeDateRange
   */
  public function setRelativeDateRange($relativeDateRange)
  {
    $this->relativeDateRange = $relativeDateRange;
  }
  /**
   * @return self::RELATIVE_DATE_RANGE_*
   */
  public function getRelativeDateRange()
  {
    return $this->relativeDateRange;
  }
  /**
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
class_alias(DateRange::class, 'Google_Service_Dfareporting_DateRange');
