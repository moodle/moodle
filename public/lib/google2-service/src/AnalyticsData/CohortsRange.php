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

class CohortsRange extends \Google\Model
{
  /**
   * Should never be specified.
   */
  public const GRANULARITY_GRANULARITY_UNSPECIFIED = 'GRANULARITY_UNSPECIFIED';
  /**
   * Daily granularity. Commonly used if the cohort's `dateRange` is a single
   * day and the request contains `cohortNthDay`.
   */
  public const GRANULARITY_DAILY = 'DAILY';
  /**
   * Weekly granularity. Commonly used if the cohort's `dateRange` is a week in
   * duration (starting on Sunday and ending on Saturday) and the request
   * contains `cohortNthWeek`.
   */
  public const GRANULARITY_WEEKLY = 'WEEKLY';
  /**
   * Monthly granularity. Commonly used if the cohort's `dateRange` is a month
   * in duration and the request contains `cohortNthMonth`.
   */
  public const GRANULARITY_MONTHLY = 'MONTHLY';
  /**
   * Required. `endOffset` specifies the end date of the extended reporting date
   * range for a cohort report. `endOffset` can be any positive integer but is
   * commonly set to 5 to 10 so that reports contain data on the cohort for the
   * next several granularity time periods. If `granularity` is `DAILY`, the
   * `endDate` of the extended reporting date range is `endDate` of the cohort
   * plus `endOffset` days. If `granularity` is `WEEKLY`, the `endDate` of the
   * extended reporting date range is `endDate` of the cohort plus `endOffset *
   * 7` days. If `granularity` is `MONTHLY`, the `endDate` of the extended
   * reporting date range is `endDate` of the cohort plus `endOffset * 30` days.
   *
   * @var int
   */
  public $endOffset;
  /**
   * Required. The granularity used to interpret the `startOffset` and
   * `endOffset` for the extended reporting date range for a cohort report.
   *
   * @var string
   */
  public $granularity;
  /**
   * `startOffset` specifies the start date of the extended reporting date range
   * for a cohort report. `startOffset` is commonly set to 0 so that reports
   * contain data from the acquisition of the cohort forward. If `granularity`
   * is `DAILY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset` days. If `granularity` is
   * `WEEKLY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset * 7` days. If `granularity` is
   * `MONTHLY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset * 30` days.
   *
   * @var int
   */
  public $startOffset;

  /**
   * Required. `endOffset` specifies the end date of the extended reporting date
   * range for a cohort report. `endOffset` can be any positive integer but is
   * commonly set to 5 to 10 so that reports contain data on the cohort for the
   * next several granularity time periods. If `granularity` is `DAILY`, the
   * `endDate` of the extended reporting date range is `endDate` of the cohort
   * plus `endOffset` days. If `granularity` is `WEEKLY`, the `endDate` of the
   * extended reporting date range is `endDate` of the cohort plus `endOffset *
   * 7` days. If `granularity` is `MONTHLY`, the `endDate` of the extended
   * reporting date range is `endDate` of the cohort plus `endOffset * 30` days.
   *
   * @param int $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return int
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * Required. The granularity used to interpret the `startOffset` and
   * `endOffset` for the extended reporting date range for a cohort report.
   *
   * Accepted values: GRANULARITY_UNSPECIFIED, DAILY, WEEKLY, MONTHLY
   *
   * @param self::GRANULARITY_* $granularity
   */
  public function setGranularity($granularity)
  {
    $this->granularity = $granularity;
  }
  /**
   * @return self::GRANULARITY_*
   */
  public function getGranularity()
  {
    return $this->granularity;
  }
  /**
   * `startOffset` specifies the start date of the extended reporting date range
   * for a cohort report. `startOffset` is commonly set to 0 so that reports
   * contain data from the acquisition of the cohort forward. If `granularity`
   * is `DAILY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset` days. If `granularity` is
   * `WEEKLY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset * 7` days. If `granularity` is
   * `MONTHLY`, the `startDate` of the extended reporting date range is
   * `startDate` of the cohort plus `startOffset * 30` days.
   *
   * @param int $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return int
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CohortsRange::class, 'Google_Service_AnalyticsData_CohortsRange');
