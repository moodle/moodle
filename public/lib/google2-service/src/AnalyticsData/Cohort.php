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

class Cohort extends \Google\Model
{
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Dimension used by the cohort. Required and only supports
   * `firstSessionDate`.
   *
   * @var string
   */
  public $dimension;
  /**
   * Assigns a name to this cohort. The dimension `cohort` is valued to this
   * name in a report response. If set, cannot begin with `cohort_` or
   * `RESERVED_`. If not set, cohorts are named by their zero based index
   * `cohort_0`, `cohort_1`, etc.
   *
   * @var string
   */
  public $name;

  /**
   * The cohort selects users whose first touch date is between start date and
   * end date defined in the `dateRange`. This `dateRange` does not specify the
   * full date range of event data that is present in a cohort report. In a
   * cohort report, this `dateRange` is extended by the granularity and offset
   * present in the `cohortsRange`; event data for the extended reporting date
   * range is present in a cohort report. In a cohort request, this `dateRange`
   * is required and the `dateRanges` in the `RunReportRequest` or
   * `RunPivotReportRequest` must be unspecified. This `dateRange` should
   * generally be aligned with the cohort's granularity. If `CohortsRange` uses
   * daily granularity, this `dateRange` can be a single day. If `CohortsRange`
   * uses weekly granularity, this `dateRange` can be aligned to a week
   * boundary, starting at Sunday and ending Saturday. If `CohortsRange` uses
   * monthly granularity, this `dateRange` can be aligned to a month, starting
   * at the first and ending on the last day of the month.
   *
   * @param DateRange $dateRange
   */
  public function setDateRange(DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Dimension used by the cohort. Required and only supports
   * `firstSessionDate`.
   *
   * @param string $dimension
   */
  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }
  /**
   * @return string
   */
  public function getDimension()
  {
    return $this->dimension;
  }
  /**
   * Assigns a name to this cohort. The dimension `cohort` is valued to this
   * name in a report response. If set, cannot begin with `cohort_` or
   * `RESERVED_`. If not set, cohorts are named by their zero based index
   * `cohort_0`, `cohort_1`, etc.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cohort::class, 'Google_Service_AnalyticsData_Cohort');
