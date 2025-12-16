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

class GoogleCloudChannelV1RunReportJobResponse extends \Google\Model
{
  protected $reportJobType = GoogleCloudChannelV1ReportJob::class;
  protected $reportJobDataType = '';
  protected $reportMetadataType = GoogleCloudChannelV1ReportResultsMetadata::class;
  protected $reportMetadataDataType = '';

  /**
   * Pass `report_job.name` to FetchReportResultsRequest.report_job to retrieve
   * the report's results.
   *
   * @param GoogleCloudChannelV1ReportJob $reportJob
   */
  public function setReportJob(GoogleCloudChannelV1ReportJob $reportJob)
  {
    $this->reportJob = $reportJob;
  }
  /**
   * @return GoogleCloudChannelV1ReportJob
   */
  public function getReportJob()
  {
    return $this->reportJob;
  }
  /**
   * The metadata for the report's results (display name, columns, row count,
   * and date range). If you view this before the operation finishes, you may
   * see incomplete data.
   *
   * @param GoogleCloudChannelV1ReportResultsMetadata $reportMetadata
   */
  public function setReportMetadata(GoogleCloudChannelV1ReportResultsMetadata $reportMetadata)
  {
    $this->reportMetadata = $reportMetadata;
  }
  /**
   * @return GoogleCloudChannelV1ReportResultsMetadata
   */
  public function getReportMetadata()
  {
    return $this->reportMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1RunReportJobResponse::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1RunReportJobResponse');
