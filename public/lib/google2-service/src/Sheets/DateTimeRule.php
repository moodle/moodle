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

namespace Google\Service\Sheets;

class DateTimeRule extends \Google\Model
{
  /**
   * The default type, do not use.
   */
  public const TYPE_DATE_TIME_RULE_TYPE_UNSPECIFIED = 'DATE_TIME_RULE_TYPE_UNSPECIFIED';
  /**
   * Group dates by second, from 0 to 59.
   */
  public const TYPE_SECOND = 'SECOND';
  /**
   * Group dates by minute, from 0 to 59.
   */
  public const TYPE_MINUTE = 'MINUTE';
  /**
   * Group dates by hour using a 24-hour system, from 0 to 23.
   */
  public const TYPE_HOUR = 'HOUR';
  /**
   * Group dates by hour and minute using a 24-hour system, for example 19:45.
   */
  public const TYPE_HOUR_MINUTE = 'HOUR_MINUTE';
  /**
   * Group dates by hour and minute using a 12-hour system, for example 7:45 PM.
   * The AM/PM designation is translated based on the spreadsheet locale.
   */
  public const TYPE_HOUR_MINUTE_AMPM = 'HOUR_MINUTE_AMPM';
  /**
   * Group dates by day of week, for example Sunday. The days of the week will
   * be translated based on the spreadsheet locale.
   */
  public const TYPE_DAY_OF_WEEK = 'DAY_OF_WEEK';
  /**
   * Group dates by day of year, from 1 to 366. Note that dates after Feb. 29
   * fall in different buckets in leap years than in non-leap years.
   */
  public const TYPE_DAY_OF_YEAR = 'DAY_OF_YEAR';
  /**
   * Group dates by day of month, from 1 to 31.
   */
  public const TYPE_DAY_OF_MONTH = 'DAY_OF_MONTH';
  /**
   * Group dates by day and month, for example 22-Nov. The month is translated
   * based on the spreadsheet locale.
   */
  public const TYPE_DAY_MONTH = 'DAY_MONTH';
  /**
   * Group dates by month, for example Nov. The month is translated based on the
   * spreadsheet locale.
   */
  public const TYPE_MONTH = 'MONTH';
  /**
   * Group dates by quarter, for example Q1 (which represents Jan-Mar).
   */
  public const TYPE_QUARTER = 'QUARTER';
  /**
   * Group dates by year, for example 2008.
   */
  public const TYPE_YEAR = 'YEAR';
  /**
   * Group dates by year and month, for example 2008-Nov. The month is
   * translated based on the spreadsheet locale.
   */
  public const TYPE_YEAR_MONTH = 'YEAR_MONTH';
  /**
   * Group dates by year and quarter, for example 2008 Q4.
   */
  public const TYPE_YEAR_QUARTER = 'YEAR_QUARTER';
  /**
   * Group dates by year, month, and day, for example 2008-11-22.
   */
  public const TYPE_YEAR_MONTH_DAY = 'YEAR_MONTH_DAY';
  /**
   * The type of date-time grouping to apply.
   *
   * @var string
   */
  public $type;

  /**
   * The type of date-time grouping to apply.
   *
   * Accepted values: DATE_TIME_RULE_TYPE_UNSPECIFIED, SECOND, MINUTE, HOUR,
   * HOUR_MINUTE, HOUR_MINUTE_AMPM, DAY_OF_WEEK, DAY_OF_YEAR, DAY_OF_MONTH,
   * DAY_MONTH, MONTH, QUARTER, YEAR, YEAR_MONTH, YEAR_QUARTER, YEAR_MONTH_DAY
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DateTimeRule::class, 'Google_Service_Sheets_DateTimeRule');
