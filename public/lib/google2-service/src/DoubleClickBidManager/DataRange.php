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

namespace Google\Service\DoubleClickBidManager;

class DataRange extends \Google\Model
{
  /**
   * Default value when range is not specified or is unknown in this version.
   */
  public const RANGE_RANGE_UNSPECIFIED = 'RANGE_UNSPECIFIED';
  /**
   * Custom date range.
   */
  public const RANGE_CUSTOM_DATES = 'CUSTOM_DATES';
  /**
   * Current day.
   */
  public const RANGE_CURRENT_DAY = 'CURRENT_DAY';
  /**
   * Previous day.
   */
  public const RANGE_PREVIOUS_DAY = 'PREVIOUS_DAY';
  /**
   * All days, including the current day, since the most recent Sunday.
   */
  public const RANGE_WEEK_TO_DATE = 'WEEK_TO_DATE';
  /**
   * All days, including the current day, since the start of the current month.
   */
  public const RANGE_MONTH_TO_DATE = 'MONTH_TO_DATE';
  /**
   * All days, including the current day, since the start of the current
   * quarter.
   */
  public const RANGE_QUARTER_TO_DATE = 'QUARTER_TO_DATE';
  /**
   * All days, including the current day, since the start of the current
   * calendar year.
   */
  public const RANGE_YEAR_TO_DATE = 'YEAR_TO_DATE';
  /**
   * The previous completed week, beginning from Sunday.
   */
  public const RANGE_PREVIOUS_WEEK = 'PREVIOUS_WEEK';
  /**
   * The previous completed calendar month.
   */
  public const RANGE_PREVIOUS_MONTH = 'PREVIOUS_MONTH';
  /**
   * The previous completed quarter.
   */
  public const RANGE_PREVIOUS_QUARTER = 'PREVIOUS_QUARTER';
  /**
   * The previous completed calendar year.
   */
  public const RANGE_PREVIOUS_YEAR = 'PREVIOUS_YEAR';
  /**
   * The previous 7 days, excluding the current day.
   */
  public const RANGE_LAST_7_DAYS = 'LAST_7_DAYS';
  /**
   * The previous 30 days, excluding the current day.
   */
  public const RANGE_LAST_30_DAYS = 'LAST_30_DAYS';
  /**
   * The previous 90 days, excluding the current day.
   */
  public const RANGE_LAST_90_DAYS = 'LAST_90_DAYS';
  /**
   * The previous 365 days, excluding the current day.
   */
  public const RANGE_LAST_365_DAYS = 'LAST_365_DAYS';
  /**
   * All time for which data is available, excluding the current day.
   */
  public const RANGE_ALL_TIME = 'ALL_TIME';
  /**
   * The previous 14 days, excluding the current day.
   */
  public const RANGE_LAST_14_DAYS = 'LAST_14_DAYS';
  /**
   * The previous 60 days, excluding the current day.
   */
  public const RANGE_LAST_60_DAYS = 'LAST_60_DAYS';
  protected $customEndDateType = Date::class;
  protected $customEndDateDataType = '';
  protected $customStartDateType = Date::class;
  protected $customStartDateDataType = '';
  /**
   * The preset date range to be reported on. If `CUSTOM_DATES` is assigned to
   * this field, fields custom_start_date and custom_end_date must be set to
   * specify the custom date range.
   *
   * @var string
   */
  public $range;

  /**
   * If `CUSTOM_DATES` is assigned to range, this field specifies the end date
   * for the date range that is reported on. This field is required if using
   * `CUSTOM_DATES` range and will be ignored otherwise.
   *
   * @param Date $customEndDate
   */
  public function setCustomEndDate(Date $customEndDate)
  {
    $this->customEndDate = $customEndDate;
  }
  /**
   * @return Date
   */
  public function getCustomEndDate()
  {
    return $this->customEndDate;
  }
  /**
   * If `CUSTOM_DATES` is assigned to range, this field specifies the starting
   * date for the date range that is reported on. This field is required if
   * using `CUSTOM_DATES` range and will be ignored otherwise.
   *
   * @param Date $customStartDate
   */
  public function setCustomStartDate(Date $customStartDate)
  {
    $this->customStartDate = $customStartDate;
  }
  /**
   * @return Date
   */
  public function getCustomStartDate()
  {
    return $this->customStartDate;
  }
  /**
   * The preset date range to be reported on. If `CUSTOM_DATES` is assigned to
   * this field, fields custom_start_date and custom_end_date must be set to
   * specify the custom date range.
   *
   * Accepted values: RANGE_UNSPECIFIED, CUSTOM_DATES, CURRENT_DAY,
   * PREVIOUS_DAY, WEEK_TO_DATE, MONTH_TO_DATE, QUARTER_TO_DATE, YEAR_TO_DATE,
   * PREVIOUS_WEEK, PREVIOUS_MONTH, PREVIOUS_QUARTER, PREVIOUS_YEAR,
   * LAST_7_DAYS, LAST_30_DAYS, LAST_90_DAYS, LAST_365_DAYS, ALL_TIME,
   * LAST_14_DAYS, LAST_60_DAYS
   *
   * @param self::RANGE_* $range
   */
  public function setRange($range)
  {
    $this->range = $range;
  }
  /**
   * @return self::RANGE_*
   */
  public function getRange()
  {
    return $this->range;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataRange::class, 'Google_Service_DoubleClickBidManager_DataRange');
