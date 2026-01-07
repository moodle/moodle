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

class ReportMetadata extends \Google\Model
{
  /**
   * Output only. The location of the generated report file in Google Cloud
   * Storage. This field will be absent if status.state is not `DONE`.
   *
   * @var string
   */
  public $googleCloudStoragePath;
  protected $reportDataEndDateType = Date::class;
  protected $reportDataEndDateDataType = '';
  protected $reportDataStartDateType = Date::class;
  protected $reportDataStartDateDataType = '';
  protected $statusType = ReportStatus::class;
  protected $statusDataType = '';

  /**
   * Output only. The location of the generated report file in Google Cloud
   * Storage. This field will be absent if status.state is not `DONE`.
   *
   * @param string $googleCloudStoragePath
   */
  public function setGoogleCloudStoragePath($googleCloudStoragePath)
  {
    $this->googleCloudStoragePath = $googleCloudStoragePath;
  }
  /**
   * @return string
   */
  public function getGoogleCloudStoragePath()
  {
    return $this->googleCloudStoragePath;
  }
  /**
   * The end date of the report data date range.
   *
   * @param Date $reportDataEndDate
   */
  public function setReportDataEndDate(Date $reportDataEndDate)
  {
    $this->reportDataEndDate = $reportDataEndDate;
  }
  /**
   * @return Date
   */
  public function getReportDataEndDate()
  {
    return $this->reportDataEndDate;
  }
  /**
   * The start date of the report data date range.
   *
   * @param Date $reportDataStartDate
   */
  public function setReportDataStartDate(Date $reportDataStartDate)
  {
    $this->reportDataStartDate = $reportDataStartDate;
  }
  /**
   * @return Date
   */
  public function getReportDataStartDate()
  {
    return $this->reportDataStartDate;
  }
  /**
   * The status of the report.
   *
   * @param ReportStatus $status
   */
  public function setStatus(ReportStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ReportStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReportMetadata::class, 'Google_Service_DoubleClickBidManager_ReportMetadata');
