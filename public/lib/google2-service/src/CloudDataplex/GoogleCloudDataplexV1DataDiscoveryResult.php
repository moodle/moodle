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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataDiscoveryResult extends \Google\Model
{
  protected $bigqueryPublishingType = GoogleCloudDataplexV1DataDiscoveryResultBigQueryPublishing::class;
  protected $bigqueryPublishingDataType = '';
  protected $scanStatisticsType = GoogleCloudDataplexV1DataDiscoveryResultScanStatistics::class;
  protected $scanStatisticsDataType = '';

  /**
   * Output only. Configuration for metadata publishing.
   *
   * @param GoogleCloudDataplexV1DataDiscoveryResultBigQueryPublishing $bigqueryPublishing
   */
  public function setBigqueryPublishing(GoogleCloudDataplexV1DataDiscoveryResultBigQueryPublishing $bigqueryPublishing)
  {
    $this->bigqueryPublishing = $bigqueryPublishing;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoveryResultBigQueryPublishing
   */
  public function getBigqueryPublishing()
  {
    return $this->bigqueryPublishing;
  }
  /**
   * Output only. Describes result statistics of a data scan discovery job.
   *
   * @param GoogleCloudDataplexV1DataDiscoveryResultScanStatistics $scanStatistics
   */
  public function setScanStatistics(GoogleCloudDataplexV1DataDiscoveryResultScanStatistics $scanStatistics)
  {
    $this->scanStatistics = $scanStatistics;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoveryResultScanStatistics
   */
  public function getScanStatistics()
  {
    return $this->scanStatistics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoveryResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoveryResult');
