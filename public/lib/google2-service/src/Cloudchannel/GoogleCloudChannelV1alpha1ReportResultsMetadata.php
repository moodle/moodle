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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1alpha1ReportResultsMetadata extends \Google\Model
{
  protected $dateRangeType = GoogleCloudChannelV1alpha1DateRange::class;
  protected $dateRangeDataType = '';
  protected $precedingDateRangeType = GoogleCloudChannelV1alpha1DateRange::class;
  protected $precedingDateRangeDataType = '';
  protected $reportType = GoogleCloudChannelV1alpha1Report::class;
  protected $reportDataType = '';
  /**
   * The total number of rows of data in the final report.
   *
   * @var string
   */
  public $rowCount;

  /**
   * The date range of reported usage.
   *
   * @param GoogleCloudChannelV1alpha1DateRange $dateRange
   */
  public function setDateRange(GoogleCloudChannelV1alpha1DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return GoogleCloudChannelV1alpha1DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * The usage dates immediately preceding `date_range` with the same duration.
   * Use this to calculate trending usage and costs. This is only populated if
   * you request trending data. For example, if `date_range` is July 1-15,
   * `preceding_date_range` will be June 16-30.
   *
   * @param GoogleCloudChannelV1alpha1DateRange $precedingDateRange
   */
  public function setPrecedingDateRange(GoogleCloudChannelV1alpha1DateRange $precedingDateRange)
  {
    $this->precedingDateRange = $precedingDateRange;
  }
  /**
   * @return GoogleCloudChannelV1alpha1DateRange
   */
  public function getPrecedingDateRange()
  {
    return $this->precedingDateRange;
  }
  /**
   * Details of the completed report.
   *
   * @param GoogleCloudChannelV1alpha1Report $report
   */
  public function setReport(GoogleCloudChannelV1alpha1Report $report)
  {
    $this->report = $report;
  }
  /**
   * @return GoogleCloudChannelV1alpha1Report
   */
  public function getReport()
  {
    return $this->report;
  }
  /**
   * The total number of rows of data in the final report.
   *
   * @param string $rowCount
   */
  public function setRowCount($rowCount)
  {
    $this->rowCount = $rowCount;
  }
  /**
   * @return string
   */
  public function getRowCount()
  {
    return $this->rowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1alpha1ReportResultsMetadata::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1alpha1ReportResultsMetadata');
